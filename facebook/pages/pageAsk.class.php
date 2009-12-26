<?php

/* Provides Facebook-client specific action team help */
class pageAsk {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $askObj;
	var $isAppTab=false;

	function __construct(&$page) {
		$this->page=&$page;		
		$this->session=$this->page->session;
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'ask');               				
		require_once(PATH_FACEBOOK.'/classes/ask.class.php');
		$this->askObj=new ask($this->db,$this->templateObj,$this->session);
	}
	
	function fetch($option='ask') {
		if ($option=='') $option='ask'; // fix default pass thru for tabs
		$inside.=$this->buildSubNav($option);	
		$inside.='<div id="col_left"><!-- begin left side --><br />';
		switch ($option) {
			default: // ask page
				$inside.=$this->buildAsk();
			break;
			case 'askSubmit':
				$inside.=$this->buildAskSubmit();			
			break;
			case 'me': // my questions
				$inside.=$this->buildMyQuestions();
			break;
			case 'browse':
				$inside.=$this->buildBrowseQuestions();
			break;
			case 'answer':
				$inside.=$this->buildAnswer();
			break;
			case 'question':
				$id=$_GET['id'];
				if (!is_numeric($id)) $this->page->decloak();				
				$inside.=$this->askObj->buildQuestionDisplay($id,isset($_GET['share']));	// show or hide share 
			break;
		}
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->buildSidebar();		
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('ask',$inside);		
		return $code;
	}		
	
	function buildSidebar() {
		$code='';
		$cacheName='askTags';
		if ($this->templateObj->checkCache($cacheName,15)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp=$this->askObj->fetchAskTags();
			$this->templateObj->cacheContent($cacheName,$temp);
		}
		$code.=$temp;
		$cacheName='askUnansweredQuestions';
		if ($this->templateObj->checkCache($cacheName,15)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp=$this->askObj->fetchSidebarItem($cacheName);
			$this->templateObj->cacheContent($cacheName,$temp);
		}
		$code.=$temp;
		$cacheName='askPopularQuestions';
		if ($this->templateObj->checkCache($cacheName,15)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp=$this->askObj->fetchSidebarItem($cacheName);
			$this->templateObj->cacheContent($cacheName,$temp);
		}
		$code.=$temp;
		$cacheName='sideLeaders';
		if ($this->templateObj->checkCache($cacheName,700)) {
			$temp=$this->templateObj->fetchCache($cacheName);
			$code.=$temp;
		}
		return $code;	
	}
	 
	function buildAsk() {
		$code='';
		$code.=$this->askObj->buildQuestionForm();
		$code.='<br />';
		$code.=$this->templateObj->templates['askIntroAnswer'];
		$code.=$this->askObj->listQuestions('recent',0,($this->session->isLoaded?$this->session->userid:0),0,5);
		return $code;
	}

	function buildMyQuestions() {
		$code='<h1>Your Questions</h1>';
		$code.=$this->askObj->listQuestions('me',0,$this->session->userid,0,99);
		return $code;
	}

	function buildBrowseQuestions() {
		if (isset($_GET['tagid'])) {
			$tagid=$_GET['tagid'];
			if (!is_numeric($tagid)) $this->page->decloak();					
		} else
	 		$tagid=0;
		if (isset($_GET['view'])) {
			$view=$_GET['view'];
			if (strlen($view)>20) $this->page->decloak();
		} else
	 		$view='recent';
		$code='<h1>Answer Questions</h1>';
		$code.=$this->askObj->fetchBrowseQuestions(false,$tagid,$this->session->userid,$view);
		return $code;
	}

	function buildAskSubmit() {
		$code='';
		$resp=$this->processQuestionForm();
		if ($resp['error']===true) {
			$code.=$this->page->buildMessage('error','There was a problem adding your question',$resp['msg']);
			$code.=$this->askObj->buildQuestionForm();
		} else {
			$code.=$this->page->buildMessage('success','Success!','Your question has been posted!');
			$code.=$this->askObj->buildQuestionDisplay($resp['id'],true);
		}
		return $code;
	}
	
	function processQuestionForm() {
		// check if we have a session
		if (!$this->session->isLoaded) {
			$this->facebook=$this->app->loadFacebookLibrary();
			$user = $this->facebook->require_login();
		} 
		$resp=$this->askObj->processQuestionForm($this->session->userid);		
		return $resp;
	}

	function buildSubNav($currentSub='send') 
	{
		$pages = array(
		 'ask' => 'Ask a Question',
		 'browse'=> 'Answer Questions',
		 'me'=> 'My Questions'
		 );
		// to do - conditionally add my questions
		
		 $tabs='<div id="subNav" class="tabs clearfix"><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		 $i=0;
		 foreach (array_keys($pages) as $pagename)
		 {		 	
		 	if ($i==0) {
		 		$clsName='class="first"';
		 	} else {
		 		$clsName='';	
		 	}
	 		$tabs.='<li '.$clsName.'><a id="subtab'.$pagename.'" href="?p=ask&o='.$pagename.'" '.($pagename=='me'?'requirelogin="1"':'').' '.($currentSub==$pagename?'class="selected"':'').'>'.$pages[$pagename].'</a></li>';	
		 	$i++;
		 }
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
	 	return $tabs;
 	}	
}	
?>