<?php
	// Copy this file to constants.php
	// Change settings to your particular site and configuration

	// IMPORTANT: be sure to change image paths to your server in /facebook/styles/default.css

	// Site directory name e.g. if using /sites/default, set to 'default'
	define ("SITE_PATH_NAME",'default');
	define ("SITE_DOMAIN",'http://default.newsi.us');
	define ("URL_CANVAS","http://apps.facebook.com/defaultapp/"); // trailing slash matters to Facebook
	define("URL_BASE",SITE_DOMAIN.'/facebook');
	define ("URL_HOME",SITE_DOMAIN.'/php/');

	// For installation and testing - remove when done
	define ("NO_SECURITY",true);	// remove to enable console security after configuration	
	define ("NO_LOGGING",true);	// remove to enable logging, must touch and permission /var/log files
	define ("NO_CACHE",true);	// remove to enable caching, must touch and permission /sites/cache files

	// Model settings 
	define ("DS","/"); // directory separator
	define ("MODULE_FACEBOOK",true);	
	define ("MODULE_ACTIVE","FACEBOOK");
	
	// Secret key settings 
	// You can either define your secret keys in the constants file
	// - or - do as we do, for security, store them in an ini file outside of the apache web directory path
	// a global $init array must exist for the database to be initialized properly
	define ("INI_FILE_FOR_SECRET_KEYS",true);
	if (INI_FILE_FOR_SECRET_KEYS) {
		// check for ini file existence
		define ("INI_PATH",DS.'var'.DS.'www'.DS.'grist'.DS); // hard coded for unification
		$init=parse_ini_file(INI_PATH.SITE_PATH_NAME.'.ini');
	} else {	
		// otherwise, set up your  $init settings here
		$init['apiKey']='put-yourkeyhere'; // deprecated use random#-anystring e.g. 1223-asldjkwemx , 35 char max
		$init['fbAnalytics']='your-google-analytics-key-for-facebook'; // get from http://google.com/analytics
		$init['database']='your database Name';
		$init['username']='your database user';
		$init['password']='your database password';
		$init['hostname']='your database hostname';
		if (MODULE_FACEBOOK) {		
			// Get these from http://developers.facebook.com based on your application
			$init['fbAppId']='your facebook appid (numeric)';
			$init['fbAPIKey']='your facebook api key';
			$init['fbSecretKey']='your facebook secret key';
		}						
	}
	define ("USE_TWITTER",true); 
	if (USE_TWITTER)
		define ("TWITTER_PWD",$init['twitterPwd']);	// your twitter password from above INI file or set statically here
	define ("USE_RECAPTCHA",false);	
	if (USE_RECAPTCHA)
		define ("KEY_PRI_RECAPTCHA",$init['key_pri_recaptcha']); // get from http://recaptcha.net

	// Site settings
	define ("SITE_TITLE",'Default Site Title');
	define ("SITE_TITLE_SHORT",SITE_PATH_NAME);
	define ("CACHE_PREFIX",'dft'); // two or three letter prefix for your site title
	define ("SITE_SPONSOR",'Default Corp.');
	define ("SITE_TOPIC",'News');
	$siteTopics=array('news');		
	define ("SITE_TEAM_TITLE",'Community'); // title of the Community/Team tab
	define ("SUPPORT_EMAIL",'support@'.SITE_PATH_NAME.'.com'); // address where emails should be sent to
	define ("SUPPORT_ADMIN",'admin@'.SITE_PATH_NAME.'.com'); // address where emails should be sent to
	define("ENABLE_ACTION_REWARDS",false);     // enable rewards: rewards page & sidebars
	define("ENABLE_ACTION_CHALLENGES",true);  // enable challenges: b & sidebars
	define ("ENABLE_USER_BLOGGING",true);
	define ("ENABLE_STORY_PANEL",true);	
	define("ENABLE_MINOR_CONSENT", false);
	define("ENABLE_TEMPLATE_EDITS", false); // enable for wysiwyg template editing by admins
	define ("REG_SIMPLE","true");	
	define ("TABS_SIMPLE","true"); // new simple tabs
	define ("TAB_STORIES","News");
	// tags module
	$crowdTags=array('education','health','music','technology','food','politics','transportation','lifestyle','arts','sports','business','gardening','travel','recreation','government','environment');

	/* URL Settings */
	define ("URL_PREFIX",'/index.php');	
	define ("URL_CALLBACK",URL_BASE.URL_PREFIX);
	define ("URL_RSS",URL_BASE."?p=rss"); // or burned RSS feed e.g. http://feeds2.feedburner.com/default
	define('URL_UPLOADS', URL_BASE.'/uploads');
	define('URL_THUMBNAILS', URL_UPLOADS.'/images');
	define('URL_SUBMITTED_IMAGES', URL_UPLOADS.'/submissions');
	define ("URL_CACHE",URL_HOME.'?p=cache');
	define ("URL_CONSOLE",URL_HOME."?p=console");
	
	/* Directory path settings */
	define ("PATH_ROOT",$_SERVER['DOCUMENT_ROOT']);
	define ('SRC_ROOT',DS.'var'.DS.'www'.DS.'grist'.DS.'current');
	define ('SRC_SITE',DS.'sites'.DS.SITE_PATH_NAME);
	define ('PATH_CORE',SRC_ROOT.DS.'core'.DS);
	define ('PATH_CONSOLE',SRC_ROOT.DS.'php'.DS.'console');
	define ('PATH_SITE',PATH_ROOT.SRC_SITE.DS.'facebook');
	define ('PATH_FACEBOOK',SRC_ROOT.DS.'facebook');	
	define ('PATH_CACHE',SRC_ROOT.DS.SRC_SITE.DS.'cache'); 
	define ('PATH_SITE_IMAGES', SRC_ROOT.SRC_SITE.DS.'facebook'.DS.'images'.DS);
	define ('PATH_IMAGES',PATH_FACEBOOK.DS.'images'.DS);
	define ('PATH_TEMPLATES',SRC_ROOT.SRC_SITE.DS.'facebook'.DS.'templates');
	define ('PATH_STYLES',SRC_ROOT.SRC_SITE.DS.'facebook'.DS.'styles'); // or, move to /smt/sites/climate/facebook/styles directory for site-specific approach
	define ('PATH_FACEBOOK_STYLES',SRC_ROOT.SRC_SITE.DS.'facebook'.DS.'styles'); // this has to have a shared name with constants.php
	define ('PATH_SCRIPTS',SRC_ROOT.DS.'facebook'.DS.'scripts');
	define('PATH_UPLOAD_IMAGES', SRC_ROOT.SRC_SITE.DS.'facebook'.DS.'uploads'.DS.'images'.DS);	
	define('PATH_UPLOAD_SUBMISSIONS', SRC_ROOT.SRC_SITE.DS.'facebook'.DS.'uploads'.DS.'submissions'.DS);
		
 	// used in page.class.php
 	define('ARCHIVE_FOLDER', PATH_CACHE); // location to store archive, don't add starting or trailing slashes
 	define('JSMIN_PATH', SRC_ROOT.'/core/utilities'); // full path to JSMin executable
	define("PATH_SERVER_LOGS","/var/log/"); // programming logs
	define("PATH_LOGFILE",PATH_SERVER_LOGS.CACHE_PREFIX.'.log');
	define("PATH_CRONLOG",PATH_LOGFILE); 
	define("PATH_SYNCLOGFILE",PATH_LOGFILE);
	define("PATH_LOGS",SRC_ROOT.'/sites/logs'); // application usage logs for research
	
	/* PHP Module Settings */
	define ("MODULE_PHP","FACEBOOK");
	define ("PATH_PHP",SRC_ROOT.'/php/');
	define ("PATH_PHP_IMAGES",SRC_ROOT.'/sites/images/');
	define ("PATH_PHP_TEMPLATES",SRC_ROOT.SRC_SITE.'/php/templates');
	define ("PATH_PHP_STYLES",PATH_PHP.'styles'); // or, move to /styles directory for site-specific approach
	define ("PATH_PHP_SCRIPTS",PATH_PHP.'scripts');
	define ("PHP_HEADER",'<h1>'.SITE_TOPIC.'</h1><p>Keep up to date with us</p>');

	// feature settings

	// Forum settings
	define ("ENABLE_WALL",true);	
	define ("SITE_WALL_TITLE",'Talk');

	// Media stream
	define ("ENABLE_IMAGES",true);
	define ("MEDIA_INTERVAL",30);	// days for photos to stick around
	
	// Resource links page 
	define ("ENABLE_LINKS",true);	
	
	// Feedback discussion on home page
	define("ENABLE_SIMPLE_FEEDBACK",true); // feedback widget on home page
	
	// Micro blog area - twitter room
	define ("ENABLE_MICRO",true);	// requires TWITTER_USER & valid TWITTER_PWD 
	
	// Ideas
	define ("ENABLE_IDEAS",true);	
	define ("SITE_IDEAS_TITLE",'Ideas');
	define ("IDEAS_POPULAR_INTERVAL",7); // days

	// Ask - Knowledge base of questions and answers
	define ("ENABLE_ASK",true);	
	define ("ASK_POPULAR_INTERVAL",7); // days
	define ("SITE_ASK_TITLE",'Answers');

	// MISC SETTINGS AND NUMERIC CONSTANTS
	define ("MAX_SESSIONS_ACTIVE",500);
	define ("AGE_STORY_MAX_DAYS",7);
	define ("AGE_TOP_STORY_MAX_HOUR",72);
	define ("ROWS_PER_PAGE",7);
	define ("NAV_INCL_BLOGS",false);
	
	// Twitter settings
	define ("TWITTER_USER","defaultapp");
	define ("TWITTER_HASH",'#default');
	define ("TWITTER_MODULE_TARGET","FACEBOOK"); // story urls in twitter link to Facebook app
	define ("TWITTER_SCORE_THRESHOLD",10);
	define ("TWITTER_INTERVAL_MINUTES",120);
	
	// AFFILIATES & ADVERTISING
	define ("AMAZON_ASSOCIATE_ID","newscloudoss-20");
	define ("ADS_HOME_SMALL_BANNER",true);
	define ("ADS_ANY_SMALL_BANNER",true);
	define ("ADS_ANY_LARGE_RECT",true);
	define ("ADS_ANY_LARGE_BANNER",true);

	// STYLE MAPPING - select one below or make your own
	// NOTE 1: if you change style maps, be sure to delete PATH_CACHE/* and empty your browser cache or touch /sites/facebook/styles/default.css
	// NOTE 2: Do not use # in your color definitions here
	// NOTE 3: Check out the design kit photoshop files in /docs/designKit

	// Skin: Generic newscloud 
	define ("FONTS_MAIN",'"lucida grande",tahoma,verdana,arial,sans-serif');
	define ("FONTS_HEADS",'"lucida grande",tahoma,verdana,arial,sans-serif');
	define ("CLR_BODY","333333");
	define ("CLR_LINKS","3B5998");
	define ("CLR_EDGES1","999999");
	define ("CLR_EDGES2","CCCCCC");
	define ("CLR_KEY1","D8DFEA");
	define ("CLR_KEY2","ECEFF5");
	define ("CLR_KEY3","23355B");
	define ("CLR_KEY4","666666");
	define ("CLR_UTILITY","F0F0F0");

	/* Other skin, example settings - not perfect, but provide some basic color maps
	
	// Skin: Parent Buzz 
	define ("FONTS_MAIN",'"lucida grande",tahoma,verdana,arial,sans-serif');
	define ("FONTS_HEADS",'"lucida grande" arial, helvetica,tahoma,sans-serif');
	define ("CLR_BODY","333333");
	define ("CLR_LINKS","00628F");
	define ("CLR_EDGES1","E1E1E1");
	define ("CLR_EDGES2","E1E1E1");
	define ("CLR_KEY1","698C00");
	define ("CLR_KEY2","ECEFF5");
	define ("CLR_KEY3","405B1D");
	define ("CLR_KEY4","666666");
	define ("CLR_UTILITY","e7f2f5");

	// Skin: MnDaily 
	define ("FONTS_MAIN",'"lucida grande",tahoma,verdana,arial,sans-serif');
	define ("FONTS_HEADS",'Georgia, "Times New Roman", Times, "lucida grande",tahoma,verdana,arial,sans-serif');
	define ("CLR_BODY","333333");
	define ("CLR_LINKS","901D18");
	define ("CLR_EDGES1","8797A9");
	define ("CLR_EDGES2","901D18");
	define ("CLR_KEY1","8797A9");
	define ("CLR_KEY2","D1D1D1");
	define ("CLR_KEY3","FFCA4D");
	define ("CLR_KEY4","CC9900");
	define ("CLR_UTILITY","EEEEEE");

	// Skin: The Needle 
	define ("FONTS_MAIN",'"lucida grande",tahoma,verdana,arial,sans-serif');
	define ("FONTS_HEADS",'"Arial Black", arial, helvetica,tahoma,sans-serif');
	define ("CLR_BODY","333333");
	define ("CLR_LINKS","00628F");
	define ("CLR_EDGES1","999999");
	define ("CLR_EDGES2","E1E1E1");
	define ("CLR_KEY1","698C00");
	define ("CLR_KEY2","ECEFF5");
	define ("CLR_KEY3","A8BF60");
	define ("CLR_KEY4","FFFFFF");
	define ("CLR_UTILITY","E7F2F5");

*/

	// DEPRECATED 
	define ("SITE_CLOUDID",1); 
	define ("RESEARCH_SITE_ID",1); // value definied in research.sites database
	// define ("USE_SIMPLEPIE",true); // fetch rss feeds locally
	// define ("URL_SMT_NODE","http://api.newscloud.com/services/cloud.php");	
	// define ("URL_SMT_SERVER",URL_CANVAS);
	// define("ENABLE_RESEARCH_STUDY", true); // might not need to be global if all relevant logic is in account.php template and rules templates
?>