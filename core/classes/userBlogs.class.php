<?php

require_once(PATH_CORE .'/classes/dbRowObject.class.php');

class UserBlogsRow extends dbRowObject
{
	
}


class UserBlogsTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="UserBlogs";
	static $idname = "blogid";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "UserBlogsRow";
		
	static $fields = array(				
		"siteContentId" => "INT(11) default 0",
		"userid" => "INT(11) default 0",			
		"title" => "VARCHAR(255) default ''",		
		"entry" => "TEXT default ''",
		"url" => "VARCHAR(255) default ''",
		"imageUrl" => "VARCHAR(255) default ''",
		"videoEmbed" => "VARCHAR(255) default ''",
		"status" => "enum ('draft','published') default 'draft'"			
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

class UserBlogs {
	var $db;
	var $templateObj;
		
	function UserBlogs(&$db=NULL) {
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}

	function getById($id=0,$returnQuery=false) {
		$q=$this->db->queryC("SELECT * FROM UserBlogs WHERE id=$id;");
		if ($returnQuery) return $q;
		if ($q!==false) {
			$story=$this->db->readQ($q);
			return $story;
		} else
			return false;	
	}
	
	function getDraftsByUserId($userid=0,$excludeBlogId=0) {
		$q=$this->db->queryC("SELECT * FROM UserBlogs WHERE userid=$userid AND blogid<>$excludeBlogId AND status='draft';");
		return $q;
	}
	
	function cleanup() {
		// delete content for deleted users
		$this->db->delete("Blogs","NOT EXISTS (select * from User where User.userid=Blogs.userid)");
		// to do: delete blog entries related to deleted content
	}
}	
?>