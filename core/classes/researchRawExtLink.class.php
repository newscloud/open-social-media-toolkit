<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class RawExtLink extends dbRowObject  
{
 
};

class RawExtLinkTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="RawExtLinks";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "RawExtLink";
	static $insertFullData = false;
		
	static $fields = array(		
		"action" =>				"VARCHAR(255) default ''",
		"str" =>				"VARCHAR(255) default ''",
		"qs" =>					"TEXT",
		"itemid" =>				"BIGINT(20) unsigned default 0",
		"itemid2" =>			"BIGINT(20) unsigned default 0",
		"userid" =>				"BIGINT(20) unsigned default 0",
		"siteid" =>				"BIGINT(20) unsigned default 0",
		"t" =>					"TIMESTAMP"
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
	
	function insertFullData() {
		if (!self::$insertFullData)
			return false;

		$logdir = opendir(PATH_LOGS);
		$rowcount = 0;
		while (($filename = readdir($logdir)) !== false) {
			if ($filename == '.' || $filename == '..')
				continue;
			$sql = 'INSERT IGNORE INTO RawExtLinks (action, str, qs, itemid, siteid, t) VALUES ';
			if (!preg_match('/^[0-9]{8}_([^_]+)_extLink.log$/', $filename, $match))
				continue;
			else
				$sitename = $match[1];
			$res = $this->db->queryC("SELECT id FROM Sites where shortname = '$sitename'");
			if (!$res)
				return false;
			else
				$res = $this->db->readQ($res);
			$siteid = $res->id;

			$file = file(PATH_LOGS."/$filename");
			foreach ($file as $num => $line) {
				$d = unserialize($line);
				$sql .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s'),", $d->action, mysql_real_escape_string($d->str), mysql_real_escape_string($d->qs), $d->itemid, $siteid, date('Y-m-d H:i:s', $d->t));
				$rowcount++;
			}
			$sql = substr($sql, 0, -1);

			$this->db->query($sql);
		}
		echo "\n\tInserting data for $rowcount external links.\n";
	}

	function insertNewestData() {
		$logdir = opendir(PATH_LOGS);
		$rowcount = 0;
		while (($filename = readdir($logdir)) !== false) {
			$date = date('Ymd', time() - 24 * 60 * 60);
			if ($filename == '.' || $filename == '..')
				continue;
			$sql = 'INSERT IGNORE INTO RawExtLinks (action, str, qs, itemid, siteid, t) VALUES ';
			if (!preg_match('/^'.$date.'_([^_]+)_extLink.log$/', $filename, $match))
				continue;
			else
				$sitename = $match[1];
			$res = $this->db->queryC("SELECT id FROM Sites where shortname = '$sitename'");
			if (!$res)
				return false;
			else
				$res = $this->db->readQ($res);
			$siteid = $res->id;

			$file = file(PATH_LOGS."/$filename");
			foreach ($file as $num => $line) {
				$d = unserialize($line);
				$sql .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s'),", $d->action, mysql_real_escape_string($d->str), mysql_real_escape_string($d->qs), $d->itemid, $siteid, date('Y-m-d H:i:s', $d->t));
				$rowcount++;
			}
			$sql = substr($sql, 0, -1);

			$this->db->query($sql);
		}
		echo "\n\tInserting data for $rowcount external links on date: $date.\n";
	}
	function testPopulate()
	{
		
	}
	
};
	
?>