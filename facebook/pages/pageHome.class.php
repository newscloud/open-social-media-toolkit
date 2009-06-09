<?php
class pageHome {

	var $page;
	var $db;
	var $rowsPerPage=10;
	var $facebook;
	var $app;
	var $teamObj;
	var $wtObj;
	var $fwtObj;
	var $initialized;
	
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->common = &$page->common;
	}

	function initObjs() {
		if ($this->initialized)
			return true;
		require_once(PATH_CORE.'/classes/widgets.class.php');
		$this->wtObj=new WidgetsTable($this->db);
		$this->fwtObj=new FeaturedWidgetsTable($this->db);								
		$this->initialized = true;
	}
	
	function fetch() {
		if (isset($_GET['currentPage']))
			$currentPage=$_GET['currentPage'];
		else
			$currentPage=1;
		// check for referral and log it
		$referid=$this->page->fetchReferral();
		if ($referid!==false) {
			$this->page->recordReferral($referid,'referToSite');
		}						
		// build the home page
		require_once(PATH_CORE.'/classes/home.class.php');
		$homeObj=new home($this->db);		
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);				
		$inside.='<div id="col_left"><!-- begin left side -->';
		$inside.='<div id="featurePanel" class="clearfix">';
		$inside.=$this->page->buildPanelBar($this->common['FeaturedStoriesTitle'],'<a href="?p=stories&o=sponsor" onclick="switchPage(\'stories\',\'all\',\'sponsor\');return false;">More from '.SITE_SPONSOR.' editors</a>');
		$inside.=$homeObj->fetchFeature();
		$inside.='</div><!--end "featurePanel"-->';
		if (defined('ADS_HOME_SMALL_BANNER')) {
			$inside.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=homeSmallBanner" frameborder="0" scrolling="no" style="width:478px;height:70px;padding:0px;margin:-5px 0px 0px 0px;"/>',$this->common['adWrapSmallBanner']);
		}					
		// look for featured widget
		$this->initObjs();
		$featuredWidget=$this->fwtObj->lookupWidget('homeFeature');		
		if ($featuredWidget<>'') {
			$inside.=$featuredWidget;
		}
		$inside.='<div class="panel_1">';		
		$inside.=$this->page->buildPanelBar('Top News','<a class="rss_link" onclick="quickLog(\'extLink\',\'home\',0,\''.URL_RSS.'\');" target="_blank" href="'.URL_RSS.'">RSS</a><span class="pipe">|</span><a href="?p=postStory" onclick="switchPage(\'postStory\');return false;">Post a story</a>','The top stories as chosen by readers');
		$inside.='<div id="storyList">';
		$inside.=$homeObj->fetchHomePage($currentPage);
		$inside.='<input type="hidden" id="pagingFunction" value="fetchHomePage">';		
		$inside.='</div><!-- end storyList -->';
		$inside.=$this->page->buildPanelBar('','<a href="#" onclick="switchPage(\'stories\');return false;">More stories</a><span class="pipe">|</span><a href="#" onclick="switchPage(\'postStory\');return false;">Post a story</a>');
		$inside.='</div><!--end "panel_1"-->';
		$inside.=$this->teamObj->fetchLegend();
		$inside.='</div><!-- end left side --><div id="col_right">';
		$usePromo=true;
		if ($this->page->session->isMember==1) {
			sscanf($this->page->session->u->dateRegistered,"%4u-%2u-%2u %2u:%2u:%2u",$year,$month,$day,$hour,$min,$sec);
	        $tstampRegistered=mktime($hour,$min,$sec,$month,$day,$year);
	        if ($tstampRegistered<(time()-7*24*60*60)) {
	        	// after one week - use general announcement if available
				$this->initObjs();			
				$annCode.=$this->wtObj->fetchWidgetsByTitle('coverAnnounce');				        	
	        	if ($annCode<>'') {
		        	$inside.=$annCode;
		        	$usePromo=false;
	        	}
	        }
		} 
		if ($usePromo) {
			$inside.=$homeObj->fetchPromo();	
		}
		
		$inside.=$this->teamObj->fetchSidePanel('home');
/*
		if (defined('ENABLE_STORY_PANEL') AND ENABLE_STORY_PANEL===true) {
			$cacheName='sideLeaders';
			if ($this->templateObj->checkCache($cacheName,30)) {
				// still current, get from cache
				$temp=$this->templateObj->fetchCache($cacheName);
			} else {
				require_once(PATH_FACEBOOK.'/classes/storyPanels.class.php');
				$this->spObj=new storyPanels($this->page);							
				$temp=$this->spObj->fetchStoryList('rated','inside',5);
				$temp.=$this->spObj->fetchStoryList('discussed','inside',5);				
				$this->templateObj->cacheContent($cacheName,$temp);
			}
			$code.=$temp;		
		}		
 * 
 */		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;
		$code.=$this->page->constructPage('home',$inside);			
		return $code;
	}

}

?>