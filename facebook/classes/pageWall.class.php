<?php

/* Provides Facebook-client specific action team help */
class pageWall {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $isAppTab=false;

	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'teamactivity');               				
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
	}

	function fetch($mode='fullPage',$option='alltime') {
		$tabs.=$this->teamObj->buildSubNav('wall');		
		$inside.='<div id="col_left"><!-- begin left side -->';
		$inside.='<fb:comments xid="'.CACHE_PREFIX.'_wall" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=wall"></fb:comments>';	
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->teamObj->fetchSidePanel('leaders');		
		$inside.='</div> <!-- end right side -->';
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside);		
		return $code;
	}		

}	
?>