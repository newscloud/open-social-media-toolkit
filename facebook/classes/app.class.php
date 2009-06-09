<?php

class app  {

		var $db;
		var $isAjax=false;		
		var $facebook=false; // Facebook session

		// viewer variables
		var $session;
		var $ssObj;
		
		// Facebook platform properties
		// to do - need to have a more sophisticated admin table
		var $notifications_per_day; // number of notifications allocated by Facebook for your app see http://www.facebook.com/business/insights/app.php?id={appid}&tab=allocations
		var $requests_per_day; // number of requests allocated

		function __construct($facebook=NULL,$isAjax=false) {
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
				
		function quickLog($obj=NULL,$log='default') {
			if ($obj == NULL)
				$obj = $this->getActivityLog();
			// embed query arguments for page in session entry
			$obj->qs=$_SERVER['QUERY_STRING'];
			// embed current timestamp
			$obj->t=time();
			$str=serialize($obj);
			// e.g. log to 20090101_prefix_default.log
			$filename=date('Ymd').'_'.CACHE_PREFIX.'_'.$log.'.log';
			$fHandle=fopen(PATH_LOGS.'/'.$filename,'a');
			if ($fHandle!==false) {
				fwrite($fHandle,$str."\n");
				fclose($fHandle);
			}			
		}
		
		function getActivityLog()
		{
			require_once(PATH_CORE.'/classes/log.class.php');
			$log = new log($this->db);
			return $log;
		}
		
}
?>