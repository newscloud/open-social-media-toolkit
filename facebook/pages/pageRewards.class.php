<?php

class pageRewards {

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
	}

	function fetch($mode='fullPage', $o='') {
		// build the prizes page
		if (isset($_GET['currentPage']))
			$currentPage=$_GET['currentPage'];
		else
			$currentPage=1;			
		
		if (isset($_GET['filter']))
			$filter=$_GET['filter'];
		else
			$filter='weekly';//REDEEM:'redeemable';			
			
		require_once(PATH_CORE.'/classes/prizes.class.php');
		$rewards = new rewards($this->db);

		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		$tabs=$this->teamObj->buildSubNav('rewards');	 
		// required for ajax paging

		
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'rewards');
			
		
		if (isset($_GET['o']) && $_GET['o']=='summary')
		{
			$inside= $this->fetchSummaryPage(isset($_GET['textOnly']));

		} else if (isset($_GET['o']) && $_GET['o']=='winners' || $o=='winners')
		{
			$inside = $this->fetchWinnersPage($currentPage);
			$inside.='<input type="hidden" id="pagingFunction" value="fetchWinners">';		
	
		} else
		{
			if (isset($_GET['id']) && $_GET['id']) // it might be null on some links due to the way setTeamTab was modified 
			{
				
			
				$inside='<div id="col_left"><!-- begin left side -->';
					// reward detail page
					$inside .= '<a href="?p=rewards" onclick="setTeamTab(\'rewards\'); return false;"> &laquo;'.
								htmlentities($this->templateObj->templates['BackText']) .'</a><br /><br />';
					$inside .= '<hr />';
					
					if ($this->page->session->isLoaded AND ($this->page->session->u->eligibility=='general' OR $this->page->session->u->eligibility=='ineligible')) {
						$inside.=$this->fetchEligibilityBox();
						$showRedeemButtons=true;
					} else {						
						$showRedeemButtons=false;	
					}
					$inside.=$rewards->fetchRewardDetail($_GET['id'],$showRedeemButtons);
				
		
		
				$inside.='</div><!-- end left side --><div id="col_right">';
				$inside.=$this->teamObj->fetchSidePanel('rewards');
				
				$inside.='</div> <!-- end right side -->';
				
				
			} else 
			{	
					
				// reward base page
				if ($this->page->session->isLoaded 
						AND ($this->page->session->u->eligibility=='general' 
							OR $this->page->session->u->eligibility=='ineligible')) 
				{
					$inside.=$this->fetchEligibilityBox();
				} 

				
				$inside.='
				<div id="rewardsHead">
					<div class="panel_half float_left">
							<div class="storyBlockWrap">
							  <p>'.$this->templateObj->templates['rewardOverview'].//Earn points for your climate change actions, both in Facebook and in the real world. Redeem your points for for great earth-friendly prizes. Top participants are eligible for the super cool Grand and Runners-up Prizes!
								'</p>
								<p class="bump10"><a href="?p=invite" onclick="switchPage(\'invite\');return false;" class="btn_1">Invite more friends</a></p>
								</div><!--end "storyBlockWrap"-->
					    </div><!--end "panel_half"-->
						<div class="panel_half float_right">'
							. $this->templateObj->templates['GrandPrize'].												        
					'</div><!--end "panel_half"-->
				</div><!--end "rewardsHead"-->';

				//$inside .='<div>';
				$inside.='<h2>'.$this->templateObj->templates['RewardTitle'].'</h2>';
				//$inside .='<div style="float: right"><a href="?p=rewards&o=summary">Summary</div></div>';	  			
				$inside.=$rewards->fetchRewards('dollarValue'/*REDEEM:'pointCost'*/,$filter, $currentPage,false, 'team'); // $this->page->session->u->eligibility - with 16-25 yo change, show every reward to everyone
				
				$inside .='<h3><a href="?p=rewards&o=summary">Summary of all rewards by week...</a></h3>';
			}
		}		
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';		
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside,'fetchRewardsPage');		
		return $code;
	}

	function fetchEligibilityBox() {
		$code = $this->templateObj->templates['EligibilityBox'];
 		return $code;	
 	}			

 	
 	function fetchWinnersPage($currentPage=1)
 	{
 		$inside.='<h1>'.SITE_TITLE.' '.SITE_TEAM_TITLE.' Winners</h1>';
		
 		require_once(PATH_CORE.'/classes/prizes.class.php');
		$rewards = new rewards($this->db);
				
		$inside.='<div id="ajaxFeed" class="panel_1">';
					//<h2>'.$this->templateObj->templates['WinnersTitle'].'</h2>'; // do I need to keep this id?
		
 		//$inside .= $rewards->fetchWinners('',$currentPage);//" (WEEK(Log.dateCreated,1)-WEEK('$studyStartDate',1))=$i");
 		$inside .= $rewards->fetchPrizesWithWinners();
		$inside .= '</div><!--end "ajaxFeed" "panel_1"-->';
		
		
 		return $inside;
 	}
 	
 	function fetchWeeklyPrizeSchedule()
 	{
 		require_once(PATH_CORE.'/classes/prizes.class.php');
		$rewards = new rewards($this->db);
	
 		$studyWeeks = $this->templateObj->templates['ContestWeeks']; //9;
 		$studyStartDate = $this->templateObj->templates['ContestStartDate']; //'2009/3/01 00:10:00';
 		//$studyEndDate = '2009/4/28';
 		//$whereString = '';//;"dateEnd<'$studyEndDate' AND dateStart>'$studyStartDate'"; // this is too buggy
 		$currentPage = 1;
 		
 		for ($i=0; $i<$studyWeeks; $i++) // or however many weeks the study is
 		{
 			$inside.="<h3>Week ".($i+1) . " (ending ".date('F j, Y',strtotime($studyStartDate)+($i+1)*60*60*24*7).")</h3>";													
 			$inside.='<div id="rewardGrid">'.$rewards->fetchRewardsPage('dollarValue','weekly',$currentPage,true, 
 					"  (WEEK(dateStart,1)-WEEK('$studyStartDate',1))=$i", 'team')
 					.'</div>';
 		}
 		return $inside;
 	}
 	
	function fetchSummaryPage($textOnly=false)
	{		
		require_once(PATH_CORE.'/classes/prizes.class.php');
		$rewards = new rewards($this->db);
		
		if (!$textOnly) 
		{
			$currentPage=1;
			
			$inside.='<h1>Summary of '.SITE_TITLE.' '.SITE_TEAM_TITLE.' Rewards</h1>';
			$inside.='<a name="ingroup" />';	
			/* REDEEM: $inside.='<h1>Rewards available to in-group members</h1> 
					<a href="#outgroup">See rewards available to out-group members</a>';
		
			$inside.='<h5>Redeemable with Earned Points</h5><p>Eligible action team members can redeem their points for these rewards. Points turned in for prizes are not subtracted from your totals for weekly, grand and runners-up prizes.</p>';		
			$inside.=$rewards->fetchRewards('pointCost','redeemable', $currentPage,true, 
												'team');
			*/	
			/* REDEEM:
			$inside.='<h5>Weekly Rewards</h5><p>These will be given to the top eligible participants for a specified week during the life of the Action Team period.</p>';													
			$inside.=$rewards->fetchRewards('pointCost','weekly', $currentPage,true, 
												'team');
			*/
	
			$inside.='<h5>Weekly Rewards</h5>'.
				'<p>These will be given to the top eligible participants for a specified week during the life of the '.SITE_TEAM_TITLE.' contest.';													
				'The top scorer(s) will receive the first reward on the left; next-highest scoring members receive the next reward, etc.</p>';													
			$inside .=$this->fetchWeeklyPrizeSchedule();
			
			$inside.='<h5>Grand and Runners-up Rewards</h5><p>These will be awarded to the top eligible participants at the end of the '.SITE_TEAM_TITLE.' contest.</p>';													
			$inside.=$rewards->fetchRewards('dollarValue','grand', $currentPage,true, 
												'team');
	/* REDEEM:
			$inside.='<a name="outgroup" />';
			$inside.='<h1>Rewards available to out-group members</h1>
				<a href="#ingroup">See rewards available to in-group members</a>';
			
			$inside.='<h2>Redeemable</h2>';		
			$inside.=$rewards->fetchRewards('pointCost','redeemable', $currentPage,true, 
												'general');
				
			$inside.='<h2>Weekly</h2>';													
			$inside.=$rewards->fetchRewards('pointCost','weekly', $currentPage,true, 
												'general');
												
			$inside.='<h2>Grand and Runner-up</h2>';													
			$inside.=$rewards->fetchRewards('pointCost','grand', $currentPage,true, 
												'general');
		*/	
			
				
		} else {
			// build bloggable text summary of rewards available
				require_once(PATH_CORE.'/classes/template.class.php');
				$this->templateObj=new template($this->db);
				$this->templateObj->registerTemplates(MODULE_ACTIVE, 'rewards');			
			
			$temp.='<h2>Grand and Runners-up Rewards</h2><p>These will be awarded to the top eligible participants at the end of the '.SITE_TEAM_TITLE.' contest.</p>';													
			$prizeList=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Prizes WHERE isGrand>0 ORDER BY dollarValue DESC ;"); 	
			if ($this->templateObj->db->countQ($prizeList)>0) {
				$temp.=$this->templateObj->mergeTemplate($this->templateObj->templates['rewardList'],$this->templateObj->templates['rewardItemTextFinal']); 
			}
			$temp.='<h2>Weekly Rewards</h2>'.
				'<p>These will be given to the top eligible participants for a specified week during the life of the '.SITE_TEAM_TITLE.' contest.';													
				'The top scorer(s) will receive the first reward on the left; next-highest scoring members receive the next reward, etc.</p>';													
			$prizeList=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(dateEnd, '%c/%e') AS shortDateEnd FROM Prizes WHERE isWeekly=1 ORDER BY dateEnd ASC, dollarValue DESC;"); 	
			if ($this->templateObj->db->countQ($prizeList)>0) {
				$temp.=$this->templateObj->mergeTemplate($this->templateObj->templates['rewardList'],$this->templateObj->templates['rewardItemTextWeekly']); 
			}
			$inside.=$temp;
			$this->templateObj->cacheContent('rewardListText',$temp);			
		}
		return $inside;
		
	}
 	
 	
}


?>