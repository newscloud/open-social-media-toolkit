<?php
/*
 * tag class
 */

require_once (PATH_CORE . '/classes/dbRowObject.class.php');
class tagRow extends dbRowObject
{
	
}

class tagsTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Tags";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "tagRow";
	static $fields = array(		
		"tag" 		=> "VARCHAR(50) default ''",
		"raw_tag" 		=> "VARCHAR(75) default ''",
	);

	var $_normalized_valid_chars = 'a-zA-Z0-9';
    var $_normalize_tags = true;

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
	
	// Cardric table creation routine, same for all *Table classes 		
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

	function lookupOrAdd($tag='') {
		$ntag=$this->normalize_tag($tag);
		// check if tag exists
		$qstr = $this->db->queryC ("SELECT id FROM Tags WHERE tag='$ntag';");
		$t = $this->getRowObject();
		if ($qstr===false) {
			// insert the tag
			$t->tag=$ntag;			
			$t->raw_tag=$tag;
			$t->insert();								
		} else {
			// get the id
			$d=$this->db->readQ($qstr);
			$t->load($d->id);
		}
		return $t;		
	}

   function normalize_tag($tag) {

        if ($this->_normalize_tags) {

            $normalized_valid_chars = $this->_normalized_valid_chars;

            $normalized_tag = preg_replace("/[^$normalized_valid_chars]/", "", $tag);

            return strtolower($normalized_tag);

        } else {
            return $tag;

        }
    }
	
	function initializeStuff() {
		// add amazon tags to stuff table
		echo 'initializing tags for stuff<br />';
		$stuffTags=array('Apparel','Baby','Beauty','Books','Classical','DVD','Electronics','Gourmet Food','Health & Personal Care','Jewelry','Kitchen','Magazines','Merchants','Miscellaneous','Music','Musical Instruments','Office Products','Outdoor Living','Computers & Hardware','Photo','Software','Sporting Goods','Tools','Toys','VHS','Video','Video Games','Wireless');
		foreach ($stuffTags as $item) {
			$this->lookupOrAdd($item);
		}
		echo 'Done<br />';
	}
	
	function initialize() {
		// provides some common basic tags	
		echo 'Initializing tags...<br />';	$crowdTags=array('education','health','music','technology','food','politics','transportation','lifestyle','arts','sports','business','gardening','travel','recreation','government','environment');
		foreach ($crowdTags as $item) {
			$this->lookupOrAdd($item);
		}
		echo 'Done<br />';
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////	
}

class taggedObjectRow extends dbRowObject
{
	
}

class TaggedObjectsTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="TaggedObjects";
	static $idname = "id";
	static $idtype = "BIGINT(20) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "taggedObjectRow";
	static $fields = array(		
		"tagid" 		=> "int(10) unsigned NOT NULL default 0",
		"userid" 		=> "BIGINT(20) unsigned NOT NULL default 0",
		"itemid" 		=> "BIGINT(20) unsigned NOT NULL default 0",
		"itemType" => 	"ENUM ('story','ask','idea','stuff') default 'story'",		
		"dt" => "datetime"
		
	);

	var $_normalized_valid_chars = 'a-zA-Z0-9';
    var $_normalize_tags = true;

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
	
	// Cardric table creation routine, same for all *Table classes 		
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
}


class tags
{
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
	
}
?>