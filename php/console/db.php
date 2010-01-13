<?php

class DB {
	var $dbh;
	var $dbname = 'research';
	
	function __construct($dbname = false) {
		global $init;
		if (!$dbname)
			$dbname = $init['database'];
		else
			$this->dbname = $dbname;
		$this->dbh = mysql_connect($init['hostname'],$init['username'], $init['password']);

		mysql_select_db($this->dbname);
	}

	function query($sql) {
		return mysql_query($sql, $this->dbh);
	}

	function get_pods() {
		$data = array();
		$pods = mysql_query("SELECT * FROM Admin_DataStore WHERE type = 
'pod'", $this->dbh);
		while (($pod = mysql_fetch_assoc($pods)) !== false) {
			$data[$pod['name']] = array(
				'id' => $pod['id'],
				'data' => json_decode($pod['data'], true)
			);
		}

		return $data;
	}

	function get_pod($podid = 0) {
		if ($podid == 0) {
			return false;
		} else {
			$result = mysql_query("SELECT * FROM Admin_DataStore WHE
RE type = 'pod' AND id = $podid", $this->dbh);
			$arr = mysql_fetch_assoc($result);
			if (isset($arr['id']) && is_numeric($arr['id'])) {
				$data = array(
					'id' => $arr['id'],
					'data' => json_decode($arr['data'], true
)
				);
				return $data;
			} else {
				return false;
			}
		}
	}

	function selectdb($dbname) {
		mysql_select_db($dbname, $this->dbh);
	}
}

?>
