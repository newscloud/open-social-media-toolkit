<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class SessionLength extends dbRowObject  
{
 
};

class SessionLengthTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="SessionLengths";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "SessionLength";
	static $insertFullData = true;
		
	static $fields = array(		
		"session_length" 	=>	"VARCHAR(255) default ''",
		"avg_click_rate" 	=>	"VARCHAR(255) default ''",
		"userid" 			=>	"BIGINT(20) unsigned default 0",
		"siteid" 			=>	"BIGINT(20) unsigned default 0",
		"total_actions" 	=>	"INTEGER default 0",
		"start_session"		=>	"TIMESTAMP",
		"end_session" 		=>	"TIMESTAMP",
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

		$row_count = 0;
		$row_sql = "SELECT count(1) AS session_count FROM RawSessions";
		$results = mysql_fetch_assoc($this->db->query($row_sql));
		$row_count = $results['session_count'];
		$ROW_LIMIT = 10000;
		$IDLE_TIME = 60 * 10; // 10 mins

		for ($limit = 0; $limit < $row_count; $limit += $ROW_LIMIT) {
			$sql = "SELECT userid, siteid, t FROM RawSessions WHERE userid != 0 ORDER BY userid, t ASC LIMIT $limit, ".($limit + $ROW_LIMIT);
			$results = $this->db->query($sql);

			$user_stats = array();
			$last_t = 0;
			$curr_userid = false;
			$curr_siteid = false;
			$stats = array();
			while (true) {
				if (($row = mysql_fetch_array($results)) !== false)
					list($userid, $siteid, $t) = $row;

				if (!$row || (($t = strtotime($t)) > ($last_t + $IDLE_TIME)) || $userid !== $curr_userid) {
					if (count($stats) > 1) {
						$time = round(($stats[count($stats)-1] - $stats[0]), 2);
						$user_stat = array(
							'session_length' 	=> $time,
							'avg_click_rate' 	=> round($time / count($stats), 2),
							'userid'			=> $curr_userid,
							'siteid'			=> $curr_siteid,
							'start_session'		=> date("Y-m-d H:i:s", $stats[0]),
							'end_session'		=> date("Y-m-d H:i:s", $stats[count($stats)-1]),
							'total_actions'		=> count($stats)
						);
						$user_stats[$curr_userid][] = $user_stat;
					} else if (count($stats) == 1) {
						$user_stat = array(
							'session_length' 	=> 0,
							'avg_click_rate' 	=> 0,
							'userid'			=> $curr_userid,
							'siteid'			=> $curr_siteid,
							'start_session'		=> date("Y-m-d H:i:s", $stats[0]),
							'end_session'		=> date("Y-m-d H:i:s", $stats[0]),
							'total_actions'		=> count($stats)
						);
						$user_stats[$curr_userid][] = $user_stat;
					}

					$curr_userid = $userid;
					$curr_siteid = $siteid;
					$stats = array();
				}

				$last_t = $t;
				$stats[] = $t;

				if (!$row) break;
			}


			if (!count($user_stats)) continue;

			$insert_sql = "INSERT IGNORE INTO SessionLengths (session_length, avg_click_rate, userid, siteid, start_session, end_session, total_actions) VALUES ";

			foreach ($user_stats as $userid => $stats)
				foreach ($stats as $s)
					$insert_sql .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s'),", $s['session_length'], $s['avg_click_rate'], $s['userid'], $s['siteid'], $s['start_session'], $s['end_session'], $s['total_actions']);

			$insert_sql = substr($insert_sql, 0, -1);


			$this->db->query($insert_sql);
		}
	}

	function insertNewestData() {
		/* TODO: implement this */
		return $this->insertFullData();
		//return false;
	}

	function testPopulate()
	{
		
	}
	
};
	
?>