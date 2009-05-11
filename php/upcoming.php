<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_AJAX",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	require_once(PATH_PHP.'/classes/upcoming.class.php');
	$upObj=new upcoming($db);

	/* process request variables */
	if (isset($_GET['currentPage']))
		$currentPage=$_GET['currentPage'];
	else
		$currentPage=1;		

	/* begin building the page */	
	$page->setTitle('Upcoming Stories');
	$page->pkgStyles(CACHE_PREFIX.'nrUpcoming',array(PATH_PHP_STYLES.'/paging.css',PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css'));
	$page->pkgScripts(CACHE_PREFIX.'nrUpcoming',array(PATH_PHP_SCRIPTS.'/upcoming.js',PATH_PHP_SCRIPTS.'/voting.js',PATH_PHP_SCRIPTS.'/journal.js'));					
	$page->addToHeader($common->buildHeader().$common->buildNavigation('Upcoming'));
	$page->addToFooter($common->buildFooter());	
	$page->addRSSFeed(URL_HOME.'?p=rss');						
	$code='';
	$code.='<div id="pageBody">';
	$code.='<div id="pageContent">';	
	$code.='<div id="storyList">';
	$code.=$upObj->fetchUpcomingStories(0,$currentPage);
	$code.='</div><!-- end storyList -->';
	$code.='</div><!-- end pageContent -->';
	$code.='</div><!-- end pageBody -->';
	$page->addToContent($code);
	$page->display();
			
?>