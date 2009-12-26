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
			case 'setAdmin':
				$this->db->delete("fbSessions");
				// only works if no admin exists with fbId
				$q1=$this->db->queryC("SELECT * FROM User,UserInfo WHERE User.userid=UserInfo.userid AND fbId<>0 AND isAdmin=1");
				if ($q1!==false) {
					$static='<p>Error: Another administrator is already registered.</p>';
				} else {
					$this->facebook=$this->page->app->loadFacebookLibrary();
					$fbId = $this->facebook->require_login();
					if ($fbId<>0 AND is_numeric($fbId)) {
						$q=$this->db->queryC("SELECT userid FROM UserInfo WHERE userid=$fbId");
						if (!$q) {
							// set fbId to existing userinfo 
							$q3=$this->db->queryC("SELECT * FROM User,UserInfo WHERE User.userid=UserInfo.userid AND fbId=0 AND isAdmin=1 ORDER BY User.userid ASC LIMIT 1");
							if ($q3!==false) {
								$d=$this->db->readQ($q3);
								$this->db->update("UserInfo","fbId=$fbId","userid=".$d->userid);								
							} else {
								$static='<p>Error: Try using the management console to set your Facebook id and admin status manually.</p>';						
							}
						} else {
							// set current user as admin
							$d=$this->db->readQ($q);
							$userid=$d->userid;
							$this->db->update("User","isAdmin=1,ncUid=".rand(1,99999),"userid=$fbId");
						}
						$static='<p>Success: You are now registered as an admin. The next time you visit the <a href="'.URL_CANVAS.'">Facebook application</a> you will see an Admin link to the management console in the footer below.</p>';
					} else 
						$static='<p>Error: Could not get your Facebook account information. Try refreshing this page</p>';
				}
			break;
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