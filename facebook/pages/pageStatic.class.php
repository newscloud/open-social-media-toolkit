<?php

class pageStatic {

	var $page;
	var $db;
	var $facebook;
	var $fbApp;
	var $templateObj;
		
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		// $this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
	}

	function fetch($option='about') 
	{
		
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
		
		switch ($option) {
			case 'testing':
				include_once(PATH_TEMPLATES.'/testingNotice.php');
			break;
			case 'privacy':
			case 'tos':
				include_once(PATH_TEMPLATES.'/tos.php');
			break;
			case 'consent':
				include_once(PATH_TEMPLATES.'/consent.php');
			break;			
			case 'rules':
				include_once(PATH_TEMPLATES.'/rules.php');
			break;
			case 'faq':
				include_once(PATH_TEMPLATES.'/faq.php');
			break;
			case 'offline':
				include_once(PATH_TEMPLATES.'/offline.php');
			break;
			case 'maxSessions':
				include_once(PATH_TEMPLATES.'/maxSessions.php');
			break;
			case 'eqex':
				if ($this->page->session->isLoaded AND $this->page->session->isMember)
					include_once(PATH_TEMPLATES.'/eqex.php');
				else {
					$static='<br />'.$this->page->buildMessage('error','Membership Required','Please <a href="?p=signup">sign up for Hot Dish</a> in order to access this page.').'<br />';
				}
			break;
			case 'cbd':
				if ($this->page->session->isLoaded AND $this->page->session->isMember)
					include_once(PATH_TEMPLATES.'/cbd.php');
				else {
					$static='<br />'.$this->page->buildMessage('error','Membership Required','Please <a href="?p=signup">sign up for Hot Dish</a> in order to access this page.').'<br />';
				}
			break;
			default: // about
				include_once(PATH_TEMPLATES.'/about.php');
			break;
		}
		$inside.=$static;
		if ($this->page->isAjax) return $inside;
		$code=$this->page->constructPage('static',$inside);				
		return $code;
	}

}

?>