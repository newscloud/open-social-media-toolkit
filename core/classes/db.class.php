<?php

class cloudDatabase {
	// connection details
	var $database = '';
	var $username = '';
	var $password = '';
	var $hostname = '';
	var $site;
	var $base;
	var $admin;
	var $robotid;
	var $init;
	var $row;
	var $ui; // user information
	var $debug;
	var $logInserts=false;
	
	// current raw database handler
	var $handle;
	// current raw result 
	var $result;
	// current query string
	var $last_query = '';	
	// current count
	var $cnt;
	
	var $template_callbacks = array();
	
	function cloudDatabase() 
	{
		global $init; // global handle to the secret keys
		$this->database = $init['database'];
		$this->username = $init['username'];
		$this->password = $init['password'];
		$this->hostname = $init['hostname'];
		// fire it up	
		$this->_connect();
		$this->selectDB($this->database);
	}
	
	function _connect() 
	{
		$this->handle = mysql_pconnect($this->hostname, $this->username, $this->password) or die(mysql_error());
	}
	
	function _close() 
	{
		mysql_close($this->handle);
	}
	
	function selectDB($database_name)
	{
		mysql_select_db($database_name, $this->handle);
		// mysql_query("SET NAMES utf8");		
	}
	
	function query($q_string) 
	{
		if ($this->debug) 
			$this->log($q_string);
			
		$this->last_query = $q_string;
		$this->result = mysql_query($q_string, $this->handle);// or die(mysql_error()); // DJM: i'm sick of this crap
		
		if (!$this->result)
		{
		
	   		//echo '<pre>'. var_dump(debug_backtrace(), true) . '</pre>';
	   		 
	   		if ($this->debug)		
	   			echo "<p><h2> Query String: </h2>$q_string</p><br />";
	   			
	   		$error = mysql_error();
	   		$logMessage = '('. date('Y-m-d H:i:s', time()) .') '. "$error ... Query String: $q_string"; 
	   		$logHash = hash('md5',$logMessage);
	   		$this->log("[$logHash] $logMessage"); // embed unique hash (hopefully) in log message
	   		die("<h2>MySQL Error Encountered</h2> <p>Please notify site admins and show them this message (refence code: $logHash)</p>");		
		}
		return $this->result;
	}
		
	
	function queryC($q_string) {
		// queries and counts, returns false if empty
		if ($this->debug) 
			$this->log($q_string);
		
		$this->query($q_string);
		$this->cnt=$this->count();
		if ($this->cnt==0)
			return false;
		else
			return $this->result;
	}
	
	function count() {
		// count results from last query
		if ($this->result!==false)
			return mysql_num_rows($this->result);
		else
			return 0;
	}
	
	function read() {
		// read object from last query
		if ($this->result!==false)
			return mysql_fetch_object($this->result);
		else
			return false;
	}

	function countQ($result) {
		// count results of a specific query
		if ($result!==false)
			return mysql_num_rows($result);
		else
			return 0;
	}

	function readQ($result) {
		// read object from this query
		if ($result!==false)
			return mysql_fetch_object($result);
		else
			return false;
	}
	
	function insert($table,$columns,$values) {
		//echo "INSERT INTO $table ($columns) VALUES ($values);";
		$error = false;
		if (mysql_query("INSERT INTO $table ($columns) VALUES ($values);"))
			$result = $this->getId();
		else
			$result = "MySQL ERROR: ".mysql_error();

		if ($this->logInserts OR $this->debug)
			$this->log("INSERT INTO $table ($columns) VALUES ($values);");

		return $result;
	}

	function update($table,$setList,$whereList='') {
		if ($whereList<>'') $whereList="WHERE $whereList";

		//echo "UPDATE $table SET $setList $whereList;"; // bear with me for a sec guys :)
		if (mysql_query("UPDATE $table SET $setList $whereList;"))
			$result = 1;
		else
			$result = "MySQL ERROR: ".mysql_error();

		if ($this->debug)
			$this->log("UPDATE $table SET $setList $whereList;");		

		return $result;
	}
	
	function delete($table,$whereList='') {
		if ($whereList<>'') $whereList="WHERE $whereList";
//		echo ("DELETE FROM $table $whereList;");
		if (mysql_query("DELETE FROM $table $whereList;"))
			$result = 1;
		else
			$result = "MySQL ERROR: ".mysql_error();

		return $result;
	}
	
	function getId() {
		return mysql_insert_id();
		//$result = mysql_query("SELECT LAST_INSERT_ID();");
		//$id_array = mysql_fetch_row ($result);
		//return $id_array[0];
	}

	function countFoundRows() {
		$result=mysql_query("SELECT FOUND_ROWS();");
		$cntArray = mysql_fetch_array($result);
		return $cntArray[0];	
	}
	
	function buildIdList($q_string) {
		// builds a comma separated list of ids from the id column in the query
		// can work with buildQuotedList in utilities.class.php
		$str='';
		$this->result=mysql_query($q_string,$this->handle);
		while ($data=$this->read()) {
			$str.=$data->id.',';
		}
		// remove trailing comma
		$str=trim($str,',');
		return $str;
	}
	
	function paging($pageCurrent=1,$rowLimit=10,$link='',$jscriptFunction='refreshPage',$ajaxOn=false,$nav=NULL) {
		// $link is the url that the page navigation will point to - this functions add the page offset as the suffix
		// e.g. $link ='/search/keyword/tag/sort/' ... pages will link to '/search/keyword/tag/sort/pagenumber/'
		// previous query must use SQL_CALC_FOUND_ROWS
		$rowTotal=$this->countFoundRows();
		$pageTotal=ceil($rowTotal/$rowLimit);
		$nav->last=$pageTotal;
		$nav->current=$pageCurrent;
		$pageStart=($pageCurrent-4)>0 ? ($pageCurrent-4) : 1;
		$pageEnd=($pageCurrent+4)>$pageTotal ? $pageTotal : ($pageCurrent+4);
		$ellipsis='<span>...</span>';
		if ($rowTotal==0)
			return '';
		$text='<div class="pages">';
		// previous page
		if ($pageCurrent>1) {
			$text.='<a class="nextprev" onclick="'.$jscriptFunction.'(this,'.($pageCurrent-1).');return false;" href="'.$link.($pageCurrent-1).'">&#171; Previous</a>';
			$nav->previous=$pageCurrent-1;
		} else {
			$text.='<span class="nextprev">&#171; Previous</span>';
			$nav->previous=1;
		}
		// page 1 & 2
		if ($pageCurrent>5)
			$text.='<a onclick="'.$jscriptFunction.'(this,1);return false;" href="'.$link.'1/">1</a><a onclick="'.$jscriptFunction.'(this,2);return false;" href="'.$link.'2/">2</a>'.$ellipsis;
		// current nine pages
		for ($i=$pageStart;$i<=$pageEnd;$i++) {
			if ($i==$pageCurrent)
				$text.='<span class="current">'.$i.'</span>';
			else
				$text.='<a onclick="'.$jscriptFunction.'(this,'.$i.');return false;" href="'.$link.$i.'">'.$i.'</a>';
		}
		if (($pageTotal-$pageCurrent)>5)
			$text.=$ellipsis.'<a onclick="'.$jscriptFunction.'(this,'.($pageTotal-1).');return false;" href="'.$link.($pageTotal-1).'/">'.($pageTotal-1).'</a><a onclick="'.$jscriptFunction.'(this,'.$pageTotal.');return false;" href="'.$link.$pageTotal.'/">'.$pageTotal.'</a>';
		// next page
		if ($pageCurrent<$pageTotal) {
			$text.='<a class="nextprev" onclick="'.$jscriptFunction.'(this,'.($pageCurrent+1).');return false;" href="'.$link.($pageCurrent+1).'">Next &#187;</a>';
			$nav->next=$pageCurrent+1;
		} else {
			$nav->next=$pageCurrent;
			$text.='<span class="nextprev">Next &#187;</span>';
		}
		$text.='</div>';
		if (!$ajaxOn) {
			// removes all onclick segments
			$text = eregi_replace("onclick=\"[^\"]*", "", $text);
		}
		return $text;
	}
	
	function toDateTime($unixTime=0) {
		// convert unix time to MySQL DATETIME
		if ($unixTime==0) $unixTime=time();
		return date ("Y-m-d H:i:s", $unixTime);		
	}
	
	function dumpResult()
	{
		echo "<pre><h3>" . $this->last_query . "</h3>";
		while ($current_row = mysql_fetch_object($this->result)) {
			var_dump($current_row);
		}
		echo "</pre>";
	}
	
	function setTemplateCallback($merge_string, $callback_function, $col)
	/**
	 * registers a function (or object and method with array(object=>function)
	 * for use in template processing, where $col is the database field used as function argument
	 */
	{
		$this->template_callbacks[$merge_string]['func'] = $callback_function;
		$this->template_callbacks[$merge_string]['col'] = $col;
	}

	function resetTemplateCallbacks() {
		$this->template_callbacks=array();
	}
		
	function processTemplate($html_template, $row_limit = 99) 
	/**
	 * Takes a string (HTML template) 
	 * and replaces any merge fields in form of {field}
	 * if a match is found in a query result set
	 * <div id='{idrow}'>{firstname}</div> becomes:
	 * <div id='1234'>Chadwick</div>
	 * Multiple rows produce multiple iterations of the template
	 * custom callback functions can be defined with $this->setTemplateCallback
	 */
	{
		$ret = '';
		$l = 0;
		while ( $this->row = mysql_fetch_array($this->result) ) {
			if ($l < $row_limit) {
				$new_html = $html_template;
				// replace merge-fields with function callbacks
				foreach ($this->template_callbacks as $merge => $cb) {
					// Process column data
					$params = array();
					if (is_array($cb['col'])) {
						foreach ($cb['col'] as $param) {
							if (array_key_exists($param, $this->row))
								$params[] = $this->row[$param];
							else
								$params[] = $param;
						}
					} else {
						$params = $this->row[$cb['col']];
					}
					//echo "CALLBACK ".'<pre>'.print_r($merge, true). print_r($cb['func'][1],true) .print_r($this->row). '</pre>';
					
					//echo "replace CALLBACK {$cb['func'][1]}({$cb['col']}) on $merge, for row: <pre>".print_r($this->row,true)."</pre>";
					$new_html = str_replace("{" . $merge . "}", call_user_func_array($cb['func'], $params), $new_html);
					//$new_html = str_replace("{" . $merge . "}", call_user_func_array($cb['func'], $this->row[$cb['col']]), $new_html);
				} 
				// replace merge-fields with db vals
				foreach ($this->row as $key => $val) {
					$new_html = str_replace("{" . $key . "}", $val, $new_html);
				}				
			}
			$ret .= $new_html;
			$l++;
		}
		return $ret;
	}
	
	/*
	 * TODO: (maybe)
	 * From the PHP Manual comments on mysql_fetch_array - solution for identical field name disambiguation
	 * 
	 * 
	 		 Just a fairly useful (to me at least!) "implementation" of mysql_fetch_assoc to stop the clobbering of identical column names and allow you to work out which table produced which result column when using a JOIN (or simple multiple-table) SQL query:
			(assuming a live connection ...)
			<?php
			$sql = "SELECT a.*, b.* from table1 a, table2 b WHERE a.id=b.id"; // example sql
			$r = mysql_query($sql,$conn);
			if (!$r) die(mysql_error());
			$numfields = mysql_num_fields($r);
			$tfields = Array();
			for ($i=0;$i<$numfields;$i++)
			{
			    $field =  mysql_fetch_field($r,$i);
			    $tfields[$i] = $field->table.'.'.$field->name;
			}
			while ($row = mysql_fetch_row($r))
			{
			    $rowAssoc = Array();
			    for ($i=0;$i<$numfields;$i++)
			    {
			        $rowAssoc[$tfields[$i]] = $row[$i];
			    }
			//    do stuff with $rowAssoc as if it was $rowAssoc = mysql_fetch_assoc($r) you had used, but with table. prefixes
			}
			?>
			let's you refer to $rowAssoc['a.fieldname'] for example.
	 * 
	 * 
	 * 
	 * 
	 * 
	 */
	
	
	function userSessionOpen($save_path, $sess_name)
	{
		if (!$this->handle) {
			$this->_connect();
			return true;
		} else {
			return true;
		}
	}
	function userSessionClose()
	{
		if ($this->_close()) {
			return true;
		}
	}
	function userSessionRead($sess_id)
	{
		$this->query("SELECT data FROM php_session WHERE id = '" . addslashes($sess_id) . "';");
		list($data) = mysql_fetch_row($this->result);
		if (isset($data)) {
			return $data;
		} else {
			return NULL;
		}
	}
	function userSessionWrite($sess_id, $sess_data)
	{
		$this->query("REPLACE php_session (id, data) VALUES('" . addslashes($sess_id) . "','" . addslashes($sess_data) . "');");
		return $this->result;
	}
	function userSessionDestroy($sess_id)
	{
		$this->query("DELETE FROM php_session WHERE id = '" . addslashes($sess_id) . "';");
		return $this->result;
	}
	function userSessionGC($sess_maxlife)
	{
		$this->query(sprintf("DELETE FROM php_session WHERE t < DATE_SUB(NOW(),INTERVAL %d SECOND);", $sess_maxlife));
		return true; //ignore errors
	}

	function safe($str) {
		$str=addslashes(stripslashes($str));
		return $str;
	}

	function log($str='Empty log string',$filename=PATH_LOGFILE) {
		// write to newscloud log file for debugging
		// must touch and permission file at PATH_LOGFILE for this to work		
		$fHandle=fopen($filename,'a');
		if ($fHandle!==false) {
			if (!is_object($str) AND !is_array($str)) {
				fwrite($fHandle,$str."\n");				
			} else {
				fwrite($fHandle,print_r($str,true)."\n");
			}			
			fclose($fHandle);
		}
	}	
	
	function setDebug($status=true) {
		$this->debug=$status;
	}
}
?>