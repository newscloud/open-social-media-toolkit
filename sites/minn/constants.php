<?php
	// IMPORTANT: change image paths in /facebook/styles/default.css

	// FACEBOOK SPECIFIC 
	define ("MODULE_ACTIVE","FACEBOOK");
	define ("MODULE_FACEBOOK",TRUE);	

	// SITE SPECIFIC
	define ("TEST_MODE","OFF");
	define ("DEBUG_LOCAL",FALSE);
	define ("SITE_CLOUDID",655); // see approvedClouds.txt 
	define ("RESEARCH_SITE_ID",2); // value definied in research.sites database
	define ("SITE_TITLE",'Minnesota Daily');
	define ("SITE_TITLE_SHORT",'Daily');
	define ("SITE_SPONSOR",'mndaily');
	define ("SITE_TEAM_TITLE",'Action Team');
	define ("SITE_TOPIC",'Daily');
	define ("CACHE_PREFIX",'mn');
	define ("TWITTER_USER","mndailynews");
	define ("USE_RECAPTCHA",FALSE);
	define ("NAV_INCL_BLOGS",false);
	DEFINE ("AMAZON_ASSOCIATE_ID","mndaily-20");
	define ("AGE_STORY_MAX_DAYS",7);
	define ("AGE_TOP_STORY_MAX_HOUR",24);
	define ("ROWS_PER_PAGE",7);
	define ("MODULE_ACTIONTEAM",TRUE);	
	define ("USE_TWITTER",TRUE);
	define ("TWITTER_INTERVAL_MINUTES",10000);
	define ("TWITTER_SCORE_THRESHOLD",2000);
	define ("TWITTER_MODULE_TARGET","FACEBOOK");
	define ("USE_SIMPLEPIE",true); // fetch rss feeds locally
	define ("MAX_SESSIONS_ACTIVE",500);
	define ("ADS_HOME_SIDEBAR",true);
	define ("ADS_ANY_SIDEBAR",true);

	// to do: move these back to constants.php
	// hack: moved these to teamBackend where they are used to avoid annoying include problem
	//	$nameLevels=array('reader','contributor','bronze','silver','gold','platinum');
	//	$pointLevels=array(0,100,1000,2500,5000,10000);
		
	/* URL Settings */
	define ("URL_CANVAS","http://apps.facebook.com/mndaily");
	define("URL_BASE","http://host.newscloud.com/sites/minn/facebook");
	define ("URL_PREFIX",'/index.php');	
	define ("URL_CALLBACK",URL_BASE.URL_PREFIX);
	define ("URL_HOME",'http://minn.newsi.us/');
	define ("URL_CACHE",URL_HOME.'?p=cache');
	define ("URL_CONSOLE","http://minn.newsi.us?p=console");
	define ("URL_RSS","http://feeds2.feedburner.com/mndaily"); // burned feed from URL_CANVAS?p=rss		
	// TODO: problem: management console needs these as well
	define('URL_UPLOADS', URL_BASE.'/uploads');
	define('URL_THUMBNAILS', URL_UPLOADS.'/images');
	define('URL_SUBMITTED_IMAGES', URL_UPLOADS.'/submissions');
	
	
	/* Directory path settings */
	define ("SRC_ROOT","/var/www/grist/current");
	define ("PATH_CONSOLE",SRC_ROOT.'/php/console');
	define ("PATH_ROOT",$_SERVER['DOCUMENT_ROOT']);
	define ("PATH_SITE",PATH_ROOT."/sites/minn/facebook");
	define ("PATH_FACEBOOK",SRC_ROOT."/facebook");	
	define ("PATH_CACHE",SRC_ROOT.'/sites/cache'); 
	define ("PATH_SITE_IMAGES", SRC_ROOT.'/sites/minn/facebook/images/');
	define ("PATH_IMAGES",PATH_FACEBOOK.'/images/');
	define ("PATH_TEMPLATES",SRC_ROOT.'/sites/minn/facebook/templates');
	define ("PATH_STYLES",SRC_ROOT.'/sites/minn/facebook/styles'); // or, move to /smt/sites/climate/facebook/styles directory for site-specific approach
	define ("PATH_FACEBOOK_STYLES",SRC_ROOT.'/sites/minn/facebook/styles'); 	/*this has to have a shared name with constants.php*/
	define ("PATH_SCRIPTS",PATH_ROOT.'/facebook/scripts');
	// paths for admin uploads - djm
	define('PATH_UPLOAD_IMAGES', SRC_ROOT.'/sites/minn/facebook/uploads/images/');	
	// paths for user uploads - djm
	define('PATH_UPLOAD_SUBMISSIONS', SRC_ROOT.'/sites/minn/facebook/uploads/submissions/');
		
	/* Core Settings */
	define ("PATH_CORE",SRC_ROOT.'/core/');
 	// used in page.class.php
 	define('ARCHIVE_FOLDER', SRC_ROOT.'/sites/cache'); // location to store archive, don't add starting or trailing slashes
 	define('JSMIN_PATH', SRC_ROOT.'/core/utilities'); // full path to JSMin executable
	define("PATH_LOGFILE",'/var/log/minn.log');
	define("PATH_CRONLOG",'/var/log/minnCron.log'); // to get rid of this, point PATH_LOGS at /var/log ?
	define("PATH_SYNCLOGFILE",'/var/log/minnSync.log');
	define("PATH_LOGS",SRC_ROOT.'/sites/logs');

	/* PHP Module Settings */
	define ("MODULE_PHP","FACEBOOK");
	define ("PATH_PHP",SRC_ROOT.'/php/');		

	/* get secret keys */
	// You can either define your secret keys in the constants file
	// - or - do as we do, store them in an ini file outside of the apache web directory path
	// a global $init array must exist for the database to be initialized properly
	define ("INI_FILE_FOR_SECRET_KEYS",true);
	if (INI_FILE_FOR_SECRET_KEYS) {
		define ("INI_PATH",'/var/www/grist/'); // hard coded for unification
		$init=parse_ini_file(INI_PATH.'minn.ini');
		if (USE_RECAPTCHA)
			define ("KEY_PRI_RECAPTCHA",$init['key_pri_recaptcha']);					
		if (USE_TWITTER)
			define ("TWITTER_PWD",$init['twitterPwd']);					
	} else {	
		// get your api key at http://newscloud.com/learn/apidocs/apikey/
		$init['apiKey']='put-yourkeyhere';
		$init['fbAnalytics']='your-google-analytics-key-for-facebook';
		$init['database']='your database Name';
		$init['username']='your database user';
		$init['password']='your database password';
		$init['hostname']='your database hostname';
		if (MODULE_FACEBOOK) {		
			// Get these from http://developers.facebook.com
			$init['fbAPIKey']='your facebook api key';
			$init['fbSecretKey']='your facebook secret key';
		}						
	}

	/* SMT Server Settings */
	define ("URL_SMT_SERVER","http://www.newscloud.com");
	define ("URL_SMT_NODE","http://api.newscloud.com/services/cloud.php");	

	// to do - move to templates
	define ("PHP_HEADER",'<h1>Climate Change Times</h1><p>Keep up to date with the warming planet</p>');
	// for php sites
	define ("PATH_PHP_IMAGES",SRC_ROOT.'/sites/images/');
	define ("PATH_PHP_TEMPLATES",SRC_ROOT.'/sites/minn/php/templates');
	define ("PATH_PHP_STYLES",PATH_PHP.'styles'); // or, move to /styles directory for site-specific approach
	define ("PATH_PHP_SCRIPTS",PATH_PHP.'scripts');
		
	
		// Action Team Configuration
	// define("ENABLE_RESEARCH_STUDY", true); // might not need to be global if all relevant logic is in account.php template and rules templates
	// define("ENABLE_ACTION_TEAM", true); // use this if some site needs NO action team whatsoever   
	define("ENABLE_ACTION_REWARDS",true);     // enable rewards: rewards page & sidebars
	define("ENABLE_POINTS_BASED_REWARDS",true);     // enable rewards: rewards page & sidebars
	define("ENABLE_ACTION_CHALLENGES",true);  // enable challenges: b & sidebars
	define ("ENABLE_USER_BLOGGING",true);	
//	define ("ENABLE_ACTION_WALL",true);
	define("ENABLE_TEMPLATE_EDITS", false);
	define("ENABLE_MINOR_CONSENT", true);
?>