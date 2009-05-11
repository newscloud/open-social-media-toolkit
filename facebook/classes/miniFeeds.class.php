<?
class miniFeeds
{
	var $session;
	var $db;
	var $facebook;
	var $app;
	
	function __construct(&$db=NULL)	{
		//always make sure not using dbConsoleModel
		require_once(PATH_CORE .'/classes/db.class.php');
		$this->db=new cloudDatabase();
	}	
	function loadFacebook(&$facebook){
		$this->facebook =&$facebook;
	}
	
	function updateMiniFeeds(){				
		require_once(PATH_CORE.'/classes/systemStatus.class.php');
		$ssObj=new systemStatus($this->db);		
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($this->db);
		
		//get template bundle ids, store in array to compare with log actions
		$translators=array();
		$q="select name,numValue from SystemStatus where name LIKE '%tbId_%'";
		$query=$this->db->query($q);
		while ($c=$this->db->readQ($query)) 
			$translators[$c->name]=$c->numValue;
		$problemSessionKeys=array();
		//get log actions
		$q="select Log.id,fbId,t,fb_sig_session_key,userid,action,userid2,itemid,itemid2 from Log join fbSessions on Log.userid1=fbSessions.userid WHERE fb_sig_expires>NOW() and isFeedPublished='pending' and FIND_IN_SET(action,'signup,completedChallenge,redeemed,wonPrize,postStory,comment') order by fbId,action";
		
		$query=$this->db->query($q);
		while ($action=$this->db->readQ($query)) {					 
			if (array_search($action->fbId,$problemSessionKeys)===false) {
				echo 'trying'.$action->fbId;
				//get feed elements
				require_once(PATH_FACEBOOK.'/classes/publisher.class.php');
				$pubObj=new publisher($this->db,$this->session,$this->app);
				$theItemid=$action->itemid;
				switch ($action->action){
					case 'postBlog':
					case 'postStory': $pubType='story'; break;
					case 'comment': $pubType='story'; $theItemid=$action->itemid2; break;
					case 'signup': $pubType='invite'; break;
					case 'completedChallenge': $pubType='challenge'; break;
					// REDEEM: case 'redeemed': $pubType='reward'; break;
					case 'wonPrize': $pubType='reward'; break;			
				}
				$feedElements=$pubObj->fetchFeedElements($pubType,$theItemid);		
				if (trim($feedElements['title'])!=''){
					$templateData=array( "title"=>$feedElements['title'],
										 "headline"=>$feedElements['title'],
										 "storyLink"=>$feedElements['storyLink'].'?&referfbid='.$action->fbId,
										 "url"=>URL_CANVAS.'?&referfbid='.$action->fbId,
										 "story"=>$feedElements['story'], //THIS ISN'T WORKING FOR NEWSWIRE
										 "pubType"=>$feedElements['pubType'],
										 "appName"=>SITE_TITLE,
										 "refId"=>$action->fbid,
										 "storyImage"=>$feedElements['image'],
										 "images"=>array(array('src'=>$feedElements['image'], 'href'=>$feedElements['storyLink'].'?&referfbid='.$action->fbId)) 
										);
										
					//echo '<br />---------<br />'; var_dump($templateData); echo '<br />';
					//publish the feed
					$this->facebook->set_user($action->fbId, $action->fb_sig_session_key);			
					$templateBundleId = $translators['tbId_'.$action->action]; 
					// only try once for a problem session key
						try {					
							$this->facebook->api_client->feed_publishUserAction($templateBundleId,json_encode($templateData),array(),'');
							//update log
							$logObj->setFeedPublishStatus($action->id,'complete');
						} catch (Exception $e) {
							$problemSessionKeys[]=$action->fbId;
							$this->db->log('Minifeed exception');
							$this->db->log($action);
							echo 'minifeed exception';
							var_dump($action);
						}										
				}
				
			}
		}		
		/*
		//temp for dev
		require_once(PATH_FACEBOOK.'/classes/publisher.class.php');
		$pubObj=new publisher($this->db,$session,&$app);
		$feedElements=$pubObj->fetchFeedElements('invite',0);		
		$templateData=array(   "title"=>$feedElements['title'],
							   "headline"=>$feedElements['title'],
							   "storyLink"=>$feedElements['storyLink'],
							   "url"=>URL_CANVAS,
							   "story"=>$feedElements['story'], //THIS ISN'T WORKING FOR NEWSWIRE
							   "pubType"=>$feedElements['pubType'],
							   "appName"=>SITE_TITLE,
							   "refId"=>'756923320',
							   "storyImage"=>$feedElements['image'],
							   "images"=>array(array('src'=>$feedElements['image'], 'href'=>$feedElements['storyLink'])) 
							);
								
		$this->facebook->set_user('756923320', '2.zJxB_Pq21pR8KXtw7UtrSg__.3600.1234000800-756923320');					
		
		$targetIds = array();
		
		$templateBundleId = $translators['tbId_signup']; 
		$this->facebook->api_client->feed_publishUserAction($templateBundleId,json_encode($templateData),$targetIds,'');*/

	}
}
?>