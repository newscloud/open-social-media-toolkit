<?php

class pageRules {

	var $page;
	var $db;
	var $facebook;
	var $fbApp;
	var $teamObj;
	var $templateObj;
		
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
	}

	function fetch($mode='fullPage') {
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		$tabs.=$this->teamObj->buildSubNav('rules');	 
		
		// lame!
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
	
		
		include (PATH_TEMPLATES . '/rules.php');
		$inside.=$static;
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';		
		if ($this->page->isAjax) return $inside;	
		
		if (isset($_GET['currentPage']))
			$currentPage=$_GET['currentPage'];
		else
			$currentPage=1;			
		$code=$this->page->constructPage('rules',$inside);
/* switched out with constructPage above on 7-31-09 by jr
		$code.=$this->page->buildJavaScript();
		$code.=$this->page->setHiddenVariables();
		$code.=$this->page->buildLoadingStatus();
		$code.='<div id="pageBody">';
		$code.=$this->page->buildPageTabs('team');
		$code.='<div id="pageContent">';	
		//$this->templateObj->registerTemplates(MODULE_ACTIVE, 'rules');		
		$code .= $inside; // variable containing text created inside the rules.php template file 
		$code.=$this->page->buildDialog();
		$code.='</div><!-- end pageContent -->';
		$code.='</div><!-- end pageBody -->';
		*/
		return $code;
	}
	
}

?>