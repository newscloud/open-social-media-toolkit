<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class UserCollective extends dbRowObject  
{
 
};

class UserCollectiveTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="UserCollectives";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "UserCollective";
	static $resistanceIsFutile = true;
		
	static $fields = array(		
		"eligibility" => 	"ENUM ('team','general','ineligible') default 'team'",
		"userid" 			=>	"BIGINT(20) unsigned default 0",
		"siteid" 			=>	"BIGINT(20) unsigned default 0",
		"optInStudy" => 	"TINYINT(1) default 1", // user OKs conditions of data collection for study
		"researchImportance" => "TINYINT(1) default 0",
		"gender" => "ENUM('male','female','other') ",
		"age" => "TINYINT(1) default 0",
		"city" => "VARCHAR(255) default 'Unknown'",	
		"state" => "VARCHAR(255) default ''",
		"country" => "VARCHAR(255) default ''",
		"zip" => "VARCHAR(255) default ''",
		"cachedPointTotal" => "INT(4) default 0" ,
		"email" => 			"varchar(255) default ''",
		"isMember" => 		"TINYINT(1) default 0" // User has signed up with the local site
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
	
	function assimilateUsers() {
		if (!self::$resistanceIsFutile)
			return false;
		echo "\n   Resistance is futile.   \n";

		$this->db->selectDB('research');

		$sites = array();
		$sites_res = $this->db->query("SELECT * FROM Sites");
		while (($row = mysql_fetch_assoc($sites_res)) !== false)
			$sites[$row['shortname']] = array('dbname' => $row['dbname'], 'siteid' => $row['id']);

		foreach ($sites as $name => $dbinfo) {
			$dbname = $dbinfo['dbname'];
			$siteid = $dbinfo['siteid'];
			$this->db->selectDB($dbname);
			$insert_sql = "REPLACE INTO UserCollectives (userid, siteid, eligibility, optInStudy, isMember, cachedPointTotal, email, researchImportance, gender, age, city, state, country, zip) VALUES ";

			$sql = "SELECT User.userid, User.eligibility, User.optInStudy, User.isMember, User.cachedPointTotal, User.email, UserInfo.researchImportance, UserInfo.gender, UserInfo.age, UserInfo.city, UserInfo.state, UserInfo.country, UserInfo.zip FROM User LEFT JOIN UserInfo ON User.userid = UserInfo.userid";
			$user_results = $this->db->query($sql);
			$user_count = 0;

			while(($row = mysql_fetch_assoc($user_results)) !== false) {
				$row = array_map('mysql_real_escape_string', $row);
				$user_count++;
				$insert_sql .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),",
					$row['userid'],
					$siteid,
					$row['eligibility'],
					$row['optInStudy'],
					$row['isMember'],
					$row['cachedPointTotal'],
					$row['email'],
					$row['researchImportance'],
					$row['gender'],
					$row['age'],
					$row['city'],
					$row['state'],
					$row['country'],
					$row['zip']
				);
			}
			$insert_sql = substr($insert_sql, 0, -1);

			if ($user_count > 0) {
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