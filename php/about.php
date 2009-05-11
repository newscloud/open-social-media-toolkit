<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	
	/* begin building the page */	
	$page->setTitle('About');
	$page->pkgStyles(CACHE_PREFIX.'nrAbout',array(PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css'));
	$page->pkgScripts(CACHE_PREFIX.'nrAbout',array());					
	$page->addToHeader($common->buildHeader().$common->buildNavigation('About'));
	$page->addToFooter($common->buildFooter());	
	$page->addRSSFeed(URL_HOME.'?p=rss');		
	
	$code='';
	$code.='<div id="pageBody">';
	$code.='<div id="pageContent">';	
	include_once (PATH_PHP_TEMPLATES.'/about.php');
	$code.=$about;
	$code.='</div><!-- end pageContent -->';
	$code.='</div><!-- end pageBody -->';
	$page->addToContent($code);
	$page->display();	
?>