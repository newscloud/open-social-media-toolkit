<?php
	/* INSTALLATION MENU */
	/* Used for preparing the application */

	// check for database
	global $init; // global handle to the secret keys
	$database = $init['database'];
	$username = $init['username'];
	$password = $init['password'];
	$hostname = $init['hostname'];
	$con = mysql_connect($hostname, $username, $password);
	if (!$con)
    {
	  die('Could not connect: ' . mysql_error());
	}
	if (mysql_num_rows(mysql_query("SHOW DATABASES like '$database'"))==0) {
		if (mysql_query("CREATE DATABASE $database")) {
		  echo "Database created<br />";
		} else
		  {
		  echo "Error creating database: " . mysql_error();
		  }		
	}
		
	/* initialize database and libraries */
	include_once ('initialize.php');
		
	/* Process incoming variable requests */	
	if (isset($_GET['apiKey'])) {
		$apiKey=$_GET['apiKey'];
		if (strlen($apiKey)>35) die('API key is invalid');
	}
	if (!defined('NO_SECURITY') OR !NO_SECURITY) {	
		if (!isset($_GET['apiKey'])) 
 		{
			echo 'You need to include your secret as an argument in your url.';
			die();		
		} else {
			// verify api key
			if ($init['apiKey']<>$apiKey) {
				echo 'Invalid access!';
				die();
			}			
		}
	}
	if (isset($_GET['action'])) {
		$action=$_GET['action'];
		if (strlen($action)>15) exit();
	} else 
		$action='menu';

	$menu='<a name="menu" /><h1>Welcome to the NewsCloud Social Media Toolkit</h1>';
	$menu.='<p>Connected to the '.$db->database.' database</p>';
	$menu.='<h3>Installation Menu:</h3><ol>';
	$menu.='<li style="list-style-type: decimal;"><a href="?p=config&action=initDB&apiKey='.$apiKey.'">Initialize the database</a></li>';
	$menu.='<li style="list-style-type: decimal;"><a href="?p=config&action=addTestData&apiKey='.$apiKey.'">Populate test data</a></li>';
	$menu.='<li style="list-style-type: decimal;"><a href="?p=config&action=uploadSettings&apiKey='.$apiKey.'">Upload your Application settings to Facebook</a></li>';	
	$menu.='<li style="list-style-type: decimal;">Visit your <a target="_fbapp" href="'.URL_CANVAS.'">Application on Facebook</a></li>';
	$menu.='<li style="list-style-type: decimal;">Visit the <a target="_fbmc" href="'.URL_CONSOLE.'">Management Console</a></li>';
	$menu.='<li style="list-style-type: decimal;">Remove <a href="?p=config&action=removeTestData&apiKey='.$apiKey.'">test data</a></li>';
	$menu.='<li style="list-style-type: decimal;"><a target="_fbhelp" href="http://support.newscloud.com/discussions/site-gallery">Add your site</a> to our public gallery</li>';
	$menu.='</ol>';
		
	// deprecated -	$menu.='<li><a href="?p=config&action=reSync&apiKey='.$apiKey.'">Synchronize the database</a> with the remote NewsCloud server</li>';
	// deprecated -		$menu.='<li>Hit p=config, action=cleanupUser with userid param set to userid to clean out their user records</li>';
	// deprecated -		$menu.='<li><a href="?p=config&action=rewardBetaTesters&apiKey='.$apiKey.'">Submits beta tester\'s emails for Beta Test HotDish challenge (1-time)</a></li>';
	//$menu.='<li><a href="?p=config&action=resetDB&apiKey='.$apiKey.'">Reset the database</a> - erases everything</li>';
	//$menu.='<li><a href="?p=config&action=resetLog&apiKey='.$apiKey.'">Reset the log</a> - erases all user activities</li>';
		
	switch ($action) {
		default:
		break;
		case 'initDB':
			echo 'Scroll down to the <a href="#menu">installation menu</a> when complete<br /><br />';
			// override default time limit
			set_time_limit(300);			
			include_once PATH_CORE."utilities/initDatabase.php";
			require_once(PATH_CORE.'/classes/cron.class.php');
			$cObj=new cron($init['apiKey']);
			$cObj->initJobs();
			$cObj->resetJobs();
		break;
		case 'addTestData':
			include_once PATH_CORE."utilities/populateTestData.php";			
		break;
		case 'removeTestData':
			include_once PATH_CORE."utilities/removeTestData.php";			
		break;
		case 'uploadSettings':
				echo 'Uploading settings in SystemStatus table up to Facebook Developer application<br />';
				require_once (PATH_CORE.'/classes/systemStatus.class.php');
				$ssObj=new systemStatus();
				$propList=$ssObj->loadFacebookProperties();
				echo 'These settings will be uploaded:<br />';
				print_r($propList);
				echo '<br />';
				require_once PATH_FACEBOOK."/classes/app.class.php";
				$app=new app(NULL,true);
				$facebook=&$app->loadFacebookLibrary();				
				$props=$facebook->api_client->admin_setAppProperties($propList);
				echo 'Completed settings upload. Visit the <a href="http://www.facebook.com/developers/apps.php?app_id='.$init['fbAppId'].'">Facebook Developer app</a> to review them.<br />';						
		break;					
		case 'populateSubscriptions':
			require_once(PATH_CORE.'/classes/subscriptions.class.php');
			$subObj=new SubscriptionsManager($db);
			$subObj->populateSubscriptions();
		break;
		case 'resetDB':
			require_once(PATH_CORE.'/classes/cleanup.class.php');
			$cObj=new cleanup($db);
			$cObj->flushDatabase();
			if (MODULE_FACEBOOK)
			{	
				echo 'cleaning up fb tables<br />';
				require_once(PATH_FACEBOOK.'/classes/cleanupFacebook.class.php');
				$cleanupObj=new cleanupFacebook($db,'');
				$cleanupObj->flushDatabase(); 
				echo 'fb/st Database cleansed<br />';
			}	
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
		/* deprecated
			case 'rewardBetaTesters':
				require_once (PATH_CORE. '/classes/teamBackend.class.php');
				$teamObj = new teamBackend($db);
				$teamObj->rewardBetaTesters(); 				
			break; 
			case 'reSync':
				// call all cron jobs with new sync info
				require_once(PATH_CORE.'/classes/cron.class.php');
				$cObj=new cron($init['apiKey']);
				$db->update("cronJobs","nextRun=0","1=1");
				$cObj->fetchJobs();
			break;
			*/
	}
	// check for warnings
	// re-run
	$warnings=checkWarnings($db);			
	echo $menu.$warnings;
	$resources='<h3>Useful resources:</h3><ul>';
	$resources.='<li><a target="_fbhelp" href="http://support.newscloud.com">NewsCloud Open Source Support Community </a></li>';
	$resources.='<li><a target="_fbhelp" href="http://opensource.newscloud.com">NewsCloud Open Source Blog</a></li>';
	$resources.='<li><a target="_fbhelp" href="http://blog.newscloud.com/services.html">NewsCloud Consulting Services</a></li>';
	$resources.='<li><a target="_fbhelp" href="http://youreyelevel.com/smt/">Eye Level Design Consulting</a></li>';
	$resources.='</ul>';
	$resources.='<p><a href="http://www.twitter.com/newscloud"><img src="http://twitter-badges.s3.amazonaws.com/follow_bird-c.png" alt="Follow newscloud on Twitter"/></a>';
	echo $resources;
	
	function checkWarnings(&$db) {
		$warnings='';
		$q=$db->query("SHOW TABLES;"); // check if db has been configured
		if ($db->countQ($q)>0) {
			$q=$db->query("SELECT * FROM User,UserInfo WHERE User.userid=UserInfo.userid AND isAdmin=1");
			if ($db->countQ($q)==0) {
				$warnings.='<p><span style="color:red;"><strong>WARNING: No administrator yet.</strong></span> Set admin email in constants and run populate test data.</p>';
			} else {
				$data=$db->readQ($q);
				if ($data->fbId==0) {
					$warnings.='<p><span style="color:red;"><strong>WARNING: Administrator Facebook account not configured. <a href="'.URL_CANVAS.'?p=setAdmin">Configure now</a></strong></span></p>';
				}
			}			
		}
		
		if (defined('NO_SECURITY') AND NO_SECURITY) {
			global $init;	
			$warnings.='<p><span style="color:red;"><strong>WARNING: Security is turned off</strong></span> Before you launch, be sure to turn security on in constants.php. <strong>Important</strong>: Bookmark <strong><a href="'.SITE_DOMAIN.'?p=config&apiKey='.$init['apiKey'].'">this link</a></strong> before you turn security on. After you turn security on, you can reach the management console from the Admin link in the footer of your Facebook application.</p>';
		}
		if (defined('NO_CACHE') AND NO_CACHE) {	
			$warnings.='<p><span style="color:orange;"><strong>NOTICE: Caching is turned off</strong></span> When you are ready to launch, you can turn on caching in constants.php to enhance performance. You will need to permission the /sites/cache directory for Apache to write to.</p>';
		} else {
			try {	
				$tempStr=' <p><span style="color:red;"><strong>WARNING:</strong></span> Caching problem - could not open or write to '.PATH_CACHE.' - set ownership for Apache Web service e.g. chown www-data:www-data '.PATH_CACHE.' and set write permissions for the directory chmod -R 755 '.PATH_CACHE.'</p>';
				$handle = fopen(PATH_CACHE.DS.'configTest.txt', "a");
				if (!$handle) 
					$warnings.=$tempStr;
				else {
					$res=fwrite($handle,"config.php cache test");
					if (!$res) $warnings.=$tempStr;									
				}
			} catch (Exception $e) {
				$warnings.=$tempStr;
			}			
		}
		if (defined('NO_LOGGING') AND NO_LOGGING) {	
			$warnings.='<p><span style="color:orange;"><strong>NOTICE: Logging is turned off</strong></span> You can turn it on constants.php. You will need to touch and permission the specified /var/logs/*.log files for Apache to write to.</p>';
		} else {
			try {	
				$tempStr=' <p><span style="color:red;"><strong>WARNING:</strong></span> Logging problem - could not open or write to '.PATH_LOGFILE.' - create the file e.g. touch '.PATH_LOGFILE.', then set ownership for Apache Web service e.g. chown www-data:www-data '.PATH_LOGFILE.'</p>';
				$handle = fopen(PATH_LOGFILE, "a");
				if (!$handle) 
					$warnings.=$tempStr;	
				else {
					$res=fwrite($handle,"config.php log test");
					if (!$res) $warnings.=$tempStr;									
				}
			} catch (Exception $e) {
				$warnings.=$tempStr;
			}			
		}
		
		return $warnings;
	}
	/* deprecated
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
	*/
?>
