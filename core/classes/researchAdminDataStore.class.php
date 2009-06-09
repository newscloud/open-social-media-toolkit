<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class AdminDataStore extends dbRowObject  
{
 
};

class AdminDataStoreTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Admin_DataStore";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "AdminDataStore";
		
	static $fields = array(		
		"type" =>				"VARCHAR(255) default ''",
		"name" =>				"VARCHAR(255) default ''",
		"data" =>					"TEXT"
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

		$db->selectDB('research');
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