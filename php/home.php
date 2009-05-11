<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_AJAX",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	require_once PATH_PHP.'classes/home.class.php';
	$homeObj=new home($db);

	/* process request variables */
	if (isset($_GET['currentPage']))
		$currentPage=$_GET['currentPage'];
	else
		$currentPage=1;		
	
	/* begin building the page */
	$page->setTitle('Front Page');
	$page->pkgStyles(CACHE_PREFIX.'nrHome',array(PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css'));
	$page->pkgScripts(CACHE_PREFIX.'nrHome',array(PATH_PHP_SCRIPTS.'/voting.js',PATH_PHP_SCRIPTS.'/journal.js',PATH_PHP_SCRIPTS.'/publishWire.js'));					
	$page->addToHeader($common->buildHeader().$common->buildNavigation('Front Page'));
	$page->addRSSFeed(URL_HOME.'?p=rss');	
	//$page->addHead('<meta name="verify-v1" content="-verify code here-" />');				
	$page->addToFooter($common->buildFooter());	
	$code='';
	$code.='<div id="pageBody">';
	$code.='<div id="pageContent">';	
	$code.='<div id="colAlpha">';
	/*	
	$code.='<div id="storyList">';
	$code.=$homeObj->fetchHomePage(0,$currentPage);
	$code.='</div><!-- end storyList -->';
	*/
	$code.=$common->fetchCache(CACHE_PREFIX.'Upcoming').'<br />';
	$code.='</div><!-- end colAlpha -->';
	$code.='<div id="colBeta">';	
	include_once (PATH_PHP_TEMPLATES.'/intro.php');
	$code.=$intro;
	$code.=$common->fetchCache(CACHE_PREFIX.'Announcement');
	$code.=$common->fetchCache(CACHE_PREFIX.'Newswire');
	$code.=$common->fetchCache(CACHE_PREFIX.'Recent');
	$code.='</div><!-- end colBeta -->';
	$code.='</div><!-- end pageContent -->';
	$code.='</div><!-- end pageBody -->';
	$page->addToContent($code);
	$page->display();	
?>