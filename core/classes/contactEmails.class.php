<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class ContactEmail extends dbRowObject  
{
 
};

class ContactEmailTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="ContactEmails";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "ContactEmail";
		
	static $fields = array(		
		"email" => 			"VARCHAR(255) default ''",
		"subject" =>		"VARCHAR(255) default ''",
		"message" =>	 	"TEXT default ''",
		"userid" => 		"BIGINT(20) unsigned default 0",
		"is_read" => 		"TINYINT(1) default 0",
		"replied" => 		"TINYINT(1) default 0",
		"topic" => 			"ENUM ('general','editorial', 'team', 'feedback', 'bug') default 'general'",
		"date" =>	 		"DATETIME"
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
		
		
		$contactemail = $this->getRowObject();
		

		$contactemail->email = 'me@myemail.com';
		$contactemail->subject = 'My contact email subject!';
		$contactemail->message = 'This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..';
		$contactemail->userid = 58;
		$contactemail->date = 'NOW()';
		$contactemail->topic = 'general';
		$contactemail->insert();
		
		$contactemail->email = 'me@myemail.com';
		$contactemail->subject = 'My contact email subject!';
		$contactemail->message = 'This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..';
		$contactemail->userid = 58;
		$contactemail->date = 'NOW()';
		$contactemail->topic = 'editorial';
		$contactemail->insert();
		
		$contactemail->email = 'me@myemail.com';
		$contactemail->subject = 'My contact email subject!';
		$contactemail->message = 'This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..';
		$contactemail->userid = 58;
		$contactemail->date = 'NOW()';
		$contactemail->topic = 'team';
		$contactemail->insert();
		
		$contactemail->email = 'me@myemail.com';
		$contactemail->subject = 'My contact email subject!';
		$contactemail->message = 'This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..';
		$contactemail->userid = 58;
		$contactemail->date = 'NOW()';
		$contactemail->topic = 'feedback';
		$contactemail->insert();
		
		$contactemail->email = 'me@myemail.com';
		$contactemail->subject = 'My contact email subject!';
		$contactemail->message = 'This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..';
		$contactemail->userid = 58;
		$contactemail->date = 'NOW()';
		$contactemail->topic = 'bug';
		$contactemail->insert();
		
		$contactemail->email = 'me@myemail.com';
		$contactemail->subject = 'My contact email subject!';
		$contactemail->message = 'This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..This is my message..';
		$contactemail->userid = 58;
		$contactemail->date = 'NOW()';
		$contactemail->topic = 'foobar';
		$contactemail->insert();
	}
	
};
	
?>