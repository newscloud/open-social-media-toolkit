<?php

class pageProfile {

	var $page;
	var $db;
	var $facebook;
	var $fbApp;
	var $templateObj;
	var $memberid;
	var $isProfileOwner;
		
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->session=&$page->session;
		$this->setupLibraries();
		$this->commonTeam = &$page->commonTeam;
		// memberid is the id to show the profile page for, set in URL
				
		if (isset($_GET[memberid])) 
		{
			$this->memberid=$_GET[memberid];
		} else if (isset($_GET[arg3])) 
		{
			// id is passed as arg3 in switch page via ajax
			$this->memberid=$_GET[arg3];
		} else
			$this->memberid=false;
		
			// isMember - tells whether the viewer is the person on the profile
		//echo '<pre>'. print_r($this, true) . '</pre>';
		//echo 'true?: '. (($this->memberid==$this->session->fbId) ? 'true' : 'false');
		if ($this->memberid==$this->session->fbId) 
		{
 			$this->isProfileOwner = true;
		} else 
		{
			 $this->isProfileOwner = false;
		}
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'profile');
	}
	
	function fetch() {
		if ($this->memberid===false) {
			$this->page->go404('Member not found');
			exit;
		}
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$actionTeam = new actionTeam(&$this->page);
		if ($_GET['message']) $inside .= $this->page->buildMessage('success', "Submission successful", urldecode($_GET['message']));
		
		$this->updateUserCachedPoints($this->memberid, 
			$this->isProfileOwner || $this->session->u->isAdmin || $this->session->u->isModerator); // update immediately under any of these circumstances
		
		$inside .=' <div id="col_left">
					<script>
						<!-- 
						updateProfileTabName('.$this->session->fbId.','.$this->memberid.'); 
						//-->
					</script>
			          <!-- begin left side -->


					<div id="myProfile">
					    	
						<!-- profileSummary goes here -->
					    '.$actionTeam->fetchProfileSummaryPanelForProfilePage(
								$this->memberid, $this->isProfileOwner, true, false) .'    
					</div><!-- end "myProfile"-->
					
					<!--  profileBio here -->
					'.self::fetchBio($this->isProfileOwner, $this->memberid).'
					<!--  pending challenges here -->
					'.(ENABLE_ACTION_CHALLENGES ? $this->fetchPendingChallenges() : '').'
					<!--  orders summary goes here -->					
					'.$this->fetchRedeemLinks().'
					<!--  orders summary goes here -->					
					'.$this->fetchOrderList().'
					

					<!--  admin -challenge submit div goes here -->
					'.(isset($_GET['viewSubmitted']) ? $this->fetchChallengesSubmittedFeedBox() : '') .'
					
					
					<!--  feed div goes here -->
					'.(isset($_GET['viewHistory']) ? $this->fetchPlainHistoryBox() : $this->fetchFeedBox() ) .'

					
					
					</div><!--end "col_left"-->
					
					<div id="col_right">';

					if (($this->session->isAdmin) && (isset($_GET['showAdminPanel'])))
					{
						$inside .= $this->fetchAdminPanel(); // not implemented now, but potentially useful					
					}
					// Blog posts by this user (if any)
					$q=$this->db->queryC("SELECT UserBlogs.blogid,UserInfo.fbId,UserInfo.userid FROM UserBlogs LEFT JOIN UserInfo ON UserInfo.userid=UserBlogs.userid WHERE UserInfo.fbId=".$this->memberid." AND UserBlogs.status='published';");
					if ($q!==false) {
						$data=$this->db->readQ($q);
						$this->templateObj->registerTemplates(MODULE_ACTIVE, 'read');
						$this->templateObj->db->result = $this->templateObj->db->query("SELECT * FROM Content WHERE isBlogEntry=1 AND userid=".$data->userid." ORDER BY date DESC LIMIT 10 ");
						$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'userid');
						$inside.= '<div class="panel_1"><div class="panelBar clearfix"><h2>Blog posts by <a href="?p=profile&memberid='.$this->memberid.'" onclick="return switchPage(\'profile\', \'\', '.$this->memberid.';"><fb:name ifcantsee="Anonymous" uid="'.$this->memberid.'" capitalize="true" firstnameonly="false" linked="false" /></a></h2></div><!-- end panelbar --><br />';
						$inside.= $this->templateObj->mergeTemplate($this->templateObj->templates['otherStoryList'],$this->templateObj->templates['otherStoryItem']);
						$inside.= '</div><!-- end panel_1 -->';			
					}
					
								
					if (!$this->isProfileOwner)		
						$adjPage='otherProfile';
					else
						$adjPage='myProfile';
					$inside.=$actionTeam->fetchSidePanel($adjPage);
					
					$inside.='</div><!--end "col_right"-->';
		
		/*
		$profileSummary = $actionTeam->fetchProfileSummary($this->memberid, $this->isProfileOwner, true, false, true);
		$inside .= $profileSummary; 
		
		$inside .= self::fetchBio($this->isProfileOwner, $this->memberid);
		//$inside.='<p><strong>Challenges I\'ve Completed</strong><p>';
		//$inside.= $this->fetchChallengesCompleted(isset($_GET['currentPage']) ? $_GET['currentPage'] : 1);
		//$inside.='<br />';
		//$inside.='<p><strong>Stories I\'ve posted</strong><p>';
		//$inside.=$this->fetchPostedStories();
		
		
		$inside .= $this->fetchPendingChallenges();
		$inside .= $this->fetchOrderList();
		$inside .= $this->fetchFeedBox();
		$inside.='<br />';
		
		
		*/
		if ($this->page->isAjax) return $inside;
		// build the profile page	
	
		$code=$this->page->constructPage('profile',$inside);
		
		
		return $code;
	}
	
	
	/*
	 * 
<div id="profileBio" class="panel_1">
	<div class="panelBar clearfix">
		<h2>My Bio</h2>
		<div class="bar_link"><a href="#">Edit</a></div>
	</div><!--end "panelBar"-->
    <div class="panel_block">
      <p><a href="#">Please enter a brief statement about yourself</a></p>
    </div>
</div><!--end "profileBio" "panel_1"-->
	 * 
	 */
	
	static function fetchBio($selfviewing, $fbId)
	{
	
		$code .='<div id="profileBio" class="panel_1">';
		$code .= self::fetchBioArea($selfviewing,$fbId);
		$code .='</div><!--end "profileBio" "panel_1"-->';

		return $code;		
	}
	
	static function fetchBioArea($selfviewing, $fbId)
	{
		// try just including this here so pages that include this file directly dont have problems
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance(); // grrr
		
		
		include(PATH_TEMPLATES.'/commonTeam.php');
		
	
		$code .= '<div class="panelBar clearfix"><h2>'.$commonTeam['BioPanelTitle'].'</h2>';
		if ($selfviewing)
		{
			
			$code .= '<div class="bar_link"><a href="#" class="bold" onclick="editBio('.$fbId.'); return false;">Edit</a></div>';
			
			
		} 
		$code .= '</div><!-- end panelBar-->';
		
		require_once(PATH_CORE .'/classes/user.class.php');
		$uit = new UserInfoTable();
		$ui = $uit->getRowObject();
		$ui->loadFromFbId($fbId);
		$code .= '<div class="panel_block"><p>'.$ui->bio. '</p></div>';
		return $code;				
	}

	
	static function fetchBioEditor($fbId)
	{		
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance();
	
		include(PATH_TEMPLATES.'/commonTeam.php'); // hack because this stuff is static and not the best ajax ever
		
		$code .= '<div class="panelBar clearfix"><h2>'.$commonTeam['BioPanelTitle'].' (Limit 500 characters)</h2>';
		if (true)
		{			
			$code .= '<div class="bar_link"><a href="#" class="bold" onclick="saveBio('.$fbId.'); return false;">Save</a></div>';						
		} 
		$code .= '</div><!-- end panelBar-->';
		
		require_once(PATH_CORE .'/classes/user.class.php');
		$uit = new UserInfoTable();
		$ui = $uit->getRowObject();
		$ui->loadFromFbId($fbId);
		$code .= '<div class="panel_block"><textarea id="bioText" rows="7" cols="60">'.$ui->bio. '</textarea></div>';
		
		return $code;
		
	}
	
	function fetchPostedStories() {
		// to do: restrict to posted by this userid
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT * FROM Content ORDER BY date DESC LIMIT ".ROWS_PER_PAGE.";");
		$code=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);							
		return $code;		
	}

	/*
	 * 
<div id="profilePending" class="panel_1">
	<div class="panelBar clearfix">
		<h2>Pending Challenges</h2>
		<div class="bar_link"><a href="#">What's this?</a></div>
	</div><!--end "panelBar"-->
    <div class="historyTable">
                  <table cellspacing="0">
                    <tbody>
                      <tr>
                        <th>Challenge Submitted</th>
                        <th>Points</th>
                        <th>Date</th>
                      </tr>
                      <tr>
                        <td class="bold"><a href="#">Challenge Name One</a></td>
                        <td class="pointValue">200</td>
                        <td>Feb 8, 2009</td>
                      </tr>
                      <tr>
                        <td class="bold"><a href="#">Challenge Name Two</a></td>
                        <td class="pointValue">100</td>
                        <td>Feb 7, 2009</td>
                      </tr>
                      <tr>
                        <td class="bold"><a href="#">Challenge Name Three</a></td>
                        <td class="pointValue">300</td>
                        <td>Feb 3, 2009</td>
                      </tr>
                    </tbody>
                  </table>
        </div><!--end "historyTable"-->
</div><!--end "profileBio" "panel_1"-->

	 */
	
	
	function fetchPendingChallenges()
	{
		if ($this->isProfileOwner)
			$userid = $this->session->userid;
		else
			return '';

			
		
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'teamactivity');               
   	// build list of challenges we've done with photos and videos attached
		
		// to do - take out rows per page
		$rowsPerPage=10;
		// userid is passed in because there is no session when refreshed with Ajax
		
		$currentPage = 1;
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		$challengeList=$this->templateObj->db->query( // TODO: sort by dateAwarded and dont show submissions
		"SELECT SQL_CALC_FOUND_ROWS 
				Challenges.title AS title, pointValue, challengeid,  
				ChallengesCompleted.status AS status, ChallengesCompleted.id AS completedid,
				Challenges.pointValue AS pointValue,
				MONTHNAME(dateSubmitted) AS month,
				DAY(dateSubmitted) AS day,
				DATE_FORMAT(dateSubmitted, '%M %e, %Y') AS date
				
				FROM ChallengesCompleted,Challenges,UserInfo
				WHERE UserInfo.userid=$userid 
					AND ChallengesCompleted.challengeid=Challenges.id 
					AND ChallengesCompleted.userid=UserInfo.userid
					AND ChallengesCompleted.status='submitted'
					AND Challenges.type='submission'
					".
				//	"AND (Photos.challengeCompletedId=ChallengesCompleted.id OR Videos.challengeCompletedId = ChallengesCompleted.id)".\ 
				//	"AND (Videos.challengeCompletedId = ChallengesCompleted.id) ". 
				"ORDER BY dateSubmitted DESC LIMIT $startRow,".$rowsPerPage.";"); // $this->page->rowsPerPage
	//	$code.='<div>';
		// to do - later we'll move these template defs
		if ($this->templateObj->db->countQ($challengeList)>0) 
		{
			$rowTotal=$this->templateObj->db->countFoundRows();
			//$pagingHTML=$this->page->paging($currentPage,$rowTotal,$rowsPerPage,'&p=profile&currentPage='); // later put back page->rowsPerPage			
			$code.=$this->templateObj->mergeTemplate(
				$this->templateObj->templates['profileChallengeList'],$this->templateObj->templates['profileChallengeItem']);           
			
		} else {
			$code.='No challenges found.';
		}			
		//$code.=$pagingHTML;
		$code =
		'<div id="profilePending" class="panel_1">
			<div class="panelBar clearfix">
				<h2>'.$this->commonTeam['PendingChallengesPanelTitle'].'</h2>
				<div class="bar_link"><a href="#" onclick="showDialog(\'default\',\'What are pending challenges?\',\'Some challenges, such as those involving pictures, document review or video, require review by our community manager. Please allow up to 72 hours for points to be assigned for these types of challenges.\');return false;">What\'s this?</a></div>
			</div><!--end "panelBar"-->
		    <div class="historyTable">
		             '.$code .'    
		        </div><!--end "historyTable"-->
		</div><!--end "profileBio" "panel_1"-->';
		
/*		$code = '<div><div><h2>Pending Challenges</h2></div>
					<table>
						<tr><th>Challenge Submitted</th><th>Points</th><th>Date</th></tr>'.
						$code.
					'</table>'.
				'</div>';
	*/	
		$this->db->resetTemplateCallbacks();
		return $code;
				
	}
	
	/*
	 *  <div id="ajaxFeed" class="panel_1">
	<div class="panelBar clearfix">
		<h2>My Action Feed</h2>
		<div class="bar_link"><a href="#">Post a story</a></div>
	</div><!__end "panelBar"__>
	 * 
	 * 
	 * </div><!-- end ajaxFeed -->
	 */
	
	function fetchFeedBox($currentPage=1)
	{
		require_once(PATH_FACEBOOK.'/classes/actionFeed.class.php');
		$actionFeed = new actionFeed(&$this->db);
		
		$code.='<input type="hidden" id="pagingFunction" value="fetchFeedPage">';		
		
		$code.='<div class="panel_1">';		
		$code .='<div class="panelBar clearfix">
					<h2>'.$this->commonTeam['ProfileFeedPanelTitle'].'</h2>
					<!-- <div class="bar_link"><a href="#">Post a story</a></div> TODO: put this back?-->
					</div><!__end "panelBar"__>';
		
		$code .='<div id="ajaxFeed">';
		$code.=$actionFeed->fetchFeed('all', $currentPage, 
			UserInfoTable::getUserid($this->memberid), 0,false,$this->session->u->isAdmin || $this->isProfileOwner); // show scorelog button if admin viewing
		$code.='<!-- end ajaxFeed --></div>';
		$code .='</div><!-- end panel_1 -->';

		
		
		return $code;
	}
	
	function fetchPlainHistoryBox()
	{
		require_once(PATH_FACEBOOK.'/classes/actionFeed.class.php');
		$actionFeed = new actionFeed(&$this->db);
		
		
		
		$code.='<div class="panel_1">';		
		$code .='<div class="panelBar clearfix">
					<h2>'.$this->commonTeam['ProfileFeedPanelTitle'].'/h2>
					<!-- <div class="bar_link"><a href="#">Post a story</a></div> TODO: put this back?-->
					</div><!__end "panelBar"__>';
		
		$code .='<div>';
		$code.=$actionFeed->fetchFeedScoreLog(UserInfoTable::getUserid($this->memberid),1,0); // all pages, hopefully minimal formatting

		$code.='<!-- end --></div>';
		$code .='</div><!-- end panel_1 -->';

		
		
		return $code;
		
	}
	
	/*
	 * Note that since I didnt link this in with the normal filter regime paging wont work...can be fixed but not worth it for now
	 */
	
	
	function fetchChallengesSubmittedFeedBox($currentPage=1)
	{
		require_once(PATH_FACEBOOK.'/classes/actionFeed.class.php');
		$actionFeed = new actionFeed(&$this->db);
		
		$code.='<input type="hidden" id="pagingFunction" value="fetchFeedPage">';		
		
		$code.='<div class="panel_1">';		
		$code .='<div class="panelBar clearfix">
					<h2>'.$this->commonTeam['ChallengesSubmittedFeedPanelTitle'].'</h2>
					<!-- <div class="bar_link"><a href="#">Post a story</a></div> TODO: put this back?-->
					</div><!__end "panelBar"__>';
		
		$code .='<div id="ajaxFeed">';
		$code.=$actionFeed->fetchFeedChallengesSubmittedPage(UserInfoTable::getUserid($this->memberid),$currentPage);
		$code.='<!-- end ajaxFeed --></div>';
		$code .='</div><!-- end panel_1 -->';

		
		
		return $code;
	}
	
	
	
	/*
	 * 
	


	 */
	
	function fetchOrderList($currentPage=1)
	{
		if ($this->isProfileOwner /*AND $this->session->u->eligibility=='team'*/)
			$userid = $this->session->userid;
		else
			return '';
				
		// to do - take out rows per page
		$rowsPerPage=100;
		// userid is passed $code='';
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page

		$challengeList=$this->templateObj->db->query(
		"SELECT SQL_CALC_FOUND_ROWS
				DATE_FORMAT(CASE Orders.status WHEN 'submitted' THEN dateSubmitted 
									WHEN 'approved'  THEN dateApproved
									WHEN 'shipped' 	THEN dateShipped
									WHEN 'canceled'	THEN dateCanceled
									WHEN 'refunded'	THEN dateRefunded END, 
							'%M %e, %Y') AS date,
 				Orders.status,
				Prizes.title,
				Orders.prizeid,  
				Orders.id AS orderid 
				FROM Orders,Prizes WHERE Orders.prizeid=Prizes.id AND Orders.userid=$userid 
				ORDER BY Orders.dateSubmitted ASC LIMIT $startRow,".$rowsPerPage.";"); // $this->page->rowsPerPage
				
				
				//$code.='<div>';
		// to do - later we'll move these template defs
		if ($this->templateObj->db->countQ($challengeList)>0) 
		{
			$listTemplate='
			    <table cellspacing="0">
                    <tbody>
                      <tr>
                        <th>Reward</th>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                      </tr>
                      {items}

                    </tbody>
                  </table>';

			
			$itemTemplate='<tr>'.
				'<td class="bold"><a href="?p=rewards&id={prizeid}" onclick="setTeamTab(\'rewards\', {prizeid}); return false;">{title}</a></td>'.
			
				'<td><a href="?p=orders&id={orderid}" onclick="setTeamTab(\'orders\',{orderid}); return false;">{orderid}</a></td>'.
				'<td>{date}</td>'.
				'<td class="pointValue">{status}</td>'.  
				'</tr>'; 
		
			$rowTotal=$this->templateObj->db->countFoundRows();
			$code.=$this->templateObj->mergeTemplate($listTemplate,$itemTemplate);
		} else {
			//$code.='You have not placed any orders.';
			return ''; // just hide it if theres nothing
		}			
		$code = '<div id="profileOrders" class="panel_1">
					<div class="panelBar clearfix">
						<h2>Order History</h2>'.
						//'<div class="bar_link"><a href="?p=rewards" onclick="setTeamTab(\'rewards\'); return false;">Redeem your points</a></div>'.
					'</div><!--end "panelBar"-->
				    <div class="historyTable">
						    '.$code.'                   
					</div><!--end "historyTable"-->	    
				</div><!--end "profileBio" "panel_1"-->';
		
		return $code;
	}	

	
	function fetchRedeemLinks($currentPage=1)
	{
		if ($this->isProfileOwner /*AND $this->session->u->eligibility=='team'*/)
			$userid = $this->session->userid;
		else
			return '';
		
		
		
		// to do - take out rows per page
		$rowsPerPage=100;
		// userid is passed $code='';
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page

		$challengeList=$this->templateObj->db->query(
		"SELECT itemid AS prizeid, title 
			FROM Log
			LEFT JOIN Prizes ON itemid=Prizes.id	 
			WHERE Log.userid1=$userid 
					AND Log.action='wonPrize' 
					AND itemid NOT IN (SELECT prizeid FROM Orders WHERE userid=$userid)
				
					;");
		
		
				
				//$code.='<div>';
		// to do - later we'll move these template defs
		if ($this->templateObj->db->countQ($challengeList)>0) 
		{
			$listTemplate='
			    <table cellspacing="0">
                    <tbody>
                      <!-- <tr>
                        <th>Reward</th>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                      </tr> -->
                      {items}

                    </tbody>
                  </table>';
			
		/*	$itemTemplate='<p>
				<a href="?p=prizeInfo&id={Prizes.id}">
				#{Orders.id} submitted on {Orders.dateSubmitted} for {Prizes.title} - 
				<a href="?p=orderInfo&id={Orders.id}">details</a></p>'; /*{pointCost}*/
			$itemTemplate='<tr>'.
				'<td class="bold"><a href="?p=redeem&id={prizeid}" onclick="setTeamTab(\'redeem\', {prizeid}); return false;">{title}</a></td>'.
			
				//'<td><a href="?p=orders&id={orderid}" onclick="setTeamTab(\'orders\',{orderid}); return false;">{orderid}</a></td>'.
				//'<td>{date}</td>'.
				//'<td class="pointValue">{status}</td>'.  
				'</tr>'; 
		
			$rowTotal=$this->templateObj->db->countFoundRows();
		//	$pagingHTML=$this->page->paging($currentPage,$rowTotal,$rowsPerPage,'?userid='.$userid.'&p=orders&currentPage='); // later put back page->rowsPerPage			
			// $this->templateObj->db->setTemplateCallback('comments', array($this, 'decodeComment'), 'comments');
			//$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['commentList'],$this->templateObj->templates['commentItem']);
			$code.=$this->templateObj->mergeTemplate($listTemplate,$itemTemplate);
		} else {
			//$code.='You have not placed any orders.';
			return '';
		}			
	//	$code.=$pagingHTML;
		
		
		$code = '<div id="profileRedeemLinks" class="panel_1">
					<div class="panelBar clearfix">
						<h2>Redeem Links</h2>
						<div class="bar_link">'.
							//'<a href="?p=rewards" onclick="setTeamTab(\'rewards\'); return false;">Redeem your points</a>'.
							'</div>
					</div><!--end "panelBar"-->
				    <div class="historyTable">
						    '.$code.'                   
					</div><!--end "historyTable"-->	    
				</div><!--end "profileBio" "panel_1"-->';
		
		return $code;
	}		
	
	
	function buildPhotos($completedid)
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
	
			}
		}		
		
		return $code;

	}
	
	function buildVideos($completedid)
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
	
			}
		}		
		return $code;
	}
	
	
	function buildVideoPlayer($embedCode)
	{
		require_once(PATH_CORE .'/classes/video.class.php');
		return videos::buildPlayerFromLink($embedCode, 160, 120);
	}

	/*
	function fetchAdminPanel()
	{
			$code .= '
		<div id="profileAdmin" class="panel_2 clearfix">
			<div class="panelBar clearfix">
				<h2>Admin Panel</h2>
			</div><!__end "panelBar"__>'

		.	'<div class="panel_block">'
			.'<form '		
			.'</div><!__end "panel_block"__>'
		.'</div><!__end "panel_1"__>';
		
		return $code;
		
	}*/
	
	
	function updateUserCachedPoints($fbId, $always = false) // updates if older than 1 hour unless overridden
	{
		require_once(PATH_CORE .'/classes/user.class.php');
		$userTable = new UserTable($this->db);
		$user = $userTable->getRowObject();

		$userinfoTable = new UserInfoTable($this->db);
		$userinfo = $userinfoTable->getRowObject();
	
		if ($userinfo->loadWhere("fbId=$fbId " . 
						($always ? "" : "AND DATE_ADD(lastUpdateCachedPointsAndChallenges, INTERVAL 1 HOUR) < NOW()")))
		{
			$userinfoTable->updateUserCachedPointsAndChallenges($userinfo->userid, &$user, &$userinfo,$weekOf);				
		}
	
	}
	
}

		


?>