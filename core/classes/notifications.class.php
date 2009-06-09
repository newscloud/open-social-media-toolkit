<?php

require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class Notifications extends dbRowObject 
{
   function __construct($db, $tablename, $fieldnames, $idname) // could create directly, but better to ask the PrizeTable object for it!
  {
  	parent::__construct( $db, $tablename,
	    $fieldnames, $idname );
  
  }

}

class NotificationsTable 
{
	var $db;
	
	static $tablename="Notifications";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "Notifications";
	
	static $fields = array(		
			"msgid"			=>"INT(11) default 0",
			"status"			=>"ENUM ('sent','pending','error','opened') default 'pending'",
			"userid"			=>"INT(11) default 0",		
			"dateSent"		=>"DATETIME",		
			"toUserId"			=>"INT(11) default 0",				
			"toFbId"			=>"BIGINT(20) default 0"				
			);
	static $keydefinitions = array(); 	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
			
	function __construct(&$db=NULL) 
	{

		if (is_null($db)) { 
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


	function checkExists($toFbId,$msgid)
  	{
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE toFbId=$toFbId AND msgid=$msgid;");
		return $chkDup;		
  	} 
  	
  	function countSentToday($userid) {
  		// how many notifications sent today
  		$midnight=date('Y-m-d',time()).' 00:00:00';
  		$q=$this->db->query("SELECT count(id) as cnt FROM ".self::$tablename." WHERE userid=$userid AND dateSent>'".$midnight."';"); // date_sub(NOW(), INTERVAL 24 HOUR)
  		$data=$this->db->readQ($q);
		return $data->cnt;
  	}

	function setStatus($msgid=0,$toFbId=0,$status='pending') {
		$this->db->update(self::$tablename,"status='$status',dateSent=NOW()",'msgid='.$msgid.' AND toFbId='.$toFbId);
	} 

	function getPendingNotificationsByMsg($msgid=0) {
		// get all pending notifications
		return $this->db->buildIdList("SELECT toFbId as id FROM ".self::$tablename." WHERE msgid=$msgid AND status='pending';");
	}	
	  
	  function getRecipientList($msgid=0,$limit=1) {
	  		// get next list of recipients up to limit
	  		return $this->db->buildIdList("SELECT toFbId as id FROM ".self::$tablename." WHERE msgid=$msgid AND status='pending' LIMIT $limit;");
	  }	

	function lookupReferral($referid=0,$itemid=0,$toFbId=0) {
  		$q=$this->db->query("SELECT ".self::$tablename.".msgid FROM ".self::$tablename." LEFT JOIN NotificationMessages ON NotificationMessages.msgid=".self::$tablename.".msgid  WHERE toFbId=$toFbId AND itemid=$itemid AND Notifications.userid=$referid;");
  		$data=$this->db->readQ($q);
		return $data->msgid;		
	}	  
}

class NotificationMessages extends dbRowObject 
{

  function __construct($db, $tablename, $fieldnames, $idname) // could create directly, but better to ask the PrizeTable object for it!
  {
  	parent::__construct( $db, $tablename,
	    $fieldnames, $idname );
  
  }	
}


class NotificationMessagesTable 
{
	var $db;
	
	static $tablename="NotificationMessages";
	static $idname = "msgid";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "NotificationMessages";
	
	static $fields = array(		
			"userid"			=>"INT(11) default 0",
			"type"			=>"ENUM ('sharedStory') default 'sharedStory'",
			"itemid"			=>"INT(11) default 0", // e.g. siteContentId
			"subject"				=>"VARCHAR(255) default ''",
			"message"		=>"TEXT default ''",
			"embed"		=>"TEXT default ''",
			"dateCreated"		=>"DATETIME",
			"lastAttempt"		=>"DATETIME",
			"status"			=>"ENUM ('sent','pending','blocked','approved') default 'pending'"				
			);
	static $keydefinitions = array(); 	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
			
	function __construct(&$db=NULL) 
	{

		if (is_null($db)) { 
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

	function updateMessageStatus($msgid) {
		// check if all notifications for a message have been sent
		$q=$this->db->queryC("SELECT count(id) as cnt FROM Notifications WHERE msgid=$msgid AND status='pending';");
		$data=$this->db->readQ($q);
		if ($data->cnt==0) {
			// no more pending, set status as sent
			$this->db->update("NotificationMessages","status='sent',lastAttempt=NOW()","msgid=$msgid");
		} else {
			$this->db->update("NotificationMessages","lastAttempt=NOW()","msgid=$msgid");
		}
	}
	
	function checkAndInsert(&$msg) {
		// check if it exists
		$q=$this->checkExists($msg->userid,$msg->itemid,$msg->subject,$msg->message);
		if (!$q) {
			// if not, insert it and return id
			$msgid=$msg->insert();	
		} else {
			// if dup, return id
			$data=$this->db->readQ($q);
			$msgid=$data->msgid;			
		}
		return $msgid;	
	}
	
	function checkExists($userid,$itemid,$subject,$message)
  	{
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE userid=$userid AND itemid=$itemid AND subject='$subject' AND message='$message';");
		return $chkDup;		
  	} 
	
	function getPendingValidMessages() {
		// get next list of recipients up to limit
		//return $this->db->buildIdList("SELECT msgid FROM ".self::$tablename." WHERE status='pending' LIMIT $limit;");
		return $this->db->buildIdList("SELECT msgid as id FROM ".self::$tablename.", fbSessions WHERE status='pending' AND NotificationMessages.userid = fbSessions.userid AND fbSessions.fb_sig_expires > NOW()");
	}	

}
?>