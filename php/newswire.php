<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_AJAX",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	require_once PATH_PHP.'classes/newswire.class.php';
	$nwObj=new newswire($db);

	/* process request variables */
	if (isset($_GET['currentPage']))
		$currentPage=$_GET['currentPage'];
	else
		$currentPage=1;		
	
	/* begin building the page */
	$page->setTitle('Newswire');
	$page->pkgStyles(CACHE_PREFIX.'nrNewswire',array(PATH_PHP_STYLES.'/paging.css',PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css'));
	$page->pkgScripts(CACHE_PREFIX.'nrNewswire',array(PATH_PHP_SCRIPTS.'/common.js',PATH_PHP_SCRIPTS.'/newswire.js',PATH_PHP_SCRIPTS.'/publishWire.js'));					
	$page->addToHeader($common->buildHeader().$common->buildNavigation('Newswire'));
	$page->addToFooter($common->buildFooter());
	$page->addRSSFeed(URL_HOME.'?p=rss');					
	$code='';
	$code.='<div id="pageBody">';
	$code.='<div id="pageContent"><div id="theFilter"></div>';
	$code.='<div id="storyList">';
	$code.=$nwObj->fetchNewswirePage(0,$currentPage);
	$code.='</div><!-- end storyList -->';
//	$code.=$fb->buildDialog();
	$code.='</div><!-- end pageContent -->';
	$code.='</div><!-- end pageBody -->';
	$page->addToContent($code);
	$page->display();
			
?>