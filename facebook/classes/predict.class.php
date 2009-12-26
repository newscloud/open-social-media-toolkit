<?php
/*
 * Predictions class
 */
require_once (PATH_CORE.'/classes/dbRowObject.class.php');
class predictRow extends dbRowObject
{

}

class predictTable
{

	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Predict";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "predictRow";
	static $fields = array(		
		"userid" => "BIGINT(20) unsigned default 0",
		"title" 		=> "VARCHAR(255) default ''",
		"groupid" 		=> "INT(4) default 0",
		"tagid" 		=> "INT(11) default 0",
		"type" 		=> "ENUM ('yesno','selection','open','state') default 'yesno'",
		"status" => 			"ENUM ('preview','open','closed') default 'open'",
		"numLikes" 		=> "INT(4) default 0",
		"numAnswers" 		=> "INT(4) default 0",
		"result" 		=> "VARCHAR(50) default ''", // correct answer
		"options" 		=> "TEXT", // option list for selections
		"dt" 				=> "datetime"
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

	//  table creation routine, same for all *Table classes 		
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

class predictAnswersRow extends dbRowObject
{

}

class predictAnswersTable
{

	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="predictAnswers";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "predictAnswersRow";
	static $fields = array(		
		"predictid" 		=> "INT(11) default 0",
		"userid" => "BIGINT(20) unsigned default 0",
		"guess" 		=> "VARCHAR(50) default ''",
		"dt" 				=> "datetime"
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

	//  table creation routine, same for all *Table classes 		
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


class predict
{
	var $db;	
	var $utilObj;
	var $templateObj;
	var $session;
	var $initialized;
	var $app;
		
	function __construct(&$db=NULL,&$templateObj=NULL) 
	{
		$this->initialized=false;
		if (is_null($db)) 
		{ 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
		if (!is_null($templateObj)) $this->templateObj=$templateObj;
		$this->initObjs();
	}
	
	function initObjs() {
		if ($this->initialized)
			return true;
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		if (is_null($this->templateObj)) 
		{ 
			require_once(PATH_CORE.'/classes/template.class.php');
			$this->templateObj=new template($this->db);
		} 
		$this->initialized = true;
	}

}
?>