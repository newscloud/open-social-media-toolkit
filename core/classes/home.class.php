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
			$excludeStr=' AND NOT FIND_IN_SET(Content.siteContentId,\''.$data->story_1_id.','.$data->story_2_id.','.$data->story_3_id.','.$data->story_4_id.','.$data->story_5_id.','.$data->story_6_id.'\')';
			$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*,ContentImages.url as imageUrl,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) LEFT JOIN ContentImages ON (ContentImages.siteContentId=Content.siteContentId) WHERE Content.date<now() AND Content.date > date_sub(NOW(), INTERVAL ".AGE_TOP_STORY_MAX_HOUR." HOUR) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
			//$this->db->log("SELECT SQL_CALC_FOUND_ROWS Content.*, UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE date<now() AND date > date_sub(NOW(), INTERVAL ".AGE_TOP_STORY_MAX_HOUR." HOUR) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
			$this->templateObj->db->setTemplateCallback('submitBy', array($this->templateObj, 'submitBy'), 'postedByName');
			$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', 130));
			$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImageUrl'), 'imageUrl');
			$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'postedById');
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), 'postedById');
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
			if ($this->templateObj->db->countQ($this->templateObj->db->result)<5) {
				// if not enough stories, increase max hour - do this once
				$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*,ContentImages.url as imageUrl,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) LEFT JOIN ContentImages ON (ContentImages.siteContentId=Content.siteContentId) WHERE Content.date<now() AND Content.date > date_sub(NOW(), INTERVAL ".(2*AGE_TOP_STORY_MAX_HOUR)." HOUR) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
				if ($this->templateObj->db->countQ($this->templateObj->db->result)<3) {
					// if still too few, check in last week				
					$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*,ContentImages.url as imageUrl,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) LEFT JOIN ContentImages ON (ContentImages.siteContentId=Content.siteContentId) WHERE Content.date<now() AND Content.date > date_sub(NOW(), INTERVAL 7 DAY) AND isBlocked = 0 $excludeStr ORDER BY score DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
				}
			}
			//$this->templateObj->db->setTemplateCallback('cmdAdd', array($this->templateObj, 'commandAdd'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('cmdRead', array($this->templateObj, 'commandRead'), 'permalink');
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);
			$this->templateObj->cacheContent($cacheName,$code); 
		}			
		return $code;
	}

	function fetchAskQuestions(&$page=NULL) {
		$x=rand(0,100);
		if ($x<50) {
			$mode='recent';
			$title='Recent Questions';
			$cacheName='home_askRecent';
		} else {
			$mode='popular';
			$title='Popular Questions';
			$cacheName='home_askPopular';
		}
		if ($this->templateObj->checkCache($cacheName,30)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {		
			require_once(PATH_FACEBOOK.'/classes/ask.class.php');
			$askObj=new ask($this->db,$this->templateObj);				
			$code='';
			$code.='<div class="panel_1">';		
			$code.=$page->buildPanelBar($title,'<span class="pipe">|</span><a href="?p=ask&o=browse" onclick="switchPage(\'ask\',\'browse\');return false;">See all</a>','Questions asked by '.SITE_TITLE.' members');
			$code.='<div id="storyList">';
			$code.=$askObj->listQuestions($mode,0,0,0,3);
			$code.='</div><!-- end storyList -->';
			$code.='</div><!--end "panel_1"-->';
			$this->templateObj->cacheContent($cacheName,$code);				
		}
		return $code;	
	}		

	function fetchIdeas(&$page=NULL) {
		$x=rand(0,100);
		if ($x<50) {
			$mode='recent';
			$title='Recent Ideas';
			$cacheName='home_ideasRecent';
		} else {
			$mode='popular';
			$title='Popular Ideas';
			$cacheName='home_ideasPopular';
		}
		if ($this->templateObj->checkCache($cacheName,30)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {		
			require_once(PATH_FACEBOOK.'/classes/ideas.class.php');
			$iObj=new ideas($this->db,$this->templateObj);				
			$code='';
			$code.='<div class="panel_1">';
			$code.=$page->buildPanelBar($title,'<span class="pipe">|</span><a href="?p=ideas" onclick="switchPage(\'ideas\');return false;">See all</a>','Ideas suggested by '.SITE_TITLE.' members');
			$code.='<div id="storyList">';
			$code.=$iObj->listIdeas($mode,0,0,0,3);
			$code.='</div><!-- end storyList -->';
			$code.='</div><!--end "panel_1"-->';
			$this->templateObj->cacheContent($cacheName,$code);				
		}
		return $code;	
	}

	function fetchMicro(&$page=NULL) {
		$mode='recent';
		$title='Recent Tweets about '.SITE_TOPIC;
		$cacheName='home_microRecent';
		if ($this->templateObj->checkCache($cacheName,10)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {		
			require_once(PATH_FACEBOOK.'/classes/micro.class.php');
			$mObj=new micro($this->db,$this->templateObj);				
			$code='';
			$code.='<div class="panel_1">';
			$code.=$page->buildPanelBar($title,'<span class="pipe">|</span><a href="?p=tweets" onclick="switchPage(\'micro\');return false;">Visit our '.SITE_TOPIC.' tweet summary</a>');
			$code.='<div id="storyList">';
			$code.=$mObj->homeList();
			$code.='</div><!-- end storyList -->';
			$code.='</div><!--end "panel_1"-->';
			$this->templateObj->cacheContent($cacheName,$code);				
		}
		return $code;	
	}
	
	function fetchStuff(&$page=NULL,$limit=7) {
		// fetches image bar of recent items
		$cacheName='home_recentStuff';
		if ($this->templateObj->checkCache($cacheName,120)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {		
			$temp='';
			if (defined('SITE_LOCAL')) {
				$q=$this->db->queryC("SELECT Items.id,title,tagid,raw_tag,imageUrl FROM stuff.Items LEFT JOIN stuff.Tags ON tagid=Tags.id LEFT JOIN stuff.Access ON Access.itemid=Items.id  WHERE isHidden=0 AND status='available' AND accessType='site' AND accessid=".RESEARCH_SITE_ID." AND imageUrl<>''  ORDER BY dt DESC LIMIT $limit;");				
			} else {
				$q=$this->db->queryC("SELECT Items.id,title,tagid,raw_tag,imageUrl FROM stuff.Items LEFT JOIN stuff.Tags ON tagid=Tags.id WHERE isHidden=0 AND status='available' AND imageUrl<>''  AND isFriendsOnly=0 ORDER BY dt DESC LIMIT $limit;");								
			}
			if ($q!==false) {
				$temp.='<div class="imageStrip" >';
				$temp.='<div class="imageStripPanel">';
				while ($data=$this->db->readQ($q)) {
					$temp.='<a href="?p=things&o=view&id='.$data->id.'"><img title="'.$data->title.'" alt="'.$data->title.' in '.$data->raw_tag.'"  src="'.$data->imageUrl.'"></a>';
				 }
				$temp.='</div><!-- end imageStripPanel --></div><!-- end imageStrip --><br clear="both" />';				
				$code='<div class="panel_1">';
				$code.=$page->buildPanelBar('Recent '.SITE_STUFF_TITLE,'<span class="pipe">|</span><a href="?p=things" onclick="switchPage(\'things\');return false;">See all</a>','Items available at  '.SITE_TITLE);
				$code.=$temp;
				$code.='</div><!--end "panel_1"-->';				
				$this->templateObj->cacheContent($cacheName,$code);				
			} 			
		}
		return $code;
	}
	
	function fetchImages(&$page=NULL,$limit=7) {
		// fetches image bar of recent photos
		$cacheName='home_recentImages';
		if ($this->templateObj->checkCache($cacheName,120)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {		
			$temp='';
			$q=$this->db->queryC("SELECT * FROM FeedMedia WHERE previewImageUrl<>'' AND t>date_sub(NOW(),INTERVAL ".(defined('MEDIA_INTERVAL')?MEDIA_INTERVAL:"2")." DAY) ORDER BY RAND() DESC LIMIT $limit;");
			if ($q!==false) {
				$temp.='<div class="imageStrip" >';
				$temp.='<div class="imageStripPanel">';
				while ($data=$this->db->readQ($q)) {
					$temp.='<a href="?p=media&media=image&id='.$data->id.'"><img title="'.$data->title.' by '.$data->author.'"   alt="'.$data->title.' by '.$data->author.'"  src="'.$data->previewImageUrl.'"></a>';
				 }
				$temp.='</div><!-- end imageStripPanel --></div><!-- end imageStrip --><br clear="both" />';				
				$code='<div class="panel_1">';
				$code.=$page->buildPanelBar('Recent Photos',(defined('ENABLE_MEDIA_PROFILE')?'<span class="pipe">|</span><a href="?p=media&o=pro" onclick="switchPage(\'media\',\'pro\');return false;">Customize your profile pic</a>':'').'<span class="pipe">|</span><a href="?p=media" onclick="switchPage(\'media\',\'view\');return false;">See all</a>','Images related to '.SITE_TOPIC); 
				$code.=$temp;
				$code.='</div><!--end "panel_1"-->';				
				$this->templateObj->cacheContent($cacheName,$code);				
			} 			
		}
		return $code;
	}	

	function fetchFeature() {
		$cacheName='home_feature';
		if ($this->templateObj->checkCache($cacheName,60)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {			
			/*
			confused - why is this function embedded here? jr
			function getImageUrl($imageid = 0, $size = 'large') {
				$largeImageWidth = "185";
				$largeImageHeight = "130";
				$smallImageWidth = "40";
				$smallImageHeight = "30";
				if ($imageid == 0 || !($size == 'large' || $size == 'small'))
					return URL_CALLBACK.'?p=cache&img=watermark.jpg';
				else
					return sprintf("{URL_BASE}/index.php?p=scaleImg&id=%s&x=%s&y=%s&fixed=x&crop", $imageid, ${$size.'ImageWidth'}, ${$size.'ImageHeight'});
			}
			*/
			require_once(PATH_CORE.'/classes/utilities.class.php');
			$this->utilObj=new utilities($this->db);
			define ("LENGTH_CAPTION",250);			
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
				$story_results = $this->db->query("SELECT Content.*,ContentImages.url as imageUrl FROM Content LEFT JOIN ContentImages ON Content.siteContentId=ContentImages.siteContentId WHERE Content.siteContentId IN (".join(',', $story_ids).") ORDER BY FIND_IN_SET(Content.siteContentId, '".join(',', $story_ids)."')");
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

	function buildFeedbackBox(&$page=NULL,$isAjax=false) {
		$code='';
		$cacheName='home_feedback';
		if ($this->templateObj->checkCache($cacheName,1440)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {			
			$code='<div class="panel_1">';
			$q=$this->db->queryC("SELECT id FROM ForumTopics WHERE title='Feedback';");
			if ($q===false) return false;
			$d=$this->db->readQ($q);
			$q=$this->db->queryC("SELECT fbId FROM User LEFT JOIN UserInfo ON User.userid=UserInfo.userid WHERE isAdmin=1 LIMIT 1;");
			$d2=$this->db->readQ($q);
			$code.=$page->buildPanelBar('Give us your feedback','<span class="pipe">|</span><a href="?p=wall&topic='.$d->id.'">See all</a>','');
			$temp='<fb:comments xid="'.CACHE_PREFIX.'_wall_'.$d->id.'" canpost="true" candelete="true" numposts="1" showform="false" send_notification_uid="'.$d2->fbId.'" />';	
			$this->db->log($temp);
			$code.=$temp;
			$code.='</div><!--end "panel_1"-->';		
			if (!$isAjax) {
	 			$code='<div id="feedbackBox">'.$code.'</div>';
			}
			$this->templateObj->cacheContent($cacheName,$code);				
		}
		return $code;
	}
}

?>
