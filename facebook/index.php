<?php
/* site request handler */
if (isset($_GET['p'])) {
	$p=$_GET['p'];
} else
	$p='home';
// arg o = which sub page
if (isset($_GET['o'])) {
	$o=$_GET['o'];
} else
	$o='';
// ajax request - return only an fbml component
if (isset($_GET['ajax'])) {
	$ajax=true;
} else
	$ajax=false;

if (ENABLE_TEMPLATE_EDITS)
{
	require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');						
	$dynTemp = dynamicTemplate::getInstance();
	$dynTemp->authEnableEditMode();
		
}
	
// these don't require full page load
$partialPages=array('cache','ajax','engine','postAuth','rss', 'postAlt','ver');
// determine whether authentication is required for this page
if (array_search($p,$partialPages)!==false) {
	switch ($p) {		
		case 'postAuth':
			include_once(PATH_FACEBOOK.'/postAuth.php');
		break;
		case 'postAlt':
			include_once(PATH_FACEBOOK.'/postAlt.php');		
		break;
		case 'cache':
			include_once(PATH_FACEBOOK.'/cache.php');
			break;
		case 'ajax':
			include_once(PATH_FACEBOOK.'/ajax.php');
			break;
		case 'engine':
			include_once(PATH_CORE.'/engine.php');
			break;
		case 'rss':
			include_once(PATH_FACEBOOK.'/rss.php');
			break;
		case 'ver':
			include_once(PATH_FACEBOOK.'/verify.php');
		break;
	}
	exit;
}

// use pre caching
if (isset($_POST['fb_sig_logged_out_facebook']) AND array_search($p,array('home','read','team'))!==false) {
	if ($p=='read')
		$preCacheName='pc_'.$p.'_'.$_GET['cid'].'_anon';
	else
		$preCacheName='pc_'.$p.'_anon';	
	if (checkCache($preCacheName,30)) {
		$code=fetchCache($preCacheName);
		if (isset($_GET['referid'])) {
			$code=str_ireplace('\?p\=signup','?referid='.$_GET['referid'].'&p=signup',$code);
		}
		echo $code;
		iLog();
		exit;
	} else
		$cachePage=true;
}

$pageNoFacebookLoad=array('home','read','team','stories');
if (array_search($p,$pageNoFacebookLoad)===false) {
	/* configure facebook client library for pages that need it*/
	//$startTime = microtime(true);
	include_once PATH_FACEBOOK.'/lib/facebook.php';
	//$fbinitStart = microtime(true);
	$facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);
	//$totalTime = microtime(true)-$startTime;
	//$initTime = microtime(true)-$fbinitStart;
	//$fblibloadprofstring = "Profiling: creating facebook lib: total $totalTime, initializing $initTime\n";
} 
/* initialize the SMT Facebook appliation class */
require_once PATH_FACEBOOK."/classes/app.class.php";
$app=new app($facebook);
if ($app->siteStatus=='offline') 
	$p='offline';
else if ($app->session->maxSessionsReached) 
	$p='maxSessions';
else if (!isset($_POST['ids']) && $_GET['c']=='skipped' && $p=='invite') 
	$p='home';
else if ($app->session->isMember===false AND ($p=='home')) { //  OR $p=='read' and 'team'	wait for components
	if ($p=='read')
		$preCacheName='pc_'.$p.'_'.$_GET['cid'].'_anon';
	else
		$preCacheName='pc_'.$p.'_anon';	
	if (checkCache($preCacheName,30)) {
		$code=fetchCache($preCacheName);
		if (isset($_GET['referid'])) {
			$code=str_ireplace('\?p\=signup','?referid='.$_GET['referid'].'&p=signup',$code);
		}
		echo $code; // session log already occurred
		exit;
	} else
		$cachePage=true;
}

if (ENABLE_TEMPLATE_EDITS)
{
	//require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');						
	//$dynTemp = dynamicTemplate::getInstance();
	$dynTemp->authEnableEditMode($app->session); // retry authorization if theres a live session
		
}

/* begin building page response */
$code='';
require_once(PATH_FACEBOOK.'/classes/pages.class.php');
$pagesObj=new pages($app,$user);
// check that current session authLevel allows viewing the page or needs redirect
if ($pagesObj->authenticateForPage($p,$app->session)===false) 
	$p='signup';
//if (TEST_MODE=='PRE_ALPHA') $pagesObj->authenticateTesters($app->session); // sandbox mode: check facebook user against our debug list	
switch ($p) {
	default: // home
		$code=$pagesObj->fetch('home');
		break;
	case 'stories':
		$code=$pagesObj->fetch('stories',$o);
		break;
	case 'read':
		// get the content id
		if (isset($_GET['cid'])) {
			$cid = $_GET['cid'];
		} else
			$cid = false;	
		if (!$cid)
			$code=$pagesObj->fetch('home');
		else
			$code=$pagesObj->fetch('read', $o, $cid);
		break;
	case 'appTab': // Facebook application tab
		$code=$pagesObj->fetch('appTab');
		break;
	case 'team':
		$code=$pagesObj->fetch('team');
		break;
	case 'rewards':
		$code=$pagesObj->fetch('rewards'); 
		break;
	case 'winners':
		$code=$pagesObj->fetch('winners'); 
		break;
	case 'links':
		$code=$pagesObj->fetch('links'); 
		break;
	case 'leaders':
		$code=$pagesObj->fetch('leaders',$o);
		break;
	case 'challenges':
		$code=$pagesObj->fetch('challenges');
		break;
	case 'challengeSubmit':
		$code=$pagesObj->fetch('challengeSubmit');
		break;
	case 'wall':
		$code=$pagesObj->fetch('wall');
		break;
	case 'cards':
			$code=$pagesObj->fetch('cards',$o);
			break;
	case 'rules':
		$code=$pagesObj->fetch('rules');
		break;
	case 'redeem':
		$code=$pagesObj->fetch('redeem');
		break;
	case 'orders':
		$code=$pagesObj->fetch('orders');
		break;
	case 'completed':
		$code=$pagesObj->fetch('completed');
		break;
	case 'invite':
		$code=$pagesObj->fetch('invite');
		break;
	case 'profile':
		$code=$pagesObj->fetch('profile');
	break;
	case 'account':
		$code=$pagesObj->fetch('account',$o);
	break;
	case 'contact':
		$code=$pagesObj->fetch('contact', $o);
	break;
	case 'signup':
		$code=$pagesObj->fetch('signup');
		break;
	case 'postStory':
		$code=$pagesObj->fetch('postStory',$o);		
	break;
	case 'admin':
		$code=$pagesObj->fetch('admin',$o);		
	break;
	case 'consent':
	case 'help':
	case 'about':
	case 'privacy':
	case 'tos':
	case 'testing':
	case 'faq':
	case 'offline':
	case 'maxSessions':
	case 'eqex':
	case 'cbd':
		$code=$pagesObj->fetch('static',$p);
		break;
	case '404':
		$code=$pagesObj->fetch('404');
	break;			
	case 'design': // to do - remove at end of project cycle  
		$code=$pagesObj->fetch('design',$o);
		echo $code;
		exit;
		break;	
}
if (!$ajax) {
	// build the header and navigation above the constructed body
	$header=$pagesObj->buildStyles($p);
	if ($p<>'signup') {
		$header.=$pagesObj->buildHeader();
	}		
	$code='<div id="pageBodyWrap">'.$code.'<!-- AJAX wrapper around page body --></div>';
	$code.=$pagesObj->setHiddenSession();	
	$code='<div id="appFrame">'.$code.'<!-- end appFrame --></div>';
	$footer=$pagesObj->buildFooter().$pagesObj->addAnalytics($init['fbAnalytics'],$p);
	$code=$header.$code.$footer;	
}
if ($cachePage) {
	if (isset($_POST['fb_sig_logged_out_facebook'])) $code = preg_replace('/on[cC]lick="[^"]+"/', '', $code); // remove jscript
	cacheContent($preCacheName,$code);	
	if (isset($_GET['referid'])) {
		$code=str_ireplace('\?p\=signup','?p=signup&referid='.$_GET['referid'],$code);
	}
}
$pagesObj->display($code);
// $pagesObj->debug();

function checkCache($filename,$age=15) {
	if (ENABLE_TEMPLATE_EDITS) return false;
	// checks if cached file is older then $age minutes
	// returns true if file is fresh
	$filename=PATH_CACHE.'/'.CACHE_PREFIX.'_'.$filename.'.cac';
	if (file_exists($filename) AND !isset($_GET['nc'])) {
		// use last cache version for robots
		if ((time()-(60*$age))<filemtime($filename)) return true; // OR $page->isRobot()
	}
	return false;
}

function fetchCache($filename) {
	//if (ENABLE_TEMPLATE_EDITS) return 'Error - cannot fetch from cache while site in edit mode'; // dont allow any cache fetches during edit mode 
	$filename=PATH_CACHE.'/'.CACHE_PREFIX.'_'.$filename.'.cac';
	$fHandle=fopen($filename,'r');
	$fSize=filesize($filename);
	if ($fSize>0) 
		$contents = fread($fHandle, $fSize);
	else
		$contents='';
	fclose($fHandle);
	return $contents;
}

function cacheContent($filename,$html) {
	if (ENABLE_TEMPLATE_EDITS) return; // NEVER let editing code enter the cache  
	
	// writes the code in $html to $filename in cache directory
	$filename=PATH_CACHE.'/'.CACHE_PREFIX.'_'.$filename.'.cac';
	$fHandle=fopen($filename,'w');
	if ($fHandle!==false) {
		fwrite($fHandle,$html);
		fclose($fHandle);
	}
}		

function iLog() {
	$obj=new stdClass;
	$obj->userid=0;
	$obj->qs=$_SERVER['QUERY_STRING'];
	$obj->t=time();
	$str=serialize($obj);
	$filename=date('Ymd').'_'.CACHE_PREFIX.'_session.log';
	$fHandle=fopen(PATH_LOGS.'/'.$filename,'a');
	if ($fHandle!==false) {
		fwrite($fHandle,$str."\n");
		fclose($fHandle);
	}			
}

?>
