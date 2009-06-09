<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class RawSession extends dbRowObject  
{
 
};

class RawSessionTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="RawSessions";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "RawSession";
	static $insertFullData = false;
		
	static $fields = array(		
		"fb_sig_session_key" 	=>	"VARCHAR(255) default ''",
		"fb_sig_time" 				=>	"DATETIME",
		"fb_sig_expires" 			=>	"DATETIME",
		"fb_sig_update_time" 	=>	"DATETIME",
		"qs" 									=>	"TEXT",
		"userid" 							=>	"BIGINT(20) unsigned default 0",
		"siteid" 							=>	"BIGINT(20) unsigned default 0",
		"fbId" 								=>	"BIGINT(20) unsigned default 0",
		"sessionTableId" 			=>	"BIGINT(20) unsigned default 0",
		"t" 									=> "TIMESTAMP"
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
			$sql = 'INSERT IGNORE INTO RawSessions (fb_sig_session_key, fb_sig_time, fb_sig_expires, fb_sig_update_time, qs, userid, fbId, sessionTableId, t, siteid) VALUES ';
			if (!preg_match('/^[0-9]{8}_([^_]+)_session.log$/', $filename, $match))
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
			$count = 0;
			foreach ($file as $num => $line) {
				//if ($count++ == 1) break;
				$d = unserialize($line);
				$sql .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),", $d->fb_sig_session_key, $d->fb_sig_time, $d->fb_sig_expires, $d->fb_sig_update_time, mysql_real_escape_string($d->qs), $d->userid, $d->fbId, $d->id, date('Y-m-d H:i:s', $d->t), $siteid);
				$rowcount++;
			}
			$sql = substr($sql, 0, -1);

			$this->db->query($sql);
		}
		echo "\n\tInserting data for $rowcount sessions.\n";
	}

	function insertNewestData() {
		$logdir = opendir(PATH_LOGS);
		$rowcount = 0;
		while (($filename = readdir($logdir)) !== false) {
			$date = date('Ymd', time() - 24 * 60 * 60);
			if ($filename == '.' || $filename == '..')
				continue;
			$sql = 'INSERT IGNORE INTO RawSessions (fb_sig_session_key, fb_sig_time, fb_sig_expires, fb_sig_update_time, qs, userid, fbId, sessionTableId, t, siteid) VALUES ';
			if (!preg_match('/^'.$date.'_([^_]+)_session.log$/', $filename, $match))
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
			$count = 0;
			foreach ($file as $num => $line) {
				//if ($count++ == 1) break;
				$d = unserialize($line);
				$sql .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),", $d->fb_sig_session_key, $d->fb_sig_time, $d->fb_sig_expires, $d->fb_sig_update_time, mysql_real_escape_string($d->qs), $d->userid, $d->fbId, $d->id, date('Y-m-d H:i:s', $d->t), $siteid);
				$rowcount++;
			}
			$sql = substr($sql, 0, -1);

			$this->db->query($sql);
		}
		echo "\n\tInserting data for $rowcount sessions on date: $date.\n";
	}
	function testPopulate()
	{
		
	}
	
};
	
?>