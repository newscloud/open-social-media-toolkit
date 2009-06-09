<?php

/* Provides Facebook-client specific story sidebars help */
class storyPanels {
	
	var $page;
	var $db;
		
	function __construct(&$page) {
		$this->page=&$page;
		$this->db=&$page->db;
	}
	
	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db); 
	}	
	
	function fetchStoryPanels() {
		$cacheName='sideLeaders';
		if ($this->templateObj->checkCache($cacheName,30)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp=$this->fetchStoryList('rated','inside',5);
			$temp.=$this->fetchStoryList('discussed','inside',5);				
			$this->templateObj->cacheContent($cacheName,$temp);
		}
		$code.=$temp;		
	}
	
	function fetchStoryList($mode='read') {
		// filter log in date range
	}
	
}	
?>