<?php

/* Crowdsharing ideas class */
class pageIdeas {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $iObj;
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
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'ideas');               				
		require_once(PATH_FACEBOOK.'/classes/ideas.class.php');
		$this->iObj=new Ideas($this->db,$this->templateObj,$this->session);
	}
	
	function fetch($option='browse') {
		if ($option=='') $option='browse'; // fix default pass thru for tabs
		$inside.=$this->buildSubNav($option);	
		$inside.='<div id="col_left"><!-- begin left side --><br />';
		switch ($option) {
			default: // browse page
				$inside.=$this->buildBrowseIdeas();
			break;
			case 'add': // suggest an idea
				$inside.=$this->buildAddIdea();
			break;
			case 'addSubmit': // submit the idea form
				$inside.=$this->buildIdeaAddSubmit();			
			break;
			case 'me': // my ideas
				$inside.=$this->buildMyIdeas();
			break;
			case 'view': // show a single idea
				$id=$_GET['id'];
				if (!is_numeric($id)) $this->page->decloak();
				$inside.=$this->iObj->buildIdeaDisplay($id,isset($_GET['share']));	// show or hide share 
			break;
		}
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->buildSidebar();		
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('ideas',$inside);		
		return $code;
	}		
	
	// option methods
	
	function buildBrowseIdeas() {
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
		if (isset($_GET['type'])) 
			$type=$_GET['type'];
	 	else
	 		$type='share';
		if (isset($_GET['status'])) 
			$status=$_GET['status'];
	 	else
	 		$status='available';
		$code='<h1>Browse Ideas</h1>';
		$code.=$this->iObj->fetchBrowseIdeas(false,$tagid,$view);
		return $code;
	}
		
	function buildAddIdea() {
		$code='';
		$code.=$this->iObj->buildIdeaForm();
		$code.='<br />';
		$code.=$this->templateObj->templates['ideasIntroAdd'];
		$code.=$this->iObj->listIdeas('recent',0,$this->session->userid,0,5);
		return $code;
	}
	
	// sidebar methods

	function buildSidebar() {
		$code='';
		$cacheName='ideasTags';
		if ($this->templateObj->checkCache($cacheName,15)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp=$this->iObj->fetchIdeasTags();
			$this->templateObj->cacheContent($cacheName,$temp);
		}
		$code.=$temp;
		$cacheName='ideasNew';
		if ($this->templateObj->checkCache($cacheName,15)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp=$this->iObj->fetchSidebarItem($cacheName);
			$this->templateObj->cacheContent($cacheName,$temp);
		}
		$code.=$temp;
		$cacheName='ideasPopular';
		if ($this->templateObj->checkCache($cacheName,15)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp=$this->iObj->fetchSidebarItem($cacheName);
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
	 
	function buildMyIdeas() {
		$code='<h1>Your Ideas</h1>';
		$code.=$this->iObj->listIdeas('me',0,$this->session->userid,0,99);
		return $code;
	}

	function buildIdeaAddSubmit() {
		$code='';
		// check if we have a session
		if (!$this->session->isLoaded) {
			$this->facebook=$this->app->loadFacebookLibrary();
			$user = $this->facebook->require_login();
		} 
		$resp=$this->iObj->processIdeaForm($this->session->userid);		
		if ($resp['error']===true) {
			$code.=$this->page->buildMessage('error','There was a problem adding your question',$resp['msg']);
			$code.=$this->iObj->buildIdeaForm();
		} else {
			$code.=$this->page->buildMessage('success','Success!','Your question has been posted!');
			$code.=$this->iObj->buildIdeaDisplay($resp['id'],true);
		}
		return $code;
	}
	
	function buildSubNav($currentSub='send') 
	{
		$pages = array(
		 'browse' => 'Browse Ideas',
		 'add'=> 'Suggest an Idea',
		 'me'=> 'My Ideas'
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
	 		$tabs.='<li '.$clsName.'><a id="subtab'.$pagename.'" href="?p=ideas&o='.$pagename.'" '.($pagename=='me'?'requirelogin="1"':'').' '.($currentSub==$pagename?'class="selected"':'').'>'.$pages[$pagename].'</a></li>';	
		 	$i++;
		 }
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
	 	return $tabs;
 	}	
}	
?>