<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	require_once(PATH_CORE.'/classes/resources.class.php');
	$resObj=new resources($db);
	
	/* begin building the page */	
	$page->setTitle('Resources');
	$page->pkgStyles(CACHE_PREFIX.'nrAbout',array(PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css'));
	$page->pkgScripts(CACHE_PREFIX.'nrAbout',array());					
	$page->addToHeader($common->buildHeader().$common->buildNavigation('Links'));
	$page->addToFooter($common->buildFooter());	
	$page->addRSSFeed(URL_HOME.'?p=rss');		
	$code='';
	$code.='<div id="pageBody">';
	$code.='<div id="pageContent">';	
	//$code.='<div id="colAlpha">';
	$code.=$resObj->buildLinksColumn();
	//$code.='</div><!-- end colAlpha -->';
	//$code.='<div id="colBeta">';	
	//$code.=$resObj->buildLinksColumn('products');
	//$code.='</div><!-- end colBeta -->';
	$code.='</div><!-- end pageContent -->';
	$code.='</div><!-- end pageBody -->';
	$page->addToContent($code);
	$page->display();	
?>