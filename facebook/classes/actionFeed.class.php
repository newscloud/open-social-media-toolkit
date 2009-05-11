<?php

// provides a common locus for log-generated feeds used in various places, including team and profile pages

class actionFeed
{
	var $db;
	var $emptyLogMessage = 'There are no actions to display yet!';
	var $showOnlyChallengeBlog = false;
	function __construct(&$db=null)
	{
		if (is_null($db)) 
		{ 
			require_once(PATH_CORE .'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;		
			
		$this->isAppTab = $isAppTab;
		$this->setupLibraries();
	}
	
	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
	}
			// builds outside of team feed widget, filtered by a specific userid or 0 for everyone

	
	

	/*
	 * Design HTML for the action feed
	 *  
	 <div id="ajaxFeed" class="panel_1">
	<div class="panelBar clearfix">
		<h2>My Action Feed</h2>
		<div class="bar_link"><a href="#">Post a story</a></div>
	</div><!__end "panelBar"__>
    <div class="subFilter">Filter by:
        <input type="hidden" fbcontext="35ae24e89811" value="all" id="app18743077155_filter"/>
        <input type="hidden" fbcontext="35ae24e89811" value="63" id="app18743077155_filter_userid"/>
        <a onClick="" href="#" class="feedFilterButton selected">All Actions</a>
		 <a onClick=" href="#" style="" class="feedFilterButton">Stories</a> 
		<a onClick="(new Image()).src = '/ajax/ct.php?app_id=18743077155&amp;action_type=3&amp;post_form_id=c824ac961e4bab6aeed9d05eb633ba5f&amp;position=3&amp;' + Math.random();fbjs_sandbox.instances.a18743077155.bootstrap();return fbjs_dom.eventHandler.call([fbjs_dom.get_instance(this,18743077155),function(a18743077155_event) {a18743077155_setFeedFilter('challenges'); return false;},18743077155],new fbjs_event(event));return true;" href="#" style="" class="feedFilterButton">Challenges</a> <a onClick="(new Image()).src = '/ajax/ct.php?app_id=18743077155&amp;action_type=3&amp;post_form_id=c824ac961e4bab6aeed9d05eb633ba5f&amp;position=3&amp;' + Math.random();fbjs_sandbox.instances.a18743077155.bootstrap();return fbjs_dom.eventHandler.call([fbjs_dom.get_instance(this,18743077155),function(a18743077155_event) {a18743077155_setFeedFilter('rewards'); return false;},18743077155],new fbjs_event(event));return true;" href="#" style="" class="feedFilterButton">Rewards</a>
        </div><!__end "subtitle"__>

    <div class="list_stories">            
        <ul>
            <li class="panel_block">
                <div class="thumb"><a href="http://www.msnbc.msn.com/id/28529073/"><img src="http://www.newscloud.com/images/scaleImage.php?id=34608&x=185&y=130&fixed=x&crop" /></a>
                </div>
                <div class="storyBlockWrap">
                    <h3><span class="bold"><a href="'.URL_CANVAS.'?p=profile&memberid=1180126201" onClick="return switchPage('profile', '', 1180126201);"><fb:name ifcantsee="Anonymous" uid="1180126201" capitalize="true" firstnameonly="false" linked="false" /></a> posted a story</span> 1 hour ago</h3>
                    <p class="storyHead"><a href="http://www.msnbc.msn.com/id/28529073/">Researchers make car parts out of coconuts</a></p>
                    <p class="storyCaption">Researchers in Texas are making car parts out of coconuts.  A team at Baylor University has made trunk liners, floorboards and car <a href="#" class="more_link">&hellip;&nbsp;more</a></p>                
                </div><!__end "storyBlockWrap"__>
            </li>
            <li class="panel_block">
                <div class="thumb"><a href="http://www.msnbc.msn.com/id/28529073/"><img src="http://www.newscloud.com/images/scaleImage.php?id=34608&x=185&y=130&fixed=x&crop" /></a>
                </div>
                <div class="storyBlockWrap">
                    <h3><span class="bold"><a href="'.URL_CANVAS.'?p=profile&memberid=1180126201" onClick="return switchPage('profile', '', 1180126201);"><fb:name ifcantsee="Anonymous" uid="1180126201" capitalize="true" firstnameonly="false" linked="false" /></a> posted a video</span> 2 hours ago</h3>
                    <p class="storyHead"><a href="http://www.msnbc.msn.com/id/28529073/">Researchers make car parts out of coconuts</a></p>
                    <p class="storyCaption">Researchers in Texas are making car parts out of coconuts.  A team at Baylor University has made trunk liners, floorboards and car <a href="#" class="more_link">&hellip;&nbsp;more</a></p>                
                </div><!__end "storyBlockWrap"__>
            </li>
            <li class="panel_block">
                <div class="thumb"><a href="http://www.msnbc.msn.com/id/28529073/"><img src="http://www.newscloud.com/images/scaleImage.php?id=34608&x=185&y=130&fixed=x&crop" /></a>
                </div>
                <div class="storyBlockWrap">
                    <h3><span class="bold"><a href="'.URL_CANVAS.'?p=profile&memberid=1180126201" onClick="return switchPage('profile', '', 1180126201);"><fb:name ifcantsee="Anonymous" uid="1180126201" capitalize="true" firstnameonly="false" linked="false" /></a> commented on the story <a href="#">It's Time to Aim Low</a></span> on Feb 2, 2009</h3>
				<blockquote>
					<div class="quotes">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam dolor nunc, vehicula et, tristique sed, auctor et, massa.</p>
						<p> Nam at purus vitae diam commodo venenatis. Ut leo enim, vestibulum eget, rhoncus in, suscipit laoreet, magna. Aliquam diam. Nunc tempor lorem eget nisl.</p>
					</div>
				</blockquote>
                </div><!__end "storyBlockWrap"__>
            </li>
		</ul>
    </div><!__end "list_stories"__>

</div><!__end "ajaxFeed" "panel_1"__>
	 * 
	 * 
	 */
	
	function fetchFeed($filter='all', $currentPage=1,$filter_userid = 0,$filter_challengeid=0, $isAjax=false,$showScoreLog=false)
	{	
		if (!$isAjax) 
		{
			$code='<div id="navFilter">'; 
	        $code.=$this->fetchSubFilter($filter, 0, $showScoreLog);
	        $code.='<!-- end navFilter --></div>';			
		}
		
		// testing -- may break feed filtering
		$code .= '<input type="hidden" id="filter" value="'.$filter.'">';        		
		$code .= '<input type="hidden" id="filter_userid" value="'.$filter_userid.'">';        	
		$code .= '<input type="hidden" id="filter_challengeid" value="'.$filter_challengeid.'">';        	
				
		$code.='<div id="feedList">';
	
		if ($filter == 'scorelog')
			$code .= $this->fetchFeedScoreLog($filter_userid, $currentPage);
		else
			$code.=$this->fetchFeedPage($filter,$currentPage,$filter_userid,$filter_challengeid);
	
       // $code.=$this->setHiddenVariables('feed','fetchFeedPage');		
	
        $code.='<!-- end feedList --></div>';
        return $code;		
	}
	
   function fetchSubFilter($filter='all', $filter_userid, $showScoreLog=false) 
   {
   		$catlist = array(
			   		'all'		=>'All Actions', 
			   		'stories'	=>'Stories', 
					'comments'	=>'Comments',
			   		'challenges'=>'Challenges', 
			   		'rewards'	=>'Rewards',
   					);
   		$statelist = array(
   						'rewards'=>(ENABLE_ACTION_REWARDS ? '':'hidden'),
   						'challenges'=>(ENABLE_ACTION_CHALLENGES ? '':'hidden'),
   					);
  											
		if ($showScoreLog) $catlist['scorelog']= 'History';

        $code.='<div class="subFilter">Filter by:'; 
		//$code .= '<input type="hidden" id="filter" value="'.$filter.'">';        	
		//$code .= '<input type="hidden" id="filter_userid" value="'.$filter_userid.'">';        	
		foreach ($catlist as $key => $field) 
        {    	
        	
        	if ($key==$filter) $selected='selected';
        	else $selected = '';
        	$code .= '<span class="'.$statelist[$key].'"><a id="'.$key.'FeedFilter" class="feedFilterButton '.$selected.'"  
        				href="#" onClick="setFeedFilter(\''.$key.'\'); return false;">'.$field.'</a></span> &nbsp;&nbsp;';
        	
        	//<select name="sort" id="sort" onChange="refreshRewards();">';
            //$code.='<option value="'.$field.'" '.($sort==$field?'SELECTED':'').'>'.$catlist[$field].'</option>';
        }
        $code.='</div><br clear="all" />';

        return $code;       
    }
	
	
		
	function fetchFeedPage($filter_category='all',$currentPage = 1,$filter_userid = 0, $filter_challengeid=0)
	{
		require_once(PATH_CORE .'/classes/log.class.php');
		$lt = new LogTable($this->db);
		$action = $lt->getRowObject();
		
		//echo 'test:' . $filter_category;
		//$code .= 'test:' . $filter_category;
		
		
		switch($filter_category)
		{
			default:
			case 'all': $where = 
				" action='signup' ".
				"OR action='completedChallenge' ". 
				"OR action='redeemed' 
				OR action='comment'
				OR action='postStory'
				OR action='postBlog' OR action='wonPrize'
				OR action='chatStory' 
				OR action='friendSignup' ";
			break;
			case 'stories':
				$where =" action='postStory' OR action='postBlog'";
			break;
			case 'comments':
				$where =" action='comment' ";
			break;
			case 'challenges':
				$where =" action='completedChallenge' ";				
			break;
			case 'rewards':
				$where = " action='redeemed' OR action='wonPrize' ";
			break;
				
		}
		
		//$code .= 'filtered userid:' . $filter_userid;
		if ($filter_userid)
		{
			$where = "( $where ) AND userid1=$filter_userid ";	
		}
	
		if ($filter_challengeid)
		{
			$where = "( $where ) AND itemid IN (SELECT id FROM ChallengesCompleted WHERE challengeid=$filter_challengeid) ";
			// minor hack
			$this->showOnlyChallengeBlog = true; // hack: for now implied by filter_challengeid - keeps us from having to add extra parameters to hide the challenge headlines on the challenge blog paging
		}
		//$code .= $where;
		// no paging implemented here yet
		$rowsPerPage=20;
		// userid is passed in because there is no session when refreshed with Ajax
		
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		$actionIdList=$this->db->query( // TODO: sort by dateAwarded and dont show submissions
			"SELECT SQL_CALC_FOUND_ROWS	id FROM Log 
			WHERE $where  				
			ORDER BY dateCreated DESC LIMIT $startRow,".$rowsPerPage.";"); // $this->page->rowsPerPage
		
		$rowTotal=$this->templateObj->db->countFoundRows();
				
		if ($this->db->countQ($actionIdList)>0) 
		{
			while($data=$this->db->readQ($actionIdList))
			{	
				if ($action->load($data->id))
				{
					$actionitemcode = $this->buildActionItem($action); 
					if ($actionitemcode <>'') $code .= '<li id="actionFeedItem">'. $actionitemcode .'</li>'; // hack so empty <li>'s dont appear and mess things up
				}			
			}
			
			$code = // action list template
				'<div class="list_stories"><ul>'.$code.'</ul></div>';
		
			
		} else {
			$code.=$this->emptyLogMessage;
		}			
	

		$pagingHTML=$this->feedPaging($currentPage,$rowTotal,
							$rowsPerPage,''/*'?&p=team&filter='.$filter_category.'&currentPage='*/,
							'refreshFeed'); // later put back page->rowsPerPage			
		$code .=$pagingHTML;	
		
		return $code;
				
			
	}
	
	function fetchFeedChallengesSubmittedPage($filter_userid,$currentPage = 1 )
	{
		
		$rowsPerPage=10;
		// userid is passed in because there is no session when refreshed with Ajax
		
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		$actionIdList=$this->db->query( // TODO: sort by dateAwarded and dont show submissions
		"SELECT SQL_CALC_FOUND_ROWS	ChallengesCompleted.id,fbId,dateSubmitted FROM ChallengesCompleted,Challenges,UserInfo 
			WHERE ChallengesCompleted.status='submitted'
				AND Challenges.id=ChallengesCompleted.challengeid
				AND Challenges.type='submission'
				AND ChallengesCompleted.userid=UserInfo.userid 
				AND UserInfo.userid=$filter_userid				
			ORDER BY dateSubmitted DESC LIMIT $startRow,".$rowsPerPage.";"); // $this->page->rowsPerPage
		
		$rowTotal=$this->templateObj->db->countFoundRows();
				
		if ($this->db->countQ($actionIdList)>0) 
		{
			while($data=$this->db->readQ($actionIdList))
			{	
				//if ($action->load($data->id))
				//	$code .= '<li id="actionFeedItem">'. $this->buildActionItem($action) .'</li>';			
				$action->itemid=$data->id;
				$action->t = $data->dateSubmitted;
				
				$code .= '<li id="actionFeedItem">'. $this->fetchChallengeCompletedFeedItem($action,$data->fbId, true) .'</li>';			
			}
			
			$code = // action list template
				'<div class="list_stories"><ul>'.$code.'</ul></div>';
		
			
		} else {
			$code.=$this->emptyLogMessage;
		}			
	

		$pagingHTML=$this->feedPaging($currentPage,$rowTotal,
							$rowsPerPage,''/*'?&p=team&filter='.$filter_category.'&currentPage='*/,
							'refreshFeed'); // later put back page->rowsPerPage			
		$code .=$pagingHTML;	
		
		return $code;
				
		
	}
		
	function fetchFeedScoreLog($filter_userid,$currentPage = 1,$rowsPerPage=10 )
	{
		
		// userid is passed in because there is no session when refreshed with Ajax
		require_once(PATH_CORE .'/classes/log.class.php');
		
		$lt = new LogTable($this->db);
		$logaction = $lt->getRowObject();
		
		
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		$actionIdList=$this->db->query( // TODO: sort by dateAwarded and dont show submissions
			"SELECT SQL_CALC_FOUND_ROWS	
					ChallengesCompleted.id AS id,fbId,dateSubmitted,
					shortName, logid, pointsAwarded
			FROM ChallengesCompleted,UserInfo,Challenges 
			WHERE ChallengesCompleted.status='awarded' 
				AND ChallengesCompleted.userid=UserInfo.userid
				AND ChallengesCompleted.challengeid = Challenges.id 
				AND UserInfo.userid=$filter_userid				
			ORDER BY dateSubmitted DESC ". ($rowsPerPage ? "LIMIT $startRow,".$rowsPerPage.";" : ";")); // $this->page->rowsPerPage
		
		$rowTotal=$this->templateObj->db->countFoundRows();
				
		if ($this->db->countQ($actionIdList)>0) 
		{
			while($data=$this->db->readQ($actionIdList))
			{	

				/*$debugcomment .= "<div class='hidden'> logid=$data->logid, ccid=$data->id </div>";
				$code .= "<li id='actionFeedItem'>$debugcomment</li>"; // hack for now 
				*/
				$showCompletedChallengeEntry = true;
				if ($data->logid) // see if the CC has a log entry linked to it
				{
					if ($logaction->load($data->logid)) // load log entry
					{
						if ($logaction->action <> 'completedChallenge') // see if it was of a type that has custom display code, otherwise will be handled by regular CC display below
						{
							$actionitemcode = $this->buildActionItem($logaction); 
							if ($actionitemcode <>'') // custom code built, add point text and output
							{
								$showCompletedChallengeEntry = false; // suppress it if we can build a nicer display
								$pointText = '<div class="storyBlockWrap"><p class="storyCaption"><span class="pointValue"> for '. $data->pointsAwarded.'<span class="pts"> points </span></span></p></div>';
								$code .= '<li id="actionFeedItem">'. $actionitemcode . $pointText . '</li>';
							
							}
				
						}
					}
				}

				if ($showCompletedChallengeEntry)
				{
					$action->itemid=$data->id;
					$action->t = $data->dateSubmitted;
					
					$code .= '<li id="actionFeedItem">'. $this->fetchChallengeCompletedFeedItem($action,$data->fbId, true, true) .'</li>';
				}				
			}
			
			$code = // action list template
				'<div class="list_stories"><ul>'.$code.'</ul></div>';
		
			
		} else {
			$code.=$this->emptyLogMessage;
		}			
	

		$pagingHTML=$this->feedPaging($currentPage,$rowTotal,
							$rowsPerPage,''/*'?&p=team&filter='.$filter_category.'&currentPage='*/,
							'refreshFeed'); // later put back page->rowsPerPage			
		$code .=$pagingHTML;	
		
		return $code;
				
		
	}
	
	
	function setHiddenVariables($pageName='home',$pagingFunction='refreshPage') {
		$code.='<input type="hidden" id="pageName" value="'.$pageName.'">';
		$code.='<input type="hidden" id="pagingFunction" value="'.$pagingFunction.'">';
		return $code;
	}
	
	
	// copied from template.class.php and modified ONLY to use the jscriptFunction param to call the page refresh function 
	function feedPaging($pageCurrent=1,$rowTotal=0,$rowLimit=7,$link='',$jscriptFunction='',$ajaxOn=false,$nav=NULL) 
	{
		switch (MODULE_ACTIVE) {
			default:
				$hrefCode='href="javascript:void(0);"';
			break;
			case 'FACEBOOK':
				$hrefCode='href="#"';
			break;
		}
		// $link is the url that the page navigation will point to - this functions add the page offset as the suffix
		// e.g. $link ='/search/keyword/tag/sort/' ... pages will link to '/search/keyword/tag/sort/pagenumber/'
		// previous query must use SQL_CALC_FOUND_ROWS
		$pageTotal=ceil($rowTotal/$rowLimit);
		$nav->last=$pageTotal;
		$nav->current=$pageCurrent;
		$pageStart=($pageCurrent-4)>0 ? ($pageCurrent-4) : 1;
		$pageEnd=($pageCurrent+4)>$pageTotal ? $pageTotal : ($pageCurrent+4);
		$ellipsis='<span>...</span>';
		if ($rowTotal==0)
			return '';
		$text='<div class="pages">';
		// previous page
		if ($pageCurrent>1) {
			$text.='<a '.$hrefCode.' class="nextprev" onclick="return '.$jscriptFunction.'('.($pageCurrent-1).');">&#171; Previous</a>'; 
			$nav->previous=$pageCurrent-1;
		} else {
			$text.='<span class="nextprev">&#171; Previous</span>';
			$nav->previous=1;
		}
		// page 1 & 2
		if ($pageCurrent>5)
			$text.='<a '.$hrefCode.' onclick="return '.$jscriptFunction.'(1);">1</a><a href="javascript:void(0);" onclick="return '.$jscriptFunction.'(2);">2</a>'.$ellipsis; 
		// current nine pages
		for ($i=$pageStart;$i<=$pageEnd;$i++) {
			if ($i==$pageCurrent)
				$text.='<span class="current">'.$i.'</span>';
			else
				$text.='<a '.$hrefCode.' onclick="return '.$jscriptFunction.'('.$i.');" >'.$i.'</a>';
		}
		if (($pageTotal-$pageCurrent)>5)
			$text.=$ellipsis.'<a '.$hrefCode.' onclick="return '.$jscriptFunction.'('.($pageTotal-1).');">'.($pageTotal-1).'</a><a '.$hrefCode.' onclick=" return '.$jscriptFunction.'('.$pageTotal.');">'.$pageTotal.'</a>'; 
		// next page
		if ($pageCurrent<$pageTotal) {
			$text.='<a '.$hrefCode.' class="nextprev" onclick="return '.$jscriptFunction.'('.($pageCurrent+1).');">Next &#187;</a>'; 
			$nav->next=$pageCurrent+1;
		} else {
			$nav->next=$pageCurrent;
			$text.='<span class="nextprev">Next &#187;</span>';
		}
		$text.='</div>';
		return $text;
	}	
		
	function buildActionItem($action)
	{
		require_once(PATH_CORE .'/classes/user.class.php');
		require_once(PATH_CORE .'/classes/template.class.php');
		$uit = new UserInfoTable($this->db);
		$fbIds = $uit->getFbIdsForUsers(array($action->userid1));
		$fbId = $fbIds[0];

		if ($action->userid2)
		{	
			$fbIds2 = $uit->getFbIdsForUsers(array($action->userid2));
			$fbId2 = $fbIds2[0];
		}
		
		if (!$fbId)
		{
			// cant return anything or #&*$@! paging gets screwed
			//$code .= '<div class="hidden">No fbId found for userid '. $action->userid1 . '</div>';
			$this->db->log("Action Feed: No fbId found for userid $action->userid1 performing $action->action on $action->t");
			return ''; //$code;
		}
		
		$ago .= self::getElapsedString(strtotime($action->t));
		
	
		switch ($action->action)
		{	
			case 'completedChallenge':
				$code .= $this->fetchChallengeCompletedFeedItem($action,$fbId,false); // hack for now so the console can access it also
				//$ct = new ChallengeTable($this->db);
				break;
			case 'signup':
				
				/*$code .= template::buildLinkedProfilePic($fbIds[0], 'size="square"') .' '. template::buildLinkedProfileName($fbIds[0])
						.' joined the action team!';
				*/
				$code .= '<div class="profilePicLarger">'.template::buildLinkedProfilePic($fbIds[0], 'size="square"') .' '.
			                '</div>
			                <div class="storyBlockWrap">
			                    <h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).
			                    	' joined the '.SITE_TEAM_TITLE.'!</span> '.$ago.'</h3>
			      		</div><!__end "storyBlockWrap"__>';
					
						
						
				break;
				
			case 'friendSignup':
				

				$code .= '<div class="profilePicLarger">'.template::buildLinkedProfilePic($fbIds[0], 'size="square"') .' '.
			                '</div>
			                <div class="storyBlockWrap">
			                    <h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).
			                    	' got credit for inviting '.template::buildLinkedProfileName($fbId2).'!</span> '.$ago.'</h3>
			      		</div><!__end "storyBlockWrap"__>';
					
						
						
				break;
				
		
			case 'chatStory':
				

				require_once(PATH_CORE .'/classes/content.class.php');
				$contentTable = new ContentTable($this->db);	
				$content = $contentTable->getRowObject();				
				$contentid = $action->itemid; 

				// hack: since jeff put the fbId in itemid2 for chatStory
				$fbId2 = $action->userid2;
				
				if ($content->load($contentid)) // hack: would like it to be in 1
				{
					$code .= 
				               ' <div class="thumb">'.template::buildLinkedStoryImage($content->imageid, $contentid).
				                '</div>
				                <div class="storyBlockWrap">
				                	<div class="feed_poster">'.
				                		'<div class="avatar">'.template::buildLinkedProfilePic($fbIds[0], 'size="square"  with="30" height="30"') .'</div>'.
										'<div class="avatar">'.template::buildLinkedProfilePic($fbId2, 'size="square"  with="30" height="30"') .'</div>'.
				                    	'<h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).
				                    		' chatted with '.template::buildLinkedProfileName($fbId2).
											' about the story '.template::buildStoryLink($content->title, $contentid).
				                    	'</span> '.$ago.'</h3>
				                    </div>				                            
				                </div><!__end "storyBlockWrap"__>';								
				}
				
						
						
						
				break;
				
				
				
				
			//case 'acceptedInvite': // actually want to filter these?
				// yeah, dont show these
				
				//break; 	
			case 'redeemed':
				require_once (PATH_CORE .'/classes/prizes.class.php');
				$prizeTable = new PrizeTable($this->db);
				$prize = $prizeTable->getRowObject();
				if ($prize->load($action->itemid) && !$prize->isWeekly && !$prize->isGrand) // exclude weekly and grand prizes from this display
				{
					/*
					$indefarticle = template::getIndefiniteArticle($prize->title);
					$code .= template::buildLinkedProfilePic($fbIds[0], 'size="square"') .' '. template::buildLinkedProfileName($fbIds[0])
							." used {$prize->pointCost} points to get $indefarticle "
							. template::buildRewardLink($prize->title, $prize->id) .""
							. template::buildLinkedRewardPic($prize->id, $prize->thumbnail, $width=70);
					//$code .= ' '. self::getElapsedString(strtotime($action->t));
					*/
					$code .= '<div class="thumb">'.template::buildLinkedRewardPic($prize->id, $prize->thumbnail, $width=70).
				                '</div>				           
				                <div class="storyBlockWrap">
				                	<div class="feed_poster"><div class="avatar">'.template::buildLinkedProfilePic($fbIds[0], 'size="square" with="30" height="30"') .'</div>
				                    	<h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).' redeemed <span class="pointValue">'.$prize->pointCost.'<span class="pts"> points</span></span>.</span> '.$ago.'</h3>
			                    	</div>
				                    <p class="storyHead">'.template::buildRewardLink($prize->title, $prize->id) .' </p>
				                    <p class="storyCaption"></p>                
				                </div><!__end "storyBlockWrap"__>';
							
				} else
				{
					// debug:		
					//$code .= 'No prize found for id ' . $action->itemid;
				}
				break;
				
					
			case 'wonPrize': // implement
				require_once (PATH_CORE .'/classes/prizes.class.php');
				$prizeTable = new PrizeTable($this->db);
				$prize = $prizeTable->getRowObject();
				if ($prize->load($action->itemid))
				{
					if ($prize->isWeekly)
						$winText = 'won a Weekly Prize!';

					if ($prize->isGrand==1)
						$winText = 'won the Grand Prize and made the planet green with envy!';
						
					if ($prize->isGrand >1)
						$winText = 'won a Runner-up Prize!';
					
					$code .= '<div class="thumb">'.template::buildLinkedRewardPic($prize->id, $prize->thumbnail, $width=70).
		                '</div>
		                <div class="storyBlockWrap">
		                <div class="feed_poster"><div class="avatar">'.template::buildLinkedProfilePic($fbIds[0], 'size="square"  with="30" height="30"') .'</div>
		                    <h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).
		                    	' '.$winText.' '.$ago.'</h3>
		                    </div>
		                    <p class="storyHead">'.template::buildRewardLink($prize->title, $prize->id) .' </p>
		                    <p class="storyCaption"></p>                
		                </div><!__end "storyBlockWrap"__>';
							
				} else
				{
					// debug:		
					//$code .= 'No prize found for id ' . $action->itemid;
				}
				break;
				
				break;
			case 'publishStory':
				// TODO ?
				//return '';
				//break;
			case 'postBlog':		
			case 'postStory':

				require_once(PATH_CORE .'/classes/content.class.php');
				$contentTable = new ContentTable($this->db);	
				$content = $contentTable->getRowObject();				
				$contentid = $action->itemid; 
								
				if ($content->load($contentid)) // hack: would like it to be in 1
				{
					/*$code .= template::buildLinkedStoryImage($content->imageid, $contentid);
					$code .= template::buildLinkedProfilePic($fbIds[0], 'size="square"') .' '. template::buildLinkedProfileName($fbIds[0])
							.' posted a story';
					$code .= '<p>' . template::buildStoryLink($content->title, $contentid)
								.'</p>';
						*/		
					$code .= //'<li class="panel_block">
				               ' <div class="thumb">'.template::buildLinkedStoryImage($content->imageid, $contentid).
				                '</div>
				                <div class="storyBlockWrap">
				                	<div class="feed_poster"><div class="avatar">'.template::buildLinkedProfilePic($fbIds[0], 'size="square"  with="30" height="30"') .'</div>
				                    	<h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).' posted a story</span> '.$ago.'</h3>
				                    </div>
				                    <p class="storyHead">'.template::buildStoryLink($content->title, $contentid).' </p>
				                    <p class="storyCaption">'.$this->templateObj->ellipsis(strip_tags($content->caption, 200)) . 
							' '.template::buildStoryLink('...more', $contentid) .'</p>                
				                </div><!__end "storyBlockWrap"__>';
				            //'</li>'
												
								
				}
				break;
				
			case 'vote': // ignore
				require_once(PATH_CORE .'/classes/content.class.php');
				$contentTable = new ContentTable($this->db);	
				$content = $contentTable->getRowObject();
			
				$contentid = $action->itemid; // grrrr				
				
				if ($content->load($contentid)) // hack: would like it to be in 1
				{
					$code .=  ' <div class="thumb">'.template::buildLinkedStoryImage($content->imageid, $contentid).
				                '</div>
				                <div class="storyBlockWrap">
			                	<div class="feed_poster">'.
			                	//'<div class="avatar">'.template::buildLinkedProfilePic($fbIds[0], 'size="square" height="30" width="30"') .'</div>'.
				                    	'<h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).
				                    	' voted on the story '.template::buildStoryLink($content->title, $contentid).'</span> '.$ago.'</h3>
				                    </div>
				                </div><!__end "storyBlockWrap"__>
				            ';								
			
				}
				
				break;
				
			case 'comment':
				
				
				/*
				 * 
				 *  <li class="panel_block">
                <div class="thumb"><a href="http://www.msnbc.msn.com/id/28529073/"><img src="http://www.newscloud.com/images/scaleImage.php?id=34608&x=185&y=130&fixed=x&crop" /></a>
                </div>
                <div class="storyBlockWrap">
                    <h3><span class="bold"><a href="'.URL_CANVAS.'?p=profile&memberid=1180126201" onClick="return switchPage('profile', '', 1180126201);"><fb:name ifcantsee="Anonymous" uid="1180126201" capitalize="true" firstnameonly="false" linked="false" /></a> commented on the story <a href="#">It's Time to Aim Low</a></span> on Feb 2, 2009</h3>
				<blockquote>
					<div class="quotes">
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam dolor nunc, vehicula et, tristique sed, auctor et, massa.</p>
						<p> Nam at purus vitae diam commodo venenatis. Ut leo enim, vestibulum eget, rhoncus in, suscipit laoreet, magna. Aliquam diam. Nunc tempor lorem eget nisl.</p>
					</div>
				</blockquote>
                </div><!__end "storyBlockWrap"__>
            </li>
				 */
				// assume comment->siteContentId is what is stored...
				require_once(PATH_CORE .'/classes/content.class.php');
				require_once(PATH_CORE .'/classes/comments.class.php');
				require_once(PATH_CORE .'/classes/video.class.php');
				$contentTable = new ContentTable($this->db);
				$commentTable = new CommentTable($this->db);
				$videoTable = new VideoTable($this->db);
				
				$content = $contentTable->getRowObject();
				$comment = $commentTable->getRowObject();
				$video = $videoTable->getRowObject();
				
				$commentid = $action->itemid; // grr
				$contentid = $action->itemid2; // grrrr
				
				
				if ($comment->load($commentid) && $content->load($contentid)) // hack: would like it to be in 1
				{
					if ($comment->videoid && $video->load($comment->videoid))
					{
						$quoteContents =  '<div style="text-align:center;">'. videos::buildPlayerFromLink($video->embedCode, 160, 100) .'</div>';	
					}
					else 
					{
						$quoteContents =  '<p>'.$this->templateObj->ellipsis(strip_tags($comment->comments), 200) . 					
											' '.template::buildStoryLink('...more', $contentid) .'</p>';
					}
					
					
					$code .=  ' <div class="thumb">'.template::buildLinkedStoryImage($content->imageid, $contentid).
				                '</div>
				                <div class="storyBlockWrap">
			                	<div class="feed_poster"><div class="avatar">'.template::buildLinkedProfilePic($fbIds[0], 'size="square" height="30" width="30"') .'</div>
				                    	<h3><span class="bold">'. template::buildLinkedProfileName($fbIds[0]).
				                    	' commented on the story '.template::buildStoryLink($content->title, $contentid).'</span> '.$ago.'</h3>
				                    </div>
				                    <blockquote>
				                    	<div class="quotes">'.
				                    		$quoteContents.  
										'</div>
									</blockquote>               
				                </div><!__end "storyBlockWrap"__>
				            ';								
			
				}
				break;
			
			
		}
		
		return $code; 
	}
	
	
	function fetchChallengeCompletedFeedItem($action, $fbId, $returnerrors = false, $noFiltering = false)
	{
			
		$ago .= self::getElapsedString(strtotime($action->t));
		
		require_once (PATH_CORE .'/classes/template.class.php');
		require_once (PATH_CORE . '/classes/challenges.class.php');
		$ct = new ChallengeTable($this->db);
		// $this->db->setDebug(true); // NEVER TURN ON FOR LIVE SITE
		$completedTable = new ChallengeCompletedTable($this->db);
		$completed = $completedTable->getRowObject();
		if (!$action->itemid)
		{
			if ($returnerrors) $code = 'completedChallenge itemid empty:' . print_r($action, true);
			return;
		}
		if ($completed->load($action->itemid) /*&& $completed->status=='awarded' && $completed->pointsAwarded> 0*/)
		{
			$challenge = $ct->getRowObject();
			
			if ($challenge->load($completed->challengeid))
			{
				// hack to cleanup actionFeed of CCs that should be hidden or now have custom feed items
				$filterPastChallenges = array('levelIncrease', 'addBookmarkTool', 'friendSignup');
				if (!$noFiltering && array_search($challenge->shortName,$filterPastChallenges) !== false)
					return ''; // filtered
	
										
				$photocode = $this->buildPhotos($completed->id, &$pcount);
				if ($pcount > 1)
				{	$submittedText .=  'photos '; $submittedContent .= $photocode; }							
				else if ($pcount > 0)
				{	$submittedText .=  'a photo '; $submittedContent .= $photocode;		}
			
				$videocode = $this->buildVideos($completed->id, &$vcount);
				
				if ($pcount > 0 and $vcount>0) $submittedText .= ' and ';
				
				if ($vcount > 1)
				{	$submittedText .=  'videos '; $submittedContent .= $videocode; }							
				else if ($vcount > 0)
				{	$submittedText .=  'a video '; $submittedContent .= $videocode; }
				
				
				
				if ($completed->status == 'awarded') // redundant here because we are built from the log which only contains awarded completions
				{						
					/*$code .= template::buildLinkedProfilePic($fbIds[0], 'size="square"') .' '. template::buildLinkedProfileName($fbId)
							.' completed the challenge '. template::buildChallengeLink($challenge->title, $challenge->id) . 
							' for ' . $completed->pointsAwarded 
							. ' points and submitted ' . $submittedText;
						*/	
					if (!$this->showOnlyChallengeBlog)
					{
						$code .= '<div class="thumb">'.template::buildLinkedChallengePic($challenge->id, $challenge->thumbnail).
					                '</div>
					                <div class="storyBlockWrap">
					                	<div class="feed_poster"><div class="avatar">'.template::buildLinkedProfilePic($fbId, 'size="square"  with="30" height="30"') .'</div>
					                    	<h3><span class="bold">'. template::buildLinkedProfileName($fbId).' completed a challenge					                   
					            			</span> '.$ago.'</h3>
				            			</div>
					                    <p class="storyHead">'. template::buildChallengeLink($challenge->title, $challenge->id) . ' </p>
					                    <p class="storyCaption"> <span class="pointValue"> for '. $completed->pointsAwarded.'<span class="pts"> points </span></span></p>
					                    '. $submittedContent.
										(($completed->comments<>'') ? 
										(' <blockquote>
												<div class="quotes">'
						               				.$completed->comments.'
						               			</div>
						               		</blockquote>') : '' )                
					                .'</div><!__end "storyBlockWrap"__>
					            	';
					} else
					{
						$code .= '<div class="profilePicLarger">'.template::buildLinkedProfilePic($fbId,'size="square"').
					                '</div>
					                <div class="storyBlockWrap">
					                	<div class="feed_poster">
					                    	<h3><span class="bold">'./*template::buildLinkedProfilePic($fbId) .' '.*/ template::buildLinkedProfileName($fbId).' 
					                    
					            			</span> '.$ago.'</h3>
				            			</div>					              					                    
					                    '.	$submittedContent.
										(($completed->comments<>'') ? 
										(' <blockquote>
						                    	<div class="quotes">
						               				'.$completed->comments.'
						               			</div>
						               		</blockquote>') : '' )                
					                .'</div><!__end "storyBlockWrap"__>
					            	';
						
					}
					
				                
							
				} else
				{
					/*$code .= template::buildLinkedProfilePic($fbIds[0], 'size="square"') .' '. template::buildLinkedProfileName($fbId)
							.' submitted '. $submittedText 
							. 'for the challenge '. template::buildChallengeLink($challenge->title, $challenge->id); 
					*/
					$code .= '<div class="thumb">'.template::buildLinkedChallengePic($challenge->id, $challenge->thumbnail).
			                '</div>
			                <div class="storyBlockWrap">
			                	<div class="feed_poster"><div class="avatar">'.template::buildLinkedProfilePic($fbId, 'size="square" with="30" height="30"') .'</div>
			                    	<h3><span class="bold">'. template::buildLinkedProfileName($fbId).' submitted '.$submittedText 
									.' for the challenge '. template::buildChallengeLink($challenge->title, $challenge->id) . '
			                    
			            			</span> '.$ago.'</h3>
		            			</div>'.
			                    //'<p class="storyHead">'. template::buildChallengeLink($challenge->title, $challenge->id) . ' </p>
			                    '<p class="storyCaption">'. $submittedContent .'</p>                
			                </div><!__end "storyBlockWrap"__>
			            	';				
			
							
				}
				//$code .= self::getElapsedString(strtotime($action->t));
				
			} else
			{ if ($returnerrors) $code = 'Couldnt load challengeid ' . $completed->id . '<br>'; } // debugging
			
		}	else			
		{	if ($returnerrors) $code = 'Couldnt load completedid ' . $action->itemid . '<br>';  }// debugging
		return $code;
		
	}
	
	
	static function getElapsedString($date) 
	{
		//print $date;	
		$t = time() - $date;
		
		if ($t<60)		
			if($t <> 1){
				return $t . " seconds ago";
			} else {
				return $t . " seconds ago";
			}		
		$t = round($t/60);		
		if ($t<60)
			if($t <> 1){
				return $t . " minutes ago";
			} else {
				return $t . " minute ago";
			}		
		$t = round($t/60);	
		if ($t<24){
			if($t<> 1){
				return $t . " hours ago";
			} else {
				return $t . " hour ago";
			}
		}		
		$t = round($t/24);
		if ($t<7)
			if($t <> 1){
				return $t . " days ago";
			} else {
				return $t . " day ago";
			}
		$t = round($t/7);
		if ($t<4)
			if($t <> 1){
				return $t . " weeks ago";
			} else {
				return $t . " week ago";
			}
			
		return date("F j, Y", strtotime($date));
	}
	
	
	function buildPhotos($completedid, &$count)
	{
		require_once(PATH_CORE.'/classes/photo.class.php');
		
		// slight hack, assumes only one photo/video per challenge, as per our current spec, even though the schema allows more
		$photoTable = new PhotoTable($this->db);
		$photo = $photoTable->getRowObject();
		
		$photoids = $photoTable->getPhotosForCompletedChallenge($completedid);
		foreach ($photoids as $id)
		{
			if ($photo->load($id))
			{
				$code .= '<img src="' . URL_SUBMITTED_IMAGES.'/'. $photo->localFilename. '" width="150" />';
				$count++;
			}
		}		
		
		return $code;

	}
	
	function buildVideos($completedid, &$count)
	{
		//require_once(PATH_CORE.'/classes/challenges.class.php');
		require_once(PATH_CORE.'/classes/video.class.php');
		$videoTable = new VideoTable($this->db);
		$video = $videoTable->getRowObject();

		$videoids = $videoTable->getVideosForCompletedChallenge($completedid);
		foreach ($videoids as $id)
		{
			if ($video->load($id))
			{
				$code .= $this->buildVideoplayer($video->embedCode);
				$count++;
				
			}
		}		
	
		return $code;
	}
	
	
	function buildVideoPlayer($embedCode)
	{
		require_once(PATH_CORE .'/classes/video.class.php');
		return videos::buildPlayerFromLink($embedCode, 160, 120);
	}
	
	
}
?>