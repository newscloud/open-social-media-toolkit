<?php

/* this class is used for synchronizing accounts between the local cloud site and the remote server (newscloud.com) */

class userRemoteSync {

	var $db;			

	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
	}	

	function findUnlinkedAccounts() {
		$q=$this->db->query("SELECT User.userid,User.name,User.email,fbId,city FROM User LEFT JOIN UserInfo ON (User.userid=UserInfo.userid) WHERE ncUid=0 AND name<>'' AND email<>'' AND isEmailVerified=1 AND User.isBlocked=0 ;");
		return $q;
	}
	
	function findUserLevelIncreases($tstamp=0) {
		// to do : restrict query to timestamp
		$q=$this->db->query("SELECT Log.id, userid1 AS userid,itemid,User.ncUid FROM Log,User WHERE Log.userid1=User.userid AND User.ncUid>0 AND action='levelIncrease' and status='pending' AND User.isBlocked=0 ;");
		return $q;
		
	}
	
	function syncAccountsByAge($limit=25) {
		$q=$this->db->query("SELECT User.*,UserInfo.* FROM User LEFT JOIN UserInfo ON (User.userid=UserInfo.userid) AND User.isBlocked=0 ORDER BY lastRemoteSyncUpdate ASC;");
		return $q;
	}
	
	
	
	function touchSyncUpdate($userid=0) {
		$this->db->update("UserInfo","lastRemoteSyncUpdate=now()","userid=$userid");
	}
	
} // end userRemoteSync
?>
