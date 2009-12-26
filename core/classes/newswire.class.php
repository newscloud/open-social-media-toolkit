<?php

class newswire {
	
	var $db;
	var $templateObj;
		
	function newswire(&$db=NULL) {
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}

	function getWireStory($id=0) {
		$result=$this->db->query("SELECT * FROM Newswire WHERE id=$id;");
		$info=$this->db->readQ($result);
		return $info;	
	}

	function getWebpage($id=0) {
		$result=$this->db->query("SELECT url FROM Newswire WHERE id=$id;");
		$info=$this->db->readQ($result);
		return $info->url;	
	}
	
	function add($wire) {
		// check for duplicate
		$chkDup=$this->db->queryC("SELECT id FROM Newswire WHERE url='$wire->url' AND feedType='$wire->feedType';");
		if (!$chkDup) {
			// insert the story into the table
			$this_query=$this->db->insert("Newswire","title,caption,source,url,date,wireid,feedType,feedid","'$wire->title','$wire->caption','$wire->source','$wire->url','$wire->date',$wire->wireid,'$wire->feedType',$wire->feedid");
			$newId=$this->db->getId();
			return $newId;	
		} else 
			return false;
	}

	function createTempContent($userinfo=NULL,$wireid=0) {
		require_once(PATH_CORE.'/classes/content.class.php');
		$cObj=new content($this->db);
		$info=$this->getWireStory($wireid);
		require_once(PATH_CORE.'/classes/parseStory.class.php');
		$psObj = new parseStory();
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);						
		$info->title=stripslashes($info->title);
		$info->caption=stripslashes($this->utilObj->shorten($info->caption));
		// to do - replace proxy feed urls with final redirect
		$info->title=$psObj->cleanTitle($info->title);		
		// create permalink	
		$info->permalink=$cObj->buildPermalink($info->title);
		// serialize the content
		$info->title=mysql_real_escape_string($info->title);
		$info->caption=mysql_real_escape_string($info->caption);
		$story=$cObj->serialize(0,$info->title,$info->caption,$info->source,$info->url,$info->permalink,$userinfo->ncUid,$userinfo->name,$userinfo->userid,'',$userinfo->votePower,0,0);
		// post wire story to content
		$siteContentId=$cObj->add($story);
		return $siteContentId;
	}								
		
	function serialize($title='',$caption='',$source='',$url='',$date='',$wireid=0,$feedType='wire',$feedid=0) {
		// creates an object for an action
		$data= new stdClass;
		$data->title=mysql_real_escape_string($title);
		$data->caption=mysql_real_escape_string($caption);
		$data->source=$source;
		$data->url=mysql_real_escape_string($url);
		$data->date=$date;
		$data->wireid=$wireid; // deprecated
		$data->feedType=$feedType;
		$data->feedid=$feedid;
		return $data;
	}

	function fetchBreakingNewswire($limit=7) {
		$storyList=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Newswire WHERE feedType='wire' AND date<now() AND date > date_sub(NOW(), INTERVAL 14 DAY) ORDER BY date DESC LIMIT $limit;");
		return $storyList;
	}
	
	function cleanup($numberDays=15) {
		if ($numberDays>0) {
			// delete stories in FeedStories older than numberDays days
			$deleteQuery=$this->db->query("DELETE FROM Newswire WHERE date < date_sub(NOW(), INTERVAL $numberDays DAY);");			
		} else {
			// delete all feed stories 
			$deleteQuery=$this->db->query("DELETE FROM Newswire;");			
		}
	}	

	function buildStoriesTabs($current='all') {
		$tabs='<div id="subNav" class="tabs clearfix"><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		$tabs.='<li class="first"><a id="tabAllStories" href="?p=stories&o=all" class="'.($current=='all'?'selected':'').'" onclick="setNewswireTab(\'all\');return false;">All Stories</a></li>';
		$tabs.='<li ><a id="tabRawFeeds" href="?p=stories&o=raw" class="'.($current=='raw'?'selected':'').'" onclick="setNewswireTab(\'raw\');return false;">Raw Feeds</a></li>';
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
		return $tabs;
	}	

	function fetchNewswire($option='all',$filter='all',$memberFriends='',$currentPage=1) {		
		$code.='<input type="hidden" id="option" value="'.$option.'">';
		$code.='<div id="navFilter"><input type="hidden" id="filter" value="'.$filter.'">';
		if ($option=='all') {
			$code.=$this->fetchStoryFilter($filter);
		}	
		$code.='<!-- end navFilter --></div>';
		$code.='<div id="storyList">';
		$code.=$this->fetchNewswirePage($option,$filter,$memberFriends,$currentPage);
		$code.='<!-- end storyList --></div>';
		$code.='<input type="hidden" id="pagingFunction" value="fetchNewswirePage">';				
		return $code;
	}

	function fetchStoryFilter($filter='all') {
		$code='<div class="subFilter">Filter by: <a id="storyFilterAll" class="feedFilterButton '.(($filter=='all')?'selected':'').'" href="#" onclick="setNewswireFilter(\'all\');return false;">All stories</a>';
		$code.='<a id="storyFilterSponsor" class="feedFilterButton '.(($filter=='sponsor')?'selected':'').'" href="#" onclick="setNewswireFilter(\'sponsor\');return false;">'.SITE_SPONSOR.'</a>'; 
		$code.='<a id="storyFilterFriends" class="feedFilterButton '.(($filter=='friends')?'selected':'').'" href="#" onclick="setNewswireFilter(\'friends\');return false;">Friends</a>';
        $code.='</div><!--end "subfilter"-->';
		return $code;		
	}

	function fetchRawStories() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'newswire');
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);				
		$this->templateObj->db->result=$this->templateObj->db->query("select id,title,caption,source,url,wireid	from Newswire where (
		   select count(*) from Newswire as f where f.wireid= Newswire.wireid and f.id > Newswire.id 
		) < 5 ORDER BY id DESC;");
		$rowTotal=$this->templateObj->db->countFoundRows();
		$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
		$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', 500));		
		$this->templateObj->db->setTemplateCallback('safeCaption', array($this, 'encodeCleanString'), array('caption', 500));
		$this->templateObj->db->setTemplateCallback('safeUrl', array($this, 'encodeUrl'), 'url');
		$code=$this->templateObj->mergeTemplate($this->templateObj->templates['rawList'],$this->templateObj->templates['autoItem']);			
		return $code;
	}
	
	function encodeCleanString($str,$cnt) {
		return urlencode(substr(strip_tags($str), 0, ($cnt - 1)));
	}
	
	function encodeUrl($url) {
		return urlencode($url);
	}
	
	function fetchNewswirePage($option='all',$filter='all',$memberFriends='',$currentPage=1) {
		// filter = show posted stories or rss feeds
		// sort for posted stories: sort by votes, sort by date
		// sort for rss feeds: (none)
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'newswire');
		// load stories from cache or create a new one, unless its the friends' stories
		if ($filter=='friends') $neverCache=true; else $neverCache=false;
		$cacheName='nw_'.$option.'_'.$filter.'_'.$currentPage;
		if ($this->templateObj->checkCache($cacheName,15) AND !$neverCache) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {
			require_once(PATH_CORE.'/classes/utilities.class.php');
			$this->utilObj=new utilities($this->db);				
			$startRow=($currentPage-1)*ROWS_PER_PAGE;
			switch ($option) {
				case 'raw':
					$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Newswire WHERE date<now() AND date > date_sub(NOW(), INTERVAL ".AGE_STORY_MAX_DAYS." DAY) ORDER BY date DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
					$rowTotal=$this->templateObj->db->countFoundRows();
					$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,ROWS_PER_PAGE,'?p=stories&o=raw&currentPage=');			
					$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
					$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', 130));
					//$this->templateObj->db->setTemplateCallback('cmdAdd', array($this->templateObj, 'commandAdd'), 'siteContentId');
					//$this->templateObj->db->setTemplateCallback('cmdRead', array($this->templateObj, 'commandRead'), 'permalink');
					$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['rawList'],$this->templateObj->templates['rawItem']);			
				break;
				default:
					switch ($filter) {						
						case 'sponsor':
							$moderatorList=$this->db->buildIdList("SELECT userid AS id FROM User WHERE isSponsor=1;");
							if ($moderatorList<>'') {
								// to do - remove broken postedbyid = userid join
								$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE FIND_IN_SET(Content.userid,'".$moderatorList."') AND date>date_sub(NOW(), INTERVAL ".AGE_STORY_MAX_DAYS." DAY) ORDER BY date DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
								$rowTotal=$this->templateObj->db->countFoundRows();
								$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,ROWS_PER_PAGE,'?p=stories&o=all&filter=all&currentPage=');			
								$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
								$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
								$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'postedById');
								$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), 'postedById');
								$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', 130));
								$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
								$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
								$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);										
							} else {
								$code.='There are no sponsors identified for this site.';							
							}
						break;
						case 'friends':
							if ($memberFriends<>'') {
								// to do - remove broken postedbyid = userid join
								//$this->templateObj->db->log("SELECT SQL_CALC_FOUND_ROWS Content.*,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE FIND_IN_SET(Content.userid,".$memberFriends.") AND date>date_sub(NOW(), INTERVAL ".AGE_STORY_MAX_DAYS." DAY) ORDER BY date DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
								$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Content WHERE FIND_IN_SET(userid,'".$memberFriends."') AND date>date_sub(NOW(), INTERVAL ".AGE_STORY_MAX_DAYS." DAY) ORDER BY date DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
								$rowTotal=$this->templateObj->db->countFoundRows();
								if ($rowTotal>0) {
									$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,ROWS_PER_PAGE,'?p=stories&o=all&filter=all&currentPage=');			
									$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
									$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
									$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'postedById');
									$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), 'postedById');
									$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', 130));
									$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
									$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
									$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);																		
								} else {
									$code.='Your friends have not posted a story yet!';
								}
							} else {
								$code.='None of your friends have joined '.SITE_TITLE;							
							}
						break;
						default:
							// to do - remove broken postedbyid = userid join
							$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid) WHERE date>date_sub(NOW(), INTERVAL ".AGE_STORY_MAX_DAYS." DAY) AND isBlocked=0 ORDER BY date DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
							$rowTotal=$this->templateObj->db->countFoundRows();
							$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,ROWS_PER_PAGE,'?p=stories&o=all&filter=all&currentPage=');			
							$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
							$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
							$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'postedById');
							$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), 'postedById');
							$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', 130));
							$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
							$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
							//$this->templateObj->db->setTemplateCallback('cmdAdd', array($this->templateObj, 'commandAdd'), 'siteContentId');
							// $this->templateObj->db->setTemplateCallback('cmdRead', array($this->templateObj, 'commandRead'), 'permalink');						
							$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);			
						break;
					}
				break;
			}
			$code.=$pagingHTML;
			if (!$neverCache) $this->templateObj->cacheContent($cacheName,$code); // note: conditionally write the file, if not always needed
		}					
		return $code;
	}		

	function fetchPublisherStories($option='all',$filter='default',$currentPage=1) { /*for publisher/email attachment*/
		// to do: build filter and sort controls - outside of ajax...see sample code in newscloud facebook app or ask Jeff 
		// filter = show posted stories or rss feeds
		// sort for posted stories: sort by votes, sort by date
		// sort for rss feeds: (none)
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'publisher');				
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);				
		$startRow=($currentPage-1)*ROWS_PER_PAGE;
		$makeList=1;
		switch ($filter){
			case 'user':
				$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Content WHERE userid=(SELECT userid FROM UserInfo WHERE fbId=".$_POST['fb_sig_user'].") AND date>date_sub(NOW(), INTERVAL ".AGE_STORY_MAX_DAYS." DAY) ORDER BY date DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
				$rowTotal=$this->templateObj->db->countFoundRows();
				if ($rowTotal<1) {
					$makeList=0;
					$code.='You have not posted a story yet!';
				}
			break;
			default:
				$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Content WHERE date>date_sub(NOW(), INTERVAL ".AGE_STORY_MAX_DAYS." DAY) ORDER BY date DESC LIMIT $startRow,".ROWS_PER_PAGE.";");
			
			break;
		}
		if ($makeList){
			$rowTotal=$this->templateObj->db->countFoundRows();
			$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,ROWS_PER_PAGE,'?p=stories&o=all&filter=default&currentPage=');			
			$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getAbsoluteStoryImage'), 'imageid');
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
			$this->templateObj->db->setTemplateCallback('submitBy', array($this->templateObj, 'submitBy'), 'postedByName');
			$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), 'caption');
			$this->templateObj->db->setTemplateCallback('storyLink', array($this->templateObj, 'getAbsoluteStoryLink'), 'siteContentId');
			//
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);
		}

		return $code;			
	}	
	
	function fetchPostedStoryInfo($cid){ /*for publisher/email attachment*/
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'publisher');				
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);			

		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Content WHERE  siteContentId=".$cid." LIMIT 1");
		$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
		$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
		$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), 'caption');
		$code=$this->templateObj->mergeTemplate($this->templateObj->templates['postedList'],$this->templateObj->templates['postedItem']);

		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS Content.*,ContentImages.url FROM Content LEFT JOIN ContentImages ON (Content.siteContentId=ContentImages.siteContentId) WHERE  Content.siteContentId=".$cid." LIMIT 1");
		$storyInfo=$this->templateObj->db->read();
		
		$retArray=array('title'=>trim($storyInfo->title),
						'storyLink'=>URL_CANVAS.'?p=read&o=comments&cid='.$cid.'&record',
						'image'=>$storyInfo->imageUrl,
						'story'=>$code
						);		

		return $retArray;
	}
	
	/*profile box stuff*/
	function fetchFeaturedStoriesForProfileBox(){
		//echo 'module active is '.MODULE_ACTIVE;

		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'profile');
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);

		$this->templateObj->db->result = $this->templateObj->db->query("SELECT * FROM FeaturedTemplate WHERE id = 1");
		$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getAbsoluteStoryImage'), 'imageid');
		$this->templateObj->db->setTemplateCallback('storyLink', array($this->templateObj, 'getAbsoluteStoryLink'), 'siteContentId');
		$featured = mysql_fetch_assoc($this->templateObj->db->result);
		
		$code = '';
		$story_ids = array();
		foreach ($featured as $field => $value)
			if (preg_match('/^story_([0-9]+)_id$/', $field))
				$story_ids[] = $value;
		
		$story_ids = array_slice($story_ids, 0, 3);
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT * FROM Content WHERE siteContentId IN (".join(',', $story_ids).")");	
		$code=$this->templateObj->mergeTemplate($this->templateObj->templates['storyList'],$this->templateObj->templates['storyItem']);
		$code.='end of fetch featured stories';
		//echo 'templat eload is : '; var_dump($this->templateObj->templates);
		return $code;	
	}
	
	function makeStoryLink($story){
		$cid = $story['siteContentId'];
		$title = $story['title'];
		return "<a href=\"".URL_CANVAS."/?p=read&cid=$cid\">$title</a>";
	}
}	
?>