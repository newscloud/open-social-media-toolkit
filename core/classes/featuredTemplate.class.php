<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class FeaturedTemplate extends dbRowObject  
{
 
};

class FeaturedTemplateTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="FeaturedTemplate";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "FeaturedTemplate";
		
	static $fields = array(		
		"template" => 		"VARCHAR(255) default ''",
		"story_1_id" =>		"INT unsigned default 0",
		"story_2_id" =>		"INT unsigned default 0",
		"story_3_id" =>		"INT unsigned default 0",
		"story_4_id" =>		"INT unsigned default 0",
		"story_5_id" =>		"INT unsigned default 0",
		"story_6_id" =>		"INT unsigned default 0",
		"t" => "timestamp"
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