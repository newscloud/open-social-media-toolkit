<?php
require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class SurveyMonkey extends dbRowObject  
{
 
};

class SurveyMonkeyTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="SurveyMonkeys";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "SurveyMonkey";
		
	static $fields = array(		
		"email"	=>	"VARCHAR(255) default ''",
		"userid"	=>	"BIGINT(20) unsigned default 0",
		"siteid" 	=>	"BIGINT(20) unsigned default 0",
		"q1a"	=> "TINYINT(1) default 0",
		"q1b"	=> "TINYINT(1) default 0",
		"q1c"	=> "TINYINT(1) default 0",
		"q1d"	=> "TINYINT(1) default 0",
		"q1e"	=> "TINYINT(1) default 0",
		"q1f"	=> "TINYINT(1) default 0",
		"q1g"	=> "TINYINT(1) default 0",
		"q2a"	=> "TINYINT(1) default 0",
		"q2b"	=> "TINYINT(1) default 0",
		"q2c"	=> "TINYINT(1) default 0",
		"q2d"	=> "TINYINT(1) default 0",
		"q2e"	=> "TINYINT(1) default 0",
		"q3a"	=> "TINYINT(1) default 0",
		"q3b"	=> "TINYINT(1) default 0",
		"q3c"	=> "TINYINT(1) default 0",
		"q3d"	=> "TINYINT(1) default 0",
		"q3e"	=> "TINYINT(1) default 0",
		"q3f"	=> "TINYINT(1) default 0",
		"q3g"	=> "TINYINT(1) default 0",
		"q3h"	=> "TINYINT(1) default 0",
		"q4a"	=> "TINYINT(1) default 0",
		"q4b"	=> "TINYINT(1) default 0",
		"q4c"	=> "TINYINT(1) default 0",
		"q4d"	=> "TINYINT(1) default 0",
		"q4e"	=> "TINYINT(1) default 0",
		"q4f"	=> "TINYINT(1) default 0",
		"q4g"	=> "TINYINT(1) default 0",
		"q5a"	=> "TINYINT(1) default 0",
		"q5b"	=> "TINYINT(1) default 0",
		"q5c"	=> "TINYINT(1) default 0",
		"q5d"	=> "TINYINT(1) default 0",
		"q5e"	=> "TINYINT(1) default 0",
		"q5f"	=> "TINYINT(1) default 0",
		"q5g"	=> "TINYINT(1) default 0",
		"q5h"	=> "TINYINT(1) default 0",
		"q5i"	=> "TINYINT(1) default 0",
		"q5j"	=> "TINYINT(1) default 0",
		"q5k"	=> "TINYINT(1) default 0",
		"q6"	=> "TINYINT(1) default 0",
		"q7"	=> "TINYINT(1) default 0",
		"q8a"	=> "TINYINT(1) default 0",
		"q8b"	=> "TINYINT(1) default 0",
		"q8c"	=> "TINYINT(1) default 0",
		"q8d"	=> "TINYINT(1) default 0",
		"q8e"	=> "TINYINT(1) default 0",
		"q8f"	=> "TINYINT(1) default 0",
		"q8g"	=> "TINYINT(1) default 0",
		"q8h"	=> "TINYINT(1) default 0",
		"q9a"	=> "TINYINT(1) default 0",
		"q9b"	=> "TINYINT(1) default 0",
		"q9c"	=> "TINYINT(1) default 0",
		"q9d"	=> "TINYINT(1) default 0",
		"q9e"	=> "TINYINT(1) default 0",
		"q9f"	=> "TINYINT(1) default 0",
		"q10a"	=> "TINYINT(1) default 0",
		"q10b"	=> "TINYINT(1) default 0",
		"q10c"	=> "TINYINT(1) default 0",
		"q10d"	=> "TINYINT(1) default 0",
		"q10e"	=> "TINYINT(1) default 0",
		"q10f"	=> "TINYINT(1) default 0",
		"q11a"	=> "TINYINT(1) default 0",
		"q11b"	=> "TINYINT(1) default 0",
		"q11c"	=> "TINYINT(1) default 0",
		"q11d"	=> "TINYINT(1) default 0",
		"q11e"	=> "TINYINT(1) default 0",
		"q11f"	=> "TINYINT(1) default 0",
		"q11g"	=> "TINYINT(1) default 0",
		"q11h"	=> "TINYINT(1) default 0",
		"q12a"	=> "TINYINT(1) default 0",
		"q12b"	=> "TINYINT(1) default 0",
		"q12c"	=> "TINYINT(1) default 0",
		"q12d"	=> "TINYINT(1) default 0",
		"q12e"	=> "TINYINT(1) default 0",
		"q12f"	=> "TINYINT(1) default 0",
		"q12g"	=> "TINYINT(1) default 0",
		"q12h"	=> "TINYINT(1) default 0",
		"q13a"	=> "TINYINT(1) default 0",
		"q13b"	=> "TINYINT(1) default 0",
		"q13c"	=> "TINYINT(1) default 0",
		"q13d"	=> "TINYINT(1) default 0",
		"q14a"	=> "TINYINT(1) default 0",
		"q14b"	=> "TINYINT(1) default 0",
		"q14c"	=> "TINYINT(1) default 0",
		"q14d"	=> "TINYINT(1) default 0",
		"q14e"	=> "TINYINT(1) default 0",
		"q14f"	=> "TINYINT(1) default 0",
		"q15"	=> "TINYINT(1) default 0",
		"q16"	=> "TINYINT(1) default 0",
		"q17a"	=> "TINYINT(1) default 0",
		"q17b"	=> "TINYINT(1) default 0",
		"q17c"	=> "TINYINT(1) default 0",
		"q17d"	=> "TINYINT(1) default 0",
		"q17e"	=> "TINYINT(1) default 0",
		"q18"	=> "TINYINT(1) default 0",
		"q19"	=> "TINYINT(1) default 0",
		"q20"	=> "TINYINT(1) default 0",
		"q21"	=> "TINYINT(1) default 0",
		"q22"	=> "TINYINT(1) default 0",
		"q23"	=> "TINYINT(1) default 0",
		"q24"	=> "TINYINT(1) default 0",
		"q25"	=> "TINYINT(1) default 0",
		"q26"	=> "TINYINT(1) default 0",
		"q27"	=> "TINYINT(1) default 0",
		"q28a"	=> "TINYINT(1) default 0",
		"q28b"	=> "TINYINT(1) default 0",
		"q28c"	=> "TINYINT(1) default 0",
		"q28d"	=> "TINYINT(1) default 0",
		"q28e"	=> "TINYINT(1) default 0",
		"q28f"	=> "TINYINT(1) default 0",
		"q29"	=> "TINYINT(1) default 0",
		"q30"	=> "TINYINT(1) default 0",
		"q31"	=> "TINYINT(1) default 0",
		"q32"	=> "TINYINT(1) default 0",
		"q33"	=> "TINYINT(1) default 0",
		"q34"	=> "TINYINT(1) default 0",
		"q35"	=> "TINYINT(1) default 0",
		"q36"	=> "TINYINT(1) default 0",
		"q37"	=> "TINYINT(1) default 0",
		"q38a"	=> "TINYINT(1) default 0",
		"q38b"	=> "TINYINT(1) default 0",
		"q38c"	=> "TINYINT(1) default 0",
		"q38d"	=> "TINYINT(1) default 0",
		"q38e"	=> "TINYINT(1) default 0"
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

		$this->db->selectDB('research');
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
	
	function loadCSV() {
		ini_set('auto_detect_line_endings',TRUE);                                                
		$CSV_FILE = PATH_CONSOLE.'/survey_monkey.csv';
		$SITE_ID = 1;
		$COL_COUNT = 124;

		$insert_sql = "REPLACE INTO SurveyMonkeys (email,userid,siteid,q1a,q1b,q1c,q1d,q1e,q1f,q1g,q2a,q2b,q2c,q2d,q2e,q3a,q3b,q3c,q3d,q3e,q3f,q3g,q3h,q4a,q4b,q4c,q4d,q4e,q4f,q4g,q5a,q5b,q5c,q5d,q5e,q5f,q5g,q5h,q5i,q5j,q5k,q6,q7,q8a,q8b,q8c,q8d,q8e,q8f,q8g,q8h,q9a,q9b,q9c,q9d,q9e,q9f,q10a,q10b,q10c,q10d,q10e,q10f,q11a,q11b,q11c,q11d,q11e,q11f,q11g,q11h,q12a,q12b,q12c,q12d,q12e,q12f,q12g,q12h,q13a,q13b,q13c,q13d,q14a,q14b,q14c,q14d,q14e,q14f,q15,q16,q17a,q17b,q17c,q17d,q17e,q18,q19,q20,q21,q22,q23,q24,q25,q26,q27,q28a,q28b,q28c,q28d,q28e,q28f,q29,q30,q31,q32,q33,q34,q35,q36,q37,q38a,q38b,q38c,q38d,q38e) VALUES ";

		$row = 1;
		$error = '';
		$handle = fopen($CSV_FILE, "r");                          
		while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
			if ($row == 1) {
				$row++;
				continue;
			}
			$email = $data[0];
			$user_sql = "SELECT userid FROM UserCollectives WHERE email = '$email' AND siteid = $SITE_ID";
			$res = $this->db->queryC($user_sql);
			if (!$res) {
				$error .= "{Couldn't find userid with email: $email}";
				continue;
			} else {
				$res = $this->db->readQ($res);
			}
			$userid = $res->userid;

			$num = count($data);
			//echo "<p> $num fields in line $row: <br /></p>\n";                                   
			$row++;
			$value_str = "('$email', '$userid', '$SITE_ID',";
			//for ($c=1; $c < $num; $c++) {
			for ($c=1; $c < $COL_COUNT; $c++) {
				if (isset($data[$c]))
					$val = mysql_real_escape_string($data[$c]);
				else
					$val = 0;

				$value_str .= "'$val',";
			}
			$value_str = substr($value_str, 0, -1);
			$value_str .= '),';
			$insert_sql .= $value_str;
				//echo $data[$c] . "<br />\n";                                                   
		}
		fclose($handle);
		if ($row > 1) {
			$insert_sql = substr($insert_sql, 0, -1);
			$this->db->query($insert_sql);

			return "Inserted ".($row-1)." surveys.(Error: $error)";
		} else {
			return "ERROR:: Failed to insert survey data..(Error: $error)";
		}
	}

};
	
?>