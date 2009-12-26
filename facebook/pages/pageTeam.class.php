<?php

class pageTeam {

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
		$this->commonTeam = &$page->commonTeam;
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
	}

	function fetch($mode='fullPage',$option='team') 
	{
		$showPage=true;
		$inside='';
		//$this->page->recordSrc();		
		// check for minors and consent form
		if ($this->page->session->isLoaded AND ($this->page->session->ui->rxConsentForm==0 AND $this->page->session->ui->age>0 AND $this->page->session->ui->age<18 AND ENABLE_MINOR_CONSENT)) 
		{
			// via timeStrToUnixModB in util class		
			sscanf($this->page->session->u->dateRegistered,"%4u-%2u-%2u %2u:%2u:%2u",$year,$month,$day,$hour,$min,$sec);
        	$dateReg=mktime($hour,$min,$sec,$month,$day,$year);
        	if (time()-$dateReg>(3600*24*5)) { // five days elapsed
        		$showPage=false; // block usage of team page
        		$inside=$this->page->buildMessage('error','Action Team Access Disabled','Please ask your parent or guardian send in your consent form to continue using the '.SITE_TITLE.' '.SITE_TEAM_TITLE.'. <a href="'.URL_CALLBACK.'?p=cache&pdf=consentForm" target="_blank">Download the consent form here</a>.');
        	} else {
				$inside=$this->page->buildMessage('explanation','Waiting for your consent form','To prevent access to the '.SITE_TITLE.' '.SITE_TEAM_TITLE.' from being disabled, please ask your parent or guardian send in your consent form ASAP. <a href="'.URL_CALLBACK.'?p=cache&pdf=consentForm" target="_blank">Download the consent form here</a>.');
          	}
		} 
		if ($showPage) {
			require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
			$this->teamObj=new actionTeam($this->page);
			$tabs=$this->teamObj->buildSubNav('team');	 
			if ($this->page->session->ui->hideTeamIntro==0) {
				$inside.=$this->fetchUpperBox();				
			}				
			$inside.=$this->fetchLowerBox();
			if ($mode=='teamWrap') return $inside;
			$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
			$inside.='<input type="hidden" id="pagingFunction" value="fetchFeedPage">';		
			if ($this->page->isAjax) return $inside;
			// check for referral and log it
			$referid=$this->page->fetchReferral();
			if ($referid!==false) {
				$this->page->recordReferral($referid,'referToSite');
				// removed $this->facebook->require_login();
			}												
			/*
			 * "Loose nukes sink users"
			 * if (isset($_GET['nukeMe'])) // TODO: debug option, disable for release 
			{
				$inside .= 'Nuking....';
				$inside .= $this->debugNukeUser();
			} */	
			if (isset($_GET['newsignup'])) {				
				//$inside=$this->page->buildMessage('success','Please verify your email address','<p>You will soon receive an email with a link to verify your email address. If you do not receive this email, <i>please check your spam folder.</i></p>').$inside;
				mail('newscloud@gmail.com', SITE_TITLE.' Sign Up By '.$this->page->session->u->email, 'No more to say.', 'From: support@newscloud.com'."\r\n");									
			}			
		} 				
		$code=$this->page->constructPage('team',$inside);
		return $code;
	}
			
	function fetchUpperBox()
	{
		$code='';
		$code.='<div id="teamPanel"><div id="teamIcon" class="panel_block">';
		// video - to do - ask Zibby if there are two videos, or only one...
		
		$code .=$this->commonTeam['IntroThumb']; // includes thumb div with video or image or whatever
			$code.='<div class="storyBlockWrap">';
		if ($this->page->session->isMember)
		{
			// member just joined
			if (isset($_GET['newsignup']) ) // && $_GET['newsignup'] - jr says does nothing to do - delete comment
			{
				$code .= $this->commonTeam['NewSignupIntro'];
						
			} else {
				// pre-existing member
				$code .= $this->commonTeam['MemberIntro'];
			}

			// to do - automate this list???
			// removed from nonMember view
			$code.=
				'<p class="bold">Earn points from your story activity</p>';
	
			
			
			require_once(PATH_CORE.'/classes/challenges.class.php');
 		
	 		$ct = new ChallengeTable($this->db);
	 		$challenges=$ct->getTitlesAndPointsByShortName(array('postStory','invite','shareStory',
 												'friendSignup','vote','comment','blog','addAppTab','addBookmarkTool' ));
 			
          $code.= '<div class="pointsTable">
                  <table cellspacing="0">
                    <tbody>';
          
          
          $code .= '<tr>'
          			.$this->teamObj->buildLegendEntry('postStory',$challenges['postStory'])
          			.$this->teamObj->buildLegendEntry('stories',$challenges['vote'])
          			.'</tr>';
          $code .= '<tr>'
          			.$this->teamObj->buildLegendEntry('stories',$challenges['shareStory'])
          			.$this->teamObj->buildLegendEntry('challenges',$challenges['addBookmarkTool'])
          			.'</tr>';

			
			/*
	                        '<td><a href="#" onclick="switchPage(\'postStory\');return false;">Post a story</a></td>'.
	                        '<td class="pointValue">Earn 10 <span class="pts">pts</span></td>'.
	                        '<td><a href="#" onclick="switchPage(\'stories\');return false;">Vote on stories</a></td>'.
	                        '<td class="pointValue">Earn 5 <span class="pts">pts</span></td>'.
	                      '</tr>'.
	                      '<tr>'.
	                        '<td><a href="#" onclick="switchPage(\'stories\');return false;">Share a story</a></td>'.
	                        '<td class="pointValue">Earn 25 <span class="pts">pts</span></td>'.
	                        '<td><a href="#" onclick="switchPage(\'postStory\');return false;">Add bookmark tool</a></td>'.
	                        '<td class="pointValue">Earn 25 <span class="pts">pts</span></td>'.
	                      '</tr>'.
			*/
			
	        $code.=            '</tbody>'.
	                  '</table>'.
	        '</div><!--end "pointsTable"-->';		
			
		} else
		{
			$code .= $this->commonTeam['NonMemberIntro'];
	
		}
		$code.='</div><!--end "storyBlockWrap"-->';
		$code .= '</div><!--end "team bob panel_block"--></div><!--end "teamPanel"-->';
		
		return $code;
		
	}

	function fetchLowerBox()
	{
		$code='<div id="col_left">';
		if ($this->page->session->isMember) {
			$code .='<div class="panel_1">';
			$code .= $this->fetchTeamFriends();
			$code .='</div><!-- end panel_1 -->';			
		}		
		// cache the team feed box
		$cacheName='team_feed';
		if ($this->templateObj->checkCache($cacheName,7)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {
			$temp='<div class="panel_1">'.
				'<div class="panelBar clearfix">'.
					'<h2>'.$this->commonTeam['TeamFeedPanelTitle'].'</h2>'.
					'<div class="bar_link"><a href="?p=postStory" onclick="switchPage(\'postStory\');return false;">Post a story</a></div>'.
				'</div><!--end "panelBar"-->';
			$temp.=$this->fetchFeedBox();
			$temp.='</div><!--end " panel_1"-->';
			$this->templateObj->cacheContent($cacheName,$temp);
		}	
		$code.=$temp;
			$code .= '</div><!--end col_left -->';

			$code.='<div id="col_right">';
			$code.=$this->teamObj->fetchSidePanel('team');
			
/*	$code.='<div class="panel_2">
        <div class="panelBar clearfix">
            <h2>Featured Action Rewards</h2>
            <div class="bar_link"><a href="#" onclick="switchPage(\'rewards\');return false;">See all</a></div>
        </div><!--end "panelBar"-->';
			$code.=$this->fetchTeamPrizes();
 			$code.='</div><!--end "panel_2"-->';
*/
		$code .= '</div><!--end col_right-->';		
		return $code;
	}
	
	function fetchTeamPrizes()
	{
		//require_once(PATH_CORE. '/classes/prizes.class.php');
		//$rewards = new rewards($this->db);
		
		//$code .= $rewards->fetchRewardsPage('pointCost', 1);
		require_once(PATH_FACEBOOK .'/classes/actionTeam.class.php');
		$at = new actionTeam(&$this->page);
		$code .= $at->fetchRewardsRandom();
		return $code;
	}
	
	function fetchFeedBox($filter_userid = 0, $currentPage=1)
	{
		require_once(PATH_FACEBOOK.'/classes/actionFeed.class.php');
		$actionFeed = new actionFeed(&$this->db);
		$code.=$actionFeed->fetchFeed('all', $currentPage); 
		return $code;
	}
	
	function fetchTeamFriends($maxfriends = 5)
	{		
		$cacheName="teamFriends_{$this->page->session->userid}_collapsed";
		if ($this->templateObj->checkCache($cacheName,7)) 
		{	// still current, get from cache	
			$code=$this->templateObj->fetchCache($cacheName);
		} else 
		{		
			// note that with the availability of the session in ajax we can actually simplify this with the cached user info itselfg
			$code = pageTeam::fetchTeamFriendList($this->db, $this->page->session->userid, 'collapsed',false,$this->commonTeam['TeamFriendsPanelTitle']);
			
			$this->templateObj->cacheContent($cacheName,$code);
		}
		return $code;
	}
	// TODO: CLEANUP - this is a mess! lose the static or move inner panel fetch to core
	static function fetchTeamFriendList($db, $userid, $state='expanded',$isAjax=false, $panelTitle)
	{
		if ($userid)
			$memberfriends = pageTeam::getMemberFriends($db, $userid);
		$code =	'<div class="panelBar clearfix"><h2>'.$panelTitle.' ('.count($memberfriends).')</h2>';		
		$code .=  '<div class="bar_link"><a href="?p=invite" onclick="switchPage(\'invite\');return false;">Invite more</a></div><div id="friendsSeeAll" class="bar_link '.($state=='collapsed'?'':'hidden').'"><a href="#" onclick="refreshTeamFriendsList(\'expanded\');return false;">See all</a></div>';
		$code .=  '<div id="friendsSeeFewer" class="bar_link '.($state=='collapsed'?'hidden':'').'"><a class="" href="#" onclick="refreshTeamFriendsList(\'collapsed\');return false;">See fewer</a></div>';
		if ($state == 'collapsed')
		{
			$maxfriends = 6;
		}
		else
		{
			$maxfriends = 300;
		}
		$code.='</div><!--end "panelBar"-->';	
		$code.= '<div id="ajaxTeamFriendsList">';
		// using a token to str_replace below because of your need to set maxfriends before building array of pics		
		$code.='{inside}';
		$code .= '</div><!-- end ajaxTeamFriendsList -->';	
		$inside='<div class="panel_block">';
		require_once(PATH_CORE .'/classes/template.class.php');
		for ($i=0; $i < min( $maxfriends, count($memberfriends)); $i++) 
		{
			$inside .= '<div class="friend">'.
					template::buildLinkedProfilePic($memberfriends[$i]).
					'</div>';
		}
		$inside .= '</div><!--end "panel_block"-->';
		if ($isAjax) return $inside;
		$code=str_replace('{inside}',$inside,$code);
		return $code;
	}
	
		
	static function getMemberFriends($db, $userid)
	{
		if (is_null($db)) 
		{ 
			require_once(PATH_CORE.'/classes/db.class.php');
			$db=new cloudDatabase();
		} 
		
		require_once (PATH_CORE .'/classes/user.class.php');
		$userInfoTable = new UserInfoTable($db);
		$userinfo = $userInfoTable->getRowObject();
	
		if ($userinfo->load($userid))
			return $userInfoTable->getFbIdsForUsers(
						explode(',',$userinfo->memberFriends));
			
		return null;
		
	}
	
	
	function debugNukeUser()
	{
		
		require_once (PATH_CORE. '/classes/teamBackend.class.php');
		$teamObj = new teamBackend($db);
		$teamObj->cleanupUser($this->page->session->userid);
				
	}

	
}

?>