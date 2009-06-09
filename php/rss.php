<?php
	/* initialize database and libraries */
	define(RSS_AGE_MINUTES,60);
	define(RSS_NUMBER_STORIES,15);
	define ("INIT_COMMON",true);
	include_once ('initialize.php');

	/* process request variables */
	if (isset($_GET['action']))
		$action=$_GET['action'];
	else
		$action='TopStories';		

	/* begin building the page */
	$cacheName=CACHE_PREFIX.'Rss'.$action;
	if ($common->checkCache($cacheName,RSS_AGE_MINUTES)) {
		// still current, get from cache (fast)
		$code=$common->fetchCache($cacheName);		
	} else {
		// recreate the page  (slow)
		require_once(PATH_CORE.'/classes/rss.class.php');
		$rssObj=new rss($db,URL_HOME);
		$code=$rssObj->build($action);
		$common->cacheContent($cacheName,$code);		
	}				
	echo $code;	
?>