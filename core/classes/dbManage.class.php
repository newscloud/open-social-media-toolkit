<?php

	class dbManage {
		var $db;
		var $ssObj;
		var $debug;
		
		function dbManage($debug=false) {
			require_once('db.class.php');
			$this->db=new cloudDatabase();
			$this->debug=$debug;
			require_once (PATH_CORE.'/classes/systemStatus.class.php');
			$this->ssObj=new systemStatus($this->db);			
		}
		
		function modifyLibrary($path='',$lib='') {
			$lastModified=$this->ssObj->getState('dm_'.$lib);
			if ($lastModified=='' OR intval($lastModified)<filemtime($path.$lib)) {
				$this->ssObj->setState('dm_'.$lib,time()); // add or update in sysStatus
				$this->db->log('NEED TO UPDATE:'.$lib);
				return true; // file is out of date
			} else {
				$this->db->log('OK:'.$lib);		
				return false;
			}
		}
		
		function addColumn($table,$column,$column_info) {
			$result=$this->db->query("SHOW COLUMNS FROM $table LIKE '$column';");
			if (mysql_num_rows ($result) == 0)
			{
				$this->db->query("ALTER TABLE $table ADD COLUMN $column $column_info;");
				$this->display($column.' added to '.$table.'<br><br>');
			} else {
				$this->display($column.' already exists in '.$table.'<br><br>');
			}
			return;
		}
		
		function modifyColumn($table,$column,$column_info) {
			$result=$this->db->query("ALTER TABLE $table MODIFY $column $column_info;");
			$this->display($column.' modified in '.$table.': '.$column_info.'<br><br>');
		}
		
		function updateAddColumn($table,$column,$column_info)  // combined add/modify for convenience
		{
			//$this->addColumn($table,$column,$column_info);
			$this->debug= 1;
			$result=$this->db->query("SHOW COLUMNS FROM $table LIKE '$column';");
			if (mysql_num_rows ($result) == 0)
			{
				// need to add the column
				$this->db->query("ALTER TABLE $table ADD COLUMN $column $column_info;");
				$this->display($column.' added to '.$table.'<br><br>');
			} else 
			{
				$this->display($column.' already exists in '.$table.'<br><br>');
				// so modify it!
				$this->modifyColumn($table,$column,$column_info);
			}
			return;
		}
		
		function dropColumn($table,$column) {
			$result=$this->db->query("SHOW COLUMNS FROM $table LIKE '$column';");
			if (mysql_num_rows ($result) == 1)
			{
				$this->db->query("ALTER TABLE $table DROP COLUMN $column;");
				$this->display($column.' dropped from '.$table.'<br><br>');
			} else {
				$this->display($column.' did not exist in '.$table.'<br><br>');
			}
			return;
		}
		
		function addTable($table,$column,$column_info,$type='') {
			$result = mysql_query ("show tables like '$table';");
			if (mysql_num_rows ($result) == 0)
			{	
				if ($type=='')
					mysql_query ("CREATE TABLE $table ($column $column_info, PRIMARY KEY ($column));");
				else
					mysql_query ("CREATE TABLE $table ($column $column_info, PRIMARY KEY ($column)) ENGINE=$type;");
				$this->display($table.' table created.<br/>');
			}
			else
			{
				$this->display($table.' table already created.  Not doing anything.<br/>');
			}
			return;
		}

		function updateAddKey($table,$modifier, $keyOrIndex, $name, $def)
		{
			$result = mysql_query ("show tables like '$table';");
			if (mysql_num_rows ($result) >0)
			{	
			
				mysql_query($dropQ = "ALTER TABLE $table DROP $keyOrIndex $name;");					
				$this->display("$table table dropped $keyOrIndex $name <br/>");
				mysql_query($addQ = "ALTER TABLE $table ADD $modifier $keyOrIndex $name $def;");
				$this->display("$table table added $modifier $keyOrIndex $name $def<br/>");
			}
			else
			{
				$this->display($table.' table does not exist.  Not doing anything.<br/>');
			}
	
		}
		
		function dropTable($table) {
			$result = mysql_query ("show tables like '$table';");
			if (mysql_num_rows ($result) >0)
			{	
				mysql_query ("DROP TABLE $table;");
				$this->display($table.' table deleted.<br/>');
			}
			else
			{
				$this->display($table.' table does not exist.  Not doing anything.<br/>');
			}
			return;
		}
		
		function display($str) {
			if ($this->debug)
				echo $str;
		} 
		
	}
?>

