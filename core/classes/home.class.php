<?php

class home {
	
	var $db;
	var $templateObj;
	var $utilObj;
		
	function home(&$db=NULL) {
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);				
	}
	
	function fetchHomePage($currentPage=1) {
		$cacheName='home_ts';
		if ($this->templateObj->checkCache($cacheName,10)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {
			$this->templateObj->registerTemplates(MODULE_ACTIVE, 'home');
			require_once(PATH_CORE.'/classes/utilities.class.php');
			$this->utilObj=new utilities($this->db);
			$code='';
			$startRow=($currentPage-1)*ROWS_PER_PAGE;
			$q = $this->templateObj->db->query("SELECT * FROM FeaturedTemplate WHERE id = 1");
			$data=$this->db->readQ($q);
			$excludeStr=' AND NOT FIND_IN_SET(siteContentId,\''.$data->story_1_id.','.$data->story_2_id.','.$data->story_3_id.','.$data->story_4_id.','.$data->story_5_id.','.$data->story_6_id.'\')';
			$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*, UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE date<now() AND date > date_sub(NOW(), INTERVAL ".AGE_TOP_STORY_MAX_HOUR." HOUR) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
			//$this->db->log("SELECT SQL_CALC_FOUND_ROWS Content.*, UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE date<now() AND date > date_sub(NOW(), INTERVAL ".AGE_TOP_STORY_MAX_HOUR." HOUR) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
			$this->templateObj->db->setTemplateCallback('submitBy', array($this->templateObj, 'submitBy'), 'postedByName');
			$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', 130));
			$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
			$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'postedById');
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), 'postedById');
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
			if ($this->templateObj->db->countQ($this->templateObj->db->result)<5) {
				// if not enough stories, increase max hour - do this once
				$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*, UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE date<now() AND date > date_sub(NOW(), INTERVAL ".(2*AGE_TOP_STORY_MAX_HOUR)." HOUR) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
				if ($this->templateObj->db->countQ($this->templateObj->db->result)<3) {
					// if still too few, check in last week				
					$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*, UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE date<now() AND date > date_sub(NOW(), INTERVAL 7 DAY) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
				}
			}
			//$this->templateObj->db->setTemplateCallback('cmdAdd', array($this->templateObj, 'commandAdd'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('cmdRead', array($this->templateObj, 'commandRead'), 'permalink');
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);
			$this->templateObj->cacheContent($cacheName,$code); 
		}			
		return $code;
	}

	function fetchFeature() {
		$cacheName='home_feature';
		if ($this->templateObj->checkCache($cacheName,60)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {			
			function getImageUrl($imageid = 0, $size = 'large') {
				$largeImageWidth = "185";
				$largeImageHeight = "130";
				$smallImageWidth = "40";
				$smallImageHeight = "30";
				if ($imageid == 0 || !($size == 'large' || $size == 'small'))
					return URL_CALLBACK.'?p=cache&img=watermark.jpg';
				else
					return sprintf("http://www.newscloud.com/images/scaleImage.php?id=%s&x=%s&y=%s&fixed=x&crop", $imageid, ${$size.'ImageWidth'}, ${$size.'ImageHeight'});
			}
			require_once(PATH_CORE.'/classes/utilities.class.php');
			$this->utilObj=new utilities($this->db);
			include PATH_TEMPLATES.'/featuredStories.php';
			//$this->templateObj->registerTemplates(MODULE_ACTIVE, 'featuredStories');
			$this->templateObj->db->result = $this->templateObj->db->query("SELECT * FROM FeaturedTemplate WHERE id = 1");
			if ($this->templateObj->db->count()>0) { // check that featured stories are defined
				$featured = mysql_fetch_assoc($this->templateObj->db->result);
				$template = $featured['template'];
				$code = '';
				$story_ids = array();
				foreach ($featured as $field => $value)
					if (preg_match('/^story_([0-9]+)_id$/', $field))
						$story_ids[] = $value;
				$stories = array();
				$story_results = $this->db->query("SELECT * FROM Content WHERE siteContentId IN (".join(',', $story_ids).") ORDER BY FIND_IN_SET(siteContentId, '".join(',', $story_ids)."')");
				while ($story = mysql_fetch_assoc($story_results))
					$stories[] = $story;
				//$code='<div id="featurePanel"><div id="featuredStories">';
				$code='<div id="featuredStories">';
				switch ($template) {
					case 'template_1':
						$code .= buildTemplate1($stories);
					break;
					case 'template_2':
						$code .= buildTemplate2($stories);
					break;
					case 'template_3':
						$code .= buildTemplate3($stories);
					break;
					case 'template_4':
						$code .= buildTemplate4($stories);
					break;
					case 'template_5':
						$code .= buildTemplate5($stories);
					break;
				}
				$code.='<!-- end of featured stories --></div>';
				$this->templateObj->cacheContent($cacheName,$code);				
			}
		}
		return $code;	
		
	}

	function fetchPromo() {
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'home');		
		$code.=$this->templateObj->templates['promo'];
		return $code;			
	}

}

?>
