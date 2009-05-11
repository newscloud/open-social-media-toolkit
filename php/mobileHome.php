<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_MOBILE_PAGE",true);
	define ("INIT_COMMON",true);
	define ("INIT_SESSION",true);
	define ("INIT_PAGE",false);
	define ("INIT_AJAX",false);
	include_once ('initialize.php');

	/* get request variables */
	if (isset($_GET['a'])) {
		$a=$_GET['a'];
	} else {
		$a='topStories';
	}
	
	/* begin building the page */
	switch ($a) {
		case 'mostRecent':
			$mode='most recent';
			$cacheName=CACHE_PREFIX.'MobMR';
			$code='<p><a href="?a=topStories">Top Stories</a> | <a href="?a=mostRecent"><strong>Recently Posted</strong></a></p>';
			$sort='date DESC';
		break;
		default:		
			$mode='top rated';
			$cacheName=CACHE_PREFIX.'MobTR';
			$code='<p><a href="?a=topStories"><strong>Top Rated</strong></a> | <a href="?a=mostRecent">Recently Posted</a></p>';
			$sort='score DESC';
		break;
	}

	if ($common->checkCache($cacheName,15)) {
		// still current, get from cache
		$temp=$common->fetchCache($cacheName);
	} else {
		// build the page
		define("STORIES_PER_PAGE",25);		
		define("STORIES_MAX_AGE_DAYS",15);		
		require_once(PATH_CORE.'/classes/template.class.php');
		$templateObj=new template($db);
		$templateObj->registerTemplates('PHP');		
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$utilObj=new utilities($db);
		
		$templateObj->db->result=$templateObj->db->query("SELECT * FROM Content WHERE date>date_sub(NOW(), INTERVAL ".STORIES_MAX_AGE_DAYS." DAY) ORDER BY $sort LIMIT ".STORIES_PER_PAGE.";");		
		$templateObj->db->setTemplateCallback('caption', array($templateObj, 'cleanEllipsis'), 'caption');
		$temp=$templateObj->mergeTemplate($templateObj->templates['mobileList'],$templateObj->templates['mobileItems']);			
		// cache it
		$common->cacheContent($cacheName,$temp);
	}
	$code.=$temp;
	$page->addRSSFeed(URL_HOME.'?p=rss');
	$page->pkgStyles('mobile',array(PATH_PHP_STYLES.'/mobile.css'));
	$page->addToHeader('<h4>Welcome to '.SITE_TITLE.' Mobile</h4>');	
	$page->addToContent($code);
	$page->display();				
?>

