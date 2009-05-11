<?php
class pageLinks {

	var $page;
	var $db;
	var $facebook;
	var $app;
	var $templateObj;
	var $rowsPerPage=10;
	
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
	}

	function fetch() {
		$inside='';
		// cron regularly updates the featured story, so no need to check cache - see updateCache in cron.class.php and cache.class.php
		//$code=$pageObj->fetchCache(CACHE_PREFIX.'FeaturedStory');
		if (defined('ADS_ANY_LARGE_BANNER')) {
			$inside.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=anyLargeBanner" frameborder="0" scrolling="no" style="width:738px;height:100px;padding:0px;margin:-20px 0px 10px 0px;"/>',$this->page->common['adWrapLargeBanner']);
		}					
		$inside.=$this->fetchPage();
		if ($this->page->isAjax) return $inside;
		$code=$this->page->constructPage('links',$inside);		
		return $code;
	}

	function fetchPage() {
		require_once(PATH_CORE.'/classes/resources.class.php');
		$resObj=new resources($this->db);
		$code.=$resObj->buildLinksColumn();
		return $code;
	}
}

?>