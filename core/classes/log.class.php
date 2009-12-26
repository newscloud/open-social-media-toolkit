<?php

/*
 * 
 * 	// Create the Log table
		$manageObj->addTable("Log","id","BIGINT(20) unsigned NOT NULL auto_increment","MyISAM");
 * 
 */

require_once(PATH_CORE .'/classes/dbRowObject.class.php');

class LogRow extends dbRowObject
{

	function __construct( $db, $table, $fields, $idname='id' )
  	{
 		parent::__construct($db, $table, $fields, $idname);
		$this->filterFieldFromInsertAndUpdate('t'); // prevent timestamp field from being stomped on
		
	}
	
	function insert()
	{
	
		// hack: exclude the t field because its a timestamp. bit of a hack
		//dbRowObject::$debug = true;
		// todo: make this work
		//unset($this->nonidfields['t']);
		
		//echo '<pre>'.print_r($this->nonidfields, true) . '</pre>';
		parent::insert();
		
		//$this->nonidfields['t']='';
		
	}
}


class LogTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Log";
	static $idname = "id";
	static $idtype = "BIGINT(20) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "LogRow";
		
	static $fields = array(		
		"userid1" => "BIGINT(20)default 0",
		"action" => // IMPORTANT NOTE: see mysql docs about ALTER ... MODIFY and enums before attempting to change the spelling of any fields. you will corrupt the existing data!
				"ENUM('vote','comment',
						'readStory','readWire','invite','postStory','publishWire',
						'publishStory','shareStory','referReader','referToSite',
						'postTwitter', 'signup', 'acceptedInvite',
						'redeemed', 'wonPrize', 'completedChallenge', 'addedWidget', 'addedFeedHeadlines',
						'friendSignup', 'addBookmarkTool',	'levelIncrease','sessionsRecent','sessionsHour','pageAdd','chatStory','postBlog','sendCard','askQuestion','answerQuestion','likeQuestion','likeAnswer','likeIdea','likeStuff','addStuff','storyFeatured','madePredict'
						) default 'readStory'",
		"itemid" => "INT(11) default 0",
		"itemid2" => "INT(11) default 0",
		"userid2" => "BIGINT(20) default 0",
		"ncUid" => "BIGINT(20) default 0",
		"t" => "timestamp", // DEFAULT CURRENT_TIMESTAMP", // back to what it was - default current AND on update
		"dateCreated" => "DATETIME", // a timestamp to record creation, but i'm not even going to try to let mysql handle it automatically 
		"status" => "ENUM('pending','ok','error') default 'pending'",
		"isFeedPublished" => "ENUM('pending','complete') default 'pending'"
	
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
	
	function testPopulate()
	{
		echo 'LogTable->testPopulate()<br>';
		$l = $this->getRowObject();
		
		$l->action='comment';
		$l->userid1=666;
		$l->insert();
		
	}

}

class LogExtraRow extends dbRowObject
{

}


class LogExtraTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="LogExtra";
	static $idname = "id";
	static $idtype = "BIGINT(20) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "LogExtraRow";
		
	static $fields = array(		
		"logid" => "BIGINT(20)default 0",
		"txt" => "TEXT default ''"
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


	class log {
	
		var $db; // database object

		function log(&$db=NULL) {
			if (is_null($db)) { 
				require_once('db.class.php');
				$this->db=new cloudDatabase();
			} else
				$this->db=$db;
		}
		
		function narrate($obj='') {
			// writes to smtSync.log
			$this->db->log($obj,PATH_SYNCLOGFILE);			
		}

		function fetch($id) {
			// return log item as a data object
			$this->db->query("SELECT * FROM Log WHERE id=$id;");
			$data=$this->db->read();
			return $data;
		}

		function fetchExtra($id) {
			// return log item as a data object
			$this->db->query("SELECT Log.*,LogExtra.txt FROM Log LEFT JOIN LogExtra ON Log.id=LogExtra.logid   WHERE Log.id=$id;");
			$data=$this->db->read();
			return $data;
		}
		
		function transmit($timestamp=0,$limit=25) {
			require_once('utilities.class.php');		
			$utilObj=new utilities($this->db);
			// return serialized log info for synchronization
			$log=array();
			/*** Transmit votes - req. ncUid and contentid ***/			
			$votes=array();
			$x=0;
			$page=0;
			$q=$this->db->query("SELECT Log.*,User.ncUid,Content.contentid FROM Log,User,Content WHERE Log.itemid=Content.siteContentId AND contentid>0 AND User.ncUid<>0 AND Log.userid1=User.userid AND action='vote' AND status='pending' AND Content.isBlocked=0 AND User.isBlocked=0 ORDER BY Log.id DESC");			
			while ($data=$this->db->readQ($q)) {
					$votes[$x][contentid]=$data->contentid;
					$votes[$x][logid]=$data->id;
					// fetch newscloud id from userid1
					$votes[$x][uid]=$data->ncUid; // $this->lookupUid($data->userid1);
					$x+=1;
			}
			$this->narrate('Transmitting votes');
			$this->narrate($votes);
			$log['votes']=$votes;
			/*** Transmit new users - req isVerifiedEmail=1 ***/			
			$newUsers=array();
			$x=0;
			require_once('userRemoteSync.class.php');		
			$rsObj=new userRemoteSync($this->db);
			require_once (PATH_CORE.'/classes/systemStatus.class.php');
			$ssObj=new systemStatus($this->db);
			$partnerid=$ssObj->getState('partnerid');			
			$q=$rsObj->findUnlinkedAccounts();
			while ($data=$this->db->readQ($q)) {
				$newUsers[$x][partnerid]=$partnerid;
				$newUsers[$x][userid]=$data->userid;
				$newUsers[$x][name]=$data->name;
				$newUsers[$x][email]=$data->email;
				$newUsers[$x][fbId]=$data->fbId;
				$newUsers[$x][city]=$data->city;
				$x+=1;
			}
			$log['newUsers']=$newUsers;			
			$this->narrate('New users to sync with NewsCloud');
			$this->narrate($newUsers);
			// build for users to synchronize - changed bio, changed user level - to do : facebook image
			/*** Transmit users who change their user level ***/			
			$levelChanges=array();
			$x=0;									  							
			require_once('userRemoteSync.class.php');		
			$rsObj=new userRemoteSync($this->db);
			$q=$rsObj->findUserLevelIncreases($timestamp);
			while ($data=$this->db->readQ($q)) {
				$levelChanges[$x][logid]=$data->id;
				$levelChanges[$x][userid]=$data->userid;
				$levelChanges[$x][uid]=$data->ncUid;
				$levelChanges[$x][userlevel]=$data->itemid;
				$x+=1;
			}
			$log['levelChanges']=$levelChanges;		
			$this->narrate('User Level Increases');
			$this->narrate($levelChanges);
			/*** Transmit read story records - req. ncUid and contentid ***/			
			$q=$this->db->query("SELECT Log.id,Content.contentid,User.ncUid FROM Log,Content,User WHERE action='readStory' AND status='pending' AND User.userid=Log.userid1 AND Log.itemid=Content.siteContentId  AND User.ncUid>0 AND Content.contentid>0 AND Content.isBlocked=0 AND User.isBlocked=0 ORDER BY id DESC LIMIT $limit;");
			$readStory=array();
			$x=0;
			while ($data=$this->db->readQ($q)) {
				$readStory[$x][contentid]=$data->contentid;
				$readStory[$x][logid]=$data->id;
				$readStory[$x][uid]=$data->ncUid;
				$x+=1;
			}
			$log['readStory']=$readStory;
			/*** Transmit published story to journal - req. ncUid and contentid ***/
			$q=$this->db->query("SELECT Log.id,Content.contentid,User.ncUid FROM Log,Content,User WHERE action='publishStory' AND status='pending' AND User.userid=Log.userid1 AND Log.itemid=Content.siteContentId  AND User.ncUid>0 AND Content.contentid>0 AND Content.isBlocked=0 AND User.isBlocked=0 ORDER BY id DESC LIMIT $limit;");
			$pubStory=array();
			$x=0;
			while ($data=$this->db->readQ($q)) {
					$pubStory[$x][contentid]=$data->contentid;
					$pubStory[$x][logid]=$data->id;
					$pubStory[$x][uid]=$data->ncUid;
					$x+=1;
			}
			$log['pubStory']=$pubStory;
			/*** Transmit posted raw stories - req ncUid ***/
			$q=$this->db->queryC("SELECT Log.id,Log.itemid,User.ncUid FROM Log,User WHERE action='publishWire' AND status='pending' AND User.userid=Log.userid1 AND User.ncUid>0 AND User.isBlocked=0 ORDER BY id DESC LIMIT $limit;");
			$pubWire=array();
			$x=0;
			while ($data=$this->db->readQ($q)) {
				$wireQuery=$this->db->queryC("SELECT * FROM Newswire WHERE id=$data->itemid;");
				if ($wireQuery!==false) {
					$wi=$this->db->readQ($wireQuery);
					$pubWire[$x][logid]=$data->id;
					$pubWire[$x][uid]=$data->ncUid;
					// fetch wire story url					
					$pubWire[$x][url]=$wi->url;
					$pubWire[$x][siteContentId]=$data->itemid2;
					$pubWire[$x][wireid]=$wi->wireid;
					switch ($wi->feedType) {
						default: // wire
							$pubWire[$x][feedType]='wire';
						break;
						case 'blog':
							$pubWire[$x][feedType]='blog';
							$pubWire[$x][title]=$wi->title;
							$pubWire[$x][date]=$wi->date;							
							$pubWire[$x][caption]=$utilObj->shorten($wi->caption,500);
						break;
					}
					$x+=1;
				} else {
					// story deleted from newswire
					$this->db->update("Log","status='error'","id=$data->id");
				}				
			}
			$log['pubWire']=$pubWire;
			/*** Transmit posted stories, a new user posted story - req ncUid ***/
 			$q=$this->db->queryC("SELECT Log.id,Log.itemid,User.ncUid FROM Log,User WHERE action='postStory' AND status='pending' AND User.userid=Log.userid1 AND User.ncUid>0 AND User.isBlocked=0 ORDER BY id DESC LIMIT $limit;");
			if ($q!==false) {
				//$this->db->log('inside poststory');
				require_once (PATH_CORE.'/classes/content.class.php');
				$cObj=new content($this->db);			
				$postStory=array();
				$x=0;
				while ($data=$this->db->readQ($q)) {
					$this->db->log($data->id);
					// fetch contentid from siteContentid					
					$si=$cObj->getById($data->itemid);
					if ($si!==false) {
						$postStory[$x][logid]=$data->id;
						$postStory[$x][siteContentId]=$data->itemid;					
						$postStory[$x][uid]=$data->ncUid;						
						$postStory[$x][title]=$si->title;
						$postStory[$x][url]=$si->url;					
						$postStory[$x][date]=$si->date;							
						$postStory[$x][caption]=htmlentities($utilObj->shorten(strip_tags($si->caption),500),ENT_QUOTES);
						$imageProps=$cObj->getImage($data->itemid);
						if ($imageProps!==false)					
							$postStory[$x][imageurl]=$imageProps->url;
						$x+=1;
					}
				}
				$log['postStory']=$postStory;								
			}
			$this->narrate('Posting stories to NewsCloud');
			$this->narrate($postStory);					
 			/*** Transmit comments - req. ncUid and contentid ***/			
			$comments=array();
			$q=$this->db->query("SELECT Log.id,Comments.comments,Comments.siteCommentId,Content.contentid,User.ncUid FROM Log,Comments,Content,User WHERE Log.itemid=Comments.siteCommentId AND Comments.siteContentId=Log.itemid2 AND Content.siteContentId=Log.itemid2 AND  Content.contentid>0 AND Log.userid1=User.userid AND User.ncUid>0   AND action='comment' AND status='pending' AND Content.isBlocked=0 AND User.isBlocked=0 AND Comments.videoid=0 ORDER BY id DESC LIMIT $limit;");
			$x=0;
			while ($data=$this->db->readQ($q)) {
				$comments[$x][uid]=$data->ncUid;
				$comments[$x][contentid]=$data->contentid;
				$comments[$x][logid]=$data->id;
				$comments[$x][comments]=htmlentities($data->comments,ENT_QUOTES);
				$comments[$x][siteCommentId]=$data->siteCommentId;
				$x+=1;						
				// fetch contentid from siteContentid
				//$contentid=$this->lookupContentId($data->itemid);
				//if ($contentid!==false AND $contentid<>0) {
					// fetch newscloud id from userid1
					//$ncUid=$this->lookupUid($data->userid1);
					//if ($ncUid<>0) {
					//}
				//}
			}
			$log['comments']=$comments;						
			$this->narrate('Transmitting comments');
			$this->narrate($comments);
			// send serialized array out
			return serialize($log);
		}
		
		function lookupUid($userid=0) {
			$q=$this->db->query("SELECT ncUid FROM User WHERE userid=$userid;");
			$data=$this->db->readQ($q);
			return $data->ncUid;
		}

		function lookupContentId($siteContentId=0) {
			$q=$this->db->queryC("SELECT contentid FROM Content WHERE siteContentId=$siteContentId;");
			if ($q===false)
				return false;			
			$data=$this->db->readQ($q);
			return $data->contentid;
		}
		
		function receive($rx='') {
			$msg='';
			// update database based on log synchronization results
			$resp=unserialize($rx);
			// process log result and perform appropriate actions
			// process vote results
			$votes=$resp['votes'];
			if (count($votes)>0  AND is_array($votes)) {
				foreach ($votes as $item) {
					$this->processResultStatus($item[logid],$item[result]);
					$msg.=$item[logid].'->'.$item[result].'<br />';									
				}
			}
			// process comment results
			$comments=$resp['comments'];
			if (count($comments)>0  AND is_array($comments)) {
				foreach ($comments as $item) {
					$this->processResultStatus($item[logid],$item[result]);
					// sync NC commentid
					$commentid=$item[commentid];
					$siteCommentId=$item[siteCommentId];
					$this->db->update("Comments","commentid=$commentid","siteCommentId=$siteCommentId");					
					$msg.=$item[logid].'->'.$item[result].' and commentid: '.$commentid.'<br />';									
				}
			}
			// process readStory results
			$readStory=$resp['readStory'];
			if (count($readStory)>0  AND is_array($readStory)) {
				foreach ($readStory as $item) {
					$this->processResultStatus($item[logid],$item[result]);
					$msg.=$item[logid].'->'.$item[result].'<br />';									
				}
			}
			// process pubStory results
			$pubStory=$resp['pubStory'];
			if (count($pubStory)>0  AND is_array($pubStory)) {
				foreach ($pubStory as $item) {
					$this->processResultStatus($item[logid],$item[result]);
					$msg.=$item[logid].'->'.$item[result].'<br />';									
				}
			}
			// process postStory results
			$postStory=$resp['postStory'];
			if (count($postStory)>0  AND is_array($postStory)) {
				foreach ($postStory as $item) {
					$contentid=$item[contentid];
					$imageid=$item[imageid];
					$siteContentId=$item[siteContentId];
					$this->processResultStatus($item[logid],$item[result]);
					// synchronize contentid
					$this->db->update("Content","contentid=$contentid,imageid=$imageid","siteContentId=$siteContentId");
					$this->db->update("Comments","contentid=$contentid","siteContentId=$siteContentId");
					$msg.='Post story:'.$item[logid].'->'.$item[result].'<br />';									
				}
			}
			// process pubWire results
			$pubWire=$resp['pubWire'];
			if (count($pubWire)>0 AND is_array($pubWire)) {
				foreach ($pubWire as $item) {
					$siteContentId=$item[siteContentId];
					$contentid=$item[contentid];
					$this->processResultStatus($item[logid],$item[result]);
					// synchronize contentid
					$this->db->update("Content","contentid=$contentid","siteContentId=$siteContentId");
					$this->db->update("Comments","contentid=$contentid","siteContentId=$siteContentId");
					$msg.=$item[logid].'->'.$item[result].' and '.$contentid.' <= '.$siteContentId.'<br />';													
				}
			}
			
			// process registered users
			$newUsers=$resp['newUsers'];
			if (count($newUsers)>0 AND is_array($newUsers)) {
				foreach ($newUsers as $item) {
					$userid=$item[userid];
					$ncUid=$item[ncUid];
					$name=$item[name];
					$this->db->update("User","ncUid=$ncUid","userid=$userid");
					$this->db->update("Content","postedById=$ncUid","userid=$userid");
					$this->db->update("Comments","postedById=$ncUid","userid=$userid");
					$msg.=$item[userid].'->'.$item[result].' and userid '.$userid.' <=> ncUid'.$ncUid.'<br />';													
				}
			}			
			// process level changes
			$msg.='<h3>User Level Changes</h3>';
			$levelChanges=$resp['levelChanges'];
			if (count($levelChanges)>0 AND is_array($levelChanges)) {
				foreach ($levelChanges as $item) {
					$this->processResultStatus($item[logid],$item[result]);
					$msg.=$item[logid].'->'.$item[result].'<br />';									
				}
			}			
			return $msg;
		}
		
		function processResultStatus($logid=0,$result='') {
			switch ($result) {
				case 'ok':
					$this->db->update("Log","status='ok'","id=$logid");
				break;
			} 			
		} 
		
		function serialize($id=0,$userid1=0,$action='',$itemid=0,$userid2=0,$itemid2=0) {
			// creates an object for an action
			$data= new stdClass;
			$data->id=$id;
			$data->userid1=$userid1;
			$data->action=$action;
			$data->itemid=$itemid;
			$data->userid2=$userid2;
			$data->itemid2=$itemid2;
			return $data;
		}
				
		function add($log) {
			$log->id = $this->db->insert("Log","itemid,userid1,userid2,action,itemid2,dateCreated","$log->itemid,$log->userid1,$log->userid2,'$log->action',$log->itemid2,NOW()");
			$this->checkSubmitSiteChallenge($log);
		}
		
		function update($log) {
			if ($debug) $this->db->log( 'loginfo: <pre>'.print_r($log, true). '</pre>');
			// update Log row in db from data object
			$this->db->query("SELECT * FROM Log WHERE userid1=$log->userid1 AND action='$log->action' AND itemid=$log->itemid AND userid2=$log->userid2;");
			if ($this->db->count()==0) {
				// add new log
				//$log->id= $this->db->insert("Log","itemid,itemid2,userid1,userid2,action,dateCreated","$log->itemid,$log->itemid2,$log->userid1,$log->userid2,'$log->action',NOW()");
				// exhibit A why this approach is insane:
				$log->id = $this->db->insert("Log","itemid,userid1,userid2,action,itemid2,dateCreated","$log->itemid,$log->userid1,$log->userid2,'$log->action',$log->itemid2,NOW()");
				
				$this->checkSubmitSiteChallenge($log);
				
				return true;	
			} else {
				// update log
				$this->db->update("Log","userid1=$log->userid1,userid2=$log->userid2,action='$log->action',itemid=$log->itemid","id=$log->id");
				return false;
			}
		}
		
		function setStatus($id,$status='ok') {
			$this->db->update("Log","status='$status'","id=$id");
		}
		
		function setFeedPublishStatus($id,$status='complete') {
			$this->db->update("Log","isFeedPublished='$status'","id=$id");
		}
		function updateFromPublisher($log){
			$this->db->insert("Log","itemid,userid1,userid2,action,isFeedPublished","$log->itemid,$log->userid1,$log->userid2,'$log->action','complete'");			
		}

		/*
		 *  Master list of hooked site actions that can trigger challenges
		
			second column specifies logging behavior - auto challenges that we want to show as completed challenges in the feed should set to false
		 	true => dont log separately as completed challenges - useful if the action feed will show a special story already or if it isnt important to be seen on the action feed at all
		 	false => also log as completing a challenge

		*/  
		static  $siteChallengeActions = array(
						'vote' 			=> true, // in - but should it be?
						'comment' 		=> true, // in
						'invite' 		=> true, // in
						'postStory' 	=> true, // in
						'postBlog' 	=> true, // in
						'publishWire' 	=> true, // ?
						'publishStory'	=> true, // ?
						'shareStory'	=> true, // in
						'referReader'	=> true, // ?
						'postTwitter'	=> true,  // ?
						'signup'		=> true, // tested
						'addedWidget'	=> false, // which widget?
						'addedFeedHeadlines' => false, // NOT in
						'addBookmarkTool'=> true, // tested
						'friendSignup' 	=> true,  // tested (i think)
						'levelIncrease' => true,  // tested
						'chatStory' 	=> true, // new 
						);
		
	
		function checkSubmitSiteChallenge($log)
		{					
				
			//echo 'log action:' . $log->action . '<br>';
			if (!(array_search($log->action, array_keys(self::$siteChallengeActions))===false)) // lame
			{
				//echo 'found action in siteChallengeActions<br>';
				require_once(PATH_CORE .'/classes/challenges.class.php');
				$ct = new ChallengeCompletedTable($this->db);
				
				if (!$ct->submitAutomaticChallenge(
						$log->userid1, $log->action, &$statuscode,self::$siteChallengeActions[$log->action], $log->id)) // returns false if it couldnt be approved						
				{
					//echo $statuscode; // TODO: take this out when done testing
					//$this->db->log($statuscode);				
				}
				//echo $statuscode; // TODO: take this out when done testing
				//$this->db->log("checkSubmitSiteChallenge $log->action: $statuscode dontLog: ".self::$siteChallengeActions[$log->action]);
				//$this->db->log(print_r(self::$siteChallengeActions, true));				

				
				
				// update cached user vars for select site actions
				
				require_once(PATH_CORE.'/classes/user.class.php');
				$ut = new UserTable($this->db);
				$uit = new UserInfoTable($this->db);
				$user = $ut->getRowObject();
				$userinfo = $uit->getRowObject();
				switch ($log->action)
				{
				case 'invite': 
					if ($userinfo->load($log->userid1)) { $userinfo->cachedFriendsInvited++; $userinfo->update(); } break;
				case 'comment': 
					if ($user->load($log->userid1)) { $user->cachedCommentsPosted++; $user->update(); } break;					
				case 'postStory': 
				case 'postBlog':
					if ($user->load($log->userid1)) { $user->cachedStoriesPosted++; $user->update(); } break;
				default: break;	
				}
			
			}
					
		}
		
		function checkLimits($userid=0,$whereStr='',$actionStr='',$nickel=5,$hour=7,$day=25) {
			$error=false;
			$errorMsg='';
			// Make sure user has not exceeded their rate limits
			$actionLimits = array(
				'nickel'	=> $nickel,
				'hour'		=> $hour,
				'day'			=> $day
			);
			$limitSql = "SELECT COUNT(CASE WHEN t > '".date("Y-m-d H:i:s", time() - (5 * 60))."' THEN 1 ELSE null END) AS nickel, COUNT(CASE WHEN t > '".date("Y-m-d H:i:s", time() - (60 * 60))."' THEN 1 ELSE null END) as hour, COUNT(CASE WHEN t > '".date("Y-m-d 00:00:00", time())."' THEN 1 ELSE null END) as day FROM Log WHERE userid1 = $userid AND $whereStr";
			// to do - remove after debug
			$results = $this->db->query($limitSql);
			$actionTotals = mysql_fetch_assoc($results);
			if ($actionTotals['day'] >= $actionLimits['day']) {
				$error = true;
				$errorMsg = 'You have exceeded your rate limit for '.$actionStr.'. Please try again in one day.';
			} else if ($actionTotals['hour'] >= $actionLimits['hour']) {
				$error = true;
				$errorMsg = 'You have exceeded your rate limit for '.$actionStr.'. Please try again in one hour.';
			} else if ($actionTotals['nickel'] >= $actionLimits['nickel']) {
				$error = true;
				$errorMsg = 'You have exceeded your rate limit for '.$actionStr.'. Please try again in 5 mins.';
			}
			if ($error) {
				$result=array();			
				$result['error']=$error;
				$result['msg']=$errorMsg;
				return $result;				
			} else 
				return false; 			
		}
	}	 
?>