<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class LogDump extends dbRowObject  
{
 
};

class LogDumpTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="LogDumps";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "LogDump";
	static $dumpLogs = true;
		
	static $fields = array(		
		"userid1" => "BIGINT(20)default 0",
		"action" => // NOTE: see mysql docs about ALTER ... MODIFY and enums before attempting to change the spelling of any fields. you will corrupt the existing data!
				"ENUM('vote','comment',
						'readStory','readWire','invite','postStory','publishWire',
						'publishStory','shareStory','referReader','referToSite',
						'postTwitter', 'signup', 'acceptedInvite',
						'redeemed', 'wonPrize', 'completedChallenge', 'addedWidget', 'addedFeedHeadlines',
						'friendSignup', 'addBookmarkTool',
						'levelIncrease','sessionsRecent','sessionsHour','pageAdd','chatStory','postBlog'
						) default 'readStory'",
		"itemid" => "INT(11) default 0",
		"itemid2" => "INT(11) default 0",
		"userid2" => "BIGINT(20) default 0",
		"ncUid" => "BIGINT(20) default 0",
		"t" => "timestamp", // DEFAULT CURRENT_TIMESTAMP", // back to what it was - default current AND on update
		"dateCreated" => "DATETIME", // a timestamp to record creation, but i'm not even going to try to let mysql handle it automatically 
		"status" => "ENUM('pending','ok','error') default 'pending'",
		"isFeedPublished" => "ENUM('pending','complete') default 'pending'",
		"siteid" 			=>	"BIGINT(20) unsigned default 0",
	);


//userid1, action, itemid, itemid2, userid2, ncUid, t, dateCreated, status, isFeedPublished, siteid, 


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
	
	function dumpLogs() {
		if (!self::$dumpLogs)
			return false;
		echo "\n   Dumping log data.   \n";

		$this->db->selectDB('research');

		$sites = array();
		$sites_res = $this->db->query("SELECT * FROM Sites");
		while (($row = mysql_fetch_assoc($sites_res)) !== false)
			$sites[$row['shortname']] = array('dbname' => $row['dbname'], 'siteid' => $row['id']);

		foreach ($sites as $name => $dbinfo) {
			echo "\n   Dumping log data for site: $name({$dbinfo['siteid']}).   \n";
			$dbname = $dbinfo['dbname'];
			$siteid = $dbinfo['siteid'];
			$this->db->selectDB($dbname);
			//$insert_sql = "REPLACE INTO LogDumps (userid, siteid, eligibility, optInStudy, isMember, cachedPointTotal, email, researchImportance, gender, age, city, state, country, zip) VALUES ";
			$insert_sql = "REPLACE INTO LogDumps (userid1, action, itemid, itemid2, userid2, ncUid, t, dateCreated, status, isFeedPublished, siteid) VALUES ";

			$sql = "SELECT userid1, action, itemid, itemid2, userid2, ncUid, t, dateCreated, status, isFeedPublished FROM Log";
			$log_results = $this->db->query($sql);
			$log_count = 0;

			while(($row = mysql_fetch_assoc($log_results)) !== false) {
				$row = array_map('mysql_real_escape_string', $row);
				$log_count++;
				$insert_sql .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),",
					$row['userid1'],
					$row['action'],
					$row['itemid'],
					$row['itemid2'],
					$row['userid2'],
					$row['ncUid'],
					$row['t'],
					$row['dateCreated'],
					$row['status'],
					$row['isFeedPublished'],
					$siteid
				);
			}
			$insert_sql = substr($insert_sql, 0, -1);

			if ($log_count > 0) {
				$this->db->selectDB('research');
				$this->db->query($insert_sql);
			}
		}

	}

	function insertNewestData() {
		/* TODO: implement this */
		return false;
	}

	function testPopulate()
	{
		
	}
	
};
	
?>