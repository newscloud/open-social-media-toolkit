<?php
class page404 {

	var $page;
	var $db;
	var $facebook;
	var $session;
	var $app;
	var $templateObj;
	var $rowsPerPage=10;
	
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->session=&$page->session;
	}

	function fetch($msg='') {
		if ($msg=='') {
			$inside='<h1>We could not find the page you were looking for. Return to the <a href="'.URL_CANVAS.'">home page</a>.</h1>';
		} else
			$inside='<h1>'.$msg.'</h1>';
		$code=$this->page->constructPage('404',$inside);		
		return $code;
	}

}

?>