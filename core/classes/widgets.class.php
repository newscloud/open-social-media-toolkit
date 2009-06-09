<?php

require_once(PATH_CORE.'/classes/dbRowObject.class.php');


class Widgets extends dbRowObject 
{
   function __construct($db, $tablename, $fieldnames, $idname) 
  {
  	parent::__construct( $db, $tablename,
	    $fieldnames, $idname );
  }

}

class WidgetsTable 
{
	var $db;
	
	static $tablename="Widgets";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "WidgetsTable";
	
	static $fields = array(		
		"title" => "VARCHAR(255) default ''",
		"wrap" => "TEXT default ''",
		"html" => "TEXT default ''",
		"smartsize" => "TINYINT(1) default 0",
		"width" => "INT(2) default 0",
		"height" => "INT(2) default 0",
		"style" => "VARCHAR(255) default ''",
		"type" => "enum ('fbml','src','script') default 'fbml'",
		"isAd" => "TINYINT(1) default 0"				
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

	function fetchWidgetsByTitle($title='') {
		$q=$this->db->query("SELECT * FROM Widgets WHERE title='$title' LIMIT 1;");
		while ($data=$this->db->readQ($q)) {
			$code=$this->buildWidget($data);
		}
		return $code;
	}
	
	function fetchWidgets($id=0) {
		$q=$this->db->query("SELECT * FROM Widgets WHERE id=$id LIMIT 1;");
		while ($data=$this->db->readQ($q)) {
			$code=$this->buildWidget($data);
		}
		return $code;
	}
	
	function buildWidget($w) {
		if ($w->width>0 AND $w->height>0) {
			$dimensionStr='width="'.$w->width.'" height="'.$w->height.'"';
		} else {
			$dimensionStr='';
		}
		switch ($w->type) {
			default: //fbml
				$code=$w->html;
			break;
			case 'src':
				$code='<fb:iframe frameborder="0" scrolling="no" src="'.$w->html.'" '.$dimensionStr.' style="'.$w->style.'"/> '; 
			break;			
			case 'script':
				// serve widget via iframe
				$code='<fb:iframe frameborder="0" scrolling="no" src="'.URL_CALLBACK.'?p=cache&m=widget&id='.$w->id.'" '.$dimensionStr.' style="'.$w->style.'"/> ';
			break;			
		}
		if ($w->wrap<>'') {
			$code=str_replace('{widget}',$code,$w->wrap);
		}
		return $code;
	}

	function fetchWidgetCode($id) {
		$q=$this->db->query("SELECT html FROM Widgets WHERE id=$id LIMIT 1;");
		while ($data=$this->db->readQ($q)) {
			$code=$data->html;
		}
		return $code;		
	}
}

class FeaturedWidgetsTable 
{
	var $db;
	var $wtObj=NULL;
	var $objectsInit=false;
	
	static $tablename="FeaturedWidgets";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "FeaturedWidgetsTable";
	
	static $fields = array(		
		"widgetid" => "INT(11) unsigned",
		"locale" => "VARCHAR(100) default ''",					
		"position" => "TINYINT(1) default 0"
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
	
	function initObjs() {
		if (!$this->objectsInit) {
			$this->wtObj=new WidgetsTable($this->db);
		}
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
	
	function lookupWidget($locale='') {
		$q=$this->db->queryC("SELECT * FROM FeaturedWidgets WHERE locale='$locale';");
		if ($q!==false) {
			$this->initObjs();
			$data=$this->db->readQ($q);
			$code=$this->wtObj->fetchWidgets($data->widgetid);			
			return $code;
		} else {
			return '';
		}
	}
		
}

?>