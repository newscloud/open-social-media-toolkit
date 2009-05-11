<?php

class pageChallenges {

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
			
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		$tabs.=$this->teamObj->buildSubNav('challenges');	 
		$inside='<div id="col_left"><!-- begin left side -->';
		if ($message<>'') $inside .= '<div class="message"><h2>'. $message .'</h2></div>';
		if ($step != 'submit')
		{	
				$this->templateObj->registerTemplates(MODULE_ACTIVE,'challenges');               				
					
			if (!$id) {				
				// to do note: this is getting duplicated in fetchChallengeList
				$temp=$this->fetchChallengeList($currentPage);
				$inside.=$this->templateObj->templates['challengeTip'].$temp;
			} else {
							
				$inside.='&laquo; <a href="?p=challenges" onclick="switchPage(\'challenges\');return false;">'.$this->templateObj->templates['BackText'].'</a><br /><br />';
				$inside.=$this->fetchChallengeDetail($id, false, &$challenge);
				if ($challenge->id && $challenge->type=='submission')
					$inside.= $this->fetchWeDidThis($id, $currentPage);
				// display notice about challenge limits
				$inside .= $this->templateObj->templates['challengeLimits'];
								
			}
		} else
		{
			$inside .= $this->fetchChallengeSubmitted(); // TODO: obsolete, this functionality has move to pageChallengeSubmit
			
		
		}			
		
		
		
		$inside.='</div><!-- end left side --><div id="col_right">';
		// TODO: This was too wide in current use of styl, so it was bumping dwn the page
		$inside.=$this->teamObj->fetchSidePanel('challenges');
		
		$inside.='</div> <!-- end right side -->';
		
		$inside.='<input type="hidden" id="pagingFunction" value="fetchChallenges">';		
	
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside,'fetchChallenges');		
		return $code;
	}
	
	function fetchSubTab() {
		
	}

	function fetchChallengeList($currentPage=1) 
	{
		require_once(PATH_CORE.'/classes/challenges.class.php');		
		
		$challenges = new challenges($this->db);		
		$code.='<div id="ajaxFeed" class="panel_1">
					<h2>'.$this->templateObj->templates['ChallengesTitle'].'</h2>'; // do I need to keep this id?
		
		$code .= $challenges->fetchChallenges('pointValue',$currentPage, false);
		$code .= '</div><!--end "ajaxFeed" "panel_1"-->';
		return $code;
	}		

	function fetchChallengeDetail($id, $noButtons=false, &$challenge) // TODO: challenge detail page as in design, with simple upper pane and lower panes
	{
		require_once(PATH_CORE.'/classes/challenges.class.php');		
		$challengeTable = new ChallengeTable($this->db);
		$challenge = $challengeTable->getRowObject();
	
		if ($id && $challenge->load($id))
		{

			if ($challenge->maxUserCompletionsPerDay>0 || $challenge->maxUserCompletions>0)
			{
				$challengelimits = 
					"You may receive points for this challenge ".
						($challenge->maxUserCompletionsPerDay>0 ? 
							"$challenge->maxUserCompletionsPerDay times per day" : "") 
						. ($challenge->maxUserCompletionsPerDay>0 && $challenge->maxUserCompletions ? " and " : "")
						. ($challenge->maxUserCompletions>0 ? "$challenge->maxUserCompletions times".
							$this->templateObj->templates['CompletionTimesClause']."" : "")
						.'. ';
			}		
			$challengelimits .= 
					($challenge->initialCompletions>0 ? "We will only award points to the 
						first $challenge->initialCompletions users that complete it." : '');
			 
			

			// if challenge expired, no buttons
			sscanf($challenge->dateEnd,"%4u-%2u-%2u %2u:%2u:%2u",$year,$month,$day,$hour,$min,$sec);
        	$newtstamp=mktime($hour,$min,$sec,$month,$day,$year);	
			if ($newtstamp<time()) {
				$noButtons=true;
				$isExpired=true;	
			} else 
				$isExpired=false;
							 
			
			$designcode = '
<div id="readStoryList">
  <div class="panel_block">    
    <div class="thumb"><img src="' . URL_THUMBNAILS.'/'.$challenge->thumbnail. '" width="180" alt="challenge thumbnail" /></div>
    <div class="storyBlockWrap">
      <p class="storyHead">'.$challenge->title.'</p>
		    <div class="storyBlockMeta">
		    	<p class="pointValue">Earn '.($challenge->pointValue==0 ? 'flex' : $challenge->pointValue).' <span class="pts">pts</span></p>		     	
     	     </div><!--end "storyBlockMeta"-->
     	     <!--<p class="storyCaption">--><p>'.$challenge->description.'</p>'
     	     . $this->templateObj->templates['ChallengeDescriptionFootnote'] 
     	     .($challenge->type=='automatic' ?
     	     	'<h3>This challenge is part of the '.SITE_TITLE.' application itself, you will receive credit automatically just by using the site</h3>':'')
			.'<h3>'.($isExpired ? 'This challenge is expired':$challengelimits).'</h3>'	
			
	.'</div><!--end "storyBlockWrap"-->
    
    
    <p class="">
    	    '.
				($noButtons ? '' : 
					template::buildChallengeSubmitLink("I did this",
						$challenge->id,"btn_1 ".($challenge->type == 'automatic' ? 'hidden' : ''))).
				/*template::buildChallengeLink("Challenge your friends",$challenge->id,"btn_2").*/ // TODO: point to actual challenge-your-friends dialog -- if we even want this on the challenge page?
			'
    </p>
  </div><!--end "panel_block"-->
</div><!--end "readStoryList"-->
			';
			
			$code = $designcode;
			
		} else
		{
			$code .= "Invalid challenge id='$id', please try again.";
			
		}
		$code = '<div class="">'.$code.'</div>';
		
		
		//$code .= 'TODO: We did this panel goes here:';		
		return $code;
		
	}
		
	function fetchWeDidThis($challengeid, $currentPage=1)
	{
		require_once(PATH_FACEBOOK.'/classes/actionFeed.class.php');
		$actionFeed = new actionFeed(&$this->db);
		$actionFeed->emptyLogMessage = 'No one has done this yet, be the first!';
		//$actionFeed->showOnlyChallengeBlog = true; // now implied by filter_challenge being set
		
		$code.='<input type="hidden" id="pagingFunction" value="fetchFeedPage">';		
		
		$code.='<div class="panel_1">';		
		$code .='<div class="panelBar clearfix">
					<h2>We Did This</h2>
					<!-- <div class="bar_link"><a href="#">I did this too!</a></div> -->
					</div><!__end "panelBar"__>';
		
		$code .='<div id="ajaxFeed">';
		$code.=$actionFeed->fetchFeed('challenges', $currentPage, 0, $challengeid, true /*minor hack: tell it we're in ajax mode to hide the filter bar*/);
		$code.='<!-- end ajaxFeed --></div>';
		$code .='</div><!-- end panel_1 -->';

		
		
		return $code;
	}
	
	
}

?>