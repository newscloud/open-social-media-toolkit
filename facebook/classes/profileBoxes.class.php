<?php
define ("PATH_LOG_FB","/var/log/smtFB.log");
class profileBoxes
{
	var $session;
	var $db;
	var $facebook;
	
	var $debug = 0;
	function __construct(&$db=NULL)
	{
		//if (is_null($db)) { 
			require_once(PATH_CORE .'/classes/db.class.php');
			$this->db=new cloudDatabase();
		//} else {
		//	$this->db=&$db;		
		//}
	}	
	function loadFacebook(&$facebook){
		$this->facebook =&$facebook;
	}
	
	function initRefHandle() {
//		$fbml='<h3>We can do more, and have more fun doing it!</h3> Help spread the word, drive the discussion on hot topics, and earn earth-happy prizes along the way.';
//		$fbml.='</div>';
		
		require_once(PATH_FACEBOOK. '/classes/actionTeam.class.php');
		$teamObj = new actionTeam($this->db);
		
		$markup=$teamObj->fetchProfileSummaryForProfileBox(0);
		$fbml='<fb:fbml><fb:narrow>'.$markup.'</fb:narrow></fb:fbml>';
	
		$err=$this->facebook->api_client->call_method('facebook.Fbml.setRefHandle', 
												array( 'handle' => 'default', 
													   'fbml' => $fbml,) 
												);
		if ($err==1 || $err==''){//wierd return error 1 but it still works
			echo 'Profile Box initialized successfully.';
		}else{
			echo 'There was an error with the initialization: '.$err;
		}
		
	}
	function updateProfileBoxes(){	
		$q="select fbId from UserInfo where isAppAuthorized=1 and (lastProfileUpdate='0000-00-00 00:00:00' or lastProfileUpdate IS NULL or lastProfileUpdate<NOW() - INTERVAL 7 DAY)  order by dateCreated DESC limit 15;";
		$query=$this->db->query($q);
		while ($users=$this->db->readQ($query)){		
			$errors=$this->updateProfileBox($users->fbId);	
			if ($errors<=1){//return of 1 from facebook works fine
				$this->db->query("update UserInfo set lastProfileUpdate=NOW() where fbId=".$users->fbId." limit 1;");
				$this->db->query("update fbSessions set fb_sig_profile_update_time=NOW() where fbId=".$users->fbId." LIMIT 1;");
			}
		}	
		//check for pages
		$q="select itemid from Log where action='pageAdd' and isFeedPublished='pending';";
		$query=$this->db->query($q);
		while ($pages=$this->db->readQ($query)){		
			$errors=$this->updateProfileBox($pages->itemid,'default');	
			if ($errors<=1){//return of 1 from facebook works fine
				$this->db->query("update Log set isFeedPublished='complete' where action='pageAdd' and itemid=".$pages->itemid." limit 1;");
			}
		}
		//dev temp
		//$e.=$this->updateProfileBox(22898109808,'default'); //grist
		//$e.=$this->updateProfileBox(756923320,'default'); //rick  
		//$e.=$this->updateProfileBox(39522693354,'default');//asu
		//$e.=$this->updateProfileBox(693311688); //jstar
	}
	
	function updateProfileBox($fbId=0,$refHandle='') {
		require_once(PATH_FACEBOOK. '/classes/actionTeam.class.php');
		
		
		// hack: because someone didnt use the correct interface
		$dummyApp=new app(NULL,true);
		require_once(PATH_FACEBOOK. '/classes/pages.class.php');
		
		$dummyPage= new pages($dummyApp,0,true);		
		
		$teamObj = new actionTeam($dummyPage);	
		
		if ($refHandle=='default'){$passFbid=0;}else{$passFbid=$fbId;}
		$markup=$teamObj->fetchProfileSummaryForProfileBox($passFbid,URL_CANVAS.'?&referfbid='.$fbId);
		//if ($fbId==693311688) $this->db->log('log entry for jeff'.$markup);
		$fbml='<fb:fbml><fb:narrow>'.$markup.'</fb:narrow></fb:fbml>';
		try {	
			$errs=$this->facebook->api_client->profile_setFBML(NULL, $fbId, $fbml,'', '',$fbml);
			
		} catch (Exception $e) {
			$this->db->log($e,PATH_LOG_FB);
			$this->db->log($errs,PATH_LOG_FB);	
		}
		
		return $errs;
	}
	
	function deleteFeedTemplates(){	
		require_once(PATH_CORE .'/classes/db.class.php');
		$this->db=new cloudDatabase();
		$q=$this->db->query("SELECT * FROM SystemStatus WHERE name like 'tbId_%';");
		while ($data=$this->db->readQ($q)) {
			echo 'deleting '.$data->name.' '.$data->numValue.'<br />';
			$this->facebook->api_client->feed_deactivateTemplateBundleByID($data->numValue);
			echo 'deleted<br />';						
		}
		echo '<br />';
		$q=$this->db->delete("SystemStatus","name like 'tbId_%'");
		$code.='Template Bundle IDs deleted<br /><br />';
		echo $code;
	}

	//should move this to miniFeeds.class.php
	function registerFeedTemplates(){	
		require_once(PATH_CORE .'/classes/db.class.php');
		$this->db=new cloudDatabase();
		require_once(PATH_CORE.'/classes/systemStatus.class.php');
		$ssObj=new systemStatus($this->db);	

		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
		
		require_once(PATH_TEMPLATES.'/feeds.php');	
		
		$ids=array();
		foreach ($feeds as $k=>$v){
			$idName='tbId_'.$k;
			echo $idName.'<br />';
/*
 			if ($ssObj->getState($idName)!=''){
				$this->facebook->api_client->feed_deactivateTemplateBundleByID($ssObj->getState($idName));
				$this->db->query("DELETE FROM SystemStatus WHERE name='".$idName."' LIMIT 1;");	
			} 
 */
			try {	
				$id=$this->facebook->api_client->feed_registerTemplateBundle($v['oneLine'],$v['shortStory']);
				echo $id.' registered<br><br>';				
			} catch (Exception $e) {
				echo $id.' not able to register<br><br>';
				var_dump($v);
				$this->db->log($e,PATH_LOG_FB);
				$this->db->log($errs,PATH_LOG_FB);	
			}
			echo '<br><br>';
		
			$ssObj->insertState($idName,$id);			
			$ids[]=$ssObj->getState($idName);
		}
		$idList=implode(', ',$ids);
		$code.='Template Bundle IDs registered: '.$idList.'<br /><br />';
		$code.='If you are a developer for this application, you can see the templates registered for this app <a href="http://developers.facebook.com/tools.php?templates" target="_blank">here</a>.';
		echo $code;
	}

}
?>