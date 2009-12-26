<?php
	/* site request handler */
	// Note: htaccess isn't used because it increases the complexity of the install for some open source users
	
	if (isset($_GET['p']))
		$p=$_GET['p'];
	else if (defined('NO_SECURITY') AND NO_SECURITY) {
		$p='config'; // during set up, switch to config menu
	} else
		$p='home';
	if (isset($_GET['x'])) {
		include_once(PATH_PHP.'/x.php');
		exit;
	}
	if ($_SERVER['QUERY_STRING']=='' OR MODULE_PHP=='FACEBOOK') {
		// if not a special page, redirect to Facebook application
		$specialPages = array('x','config','cache','img','process','ajax','sync','engine','console','load_story','load_stories','load_template','save_template','load_statistics','rf'); 
		if (array_search($p,$specialPages)===false) {
			if (isset($_GET['o']))
				$oStr='&o='.$_GET['o'];
			else
				$oStr='';			
			if (isset($_GET['id']))
				$iStr='&id='.$_GET['id'];
			else
				$iStr='';			
			header('Location: '.URL_CANVAS.'?p='.$p.$oStr.$iStr);
			exit;
		}
	}
		
	switch ($p) {
		default:
			include_once(PATH_PHP.'/home.php');
		break;
		case 'about':
			include_once(PATH_PHP.'/about.php');
		break;
		case 'resources':
			include_once(PATH_PHP.'/resources.php');
		break;
		case 'upcoming':
			include_once(PATH_PHP.'/upcoming.php');
		break;
		case 'newswire':
			include_once(PATH_PHP.'/newswire.php');
		break;
		case 'ajax':
			include_once(PATH_PHP.'/ajax.php');
		break;
		case 'process':
			include_once(PATH_PHP.'/process.php');
		break;
		case 'engine':
			ini_set('memory_limit', '32M');		
			include_once(PATH_CORE.'/engine.php');
		break;
		case 'rf':
			include_once(PATH_PHP.'/rf.php');
		break;
		case 'readStory':
			include_once(PATH_PHP.'/readStory.php');
		break;
		case 'signin':
			include_once(PATH_PHP.'/signin.php');
		break;
		case 'img':
			include_once(PATH_PHP.'/img.php');
		break;
		case 'cache':
			include_once(PATH_PHP.'/cache.php');
		break;
		case 'config':
			include_once(PATH_PHP.'/config.php');
		break;
		case 'sync':
			include_once(PATH_CORE.'/sync.php');
		break;
		case 'rss':
			include_once(PATH_PHP.'/rss.php');
		break;		
		case 'console':
			ini_set('memory_limit', '32M');
			include_once(PATH_PHP.'/console/console.php');
		break;
		case 'load_story':
			include_once(PATH_PHP.'/console/load_story.php');
		break;
		case 'load_stories':
			include_once(PATH_PHP.'/console/load_stories.php');
		break;
		case 'load_template':
			include_once(PATH_PHP.'/console/load_template.php');
		break;
		case 'save_template':
			include_once(PATH_PHP.'/console/save_template.php');
		break;
		case 'load_statistics':
			include_once(PATH_PHP.'/console/load_statistics.php');
		break;
	}	
?>