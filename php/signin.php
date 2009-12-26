<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_AJAX",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	require_once PATH_PHP.'/classes/auth.class.php';
	$authObj=new auth($db);
	
	/* process request variables */
	if (isset($_GET['mode']))
		$mode=$_GET['mode'];
	else
		$mode='default';
	if (isset($_GET['error']))
		$error=$_GET['error'];
	else
		$error='';

	/* begin building the page */
	$page->setTitle('Sign in');
	$page->pkgStyles(CACHE_PREFIX.'nrSignIn',array(PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css',PATH_PHP_STYLES.'/signin.css'));
	$page->pkgScripts(CACHE_PREFIX.'nrSignIn',array(PATH_PHP_SCRIPTS.'/common.js',PATH_PHP_SCRIPTS.'/auth.js'));					
	$page->addToHeader($common->buildHeader().$common->buildNavigation('Sign in'));
	$page->addToFooter($common->buildFooter());	
	$page->addRSSFeed(URL_HOME.'?p=rss');			
	$code='';
	$code.='<div id="pageBody">';
	$code.='<div id="pageContent">';	
	if ($error<>'') {
		$code.='<div class="errorMessage">'.$error.'</div>';
	}
	switch ($mode) {
		default:
			if (isset($_GET['referPage']))
				$referPage=$_GET['referPage'];
			else
				$referPage='';
			$code.=$authObj->buildLoginForm($referPage,'','');
		break;
		case 'validateLogin':
			$error=false;
			if ( isset($_POST['email']) and isset($_POST['password']) ) {
				$email =    $_POST['email'];
				$password = $_POST['password'];
				require_once (PATH_CORE.'/classes/apiCloud.class.php');
				$apiObj=new apiCloud($db,$init[apiKey]);
				$checkLogin=$apiObj->validateLogin(SITE_CLOUDID,$email,$password);
				$result=$checkLogin['result'];
				if ($result) {
					// log in at NewsCloud successful
					processValidLogin($db,$checkLogin);
				} else {
					$error=true;
					$errorMsg=$checkLogin['message'];
				}
			} else {
				$errorMsg='';
				$error=true;
			}
			if (isset($_POST['referPage']))
				$nextUrl=$_POST['referPage'];
			else
				$nextUrl=URL_HOME;
			if ($error===false) {
				header("Location: ".$nextUrl);
				exit;	
			} else {
				header("Location: ?p=signin&error=".urlencode($errorMsg));
				exit;	
			}				
		break;
		case 'signOut':
			require_once PATH_CORE.'/classes/user.class.php';
			$userObj=new user($db);							
			// Unset all of the session variables.
			$userObj->signOut();
			// redirect to home page
			header("Location: ".URL_HOME);
			exit;				
		break;
		case 'processRegistration':
			require_once PATH_CORE.'/classes/user.class.php';
			$userObj=new user($db);							
			$failure=false;
			$failureMsg='';
			$result=array();
			if (isset($_POST['email'])) {
				$email = $_POST['email'];
				$match=split('@',$email);		
				$domain=strtolower($match[1]);
				if (stristr($email,'@newscloud.com')!==false) {
					$failureMsg=' Please use a valid email address of yours - not ours.';
					$failure=true;
				}
				// check that email isn't already registered
				$checkExist=$userObj->checkEmailExist($email);
				if ($checkExist!==false) {
					$failureMsg='Sorry, this email address is already registered.';
					$failure=true;				
				}
			} else  {
				$failure=true;
				$failureMsg='Please enter a valid email address.';
			}
			if (!isset($_POST['memberName']) || !isset($_POST['pass1']) || !isset($_POST['pass2'])) {
				$failure=true;
				$failureMsg='Please enter a valid email address and matching passwords.';
			} else {
				$pass1 = $_POST['pass1'];
				$pass2 = $_POST['pass2'];
				if ($pass1<>$pass2) {
					$failure=true;
					$failureMsg='Your passwords do not match.';
				}			
			}
			// set up refer page
			if (isset($_POST['referPage'])) {
				$referPage=$_POST['referPage'];
				$extraLinks.='<li><a href="'.$referPage.'">Return to the previous page</a></li>';
			}
			// look up site partnerid
			require_once (PATH_CORE.'/classes/systemStatus.class.php');
			$ssObj=new systemStatus($db);
			$partnerid=$ssObj->getState('partnerid');
			if ($partnerid==0) {
					$failure=true;
					$failureMsg='The site administrator hasn\'t properly configured this site with NewsCloud - missing partner registration.';				
			}
			if (USE_RECAPTCHA) {
				// check captcha
				require_once(PATH_CORE.'/utilities/recaptchalib.php');
				$publickey = KEY_PUB_RECAPTCHA;
				$privatekey = KEY_PRI_RECAPTCHA;
				$db->log('Pub:'.$publickey);
				$db->log('Pri:'.$privatekey);
				$db->log($_SERVER["REMOTE_ADDR"]);				
			  $resp = recaptcha_check_answer ($privatekey,
			                                  $_SERVER["REMOTE_ADDR"],
			                                  $_POST["recaptcha_challenge_field"],
			                                  $_POST["recaptcha_response_field"]);
			  if (!$resp->is_valid) {
			  	$failure=true;
			    $failureMsg='Captcha error: '.$resp->error;
			  }
			}	
			if ($failure)
			{
				$msg='There was an error with your registration. Please try again.'.$failureMsg;			
				$msg= '<div class="msgBox"><h1>Registration Problem</h1><p>'.$msg.'</p></div>';
				$result['reg']=false;
				$result['msg']=$msg;				
			} else {
				$result['reg']=true;
				$memberName = strtolower($_POST['memberName']);
				require_once (PATH_CORE.'/classes/apiCloud.class.php');
				$apiObj=new apiCloud($db,$init[apiKey]);
				$db->log($email.' '.$memberName.' '.$pass1.' '.$partnerid);
				$resp=$apiObj->userCreateAccount(SITE_CLOUDID,$email,$memberName,$pass1,$partnerid,0,$memberName,'false',false); // last argument is false for real mode
				if ($resp['result']) {
					$result['msg']=buildConfirmationText($email,$extraLinks);
				} else {
					$msg='There was an error with your registration. Please try again.';			
					$msg= '<div class="msgBox"><h1>Registration Problem</h1><p>'.$msg.'</p></div>';
					$result['reg']=false;
					$result['msg']=$msg;									
				}
			}	
			$code.=$result['msg'];
			if ($result['reg']===false) {
				$code.=$authObj->buildLoginForm($referPage);				
			}
		break;
		case 'VEA':
			$result=true;
			if (!isset($_GET['e']) and !isset($_GET['a'])) {
				$result=false;
			} else {
				$email=rawurldecode($_GET['e']);
				$actCode=rawurldecode($_GET['a']);
				require_once (PATH_CORE.'/classes/apiCloud.class.php');
				$apiObj=new apiCloud($db,$init[apiKey]);
				$resp=$apiObj->verifyActivation(SITE_CLOUDID,$email,$actCode);
				if (!$resp['result']) {
					$result=false;					
				} else {
					// log in at NewsCloud successful
					processValidLogin($db,$resp);
				}
			}			
			if ($result) {				
				$code.='<h1>Success!</h1><p>Thanks for verifying your email address.</p><p>Go to the <a href="'.URL_HOME.'">Front Page</a> and start reading and posting comments!<br clear="all" /></p>';
			} else {
				$code.='<h1>Problem!</h1><p>Your email address did not verify properly.</p>';
				$code.=$authObj->buildLoginForm('');				
			}
		break;
	}	
	$code.='</div><!-- end pageContent -->';
	$code.='</div><!-- end pageBody -->';
	$page->addToContent($code);
	$page->display();	

	function buildConfirmationText($email='',$extraLinks='') {
/*		<li><b>Please verify your email address</b>. You will soon receive an email with a link to activate your account.<br/>If you do not receive this email, <i>please check your spam folder.</i></li>
		<li>Need help? <a href="'.URL_SMT_SERVER.'/register/resendConfirm?email='.$email.'">Request another confirmation email</a>, visit our <a href="'.URL_SMT_SERVER.'/learn/help">Help page</a>.</li>
		*/

		$text='<h1>Thank you for signing up</h1><ol class="list">
<li>Return to the <a href="'.URL_HOME.'">Front Page</a></li>';
		$text.=$extraLinks;
		$text.='</ol>';	
		return $text;
	}
	
	function processValidLogin(&$db,&$resp) {
		require_once PATH_CORE.'/classes/user.class.php';
		$userObj=new user($db);				
		$userInfo=unserialize($resp[items][0][userinfo]);
		// look up local user id					
		$localInfo=$userObj->lookupUserId($userInfo->uid);
		if ($localInfo===false) {
			// if they don't exist, then register them locally												
			$newUserData=$userObj->serialize(0,$userInfo->uid,$userInfo->memberName,$email,0,$userInfo->votePower,$userInfo->remoteStatus);
			$userInfo->userid=$userObj->update($newUserData);
		} else {					
			// if they do exist, update them locally
			$userInfo->userid=$localInfo->userid;
		} 	
		// sync NewsCloud member info
		$ui=new stdClass;
		$ui->userid=$userInfo->userid;
		$ui->ncUid=$userInfo->uid;
		$ui->name=$userInfo->memberName;
		$ui->votePower=$userInfo->votePower;			
		$userObj->updateNewsCloudInfo($ui);
		$userObj->setLoginSession($ui);		
	}
?>