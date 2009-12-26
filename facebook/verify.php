<?php
	// verify email address 
	setupAppFramework();
	if (isset($_GET['test'])) {
		require_once(PATH_CORE.'/classes/remotefile.class.php');
		$rfObj = new remotePageProperty($_GET['url']);
		echo $rfObj->getPageTitle();
		$matches=$rfObj->getPageParagraphs();
 		print_r($matches);
		require_once(PATH_CORE.'/utilities/calais/opencalais.php');
		$oc = new OpenCalais($init['calais']);		
		$entities = $oc->getEntities($matches);
		foreach ($entities as $idea) {
			echo $idea . "<br />";
		}
		exit;
		foreach ($entities as $type => $values) {

			echo "<b>" . $type . "</b>";
			echo "<ul>";

			foreach ($values as $entity) {
				echo "<li>" . $entity . "</li>";
			}

			echo "</ul>";

		}
		exit;
	}
	if (!isset($_GET['e']) and !isset($_GET['a'])) {
		$result=false;
		$app->facebook->redirect(URL_CANVAS.'?p=home&msgType=error&&msg='.urlencode('There was a problem with your request.'));					
	} else {
		$email=rawurldecode($_GET['e']);
		$actCode=rawurldecode($_GET['a']);
		require_once (PATH_CORE.'/classes/apiCloud.class.php');
		$apiObj=new apiCloud($db,$init[apiKey]);
		$resp=$apiObj->verifyRemoteEmailRequest(SITE_CLOUDID,$email,$actCode);
		if (!$resp['result']) {
			$result='error';
			$title='There was a problem';
			$msg='There was a problem verifying your email address. Please <a href="?p=contact">contact us</a> if you continue to have problems.';			
		} else {
			// log in at NewsCloud successful
			$result='success';
			$title='Success!';
			$msg='Your email address has been verified successfully.';
			//$db->log("user"."isEmailVerified=1"."email='$email'");
			$db->update("User","isEmailVerified=1","email='$email'");
		/*
			// send eqex free shipping promo
			// look up fbId from email
			$q=$db->query("SELECT fbId FROM User,UserInfo WHERE User.userid=UserInfo.userid AND User.email='$email';");
			$data=$db->readQ($q);
			require_once(PATH_CORE.'/classes/template.class.php');
			$templateObj=new template($db);
			$templateObj->registerTemplates(MODULE_ACTIVE,'promos');
			$apiResult=$app->facebook->api_client->notifications_send($data->fbId,$templateObj->templates['eqexPromo'] , 'app_to_user');
			*/ 							
		}
		$app->facebook->redirect(URL_CANVAS.'?p=home&msgType='.$result.'&msgTitle='.urlencode($title).'&msg='.urlencode($msg));			
	}

	function setupAppFramework() {
	 	/* initialize the SMT Facebook appliation class, NO Facebook library */
		require_once PATH_FACEBOOK."/classes/app.class.php";
		global $app,$db,$session;
		$app=new app(NULL,true);
		$app->loadFacebookLibrary(); 
		// caution: do not assign globals by reference
		$db=$app->db;
		$session=$app->session;											
		return $app;		
	}	
?>
