<?php
class pageContact {

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

	function process($data) {
		require_once(PATH_CORE.'/classes/contactEmails.class.php');
		$cet = new ContactEmailTable($this->db);
		require_once(PATH_CORE.'/classes/user.class.php');
		$userInfoTable=new UserInfoTable($this->db);
		$userInfo = $userInfoTable->getRowObject();
		
		$contactObj = $cet->getRowObject();

		$contactObj->email = $data['email'];
		$contactObj->subject = $data['subject'];
		$contactObj->message = $data['message'];
		$contactObj->userid = $data['userid'];
		$contactObj->date = date("Y-m-d H:i:s", time());
		$contactObj->topic = $data['topic'];

		$contactObj->insert();

		// Submit email to lighthouse app
		$userInfo->load($contactObj->userid);
		
		$lhemail = 'ticket+newscloud.24722-xkthe5fe@lighthouseapp.com';
		$message = 'From: '.$contactObj->email."\n\n";
		$message .= 'User ID: '.$contactObj->userid."\n\n";
		$message .= 'Facebook profile: <a target="_blank" href="http://www.facebook.com/profile.php?id='.$userInfo->fbId.'">http://www.facebook.com/profile.php?id='.$userInfo->fbId.'</a>'."\n\n";
		$message .= 'Subject: '.$contactObj->subject."\n\n";
		$message .= 'Topic: '.$contactObj->topic."\n\n";
		$message .= "\n\n\n\nMessage:\n\n".$contactObj->message;
		mail($lhemail, $contactObj->subject, $message, 'From: support@newscloud.com'."\r\n");

		$msg = 'Successfully submitted your comment. We will review this as soon as possible.';
    $this->page->app->facebook->redirect(URL_CANVAS.'?p=home&msgType=success&msgTitle='.urlencode('Success!').'&msg='.urlencode($msg));
		
		//return '<p>Successfully submitted your comment. We will review this as soon as possible.</p>';
	}
	
	function fetch() {
		if ($this->page->isAjax)
			$this->facebook=&$this->page->app->loadFacebookLibrary();
		$inside.=$this->buildContactForm();
		if ($this->page->isAjax) return $inside;
		$code=$this->page->constructPage('contact',$inside);		
		return $code;
	}

	function buildContactForm() {
		$code='<h1>Contact Us</h1><h5>We appreciate your questions, feedback and bug reports!</h5>';		
		$code.='<fb:editor action="?p=contact&o=submit" labelwidth="100" method="post">
	  <fb:editor-custom label="From">'.$this->session->u->name.'</fb:editor-custom>
	   <fb:editor-text label="Your email address" name="email" value="'.$this->session->u->email.'"/>';
		$code .= '<fb:editor-custom><input type="hidden" name="userid" value="'.$this->session->userid.'" /></fb:editor-custom>';
		$code.=' <fb:editor-custom label="Topic"><select name="topic">';
		$code.='<option value="general">General</option>';
		$code.='<option value="editorial">Letter to the Editor</option>';
		$code.='<option value="team">'.SITE_TEAM_TITLE.'</option>';
		if (ENABLE_ACTION_CHALLENGES)		
			$code.='<option value="team">Suggest an '.SITE_TEAM_TITLE.' challenge</option>';
		$code.='<option value="feedback">Feedback</option>';
		$code.='<option value="bug">Bug Report</option>';		
	    $code.= '</select> </fb:editor-custom> '; 
	    $code.='<fb:editor-text label="Subject" name="subject" value=""/>';
	   $code.='<fb:editor-textarea label="Message" name="message"/>';
	    	$code.='<fb:editor-buttonset>  
	           <fb:editor-button value="Submit"/> <fb:editor-cancel href="'.URL_CANVAS.'"/>  </fb:editor-buttonset>';
		$code.='</fb:editor>';	
		return $code;		
	}
}

?>