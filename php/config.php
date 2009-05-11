<?php
	/* initialize database and libraries */
	include_once ('initialize.php');
	/* Process incoming variable requests */	
	if (isset($_GET['apiKey'])) {
		$apiKey=$_GET['apiKey'];
	} else {
		echo 'You need to include your apiKey as an argument in your url.';
		exit;		
	}
	if (isset($_GET['action'])) {
		$action=$_GET['action'];
	} else 
		$action='menu';
	// verify api key
	if ($init['apiKey']<>$apiKey) {
		echo 'Invalid access!';
		die();
	}
	// initialize partner registration for this site and domain (if first time)
	require_once (PATH_CORE.'classes/systemStatus.class.php');
	$ssObj=new systemStatus($db);
	if ($ssObj->checkTable()) {
		$partnerid=$ssObj->getState('partnerid');
		if ($partnerid==0) {
			require_once (PATH_CORE.'/classes/apiCloud.class.php');
			$apiObj=new apiCloud($db,$init[apiKey]);
			$resp=$apiObj->partnerRegister(SITE_CLOUDID,URL_HOME,SITE_TITLE);
			if ($resp[result]) {
				$partnerid=$resp[items][0][partnerid];
				$ssObj->setState('partnerid',$partnerid);
			}
		}		
	}
	$menu='<h1>Welcome to the configuration page for your site</h1>';
	$menu.='<p>Connected to the '.$db->database.' database</p>';
	$menu.='<ul>';
	$menu.='<li><a href="?p=home">Go to the Front Page</a></li>';
	$menu.='<li><a href="?p=config&action=initDB&apiKey='.$apiKey.'">Initialize the database</a></li>';
	$menu.='<li><a href="?p=config&action=reSync&apiKey='.$apiKey.'">Synchronize the database</a> with the remote NewsCloud server</li>';
	$menu.='<li><a href="?p=config&action=updateCache&apiKey='.$apiKey.'">Update the cache files</a></li>';
	$menu.='<li><a href="?p=config&action=resetCron&apiKey='.$apiKey.'">Initialize the Cron jobs table</a> - you still need to set up a CRON tab file on your server</li>';
	//$menu.='<li><a href="?p=config&action=resetDB&apiKey='.$apiKey.'">Reset the database</a> - erases everything</li>';
	//$menu.='<li><a href="?p=config&action=resetLog&apiKey='.$apiKey.'">Reset the log</a> - erases all user activities</li>';
	$menu.='<li>Hit p=config, action=cleanupUser with userid param set to userid to clean out their user records</li>';
	$menu.='<li><a href="?p=config&action=cleanupOrphans&apiKey='.$apiKey.'">Cleanup orphans</a> - cleans up references to deleted users</li>';
	$menu.='<li><a href="?p=config&action=prepareContestAdmins&apiKey='.$apiKey.'">Test resetting all admins and moderators!</li>';
	$menu.='<li><a href="?p=config&action=prepareContest&apiKey='.$apiKey.'">Prepare db for contest start</li>';
	$menu.='<li><a href="?p=config&action=updateScores&apiKey='.$apiKey.'">Updates ALL scores for ALL users</li>';
	$menu.='<li><a href="?p=config&action=rewardBetaTesters&apiKey='.$apiKey.'">Submits beta tester\'s emails for Beta Test HotDish challenge (1-time)</li>';
	$menu.='<li><a href="?p=config&action=uploadSettings&apiKey='.$apiKey.'">Upload Facebook App Settings</li>';	
	$menu.='</ul>';
	$menu.='<p>Learn more at the <a href="http://www.newscloud.org/index.php/Social_media_toolkit">Social Media Toolkit Wiki</a></p>';

	///////////////////////////////////////////////////////////////////////////
	// TODO: cleanup this major hack!
	
	function hackCleanupFacebookTables($db)
	{
		if (MODULE_FACEBOOK) 
		{	
			echo 'cleaning up fb tables<br />';
			require_once(PATH_FACEBOOK.'/classes/cleanupFacebook.class.php');
			$cleanupObj=new cleanupFacebook($db,'');
			$cleanupObj->flushDatabase(); 
			echo 'fb/st Database cleansed<br />';		
			
		}	
	}
	////////////////////////////////////////////////////////////////////////////
	
	switch ($action) {
		default:
		break;
		case 'initDB':
			include_once PATH_CORE."utilities/initDatabase.php";
		break;
		case 'resetCron':
			require_once(PATH_CORE.'/classes/cron.class.php');
			$cObj=new cron($init['apiKey']);
			$cObj->initJobs();
			$cObj->resetJobs();
		break;
		case 'reSync':
			// call all cron jobs with new sync info
			require_once(PATH_CORE.'/classes/cron.class.php');
			$cObj=new cron($init['apiKey']);
			$db->update("cronJobs","nextRun=0","1=1");
			$cObj->fetchJobs();
		break;
		case 'updateCache':
			// call all cron jobs with new sync info
			require_once(PATH_CORE.'/classes/cron.class.php');
			$cObj=new cron($init['apiKey']);
			$cObj->forceJob('updateCache');
		break;
		case 'resetDB':
			require_once(PATH_CORE.'/classes/cleanup.class.php');
			$cObj=new cleanup($db);
			$cObj->flushDatabase();
			hackCleanupFacebookTables($db); 

		break;
		case 'resetLog':
			$db->delete("Log"); // for debug purposes
		break;
		case 'testJob':
			
			$job = $_GET['job'];
			echo 'Testing job:'. $job. '<br>';
				require_once(PATH_CORE.'/classes/cron.class.php');
			$cObj=new cron($init['apiKey']);
			$cObj->forceJob($job);
		break;
		case 'cleanupUser':
			require_once (PATH_CORE. '/classes/teamBackend.class.php');
			$teamObj = new teamBackend($db);
			$teamObj->cleanupUser($_GET['userid']);
		break;
		case 'cleanupOrphans':
			require_once (PATH_CORE. '/classes/teamBackend.class.php');
			$teamObj = new teamBackend($db);
			$teamObj->cleanupOrphanedUsers($_GET['confirm']);
		break;	
		case 'prepareContestAdmins':
			require_once (PATH_CORE. '/classes/teamBackend.class.php');
			$teamObj = new teamBackend($db);
			$teamObj->testResetAdmins();
			 
		break;
		case 'prepareContest':
			require_once (PATH_CORE. '/classes/teamBackend.class.php');
			$teamObj = new teamBackend($db);
			if (isset($_GET['confirm'])) $teamObj->prepareContest();
			else { echo 'This will reset the database to content prep state - append &confirm and resubmit to proceed'; } 
			
		break; 
		case 'updateScores':
			require_once (PATH_CORE. '/classes/teamBackend.class.php');
			$teamObj = new teamBackend($db);
			$teamObj->updateScores();		
		break; 
		case 'rewardBetaTesters':
			require_once (PATH_CORE. '/classes/teamBackend.class.php');
			$teamObj = new teamBackend($db);
			$teamObj->rewardBetaTesters(); 				
		break; 
		case 'uploadSettings':
				require_once (PATH_CORE.'/classes/systemStatus.class.php');
				$ssObj=new systemStatus();
				$propList=$ssObj->loadFacebookProperties();
				var_dump($propList);
				require_once PATH_FACEBOOK."/classes/app.class.php";
				$app=new app(NULL,true);
				$facebook=&$app->loadFacebookLibrary();				
				$props=$facebook->api_client->admin_setAppProperties($propList); 
				echo 'Completed';						
		break;
			
			
	}
	echo $menu;
	

	
?>
