<?php

require_once(PATH_CORE .'/classes/dbRowObject.class.php');

class ContentRow extends dbRowObject
{
	
}


class ContentTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Content";
	static $idname = "siteContentId";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "ContentRow";
		
	static $fields = array(				
		"contentid" => "INT(11) default 0",
		"title" => "VARCHAR(255) default ''",
		"caption" => "TEXT default ''",
		"source" => "VARCHAR (150) default ''",
		"url" => "VARCHAR(255) default ''",
		"permalink" => "VARCHAR(255) default ''",	
		"postedById" => "INT(11) default 0",
		"postedByName" => "VARCHAR(255) default ''",
		"date" => "DATETIME",
		"score" => "INT(4) default 0",
		"numComments" => "INT(2) default 0",
		"isFeatured" => "TINYINT(1) default 0",	
		"userid" => "INT(11) default 0",
		"imageid" => "INT(11) default 0",
		"videoIntroId" => "INT(11) default 0",
		"isBlocked" => "TINYINT(1) default 0",
		"videoid" => "INT(11) default 0",
		"widgetid" => "INT(11) default 0",
		"isBlogEntry" => "TINYINT(1) default 0",
		"isFeatureCandidate" => "TINYINT(1) default 0"								
		);
	static $keydefinitions = array(); 
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
	}	
	// although many functions will be duplicated between table subclasses, having a parent class gets too messy	
	function getRowObject()
	{	
		$classname = self::$dbRowObjectClass; 
		return new $classname($this->db, self::$tablename, array_keys(self::$fields), self::$idname);
	}		
	
	// generic table creation routine, same for all *Table classes 		
	static function createTable($manageObj)
	{			
		$manageObj->addTable(self::$tablename,self::$idname,self::$idtype,"MyISAM");
		foreach (array_keys(self::$fields) as $key)
		{
			$manageObj->updateAddColumn(self::$tablename,$key,self::$fields[$key]);
		}
		foreach (self::$keydefinitions as $keydef)
		{
			$manageObj->updateAddKey(self::$tablename,$keydef[0], $keydef[1], $keydef[2], $keydef[3]);
		}
		
		
	
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	
		
}



class content {
	
	var $db;
	var $templateObj;
		
	function content(&$db=NULL) {
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}

	function getById($siteContentId=0,$returnQuery=false) {
		$q=$this->db->queryC("SELECT * FROM Content WHERE siteContentId=$siteContentId;");
		if ($returnQuery) return $q;
		if ($q!==false) {
			$story=$this->db->readQ($q);
			return $story;
		} else
			return false;	
	}
	
	function getImage($siteContentId=0,$returnQuery=false) {
		$q=$this->db->queryC("SELECT * FROM ContentImages WHERE siteContentId=$siteContentId;");
		if ($returnQuery) return $q;
		if ($q!==false) {
			$image=$this->db->readQ($q);
			return $image;
		} else
			return false;	
	}
	
	function getByPermalink($permalink='',$returnQuery=false) {
		$q=$this->db->queryC("SELECT * FROM Content WHERE permalink='$permalink';");
		if ($returnQuery) return $q;
		if ($q!==false) {
			$story=$this->db->readQ($q);
			return $story;
		} else
			return false;	
	}

	function createStoryContent($userinfo=NULL,$info=NULL,$mode='link') {
		// post a story from the post story form
		// build source from domain - to do : improve this with source objects table
		$urlParts=parse_url($info->url);
		$info->source=$urlParts['host'];
		// create permalink	
		$info->permalink=$this->buildPermalink($info->title);
		//$this->db->log($info->permalink);
		// serialize the content
		// mode = link for third party web site story link and blog for blog posts		
		if ($mode=='link')
			$isBlogEntry=0;
		else
			$isBlogEntry=1;
		$story=$this->serialize(0,$info->title,$info->caption,$info->source,$info->url,$info->permalink,$userinfo->ncUid,$userinfo->u->name,$userinfo->userid,'',$userinfo->votePower,0,0,$info->imageUrl,0,$isBlogEntry,$info->isFeatureCandidate);
		// post wire story to content
		$siteContentId=$this->add($story);
		if ($info->videoEmbed<>'') {
			// add video if it exists
			require_once(PATH_CORE .'/classes/video.class.php');
			$videoURL = videos::getVideoURLFromEmbedCodeOrURL(stripslashes($info->videoEmbed));			
			if (videos::validateVideoURL($videoURL))
			{
				$vt = new VideoTable($this->db);
				// create new video
				$videoid = $vt->createVideoForContent($userinfo->userid,$videoURL,"Video for story $siteContentId");
				if (is_numeric($videoid))
					$this->db->update("Content","videoid=$videoid","siteContentId=$siteContentId"); // store video id in content table				
			} else
			{
				// error on video, should have been picked up by validate
			}	
		}		
		return $siteContentId;
	}								
	
	function buildPermalink($title) {
		if (strlen($title)>100) {
			$title=substr($title,0,100);
		}
		$removeList = array(" ", "\'", "\:");
		$permalink = str_replace($removeList, "_", $title);
		$permalink = preg_replace("/[^a-zA-Z0-9s_]/", "", $permalink);
		$permalink = str_replace("__", "_", $permalink);				
		$cnt=0;
		while ($cnt<10) {			
			$checkPerma=$this->db->query("SELECT contentid FROM Content WHERE permalink='$permalink' LIMIT 1;");
			if ($this->db->countQ($checkPerma)>0) {
				$cnt+=1;
				$permalink=$permalink.'_'.strval(rand(100,999));
			} else {
				break;
			}
		}
		return $permalink;	
	}
	
	function add($story) {
		// check for duplicate
		$chkDup=$this->db->queryC("SELECT siteContentId FROM Content WHERE url='$story->url';");
		if (!$chkDup) {
			// insert the story into the table
			$this_query=$this->db->insert("Content","contentid,title,caption,source,url,permalink,postedById,PostedByName,userid,date,score,isFeatured,imageid,videoid,isBlogEntry,isFeatureCandidate",
				"$story->contentid,'$story->title','$story->caption','$story->source','$story->url','$story->permalink',$story->postedById,'$story->postedByName',$story->userid,'$story->date',$story->score,$story->isFeatured,$story->imageid,$story->videoid,$story->isBlogEntry,$story->isFeatureCandidate");
			$q=$this->db->query("SELECT siteContentId FROM Content WHERE url='$story->url' AND permalink='$story->permalink';");
			$data=$this->db->readQ($q);			
			$contentImageQuery = $this->db->insert("ContentImages", "url, siteContentId, date", "'$story->imageUrl', $data->siteContentId, NOW()");
			$imageid=$this->db->getId();
			$this->db->update("Content","imageid=$imageid","siteContentId=$data->siteContentId");
		} else {
			$data=$this->db->readQ($chkDup);
		}
		return $data->siteContentId; // better than get last id in case of error prone content add	
	}
	
	function updateCommentsTable($contentid=0,$siteContentId=0) {
		// updates the comments table with the latest contentid
		if ($contentid==0 OR $siteContentId==0) return false;
		$this->db->update("Comments","contentid=$contentid","siteContentId=$siteContentId");
	}

	function postComment($comment = false,&$app=NULL) {
		if (!$comment || $comment['siteContentId'] == 0)
			return false;

		$userInfo = mysql_fetch_assoc($this->db->query("SELECT * FROM User WHERE userid={$comment['userid']}"));
		$result = $this->db->insert('Comments', 'siteContentId, postedByName, postedById, userid, date, comments, videoid',
				sprintf("%s,'%s',%s,%s,now(),'%s','%s'", $comment['siteContentId'], $userInfo['name'], $comment['fbId'], $comment['userid'], $comment['comments'], $comment['videoid']));
		if (is_numeric($result)) {
			$story=$this->getById($comment['siteContentId']);
			// send out notifications about the comment
			require_once(PATH_CORE .'/classes/comments.class.php');
			$comObj=new comments($this->db);
			$comObj->notifyOthers($comment,$story,$app);			
		}
		return $result;
	}
	
	function updateCommentCount($siteContentId) {
		$this->db->update("Content","numComments=numComments+1","siteContentId=$siteContentId");
	}

	function serialize($contentid=0,$title='',$caption='',$source='',$url='',$permalink='',$postedById=0,$postedByName='',$userid=0,$date='',$score=0,$isFeatured=0,$imageid=0, $imageUrl='',$videoid=0,$isBlogEntry=0,$isFeatureCandidate=0) {
		// creates an object for an action
		$data= new stdClass;
		$data->contentid = $contentid;
		$data->title=$title;
		$data->caption=$caption;
		$data->source=$source;
		$data->url=$url;
		$data->imageUrl=$imageUrl;
		$data->permalink = $permalink;
		$data->postedById = $postedById;
		$data->postedByName = $postedByName;
		$data->userid=$userid;
		if ($date=='')
			$date=date("Y-m-d H:i:s");		
		$data->date=$date;
		$data->score=$score;
		$data->isFeatured=$isFeatured;
		$data->imageid=$imageid;
		$data->videoid=$videoid;
		$data->isBlogEntry=$isBlogEntry;
		$data->isFeatureCandidate=$isFeatureCandidate;
		if (!is_numeric($data->score)) $data->score=1;
		if (!is_numeric($data->postedById)) $data->postedById=0;
		return $data;
	}
	
	function fetchUpcomingStories($excludeIdList='',$numStories=7) {
		$query=null;
		$numHours=12;
		$failSafeDays=14;
		if ($excludeIdList<>'') {
			$excludeStr="NOT FIND_IN_SET(contentid,'$excludeIdList') AND";
		} else {
			$excludeStr='';
		}
		// this loops makes sure there are always enough top stories even if site is inactive
		while (is_null($query) OR $this->db->countQ($query)<$numStories) {
			$query=$this->db->query("SELECT * FROM Content WHERE $excludeStr date>date_sub(NOW(), INTERVAL $numHours HOUR) ORDER BY score DESC LIMIT $numStories;");			
			$numHours+=12;
			// prevent permanent loop
			if ($numHours>168) break;
		}
		// failsafe
		if ($this->db->countQ($query)<$numStories) {
			// get stories from the past week
			$query=$this->db->query("SELECT * FROM Content WHERE $excludeStr date>date_sub(NOW(), INTERVAL $failSafeDays DAY) ORDER BY score DESC LIMIT $numStories;");		
		}
		// extra failsafe
		if ($this->db->countQ($query)<$numStories) {
			// get stories from the past week
			$failSafeDays*=2;
			$query=$this->db->query("SELECT * FROM Content WHERE $excludeStr date>date_sub(NOW(), INTERVAL $failSafeDays DAY) ORDER BY score DESC LIMIT $numStories;");		
		}
		return $query;	
	}
	
	function fetchRecentStories($numStories=7) {
		// to do: excludeStr list of other stories
		$query=$this->db->query("SELECT * FROM Content WHERE isBlocked=0 ORDER BY date DESC LIMIT $numStories;");
		return $query;	
	}
	
	function fetchRecentStoryList($numDays=14,$limit=99,$excludeLocal=false) {
		// returns contentid of stories in the past number of days
		// currently used to sync votes and comments	
		if ($excludeLocal)	
			$idList=$this->db->buildIdList("SELECT contentid as id FROM Content WHERE isBlocked=0 AND contentid<>0 AND date>date_sub(NOW(), INTERVAL $numDays DAY) ORDER BY date DESC LIMIT $limit;");
		else
			$idList=$this->db->buildIdList("SELECT contentid as id FROM Content WHERE isBlocked=0 AND date>date_sub(NOW(), INTERVAL $numDays DAY) ORDER BY date DESC LIMIT $limit;");	
		return $idList;
	}	

	function updateScore($siteContentId,$score,$addTotal=true) {
		if ($addTotal)
			$this->db->update("Content","score=score+$score","siteContentId=$siteContentId");
		else {
			$this->db->update("Content","score=$score","contentid=$siteContentId");			
		}
	}
	
	function getScore($siteContentId) {
		$q=$this->db->query("SELECT score FROM Content WHERE siteContentId=$siteContentId;");
		$info=$this->db->readQ($q);
		return $info->score;	
	}


	function ajaxBanStoryPoster(&$app=null,$cid=0,$userid=0) {
		// to do - make sure user is admin
		// cid - cid of user to ban
		$contentTable = new ContentTable($this->db); 
		$c=$contentTable->getRowObject();
		$c->load($cid);	
		if ($c->userid<>$userid) {
			$this->db->update("Content","isBlocked=1","userid=".$c->userid); // block all stories by this user
			$this->db->update("Comments","isBlocked=1","userid=".$c->userid); // block all comments by this user
			require_once (PATH_CORE .'/classes/user.class.php');
			$uit = new UserInfoTable($this->db);
			$ut = new UserTable($this->db);
			$u = $ut->getRowObject();
			$ui = $uit->getRowObject();		
			$u->load($c->userid);
			// block story
			$c->isBlocked=1;
			$c->update();
			// block user
			$u->isBlocked=1;
			$u->update();
			$ui->load($c->userid);
			$facebook=$app->loadFacebookLibrary();
			$this->db->log('To Ban '.$ui->fbId);
			//$facebook->api_client->admin_banUsers($ui->fbId);
			// load facebook library - call ban api
			$code='Ban complete.';			
		} else {
			$code='Error: Trying to ban yourself again, huh?';
		}
		return $code;
	}

	function autoFeature() {
		// look up FeaturedTemplate.t to see if more than interval
		$q=$this->db->queryC("SELECT id FROM FeaturedTemplate WHERE t<DATE_SUB(NOW(),INTERVAL ".AUTOFEATURE_INTERVAL." HOUR);");
		if ($q===false) {
			//return false;
		}
		// get admin user list
		require_once(PATH_CORE.'/classes/user.class.php');
		$userTable 	= new UserTable($this->db);
		$adminList=$userTable->listAdmins();
		if ($adminList=='') {
			$this->db->log("AF Error: No admins listed");
			return false;
		}
		$cnt=0;
		$tempInterval=AUTOFEATURE_INTERVAL*2;
		while ($cnt<6) {
			// find 2 stories (posted by admin, in last n hours, not yet in log) - sort preference to images
			$qStr="SELECT Content.siteContentId,title,ContentImages.url AS imageUrl FROM Content LEFT JOIN ContentImages ON Content.siteContentId=ContentImages.siteContentId LEFT JOIN Log ON Content.siteContentId=Log.itemid AND Log.action='storyFeatured' WHERE FIND_IN_SET(Content.userid,'$adminList') AND Content.date>DATE_SUB(NOW(),INTERVAL $tempInterval HOUR) ORDER BY (Log.id IS NULL) DESC,(imageUrl<>'') DESC, Content.siteContentId DESC LIMIT 2;";
			$q1=$this->db->queryC($qStr);
			if ($this->db->cnt==2) break;
			$cnt+=1;
			$tempInterval*=2;
		}
		if ($this->db->cnt==0) return false; // not enough stories
		$numPrimaryStories=$this->db->cnt;
		$primaryStories=array();
		$excludeStoryList='';
		// load array with results
		while ($d=$this->db->readQ($q1)) {
			$primaryStories[]=$d;
			$excludeStoryList.=$d->siteContentId.',';
		}
		$excludeStoryList=trim($excludeStoryList,',');
		// find secondary stories
		$cnt=0;
		$tempInterval=AUTOFEATURE_INTERVAL*4;
		while ($cnt<3) {
			// 4 stories in last n hours e.g. 12/24 excluding above selections, posted by admins - no worries if featured before
			$qStr="SELECT Content.siteContentId,title,ContentImages.url AS imageUrl FROM Content LEFT JOIN ContentImages ON Content.siteContentId=ContentImages.siteContentId WHERE NOT FIND_IN_SET(Content.siteContentId,'$excludeStoryList') AND FIND_IN_SET(Content.userid,'$adminList') AND Content.date>DATE_SUB(NOW(),INTERVAL $tempInterval HOUR) ORDER BY Content.siteContentId DESC LIMIT 4;";
			$q2=$this->db->queryC($qStr);
			if ($this->db->cnt==4) break;
			$cnt+=1;
			$tempInterval*=2;
		}
		$numSecondaryStories=$this->db->cnt;
		$secondaryStories=array();
		// load array with results
		while ($d=$this->db->readQ($q2)) {
			$secondaryStories[]=$d;
		}
		$autoLog=PATH_SERVER_LOGS.'auto.log';
		// log changes to auto.log
		$this->db->log('Time: '.date('H:i').' on '.date('m-d'),$autoLog);
		$this->db->log('Template: '.$template,$autoLog);		
		// from story arrays, determine best template e.g. images
		// no images
		$story_1_id=0; $story_2_id=0; $story_3_id=0; $story_4_id=0; $story_5_id=0; $story_6_id=0;
		if ($primaryStories[0]->imageUrl=='') { // first always likeliest to have image due to sort
			$template='template_1';
			$story_1_id=$primaryStories[0]->siteContentId;
			$this->db->log($primaryStories[0],$autoLog);
		} else if ($primaryStories[0]->imageUrl<>'' AND $primaryStories[1]->imageUrl<>'') {			// two images
			// less than two secondary stories
			$x=rand(0,1);
			if ($x==0)
				$template='template_3'; // two images version 1
			else
				$template='template_4'; // two images version 2
			$story_1_id=$primaryStories[0]->siteContentId;
			$story_4_id=$primaryStories[1]->siteContentId;			
			$this->db->log($primaryStories[0],$autoLog);
			$this->db->log($primaryStories[1],$autoLog);
			/*if ($numSecondaryStories<4) {
			} else {
				// 4 secondary stories			
				$template='template_5';
				$story_1_id=$primaryStories[0]->siteContentId;
				$story_4_id=$primaryStories[1]->siteContentId;	
				$story_2_id=$secondaryStories[0]->siteContentId;
				$story_3_id=$secondaryStories[1]->siteContentId;			
				$story_5_id=$secondaryStories[2]->siteContentId;
				$story_6_id=$secondaryStories[3]->siteContentId;			
				$this->db->log($primaryStories[0],$autoLog);
				$this->db->log($primaryStories[1],$autoLog);
				$this->db->log($secondaryStories,$autoLog);
			}
			*/
		} else  { 	//  one image if ($primaryStories[0]->imageUrl<>'') - first always likeliest to have image due to sort  
			if ($numSecondaryStories>=2) {
				$template='template_2'; // one image, two story links
				$story_1_id=$primaryStories[0]->siteContentId;
				$story_2_id=$secondaryStories[0]->siteContentId;
				$story_3_id=$secondaryStories[1]->siteContentId;			
				$this->db->log($primaryStories[0],$autoLog);
				$this->db->log($secondaryStories[0],$autoLog);
				$this->db->log($secondaryStories[1],$autoLog);
			} else {
				$template='template_1';
				// less than two secondaryStories			
				$story_1_id=$primaryStories[0]->siteContentId;				
				$this->db->log($primaryStories[0],$autoLog);
			}
		}
		// add top two to the log as having been featured
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($this->db);
		for ($i=1;$i<=6;$i++) {
			$str = '$story_'.$i.'_id';
			eval("\$storyid = \"$str\";");
			if ($storyid>0) {
				// only log new ones
				$q=$this->db->queryC("SELECT id FROM Log WHERE action='storyFeatured' AND itemid=$storyid LIMIT 1;");
				if ($q===false) {
					$logItem=$logObj->serialize(0,0,'storyFeatured',$storyid); 			
					$logObj->add($logItem);							
				}
			}
		}		
		// taken from save_template.php in console
		$this->db->query("UPDATE Content set isFeatured = 0 WHERE isFeatured = 1");
		// set new features
		$sql = sprintf("REPLACE INTO FeaturedTemplate SET id = 1, template = '%s', story_1_id = %s, story_2_id = %s, story_3_id = %s, story_4_id = %s, story_5_id = %s, story_6_id = %s", $template, $story_1_id, $story_2_id, $story_3_id, $story_4_id, $story_5_id, $story_6_id);
		$this->db->query($sql);
		$this->db->query("UPDATE Content set isFeatured = 1 WHERE siteContentId IN ($story_1_id, $story_2_id, $story_3_id, $story_4_id, $story_5_id, $story_6_id)");
		// clear out the cache of the home top stories
		require_once(PATH_CORE.'/classes/template.class.php');
		$templateObj=new template($this->db);
		$templateObj->resetCache('home_feature');		
		// update twitter feed
		if (USE_TWITTER) {
			require_once (PATH_CORE.'classes/twitter.class.php');
			$twitterObj=new twitter_old($this->db);
			$twitterObj->postFeaturedStories();
		}
		return true;
	}
	
	function cleanup() {
		// delete content for deleted users
		$this->db->delete("Content","NOT EXISTS (select * from User where User.userid=Content.userid)");
		// delete comments for deleted stories
		$this->db->delete("Comments","NOT EXISTS (SELECT * FROM Content  WHERE Content.siteContentId=Comments.siteContentId)");
		// to do: delete log entries related to deleted stories and content
	}
	
}	
?>