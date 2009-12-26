<?php

class auth {
	var $db;
	
	function auth(&$db=NULL)
	{
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}

	function syncUser($userid=0) {
		// update data from NewsCloud server about the user
			
	}
	
	function register()
	{	
		require_once($_SERVER['DOCUMENT_ROOT'].'/classes/user.class.php');		
		require_once($_SERVER['DOCUMENT_ROOT'].'/classes/userProfile.class.php');		
		$userObj = new user();
		$upObj=new userProfile();
	
		$failure=false;
		$failureMsg='';
		$result=array();
		if (isset($_POST['email'])) {
			$email = $_POST['email'];
			if (stristr($email,'@newscloud.com')!==false) {
				$failureMsg=' Please use a valid email address of yours - not ours.';
				$failure=true;
			}
			// check that email isn't already registered
			$checkExist=$userObj->getId($email);
			if ($checkExist!==false) {
				$failureMsg='Sorry, this email address is already registered.';
				$failure=true;				
			}
		} else 
			$failure=true;
		if (!isset($_POST['memberName']) || !isset($_POST['pass1']) || !isset($_POST['pass2']))
			$failure=true;
		if ($failure)
		{
			$msg='There was an error with your registration. Please try again.'.$failureMsg;			
			$result['reg']=false;
			$result['msg']=$msg;				
		} else {
			$result['reg']=true;
			$memberName = $_POST['memberName'];
			$pass1 = $_POST['pass1'];
			$pass2 = $_POST['pass2'];
			$city='';
			$result=$upObj->newRegistration($email,$memberName,$pass1,$pass2,$city);
			if ($result['reg']) {
				// check if it was an invitation response
				$result['msg']=$this->buildConfirmationText($email,'');
			}
		}
		return $result;
	}

	function buildConfirmationText($email='',$extraLinks='') {
/*
		<li><strong>Please verify your email address</strong>. You will soon receive an email with a link to activate your account.<br/>If you do not receive this email, <i>please check your spam folder for a message from jeff@newscloud.com.</i><br /><br /></li>
		<li>Need help? <a href="http://www.newscloud.com/register/resendConfirm?email='.$email.'" target="_blank">Request another confirmation email</a>, visit our <a href="http://www.newscloud.com/learn/help" target="_blank">Help page</a> or <a href="mailto:jeff@newscloud.com.">email us</a>.<br /><br /></li>
		*/
		$text.='<h1>Thank you for signing up at NewsCloud</h1><ol>
<li>Jump to the <a href="http://www.newscloud.com" target="_blank">NewsCloud Front Page</a></li></ol>';
		return $text;
	}
	
	function isMemberNameAvailable($str='') {
		require_once ($_SERVER['DOCUMENT_ROOT'].'/classes/user.class.php');
		$userObj=new user();
		if ($str<>'') {
			if ($userObj->isMemberNameAvailable($str)) 
				$text='This name is available!';
			else
				$text='This name is taken. Please choose another.';
		} else
			$text='Please type in a member name.';
		return $text;
	}
	
	function buildLoginForm($nextUrl='',$email='',$memberName='') {
		if (USE_RECAPTCHA) {
			require_once(PATH_CORE.'/utilities/recaptchalib.php');
			$publickey = KEY_PUB_RECAPTCHA;
			$privatekey = KEY_PRI_RECAPTCHA;
			$recapResp = null;
			$recapError = null;
			$recapCode='<p>'.recaptcha_get_html($publickey, $recapError).'<br /></p>';
		} else {
			$recapCode='';
		}					
	
		if ($nextUrl=='') $nextUrl=URL_HOME;
		$referPage='<input type="hidden" name="referPage" value="'.$nextUrl.'"/>';
		$code='<div class="leftSide">';
		$code.='<form action="?p=signin&mode=processRegistration" method="post" id="register">';
		$code.='<h3>Sign Up here</h3><p>To create an account, please fill out the information below.</p>';			
		$code.='<strong>Your email address:</strong><br /><input name="email" type="text" value="'.$email.'" /><br />';
		$code.='<strong>Choose a member name:</strong><br /><input id="memberName" name="memberName" type="text" value="'.$memberName.'" /> <a style="font-size:10px;" href="javascript:void(0);" onclick="checkAvailable();">check availability</a><div id="isAvailableResult"></div><div id="isavailable" style="font-size:10px;color:red;"></div><p>Please provide a member name that will be displayed with your submissions.</p>';
		$code.='<strong>Password:</strong><br /><input name="pass1" type="password"  value="" /><p>Please provide a password for security.</p>';
		$code.='<strong>Confirm Password:</strong><br /><input name="pass2" type="password" value="" />';
		$code.=$recapCode;
		$code.='<input style="font-size:10px;" name="signup" type="submit" value="Create Account" /></form>';
		$code.=$referPage;
		$code.='<br /><br /></div>';
		$code.='<div class="rightSide">';
		$code.='<h3>Existing users,<br />sign in here</h3><p>To sign in, please provide the information below:</p>';
		$code.='<form action="?p=signin&mode=validateLogin" method="post">';
		$code.='<strong>Your email address:</strong><br /><input id="email" name="email" type="text" value="" /><br />';
		$code.='<strong>Password:</strong><br /><input id="password" name="password" type="password" value="" /><p><a href="http://www.newscloud.com/register/forgotForm" target="_blank">Forgot your password?</a></p><input style="font-size:10px;" type="submit" value="Sign in to NewsCloud" />';
		$code.=$referPage;
		$code.='</form></div><br /><br />';
		return $code;
	}
		
}
?>