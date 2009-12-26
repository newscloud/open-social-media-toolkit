<?php

class common  {

		var $db;
		var $facebook=false; // Facebook session
		var $session;

		// to do optional db
		function __construct() {
		}
		
		function initObjs() {
			// initialize class
			require_once (PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
			require_once (PATH_CORE.'/classes/systemStatus.class.php');
			$this->ssObj=new systemStatus($this->db);
			$this->ssObj->getProperties(&$this);
			if (!is_null($facebook))
				$this->facebook=&$facebook; // must do before setupTemplates();
	 		require_once (PATH_FACEBOOK.'/classes/session.class.php');
			$this->session=new session($this);				
			$this->isAjax=$isAjax;
			if (!$isAjax) {
				$this->session->setupSession();
			} else {
				// to do - setup session via jscript rather than POST
			} 
		}	
		
		function loadFacebookLibrary() {
			if (!$this->facebook) {
		 		global $init;
				include_once PATH_FACEBOOK.'/lib/facebook.php';
				$this->facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);									
			}
			return $this->facebook;
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
				
}
?>