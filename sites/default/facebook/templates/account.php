<?php

/*
 * the accountTemplate class provides a central location for site-specific account form and eligibility logic 
 * 
 */
// TODO: if dynamic templates are to be used inside here, add these dummy calls to register them
// $category='account';
//$dynTemp->useDBTemplate('ResearchQuestion','How interested are you in University of Minnesota community issues?', false, $category);
class accountTemplate
{
	var $collectEmail = true;
	var $collectAge = false;
	var $collectGender = false;
	var $collectLocation = false;
	
	function __construct()
	{
		
		
	}
	
	
	function checkEligibility($user, $userinfo)
	{
		return ($user->optInStudy) ? 'team' : 'general';
			//($userinfo->country == 'United States') && ($userinfo->age >=16 && $userinfo->age <= 25) 
		
	}
	
	
	// for building fields that go where we (arbitrarily) placed the research question fields
	function fetchResearchFields($fdata)
	{
		if (ENABLE_RESEARCH_STUDY)
		{
			if ($fdata->showResearchImportance  OR isset($_GET['force'])) {
				// does not allow changing it after its been set
				$importance=array(0=>'How interested are you in '.SITE_TOPIC.' news?',1=>'Extremely uninterested',2=>'Mostly uninterested',3=>'Somewhat uninterested',4=>'Neither interested nor uninterested',5=>'Somewhat interested',6=>'Mostly interested',7=>'Extremely interested');
			    	$code .='<fb:editor-custom label="Interest level">'. 
				  		'<select name="researchImportance">';
				foreach ($importance as $key=>$value) {
					$code.='<option value="'.$key.'" '.(($fdata->researchImportance==$key)?'SELECTED':'').'>'.$value.'</option>';
				}
				$code.= '</select></fb:editor-custom> ';			
			} else {
				$code.= '<input type="hidden" name="researchImportance" value="'.$fdata->researchImportance.'" />';
			}
		}
		return $code;	
	}
	
	// for building site-specific fields that appear towards the bottom of the Account INfo section
	function fetchCustomAccountFields($fdata)
	{
		// custom fbjs strings for age text			
		$code .= '	<fb:js-string var="generalText"><h2>Volunteer for our research study</h2><p>Your participation will help us to advance our collective understanding of how to better engage people in online information.</p></fb:js-string>'; 
		 
		// div in the form where we'll put them
		$code.= '<fb:editor-custom>';
		$code.= '<div id="accountAgeSpecificOptInText">Hidden at first - age specific opt-in text goes here</div>'; // TODO: this div updated from js based on age spec
		$code.= '</fb:editor-custom>';
		
		/*
		 * This script block registers an onChange and onLoad handler on the age select control
		 * which updates the eligibility text appropriately.
		 * 
		 */		
		
		$showOptIn = $fdata->showOptIn ? 1 : 0; // safe parameter translation
		$code .= "<script><!--
	
			function registerAccountAgeEvents()
			{
				
				var ageControl=document.getElementById('accountAge');
				ageControl.addEventListener('change',accountAgeChanged);
				ageControl.addEventListener('load',accountAgeChanged);
					
			}
		
			function accountAgeChanged(changeOptInInfo)
			{
				changeOptInInfo = $showOptIn; 
				var ageControl=document.getElementById('accountAge');
				
				// refresh age/eligibility-specific text
				
				var ageSpecDiv = document.getElementById('accountAgeSpecificOptInText');	
				var age = parseInt(ageControl.getValue()); 
				
				
				if (changeOptInInfo) {
					// opt in is available, so change headers
					if ((age >= 16 && age <= 25) || age==0) // special case so the inegilibility text doesnt show until a real age is selected
					{	
						ageSpecDiv.setInnerFBML(generalText); // these set up in document with fb:js-string		
					}
					else
					{	
						ageSpecDiv.setInnerFBML(generalText);
					}
				} else {
					ageSpecDiv.setClassName('hidden');
				}
			}
		
			registerAccountAgeEvents();
			accountAgeChanged($showOptIn); 
			--> </script>"; // should work on canvas pages
		
		return $code;
		
		
		
	}		
	
	function buildOptInStudyText($optInStudy=true) 
	{
		$code.='<fb:editor-custom><p><input type="checkbox" name="optInStudy" '.($optInStudy ? 'CHECKED':'').'>'.
		'&nbsp;I have read the <strong><a href="'.URL_CANVAS.'?p=consent" target="_blank">Research Consent Form</a></strong> and I agree to being part of the research study.</p>'.			    
       	$code.= '</fb:editor-custom> ';

	    return $code;
	}
	
	function buildAcceptRulesText($acceptRules=false)
	{
       	$code.='<fb:editor-custom><p><input type="checkbox" name="acceptRules" '.($acceptRules ? 'CHECKED':'').'>'.
		'&nbsp;By joining the '.SITE_TITLE.' '.SITE_TEAM_TITLE.', you consent to the <strong><a href="'.URL_CANVAS.'?p=tos" target="_blank">'.SITE_TEAM_TITLE.' Official Rules</a></strong>.' . 
		(ENABLE_ACTION_REWARDS ? 'Minors will be required to provide consent from parents or guardians to participate and to redeem rewards. Verification of your age and address may be required for redemption of rewards.</p>' : '');	    
       	$code.= '</fb:editor-custom> ';       	
		
		return $code;
	}
	
	
	function validateFormData($fdata)
	{
		// called from master validateFormData to check for site-specific fields in post, 
		// possibly store them in the form data, and emit site-specific error messages
		
		$fdata->researchImportance=	$_POST['researchImportance'];
		if ($isNewRegistration OR $fdata->researchImportance==0)
			$fdata->showResearchImportance=true; //wtf?
			
		if ($fdata->researchImportance == 0 )
		{
			$fdata->alert .= 'Please tell us your level of interest in '.SITE_TOPIC.'.<br />';
			$fdata->result = false;			
		}
	
		if ($this->collectGender AND $fdata->gender == '')
		{
			$fdata->alert .= 'For research purposes we need to know your gender.<br />';
			$fdata->result = false;			
		}
	
		
		return $fdata;
				
	}
	
	
};
?>