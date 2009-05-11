<?php		
	
class CronJobsTable
{
		
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="cronJobs";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "CronJob";
		
	static $fields = array(			
		"freqMinutes" => "INT(2) default 0",
		"task" => "VARCHAR(64) default ''",
		"comments" => "VARCHAR(150) default ''",
		"nextRun" => "timestamp",
		"status" => "enum ('enabled','disabled') default 'enabled'",	
		"dayOfWeek" => "VARCHAR(3) default ''",
		"hourOfDay" => "VARCHAR(2) default ''",
		"lastExecTime" => "INT(10) default 0",
		"isRunning" => "TINYINT(1) default 0",
		"lastStart" => "DATETIME",
		"lastItemTime" => "DATETIME",	
		"failureNoticeSent" => "TINYINT(1) default 0"
		);
	static $keydefinitions = array(); 
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
	}	
	// although many functions will be duplicated between table subclasses, having a parent class gets too messy	
	function getRowObject()
	{	
		$classname = self::$dbRowObjectClass; 
		return new $classname($this->db, self::$tablename, array_keys(self::$fields), self::$idname);
	}		
	
	// generic table creation routine, same for all *Table classes 		
	static function createTable($manageObj)
	{			
		$manageObj->addTable(self::$tablename,self::$idname,self::$idtype,"MyISAM");
		foreach (array_keys(self::$fields) as $key)
		{
			$manageObj->updateAddColumn(self::$tablename,$key,self::$fields[$key]);
		}
		foreach (self::$keydefinitions as $keydef)
		{
			$manageObj->updateAddKey(self::$tablename,$keydef[0], $keydef[1], $keydef[2], $keydef[3]);
		}
		
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////
}

class cron {
/**
 * Core class for managing cron processes
 **/	
	var $db;
	var $dayOfWeek;
	var $cloudid;
	var $apiKey;
	 
	function cron($apiKey='') {
		require_once (PATH_CORE.'/classes/db.class.php');
		$this->db=new cloudDatabase();
		$this->apiKey=$apiKey;
		$this->dayOfWeek=date("D");
		$this->hourOfDay=date("H");
		require_once ('systemStatus.class.php');
		//$ssObj=new systemStatus($this->db);
		//$info=$ssObj->getProperties();
		//$this->cloudid=$info->cloudid;
		$this->cloudid=SITE_CLOUDID;
	}

	function fetchJobs() {
		// log time of day
		$entryTime=time();		
		//$this->log(date('g:i a \a\t D M n, Y',$entryTime));				
		// fetch jobs that need to be run
		$jobList=$this->db->query("SELECT * FROM cronJobs WHERE nextRun<now() AND status='enabled' ORDER BY freqMinutes ASC;");
		while ($job=$this->db->readQ($jobList)) {
			// reset next run timestamp to a later timestamp based on frequency in minutes
			echo '<b>Cron: '.$job->task.'</b><br/>';
			// if job must be run on a certain day, check for the right day
			if (($job->dayOfWeek=='' OR $job->dayOfWeek==$this->dayOfWeek) 
				&& ($job->hourOfDay=='' OR $this->hourOfDay > $job->hourOfDay))			
			{
				//$this->log('Run '.$job->task);
				$startTime=time();
				try {
					$this->runJob($job);	
				} catch (Exception $e) {
					$this->log('Failed running '.$job->task.', Error: '.$e);
				}				
				$endTime=time();		
				//$this->log(' exec time: '.(($endTime-$startTime)/60).' seconds');
			}		
		}
		$this->hasDeadTasks();
		$exitTime=time();
		//$this->log('Execution time total: '.(($exitTime-$entryTime)/60).' seconds');
	}
	
	function hasDeadTasks() {
		// looks for dead tasks and emails the admin
		// task labeled as running but last started more than 15 minutes ago
		$result=$this->db->buildIdList("SELECT task as id FROM cronJobs WHERE isRunning=1 AND failureNoticeSent=0 AND status='enabled' AND lastStart<date_sub(NOW(), INTERVAL (freqMinutes+15) MINUTE);");
		if ($result<>'') {
			$q=$this->db->query("select email from User where isAdmin=1;");
			while ($data=$this->db->readQ($q)) {
				// Notify the admins		
				mail($data->email, SITE_TITLE.' Cron Job Alert', 'Dave, this is Hal, I am noticing the following cronJobs are dead: '.$result.' When are you coming back to check on them?', 'From: support@newscloud.com'."\r\n");		
			}
			$result=$this->db->update("cronJobs","failureNoticeSent=1", "isRunning=1 AND failureNoticeSent=0 AND status='enabled' AND lastStart<date_sub(NOW(), INTERVAL (freqMinutes+15) MINUTE)"); 		
		}
	}
		
	function forceJob($task='') {
		$entryTime=time();		
		//$this->log(date('g:i a \a\t D M n, Y',$entryTime));				
		$jobList=$this->db->query("SELECT * FROM cronJobs WHERE task='$task';");
		while ($job=$this->db->readQ($jobList)) {
			echo '<b>Cron: '.$job->task.'</b><br/>';
				try {
					$this->runJob($job,true);	
				} catch (Exception $e) {
					$this->log('Failed running '.$job->task.', Error: '.$e);
				}				
		}
		$this->hasDeadTasks();
		$exitTime=time();
		//$this->log('Execution time total: '.(($exitTime-$entryTime)/60).' seconds');		
	}

	function runJob($job,$force=false) {
		$startTime=microtime(true);
		$this->log($job->task.', started '.date('g:i a \a\t D M n, Y',$startTime));
		
		if (!isset($_GET['test']) && !$force) {
			if ($job->isRunning==1) {
				$this->db->log('Cron race condition: '.$job->task.' last start= '.$job->lastStart);
				echo 'Warning: Cron Race condition<br />';
				return;
			} else 
				$this->db->update("cronJobs","isRunning=1,failureNoticeSent=0","id=$job->id");
		}
		//echo 'Task: '.$job->task."\n";
		switch ($job->task) {
			default:
				// do nothing
			break;
			
			case 'updateUserLevels':
				require_once('teamBackend.class.php');
				$teamObj=new teamBackend($this->db);
				$teamObj->updateUserLevels();
			break;					
			case 'updateSiteChallenges':
				require_once('teamBackend.class.php');
				$teamObj=new teamBackend($this->db);
				$teamObj->updateSiteChallenges();
			break;					
			case 'updateCachedPointsAndChallenges':
				require_once('teamBackend.class.php');
				$teamObj=new teamBackend($this->db);
				$teamObj->updateCachedPointsAndChallenges();
			
			break;					
			case 'calcWeeklyLeaders':
				require_once ('teamBackend.class.php');
				$teamObj=new teamBackend($this->db);
				$teamObj->calcWeeklyLeaders();				
			break;
			case 'updateUserLevels':
				require_once('teamBackend.class.php');
				$teamObj=new teamBackend($this->db);
				$teamObj->updateUserLevels();
					
			case 'updateTwitter':
				// post top stories to twitter
				if (USE_TWITTER) {
					require_once ('twitter.class.php');
					$twitterObj=new twitter($this->db);
					$twitterObj->postFeaturedStories();
					$twitterObj->postNextTopStory();					
				}
			break;
			case 'updateCache':
				// build cached content for cover page layout
				require_once ('cache.class.php');
				$cacheObj=new cache($this->db);
				$cacheObj->update();
			break;
			case 'syncProperties':
				// sync Cloud properties with NewsCloud services	
				require_once ('apiCloud.class.php');
				$apiObj=new apiCloud($this->db,$this->apiKey);
				$props=$apiObj->syncProperties($this->cloudid);
				require_once ('systemStatus.class.php');
				$ssObj=new systemStatus($this->db);
				$ssObj->setProperties($props['items'][0]);
			break;
			case 'syncAnnouncements':
				require_once ('apiCloud.class.php');
				$apiObj=new apiCloud($this->db,$this->apiKey);
				$resp=$apiObj->syncAnnouncements($this->cloudid);
				if ($resp[result]!==false) { 
					$itemlist=$resp[items];
					require_once ('systemStatus.class.php');
					$ssObj=new systemStatus($this->db);
					$ssObj->resetAnnouncements();
					if (count($itemlist)>0) {
						foreach ($itemlist as $data) {		
							$ssObj->insertState('announcement',html_entity_decode($data['announce']));
						}
					}
				}
			break;
			case 'syncNewswire':	
				require_once ('apiCloud.class.php');
				$apiObj=new apiCloud($this->db,$this->apiKey);
				$resp=$apiObj->syncNewswire($this->cloudid,$this->timeStrToUnixModB($job->lastItemTime));
				$itemlist=$resp[items];
				echo 'count: '.count($itemlist).'<br />';
				if (count($itemlist)>0) {
					require_once('newswire.class.php');
					$nwObj=new newswire($this->db);
					$lastItemTime=date('Y-m-d H:i:s',(time()-(6*30*24*3600))); // set to six months earlier 
					foreach ($itemlist as $data) {					
						$wire=$nwObj->serialize($data[title],$data[caption],$data[blogtitle],$data[webpage],$data[date],$data[blogid]);						
						$id=$nwObj->add($wire);
						if ($data[date]>$lastItemTime)						
							$lastItemTime=$data[date];
						if ($id===false)
							echo 'skip '.$data[title].'<br />';
						else						
							echo 'adding '.$data[title].' id->'.$id.'<br />';
					}
					$this->db->update("cronJobs","lastItemTime='$lastItemTime'","id=$job->id");
				}
			break;			
			case 'syncLog':
				require_once ('apiCloud.class.php');
				$apiObj=new apiCloud($this->db,$this->apiKey);
				// request server ask for log
				$resp=$apiObj->requestSyncLog($this->cloudid,URL_HOME,$this->timeStrToUnixModB($job->lastStart));
				// get result of log sync
				$logResult=$resp[items][0][log];
				require_once PATH_CORE."/classes/log.class.php";
				$logObj=new log($this->db);
				// process results from sync operation
				$result=$logObj->receive($logResult);
				echo $result;
			break;
			case 'syncContent':
				// bring content from NewsCloud for this cloud to the remote site
				require_once ('apiCloud.class.php');
				$apiObj=new apiCloud($this->db,$this->apiKey);
				$resp=$apiObj->syncContent($this->cloudid,$this->timeStrToUnixModB($job->lastItemTime));
				$itemlist=$resp[items];
				if (count($itemlist)>0) {
					require_once('content.class.php');
					$cObj=new content($this->db);
					// to do : set this to actual time
					$lastItemTime=date('Y-m-d H:i:s',(time()-(6*30*24*3600))); // set to six months earlier 
					foreach ($itemlist as $data) {	
						echo 'Contentid'.$data[contentid].'<br />';
						// to do: before we can do this below, we need to be syncing ncuid when new users register
						// to do: get userid from ncUid
						// lookup userid in user table where ncuid=submitbyid
						// if not found make it 0											
						// to do: if external story, then check for local userid and set here
						$story=$cObj->serialize($data[contentid],$data[title],$data[description],'',$data[webpage],$data[permalink],$data[submitbyid],$data[submit_member],$data[userid],$data[date],$data[avgrank],0,$data[imageid]);					
						$id=$cObj->add($story);
						// update comments table with new contentids
						$cObj->updateCommentsTable($data[contentid],$id);
						if ($data[date]>$lastItemTime)						
							$lastItemTime=$data[date];						
						echo 'story added'.$id.'<br><br/>';
					}	
					$this->db->update("cronJobs","lastItemTime='$lastItemTime'","id=$job->id");
				}
			break;
			case 'syncComments':
				// bring comments from stories in this cloud from NewsCloud over to remote site
				require_once('content.class.php');
				$cObj=new content($this->db);
				$idList=$cObj->fetchRecentStoryList(14,99,true);
				$this->db->log('syncComments - stories to check for.',PATH_SYNCLOGFILE);
				$this->db->log($idList,PATH_SYNCLOGFILE);
				if ($idList<>'') {
					require_once('comments.class.php');
					$commentsObj=new comments($this->db);
					require_once ('apiCloud.class.php');
					$apiObj=new apiCloud($this->db,$this->apiKey);
					$result=$apiObj->syncComments($this->cloudid,$idList,$this->timeStrToUnixModB($job->lastItemTime));
					$itemlist=$result[items];
					$this->db->log($itemlist,PATH_SYNCLOGFILE);
					// update comment thread for each story
					if (count($itemlist)>0) {
						$lastItemTime=date('Y-m-d H:i:s',(time()-(6*30*24*3600))); // set to six months earlier
						foreach ($itemlist as $data) {	
							$temp= 'Bring over contentid'.$data[contentid].' Commentid'.$data[commentid].'<br />';
							// to do: if external story, then check for local userid and set here
							$comment=$commentsObj->remoteSerialize($data);											
							if ($data[date]>$lastItemTime)						
								$lastItemTime=$data[date];						
							$id=$commentsObj->add($comment);
							echo $temp.'<br />';
							var_dump($comment);
							$this->db->log($temp,PATH_SYNCLOGFILE);
							$this->db->log($comment,PATH_SYNCLOGFILE);
						}
					}
					$this->db->update("cronJobs","lastItemTime='$lastItemTime'","id=$job->id");					
				}			
			break;
			case 'syncScores':
				require_once('content.class.php');
				$cObj=new content($this->db);
				$idList=$cObj->fetchRecentStoryList(14,99,true);
				if ($idList<>'') {
					require_once ('apiCloud.class.php');
					$apiObj=new apiCloud($this->db,$this->apiKey);
					$resp=$apiObj->syncScores($this->cloudid,$idList,$this->timeStrToUnixModB($job->lastStart));					
					$itemlist=$resp[items];
					//var_dump($resp);
					if (count($itemlist)>0) {
						// update the score for each story with new votes
						foreach ($itemlist as $data) {	
							$this->db->update("Content","score=$data[score]","contentid=$data[contentid]");
							$temp='Set score of contentid:'.$data[contentid].'to '.$data[score];
							echo $temp.'<br />';
							$this->db->log($temp,PATH_SYNCLOGFILE);
						}
					}
				}			
			break;
			case 'syncResources':
				require_once ('apiCloud.class.php');
				$apiObj=new apiCloud($this->db,$this->apiKey);				
				require_once('resources.class.php');
				$resObj=new resources($this->db);
				$result=$apiObj->syncResources($this->cloudid);				
				$resObj->sync(html_entity_decode($result[items][0][resources]));
			break;
			case 'syncFeedList':
				require_once ('apiCloud.class.php');
				$apiObj=new apiCloud($this->db,$this->apiKey);				
				$result=$apiObj->syncFeedList($this->cloudid);
				require_once('feed.class.php');
				$feedObj=new feed($this->db);
				$feedObj->syncFeedList($result[items]);
			break;
			case 'updateSiteMap':
				$currentHour=date('G'); // 0 - 24
				$currentDayOfWeek=date('w'); // day of week 0-6
				$currentDayOfMonth=date('j'); // day of month 1-31				
				require_once('siteMap.class.php');
				$smObj=new siteMap($this->db);
				// do always - build the map for content from the last hour
				$smObj->buildMap('hourly');
				// only do this as midnight
				if ($currentHour==0) {
					// map to all the content from the last day
					$smObj->buildMap('daily');
				}
				// only do this at 3 am on first day of week
				if ($currentDayOfWeek=0 AND $currentHour==3) {
					// map to all the content from the last week, etc.
					$smObj->buildMap('weekly');
				}
				// only do this at 2 am on first day of month
				if ($currentDayOfMonth==1 AND $currentHour==2) {
					$smObj->buildMap('monthly');
				}
				// call buildIndexMap after updating any individual child maps above
				// just the time stamps from each individual map file are updated in the indexmap
				// warning: if a individual map hasn't been built - the index map won't include a reference to it
				$smObj->buildIndexMap();
			break;			
			case 'fetchFeeds':
				// import stories from feeds
				require_once('feed.class.php');
				$feedObj=new feed($this->db);
				$feedObj->fetchFeeds();			
			break;
			case 'logHourlyStats':
				require_once('statistics.class.php');
				$statsObj=new statistics($this->db);
				$statsObj->logHourlyStats();							
			break;
			case 'facebookMinifeed':
				require_once PATH_FACEBOOK."/classes/app.class.php";
				$app=new app(NULL,true);
				$facebook=&$app->loadFacebookLibrary();			
				require_once(PATH_FACEBOOK.'/classes/miniFeeds.class.php');
				$feedObj=new miniFeeds($this->db);
				$feedObj->loadFacebook($facebook);
				$feedObj->updateMiniFeeds();   
			break;
			case 'facebookProfileBoxes':				
				require_once PATH_FACEBOOK."/classes/app.class.php";
				$app=new app(NULL,true);
				$facebook=&$app->loadFacebookLibrary();			
				require_once(PATH_FACEBOOK.'/classes/profileBoxes.class.php');
				$proObj=new profileBoxes($this->db);
				$proObj->loadFacebook($facebook);
				$proObj->updateProfileBoxes();
			break;
			case 'facebookEmailEngine':
				// invoke facebookCron class
			break;
			case 'facebookAllocations':
				// check nightly facebook allocations
				$ssObj=new systemStatus($this->db);				
			 	/* initialize the SMT Facebook appliation class, NO Facebook library */
				require_once PATH_FACEBOOK."/classes/app.class.php";
				$app=new app(NULL,true);
				$facebook=&$app->loadFacebookLibrary();
				$npd=$facebook->api_client->admin_getAllocation('notifications_per_day');
				 $ssObj->setState('notifications_per_day',$npd);
				 $ssObj->setState('announcement_notifications_per_week',$facebook->api_client->admin_getAllocation('announcement_notifications_per_week'));
				 $ssObj->setState('requests_per_day',$facebook->api_client->admin_getAllocation('requests_per_day'));
				 $ssObj->setState('emails_per_day',$facebook->api_client->admin_getAllocation('emails_per_day'));
			break;
			case 'facebookSendNotifications':
				require_once PATH_FACEBOOK."/classes/app.class.php";
				$app=new app(NULL,true);
				$facebook=&$app->loadFacebookLibrary();
				require_once PATH_FACEBOOK."/classes/shareStories.class.php";
				$ssObj=new shareStories($app);				
				$ssObj->processNotifications();
			break;
			case 'facebookSendPromos':
				if (date('G')==0) {
					require_once PATH_FACEBOOK."/classes/promos.class.php";
					$promoObj=new promos($this->db);				
					$promoObj->send();
				}			
			break;
			case 'insertNewResearchData':
				require_once PATH_CORE."/classes/researchRawSession.class.php";
				require_once PATH_CORE."/classes/researchRawExtLink.class.php";
				require_once PATH_CORE."/classes/researchSessionLength.class.php";

				$rawExtLinkTable = new RawExtLinkTable($this->db);
				$rawExtLinkTable->insertNewestData();

				$rawSessionTable = new RawSessionTable($this->db);
				$rawSessionTable->insertNewestData();

				$sessionLengthTable = new SessionLengthTable($this->db);
				$sessionLengthTable->insertNewestData();
			break;
			case 'cleanup':
				require_once ('cleanup.class.php');
				$cleanObj=new cleanup($this->db,'daily');				
			break;
		}
		$execTime=microtime(true)-$startTime;
		$this->log('...completed in '.$execTime.' seconds.');
		$this->db->update("cronJobs","nextRun=date_sub(NOW(), INTERVAL (0-$job->freqMinutes) MINUTE),lastExecTime=$execTime,lastStart='".date('Y-m-d H:i:s',$startTime)."',isRunning=0","id=$job->id");
	}	
	
	function listJobs() {
		$jobList=$this->db->query("SELECT * FROM cronJobs ORDER BY freqMinutes ASC;");
		return $jobList;
	}
	
	function setJobStatus($id=0,$newStatus='enabled') {
		$this->db->update("cronJobs","status='$newStatus'","id=$id");
	}

	function addJob($task='task name',$comments='comments go here',$freqMinutes=1440,$status='enabled',$dayOfWeek='',$hourOfDay='') {
		$this->db->query("SELECT id FROM cronJobs WHERE task='$task' LIMIT 1;");
		if ($this->db->count()==0) {
			$comments=addslashes($comments);
			$this->db->insert("cronJobs","task,comments,freqMinutes,status,dayOfWeek,hourOfDay",
										"'$task','$comments',$freqMinutes,'$status','$dayOfWeek','$hourOfDay'");
		}
	}
	
	function resetJobs() {
		// resets running state of all cron jobs
		//$this->db->update("cronJobs","isRunning=0,lastItemTime=NULL,lastStart=NULL,nextRun=0","1=1");
		$this->db->delete("cronJobs");
		$this->initJobs();
	}
	
	function initJobs() {
		// this function adds NewsCloud's cron jobs to the database
		/* please do not update the frequency of these cron calls without requesting permission from jeff@newscloud.com for your site and topic */
		$this->addJob("updateCache","Update cached content",15,"disabled");
		$this->addJob("syncFeedList","Fetch blog list for this cloud",720,"enabled");
		if (USE_SIMPLEPIE)
			$this->addJob("fetchFeeds","Fetch blog feeds locally",15,"enabled"); // use simple pie to locally fetch raw feeds
		else
			$this->addJob("syncNewswire","Fetch new wire stories for this cloud",15,"enabled"); // sync raw feeds from newscloud
		$this->addJob("syncContent","Fetch new Content stories for this cloud",60,"enabled");
		$this->addJob("syncComments","Fetch new comment thread for this cloud",30,"enabled");
		$this->addJob("syncScores","Update scores for recent stories",20,"enabled");
		$this->addJob("syncProperties","Synchronize the latest Cloud properties",1440,"enabled");
		$this->addJob("syncAnnouncements","Synchronize the announcements for this Cloud",1440,"disabled");
		$this->addJob("cleanup","Cleanup unused tables",1440,"enabled");		
		$this->addJob("syncLog","Synchronize the log with NewsCloud server",30,"enabled");
		$this->addJob("syncResources","Fetch folders and links from the cloud",4320,"enabled");
		$this->addJob("updateTwitter","Update top stories in Twitter",30,"enabled");
		$this->addJob("updateSiteMap","Update site map for search engines",60,"enabled");
		$this->addJob("calcTeamLeaders","Update team leaders",60,"enabled");
		$this->addJob("insertNewResearchData","Add in the daily research stat logs",24*60,"enabled",'','03');
		if (MODULE_FACEBOOK) {
			$this->addJob("facebookProfileBoxes","Update Facebook profile boxes",15,"enabled");
			$this->addJob("facebookMinifeed","Publish facebook minifeed stories",15,"enabled");
			$this->addJob("facebookEmailEngine","Send Facebook emails",15,"enabled");
			$this->addJob("facebookAllocations","Check Facebook allocations nightly",1440,"enabled");						
			$this->addJob("updateUserLevels","Update userLevel fields to match their points",60,"enabled");
			$this->addJob("logHourlyStats","Update hourly statistics in log",60,"enabled");			
			$this->addJob("updateCachedPointsAndChallenges","Recalculate cached user point and challenge totals for internal consistency",180,"enabled");			
			$this->addJob("updateSiteChallenges","Detects and awards challenges for site internal functions that cant be detected as they happen",180,"enabled");						
			$this->addJob("calcWeeklyLeaders","Recalculates all points for all users, stores weekly leaders in WeeklyScores table, run MANUALLY for each week after scoring is finished",24*60*7,"disabled",'Mon','02');						
			$this->addJob("facebookSendNotifications","Deliver notifications",20,"enabled");
			$this->addJob("facebookSendPromos","Send promotions to new users",24*60,"enabled",'','00');
		}
	}
	
	function log($str='') {
		$this->db->log($str,PATH_CRONLOG);
	}

	function timeStrToUnixModB($str=NULL) {
		 // converts YYYY-MM-DD HH:MM:SS (MySQL TimeStamp) to unix timestamp
		if (is_null($str))
			 $newtstamp=mktime(0,0,0,1,1,1970);
		else {		 
			sscanf($str,"%4u-%2u-%2u %2u:%2u:%2u",$year,$month,$day,$hour,$min,$sec);
        	$newtstamp=mktime($hour,$min,$sec,$month,$day,$year);
        }	
		return $newtstamp;
	}	
	
} // end of class

?>