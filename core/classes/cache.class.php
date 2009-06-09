<?php

// You can turn on or off generation of these table areas to optimize generation of your cover page contents
define ("BUILD_FEATUREDSTORY",true);
define ("BUILD_STREETPANEL",true);
define ("BUILD_TOPSTORIES",true);
define ("BUILD_RECENTSTORIES",true);
define ("BUILD_ANNOUNCEMENT",true);
define ("BUILD_NEWSWIRE",true);

class cache {
			
	var $db;
	var $debug=false;
	var $pageObj;
	var $cObj;
	var $nwObj;
	var $templateObj;
	var $utilObj;
	var $storyList;
	
	function cache(&$db=NULL)
	{
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;
		require_once('systemStatus.class.php');
		$this->ssObj=new systemStatus($this->db);
		require_once('page.class.php');
		$this->pageObj=new page($this->db);		
		if (isset($_GET['debug']))
			$this->debug=true;
		$this->storyList=array();
	}

	function update() {
		require_once(PATH_CORE.'/classes/content.class.php');
		$this->cObj=new content($this->db);
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		
		if (MODULE_FACEBOOK) {
		}
		
		if (MODULE_PHP) {
			// register PHP templates
			$this->templateObj->registerTemplates('PHP');
						
			if (BUILD_RECENTSTORIES) {
				echo '<p>Building recent stories</p>';
				$code='';
				$this->templateObj->db->setTemplateCallback('contentid', array(&$this, 'trackStories'), 'contentid');
				$this->templateObj->db->result=$this->cObj->fetchRecentStories();
				$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'contentid');
				$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'contentid');			
				$code=$this->templateObj->mergeTemplate($this->templateObj->templates['sidebarList'],$this->templateObj->templates['sidebarItem'],'Recent stories');			
		 		//echo 'storyList';var_dump($this->storyList);echo "<BR>";
				$this->pageObj->cacheContent(CACHE_PREFIX.'Recent',$code);			
			}		
	
			if (BUILD_FEATUREDSTORY) {
			} 
	 		
			if (BUILD_NEWSWIRE) {
				echo '<p>Building newswire</p>';
				require_once('newswire.class.php');
				$this->nwObj=new newswire($this->db);
				$code='';
				$this->templateObj->db->result=$this->nwObj->fetchBreakingNewswire();
				$code=$this->templateObj->mergeTemplate($this->templateObj->templates['sidebarList'],$this->templateObj->templates['wireItem'],'Breaking news');			
				$this->pageObj->cacheContent(CACHE_PREFIX.'Newswire',$code);			
			}		
	
			if (BUILD_TOPSTORIES) {
				echo '<p>Building top stories</p>';
				$code='';
				$excludeList=implode(",",$this->storyList);
				// echo "Exclude list: ".$excludeList."<BR>";
				// set up templates from PHP newsroom
				$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
				$this->templateObj->db->setTemplateCallback('time_since', array($this->utilObj, 'time_since'), 'date');
				$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanEllipsis'), 'caption');
				$this->templateObj->db->setTemplateCallback('contentid', array(&$this, 'trackStories'), 'contentid');
				$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
				$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
				$this->templateObj->db->setTemplateCallback('cmdAdd', array($this->templateObj, 'commandAdd'), 'siteContentId');
				$this->templateObj->db->setTemplateCallback('cmdRead', array($this->templateObj, 'commandRead'), 'permalink');
				$this->templateObj->db->result=$this->cObj->fetchUpcomingStories($excludeList);
				$code=$this->templateObj->mergeTemplate($this->templateObj->templates['upcomingList'],$this->templateObj->templates['upcomingItem'],'Top stories');											
				$this->pageObj->cacheContent(CACHE_PREFIX.'Upcoming',$code);
			}		
	
			if (BUILD_ANNOUNCEMENT) {			
				$code=$this->ssObj->getState('announcement',true);
				$this->pageObj->cacheContent(CACHE_PREFIX.'Announcement',$code);
			}		
		}

		// Set the last updated timestamp
		echo 'Last update: '.date('h:i A').'<br/>';
		$this->ssObj->setState('lastUpdated',date('h:i A'));
		// reset the storyList array
		unset($this->storyList);		
	}

	/* Common Functions */
	
	function trackStories($contentid=0) {
		// keeps a list of stories used in template
		if ($contentid>0)
			$this->addStoryToPage($contentid);
		return $contentid;
	}
	
	function addStoryArrayToPage($list) {
		foreach ($list as $contentid) {
			$this->storyList[]=$contentid;
		}
	}
	
	function addStoryToPage($contentid) {
		// manage the array of content ids on this page
		if (array_search($contentid,$this->storyList)===false)  {
			$this->storyList[]=$contentid;
	 		// echo 'storyList inside addStoryToPage';var_dump($this->storyList);echo "<BR>";
			return true;
		} else 
			return false;
	}
					
}	
?>