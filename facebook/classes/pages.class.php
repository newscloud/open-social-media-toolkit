<?php

//require_once(PATH_CORE.'/classes/page.class.php');
// extends page 

class pages {

	var $db;
	var $app;
	var $session;
	var $facebook;
	var $rowsPerPage=ROWS_PER_PAGE;
	var $highFrequencyPages;
	var $isAjax=false;
	var $stylesheets;
	var $scripts;
	var $common;

	function pages(&$app,$dummyRemove=0,$isAjax=false) {
		$this->app=&$app;
		$this->db=&$app->db;
		$this->facebook=&$app->facebook;
		$this->session=&$app->session;
		$this->isAjax=$isAjax;
		if (DEBUG_PROFILING) $this->setProfiling();
		$this->loadCommon();
		$this->loadCommonTeam(); // not large enough yet to warrant loading on individual pages
	}

	function loadCommonTeam()
	{
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
	
		include(PATH_TEMPLATES.'/commonTeam.php');
		$this->commonTeam = $commonTeam; // convenience pointer to the common templates		
	}
	function loadCommon()
	{
		//require_once(PATH_CORE.'/classes/template.class.php');
		//$templateObj = new template($this->db);
		//$templateObj->registerTemplates(MODULE_ACTIVE, 'common');
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
	
		include_once(PATH_TEMPLATES.'/common.php');
		$this->common = $common; // convenience pointer to the common templates
	}
	
	function authenticateForPage($page='home',&$session) {
		// array of open pages for this application
		$publicPages=array('home','stories','read','team','rewards','challenges','rules','leaders','404','static','links','tos','consent','maxSessions');
		$specialPages = array('signup'); 
		// determine whether authentication is required for this page
		if (array_search($page,$publicPages)===false) 
		{
			if (!$session->isAppAuthorized) 
			{
				$this->facebook=$this->app->loadFacebookLibrary();
				$user = $this->facebook->require_login();
				return false;
			} else if (!$session->isMember && (false === array_search($page, $specialPages)))
			{
				$this->facebook=$this->app->loadFacebookLibrary();
				$this->facebook->redirect(URL_CANVAS.'?p=signup'.(isset($_GET['referid'])?'&referid='.$_GET['referid']:''));
				return false;				 
			} else 
			{
				// user is a member and is logged in - do nothing
				return true;
			}
		} else 
			return true;
	}

	function constructPage($pageName='default',$pageContent='',$noLongerNeeded='refreshPage',$includeTabs=true,$includeHidden=true,$includeScript=true,$includeFBJS=true) {
		$code='';
		if ($includeScript)
			$code.=$this->buildJavaScript();
		if ($includeHidden)
			$code.=$this->setHiddenVariables($pageName);		
		$code.='<div id="pageBody">';
		if ($includeTabs)
			$code.=$this->buildPageTabs($pageName,true,!isset($_POST['fb_sig_logged_out_facebook']));					
		$code.='<div id="pageContent">';
		$code.=$this->checkForMessage();
		$code.=$pageContent;
		$code.='<!-- end pageContent --></div>';
		$code.='<!-- end pageBody --></div>';
		if ($includeFBJS)
			$code.=$this->buildDialog();
			$code.=$this->buildLoadingStatus();		
		return $code;	
	}
	
	function fetch($page='home',$option='',$arg3='') {
		//$before = memory_get_usage();
	
		switch ($page) {
			default:
				require_once(PATH_FACEBOOK.'/pages/pageHome.class.php');
				$homeObj=new pageHome($this);
				$code=$homeObj->fetch();
				break;
			case 'read':
				require_once(PATH_FACEBOOK.'/pages/pageRead.class.php');
				$readObj=new pageRead($this);
				$code=$readObj->fetch($option, $arg3);
				break;
			case 'stories':
				require_once(PATH_FACEBOOK.'/pages/pageStories.class.php');
				$storiesObj=new pageStories($this);
				$code=$storiesObj->fetch($option,$arg3);
				break;
			case 'team':
			case 'rewards':
			case 'challenges':
			case 'challengeSubmit':
			case 'redeem':
			case 'rules':
			case 'winners':
			case 'leaders':
			case 'wall':
				$code=$this->fetchTeam('fullPage',$page,$option);
				break;
			case 'static':
				require_once(PATH_FACEBOOK.'/pages/pageStatic.class.php');
				$staticObj=new pageStatic($this);
				$code=$staticObj->fetch($option);
				break;
			case 'invite':
				// check auth for signed in, if not redirect to login
				require_once(PATH_FACEBOOK.'/pages/pageInvite.class.php');
				$inviteObj=new pageInvite($this);
				$code=$inviteObj->fetch();
				break;
			case 'signup':
				// check auth for not anonymous
				require_once(PATH_FACEBOOK.'/pages/pageSignup.class.php');
				$signupObj=new pageSignup($this);
				$code=$signupObj->fetch($option);
				break;
			case 'account':
				require_once(PATH_FACEBOOK.'/pages/pageAccount.class.php');
				$accountObj=new pageAccount($this);
				$code=$accountObj->fetch($option);
				break;
			case 'profile':
				require_once(PATH_FACEBOOK.'/pages/pageProfile.class.php');
				$proObj=new pageProfile($this);
				$code=$proObj->fetch();
				break;
			case 'links':
				// check auth for not anonymous
				require_once(PATH_FACEBOOK.'/pages/pageLinks.class.php');
				$linksObj=new pageLinks($this);
				$code=$linksObj->fetch();
				break;
			case 'postStory':
				// check auth for not anonymous
				require_once(PATH_FACEBOOK.'/pages/pagePostStory.class.php');
				$psObj=new pagePostStory($this);
				$code=$psObj->fetch($option);
				break;
			case 'orders':
				// check auth for member
				require_once(PATH_FACEBOOK.'/pages/pageOrders.class.php');
				$ordersObj=new pageOrders($this);
				$code=$ordersObj->fetch();
				break;
			case 'completed':
				// check auth for member
				require_once(PATH_FACEBOOK.'/pages/pageCompletedChallenges.class.php');
				$completedObj=new pageCompletedChallenges($this);
				$code=$completedObj->fetch();
				break;
			case 'shareStory':
				require_once(PATH_FACEBOOK.'/pages/pageShareStory.class.php');
				$shareObj=new pageShareStory($this);
				// always a submit
				$code = $shareObj->process();
				break;
			case 'contact':
				require_once(PATH_FACEBOOK.'/pages/pageContact.class.php');
				$contactObj=new pageContact($this);
				if ($option == 'submit')
					$code = $contactObj->process($_POST);
				else
					$code = $contactObj->fetch();
				break;
			case 'dbtest':
				require_once(PATH_FACEBOOK.'/pages/pageDBTest.class.php');
				$dbtestObj=new pageDBTest($this);
				$code=$dbtestObj->fetch();
				break;
			case '404':
				require_once(PATH_FACEBOOK.'/pages/page404.class.php');
				$pObj=new page404($this);
				$code=$pObj->fetch($_GET['msg']);
			break;
			case 'design':
				// for testing the design
				require_once(PATH_FACEBOOK.'/pages/pageDesign.class.php');
				$pObj=new pageDesign($this);
				$code=$pObj->fetch($option);
				exit;
			break;	
			case 'admin':
				// for testing the design
				require_once(PATH_FACEBOOK.'/pages/pageAdmin.class.php');
				$pObj=new pageAdmin($this);
				$code=$pObj->fetch($option);
			break;	
			
		}
	
		//$after = memory_get_usage();
		
		//$this->db->log("pages: fetch($page): memory bytes before: $before, after: $after, delta: ". ($after-$before));

		return $code;
	}

	function checkForMessage() {
		if (isset($_GET[msgType])) {
			$msgType=$_GET[msgType];			
		} else
			return '';
		if (isset($_GET[msgTitle])) {
			$msgTitle=urldecode($_GET[msgTitle]);			
		} else {
			$msgTitle='Attention';
		}	
		$msg=urldecode($_GET[msg]);			
		return $this->buildMessage($msgType,$msgTitle,$msg);
	}
	
	function buildMessage($type='error',$title='We encountered a problem',$msg='No error message was provided') {
		$str='<fb:'.$type.' message="'.$title.'">'.$msg.'</fb:'.$type.'>';
		//$str='<div class="wideMsgPanel panel_1"><div class="bump10"><strong>'.$title.'</strong><br />'.$msg.'</div></div><!--end "wideMsgPanel"-->';
		return $str;
	}
	
	function fetchTeam($mode='fullPage',$page='',$option='') {
		// fetches team subtab via ajax
		switch ($page) {
			case 'team':
				require_once(PATH_FACEBOOK.'/pages/pageTeam.class.php');
				$teamObj=new pageTeam($this);				
				$code=$teamObj->fetch($mode,$option);
				break;
			case 'wall':
				require_once(PATH_FACEBOOK.'/pages/pageWall.class.php');
				$wallObj=new pageWall($this);				
				$code=$wallObj->fetch($mode,$option);
			break;		
			case 'rewards':
				require_once(PATH_FACEBOOK.'/pages/pageRewards.class.php');
				$prizesObj=new pageRewards($this);
				$code=$prizesObj->fetch($mode);
				break;
			case 'winners':
				require_once(PATH_FACEBOOK.'/pages/pageRewards.class.php');
				$prizesObj=new pageRewards($this);
				$code=$prizesObj->fetch($mode,'winners');
				break;
			case 'challenges':
				require_once(PATH_FACEBOOK.'/pages/pageChallenges.class.php');
				$challengesObj=new pageChallenges($this);
				$code=$challengesObj->fetch($mode);
				break;
			case 'challengeSubmit':
				require_once(PATH_FACEBOOK.'/pages/pageChallengeSubmit.class.php');
				$challengeSubmitObj=new pageChallengeSubmit($this);
				$code=$challengeSubmitObj->fetch($mode);
				break;
			case 'leaders':
				require_once(PATH_FACEBOOK.'/pages/pageLeaders.class.php');
				$leadersObj=new pageLeaders($this);
				$code=$leadersObj->fetch($mode,$option);
				break;
			case 'rules':
				require_once(PATH_FACEBOOK.'/pages/pageRules.class.php');
				$rulesObj=new pageRules($this);
				$code=$rulesObj->fetch($mode);
				break;
			case 'redeem':
				// check auth for member
				require_once(PATH_FACEBOOK.'/pages/pageRedeem.class.php');
				$redeemObj=new pageRedeem($this);
				$code=$redeemObj->fetch($mode);
				break;
			case 'orders':
				// check auth for member
				require_once(PATH_FACEBOOK.'/pages/pageOrders.class.php');
				$ordersObj=new pageOrders($this);
				$code=$ordersObj->fetch($mode);
				break;
				;
					
	
		}		
		return $code;
	}
	
	function setProfiling() {
		// we can do this to enable profiling of pages that are used more frequently than others
		$this->highFrequencyPages=array('home','read','team');		
	}
	
/*
 *	function authenticateTesters(&$session) {
		$testerIds=array(577894904,693311688,1008723516,666669,557740193,525416881,680884417,500012797,630396078,5610030,654537372,1154622334,617520362,756923320,688429164,694767315,692721990,5202908,1531373,13803681,718756128,876495577,1257967312,1202923507, 1176673740,1154274279);
		  if (array_search($session->fbId,$testerIds)===false) {
			$this->go404('This application is restricted to testers at this time.');
		}
		$devIds=array(577894904,693311688,1008723516,756923320);
		if (array_search($session->fbId,$devIds)!==false) {
			define ("DEBUG_GLOBAL",TRUE);
			define ("DEBUG_PROFILING",TRUE);				
		}
		
	}
	 
 */
 
	function buildStyles($p='') {
		if ($p=='design') {
			// to do - remove this after design testing
			$code='<link rel="stylesheet" type="text/css" media="screen" href="'.URL_CALLBACK.'?p=cache&type=css&cf=default.css&v='.rand(0,100000).'" />';
		} else {
			$this->pkgStyles(CACHE_PREFIX.'Facebook',array());
			$code=$this->_genStylesheets();
		}
		return $code;
	}
	
	function streamStyles() {
		$css=htmlentities(file_get_contents(PATH_FACEBOOK_STYLES.'/default.css', true));
		$css=preg_replace('/\s+-(moz|webkit).*/', '', $css);
		$css=str_replace('\"',"'",$css);
		$css='<style type="text/css">'.$css.'</style>';
		return $css;
	}

	function buildJavaScript() {
		$this->pkgScripts(CACHE_PREFIX.'Facebook',array());
		$script=$this->_genScripts();
		return $script;
	}

	function setHiddenVariables($pageName='home') {
		$code.='<input type="hidden" id="pageName" value="'.$pageName.'"><input type="hidden" id="ajaxNode" value="'.URL_CALLBACK.'">';
		return $code;
	}	
	
	function setHiddenSession() {
		$code='<input type="hidden" id="fb_sig_logged_out_facebook" value="'.(isset($_POST['fb_sig_logged_out_facebook'])?'1':'0').'">';
		$code.='<input type="hidden" id="fbId" value="'.$this->session->fbId.'">';
		$code.='<input type="hidden" id="userid" value="'.$this->session->userid.'">';
		$code.='<input type="hidden" id="sessionKey" value="'.$this->session->sessionKey.'">';		
		$code.='<input type="hidden" id="sessionExpires" value="'.$this->session->sessionExpires.'">';
		$code.='<input type="hidden" id="authLevel" value="'.$this->session->authLevel.'">';		
		$code.='<input type="hidden" id="memberFriends" value="'.$this->session->ui->memberFriends.'">';
		return $code;
	}

	function buildLoadingStatus() {
		$str='<fb:js-string var="loading"><div id="loadingStatus"><img src="http://www.newscloud.com/static/facebook/images/loading.gif"><!-- end loading status div --></div></fb:js-string><fb:js-string var="smallLoading"><div id="smallLoadingStatus"><img src="http://www.newscloud.com/static/facebook/images/loading.gif"><!-- end loading status div --></div></fb:js-string>';
		return $str;
	}
	
	function buildDialog() {
		// pop up dialog for publishing
		$str='<fb:js-string var="dialogText"><div id="dialog_content"><div class="dialog_loading">Processing...please wait a moment...</div></div></fb:js-string>'
.'<fb:js-string var="signupMsg">Please <a href="?p=signup'.(isset($_GET['referid'])?'&referid='.$_GET['referid']:'').'" '.(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').'>sign up</a> to become a member in order to perform this operation.</fb:js-string>'
.'<fb:js-string var="sessionMsg">Please visit <a href="?p=home">home page</a> to refresh your '.SITE_TITLE.' session.</fb:js-string>';		
		return $str;
	}

	function buildPanelBar($heading='',$links='',$subtitle='') {		
	     $code='<div class="panelBar clearfix">';
	     if ($heading<>'') $code.='<h2>'.$heading.'</h2>';
         $code.='<div class="bar_link">'.$links.'</div>'.
		'</div><!--end "panelBar"-->';
		if ($subtitle<>'') {
			$code.='<div class="subtitle"><span>'.$subtitle.'</span></div><!--end "subtitle"-->';
		}
		return $code;
	}
	
	function buildPageTabs($current='home',$includeWrap=true,$includeScript=true) {
		$tabs='<div class="tabs clearfix"><div class="right_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		$tabs.='<li class="first"><a id="tabHome" href="?p=home" onclick="switchPage(\'home\');return false;" class="'.($current=='home'?'selected':'').'">Home</a></li>';
		$tabs.='<li ><a id="tabTeam" href="?p=team" class="'.($current=='team'?'selected':'').'" onclick="switchPage(\'team\');return false;" >'.SITE_TEAM_TITLE.'</a></li>';
		$tabs.='<li ><a id="tabStories" href="?p=stories" onclick="switchPage(\'stories\');return false;" class="'.($current=='stories'?'selected':'').'">Stories</a></li>';
		$tabs.='<li ><a id="tabPostStory" href="?p=postStory" onclick="switchPage(\'postStory\');return false;" class="'.($current=='postStory'?'selected':'').'">Post a Story</a></li>';
		$tabs.='<li ><a id="tabProfile" href="?p=profile&memberid='.$this->session->fbId.'" class="'.($current=='profile'?'selected':'').'" onclick="switchPage(\'profile\',\'\','.$this->session->fbId.');return false;">My profile</a></li>'; 
		$tabs.='</ul></div><!--end "right_tabs"--></div><!--end "tabs"-->';
		if ($includeWrap) $tabs='<div id="pageTabs" class="clearfix">'.$tabs.'</div><!--end "pageTabs"-->';
		if (!$includeScript) $tabs= preg_replace('/on[cC]lick="[^"]+"/', '', $tabs); // remove script
		return $tabs;
	}

	function buildHeader() {
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
	
		include_once(PATH_TEMPLATES.'/header.php');
		return $header;
	}

	function buildFooter() {
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php'); // TODO keep common dynTemp instance
		$dynTemp = dynamicTemplate::getInstance($this->db);
		if ($this->session->isMember AND $this->session->u->ncUid>0) {
			$actCode = crypt ($this->session->u->ncUid, $this->session->u->email);
			$actCode = $actCode . "c"; // add a letter to ending period isn't broken by email programs			
			$actCode=str_replace('/','',$actCode); // // strip out forward slash so they don't mess up the url
			if ($this->session->isAdmin OR $this->session->u->isModerator OR $this->session->u->isSponsor OR $this->session->u->isResearcher) {
				$isConsole = true;
				if ($this->session->isAdmin OR $this->session->u->isResearcher)
					$isResearch = true;
				$email = $this->session->u->email;
			}
			// Switched the includes around to take into account admin status
			include_once(PATH_TEMPLATES.'/footer.php');
			$footer=str_replace('http://www.newscloud.com','http://www.newscloud.com/ver/home/'.htmlentities($this->session->u->email).'/'.htmlentities($actCode),$footer);			
		} else { 
			include_once(PATH_TEMPLATES.'/footer.php');
		}
		return $footer;
	}

 function paging($pageCurrent=1,$rowTotal=0,$rowLimit=7,$link='',$jscriptFunction='',$ajaxOn=false,$nav=NULL) {
 	// $link is the url that the page navigation will point to - this functions add the page offset as the suffix
 	// e.g. $link ='/search/keyword/tag/sort/' ... pages will link to '/search/keyword/tag/sort/pagenumber/'
 	// previous query must use SQL_CALC_FOUND_ROWS
 	$pageTotal=ceil($rowTotal/$rowLimit);
 	$nav->last=$pageTotal;
 	$nav->current=$pageCurrent;
 	$pageStart=($pageCurrent-4)>0 ? ($pageCurrent-4) : 1;
 	$pageEnd=($pageCurrent+4)>$pageTotal ? $pageTotal : ($pageCurrent+4);
 	$ellipsis='<span>...</span>';
 	if ($rowTotal==0)
 	return '';
 	$text='<div class="pages">';
 	// previous page
 	if ($pageCurrent>1) {
 		$text.='<a href="#" class="nextprev" onclick="refreshPage('.($pageCurrent-1).');">&#171; Previous</a>';
 		$nav->previous=$pageCurrent-1;
 	} else {
 		$text.='<span class="nextprev">&#171; Previous</span>';
 		$nav->previous=1;
 	}
 	// page 1 & 2
 	if ($pageCurrent>5)
 	$text.='<a href="#" onclick="refreshPage(1);">1</a><a href="#" onclick="refreshPage(2);">2</a>'.$ellipsis;
 	// current nine pages
 	for ($i=$pageStart;$i<=$pageEnd;$i++) {
 		if ($i==$pageCurrent)
 		$text.='<span class="current">'.$i.'</span>';
 		else
 		$text.='<a href="#" onclick="refreshPage('.$i.');" >'.$i.'</a>';
 	}
 	if (($pageTotal-$pageCurrent)>5)
 	$text.=$ellipsis.'<a href="#" onclick="refreshPage('.($pageTotal-1).');">'.($pageTotal-1).'</a><a href="#" onclick="refreshPage('.$pageTotal.');">'.$pageTotal.'</a>';
 	// next page
 	if ($pageCurrent<$pageTotal) {
 		$text.='<a href="#" class="nextprev" onclick="refreshPage('.($pageCurrent+1).');">Next &#187;</a>';
 		$nav->next=$pageCurrent+1;
 	} else {
 		$nav->next=$pageCurrent;
 		$text.='<span class="nextprev">Next &#187;</span>';
 	}
 	$text.='</div>';
 	return $text;
 }

 function addAnalytics($googleCode='',$page='') {
 	$str='<fb:google-analytics uacct="'.$googleCode.'" page="Facebook: '.$page.'"/>';
 	return $str;
 }

 /*
   if (TEST_MODE=='ALPHA') {
 		$code='<div id="testBar">'.SITE_TITLE.' is still in testing.<!-- end test bar --></div>'.$code;
 	}
  */
function display($code) {
 	if (isset($_POST['fb_sig_logged_out_facebook'])) $code = preg_replace('/on[cC]lick="[^"]+"/', '', $code); // remove jscript
 	if (isset($_GET['src'])) $code=preg_replace('/\?p=([^"]+)/', '?p=$1&src='.$_GET['src'], $code); 
 	echo $code;
 }

function fetchReferral() {
	// lookds for referid userid or referfbid facebook id
	// returns userid
	if (isset($_GET['referid'])) {
		$referid=$_GET['referid'];
	} else if (isset($_GET['referfbid'])) {
		$referfbid=$_GET['referfbid'];
		// look up userid from facebook id
		require_once(PATH_CORE .'/classes/user.class.php');
		$userInfoTable = new UserInfoTable($this->db);
		$userinfo = $userInfoTable->getRowObject();
		if ($userinfo->loadFromFbId($referfbid)!==false)
		{	
			if ($referid != $userinfo->userid) // prevent referid from getting set to self in bizzare cases 
				$referid=$userinfo->userid; 
		}
		else
			return false;
	} else {
		return false;
	}	
	return $referid;
}

function recordSrc() {
	if (isset($_POST['fb_sig_user'])) { 
		$fbId=$_POST['fb_sig_user'];
	} else if (isset($_POST['fb_sig_canvas_user'])) {
		$fbId=$_POST['fb_sig_canvas_user'];
	} else
		$fbId=0;									
	if (isset($_GET['src']) AND $fbId>0) {
		$this->db->insert("AdTrack","source,userid","'".$_GET['src']."',".$fbId);
	} else if (isset($_GET['viaAdGreen'])) {
		$this->db->insert("AdTrack","source,userid","'grn teens',".$fbId);
	} else if (isset($_GET['viaAdRingtones'])) {
		$this->db->insert("AdTrack","source,userid","'AdRingtones',".$fbId);
	}
}

function recordReferral($referid=0,$action='',$itemid=0) {
	// record the referral in the log
	// $this->session->userid was referred to $action page by $referid userid, $itemid may be siteContentId
	// action may be referReader or referToSite
	if ($this->session->isLoaded AND $this->session->userid<>0) {
		// log referid as having referred this user
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($this->db);
		$logItem=$logObj->serialize(0,$referid,$action,$itemid,$this->session->userid);
		$inLog=$logObj->update($logItem);
		// check if UserInfo.refuid has not been set before
		if ($this->session->u->refuid==0) {
			// load the userinfo for this user
			
			$this->session->ui->refuid=$referid;
			$this->session->ui->update();
		
		}
		// sign up page will use refuid to mark invites as accepted
	}
}

function go404($msg='') {
	$this->facebook=$this->app->loadFacebookLibrary();
	$this->facebook->redirect(URL_CANVAS.'?p=404&msg='.$msg);	
}

 function debug() {
 	if (isset($_GET['debug']))
 	{
 		echo 'POST<br/>';
 		var_dump($_POST);
 		echo 'GET<br/>';
 		var_dump($_GET);
	 	echo $this->session->debug();
 	}
 }

 /* Template functions */

 // to do - probably need to move to separate class

 function shortAbstract($str,$cnt=150) {
 	$str=strip_tags($str);
 	if (strlen($str)>$cnt)
 	$str=substr($str,0,($cnt-1)).'...';
 	return $str;
 }

	function pkgScripts($page='default',$scripts='') {		
		$scripts=array_merge(array(PATH_SCRIPTS.'/newsroom.js'),$scripts);
		$this->scripts[]=URL_CALLBACK."?p=cache&type=js&cf=".$page."_".$this->fetchPkgVersion($page,$scripts,'js',true).".js";
	}

	function pkgStyles($page='default',$sheets) {
		// packages get common, header and layout
		$sheets=array_merge(array(PATH_STYLES.'/default.css',PATH_STYLES.'/paging.css'),$sheets);
		// to do: remove - old	$sheets=array_merge(array(PATH_STYLES.'/newsroom.css',PATH_STYLES.'/paging.css',PATH_STYLES.'/tabs.css'),$sheets);
		$this->stylesheets[]=URL_CALLBACK."?p=cache&type=css&cf=".$page."_".$this->fetchPkgVersion($page,$sheets,'css',false,true).".css";
	}

	function fetchPkgVersion($page,$files,$mode='js',$jsCompress=false,$cssCompress=false) {
	   define('JSMIN_AS_LIB', true); 
	  // get file last modified dates
	  $aLastModifieds = array();
	  foreach ($files as $sFile) {
	     $aLastModifieds[] = filemtime($sFile);
	  }
	  // sort dates, newest first
	  rsort($aLastModifieds);
 	 $iETag=$aLastModifieds[0];	
       // create a directory for storing current and archive versions
      if (!is_dir(ARCHIVE_FOLDER)) {
         mkdir(ARCHIVE_FOLDER);
      }
      
      $sMergedFilename = ARCHIVE_FOLDER."/".$page."_".$iETag.".".$mode;
      // if it does not exist, we need to create a new merged package
      if (!file_exists($sMergedFilename)) {
         // get and merge code
         $sCode = '';
         $aLastModifieds = array();
         foreach ($files as $sFile) {
            $aLastModifieds[] = filemtime($sFile);
            $sCode .= file_get_contents($sFile);
         }
         // sort dates, newest first
         rsort($aLastModifieds);
         // reset iETag incase of late breaking file update
	 	 $iETag=$aLastModifieds[0];         
	      $sMergedFilename = ARCHIVE_FOLDER."/".$page."_".$iETag.".".$mode;
           $this->pkgWrite($sMergedFilename, $sCode);
           if ($jsCompress) {
			  require_once(JSMIN_PATH."/jsmin.php");
               $jsMin = new JSMin(file_get_contents($sMergedFilename), false);
              $sCode = $jsMin->minify();              
              $this->pkgWrite($sMergedFilename, $sCode);
           } else if ($cssCompress) {           		
    		  require_once(JSMIN_PATH."/cssMin.php");
               $cssMin = new cssMin();               
              $sCode = $cssMin->minify(file_get_contents($sMergedFilename));                             
              $this->pkgWrite($sMergedFilename, $sCode);                      	
           }
      }
	  // return latest timestamp
	 return $iETag;
	}	
	
   function pkgWrite($sFilename, $sCode) {
      $oFile = fopen($sFilename, 'w');
      if (flock($oFile, LOCK_EX)) {
         fwrite($oFile, $sCode);
         flock($oFile, LOCK_UN);
      }
      fclose($oFile);
   }
  
 	function _genStylesheets()
	{
		$ret = '';
		foreach (array_unique($this->stylesheets) as $key => $val) {
			$ret .= '<link rel="stylesheet" href="' . $val . '" type="text/css" charset="utf-8" />';
		}
		return $ret;
	}

	function _genScripts()
	{
		$ret = '';
		foreach (array_unique($this->scripts) as $key => $val) {
			$ret .= '<script src="' . $val . '" type="text/javascript" language="javascript" charset="utf-8"></script>';			
		}
		return $ret;
	}  
}
?>