<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_AJAX",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	require_once(PATH_CORE.'/classes/content.class.php');
	$cObj=new content($db);
	require_once(PATH_CORE.'/classes/template.class.php');
	$templateObj=new template($db);
	require_once(PATH_CORE.'/classes/utilities.class.php');
	$utilObj=new utilities($db);
	require_once(PATH_PHP.'/classes/comments.class.php');
	$commentsObj=new comments($db);

	/* process request variables */
	if (isset($_GET['permalink']))
		$permalink=$_GET['permalink'];
	else {
		// go to 404 error page
		header("Location: ".URL_ROOT);
		exit;
	}		
	$story=$cObj->getByPermalink($permalink);
	// record story read by this user
	if ($db->ui->isLoggedIn) {
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log(&$db);
		$logItem=$logObj->serialize(0,$db->ui->userid,'readStory',$story->siteContentId); // note this is the local contentid
 		$logObj->update($logItem);
	}	
	
	/* begin building the page */
	$page->setTitle($story->title);
	$page->pkgStyles(CACHE_PREFIX.'nrStory',array(PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css'));
	$page->pkgScripts(CACHE_PREFIX.'nrStory',array(PATH_PHP_SCRIPTS.'/comments.js',PATH_PHP_SCRIPTS.'/voting.js',PATH_PHP_SCRIPTS.'/journal.js'));					
	$page->addToHeader($common->buildHeader().$common->buildNavigation('Read Story'));
	$page->addToFooter($common->buildFooter());
	$page->addRSSFeed(URL_HOME.'?p=rss');		
	$code='';
	$code.='<div id="pageBody">';
	$code.='<div id="pageContent">';
	$templateObj->registerTemplates('PHP');	
	/* fetch story */
	$templateObj->db->result=$cObj->getByPermalink($permalink,true);
	$templateObj->db->setTemplateCallback('time_since', array($utilObj, 'time_since'), 'date');
	$templateObj->db->setTemplateCallback('caption', array($templateObj, 'cleanEllipsis'), 'caption');
	$templateObj->db->setTemplateCallback('cmdVote', array($templateObj, 'commandVote'), 'siteContentId');
	$templateObj->db->setTemplateCallback('cmdAdd', array($templateObj, 'commandAdd'), 'siteContentId');
	$templateObj->db->setTemplateCallback('cmdRead', array($templateObj, 'commandRead'), 'permalink');
	$templateObj->db->setTemplateCallback('storyImage', array($templateObj, 'getLargeStoryImage'), 'imageid');	
	$code.=$templateObj->mergeTemplate($templateObj->templates['readStoryContainer'],$templateObj->templates['readStoryContent']);			
	// display comments
	$commentsObj->setupLibraries();
	$code.=$commentsObj->buildComments(true,$story);
	$code.='</div><!-- end pageContent -->';
	$code.='</div><!-- end pageBody -->';
	$page->addToContent($code);
	$page->display();	
?>