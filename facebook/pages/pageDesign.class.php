<?php

class pageDesign {

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
	}

	function fetch($option='Home') {
	    header("Content-type: text/html");
	    if (!isset($_GET['noStyles'])) {
			$sheets=array(PATH_STYLES.'/default.css');
			$this->page->stylesheets[]=URL_CALLBACK."?p=cache&type=css&cf=design_".$this->page->fetchPkgVersion('design',$sheets,'css',false).".css";
			$ret = '';
			foreach (array_unique($this->page->stylesheets) as $key => $val) {
				$ret .= '<link rel="stylesheet" href="' . $val . '" type="text/css" charset="utf-8" />';
			}
			echo $ret;	    	
	    }
		readfile(SRC_ROOT.'/sites/climate/facebook/templates/design'.ucfirst($option).'.html');
		exit;
	}
}

?>