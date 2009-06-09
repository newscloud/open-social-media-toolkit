<?php
class account
{
	var $session;
	var $db;
	
	var $debug = 0;
	function __construct(&$db, &$session)
	{
		$this->session=&$session;
		$this->db = &$db;
		$this->facebook = &$session->facebook;
	
		$this->initAccountTemplate();
		
	}	
	
	
	function initAccountTemplate()
	{
		require_once(PATH_TEMPLATES. '/account.php');		
		$this->accountTemplate = new accountTemplate();
		
	}
	
	function initFormDataBlank() // just for testing, init a mostly blank form structure
	{
		$fdata = new stdClass;
		$fdata->name = $this->session->name;
		$fdata->showResearchImportance=true;
		// stuff we get from facebook that cant be invalid
		$fdata->proxied_email = $this->session->proxied_email;
		
		return $fdata;	
		
	}
	
	function initFormData() // init form structure filling in what we can from session
	{
		$fdata = new stdClass;
		$fdata->name = $this->session->name;
		$fdata->email = $this->session->email;
		$fdata->researchImportance=$this->session->ui->researchImportance;
		if ($fdata->researchImportance==0)
			$fdata->showResearchImportance=true;
		else
			$fdata->showResearchImportance=false;
		$fdata->noCommentNotify=0;
		$fdata->rxFeatures=0;
		$fdata->rxMode='notification';
		if ($this->session->age<>'unknown')
			$fdata->age=$this->session->age;
		else
			$fdata->age='unknown'; // force select
	
		$fdata->gender 	= $this->session->sex; 
		
		// TODO: check for error if no session, as fb client might not be initialized
	
        $fbInfo=$this->facebook->api_client->users_getInfo(array($this->session->fbId),array('current_location'));
  		if (array_key_exists("current_location",$fbInfo[0])) {
                $fdata->city=$fbInfo[0]['current_location']['city'];
                $fdata->state=$fbInfo[0]['current_location']['state'];
                $fdata->country=$fbInfo[0]['current_location']['country'];
                $fdata->zip=$fbInfo[0]['current_location']['zip'];
            }
            
        if ($this->debug) echo 'fbInfo:<pre>'.print_r($fbInfo,true).'</pre>';	
		  
		
		$fdata->optInStudy = true; // default
		$fdata->showOptIn=true;
		$fdata->acceptRules=false;
		
		// stuff we get from facebook that cant be invalid
		$fdata->proxied_email = $this->session->proxied_email;
		
		if ($this->debug) echo 'populated fdata: <pre>'.print_r($fdata,true).'</pre>';	
	
		
		return $fdata;
	}
	
	function buildAccountInfoFormFields($fdata=null, $message='', $canChangePopulated=false)
	{
		
		if (strlen($message))
		{
			$code.='<fb:editor-custom label="" name="message"><fb:error><fb:message>There was a problem</fb:message>Please correct the following items: '.$message.'</fb:error>'.
				 //'<div id="accountFormAlertText">'.$message .'</div>';
					'</fb:editor-custom>';
		}		
		
		// Name 
		$code.='<fb:editor-custom label="Name" name="name">'.$fdata->name.'</fb:editor-custom>';
		$code .='<input type="hidden" name="name" value="'.$fdata->name.'"/>';
	  
		if ($this->accountTemplate->collectEmail)
		{
		
		   	$code .= '<fb:editor-text label="Email address" name="email" value="'.$fdata->email.'"/>';
			$code.='<fb:editor-custom label="" name="email-note">Required to create your account with our media partner NewsCloud and for participation in the '.SITE_TEAM_TITLE.'.</fb:editor-custom>';

		}		
		
		$code .= $this->accountTemplate->fetchResearchFields($fdata);
	
		if ($this->accountTemplate->collectGender)
		{	 
		    if ($fdata->gender /*&& !$canChangePopulated*/) 
		    {
		    	//$code .= '<fb:editor-custom label="Gender" value="'.$fdata->gender.'"/>' ;
				$code.='<fb:editor-custom label="Gender" name="gender">'.ucfirst($fdata->gender).'</fb:editor-custom>';
				$code .='<input type="hidden" name="gender" value="'.$fdata->gender.'"/>';
		    } else
		    {
		    	$code .='<fb:editor-custom label="Gender">'. 
			  		'<select name="gender">  
			  			<option value="male" '.(($this->session->sex=='male')?'SELECTED':'').'>Male</option>
			  			<option value="female" '.(($this->session->sex=='female')?'SELECTED':'').'>Female</option>
			  			<option value="other" '.(($this->session->sex=='other')?'SELECTED':'').'>Other</option>
			  		</select>'; 	     
				$code.= '</fb:editor-custom> ';
		    }
		}

		if ($this->accountTemplate->collectLocation)
		{
			$code.=$this->getLocationFields($fdata,$canChangePopulated);
		}		
	
		/*if ($fdata->showOptIn) 
			$tempScript='onLoad="accountAgeChanged(true);" onChange="accountAgeChanged(true);"';
		else 
			$tempScript='onLoad="accountAgeChanged(false);" onChange="accountAgeChanged(false);"';
		*/
		if ($this->accountTemplate->collectAge)
		{
		
		   	$code.=' <fb:editor-custom label="Age"><select id="accountAge" name="age" '.$tempScript.'>';
		   	
		   	if ($fdata->age=='unknown') 
				$code.='<option value="0" SELECTED>Please select your age below</option>';
	
			for ($i=16;$i<99;$i++ ) 
			{
				$code.='<option value="'.$i.'" '.(($fdata->age==$i)?'SELECTED':'').'>'.$i.'</option>';
			}	       	     
		    $code.= '</select> </fb:editor-custom> ';
			
		}
			    
	    $code .= $this->accountTemplate->fetchCustomAccountFields($fdata); // i.e. for HotDish, the accountAgeSpecificOptInText div would go here
		//$code .= '<fb:editor-custom>'; // wtf is this for???
	    
	    
	/*	  
 *       <fb:editor-textarea label="Comment" name="comment"/> 
	           */ 
		// TODO: with this configuration, this stuff should go on the account settings page, even though I'd rather have check boxes here 
		// JEFF SAYS : Yes this is messy. Let's brainstorm a better way		
		// to do: create a user function checks multiple permissions, sets permissionsLastChecked timestamp
		// function can log activity when setting is changed to yes or no (for action team points)
		// that way, we can check them just once per session, but remind users to opt in to these permissions on the homepage
		// set permissionsLastChecked (so we can do this daily for their first visit) OR we can get these whenever a new session is created
		// check user app permissions
	//	$facebookSMSPermitted=$this->facebook->api_client->users_hasAppPermission('sms');
	//	if (!$facebookSMSPermitted)
	//		$code.='<fb:prompt-permission perms="sms">May we send text messages to your cell phone?</fb:prompt-permission><br>';		
		return $code;
		
	}
	
	function getLocationFields($fdata,$canChangePopulated) {
		// location
		$temp .= (!strlen($fdata->city) || $canChangePopulated) ?	
			'<fb:editor-text label="City" name="city" value="'.$fdata->city.'"/>' :
		 	('<fb:editor-custom label="City" name="city">'.$fdata->city.'</fb:editor-custom>'.
		 	 '<input type="hidden" name="city" value="'.$fdata->city.'"/>') ;
		if (!strlen($fdata->state) || $canChangePopulated)
		{
			$temp .='<fb:editor-custom label="State/Province">'. 
			  		'<select name="state"> '.
						$this->makeSelDrop('provinces', $fdata->state); 
			  			'</select>'; 	     
			$temp.= '</fb:editor-custom> ';
		} else
		{
		 	$temp .= '<fb:editor-custom label="State" name="city">'.$fdata->state.'</fb:editor-custom>'.
		 	 		'<input type="hidden" name="state" value="'.$fdata->state.'"/>' ;
		}

		$temp .='<fb:editor-custom label="Country">'; 
			if (!strlen($fdata->country) || $canChangePopulated)
			{
				$temp .= '<select name="country"> '.
						$this->makeSelDrop('countries', 'United States'); 
			  			'</select>';
			} else
			{
				$temp .= $fdata->country . '<input type="hidden" name="country" value="'.$fdata->country.'"/>' ;
			}
		$temp.= '</fb:editor-custom> ';
		return $temp;
		
	}
	// signup specific wrapping and extra fields outside of core block
	function buildSignupForm($fdata=null, $message='', $canChangePopulated=false) 
	{
		if (!$fdata)
			$fdata = initFormData();

		$fdata->showOptIn = true; // always show opt in on signup page
			
		if ($this->debug) echo '<pre>'. print_r($this->session, true).'</pre>';
			
		$code='';		
	//	$code="Welcome <fb:name uid=\"".$this->session->fbId."\"/>,"; 
		
		$code.='<fb:editor action="?p=signup&step=submit" labelwidth="100">';

		
		$code .= $this->buildAccountInfoFormFields($fdata, $message, $canChangePopulated);
		
		$code.=$this->buildOptInStudyText();
		$code.=$this->buildAcceptRulesText();
				
	    $facebookEmailPermitted=$this->facebook->api_client->users_hasAppPermission('email', $this->session->fbId);	    
	   	if (!$facebookEmailPermitted)
			$code.='<fb:editor-custom><fb:prompt-permission perms="email">Would you like to receive email from us through facebook? (50 pts)</fb:prompt-permission></fb:editor-custom>';
	    
		$code.='<fb:editor-buttonset>  
	           <fb:editor-button value="Submit"/> <fb:editor-cancel href="'.URL_CANVAS.'"/>  </fb:editor-buttonset>';
		$code.='</fb:editor>';	

	
		/* delete this after its been tried in its new home
	   	// script call to trigger update of age-text after page has loaded
	    // (hack to trigger the refresh on page load without having to write the age logic in two places)
		$code .= '<script><!-- 
			accountAgeChanged(true); 
			--> </script>'; // should work on canvas pages
		*/
		
		// perms: email, offline_access, status_update, photo_upload, create_listing, create_event, rsvp_event, sms. 
		return $code;		
	}

	// signup specific wrapping and extra fields outside of core block
	function buildAccountSettingsForm($fdata=null, $message='') 
	{
		$fdata->showResearchImportance=true;			
		if (!$fdata) {
			$fdata = initFormData();
			$fdata->showOptIn=true;
		} else {
			if ($fdata->optInStudy) 
				$fdata->showOptIn=false; // don't allow opt out
			else
				$fdata->showOptIn=true;
			if ($fdata->researchImportance>0)
				$fdata->showResearchImportance=false;							
		}
		$code='';		
		
		$code.='<fb:editor action="?p=account&settings&step=submit" labelwidth="100">';

		$code .= $this->buildAccountInfoFormFields($fdata, $message, true);
		if ($fdata->showOptIn) {
			$code.=$this->buildOptInStudyText($fdata->optInStudy );
		} else {
			$code.='<input type="hidden" name="optInStudy" value="on" />';			
		}
/*
		$code.=$this->buildPermissions();
*/
		$code.='<fb:editor-buttonset>  
	           <fb:editor-button value="Update"/> <fb:editor-cancel href="'.URL_CANVAS.'"/>  </fb:editor-buttonset>';
		$code.='</fb:editor>';	
		

		/* TODO: delete when its tested in its new home
	   	// script call to trigger update of age-text after page has loaded
	    // (hack to trigger the refresh on page load without having to write the age logic in two places)
		$code .= '<script><!-- 
			accountAgeChanged(); 
			--> </script>'; // should work on canvas pages
		*/
		
		// perms: email, offline_access, status_update, photo_upload, create_listing, create_event, rsvp_event, sms. 
		return $code;		
	}
	
	function validateLocationData() {
		// used by address form
		$fdata->result = true;
		$fdata->alert = '';

		// the stuff we get from facebook or user but might be invalid anyway 
		$fdata->address1 	= stripslashes($_POST['address1']);
		$fdata->address2 	= stripslashes($_POST['address2']);
		$fdata->city 	= stripslashes($_POST['city']);
		$fdata->state 	= stripslashes($_POST['state']);
		$fdata->country	= stripslashes($_POST['country']);
		if (isset($_POST['zip']))
			$fdata->zip	= stripslashes($_POST['zip']);
		else
			$fdata->zip='';
		if ($fdata->address1 == '')
		{
			$fdata->alert .= 'Please enter at least the first line of your mailing address.<br />';
			$fdata->result = false;						
		}
		if ($fdata->city == '')
		{
			$fdata->alert .= 'Please enter your city.<br />';
			$fdata->result = false;						
		}
		if ($fdata->state == '')
		{
			$fdata->alert .= 'Please select the state where you live.<br />';
			$fdata->result = false;						
		}
		if ($fdata->country == '')
		{
			$fdata->alert .= 'Please select the country where you live.<br />';
			$fdata->result = false;						
		}
		if ($fdata->zip == '')
		{
			$fdata->alert .= 'Please enter your postal code.<br />';
			$fdata->result = false;						
		}
		return $fdata;		
	}
	
	function validateFormData($isNewRegistration=true)
	{
		// isNewRegistration true= sign uppage | false= settings page
		
		$fdata->result = true;
		$fdata->alert = '';

		// the stuff we get from facebook or user but might be invalid anyway 
		$fdata->name 	= stripslashes($_POST['name']);
		$fdata->email 	= stripslashes($_POST['email']);
		$fdata->gender 	= stripslashes($_POST['gender']);
		$fdata->age 	= stripslashes($_POST['age']);
		

		$fdata->city 	= stripslashes($_POST['city']);
		$fdata->state 	= stripslashes($_POST['state']);
		$fdata->country	= stripslashes($_POST['country']);
		if (isset($_POST['zip']))
			$fdata->zip	= stripslashes($_POST['zip']);
		else
			$fdata->zip='';
		/*$fdata->researchImportance=	$_POST['researchImportance'];  // tentatively moved into templates since this field may be site-specific
		if ($isNewRegistration OR $fdata->researchImportance==0)
			$fdata->showResearchImportance=true;*/
		$fdata->optInStudy = stripslashes($_POST['optInStudy']) == 'on' ? 1 : 0;
		$fdata->acceptRules = stripslashes($_POST['acceptRules']) == 'on' ? 1 : 0;
		$fdata->noCommentNotify = stripslashes($_POST['noCommentNotify']) == 'on' ? 1 : 0;
		$fdata->rxFeatures = stripslashes($_POST['rxFeatures']) == 'on' ? 1 : 0;
		$fdata->rxMode	= stripslashes($_POST['rxMode']);
		
		// TODO:optInEmail, etc
		
		// stuff we get from facebook that cant be invalid
		$fdata->proxied_email = $this->session->proxied_email;
		
		if ($fdata->name == '')
		{
			$fdata->alert .= 'Somehow you are using facebook without a name. '.
								'You rebel you. But we still need one.<br />';
			$fdata->result = false;														
		}
		
		if ($this->accountTemplate->collectEmail)
		{
			
			
			if (!$this->validEmail($fdata->email))
			{
				//$fdata->alert .= 'In order to earn points and redeem '.
				//'rewards as part of the '.SITE_TEAM_TITLE.' we do need your email address.<br />';
				
				$fdata->alert .= 'In order to participate in the '
				.SITE_TEAM_TITLE.' we do need your email address.<br />';
				
				$fdata->result = false;														
			} else {
				// check if email exists	
				if ($isNewRegistration) {
					require_once(PATH_CORE.'/classes/user.class.php'); 
					$userTable = new UserTable($this->db); 
					if ($userTable->checkEmailExist($fdata->email)) {
						$fdata->alert .= 'This email address is already registered with us. <br />'.
						$fdata->result = false;																	
					}			
				}	
			}
		}
		
		if ($this->accountTemplate->collectAge)
		{			
			if ($fdata->age == '' || !is_numeric($fdata->age))
			{
				$fdata->alert .= 'Sorry, the age you entered is invalid.<br />';
				$fdata->result = false;			
			}
			
			if ($fdata->age < 16)
			{
				$fdata->alert .= 'Sorry, we cannot accept people under 16 years of age.<br />';
				$fdata->result = false;			
			}
		}
		
		if ($this->accountTemplate->collectLocation)
		{		
			if ($fdata->city == '' || $fdata->state =='' || $fdata->country =='')
			{
				$fdata->alert .= 'We need to know your city, state, and country of residence.<br />';
				$fdata->result = false;						
			}
		}
					
		if ($isNewRegistration AND !$fdata->acceptRules)
		{
			$fdata->alert .= "You must accept the rules to sign up!<br />";
			$fdata->result = false;
		}

		// extra checks and fields we might want to retrieve, especially the research fields
		$fdata = $this->accountTemplate->validateFormData($fdata);
	
		
		return $fdata;
				
	}
	
	function processLocationUpdate($fdata) {
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); 
		$userInfoTable = new UserInfoTable($this->db);
		
		$userinfo = $userInfoTable->getRowObject();
		
		dbRowObject::$debug = 0; // NEVER TURN ON FOR LIVE SITE
		
		if (
			!$userinfo->load($this->session->userid))
		{
		 	$fdata->alert = 'Fatal error: userid not found in database';
		 	$fdata->result = false;
		 	echo 'Error loading user table entries.';
		 	return $fdata;			
		}
		$userinfo->address1 = $fdata->address1;
		$userinfo->address2 = $fdata->address2;
		$userinfo->city = $fdata->city;
		$userinfo->state = $fdata->state;
		$userinfo->country = $fdata->country;
		if ($fdata->zip<>'')
			$userinfo->zip=$fdata->zip; // safe overwrite only if not empty string
		
		$userinfo->update();
		return $fdata;
		
	}
	
	function updateSubscriptions($fdata) {
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); 
		$userInfoTable = new UserInfoTable($this->db);
		
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();
		
		if (!$user->load($this->session->userid) ||
			!$userinfo->load($this->session->userid)) {
		 	$fdata->alert = 'Fatal error: userid not found in database';
		 	$fdata->result = false;
		 	echo 'Error loading user table entries.';
		 	return $fdata;						
		}

		$userinfo->noCommentNotify=$fdata->noCommentNotify;
	//	$user->update();
		$userinfo->update();

		require_once(PATH_CORE.'/classes/subscriptions.class.php'); 
		$subTable = new SubscriptionsTable($this->db); 
		$sub = $subTable->getRowObject();
		$sub->userid=$this->session->userid;
		$sub->rxFeatures=$fdata->rxFeatures;
		$sub->rxMode=$fdata->rxMode;
		if ($sub->rxMode=='sms') {
	  		if (!$facebookSMSPermitted=$this->facebook->api_client->users_hasAppPermission('sms')) {
				$sub->rxMode='notification';
			}
		} else if ($sub->rxMode=='email') {
	  		if (!$facebookSMSPermitted=$this->facebook->api_client->users_hasAppPermission('email')) {
				$sub->rxMode='notification';
			}
		}
		$qDup=$subTable->checkExists($this->session->userid);
		if (!$qDup) {
			$sub->insert();
		} else {			
			$data=$this->db->readQ($qDup);
			$sub->id=$data->id;
			$this->db->log($sub);
			$sub->update();			
		}
		return $fdata;		
	}
	
	function processFormUpdateDatabase($fdata)
	{
		// TODO: update data tables based on form data which is presumably now validated
		
		if ($debug) echo 'Submitted form data ok: <pre>'.print_r($fdata, true).'</pre>';
		
		// if theres a problem, we can set $fdata->alert and return it
		
		// TODO: assume session valid and all that jazz....
		
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); 
		$userInfoTable = new UserInfoTable($this->db);
		
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();
		
		dbRowObject::$debug = 0; // NEVER TURN ON FOR LIVE SITE
		
		if (!$user->load($this->session->userid) ||
			!$userinfo->load($this->session->userid))
		{
		 	$fdata->alert = 'Fatal error: userid not found in database';
		 	$fdata->result = false;
		 	echo 'Error loading user table entries.';
		 	return $fdata;			
		}
		
		$userinfo->gender= $fdata->gender;
		$userinfo->age = $fdata->age;
		$userinfo->city = $fdata->city;
		$userinfo->state = $fdata->state;
		$userinfo->country = $fdata->country;
		if ($fdata->zip<>'')
			$userinfo->zip=$fdata->zip; // safe overwrite only if not empty string
		$userinfo->researchImportance=$fdata->researchImportance;
		$userinfo->noCommentNotify=$fdata->noCommentNotify;
		//$userinfo->birthdate = ''; // TODO 
		
		$user->name = $fdata->name;
		$user->email = $fdata->email;
		$user->optInStudy = $fdata->optInStudy;
		$user->optInEmail = $fdata->optInEmail; // wrong?
		
		$user->acceptRules = $fdata->acceptRules;
		
//		$user->optInSMS = $fdata->optInSMS;
//		$user->optInProfile = $fdata->optInProfile;
//		$user->optInFeed = $fdata->optInFeed;
		if (!$user->isMember)
			$user->dateRegistered = date('Y-m-d H:i:s', time());
				
		$user->isMember = 1; // by virtue of executing signup you are a member. not reversible?	
		$user->eligibility = $this->checkEligibility($user, $userinfo);
	
		$user->update();
		$userinfo->update();
		return $fdata;
	}

	function initFormDataFromDatabase($userid)
	{
		$fdata = new stdClass;
		
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); 
		$userInfoTable = new UserInfoTable($this->db);
		
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();
		
		//dbRowObject::$debug = 1;
		
		if (!$user->load($this->session->userid) ||
			!$userinfo->load($this->session->userid))
		{
		 	$fdata->alert = 'Fatal error: userid not found in database';
		 	$fdata->result = false;
		 	echo 'Error loading user table entries.';
		 	return $fdata;			
		}
		
		$fdata->age = $userinfo->age;
		$fdata->city= $userinfo->city;
		$fdata->state= $userinfo->state;
		$fdata->country= $userinfo->country;
		$fdata->address1 = $userinfo->address1;
		$fdata->address2 = $userinfo->address2;
		$fdata->zip = $userinfo->zip;
		$fdata->gender = $userinfo->gender;
		//$userinfo->birthdate = ''; // TODO 
		
		$fdata->name = $user->name;
		$fdata->email = $user->email;
		$fdata->optInStudy = $user->optInStudy;
		$fdata->optInEmail = $user->optInEmail;
		$fdata->optInSMS = $user->optInSMS;
		$fdata->optInProfile = $user->optInProfile;
		$fdata->optInFeed = $user->optInFeed;
		$fdata->noCommentNotify = $userinfo->noCommentNotify;

		$fdata->acceptRules = $user->acceptRules;

		require_once(PATH_CORE.'/classes/subscriptions.class.php'); 
		$subTable = new SubscriptionsTable($this->db); 
		$sub = $subTable->getRowObject();
		if ($sub->loadWhere("userid=".$this->session->userid)) {
			$fdata->rxFeatures=$sub->rxFeatures;
			$fdata->rxMode=$sub->rxMode;			
		} else {
			$fdata->rxFeatures=1;
			$fdata->rxMode='notification';
		}
		
		return $fdata;
	}
	
	function checkEligibility($user, $userinfo)
	{
		if ($user->eligibility=='ineligible') return 'ineligible'; // users marked ineligible are not allowed to change
		return $this->accountTemplate->checkEligibility($user,$userinfo);
				
	}
	

	function validEmail($email)
	{
	 	if (!$this->validEmailNew($email)) return false;
	 	// old version
        $ckemail="^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$";   
        if (ereg ($ckemail, $email) && (strlen($email) > 0) && (strlen($email) <= 255))
            return true;
        else
            return false;
	}
	
	
	function validEmailNew($email) 
	{
        $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
        $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
        $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
            '\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
        $quoted_pair = '\\x5c[\\x00-\\x7f]';
        $domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
        $quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
        $domain_ref = $atom;
        $sub_domain = "($domain_ref|$domain_literal)";
        $word = "($atom|$quoted_string)";
        $domain = "$sub_domain(\\x2e$sub_domain)*";
        $local_part = "$word(\\x2e$word)*";
        $addr_spec = "$local_part\\x40$domain";
        return preg_match("!^$addr_spec$!", $email) ? 1 : 0;
    }

    
    
    function makeSelDrop($formName, $selectedOption)
    {
            //can add more forms lists to this class later if needed...
            $provinces=array('AB'=>'Alberta', 'BC'=>'British Columbia', 'MB'=>'Manitoba', 'NB'=>'New Brunswick', 'NF'=>'Newfoundland and Labrador', 'NT'=>'Northwest Territories', 'NS'=>'Nova Scotia', 'NU'=>'Nunavut', 'ON'=>'Ontario', 'PE'=>'Prince Edward Island', 'QC'=>'Quebec', 'SK'=>'Saskatchewan', 'YT'=>'Yukon', 'Other'=>'Other','space'=>'','space'=>'', 'AK '=>'Alaska', 'AL'=>'Alabama', 'AR'=>'Arkansas', 'AS'=>'American Samoa', 'AZ'=>'Arizona', 'CA'=>'California', 'CO'=>'Colorado', 'CT'=>'Connecticut', 'DE'=>'Delaware', 'FL'=>'Florida', 'GA'=>'Georgia', 'GU'=>'Guam', 'HI'=>'Hawaii', 'IA'=>'Iowa', 'ID'=>'Idaho', 'IL'=>'Illinois', 'IN'=>'Indiana', 'KS'=>'Kansas', 'KY'=>'Kentucky', 'LA'=>'Lousiana', 'MA'=>'Massachusetts', 'MD'=>'Maryland', 'ME'=>'Maine', 'MI'=>'Michigan', 'MN'=>'Minnesota', 'MO'=>'Missouri', 'MS'=>'Mississippi', 'MT'=>'Montana', 'NC'=>'North Carolina', 'ND'=>'North Dakota', 'NE'=>'Nebraska', 'NH'=>'New Hampshire', 'NJ'=>'New Jersey', 'NM'=>'New Mexico', 'NV'=>'Nevada', 'NY'=>'New York', 'OH'=>'Ohio', 'OK'=>'Oklahoma', 'OR'=>'Oregon', 'PA'=>'Pennsylvania', 'PR'=>'Puerto Rico', 'RI'=>'Rhode Island', 'SC'=>'South Carolina', 'SD'=>'South Dakota', 'TN'=>'Tennessee', 'TX'=>'Texas', 'UT'=>'Utah', 'VA'=>'Virginia', 'VI'=>'Virgin Islands', 'VT'=>'Vermont', 'WA'=>'Washington', 'DC'=>'Washington D.C.', 'WI'=>'Wisconsin', 'WV'=>'West Virginia', 'WY'=>'Wyoming');
            $countries=array(
				"AF" => "Afghanistan",
				"AL" => "Albania",
				"DZ" => "Algeria",
				"AS" => "American Samoa",
				"AD" => "Andorra",
				"AO" => "Angola",
				"AI" => "Anguilla",
				"AQ" => "Antarctica",
				"AG" => "Antigua and Barbuda",
				"AR" => "Argentina",
				"AM" => "Armenia",
				"AW" => "Aruba",
				"AU" => "Australia",
				"AT" => "Austria",
				"AZ" => "Azerbaijan",
				"BS" => "Bahamas",
				"BH" => "Bahrain",
				"BD" => "Bangladesh",
				"BB" => "Barbados",
				"BY" => "Belarus",
				"BE" => "Belgium",
				"BZ" => "Belize",
				"BJ" => "Benin",
				"BM" => "Bermuda",
				"BT" => "Bhutan",
				"BO" => "Bolivia",
				"BA" => "Bosnia and Herzegovina",
				"BW" => "Botswana",
				"BV" => "Bouvet Island",
				"BR" => "Brazil",
				"IO" => "British Indian Ocean Territory",
				"BN" => "Brunei Darussalam",
				"BG" => "Bulgaria",
				"BF" => "Burkina Faso",
				"BI" => "Burundi",
				"KH" => "Cambodia",
				"CM" => "Cameroon",
				"CA" => "Canada",
				"CV" => "Cape Verde",
				"KY" => "Cayman Islands",
				"CF" => "Central African Republic",
				"TD" => "Chad",
				"CL" => "Chile",
				"CN" => "China",
				"CX" => "Christmas Island",
				"CC" => "Cocos (Keeling) Islands",
				"CO" => "Colombia",
				"KM" => "Comoros",
				"CG" => "Congo",
				"CD" => "Congo, the Democratic Republic of the",
				"CK" => "Cook Islands",
				"CR" => "Costa Rica",
				"CI" => "Cote D'Ivoire",
				"HR" => "Croatia",
				"CU" => "Cuba",
				"CY" => "Cyprus",
				"CZ" => "Czech Republic",
				"DK" => "Denmark",
				"DJ" => "Djibouti",
				"DM" => "Dominica",
				"DO" => "Dominican Republic",
				"EC" => "Ecuador",
				"EG" => "Egypt",
				"SV" => "El Salvador",
				"GQ" => "Equatorial Guinea",
				"ER" => "Eritrea",
				"EE" => "Estonia",
				"ET" => "Ethiopia",
				"FK" => "Falkland Islands (Malvinas)",
				"FO" => "Faroe Islands",
				"FJ" => "Fiji",
				"FI" => "Finland",
				"FR" => "France",
				"GF" => "French Guiana",
				"PF" => "French Polynesia",
				"TF" => "French Southern Territories",
				"GA" => "Gabon",
				"GM" => "Gambia",
				"GE" => "Georgia",
				"DE" => "Germany",
				"GH" => "Ghana",
				"GI" => "Gibraltar",
				"GR" => "Greece",
				"GL" => "Greenland",
				"GD" => "Grenada",
				"GP" => "Guadeloupe",
				"GU" => "Guam",
				"GT" => "Guatemala",
				"GN" => "Guinea",
				"GW" => "Guinea-Bissau",
				"GY" => "Guyana",
				"HT" => "Haiti",
				"HM" => "Heard Island and Mcdonald Islands",
				"VA" => "Holy See (Vatican City State)",
				"HN" => "Honduras",
				"HK" => "Hong Kong",
				"HU" => "Hungary",
				"IS" => "Iceland",
				"IN" => "India",
				"ID" => "Indonesia",
				"IR" => "Iran, Islamic Republic of",
				"IQ" => "Iraq",
				"IE" => "Ireland",
				"IL" => "Israel",
				"IT" => "Italy",
				"JM" => "Jamaica",
				"JP" => "Japan",
				"JO" => "Jordan",
				"KZ" => "Kazakhstan",
				"KE" => "Kenya",
				"KI" => "Kiribati",
				"KP" => "Korea, Democratic People's Republic of",
				"KR" => "Korea, Republic of",
				"KW" => "Kuwait",
				"KG" => "Kyrgyzstan",
				"LA" => "Lao People's Democratic Republic",
				"LV" => "Latvia",
				"LB" => "Lebanon",
				"LS" => "Lesotho",
				"LR" => "Liberia",
				"LY" => "Libyan Arab Jamahiriya",
				"LI" => "Liechtenstein",
				"LT" => "Lithuania",
				"LU" => "Luxembourg",
				"MO" => "Macao",
				"MK" => "Macedonia, the Former Yugoslav Republic of",
				"MG" => "Madagascar",
				"MW" => "Malawi",
				"MY" => "Malaysia",
				"MV" => "Maldives",
				"ML" => "Mali",
				"MT" => "Malta",
				"MH" => "Marshall Islands",
				"MQ" => "Martinique",
				"MR" => "Mauritania",
				"MU" => "Mauritius",
				"YT" => "Mayotte",
				"MX" => "Mexico",
				"FM" => "Micronesia, Federated States of",
				"MD" => "Moldova, Republic of",
				"MC" => "Monaco",
				"MN" => "Mongolia",
				"MS" => "Montserrat",
				"MA" => "Morocco",
				"MZ" => "Mozambique",
				"MM" => "Myanmar",
				"NA" => "Namibia",
				"NR" => "Nauru",
				"NP" => "Nepal",
				"NL" => "Netherlands",
				"AN" => "Netherlands Antilles",
				"NC" => "New Caledonia",
				"NZ" => "New Zealand",
				"NI" => "Nicaragua",
				"NE" => "Niger",
				"NG" => "Nigeria",
				"NU" => "Niue",
				"NF" => "Norfolk Island",
				"MP" => "Northern Mariana Islands",
				"NO" => "Norway",
				"OM" => "Oman",
				"PK" => "Pakistan",
				"PW" => "Palau",
				"PS" => "Palestinian Territory, Occupied",
				"PA" => "Panama",
				"PG" => "Papua New Guinea",
				"PY" => "Paraguay",
				"PE" => "Peru",
				"PH" => "Philippines",
				"PN" => "Pitcairn",
				"PL" => "Poland",
				"PT" => "Portugal",
				"PR" => "Puerto Rico",
				"QA" => "Qatar",
				"RE" => "Reunion",
				"RO" => "Romania",
				"RU" => "Russian Federation",
				"RW" => "Rwanda",
				"SH" => "Saint Helena",
				"KN" => "Saint Kitts and Nevis",
				"LC" => "Saint Lucia",
				"PM" => "Saint Pierre and Miquelon",
				"VC" => "Saint Vincent and the Grenadines",
				"WS" => "Samoa",
				"SM" => "San Marino",
				"ST" => "Sao Tome and Principe",
				"SA" => "Saudi Arabia",
				"SN" => "Senegal",
				"CS" => "Serbia and Montenegro",
				"SC" => "Seychelles",
				"SL" => "Sierra Leone",
				"SG" => "Singapore",
				"SK" => "Slovakia",
				"SI" => "Slovenia",
				"SB" => "Solomon Islands",
				"SO" => "Somalia",
				"ZA" => "South Africa",
				"GS" => "South Georgia and the South Sandwich Islands",
				"ES" => "Spain",
				"LK" => "Sri Lanka",
				"SD" => "Sudan",
				"SR" => "Suriname",
				"SJ" => "Svalbard and Jan Mayen",
				"SZ" => "Swaziland",
				"SE" => "Sweden",
				"CH" => "Switzerland",
				"SY" => "Syrian Arab Republic",
				"TW" => "Taiwan, Province of China",
				"TJ" => "Tajikistan",
				"TZ" => "Tanzania, United Republic of",
				"TH" => "Thailand",
				"TL" => "Timor-Leste",
				"TG" => "Togo",
				"TK" => "Tokelau",
				"TO" => "Tonga",
				"TT" => "Trinidad and Tobago",
				"TN" => "Tunisia",
				"TR" => "Turkey",
				"TM" => "Turkmenistan",
				"TC" => "Turks and Caicos Islands",
				"TV" => "Tuvalu",
				"UG" => "Uganda",
				"UA" => "Ukraine",
				"AE" => "United Arab Emirates",
				"GB" => "United Kingdom",
				"US" => "United States",
				"UM" => "United States Minor Outlying Islands",
				"UY" => "Uruguay",
				"UZ" => "Uzbekistan",
				"VU" => "Vanuatu",
				"VE" => "Venezuela",
				"VN" => "Viet Nam",
				"VG" => "Virgin Islands, British",
				"VI" => "Virgin Islands, U.s.",
				"WF" => "Wallis and Futuna",
				"EH" => "Western Sahara",
				"YE" => "Yemen",
				"ZM" => "Zambia",
				"ZW" => "Zimbabwe",
            );
            
            $allLists=array('provinces'=>$provinces, 'countries'=>$countries);
           
            $list=$allLists[$formName];
            $optionList='';
            foreach ($list as $key=>$item) {
                 if ($key==$selectedOption || $item==$selectedOption)  // slightly modified match selected option against abbrev and full name
                     $sel='SELECTED';
                else
                    $sel='';

                $optionList.="<option value=\"".$item."\" ".$sel.">".$item."</option> \n";  // use full name for option value to be consistent with facebook
                
                 
            }
            return $optionList;
        }
	
	function buildOptInStudyText($optInStudy=true) 
	{
		return $this->accountTemplate->buildOptInStudyText($optInStudy);
	}
	
	function buildAcceptRulesText($acceptRules=false) 
	{
		return $this->accountTemplate->buildAcceptRulesText($acceptRules);
	}
	
	function buildAccountAddressForm($fdata=null, $message='') {
		if (!$fdata) {
			// $fdata = initFormData();
		}
		$code='';		
		if (strlen($message))
		{
			$code.='<fb:editor-custom label="" name="message"><fb:error><fb:message>There was a problem</fb:message>Please correct the following items: '.$message.'</fb:error>'.
					'</fb:editor-custom>';
		}		
		$code.='<fb:editor action="?p=account&o=address&step=submit" labelwidth="100">';
	   	$code .= '<fb:editor-text label="Address line 1" name="address1" value="'.$fdata->address1.'"/>';
	   	$code .= '<fb:editor-text label="Address line 2" name="address2" value="'.$fdata->address2.'"/>';
		$code.=$this->getLocationFields($fdata,true);
	   	$code .= '<fb:editor-text label="Postal code" name="zip" value="'.$fdata->zip.'"/>';		
		$code.='<fb:editor-buttonset>  
	           <fb:editor-button value="Update"/> <fb:editor-cancel href="'.URL_CANVAS.'"/>  </fb:editor-buttonset>';
		$code.='</fb:editor>';	
		return $code;
	}
	
	function buildAccountSubscribeForm($fdata=null, $message='') 
	{
		if (!$fdata) {
			$fdata = initFormData();
		}
		$code='';		
		
		$code.='<fb:editor action="?p=account&o=subscribe&step=submit" labelwidth="100">';
		$code.=$this->buildPermissions();
		$code.='<fb:editor-custom><input type="checkbox" name="noCommentNotify" '.($fdata->noCommentNotify==1?'CHECKED':'').'> Do not send me notifications when people reply to my comments and stories</fb:editor-custom>';

		$code.='<fb:editor-custom><input type="checkbox" name="rxFeatures" '.($fdata->rxFeatures==1?'CHECKED':'').'> Notify me when featured stories are updated</fb:editor-custom>';

    	$code .='<fb:editor-custom label="Contact preference">'. 
	  		'<select name="rxMode">  
	  			<option value="notification" '.(($fdata->rxMode=='notification')?'SELECTED':'').'>Facebook Notification</option>
	  			<option value="email" '.(($fdata->rxMode=='email')?'SELECTED':'').'>Email - Requires authorization above</option>
	  			<option value="sms" '.(($fdata->rxMode=='sms')?'SELECTED':'').'>Text Message (SMS) - Requires authorization above</option>
	  		</select>'; 	     
		$code.= '</fb:editor-custom> ';
	   	
		$code.='<fb:editor-buttonset>  
	           <fb:editor-button value="Update"/> <fb:editor-cancel href="'.URL_CANVAS.'"/>  </fb:editor-buttonset>';
		$code.='</fb:editor>';	
		return $code;		
	}	
	
	function buildPermissions() {
		$code='';
	    $facebookPublishStream=$this->facebook->api_client->users_hasAppPermission('publish_stream');
	    if (!$facebookPublishStream)
			$code.='<fb:editor-custom><fb:prompt-permission perms="publish_stream">Would you like to grant us permission to publish to your Facebook profile stream? (100 pts)</fb:prompt-permission><br></fb:editor-custom>';
	    
	    $facebookEmailPermitted=$this->facebook->api_client->users_hasAppPermission('email');
	    if (!$facebookEmailPermitted)
			$code.='<fb:editor-custom><fb:prompt-permission perms="email">Would you like to receive email from us through facebook? (50 pts)</fb:prompt-permission><br></fb:editor-custom>';

  		$facebookSMSPermitted=$this->facebook->api_client->users_hasAppPermission('sms');
	   	if (!$facebookSMSPermitted)
			$code.='<fb:editor-custom><fb:prompt-permission perms="sms">Would you like to receive sms notifications from us through facebook? (50 pts)</fb:prompt-permission><br /></fb:editor-custom>';			
		
		$facebookOfflinePermitted=$this->facebook->api_client->users_hasAppPermission('offline_access');
		if (!$facebookOfflinePermitted)
			$code.='<fb:editor-custom><fb:prompt-permission perms="offline_access">Would you like Facebook to keep you permanently signed in to '.SITE_TITLE.'</fb:prompt-permission>? This will prevent timeout messages from appearing.</fb:editor-custom>';
			
		$code .= '<fb:editor-custom><fb:add-section-button section="profile" /><fb:editor-custom>';
		return $code;
	}
}
?>