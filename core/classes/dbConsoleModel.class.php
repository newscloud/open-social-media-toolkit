<?php
require_once('db.class.php');

class dbConsoleModel
{
	//var $id = 0;
	private $idname = 'id'; // default name of primary key/id
	private $table;
	private $fields; // wont currently contain a shallow copy of the table field list, but should 
	private $nonidfields; // just lists all fields except the primary key
	var $db;
	static $debug = 0;
	function __construct($table, $fields = array(), $idname='id' )
  	{
		$this->db=new cloudDatabase();
  
		// skip initializing table for 'all' so we have access to everything
		if ($table == 'all')
			$this->table = false;
		else
			$this->table = $table;
		/*
	    foreach( $fields as $key )
	    {
	    	$this->fields[ $key ] = null;
	    }
		*/
	    		
		// new		
		$this->idname = $idname;
		
	    $this->nonidfields = $fields; // use this copy of field list for db ops 
	    $this->fields[$this->idname] = null; // so id field can be set/get correctly
		
  	}

	function __get( $key )
  	{
	    return $this->fields[ $key ];
  	}

  	function __set( $key, $value )
  	{	
    	if ( array_key_exists( $key, $this->fields ) )
    	{
      		$this->fields[ $key ] = $value;
      		return true;
    	}
    	return false;
  	}

 	/*
 	 * 
 	 * Inserts a new record in to the database, and sets $this->{$idname} to the returned id
  	 * 
  	 * 
  	 */  
	//function insert($set_id=NULL)
	function insert($form_array) {
		$fields = '';
		$values = '';
		foreach ($form_array as $field => $value) {
			if ($field == 'id') {
				continue;
			} else {
				$fields .= "$field,";
				$values .= "'".mysql_real_escape_string($value)."',";
			}
		}
		$fields = substr($fields, 0, -1);
		$values = substr($values, 0, -1);

		if (dbConsoleModel::$debug) echo "\n<br />insert string: (\"$this->table\", \"$fields\", \"$values\")<br />\n";
		return $this->db->insert($this->table, $fields, $values);

	}

	/*
	 * Updates a record based on $id, using current values of all fields
	 * Assumes $id is valid  
	 */
	function update($form_array)
	{
		$id = $form_array['id'];
   	
		$update_sql = '';
		foreach ($form_array as $field => $value) {
			if ($field == 'id')
				continue;
    		$update_sql .= "$field='".mysql_real_escape_string($value)."',";
    	}
		$update_sql = substr($update_sql, 0, -1);
		
    	$where_sql = "{$this->idname}=$id";
    	
    	if (dbConsoleModel::$debug) echo "<br>update_sql: ($this->table, $update_sql, $where_sql)<br>";
		return $this->db->update($this->table,$update_sql,$where_sql);
	}
	
	/*
	 * Loads contents of row at specified id in the table, returns the assoc array or false if not found
	 * 
	 */
	
	function load($id=0, $idname=NULL) // option to override idname useful when cross-referencing tables with multiple unique id keys
	{
		//echo "<p>id: $id</p>";
		$idname = !$idname ? $this->idname : $idname;
		if ($id < 1) return false;
		if (dbConsoleModel::$debug) echo "<p>SELECT * FROM {$this->table} WHERE {$idname}=$id</p>";

		$res = $this->db->query("SELECT * FROM {$this->table} WHERE {$idname}=$id");
		
		//print_r($res);
			
		return mysql_fetch_assoc($res);
	}
	
	/*
	 * Loads all rows from a table, returns the array of results or an empty array on null
	 * 
	 */
	
	function load_all($sql = false) // option to override idname useful when cross-referencing tables with multiple unique id keys
	{
		$results = array();
		if (!$sql) {
			if (dbConsoleModel::$debug) echo "<p>SELECT * FROM {$this->table} ORDER BY {$this->idname} DESC</p>";
			$res=$this->db->query("SELECT * FROM {$this->table} ORDER BY {$this->idname} DESC");
		} else {
			if (dbConsoleModel::$debug) echo "<p>$sql</p>";
			$res=$this->db->query($sql);
		}
		
		//print_r($res);
		while ($row = mysql_fetch_assoc($res))
			$results[] = $row;
			
		return $results;
	}

	function delete($id = 0) 
	{
		if ($id < 1) return false;
		//echo "This->{$this->idname} = {$this->{$this->idname}})\n";
		//echo "{$this->idname}={$this->id}\n";
	
	 	return $this->db->delete($this->table,"{$this->idname}=$id");
		
	}

	function block($id = 0, $block = 1) 
	{
		if ($id < 1) return false;
		//echo "This->{$this->idname} = {$this->{$this->idname}})\n";
		//echo "{$this->idname}={$this->id}\n";
	
	 	return $this->db->query("UPDATE {$this->table} SET isBlocked = {$block} WHERE {$this->idname} = $id");
		
	}
	function query($sql) {
		return $this->db->query($sql);
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
	
}

?>
