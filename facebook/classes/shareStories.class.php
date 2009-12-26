<?php
/*
 * Share story 

function converts list of ids into names and pictures with lead str
- compares to member friends to see if direct link or profile switch is needed
<fb:if-is-app-user uid="12345"> 
- pages class

 */
class shareStories
{
	var $app;
	var $session;
	var $db;
	var $facebook;	
	var $debug = 0;
	
	function __construct(&$app=NULL)
	{
		if (!is_null($app)) {
			$this->app=&$app;
			$this->session=&$app->session;
			$this->facebook = &$app->session->facebook;
			$this->db=&$app->db;			
		}	
	}
	
	function buildShareSubmit($userid=0,$itemid=0,$subject='',$ids,$message='',$embed='',$nextPage='') {
		// process form results and send first batch of notifications
		require_once(PATH_CORE.'/classes/template.class.php');
		$templateObj=new template($this->db);
		require_once(PATH_CORE.'/classes/notifications.class.php');
		$notificationsTable = new NotificationsTable($this->db);
		// check arguments for errors	
		$error=false;
		if ($message=='') {
			$message='I thought the following story would interest you:';
		}
		if (count($ids)>0) {
			// load facebook library
			$this->facebook=&$this->app->loadFacebookLibrary();
			// add msg to outbound msgs as notification
			$msgid=$this->addAllNotifications($userid,$itemid,$subject,$ids,$message,$embed);
			// get daily limit
			$cntSent=$notificationsTable->countSentToday($this->session->userid);
			if ($this->app->notifications_per_day>$cntSent) {
				$cntRemains=($this->app->notifications_per_day-$cntSent);
				$result=$this->sendMessageNotifications($msgid,$cntRemains);
				// build the pic list for those sent
				$code=$templateObj->buildFacebookUserList('<p>Your story was sent to: </p>',$result[sentList]);	
				// build the pic list for those with errors
				$code.=$templateObj->buildFacebookUserList('<p>We encountered errors sending to: </p>',$result[notSentList]);	
			} else {
				// out of notifications today
				// no errors, but no everyone queued, no one sent				
			}
			// get pending for this message
			$pendingList=$notificationsTable->getPendingNotificationsByMsg($msgid);
			if ($pendingList<>'')
				$code.=$templateObj->buildFacebookUserList('<p>We have queued your story to the following users (and will send them as soon as Facebook allows): </p>',explode(',',$pendingList));			
		} else {
			$error=true;
			$errMsg='You didn\'t select any friends';			
		}
		$result=array();
		$result[error]=$error;
		$result[errMsg]='<fb:error><fb:message>There was a problem!</fb:message>'.$errMsg.'</fb:error>';
		$result[code]=$code;	
		return $result;		
	}

	function addAllNotifications($userid=0,$itemid=0,$subject='',$ids,$message='',$embed='') {
		// adds all notifications to the database
		require_once(PATH_CORE.'/classes/notifications.class.php');
		$msgTable = new NotificationMessagesTable($this->db);
		$msg = $msgTable->getRowObject();
		$embed=html_entity_decode($embed);			
		$msg->userid=$userid;
		$msg->itemid=$itemid;
		$msg->subject=$subject;
		$msg->message=$message;
		$msg->embed=$embed;
		$msg->status='pending';
		$msg->dateCreated=$this->db->toDateTime();
		$msgid=$msgTable->checkAndInsert($msg);
		$notificationsTable = new NotificationsTable($this->db);
		$notify= $notificationsTable->getRowObject();			
		foreach ($ids as $id){
			// insert a notification record to be sent
			$notify->msgid=$msgid;
			$notify->status='pending';
			$notify->toFbId=$id;
			$notify->userid=$userid;
			if (!$notificationsTable->checkExists($id,$msgid)) {
				$notifyid=$notify->insert();
			}
		}
		return $msgid;
	}

	function sendMessageNotifications($msgid=0,$limit=1){
		// send the next $limit notifications for $msgid message
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($this->db);
		require_once(PATH_CORE.'/classes/notifications.class.php');
		$msgTable = new NotificationMessagesTable($this->db);
		$msg=$msgTable->getRowObject();
		// load the message
		$msg->load($msgid);
		$notificationsTable = new NotificationsTable($this->db);
		// load $limit ids as a list
		$nextRxList=$notificationsTable->getRecipientList($msgid,$limit);
		$arrNextRxList=explode(',',$nextRxList);
		$apiResult=$this->facebook->api_client->notifications_send($nextRxList, ' shared '.$msg->embed.'', 'user_to_user');
		$result=array();
		$result[error]=false;
		if (!is_null($apiResult) AND $apiResult<>'') {			
			switch ($apiResult) {
				case 1:
				case 2:
				case 4:
				case 5:
				case 101:
				case 102:
				case 103:
				case 104:					
					// error conditions
					$result[error]=true;
					$result[errorCode]=$apiResult;
				break;
				default:
					$result[error]=false;
					// list of users who received the message
					$sentList=explode(',',$apiResult);				
					if (count($sentList)>0) {
						// only if sentList has at least one person in it
						foreach ($sentList as $toFbId) {
							// update the notification status of each: sent
							$notificationsTable->setStatus($msgid,$toFbId,'sent');	
							// log the story shared
							$logItem=$logObj->serialize(0,$msg->userid,'shareStory',$msg->itemid,$toFbId);
							$inLog=$logObj->update($logItem);									
						}
						// subtract the result list from the send list
						$notSentList=array_diff($arrNextRxList,$sentList);
						foreach ($notSentList as $toFbId) {
							// update the notification status of each: errors
							$notificationsTable->setStatus($msgid,$toFbId,'error');
						}
					} else {
						$notSentList=array();
					}					
				break;
			}
			// check if all notifications sent yet for this message
			$msgTable->updateMessageStatus($msgid);
			// return array of result
			$result[sentList]=$sentList;
			$result[notSentList]=$notSentList;				
		} 
		return $result;		
	}
	
	function buildShareDialog($itemid=0,$returnPage='home') {
		require_once(PATH_CORE.'/classes/content.class.php');
		$cObj=new content($this->db);
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$uObj=new utilities($this->db);				
		require_once(PATH_CORE.'/classes/notifications.class.php');
		$notificationsTable = new NotificationsTable($this->db);
		$story=$cObj->getById($itemid);
		$cntSent=$notificationsTable->countSentToday($this->session->userid);
		if ($this->app->notifications_per_day>$cntSent) {
			$maxSend=($this->app->notifications_per_day-$cntSent);
			$msg='<p>You can send notifications to '.$maxSend.' more people today. We will queue the rest for tomorrow.</p>'; 	
		} else {
			$maxSend=0;
			$msg='<p>You can\'t send any more notifications today. We will queue the rest for tomorrow.</p>';
		}
		$msg='<div id="introPanel">'.$msg.'<!-- end of introPanel --></div>';		 
		$link=URL_CANVAS.'?p=read&cid='.$story->siteContentId.'&referid='.$this->session->userid;
		$markup=$this->buildMarkup($story->title,$link);
		$attach=$this->buildAttachment($story->title,$uObj->shorten(strip_tags($story->caption),300),'<a href="'.$link.'">'.$uObj->ellipsis($link,80).'</a>');
		$code.='<div id="formWrap"></div><!-- end formWrap --><span style="display:none"><fb:editor></fb:editor></span><form id="dialog_form"><table class="editorkit" border="0" cellspacing="0" style="width:4px"><tr class="width_setter"><th style="width:50px"></th><td></td></tr>'.
'<tr><th></th><td class="editorkit_row">'.$msg.'</td><td class="right_padding"></td></tr>'.
'<tr><th><label>To:</label></th><td class="editorkit_row"><fb:multi-friend-input border_color="#8496ba" max="'.max($maxSend,30).'" /></td><td class="right_padding"></td></tr>';
		//$code.='<tr><th><label>Subject:</label></th><td class="editorkit_row"><input name="subject" id="subject" type="text" value="'.$story->title.'" /></td><td class="right_padding"></td></tr>';
		$code.='<tr><th class="detached_label"><label>Personal Message:</label></th><td class="editorkit_row"><textarea name="msg" id="msg"></textarea></td><td class="right_padding"></td></tr>';
		$code.='<tr><th class="detached_label"></th><td class="editorkit_row">'.$attach.'</td><td class="right_padding"></td></tr>';
		$code.='</table>';
		$code.='<input type="hidden" name="siteContentId" value="'.$itemid.'" /><input type="hidden" name="nextPage" value="'.$returnPage.'" /><input name="embed" type="hidden" value="'.htmlentities($markup).'" >';		
		$code.='<input name="subject" id="subject" type="hidden" value="" />';
		$code.='</form>';
		return $code;
	}	
	
	function buildMarkup($title='',$link='') {
		$str='<a href="'.$link.'">'.$title.'</a>';
		return $str;	
	}
	
	function buildAttachment($title='',$summary='',$link='') {
			// shows the link in facebook styling at the bottom of pop up
	$str='<div class="attachment_stage">
<div id="attachment_stage_area" class="attachment_stage_area">
<h3>'.$title.'</h3>
<span class="summary">'.$summary.'</span>
<span class="url">'.$link.'</span>
</div>
</div>';
		return $str;	
	}

	function processNotifications() {
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($this->db);
		require_once(PATH_CORE.'/classes/notifications.class.php');
		$notificationsTable = new NotificationsTable($this->db);
		$notification= $notificationsTable->getRowObject();			
		$notificationMessagesTable = new NotificationMessagesTable($this->db);
		$msg = $notificationMessagesTable->getRowObject();			
		$pendingMsgs = $notificationMessagesTable->getPendingValidMessages();
		if (strlen($pendingMsgs) == 0)
			return false;
		$pending = split(',', $pendingMsgs);
		foreach ($pending as $pMsg) {
			$msg->load($pMsg);
			$fbSession = mysql_fetch_assoc($this->db->query("SELECT * FROM fbSessions WHERE userid = {$msg->userid}"));
			print_r($fbSession);
			$countSent = $notificationsTable->countSentToday($msg->userid);
			$remaining = $this->app->notifications_per_day - $countSent;
			if ($remaining > 0) {
				$this->facebook->set_user($fbSession['fbId'], $fbSession['fb_sig_session_key']);
				$limit = min($remaining, 42);
				$results = $this->sendMessageNotifications($pMsg, $limit);
			} else {
				continue;
			}
		}
		//echo 'Notifications per day: '.$this->app->notifications_per_day;
		// loop thru unsent messages
		// loop thru unsent notifications for each message
		// sendNotifications via Facebook API
		// check which returned ok and which had errors
		// log successful ones
		// log when it gets sent
		// $logItem=$logObj->serialize(0,$userid,'shareStory',$itemid,$id);
		// $inLog=$logObj->update($logItem);
		//if (!$inLog) {
		
	}	
		
	
}
?>