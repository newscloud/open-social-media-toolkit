<?php
	/* initialize database and libraries */
	define(RSS_AGE_MINUTES,60);
	define(RSS_NUMBER_STORIES,15);
	/* initialize the SMT Facebook appliation class */
	require_once PATH_FACEBOOK."/classes/app.class.php";
	$app=new app(NULL,true);
	require_once PATH_FACEBOOK."/classes/pages.class.php";
	$page=new pages($app->db);
	require_once (PATH_PHP.'classes/common.class.php');
	$common=new common($app->db);	

	/* process request variables */
	if (isset($_GET['action']))
		$action=$_GET['action'];
	else
		$action='TopStories';		

	/* begin building the page */
	$cacheName=CACHE_PREFIX.'Rss'.'_FB_'.$action;
	if ($common->checkCache($cacheName,RSS_AGE_MINUTES)) {
		// still current, get from cache (fast)
		$code=$common->fetchCache($cacheName);		
	} else {
		// recreate the page  (slow)
		require_once(PATH_CORE.'/classes/rss.class.php');
		$rssObj=new rss($app->db,URL_CANVAS);
		$code=$rssObj->build($action);
		$common->cacheContent($cacheName,$code);		
	}				
	echo $code;	
?>