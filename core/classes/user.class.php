<?php

require_once(PATH_CORE.'/classes/dbRowObject.class.php');
class UserRow extends dbRowObject // TODO: eliminate use of old user class in code so this one can be named consistently 
{
}

class UserInfo extends dbRowObject 
{
 
  function loadFromFbId($fbId)
  {
  		return $this->load($fbId, 'fbId');
  }
  	
  function createFromUser($user, $fbId)
  {
	  	// use the userid of the existing user object to create an empty entry with specified primary key and fbId 
	
  	
  		$this->fbId = $fbId;
  		
		/* NB: in case of further buggage...
  		 * the normal insert() return value will be zero since this table does not have an autoincrement id.
  		 * therefore the return value from db->insert does not indicate success or failure...
  		 * but we can compare the return type, because the db->insert function returns an error string on failure
  		 * 
  		 * this is partially taken care of inside of dbRowObject->insert for now, but doesnt handle the error case well
  		 */
  	
  	 	return $this->insert($user->userid);
    		 
  }
  
	function updateNetworks($resp=null) {
		if (!is_null($resp)) {
			//$result['networks']['current_location'] ['city']
			if (array_key_exists("current_location",$resp['networks'][0])) {
                $this->city=$resp['networks'][0]['current_location']['city'];
                $this->state=$resp['networks'][0]['current_location']['state'];
                $this->country=$resp['networks'][0]['current_location']['country'];
                $this->zip=$resp['networks'][0]['current_location']['zip'];
	        } 		
			//$result['networks']['affiliations'][0,1,2,3] [nid],[name],[type]
			if (array_key_exists("affiliations",$resp['networks'][0]) AND count($resp['networks'][0]['affiliations'])>0) {
				$this->networks=serialize($resp['networks'][0]['affiliations']);
			}
			//$result['groups'][0,1,2,3] - [gid],[name]
			if (array_key_exists("groups",$resp) AND count($resp['groups'])>0) {
				$this->groups=serialize($resp['groups']);
			}
			$this->lastNetSync = date('Y-m-d H:i:s', time());
			return $this->update();
		}
	}
	
	function updateFriends($friends_list) // assumes its been loaded already, friends_list is an array of fbIds
	{	
		if (is_null($friends_list)) return; 		
		$this->friends = join(',',$friends_list);
		$this->numFriends = count(explode(',',$this->friends));				
		//echo 'friends<pre>'.print_r($this->friends, true).'</pre>';
		$q=$this->db->queryC("SELECT UserInfo.userid FROM UserInfo WHERE find_in_set(UserInfo.fbId,'{$this->friends}');");
		if ($q!==false) 
		{
			while($data=$this->db->readQ($q))
			{
				$memberFriends []= $data->userid;
			}
			
			//echo 'data for memberFriends<pre>'.print_r($memberFriends, true).'</pre>';
			$this->memberFriends = join(',',$memberFriends);
			$this->numMemberFriends = count(explode(',',$this->memberFriends));		
		}
		
		//$this->db->log("updateFriends for $this->userid running on friends list $friends_list, 
		//	query string is ...find_in_set(UserInfo.fbId,'{$this->friends}'), result: {$this->memberFriends} ");
	
		
		$this->lastUpdated = date('Y-m-d H:i:s', time());
		return $this->update();
	}	
	 
  
}

class UserInviteRow extends dbRowObject  
{
	function loadFromFbIdAndUserid($fbId, $userid)
	{
		return $this->loadWhere("friendFbId=$fbId AND userid=$userid");
	}

	
}


class UserTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="User";
	static $idname = "userid";
	static $idtype = "BIGINT(20) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "UserRow";
		
	static $fields = array(		

		"ncUid" => 			"BIGINT(20) default 0",
		"name" => 			"varchar(255) default ''",
		"email" => 			"varchar(255) default ''",
		"isAdmin" => 		"TINYINT(1) default 0",
		"isBlocked" => 		"TINYINT(1) default 0",	
		"votePower" => 		"INT(2) default 1",
		"remoteStatus" => 	"ENUM('noverify','verified','purged') default 'noverify'",
		"isMember" => 		"TINYINT(1) default 0", // User has signed up with the local site
		"isModerator" =>	"TINYINT(1) default 0",
		"isSponsor" =>		"TINYINT(1) default 0",
		"isBlocked" => 		"TINYINT(1) default 0", // User has been blocked by local admin
		"isEmailVerified" => 		"TINYINT(1) default 0", // User verified their email address
		"isResearcher" =>	"TINYINT(1) default 0",
		//"optInFlags" => 	"TINYINT(1) default 0", // could also use bits to store Study, Email, Profile, Feed, AppTab, etc flags for efficiency
		"acceptRules" => 	"TINYINT(1) default 0",
		"optInStudy" => 	"TINYINT(1) default 1", // user OKs conditions of data collection for study
		"optInEmail" => 	"TINYINT(1) default 1", // "" sending them emails
		"optInProfile" => 	"TINYINT(1) default 1", // "" putting box in profile
		"optInFeed" => 		"TINYINT(1) default 1", // "" publishing in feed
		//"optInAppTab" => 	"TINYINT(1) default 1", // "" adding to applications tab
		"optInSMS" => 		"TINYINT(1) default 1", // "" adding to applications tab
	
				// ... and any other opt-in things
		"dateRegistered" => "DATETIME",
		"eligibility" => 	"ENUM ('team','general','ineligible') default 'team'",
		"cachedPointTotal" => "INT(4) default 0" ,
		"cachedPointsEarned" => "INT(4) default 0" , // tracks points earned, not counting redemptions
		"cachedPointsEarnedThisWeek" => "INT(4) default 0" , // cache points earned for current week
		"cachedPointsEarnedLastWeek" => "INT(4) default 0" , // cache points earned for last week - helps with point total calculation accuracy after week has ended to have these separate
		"cachedStoriesPosted" => "INT(4) default 0",
		"cachedCommentsPosted" => "INT(4) default 0",
		"userLevel" 		=> "VARCHAR(25) default 'reader'"	
	
	);
	
		
	static $keydefinitions = array();
		// modifier, "KEY" or "INDEX", key name, key definition
		//array("UNIQUE", "KEY", "userid", "(userid)"));

	
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
	
	
	function checkEmailExist($email='') {
		$q=$this->db->queryC("SELECT userid FROM User WHERE email='$email';");
		if ($q!==false) {
			return true; // email exists
		} else 
			return false;		
	}
	
	function lookupNewsCloudId($ncUid=0) {
		$q=$this->db->queryC("SELECT * FROM User WHERE ncUid=$ncUid;");
		if ($q!==false) {
			$data=$this->db->readQ($q);
			return $data;
		} else 
			return false;	}

	function lookupUserId($userid=0) {
		$q=$this->db->queryC("SELECT * FROM User WHERE userid=$userid;");
		if ($q!==false) {
			$data=$this->db->readQ($q);
			return $data;
		} else 
			return false;	
	}

	function listAdmins() {
		$adminStr=$this->db->buildIdList("SELECT userid as id FROM User WHERE isAdmin=1;");
		return $adminStr;
	}
	
	// this can now be done with just a $user->update
	function updateNewsCloudInfo($userinfo) {
		$this->db->update("User","ncUid=$userinfo->ncUid,name='$userinfo->name',votePower=$userinfo->votePower","userid=$userinfo->userid");
	}
	
	function setLoginSession($ui) {
		// set up log in session
		$_SESSION['isLoggedIn'] = true;					
		$_SESSION['ncUid']=$ui->ncUid;				
		$_SESSION['userid']=$ui->userid;
		$_SESSION['memberName']=$ui->name;				
		$_SESSION['votePower'] = $ui->votePower;					
	}
	
	function signOut() {
		$_SESSION = array();
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (isset($_COOKIE[session_name()])) {
		   setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();		
	}

	function registerCore($ncUid=0,$name='',$email='') {
		// registers a user on the core cloud system
		//$info=$this->serialize(0,$ncUid,$name,$email);
		//$userid=$this->update($info);
		$user = new UserRow($this->db);
		$user->ncUid = $ncUid;
		$user->name = $name;
		$user->email = $email;
		$userid = $user->insert();
		
		return $userid;	
	}	
	
};

class UserInfoTable // extra, fb-specific stuff that doesnt belong in the high performance table
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="UserInfo";
	static $idname = "userid";
	static $idtype = "BIGINT(20) unsigned NOT NULL";
	static $dbRowObjectClass = "UserInfo";
		
	static $fields = array(			
		"fbId" => "BIGINT(20) default 0", // facebook profile id
		"isAppAuthorized" => "TINYINT(1) default 0", // User has signed up with the local site	
		"networkid" => "INT(11) default 0",
		"birthdate" => "DATETIME",
		"age" => "TINYINT(1) default 0",
		"rxConsentForm" =>	"TINYINT(1) default 0", // received consent form for minors		
		"gender" => "ENUM('male','female','other') ",
		"researchImportance" => "TINYINT(1) default 0",
		"dateCreated" => "timestamp",		// not accurate, this timestamp will be updated every time the record is 
		"lastUpdated" => "datetime",		// used, but semantics do not match name 
		"friends" => "TEXT default NULL", // Facebook friends - facebook ids
		"memberFriends" => "TEXT default NULL", // friends who are site members - local userids
		"numFriends" => "INT(4) default 0",
		"numMemberFriends" => "INT(4) default 0",		
		"lastInvite" => "datetime",
		"lastProfileUpdate" => "datetime",
		"lastRemoteSyncUpdate" => "datetime",
		"interests" => 		"TEXT default ''",
		"bio" => 		"TEXT default ''",
		"phone" => "VARCHAR(255) default ''",
		"address1" => "VARCHAR(255) default ''",		
		"address2" => "VARCHAR(255) default ''",
		"city" => "VARCHAR(255) default 'Unknown'",	
		"state" => "VARCHAR(255) default ''",
		"country" => "VARCHAR(255) default ''",
		"zip" => "VARCHAR(255) default ''",
		"neighborhood" => "VARCHAR(100) default ''",	
		"groups" => "TEXT default NULL", 
		"networks" => "TEXT default NULL", 
		"refuid" => "BIGINT(20) unsigned default 0",
		"lastNetSync" => "datetime", // last time locale, groups, networks updateds
		"cachedFriendsInvited" => "INT(4) default 0",
		"cachedChallengesCompleted" => "INT(4) default 0",
		"hideTipStories" => "TINYINT(1) default 0",
		"hideTeamIntro" => "TINYINT(1) default 0",
		"noCommentNotify" => "TINYINT(1) default 0",
		"lastUpdateLevels" => "datetime",
		"lastUpdateSiteChallenges" => "datetime",
		"lastUpdateCachedPointsAndChallenges" => "datetime",
		"lastUpdateCachedCommentsAndStories" => "datetime"
		
	
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

	static function getMemberUserid($user, $userinfo, $fbId) 
	{ 		
		if ($userinfo->loadFromFbId($fbId) && $user->load($userinfo->userid) && $user->isMember)
			return $userinfo->userid;
				
		return false;
	} 
	

	static function getUserid($fbId) 
	{ 	$uit = new UserInfoTable();
		$userinfo = $uit->getRowObject();	
		if ($userinfo->loadFromFbId($fbId))
			return $userinfo->userid;
				
		return false;
	} 
	
	function hideTip($userid=0,$tip='') {
		switch ($tip) {
			default:
				$this->db->update("UserInfo","hideTipStories=1","userid=$userid");
			break;
			case 'teamIntro':
				$this->db->update("UserInfo","hideTeamIntro=1","userid=$userid");
			break;
		}	
	}
	static function isFriendMember($user, $userinfo, $fbId) 
	{ 		
		if ($userinfo->loadFromFbId($fbId) && $user->load($userinfo->userid) && $user->isMember)
			return true;
				
		return false;
	} 
	
	// NOTE: not used, probably broken right now
	function mapFriends($userid, $predicate)
	{
		$userTable = new UserTable($this->db);
		$user = $userTable->getRowObject();
		
		$userinfo = $this->getRowObject();
		if (!$userinfo->load($userid))
			return false;
		
		$friends = explode(',',$userinfo->friends);
		echo 'mapFriends:friends: '.$userinfo->friends .' ; <pre>'.print_r($friends,true).'</pre>';
		
		$predfriends = array();
		foreach ($friends as $friend)
		{		

			$result =  call_user_func($predicate, $user, $userinfo, $friend); // $user and $userinfo are just container objects passed in for convenience	
			if ($result)
				$predfriends []= $result;
		}

		return $predfriends;
	}
	
	function getFbIdsForUsers($userids) // takes an array, returns an array
	{
		$userids = join(',',$userids);
		$q=$this->db->queryC("
			SELECT fbId 
			FROM UserInfo WHERE find_in_set(userid, '$userids');");
			
		$fbIds = array();

		if ($q)
		{
			while($data=$this->db->readQ($q))
			{
				$fbIds []= $data->fbId;
			}
		
		}		
		
		return $fbIds;
	}
	
	
	static function loadUserFromFbId($db, $fbId, &$user, &$userinfo)
	{
		
		$userInfoTable = new UserInfoTable($db);
		$userTable = new UserTable($db); // TODO: cache instances of the tables globally
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();
		
		// load the ui record for the fbId and if it succeeds, the corresponding user record
		return ($userinfo->loadFromFbId($fbId) && $user->load($userinfo->userid));
	}
	
	
	
	function updateUserCachedPointsAndChallenges($userid, &$user, &$userinfo, $weekOf='')
	{
		if ($user->load($userid) && $userinfo->load($userid))	
		{		
			
			/*
			 * MySQL WEEK function second parameter definition 
				Mode 	First day of week 	Range 	Week 1 is the first week É
				0 		Sunday 				0-53 	with a Sunday in this year
				1 		Monday 				0-53 	with more than 3 days this year
				2 		Sunday 				1-53 	with a Sunday in this year
				3 		Monday 				1-53 	with more than 3 days this year
				4 		Sunday 				0-53 	with more than 3 days this year
				5 		Monday 				0-53 	with a Monday in this year
				6 		Sunday 				1-53 	with more than 3 days this year
				7 		Monday 				1-53 	with a Monday in this year

				Run the big cron job shortly after the week ends...

			 */
			
			if ($weekOf=='') $weekOf='NOW()';
			
			// count points and completed challenges
			$challengeList=$this->db->query( 
				"SELECT COUNT(DISTINCT challengeid) AS numCompleted, 
						SUM(pointsAwarded) AS pointTotal, 
						SUM(IF(WEEK(dateSubmitted,1)=WEEK($weekOf,1), pointsAwarded,0)) 
							AS pointTotalThisWeek,
						SUM(IF(WEEK(dateSubmitted,1)=WEEK(DATE_SUB($weekOf, INTERVAL 1 WEEK),1), pointsAwarded,0)) 
							AS pointTotalLastWeek
						FROM ChallengesCompleted WHERE userid={$user->userid} AND pointsAwarded>0;"); 
			$chdata = $this->db->readQ($challengeList);
			
			$orderList=$this->db->query( 
				"SELECT COUNT(DISTINCT prizeid) AS numRedeemed, 
						SUM(pointCost) AS pointTotalCost 
						FROM Orders WHERE userid={$user->userid} AND pointCost>0;"); 
			$odata = $this->db->readQ($orderList);

						
			$userinfo->cachedChallengesCompleted = $chdata->numCompleted;
			$user->cachedPointsEarned = $chdata->pointTotal;
			$user->cachedPointTotal = $chdata->pointTotal - $odata->pointTotalCost;
			$user->cachedPointsEarnedThisWeek = $chdata->pointTotalThisWeek;
			$user->cachedPointsEarnedLastWeek = $chdata->pointTotalLastWeek;
			$userinfo->lastUpdateCachedPointsAndChallenges = date('Y-m-d H:i:s', time()); 

			$user->update();
			$userinfo->update();
			
			return true;
		} else
		{
			return false;
		}
	}
	
};

// OLD way -- not sure if any of this code is being called anywhere anymore...

class user {
	
	var $db;
		
	function user(&$db=NULL) {
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}

	// ************************* DJM: IN SURGERY *****************************************
	function update($user) { 
		// check for duplicate
		$chkDup=$this->db->queryC("SELECT userid FROM User WHERE userid=$user->userid;");
		if (!$chkDup) {
			// insert the user into the table
			// old way: ids assigned by SQL
			$this_query=$this->db->insert("User","ncUid,name,email,isAdmin,votePower,remoteStatus,isModerator","$user->ncUid,'$user->name','$user->email',$user->isAdmin,$user->votePower,'$user->remoteStatus',$user->isModerator");
			$newId=$this->db->getId();
					
			return $newId;	
		} else {
			$this_query=$this->db->update("User","ncUid=$user->ncUid,name='$user->name',email='$user->email',remoteStatus='$user->remoteStatus',isAdmin=$user->isAdmin,votePower=$user->votePower,isModerator=$user->isModerator","userid=$user->userid");
			return $user->userid;
		}
	}
	
	function serialize($userid=0,$ncUid=0,$name='',$email='',$isAdmin=0,$votePower=1,$remoteStatus='noverify',$isModerator=0)
				
	{
		// creates an object for a user
		$data= new stdClass;
		$data->userid = $userid;
		$data->ncUid = $ncUid;
		$data->name=$name;
		$data->email=$email;
		$data->isAdmin=$isAdmin;
		$data->votePower=$votePower;
		switch ($remoteStatus) {
			case 'verified':
			case 'enabled':
				$remoteStatus='verified';
			break;
			default:
				$remoteStatus='noverify';
			break;						
		}
		$data->remoteStatus=$remoteStatus;		
		$data->isModerator=$isModerator;		
		return $data;
	}	
	
	
	function checkEmailExist($email='') {
		$q=$this->db->queryC("SELECT userid FROM User WHERE email='$email';");
		if ($q!==false) {
			return true; // email exists
		} else 
			return false;		
	}
	
	function lookupNewsCloudId($ncUid=0) {
		$q=$this->db->queryC("SELECT * FROM User WHERE ncUid=$ncUid;");
		if ($q!==false) {
			$data=$this->db->readQ($q);
			return $data;
		} else 
			return false;	}

	function lookupUserId($userid=0) {
		$q=$this->db->queryC("SELECT * FROM User WHERE userid=$userid;");
		if ($q!==false) {
			$data=$this->db->readQ($q);
			return $data;
		} else 
			return false;	
	}
	
	function updateNewsCloudInfo($userinfo) {
		$this->db->update("User","ncUid=$userinfo->ncUid,name='$userinfo->name',votePower=$userinfo->votePower","userid=$userinfo->userid");
	}
	
	function setLoginSession($ui) {
		// set up log in session
		$_SESSION['isLoggedIn'] = true;					
		$_SESSION['ncUid']=$ui->ncUid;				
		$_SESSION['userid']=$ui->userid;
		$_SESSION['memberName']=$ui->name;				
		$_SESSION['votePower'] = $ui->votePower;					
	}
	
	function signOut() {
		$_SESSION = array();
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (isset($_COOKIE[session_name()])) {
		   setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();		
	}

	function registerCore($ncUid=0,$name='',$email='') {
		// registers a user on the core cloud system
		$info=$this->serialize(0,$ncUid,$name,$email);
		$userid=$this->update($info);
		return $userid;	
	}	
	
	// djm
}	

	
/*
	$table = "UserInvites";
	$manageObj->addTable($table,"id","INT(11) unsigned NOT NULL auto_increment","MyISAM");
	$manageObj->addColumn($table,"userid","BIGINT(20) default 0"); 
	$manageObj->addColumn($table,"friendid","BIGINT(20) default 0");
	$manageObj->addColumn($table,"dateInvited","DATETIME");
*/	


class UserInviteTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="UserInvites";
	static $idname = "id";
	static $idtype = "BIGINT(20) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "UserInviteRow";
		
	static $fields = array(		
		"userid" 		=> "BIGINT(20) default 0", 
		"friendFbId" 	=> "BIGINT(20) default 0",
		"dateInvited" 	=>"DATETIME",		
		"dateAccepted" 	=>"DATETIME"		
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
	
	
	function checkExists($userid=0,$friendFbId=0) {
		$q=$this->db->queryC("SELECT id FROM UserInvites WHERE userid=$userid AND friendFbId=$friendFbId;");
		if ($q!==false) {
			$data=$this->db->readQ($q);
			return $data->id; // invite exists
		} else 
			return false;		
	}
		
	static function getRecentlyInvitedFriends($db, $userid, $inviteInterval)
	{
		if (is_null($db)) 
		{ 
			require_once(PATH_CORE.'/classes/db.class.php');
			$db=new cloudDatabase();
		} 
		
		// get fbIds of everyone of their friends that they have invited inside the invite interval
		$idList=$db->buildIdList("
			SELECT friendFbId AS id 
			FROM UserInvites WHERE userid=$userid AND 
				UNIX_TIMESTAMP(dateInvited) > (UNIX_TIMESTAMP(NOW())-$inviteInterval);"); // invited within the invite interval means we want to exclude them
		// convert id list to array
		$friends=explode(',',$idList);		
		return $friends;			
	}
	
	function userAcceptedInvitation($userid)
	{
		//$userTable = new UserTable($this->db);
		//$user = $userTable->getRowObject();
		$userInfoTable = new UserInfoTable($this->db);
		$userInfo = $userInfoTable->getRowObject();
		$userinvite = $this->getRowObject(); 
		
		//($user->load($userid) && 
		if (!$userInfo->load($userid))
			return false;
		//$this->db->setDebug(true); // NEVER TURN ON FOR LIVE SITE
		
		if ($userinvite->loadFromFbIdAndUserid($userInfo->fbId, $userInfo->refuid))
		{
			$userinvite->dateAccepted = date('Y-m-d H:i:s', time());
			$userinvite->update();
			return true;
		} else
		{
			// echo '<p>Couldnt find an invitation to accept!</p>';
			
		}
		
		return false;
		
	}
	
	function forceAcceptLastInvite($fbId) // searches for the last invite to this user and credit it
	{			
		$idList=$this->db->buildIdList(
			"SELECT id FROM UserInvites WHERE friendFbId=$fbId ORDER BY dateInvited DESC LIMIT 0,1;");
		echo '<p>idList: $idList</p>';
		$idList=explode(',',$idList);		
			
		$userinvite=$this->getRowObject();
		if ($idList[0] && $userinvite->load($idList[0]))
		{
			$userinvite->dateAccepted = date('Y-m-d H:i:s', time());
			$userinvite->update();
			return $userinvite->userid;
		} else
		{
			// echo '<p>Couldnt find an invitation to accept!</p>';
			
		}
		
		return 0;
		
	}
	
	

	
}

?>