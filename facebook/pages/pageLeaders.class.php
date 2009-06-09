<?php

class pageLeaders {

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
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'teamactivity');               				
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
	}

	function fetch($mode='fullPage',$option='alltime') {
		$tabs.=$this->teamObj->buildSubNav('leaders');
		$inside.=$this->fetchRewardPanel();
		
		$inside.='<div id="col_left"><!-- begin left side -->';
		//$inside.=$this->fetchChallengeList($currentPage);
		$inside.=$this->fetchLeaders($option, ENABLE_ACTION_REWARDS ? 'inside' : 'none');
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->teamObj->fetchSidePanel('leaders');		
		$inside.='</div> <!-- end right side -->';
		// currently not used		
		$inside.='<input type="hidden" id="pagingFunction" value="fetchLeaders">';			
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside);		
		return $code;
	}
	
	function fetchLeaders($view='alltime',$filter='inside',$isAjax=false)
	{	
        $inside=$this->fetchLeaderList($view,$filter);	
		if ($isAjax) return $inside; 
		$code.='<div id="navFilter">'; 
        $code.=$this->fetchSubFilter($view,$filter);
		$code .= '<input type="hidden" id="leaderView" value="'.$view.'">';
		$code .= '<input type="hidden" id="leaderFilter" value="'.$filter.'">';        								
        $code.='<!-- end navFilter --></div>';
        $code.='<div id="leaderList">';
        $code.=$inside;
        $code.='<!-- end leaderList --></div>';			
        return $code;		
	}
	
   function fetchSubFilter($view='alltime',$filter='inside') 
   {
		if ($view=='') $view='alltime';
   		$catlist = array(	'alltime'=>'All Time', 
   							'weekly' =>'This Week\'s');
   		$filterList = array(	 
   							'inside' =>'Members eligible for rewards',
   							'none'=>'All members');

        $code.='<div class="subFilter">'; 
		foreach ($catlist as $key => $field) 
        {
        	if ($key==$view) $selected='selected';
        	else $selected = '';
        	$code .= '<a id="'.$key.'LeaderView" class="feedFilterButton '.$selected.'"  
        				href="#" onClick="setLeaderView(\''.$key.'\'); return false;">'.$field.'</a> &nbsp;&nbsp;';        	
        }

        if (ENABLE_ACTION_REWARDS)
        {
	        $code.='Show:';
			foreach ($filterList as $key => $field) 
	        {
	        	if ($key==$filter) $selected='selected';
	        	else $selected = '';
	        	$code .= '<a id="'.$key.'LeaderFilter" class="feedFilterButton '.$selected.'"  
	        				href="#" onClick="setLeaderFilter(\''.$key.'\'); return false;">'.$field.'</a> &nbsp;&nbsp;';        	
	        }
        }
        
        $code.='</div><br clear="all" />';
        return $code;       
    }	

	function fetchLeaderList($option='alltime',$filter='inside',$limit=25) {
		$cacheName='leaders_'.$option.'_'.$filter;
		if ($this->templateObj->checkCache($cacheName,30)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {
			$code=$this->teamObj->fetchLeaders($option,$filter,$limit,false);		
			$this->templateObj->cacheContent($cacheName,$code);
		}		
		return $code;	
	}
	
	
	function fetchRewardPanel()
	{
		
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'rewards');               				
		
		// reward base page		
		$inside.='<div id="alltimeLeaderRewardHead">
			<div id="rewardsHead">
					<div class="panel_half float_left">
							<div class="storyBlockWrap">
							  <p>'.$this->templateObj->templates['leaderRewardAlltimeOverview'].
								'</p>
								
							</div><!--end "storyBlockWrap"-->
					    </div><!--end "panel_half"-->
						<div class="panel_half float_right">'
						.$this->templateObj->templates['GrandPrize'].								
					'</div><!--end "panel_half"-->
				</div><!--end "rewardsHead"-->
			</div>';
		
		
		require_once(PATH_CORE.'/classes/prizes.class.php');
		$rewards = new rewards($this->db);
		
		$pt = new PrizeTable($this->db);
		$weeklyPrizeList = $pt->getWeeklyPrizesByDate("NOW()",'RAND()');
		
		$weeklyPrizeIds = array_keys($weeklyPrizeList);
		
		$id=$weeklyPrizeIds[0]; // just show one prize for now
		//foreach ($weeklyPrizeIds as $id)
			$weekly.='<div id="leaderWeeklyRewardBlock">'.$rewards->fetchRewardDetail($id,true,true).
				'</div>'; // bit of  a hack
			
		// this one hidden by default since thats the default state on page fetch
		$inside.='<div id="weeklyLeaderRewardHead" class="hidden">
			<div id="rewardsHead">
					<div class="panel_half float_left">
							<div class="storyBlockWrap">
							  <p>'.$this->templateObj->templates['leaderRewardWeeklyOverview'].
								'</p>								
								</div><!--end "storyBlockWrap"-->
					    </div><!--end "panel_half"-->
						<div class="panel_half float_right">
						
							'.$weekly
		
							/*.'
					        <div class="thumb">
							<a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;"><img src="'.URL_CALLBACK.'?p=cache&f=prize_47__expedition.jpg&x=120&y=90&m=scaleImg&path=uploads&fixed" /></a>
							</div><!--end "thumb"-->
							<div class="storyBlockWrap">
								<span class="pointValue">Grand Prize</span><p class="storyHead"><a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;">Become a Polar Ambassador!</a></p>
								<p>Experience the Arctic like you never imagined possible on a <strong>Spitsbergen Explorer trip for two to the Arctic with Quark Expeditions</strong>. Your 11-day adventure begins August 4, 2009 with the aim of circumnavigating the coastline of the largest island in Norway\'s Svelbard archipelago. <a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;" class="more_link">&hellip;&nbsp;more</a></p>
					            
								</div><!--end "storyBlockWrap"-->'*/
					.'</div><!--end "panel_half"-->
				</div><!--end "rewardsHead"-->
			</div>';
		
		return $inside;
		
	}
	
}

?>