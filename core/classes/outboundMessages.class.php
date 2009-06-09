<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class OutboundMessage extends dbRowObject  
{
 
};

class OutboundMessageTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="OutboundMessages";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "OutboundMessage";
		
	static $fields = array(		
		"userIntro" 		=> "VARCHAR(255) default ''",
		"msgType" 			=> "ENUM ('notification','announce') default 'announce'",
		"subject" 			=> "VARCHAR(255) default ''",
		"msgBody" 			=> "TEXT default ''",
		"buttonLinkText" 	=> "VARCHAR(255) default ''",
		"closingLinkText" 	=> "VARCHAR(255) default ''",
		"shortLink" 		=> "VARCHAR(25) default ''",
		"userGroup" 		=> "VARCHAR(255) default ''",
		"userid" 			=> "BIGINT(20) unsigned default 0",
		"t" 				=> "timestamp",
		"status" 			=> "ENUM ('sent','pending','hold','incomplete') default 'pending'",
		"usersReceived" 	=> "TEXT default ''",
		"numUsersReceived"	=> "INT(11) unsigned default 0",
		"numUsersExpected"	=> "INT(11) unsigned default 0"
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
	}
	
};
	
?>