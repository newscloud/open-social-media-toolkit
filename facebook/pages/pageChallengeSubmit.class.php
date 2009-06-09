<?php

class pageChallengeSubmit {

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

	function fetch($mode='fullPage') {
		// build the prizes page
		if (isset($_GET['currentPage']))
			$currentPage=$_GET['currentPage'];
		else
			$currentPage=1;	
		
		if (isset($_GET['id']))
			$id=$_GET['id'];
		else
			$id=NULL;	
		if (isset($_GET['step']))
			$step=$_GET['step'];
		else
			$step=NULL;	
			
		if (isset($_GET['message']))
			$message = $_GET['message'];
		else			
			$message = '';
			
		if ($_GET['debug']) echo '<pre>' . print_r($_GET, true) . '</pre>';
			
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		$tabs.=$this->teamObj->buildSubNav('challenges');	 
		$inside='<div id="col_left"><!-- begin left side -->';
		
		require_once(PATH_FACEBOOK .'/pages/pageChallenges.class.php');
		$challengePage = new pageChallenges($this->page);
		$inside .= $challengePage->fetchChallengeDetail($id, true);
		
		if (!$id) 
		{
			$inside.='No challenge id found, please go back and try again.';	
		} else 
		{
		
			if ($message =='')
			{			
				// challenge submit form
				$inside .= self::fetchSubmissionDialogForm($this->db, $id, 
						$this->page->session->userid, $this->page->session->sessionKey, $fdata);
			} else // if process sent us back here, that means there was an error, since the success condition sends us to profile
			{
				// repopulate form with get vars
				
				$fdata->embedCode = $_GET['embedCode']; // TODO: make names more accurate				
				$fdata->photo1 = $_GET['photo1'];
				$fdata->text = $_GET['text'];
				
				$inside .= $this->page->buildMessage('error', "There was a problem with your submission", $message);
				$inside .= self::fetchSubmissionDialogForm($this->db, $id, 
					$this->page->session->userid, $this->page->session->sessionKey, $fdata);
		
			}
		}		
		
		$inside.='</div><!-- end left side --><div id="col_right">';
	
		$inside.=$this->teamObj->fetchSidePanel('challenges');
		
		$inside.='</div> <!-- end right side -->';
		
		//$inside.='<input type="hidden" id="pagingFunction" value="fetchChallenges">';		
	
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside,'');		
		return $code;
	}
	
	
	static function setHiddenSession($userid, $sessionKey) {
		$code.='<input type="hidden" name="userid" value="'.$userid.'" />';
		$code.='<input type="hidden" name="sessionKey" value="'.$sessionKey.'" />';
		return $code;
	}
	
	
	static function fetchSubmissionDialogForm($db, $id, $userid, $sessionKey, $fdata) // so this could be an ajax request
	{
		
		require_once(PATH_CORE.'/classes/challenges.class.php');		
		$challengeTable = new ChallengeTable($db);
		$challenge = $challengeTable->getRowObject();
	
		if ($id && $challenge->load($id))
		{
			
			$code .= '<span style="display:none;"><fb:editor action=""></fb:editor></span>'; // trick facebook into embedding correct styles

			//	$code .= '<form action="upload_file.php" method="post" enctype="multipart/form-data">
			//				<table class="editorkit">';
				  
			/*
			 * 
			 * <fb:editor action="?p=account&settings&step=submit" labelwidth="100">
			 * <fb:editor_custom label="Name" name="name">Laser Daniel MacDonald</fb:editor_custom>
			 * <input type="hidden" name="name" value="Laser Daniel MacDonald"/>
			 * <fb:editor_text label="Email address" name="email" value="test@me.comwh"/>
			 * <fb:editor_custom label="" name="email_note">We will use this email address to set up your account with NewsCloud.com and to include you in our study (described below).</fb:editor_custom> <fb:editor_custom label="Age">
			 * <select id="accountAge" name="age" onLoad="accountAgeChanged();" onChange="accountAgeChanged();">
			 * <option value="10" >10</option><option value="11" >11</option><option value="12" >12</option><option value="13" >13</o
			 */
	 
						
			$photoSubmitCallbackURL = URL_CALLBACK .'?p=postAlt&o=challenge';
				// submission form!
			$code .= '<form name="dialog_form" id="dialog_form" 
							action="'.$photoSubmitCallbackURL.'" method="post" enctype="multipart/form-data">';
			$code .= '<table class="editorkit">';
			$code .= '<tr class="width_setter"><th style="width: 100px;"/><td/></tr>'; // label width setting					
		 	$code .= '<input name="challengeid" type="hidden" value="'. $challenge->id . '"/>';

		 	
			$code .= self::setHiddenSession($userid, $sessionKey); // so these can be passed through to the POST handler
		 
		 	$code .= '<input name="debugSubmit" type="hidden" value="0"/>'; // set to 1 for submit debug output
		 	
			// if requires text submission
		 	if (preg_match("/video/i", $challenge->requires))
			{
				$code .='<fb:editor-text label="Enter your YouTube video URL" name="embedCode" value="'.
							($fdata->embedCode <> '' ? $fdata->embedCode : '').							
						'" />';
			
			}		 	
		 	
			
			if (preg_match("/photo/i", $challenge->requires))
			{
					if (preg_match("/optionalphoto/i", $challenge->requires)) $optional='(optional)';
		
					$code .= 
				 	'<fb:editor-custom label="Select a photo (png, jpg, or gif only, 2MB limit) to upload '.$optional.'" name="photo1" >'.
		            '<input name="photo1" type="file" id="photo1" value="'.($fdata->photo1 <> '' ? $fdata->photo1 : '').'" />'.
					'</fb:editor-custom>';
		            				
			}
		
			
			if (preg_match("/text/i", $challenge->requires))
			{
				$code .=
					'<fb:editor-textarea label="Enter text here to support your submission" 
							name="text" cols="40" rows="12">'.
					($fdata->text <>'' ? $fdata->text : '').
					'</fb:editor-textarea>';					
			} 
						
			
			$code.='<fb:editor-buttonset>  
	           <fb:editor-button value="Submit"/> <fb:editor-cancel href="?p=challenges&id='.$id.'"/>  </fb:editor-buttonset>';
			$code.='</fb:editor>';	
	
			
			$code .= '</table>'.	
					 '</form>';
	
			
		} else
		{
			$code .= "Invalid challenge id=$id";
			
		}
		
		
			
		$code ='<div class="panel_1">'.		
				'<div class="panelBar clearfix">
					<h2>Tell us what you did</h2>
					<!-- <div class="bar_link"><a href="#">I did this too!</a></div> -->
					</div><!__end "panelBar"__>'.
					$code
				.'</div><!-- end panel_1 -->';
		
		
	
		return $code;
		
	}
	
	
	
	static function processChallengeSubmitPhoto($userid, $completedid)
	{
		
		if (is_uploaded_file($_FILES['photo1']['tmp_name']))
		{			
			$uploaddir = PATH_UPLOAD_SUBMISSIONS;
		    $filename="userid_{$userid}_completedid_{$completedid}_" . basename($_FILES['photo1']['name']);
		    $uploadfile = $uploaddir . $filename;
		    move_uploaded_file($_FILES['photo1']['tmp_name'], $uploadfile);
		    copy($uploadfile, $uploaddir. 'thumbnail.'. $filename); // TODO: implement resizing for bandwidth savings		    
		    return $filename;
		} else

		return false;    
	}

		
	static function processChallengeSubmitVideo($userid, $completedid)
	{
		
		if (isset($_POST['embedCode']))
		{
			// do some validation
			
			$url = $_POST['embedCode'];
			preg_match('@^(?:http://)?([^/]+)@i',
		    "$url", $matches);

			$host = $matches[1];
					
			// get last two segments of host name
			preg_match('/[^.]+\.[^.]+$/', $host, $matches);		
			
			if($matches[0] == "youtube.com")
				return $url;
				
		// TODO: allow facebook videos by pointing this at the new function videos::validateVideoURL()
				
				
			//return $_POST['embedCode'];
			
		}
		return false;    
	}
	
	
	static function uploadPhotoToFacebook()
	{
		/* configure facebook client library */
		include_once PATH_FACEBOOK.'/lib/facebook.php';
		$facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);
		
		 $this->api_client = new FacebookPhotosRestClient($api_key, $secret);
		
		
	}
	
	
	static function processChallengeSubmit(&$code,&$passback) // called from postAlt handler. everything we need must be POSTed from DialogForm
	{
		//$debug = $_POST['debugSubmit']; // NEVER TURN ON FOR LIVE SITE
		echo '<h2>Processing, please wait...</h2>';
		if ($debug) echo "POST<pre>" . print_r($_POST, true) . "</pre>";	
			
		// TODO: grab session keys from post, validate session

		$passback .= "&text={$_POST['text']}";
		$passback .= "&embedCode={$_POST['embedCode']}";
		if ($debug) echo $passback;
		
		if (isset($_POST['challengeid']) && $_POST['challengeid'])
			$challengeid=$_POST['challengeid'];
		else
		{ $code =  "There was no challenge id present in your submission"; return false; }
				
		if (isset($_POST['text']))
			$evidence=$_POST['text'];
		else
		{	$evidence =''; /*$code = "Your text submission was empty."; return false;*/ }
			

		if (isset($_POST['userid']) && $_POST['userid'])
			$userid=$_POST['userid'];
		else
		{	$code= 'Either you aren\'t a registered user or your session is expired. Please return to the home page or sign in to facebook again.'; return false; }
			
		
		require_once (PATH_CORE.'/classes/db.class.php');
		$db=new cloudDatabase();
	
		
		// create a CompletedChallenges object
		//$userid = $this->page->session->userid;
		
	/*	if (!$userid)
		{
			echo "<pre>" . print_r($this->page->session, true) . "</pre>";	
			
			return "Could not get userid from session."; 
		}*/
		require_once(PATH_CORE.'/classes/user.class.php');
		require_once(PATH_CORE.'/classes/challenges.class.php');
		$challengeTable	= new ChallengeTable($db);
		$userTable 		= new UserTable($db);
		$userInfoTable 	= new UserInfoTable($db);
		$completedTable	= new ChallengeCompletedTable($db);
		
		$user 		= $userTable->getRowObject();
		$userInfo 	= $userInfoTable->getRowObject();
		$challenge 	= $challengeTable->getRowObject();
		$completed 	= $completedTable->getRowObject();
		
		dbRowObject::$debug =$debug;
		
		$user->load($userid);
		$userInfo->load($userid);
		$challenge->load($challengeid);
		
		// validate challenge submission info
		
		// validate eligibility, date, membership
		
			
			
		if ($challenge->remainingCompletions <= 0 && $challenge->initialCompletions>0)
		{	$code = 'This challenge can no longer be completed for credit.'; return false; }
						
		if (!ChallengeTable::userIsEligible($challenge->eligibility, $user->eligibility))
		{	$code = 'We\'re sorry, you are not eligible to receive credit for this challenge.'; return false; }

		if (preg_match("/text/i", $challenge->requires) && !($evidence <> ''))
		{ 	$code = 'Sorry, you need to convince us you actually did this!'; return false; }
			
		//if () //  TODO: now is between date start and end
		$now = time();
		$dateStart 	= strtotime($challenge->dateStart);
		$dateEnd 	= strtotime($challenge->dateEnd);
		
		if ($now > $dateEnd)
		{ 	$code = 'Sorry, you are too late to receive credit for this challenge!'; return false; }

		if ($now < $dateStart)
		{	$code = 'Sorry, you can\'t receive credit for this challenge yet -- try again later!'; return false; }
			
		// if () TODO: check user maximum by querying order histor						
		// more...
		
			
		// everythings ok:
		
		$challenge->remainingCompletions--;
		
		$completed->userid = $user->userid;
		$completed->challengeid = $challenge->id;
		$phpnow = time();
		$completed->dateSubmitted = date('Y-m-d H:i:s', $phpnow);
		$completed->status = 'submitted';
		$completed->evidence = $evidence;
		$completed->comments = $evidence; // editors will review these later

		/*
		 * The following code is a bit tricky. There are two things going on. The first is that photos
		 * or videos are being checked for and their records are being created, but we have 
		 * tentatively created a CompleteChallenge record first so they can back-reference it
		 * 
		 * If a required photo or video turns out not to have appeared, we have to then return an error 
		 * and delete the CC record.
		 * 
		 * An extra wrinkle is that if both video and photo are in the requires field, we can accept 
		 * one or the other.
		 * 
		 */
		
		
		
		// Create the completed to attach to the media records...
		if (!$completed->insert())
		{	$code =  'Internal error submitting your evidence, please try again.'; return false; }
	
		if (preg_match("/photo/i", $challenge->requires) || preg_match("/optionalphoto/i", $challenge->requires)) // check if photo required
		{
			if ($photoFilename = self::processChallengeSubmitPhoto($userid, $completed->id)) // create photo if exists
			{
				
				if (!preg_match("/\.(jpg|png|gif|jpeg?)$/i",$photoFilename))
				{	$msg= 'Sorry, your photo did not appear to be of type jpg, png, or gif.'; $error=true; }
				else 
				{				
					// create photo in our db
					require_once(PATH_CORE.'/classes/photo.class.php');
					$photoTable = new PhotoTable($db);
					$photoTable->createPhotoForCompletedChallenge($userid, $completed->id, 
								$photoFilename, 'Photo submitted for ' . $challenge->title);
			
					$photoSubmitted = true; // indicate that a photo was found
				}
			} else if (!preg_match("/optionalphoto/i", $challenge->requires))
			{	$msg= 'No photo submitted'; $error = true; }
		}		
		
		//$passback .= "&photo1={$photoFilename}"; // wont be correct filename, actually quite complicated to make this work properly
		//echo $passback;
	
		
		if (preg_match("/video/i", $challenge->requires) )	// check video requires
		{
			if ($videoEmbedCode = self::processChallengeSubmitVideo($userid, $completed->id)) // create video if exists
			{
				// create photo in our db
				require_once(PATH_CORE.'/classes/video.class.php');
				$videoTable = new VideoTable($db);
				$videoTable->createVideoForCompletedChallenge($userid, $completed->id, 
							$videoEmbedCode, 'Video submitted for ' . $challenge->title);
							
				$videoSubmitted = true;	// indicate video found
			} else				
			{   $msg = 'You must enter a YouTube video url.'; $error = true; }		
				
		}
		
		// HACK: now handle the case where both photo and video boxes appeared, and only one was entered		
		if ($photoSubmitted OR $videoSubmitted) 
			$error = false; // set the $error flag set by the other one to false if one of them was sucessfully created
		
		
		if ($error) // if we couldnt get photo or video that was needed...
		{
			$completed->delete();  // delete the temporary CC record
			$code = $msg; 
			return false;	
		}
			
		$challenge->update();	

		require_once(PATH_CORE .'/classes/template.class.php');
		$code .= 'We have received your submission for the challenge <b>'.$challenge->title . 
				/* template::buildChallengeLink($challenge->title, $challenge->id).*/ // this doesnt work with urlencode for some reason :(
				 
				'</b>  (reference number #'. $completed->id . ')';		
		
		dbRowObject::$debug =0; // NEVER TURN ON FOR LIVE SITE
		
		
		// for testing purposes -- approve free points right away
		
		if ($challenge->shortName=='testPoints10k')
		{
			$code2= '';
			if (!$completedTable->approveChallenge($completed->id, $challenge->pointValue, &$code2, false ))
			{
				$code=$code2;
				return false;
			} else
			{
				 $code='Free points awarded!';
			}
		}
				
		return true;					
	}
		
}

?>