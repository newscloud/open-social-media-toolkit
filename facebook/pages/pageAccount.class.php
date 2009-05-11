<?php

/**** MIGHT WANT TO BORROW OR EXTEND pageSignup class elements ****/

class pageAccount {

	var $page;
	var $db;
	var $facebook;
	var $fbApp;
	var $templateObj;
	var $teamObj;
		
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

	function fetch($option='settings') {
		// build the signup page
		if ($this->page->isAjax) {
			$this->facebook=&$this->page->app->loadFacebookLibrary();
		}

		if (isset($_GET['message']))
		{
			$msg =$_GET['message'];
		
			switch($msg)
			{
				case 'success' : $inside .= $this->page->buildMessage('success','Update Successful','<p>You have successfully updated your account information</p>');
				
			}
		}
		
		if (isset($_GET['nukeMe'])) // TODO: debug option, disable for release ?
		{
			$inside .= 'Nuking....';
			$inside .= $this->debugNukeUser();
		} else
		{				
			switch ($option) {
				default:
					$inside .= $this->fetchAccountSettings();
				break;
				case 'subscribe':
					$inside.=$this->fetchSubscribeSettings();
				break;
				case 'oldmode':
					$inside.='<a href="?p=account&settings">Account Settings</a><br>';
				
					$inside.='<a href="?p=orders">Order History</a><br>';
				
					$inside.='<a href="?p=account&nukeMe">Nuke my user records (debug only - very dangerous!)</a><br>';
						
					$inside .= "activities, stories, friends also go here<br>";
				break;
			}
		}			
		if ($this->page->isAjax) return $inside;
		
			
	
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside,'');		
		return $code;
	}
	
	function fetchSubscribeSettings()
	{
		// this page lets users set FB contact permissions and push settings
		require_once(PATH_FACEBOOK .'/classes/account.class.php');
		$account = new account($this->db, $this->page->session);
	
		if (isset($_GET['step']) && $_GET['step'] == 'submit')
		{
		
		/*	
			$fdata = $account->validateFormData(false); // validate for existing account
								
			if ($fdata->result)
			{
				$fdata = $account->processFormUpdateDatabase($fdata);
			} 
			
			if (!$fdata->result) // can be either validation failure or processing failure
			{
				$code .= $account->buildAccountSettingsForm($fdata, $fdata->alert);
				
			} else
			{
				// success -- just redisplay w/ updated data
				$code .= "Update successful..."; 
				$this->facebook->redirect(URL_CANVAS.'?p=account&message=success');
			}	
			*/
		} else
		{
			
			$fdata = $account->initFormDataFromDatabase($this->page->session->userid);
							
			$code.='<h1>Subscription Settings</h1><h5>Please update your subscription settings below.</h5>';
			if ($this->page->session->u->ncUid==0) {
				$code.=$this->page->buildMessage('error','Please verify your email address','We do not have a record of you verifying your email address. Please look in your email and spam folder for a verification request link. If you can\'t find one, <a href="#" onclick="requestVerify();return false;">request another here</a>.');	
			}
			
			$code.=$account->buildAccountSubscribeForm($fdata);
	
		}	
		return $code;
		
	}
	
	function fetchAccountSettings()
	{

		require_once(PATH_FACEBOOK .'/classes/account.class.php');
		$account = new account($this->db, $this->page->session);
	
		if (isset($_GET['debug'])) $account->debug = $_GET['debug'];
	
				
		if ($account->debug) echo '$_POST:<pre>'.print_r($_POST,true).'</pre>';	
		
			
		if (isset($_GET['step']) && $_GET['step'] == 'submit')
		{
			
			$fdata = $account->validateFormData(false); // validate for existing account
			if ($this->debug) echo '<pre>'.print_r($fdata,true).'</pre>';	
		
						
			if ($fdata->result)
			{
				$fdata = $account->processFormUpdateDatabase($fdata);
			} 
			
			if (!$fdata->result) // can be either validation failure or processing failure
			{
				$code .= $account->buildAccountSettingsForm($fdata, $fdata->alert);
				
			} else
			{
				// success -- just redisplay w/ updated data
				$code .= "Update successful..."; 
				$this->facebook->redirect(URL_CANVAS.'?p=account&message=success');
			}	
			
		} else
		{
			
			$fdata = $account->initFormDataFromDatabase($this->page->session->userid);
			$fdata->researchImportance=$this->page->session->ui->researchImportance;
							
			$code.='<h1>Account Settings</h1><h5>Please update your account settings below.</h5>';
			if ($this->page->session->u->ncUid==0) {
				$code.=$this->page->buildMessage('error','Please verify your email address','We do not have a record of you verifying your email address. Please look in your email and spam folder for a verification request link. If you can\'t find one, <a href="#" onclick="requestVerify();return false;">request another here</a>.');	
			}
			
			$code.=$account->buildAccountSettingsForm($fdata);
	
		}
		
		
		
		return $code;
		
		
	}
	
	
	
	function debugFetchUserInfo($userid)
	{
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); 
		$userInfoTable = new UserInfoTable($this->db);

		dbRowObject::$debug = 1; // NEVER TURN ON FOR LIVE SITE
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();
		
		if (!$user->load($userid) ||
			!$userinfo->load($userid))
		{
			$code.= 'Couldnt load user or userinfo for userid='. $userid;
			return;
		}
		
	
		$code .= 'User: '.$user->debugPrint(). 'UserInfo:'.$userinfo->debugPrint().'';
		return $code;
	}

	function debugNukeUser()
	{
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); 
		$userInfoTable = new UserInfoTable($this->db);
		
		$user = $userTable->getRowObject();
		$userInfo = $userInfoTable->getRowObject();
		if ($user->load($this->page->session->userid))
		{
			$user->delete();
			$code .= 'User entry deleted...';
		}
		
		if ($userInfo->load($this->page->session->userid))
		{
			$userInfo->delete();
			$code .= 'UserInfo entry deleted...';
		}
		
		return $code; 
	}

}

?>