<?php

/* Provides Facebook-client specific action team help */
class actionTeam {
	
	var $page;
	var $db;
	var $session;
	var $isAppTab=false;
		
	function __construct(&$page) {
		$this->page=&$page;
		$this->db=&$page->db;
		$this->session=&$page->session;
		$this->common = &$page->common;
		$this->commonTeam = &$page->commonTeam;
	}
	
	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db); 
		//$this->nameLevels=$this->templateObj->templates['userLevels']->nameLevels; // uh, dont they have to be registered first??
		//$this->nameLevels=$userLevels->nameLevels; // cache these locally			
	}	
	
	function setAppTabMode($fbId) {
		$this->isAppTab=true;		
		$this->session->fbId=$fbId;
		$this->session->isMember=false;
		// to do - look up session to display profile summary
	}
	
	function buildSubNav($currentSub='team') 
	{
		/*$pages = array(
		 'team' => 'Overview',
		 'challenges'=> 'Challenges',
		 'leaders'=> 'Leaders',
		 'rewards'=> 'Rewards',
		 'rules'=> 'Rules'
		 );*/
		$pages = array();
		
		$pages['team'] = 'Overview';
		if (ENABLE_ACTION_CHALLENGES) $pages['challenges']='Challenges';
		$pages['leaders']='Leaders';
		if (ENABLE_ACTION_REWARDS) $pages['rewards']='Rewards';
		if (defined('ENABLE_ACTION_WALL') AND ENABLE_ACTION_WALL==true) {
			$pages['wall']='Forum';
		}
		$pages['rules']='Rules';		
		 
		 $tabs='<div id="subNav" class="tabs clearfix"><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		 $i=0;
		 foreach (array_keys($pages) as $pagename)
		 {		 	
		 	if ($i==0) {
		 		$clsName='class="first"';
		 	} else {
		 		$clsName='';	
		 	}
	 		$tabs.='<li '.$clsName.'><a id="subtab'.$pagename.'" href="?p='.$pagename.'" onclick="setTeamTab(\''.$pagename.'\');return false;" '.($currentSub==$pagename?'class="selected"':'').'>'.$pages[$pagename].'</a></li>';	
		 	$i++;
		 }
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
	 	return $tabs;
 	}	
 	
 	function fetchLegend($mode='short')  	
 	{
 		$code = '<div  id="actionLegend">'. 		
 			(ENABLE_ACTION_REWARDS ? '<span class="bump4 float_left">Your activities earn you reward points</span>' : '');
          if (!$this->session->isMember) 
 				$code.=$this->buildLink('signup',$this->commonTeam['JoinTheTeam'], false, false,'btn_1 btn_1_lg');
 			else {
 				$code.=ENABLE_ACTION_CHALLENGES ? $this->buildLink('challenges','See all challenges', true, false,'btn_1') : '';
 			}  	

 		require_once(PATH_CORE.'/classes/challenges.class.php');
 		
 		$ct = new ChallengeTable($this->db);
 		$challenges=$ct->getTitlesAndPointsByShortName(array('postStory','invite','shareStory',
 												'friendSignup','vote','comment','blog','addAppTab' ));
 			
          $code.= '<div class="pointsTable">
                  <table cellspacing="0">
                    <tbody>';
          
          
          $code .= '<tr>'
          			.$this->buildLegendEntry('postStory',$challenges['postStory'])
          			.$this->buildLegendEntry('invite',$challenges['invite'])
          			.'</tr>';
          $code .= '<tr>'
          			.$this->buildLegendEntry('stories',$challenges['shareStory'])
          			.$this->buildLegendEntry('challenges',$challenges['friendSignup'])
          			.'</tr>';
          			
          /*$code .= '<tr>
                        <td>'.$this->buildLink('postStory','Post a story', true, false, '').'</td>
                        <td class="pointValue">Earn 10 <span class="pts">pts</span></td>
                        <td>'.$this->buildLink('invite', 'Invite more friends', true, false,'').'</td>
                        <td class="pointValue">Earn 25 <span class="pts">pts</span></td>
                      </tr>
                      <tr>
                        <td>'.$this->buildLink('stories', 'Share a story', true, false, '').'</td>
                        <td class="pointValue">Earn 25 <span class="pts">pts</span></td>
                        <td>'.$this->buildLink('challenges','Friend signs up (because of you)',true,false,'').'</td>
                        <td class="pointValue">Earn 100 <span class="pts">pts</span></td>
                      </tr>';*/
          if ($mode=='full') {
         	$code .= '<tr>'
          			.$this->buildLegendEntry('stories',$challenges['vote'])
          			.$this->buildLegendEntry('stories',$challenges['comment'])
          			.'</tr>';
          	$code .= '<tr>'
          			.$this->buildLegendEntry('',$challenges['blog'])
          			.$this->buildLegendEntry('challenges',$challenges['addAppTab'])
          			.'</tr>';
     
        /*              $code.='<tr>
                        <td>'.$this->buildLink('stories', 'Vote on a story', true, false, '').'</td>
                        <td class="pointValue">Earn 5 <span class="pts">pt</span></td>
                        <td>'.$this->buildLink('stories','Comment on an article',true,false,'').'</td>
                        <td class="pointValue">Earn 10 <span class="pts">pts</span></td>
                      </tr>';
                      $code.='<tr>
                        <td>Blog about '.SITE_TITLE.'</td>
                        <td class="pointValue">Earn 75 <span class="pts">pts</span></td>
                        <td>'.$this->buildLink('challenges','Add the App Tab to Your Profile',true,false,'').'</td>
                        <td class="pointValue">Earn 100 <span class="pts">pts</span></td>
                      </tr>';

			
         	*/
         }
		$code.='</tbody></table></div><!-- end points Table -->
    </div><!-- end actionLegend -->';
 		
 		
 		return $code;	
 	}
 	
 	function buildLegendEntry($linkname, $challenge)
 	{
	
 		$code .=  '<td>'.$this->buildLink($linkname, $challenge['title'], true, false, '').'</td>'.
                    '<td class="pointValue">Earn '.$challenge['pointValue'].' '.
 					'<span class="pts">pt'.($challenge['pointValue'] >1 ? 's':'').'</span></td>';
 		return $code;
 		 
 	}
 	
	function fetchSidePanel($p='home',$numberItems=3) {
		$this->setupLibraries();
		if ($p=='home' AND defined('ADS_HOME_SIDEBAR')) {
			$code.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=homeSidebar" frameborder="0" scrolling="no" style="width:244px;height:70px;padding:0px;"/>',$this->common['adWrapSidebar']);
		} else if ( defined('ADS_ANY_SIDEBAR')) {
			$code.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=anySidebar" frameborder="0" scrolling="no" style="width:244px;height:70px;padding:0px;"/>',$this->common['adWrapSidebar']);
		}			
		// display widgets
		if ($p=='home') {
			require_once(PATH_CORE.'/classes/widgets.class.php');
			$wt=new FeaturedWidgetsTable($this->db);
			$code.=$wt->lookupWidget('homeSidebar');
		} 
		// using session and page, it builds a side panel
		switch ($p) {
			case 'otherProfile':
			default:			
				if ($this->session->isMember) {
					$code.=$this->fetchProfileSummaryPanelForHomePage($this->session->fbId, true /*testing*/, true);					
				} else {
					$code.=$this->fetchTeamIntro();
				}				
			break;
			case 'myProfile':
				 // do nothing at top			
			break;
			case 'team':
				if ($this->session->isMember) {
					$code.=$this->fetchProfileSummaryPanelForHomePage($this->session->fbId, true, true);					
				}
				break;
			case 'appTab':			
					$code.=$this->fetchProfileSummaryPanelForHomePage($this->session->fbId, false,false);					
					$code.=$this->fetchTeamIntro();
			break;				
		}
 		// to do: rotate through various cached panels
		// e.g. Team Leaders, Featured Challenges, Eligible Rewards, Team in Action
		if ($p=='otherProfile' OR $p=='myProfile') {
			$cacheName='sideLeaders';
			if ($this->templateObj->checkCache($cacheName,30)) {
				// still current, get from cache
				$temp=$this->templateObj->fetchCache($cacheName);
			} else {
				$temp=$this->fetchLeaders('alltime','inside',5);
				$temp.=$this->fetchLeaders('weekly','inside',5);				
				$this->templateObj->cacheContent($cacheName,$temp);
			}
			$code.=$temp;
		} else {
			
		/*
		 * 
		 if ( is_callable(array($this, $panelOption[$x])) ) {
			call_user_func_array( array($this, 'page_' . $this->default_page), NULL );				

		 */
		// to do: loop thru, pick a random number
		// pick a panel that hasn't been added to panelspicked before
		// add it based on the random number
		// continue until you have $numberItems of panels
			$panelOption=array(3=>'fetchChallenges',2=>'fetchRewardsRandom',1=>'fetchPopularStories',4=>'fetchLeaders');
			$panelCacheName=array(1=>'sidePopular',3=>'sideChallenges',2=>'sideRewards',4=>'sideLeaders');
			$panelsPicked=array();
			$x=1;
			while ($x<5) {
				$cacheName=$panelCacheName[$x];
				if ($x==2 and $this->session->isMember) $cacheName.='_member'; // for rewards panel with join button
				if ($this->templateObj->checkCache($cacheName,30)) {
					// still current, get from cache
					$temp=$this->templateObj->fetchCache($cacheName);
				} else {
					$temp=call_user_func_array(array($this,$panelOption[$x]),NULL);
					$this->templateObj->cacheContent($cacheName,$temp);
				}
				// only display rewards for anonymous viewer or team viewer, x=2 is a hack for array placement in panelCacheName				
				if (!($x==2 AND (!$this->session->isLoaded OR $this->session->u->eligibility<>'team')))
					$code.=$temp;
				$x+=1;			
			}
		}
		return $code;
	}

	function fetchTeamIntro() {
		// <!--"teamIcon" is the intro paragraph with polar-bear character background image -->
		$code.='<div id="teamPanel"><div id="teamIcon">';
		$code.=	$this->commonTeam['TeamIntro'] .
          '<p><a '.(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').' href="?p=signup'.(isset($_GET['referid'])?'&referid='.$_GET['referid']:'').'" class="btn_1 btn_1_lg">'.$this->commonTeam['JoinTheTeam'].'</a></p>
              <div class="pointsTable">
                  <table cellspacing="0">
                    <tbody>';
		
				require_once(PATH_CORE.'/classes/challenges.class.php');
		 		
		 		$ct = new ChallengeTable($this->db);
		 		$challenges=$ct->getTitlesAndPointsByShortName(array('postStory','invite','shareStory',
		 												'friendSignup','vote','comment','blog','addAppTab' ));
		 			
		            
		          
		          $code .= '<tr>'.$this->buildLegendEntry('postStory',$challenges['postStory']).'</tr>'.
		          			'<tr>'.$this->buildLegendEntry('stories',$challenges['vote']).'</tr>'.
		          			'<tr>'.$this->buildLegendEntry('stories',$challenges['shareStory']).'</tr>'.
		          			'<tr>'.$this->buildLegendEntry('invite',$challenges['invite']).'</tr>';
		          			
		          		
        $code .=   '</tbody>
                  </table>
        	</div><!--end "pointsTable"-->		';
		$code.='</div><!--end "teamIcon"--></div><!--end "teamPanel"-->';
		return $code;
	}

	
	function fetchProfileSummaryPanelForHomePage($fbId=0, $selfviewing=false, $ajaxEnabled=true )
	{
		// cache each permutation for each user
		$cacheName='prosum_'.($selfviewing?'s':'n').($ajaxEnabled?'a':'n').'_'.$fbId;
		if ($this->templateObj->checkCache($cacheName,10)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {
			 if ($selfviewing)
			{
				$user = $this->session->u;
				$userinfo = $this->session->ui;
			} else
			{
				$user = '';
				$userinfo='';
				require_once(PATH_CORE .'/classes/user.class.php');
				UserInfoTable::loadUserFromFbId($this->db, $fbId, &$user, &$userinfo);
			}
		
			$code = '
			<div id="profileSummary" class="panel_2 clearfix">
				<div class="panelBar clearfix">
					<h2>My '.SITE_TEAM_TITLE.' Summary</h2><div class="bar_link">'. (($selfviewing) ? template::buildLocalProfileLink('Edit', $userinfo->fbId) : '').'</div>
				</div><!__end "panelBar"__>'
	
			.	'<div class="panel_block">'
				. '<div class="thumb">
						'.template::buildLinkedProfilePic($userinfo->fbId, 'size="square"') //'<fb:profile_pic uid="'.$userinfo->fbId.'" linked="false" width="180px" />
					.'</div>'
			
		
					.'<div class="storyBlockWrap">'
					
						.'<h2>'.template::buildLocalProfileLink($user->name, $userinfo->fbId).'</h2>'
		
						//.'<h1><fb:name ifcantsee="Anonymous" uid="'.$userinfo->fbId.'" useyou="false" capitalize="true" firstnameonly="false" linked="false"/></h1>'
						."<h3>{$userinfo->city}, {$userinfo->state}</h3>"
			
								
					.'</div>' //<div class="storyBlockWrap">'
				
						//.$this->fetchProfileSummaryPanelHeaderForProfilePage($user, $userinfo, $selfviewing, $ajaxEnabled)
						.$this->fetchProfileSummaryPanelBlock($user,$userinfo,$selfviewing,$ajaxEnabled)
					//.'</div> <!-- end storyBlockWrap-->
				
				.'</div><!__end "panel_block"__>'
			.'</div><!__end "panel_1"__>';
			$this->templateObj->cacheContent($cacheName,$code);
		}	
		return $code;
		   
	}

	function fetchProfileSummaryPanelForProfilePage($fbId=0, $selfviewing=false, $ajaxEnabled=true )
	{
		if ($selfviewing)
		{
			$user = $this->session->u;
			$userinfo = $this->session->ui;
		} else
		{
			$user = '';
			$userinfo='';
			require_once(PATH_CORE .'/classes/user.class.php');
			UserInfoTable::loadUserFromFbId($this->db, $fbId, &$user, &$userinfo);
		}
	
		
		
		$code .= '
			<div class="panel_block">'
			. '<div class="thumb">
					'.template::buildLinkedProfilePic($userinfo->fbId, 'size="normal" width="180px"') //'<fb:profile_pic uid="'.$userinfo->fbId.'" linked="false" width="180px" />
				.'</div>'
		
	
				.'<div class="storyBlockWrap">'
					.'<h1><fb:name ifcantsee="Anonymous" uid="'.$userinfo->fbId.'" useyou="false" capitalize="true" firstnameonly="false" linked="false"/></h1>'
					."<h3>{$userinfo->city}, {$userinfo->state}</h3>"
		
					//.$this->fetchProfileSummaryPanelHeaderForProfilePage($user, $userinfo, $selfviewing, $ajaxEnabled)
					.$this->fetchProfileSummaryPanelBlock($user,$userinfo,$selfviewing,$ajaxEnabled,true)
				.'</div> <!-- end storyBlockWrap-->
			
			</div><!__end "panel_block"__>
			';
		
		return $code;
		   
	}
	
	
	function fetchProfileSummaryPanelHeaderForHomePage($user, $userinfo, $selfviewing=false, $ajaxEnabled=true) 
	{		
		
		$code.=template::buildLinkedProfilePic($userinfo->fbId, 67); //'<a href="'.URL_CANVAS.'?p=profile&memberid='.$userinfo->fbId.'"><fb:profile-pic  uid="'.$userinfo->fbId.'" linked="false" /></a> ';
		
		$code.=template::buildLocalProfileLink($user->name, $userinfo->fbId);
		//$code.='<a href="'.URL_CANVAS.'?p=profile&memberid='.$userinfo->fbId.'">'. //<fb:name capitalize="true" firstnameonly="false" uid="'.$fbId.'" linked="false" /></a>';
			//		 $user->name. '</a>';
		//$code .='</p>';
		$code .= "<h3>{$userinfo->city}, {$userinfo->state}</h3>";
		//$code .='<p>';
		if ($selfviewing) $code.=template::buildLocalProfileLink('Edit my profile', $userinfo->fbId); //'<a href="'.URL_CANVAS.'?p=profile&memberid='.$fbId.'">Edit my profile</a> ';		
		//$code.='</p>';
		
		return $code;								  
	}
	

	function fetchProfileSummaryPanelHeaderForProfilePage($user, $userinfo, $selfviewing=false, $ajaxEnabled=true) 
	{
				
		return $code;
										  
	}
	

	function renderUserLevel($userLevel)
	{
		$code = "<span class=\"userLevel{$this->nameLevels[$userLevel]}\">
					{$userLevel}</span>";
		return $code;	
	}
	
	function fetchProfileSummaryPanelBlock($user, $userinfo, $selfviewing, $ajaxEnabled=true, $forProfilePage=false) 
	{
		
		$this->setupLibraries(); // needed to pull in templates for userlevel styles
		$inTeam = true; // hack, TODO: remove this and use a smart switcher
		
		$numfriends = count(explode(',',$userinfo->memberFriends));		
		
		//$code .='<table border="0">';
		
		$code .= $this->profileSummaryRow("<span class=\"pointValue\">{$user->cachedPointTotal} <span class=\"pts\">points</span></span>
			&nbsp;".($forProfilePage ?  $this->renderUserLevel($user->userLevel) : ''), 
			/*REDEEM: 'Redeem for rewards'*/'', /*REDEEM: ($selfviewing && $user->eligibility=='team') ? 'rewards' : ''*/'', $ajaxEnabled, $inTeam);	
		if ($forProfilePage) 
		{
			$code .= $this->profileSummaryRow("<span class=\"pointValue\">{$user->cachedPointsEarnedThisWeek} <span class=\"pts\">this week</span></span>",
						'', $ajaxEnabled, $inTeam);	
			$code .= $this->profileSummaryRow("<span class=\"pointValue\">{$user->cachedPointsEarnedLastWeek} <span class=\"pts\">last week</span></span>",
						'', $ajaxEnabled, $inTeam);	
		}	
			
		$code .= $this->profileSummaryRow("{$numfriends} friends", 
			//'', '', $ajaxEnabled, $inTeam);	
		//$code .= $this->profileSummaryRow("{$userinfo->cachedFriendsInvited} invites", 
			'Invite more friends', $selfviewing ? 'invite' : '', $ajaxEnabled, $inTeam);	
		$code .= $this->profileSummaryRow("{$user->cachedStoriesPosted} stories", 
			'Post a story', $selfviewing ? 'postStory' : '', $ajaxEnabled, $inTeam);	
		$code .= $this->profileSummaryRow("{$user->cachedCommentsPosted} comments", 
			'Comment on stories', $selfviewing ? 'stories' : '', $ajaxEnabled, $inTeam);	
		
		if (ENABLE_ACTION_CHALLENGES)
			$code .= $this->profileSummaryRow("{$userinfo->cachedChallengesCompleted} challenges", 
				'Complete a challenge', $selfviewing ? 'challenges' : '', $ajaxEnabled, $inTeam);	
		// link to Facebook profile 
		if (!$selfviewing AND !$this->isAppTab) {
			$code .='<tr><td><a onclick="quickLog(\'extLink\',\'fbProfile\','.$user->userid.',\'http://www.facebook.com/profile.php?id='.$userinfo->fbId.'\');" target="_blank" href="http://www.facebook.com/profile.php?id='.$userinfo->fbId.'">Visit <fb:name ifcantsee="Anonymous" uid="'.$userinfo->fbId.'" possessive="true" firstnameonly="true" capitalize="true" linked="false" /> Facebook profile</a></td></tr>';
			$code.='<fb:if-is-friends-with-viewer uid="'.$userinfo->fbId.'"><tr><td><a onclick="quickLog(\'extLink\',\'fbEmailMe\','.$user->userid.',\'http://www.facebook.com/inbox/?compose&id='.$userinfo->fbId.'\');" target="_blank" href="http://www.facebook.com/inbox/?compose&id='.$userinfo->fbId.'">Email <fb:name ifcantsee="Anonymous" uid="'.$userinfo->fbId.'" firstnameonly="true" capitalize="true" linked="false" /></a></td></tr></fb:if-is-friends-with-viewer>';					
		}
  	
		//$code.='</table>';
		$code = '<div class="pointsTable_profile">
                  <table cellspacing="0">
                    <tbody>
                    '.$code.'
                     
                    </tbody>
                  </table>
        </div><!__end "pointsTable_profile"__>';
		
					
		return $code;
	}


	function profileSummaryRow($rowcontent, $linkText, $linkTarget, $ajaxEnabled, $inTeam=false)
	{
		$code .= "<tr>";
		$code .="<td class=\"bold\">$rowcontent</td>";
		if ($linkTarget <> '') // i.e. include link target when you want the link to appear
		{
			$code .= "<td>". $this->buildLink($linkTarget, $linkText, $ajaxEnabled, $inTeam) . "</td>";		
		} else 
			$code .= "<td></td>"; // for consistent rendering
		$code .= "</tr>";
		return $code;
	}
	
	function buildLink($linkTarget, $linkText, $ajaxEnabled, $inTeam=false, $class='')
	{
		if (!ENABLE_ACTION_REWARDS && $linkTarget == 'rewards') return $linkText; 
		if (!ENABLE_ACTION_CHALLENGES && $linkTarget == 'challenges') return $linkText; 
		if (!($linkTarget<>'')) return $linkText;
				
		$onclick = '';
		if ($ajaxEnabled)
		{
			
			switch ($linkTarget)
			{
				default:
				case 'stories':
				case 'postStory':
				case 'invite': 
					$method='switchPage';
					break;
				case 'rewards': 
				case 'challenges': 
				case 'leaders':
				 	if ($inTeam) $method='setTeamTab';
				 	else $method = 'switchPage';
				 	break;
				 
			}
			$onclick = "onclick=\"$method('$linkTarget');return false;\"";
	
		}		
		$code .= "<a class=\"$class\" href=\"".URL_CANVAS."/?p=$linkTarget\" $onclick>$linkText</a>";
		return $code;	
	}
	
	function fetchChallenges($mode='random', $limit = 3) 
	{
		if (!ENABLE_ACTION_CHALLENGES) return '';
		$modes = array('featured', 'popular', 'automatic', 'latest');
		$titles = array('Featured Challenges', 'Popular Challenges', 'Site Challenges', 'Latest Challenges');
		if ($mode=='random') 
		{
			$x=rand(0,3);
			$mode = $modes[$x];
			$title = $titles[$x];
		}
		
		
		require_once(PATH_CORE. '/classes/challenges.class.php');
		$challenges = new challenges($this->db);		
	
		
		switch ($mode) 
		{
			case 'featured':
				$code .= $challenges->fetchChallengePanelList('pointValue', $limit, "WHERE status='enabled' AND isFeatured=1",true);	
				
			break;
			case 'popular':
				$code .= $challenges->fetchChallengePanelList('completions', $limit, "WHERE status='enabled' AND type='submission'",true);	
				
			break;
			case 'automatic':
				$code .= $challenges->fetchChallengePanelList('pointValue', $limit, "WHERE status='enabled' AND type='automatic'",true);	
				
			break;
			case 'latest':
				$code .= $challenges->fetchChallengePanelList('dateStart', $limit, "WHERE status='enabled' ",true);	
				
			break;
		
		}		

		$inTeam = false;
		
		$code = '<div class="list_challenges">'. $code . '</div>';	
				
		$code ='<div class="panelBar clearfix">
	            <h2>'.$title.'</h2>
	            <div class="bar_link">'.$this->buildLink('challenges','See all', true, $inTeam).'</div>
	        </div>' . $code;
		
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		
		return $code;	
	}
	
	function fetchPopularStories($mode='random',$limit=5,$includeHeaderBar=true) {
		if ($mode=='random') {
			$x=rand(0,2);
			switch ($x) {
				default:
					$mode='rated';
				break;
				case 1:
					$mode='discussed';
				break;
				case 2:
					$mode='read';
				break;
			}
		}
		switch ($mode) {
			default: // rated
				$title = 'Top Rated Stories';
			break;
			case 'read':
				$title = 'Most Read Stories';
			break;	
			case 'discussed':		
				$title = 'Most Discussed Stories';
			break;
		}
		$code .= $this->fetchPopularList($mode, $limit);
//		$code = '<div class="list_rewards">'. $code . '</div>'; // minor hack				
		$code ='<div class="panelBar clearfix">
	            <h2>'.$title.'</h2>'.
//	            '<div class="bar_link"><a href="?p=leaders&o='.$mode.'" onclick="switchPage(\'leaders\',\''.$mode.'\');return false;">See all</a></div>'
	        		'</div><br />' . $code;		
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		return $code;	
	}	

	function fetchPopularList($mode='rated',$limit=5)
	{
		$this->setupLibraries();
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'read');                			
		$currentPage=1; $rowsPerPage= $limit;
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		if (defined('STORY_POPULAR_INTERVAL')) $popularityInterval=STORY_POPULAR_INTERVAL; else $popularityInterval=5;
		switch($mode)
		{
			case 'read':
			$storyList=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS count(id) AS pointTotal,Content.title,Content.siteContentId FROM Log LEFT JOIN Content ON Content.siteContentId=Log.itemid WHERE action = 'readStory' AND t>DATE_SUB(NOW(), INTERVAL $popularityInterval DAY) GROUP BY itemid ORDER BY pointTotal DESC LIMIT $startRow,".$rowsPerPage.";");
			break;
			case 'rated': 
			$storyList=$this->templateObj->db->query(
				"SELECT SQL_CALC_FOUND_ROWS 
						score
						AS pointTotal,	
						title,siteContentId	
						FROM Content 
						WHERE date>DATE_SUB(NOW(), INTERVAL $popularityInterval DAY)						
					ORDER BY pointTotal DESC LIMIT $startRow,".$rowsPerPage.";"); 
			break;
			case 'discussed':	
			$storyList=$this->templateObj->db->query(
				"SELECT SQL_CALC_FOUND_ROWS 
						numComments
						AS pointTotal,	
						title,siteContentId	
						FROM Content 
						WHERE date>DATE_SUB(NOW(), INTERVAL $popularityInterval DAY)						
					ORDER BY pointTotal DESC LIMIT $startRow,".$rowsPerPage.";"); 
			break; 
		}
		
		if ($this->templateObj->db->countQ($storyList)>0) {
			$rowTotal=$this->templateObj->db->countFoundRows();
			//$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$rowsPerPage,'?&p=rewards&sort='.$sort.'&currentPage='); // later put back page->rowsPerPage
			//$this->templateObj->db->setTemplateCallback('pointTotal',array($this, 'checkPointTotal') ,'pointTotal');
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['otherStoryList'],$this->templateObj->templates['otherStoryItem']);           
		} else {
			$code.='There are no stories measured yet.';
		}			
		// if ($paging) $code.=$pagingHTML;
		return $code;		
	}	
	
	function fetchLeaders($mode='random',$filter='inside',$limit=5,$includeHeaderBar=true) {
		if ($mode=='random') {
			$x=rand(0,1);
			switch ($x) {
				case 0:
					$mode='alltime';
				break;
				case 1:
					$mode='weekly';
				break;
			}
		}
		switch ($mode) {
			default:
				$title = $this->commonTeam['WeeklyTeamLeadersPanelTitle'];
			break;
			case 'alltime':
				$title = $this->commonTeam['AllTimeTeamLeadersPanelTitle'];
			break;			
		}
		//$code='<div class="actionSidePanel">'.$code.'<!-- end leaders --></div>';
		$code .= $this->fetchLeadersList($mode,$filter, $limit);
		$code = '<div class="list_rewards">'. $code . '</div>'; // minor hack
		if (!$includeHeaderBar) return $code;	// jr - used by pageLeaders
				
		$code ='<div class="panelBar clearfix">
	            <h2>'.$title.'</h2>'.
	            '<div class="bar_link"><a href="?p=leaders&o='.$mode.'" onclick="switchPage(\'leaders\',\''.$mode.'\');return false;">See all</a></div>'
	        .		'</div>' . $code;
		
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		
		
		
		return $code;	
	}	
	
	function fetchLeadersList($range='weekly',$restriction='inside', $limit=3)
	{
		$this->setupLibraries();
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'teamactivity');                	
		switch($range)
		{
			default:
			case 'alltime': $whereStr= 'cachedPointsEarned'; break;
			case 'weekly':	$whereStr= 'cachedPointsEarnedThisWeek'; break; 
		}
		switch($restriction)
		{
			default:
			case 'inside':
				$whereRestriction= "eligibility='team' AND ";
			break;
			case 'none':
			case 'outside':	$whereRestriction= ''; break;
			// to do - case 'friends':, need session member friends loaded via ajax.php 			
		}
		
		
		//$this->templateObj->db->log('restrict by'.$restriction);
		//$this->db->setTemplateCallback('linkedThumbnail',array($this, 'buildVideos') ,'completedid');
		//$this->db->setTemplateCallback('linked',array($this, 'buildPhotos') ,'completedid');
	
		
		
		$currentPage=1; $rowsPerPage= $limit;
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		/* old query (SELECT SUM(pointsAwarded) 
							FROM ChallengesCompleted 
							WHERE $whereTime ChallengesCompleted.userid=User.userid ) */
		//$this->templateObj->db->debug=true;				
		$prizeList=$this->templateObj->db->query(
			"SELECT SQL_CALC_FOUND_ROWS 
					$whereStr
					AS pointTotal,	
					name, city, state,	
					fbId	
					FROM User,UserInfo 
					WHERE $whereRestriction 
						eligibility<>'ineligible' 
						AND User.userid=UserInfo.userid AND $whereStr>0
						AND User.isBlocked=0
				
				ORDER BY pointTotal DESC LIMIT $startRow,".$rowsPerPage.";"); // $this->page->rowsPerPage
		//				$this->templateObj->db->debug=false;
		//$code.='<div>';
		// to do - later we'll move these template defs
		if ($this->templateObj->db->countQ($prizeList)>0) {
			$rowTotal=$this->templateObj->db->countFoundRows();
			//$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$rowsPerPage,'?&p=rewards&sort='.$sort.'&currentPage='); // later put back page->rowsPerPage
			$this->templateObj->db->setTemplateCallback('pointTotal',array($this, 'checkPointTotal') ,'pointTotal');			
			$code.=$this->templateObj->mergeTemplate(
				$this->templateObj->templates['leaderList'],$this->templateObj->templates['leaderItem']);           
		} else {
			$code.='There are no leaders designated yet.';
		}			
		//$code.='</div>';
		// jr - not sure if we'll need paging
		// if ($paging) $code.=$pagingHTML;
		return $code;
	
		
	}
	
	function checkPointTotal($pointTotal=0) {
		if ($pointTotal=='' OR is_null($pointTotal)) return 0; else return $pointTotal; 
	}
	
	function fetchRewardsRandom($inTeam = false,$limit=3)
	{
		if (!ENABLE_ACTION_REWARDS) return '';
		
	    $code .='  <div class="panelBar clearfix">
            <h2>'.$this->commonTeam['RewardsPanelTitle'].'</h2>
            <div class="bar_link">
            '.$this->buildLink('rewards','See all', true, $inTeam).'</div>
        </div>';
		
		require_once(PATH_CORE. '/classes/prizes.class.php');
		$rewards = new rewards($this->db);		
		
		$prizeTable = new PrizeTable($this->db);
		$prize = $prizeTable->getRowObject();
	
		$code .= '<div class="list_rewards clearfix">';

		if ($prize->loadWhere('isGrand=1'))
		{
		
			$code .= '
					<div class="top_reward clearfix">
			            <div class="thumb">'.template::buildLinkedRewardPic($prize->id, $prize->thumbnail,90).'</div>
							<div class="storyBlockWrap">
			               	  <p class="storyHead"><span class="pointValue">One Grand Prize</span><br>'
			                  . template::buildRewardLink($prize->title, $prize->id) .					
								'</p>
			                    <p>'. $this->templateObj->cleanString($prize->description, 130). template::buildRewardLink('&hellip;&nbsp;more', $prize->id) .'</p>
							</div>
			        </div>';
		
		}
		
		$code .= $rewards->fetchRewardsPanelList('RAND()', $limit, 'WHERE isGrand=0',$this->session->u->eligibility);	
		
		$code .= '</div>';
		
		if (!$this->session->u->isMember)
			$code .='<p>' .$this->buildLink('signup', $this->commonTeam['JoinTheTeam'], false, $inTeam, 'btn_1') . '</p>';
		
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		return $code;
		
	}

	
	function fetchRewards($mode='random',$limit=3)
	{
		if (!ENABLE_ACTION_REWARDS) return '';
		
		$modes = array('latest', 'grand', 'thisWeek', 'featured');
		$titles = array('Latest Rewards', 'Grand Prizes', 'This Week\'s Rewards', 'Featured Rewards');
		
		if ($mode=='random') 
		{
			$x=rand(0,3);
			$mode = $modes[$x];
			$title = $titles[$x];
		}
		
		require_once(PATH_CORE. '/classes/prizes.class.php');
		$rewards = new rewards($this->db);		
	
		switch ($mode) 
		{
			case 'latest':				
				$code .= $rewards->fetchRewardsPanelList('dateStart', 1, /*false,*/ $this->session->u->eligibility);							
			break;
			case 'grand':				
				$code .= $rewards->fetchRewardsPanelList('pointCost', 1, /*false,*/ 'WHERE isGrand=1', $this->session->u->eligibility);	
			break;
			case 'thisWeek':
				$code .= $rewards->fetchRewardsPanelList('pointCost', 1, /*false,*/ 'WHERE isWeekly=1', $this->session->u->eligibility);			
			break;
			default:
			case 'featured':
				$code .= $rewards->fetchRewardsPanelList('pointCost', 3, /*false,*/ 'WHERE isFeatured=1', $this->session->u->eligibility);	
			break;
		}	

		$code = '<div class="list_rewards">'. $code . '</div>';	
				
		$code ='<div class="panelBar clearfix">
	            <h2>'.$title.'</h2>
	            <div class="bar_link">'.$this->buildLink('rewards','See all', true, $inTeam).'</div>
	        </div>' . $code;
		
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
	
		return $code;
	}

	
	// TODO: move teamFriends into here
/*	
	<div class="panel_1">
	<div class="panelBar clearfix">
		<h2><a href="#">11 Friends</a> on the Action Team</h2>
	</div><!__end "panelBar"__>

	<div class="panel_block">
    	<div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
        <div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
        <div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
        <div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
		<div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
        <div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
        <div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
        <div class="friend" style="float: left; display: inline;"><a href="'.URL_CANVAS.'?p=profile&memberid=1008723516" onClick="return switchPage('profile', '', 1008723516);"><fb:profile_pic uid="1008723516" linked="false" size="square" /></a></div>
	</div><!__end "panel_block"__>
</div><!__end "panel_1"__>          
	*/
	
	function fetchRewardsForProfileBox(){
		$code='';

		return $code;
	
	}
	function fetchProfileSummaryForProfileBox($fbId=0,$canvasLink=URL_CANVAS)
	{
		$user = '';
		$userinfo='';
		require_once(PATH_CORE .'/classes/user.class.php');
		UserInfoTable::loadUserFromFbId($this->db, $fbId, &$user, &$userinfo);
		$code='';
		// to do - strip this down to essential css 
		//$code.='<style type="text/css">'.htmlentities(file_get_contents(PATH_FACEBOOK_STYLES.'/default.css', true)).'</style>';
		$css=htmlentities(file_get_contents(PATH_FACEBOOK_STYLES.'/default.css', true));
		$css=preg_replace('/\s+-(moz|webkit).*/', '', $css);
		$css=str_replace('\"',"'",$css);
		$code='<style type="text/css">'.$css.'</style>';
		$code.='<div id="profileBox">';
		$code.='<div>';
		$code.= str_replace("{canvasLink}",$canvasLink, $this->commonTeam['ProfileBoxIntro']);	
		if ($fbId!=0)
		{
			$this->setupLibraries();
			$code.='<h2><fb:name uid="'.$fbId.'" capitalize="true" linked="true" useyou="false" /></h2>';
			$pointsRow = $this->profileSummaryRow("<span class=\"pointValue\">{$user->cachedPointTotal} <span class=\"pts\">points</span></span>
			&nbsp;".($showUserLevels?  $this->renderUserLevel($user->userLevel) : ''), 
			/*'Redeem for rewards'*/'', /*'rewards'*/'', true, true);	
			
			$code.= '<div class="pointsTable_profile">
                  <table cellspacing="0">
                    <tbody>
                    '.$pointsRow.'        
                    </tbody>
                  </table>
        			</div><!__end "pointsTable_profile"__>';

		}		
		$code.=str_replace("{canvasLink}",$canvasLink, $this->commonTeam['ProfileBoxIntroJoinButton']);
		$code.='</div><!--end "box"-->';
		//dev for rick
		if ($fbId==756923320)
			$code.=date("m-d H:i:s");			
		$code.='</div><!--end profileBox-->';
		return $code;	
	}
	
	
}	
?>