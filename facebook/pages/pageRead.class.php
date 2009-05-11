<?php

class pageRead {

	var $page;
	var $db;
	var $facebook;
	var $session;
	var $templateObj;
		
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->session = &$page->session;
		$this->common = &$page->common;
	}

	function fetch($option = 'comments', $cid = 0) {
		// build the read story page		
		require_once(PATH_CORE.'/classes/read.class.php');
		$readObj = new read($this->db,$this->session);
		$readObj->setPageLink($this);
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj = new actionTeam($this->page);		
		if ($cid==0) {
			// need for ajax readStory script
			if (isset($_GET['cid'])) $cid=$_GET['cid'];
		}
		$referid=$this->page->fetchReferral();		
		if ($referid!==false && is_numeric($referid)) {
			// record chat action
			if (isset($_GET['chat'])) {
				if (!$this->session->isAppAuthorized) 
				{
					// require authorization so we can get their fbId - redirs to signup
					$this->facebook=$this->session->app->loadFacebookLibrary();
					$user = $this->facebook->require_login();
				}
				if ($_POST['fb_sig_added']) { 
					$targetfbId=$_POST['fb_sig_user'];
				} else if (isset($_POST['fb_sig_canvas_user'])) {
					$targetfbId=$_POST['fb_sig_canvas_user'];			
				} else {
					$targetfbId=0;
				}
				// make sure the referer is not clicking on the link themselves
				if ($targetfbId<>0 AND $referid<>$this->session->userid) {
					// log referid as having referred this user
					require_once(PATH_CORE.'/classes/log.class.php');
					$logObj=new log($this->db);
					$logItem=$logObj->serialize(0,$referid,'chatStory',$cid,$targetfbId);
					$inLog=$logObj->update($logItem);											
				}
			}	
			// check for notification and display it
			if ($this->session->isLoaded AND $referid<>$this->session->userid) {
				// reader was referred here by someone
				require_once(PATH_CORE.'/classes/notifications.class.php');
				$notificationsTable = new NotificationsTable($this->db);
				$msgid=$notificationsTable->lookupReferral($referid,$cid,$this->session->fbId);
				if ($msgid!==false AND $msgid<>'' AND !is_null($msgid)) {
					$notificationsTable->setStatus($msgid,$this->session->fbId,'opened');		
					// get fbId from userid
					require_once (PATH_CORE .'/classes/user.class.php');
					$uit = new UserInfoTable($this->db);
					$ui = $uit->getRowObject();
					$ui->load($referid);
					$msgTable = new NotificationMessagesTable($this->db);
					$msg=$msgTable->getRowObject();
					// load the message
					$msg->load($msgid);
					// cast msg object into comment property array for token replacement
					$referObj=array();
					$referObj[fbId]=$ui->fbId;
					$referObj[userid]=$referid;
					$referObj[comments]=$msg->message;
					$referObj[date]=$msg->dateCreated;
					$referMsg=$readObj->fetchReferComment($referObj);
				}
			}
			$this->page->recordReferral($referid,'referReader',$cid);
		}
		if (isset($_GET['viaBookmarklet'])) {
			//$inside.='<script type="text/javascript">function closeWindow() {window.opener = self;window.close();}</script><a href="#" onclick="closeWindow();">test</a>';
			//$inside.=$this->page->buildMessage('success','Your story has been posted','Click here if you wish to <a href="#" onclick="closeWindow();">close this window</a>.');
		} else if (isset($_GET['justPosted'])) {
			// to do: put some options here
		}		
		$inside.='<div id="col_left"><!-- begin left side -->';
		$inside.=$referMsg;
		$inside .= $readObj->fetchReadStory($cid, $option);
		$inside.='</div><!-- end left side -->';
		$inside .= '<div id="col_right">';
		$inside .= $readObj->fetchReadSidePanel($cid, $this->session,$this->page->isAjax);
		if (defined('ADS_ANY_SIDEBAR_BOTTOM')) {
			$inside.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=anySidebarBottom" frameborder="0" scrolling="no" style="width:180px;height:600px;padding:0px;margin:-5px 0px 0px 0px;"/>',$this->common['adWrapTallSidebar']);
		}								
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;
		$code.='<input type="hidden" id="filter" value="default">';
		$code.=$this->page->constructPage('read',$inside);				
		return $code;
	}

}

?>