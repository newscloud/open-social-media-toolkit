<?php

class pageSignup {

	var $page;
	var $db;
	var $facebook;
	var $app;
	var $session;
	var $templateObj;

	var $debug;
	
	function __construct(&$page) {
		$this->page=&$page;
		$this->session=&$page->session;		
		$this->db=&$page->db;		
		$this->app=&$page->app;
		$this->facebook=$this->app->loadFacebookLibrary();
		
/*
 * 		$this->facebook=&$page->facebook;
		if ($this->page->isAjax) {
			$this->facebook=&$this->app->loadFacebookLibrary();			
		}
	
 */}

	function fetch($option='') {
		// build the signup page
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);		
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'signup');               

		$referid=$this->page->fetchReferral();
		if ($referid!==false)
			$this->page->recordReferral($referid,'referToSite');
		$this->page->recordSrc();
		
		$inside.='<div id="col_left"><!-- begin left side -->';
		$this->session->syncFacebookData($this->session->fbId);
		
		// members should never see the signup page more than once!
		if ($this->session->isMember && !isset($_GET['force']))
		{
			$this->facebook->redirect(URL_CANVAS.'?p=team');	
		}
		
		require_once(PATH_FACEBOOK .'/classes/account.class.php');
		$account = new account($this->db, $this->session);
		
		if (isset($_GET['debug'])) $account->debug = $_GET['debug'];
		
		
//		if ($this->debug) echo '$_POST:<pre>'.print_r($_POST,true).'</pre>';	
		
			
		if (isset($_GET['step']) && $_GET['step'] == 'submit')
		{
			
			$fdata = $account->validateFormData();
			//if ($this->debug) echo '<pre>'.print_r($fdata,true).'</pre>';	
		
			
			// TODO: structure for all data, and a validation/error indicator structure
			// use two assoc arrays, walk through them when building/rebuilding form to note errors
			
			//...
			
			if ($fdata->result)
			{
				$fdata = $account->processFormUpdateDatabase($fdata);
			} 
			
			if (!$fdata->result) // can be either validation failure or processing failure
			{
				$inside .= $account->buildSignupForm($fdata, $fdata->alert);
				
			} else
			{
				// success
				$this->session->u->load($this->session->u->userid); // refresh session data
				$msg .= "postSignup: User {$this->session->u->name}:{$this->session->u->userid} signup successful..."; 
				$msg .= $this->postSignup($fdata->email);
				//$this->db->log($msg);
				$this->facebook->redirect(URL_CANVAS.'?p=team&newsignup=1');
				//
				
			
			}
			
			
		} else
		{

			// is this a redirect from a chat request
			if (isset($_GET['next'])) {
				$arArg=parse_url(urldecode($_GET['next']));			
				$targetLink=$arArg['query'];
				if (stristr($targetLink,'&chat')!==false) {
					$inside.=$this->page->buildMessage('error','Welcome to '.SITE_TITLE,'We\'d like you sign up but it is not required for reading and chatting. If you prefer just to read the story, <a href="'.URL_CANVAS.'?'.$targetLink.'">click here</a>. ');
				}
			}
		
			if (isset($_GET['test']) && $_GET['test']=='blank') 
				$fdata = $account->initFormDataBlank();
			else
				$fdata = $account->initFormData();
			$inside.=$this->templateObj->templates['heading'];				
			$inside.=$account->buildSignupForm($fdata);
	
		}
		$inside.='<!-- end left side --></div><div id="col_right">';
			// whyPost side panel
			$inside.=$this->templateObj->templates['intro'];
			$inside.=$this->templateObj->templates['whyJoin'];			
		
		$inside.='</div> <!-- end right side -->';
		
		if ($this->page->isAjax) return $inside;
		
		if (file_exists(PATH_SITE_IMAGES.'bg_banner.gif')) {
			$code='<img src="'.URL_CALLBACK.'?p=cache&simg=bg_banner.gif" alt="'.SITE_TITLE.' header"/><br /><br />';
		} 
		$code.=$this->page->constructPage('signup',$inside,'',false);				
		return $code;
	}
	
	function postSignup($email='')
	{
		//log->serialize($id=0,$userid1=0,$action='',$itemid=0,$userid2=0) {
		$log = $this->app->getActivityLog();
		$log->add($log->serialize(0, $this->session->userid, 'signup', 0, 0));

		// turned off now that we're doing only local sync
		// to do - do email verification locally
		// $this->sendEmailVerification($email);
		
		// to do - when refuid is zero, we should look up last invitation
		
		require_once (PATH_CORE .'/classes/user.class.php');		
		$uit = new UserInviteTable($this->db);
		if ($uit->userAcceptedInvitation($this->session->userid))
		{
			$code .= 'Invitation from user './*$_GET['refuid']*/$this->session->ui->refuid 		
			.' accepted successfully.';
			$log->add($log->serialize(0, $this->session->userid, 'acceptedInvite', 0, $this->session->ui->refuid));
			$log->add($log->serialize(0, $this->session->ui->refuid, 'friendSignup', 0,  $this->session->userid));			
		} elseif ($this->session->ui->refuid) // credit referer anyway if they didnt invite us
		{			
			$code .= 'No invitation from user '.$this->session->ui->refuid 		
			.' found, but crediting for referral anyway.';	
			$log->add($log->serialize(0, $this->session->ui->refuid, 'friendSignup', 0,  $this->session->userid));			
			
		} elseif ($last_inviting_userid = $uit->forceAcceptLastInvite($this->session->ui->fbId)) // no refuid, so try to accept the last invitation sent to this user by anyone 
		{
			$code .= 'No refuid, accepted last invite from '.$last_inviting_userid. '.';	
	
			$log->add($log->serialize(0, $this->session->userid, 'acceptedInvite', 0, $last_inviting_userid));
			$log->add($log->serialize(0, $last_inviting_userid, 'friendSignup', 0,  $this->session->userid));			
			
		} else
		{
			$code .= 'No refuid and no invite found, no invite credit assigned!';	
			
		}
		return $code;	
	}
	
	function sendEmailVerification($email='') {
		// to do - code duplicated in ajax, move to account class, verify global init
		global $init;
		// ask NewsCloud to send an email verification request
		require_once (PATH_CORE.'/classes/systemStatus.class.php');
		$ssObj=new systemStatus($this->db);
		$partnerid=$ssObj->getState('partnerid');				
		$this->db->log('partnerid '.$partnerid);
		if ($partnerid==0) {
			$this->db->log('ERROR: The site administrator hasn\'t properly configured this site with NewsCloud - missing partner registration.');				
		} else {			
			require_once (PATH_CORE.'/classes/apiCloud.class.php');
			$apiObj=new apiCloud($this->db,$init[apiKey]);
			//$this->db->log($email);			
			$resp=$apiObj->sendVerifyEmailRequest(SITE_CLOUDID,$email,$partnerid); 
			//$this->db->log(print_r($resp,true));		
		}		
	}
	
}
?>