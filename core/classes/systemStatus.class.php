<?php		
	
class systemStatus {
/**
 * Class for managing system status
 **/	
	var $db;

	function systemStatus(&$db=NULL)
	{
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;
	}
	
	function checkTable() {
		// check for existence of SystemStatus table
		$q=$this->db->query("SHOW TABLES LIKE 'SystemStatus';");
		if ($this->db->countQ($q)>0)
			return true;
		else 
			return false;	
	}
	
	function getState($field,$multi=false,$returnQuery=false) {
		// multi gets a random non-unique system variable e.g. system announcements
		// returnQuery returns the query rather than the value
		if ($multi)
			$result=$this->db->query("SELECT * FROM SystemStatus WHERE name='$field' ORDER BY RAND();");
		else 
			$result=$this->db->query("SELECT * FROM SystemStatus WHERE name='$field';");
		if ($returnQuery) return $result;
		if ($this->db->countQ($result)==0) return '';
		$data=$this->db->readQ($result);
		// return the non-null value string or numeric
		if (is_null($data->strValue) OR $data->strValue=='') 
			return $data->numValue;
		else
			return $data->strValue;
	}
	
	function setState($field,$value) {
		$result=$this->db->query("SELECT * FROM SystemStatus WHERE name='$field';");
		if ($this->db->countQ($result)==0) {
			if (!is_numeric($value))
				$this->db->insert("SystemStatus","name,strValue","'$field','$value'");
			else 
				$this->db->insert("SystemStatus","name,numValue","'$field',$value");				
		} else {		
			if (!is_numeric($value)) {
				$this->db->update("SystemStatus","strValue='$value'","name='$field'");
			} else {
				$this->db->update("SystemStatus","numValue=$value","name='$field'");
			}
		}
	}

	function insertState($field,$value) {
		if (!is_numeric($value))
			$this->db->insert("SystemStatus","name,strValue","'$field','$value'");
		else 
			$this->db->insert("SystemStatus","name,numValue","'$field',$value");				
	}
	
	function updateState($id,$value) {
		// update system status by row id
		// useful for non-unique system variables
		if (!is_numeric($value)) {
			$this->db->update("SystemStatus","strValue='$value'","id='$id'");
		} else {
			$this->db->update("SystemStatus","numValue=$value","id='$id'");
		}
	}

	function getProperties($obj=NULL) {
		if (is_null($obj)) { 
			$obj= new stdClass;
		}
		$obj->cloudid=$this->getState('cloudid');
		$obj->name=$this->getState('name');
		$obj->permalink=$this->getState('permalink');
		$obj->notifications_per_day=$this->getState('notifications_per_day');
		$obj->requests_per_day=$this->getState('requests_per_day');
		$obj->siteStatus=$this->getState('siteStatus');
		$obj->max_sessions=$this->getState('max_sessions');
		return $obj;	
	}
	
	function setProperties($info) {
		$this->setState('groupid',$info[groupid]);
		$this->setState('cloudid',$info[groupid]);
		$this->setState('name',$info[name]);
		$this->setState('permalink',$info[name_scored]);						
	}

	function loadFacebookProperties() {
		// loads fbApp_ settings
		$propList=array();
		$q=$this->db->query("SELECT * FROM SystemStatus WHERE name like 'fbApp_%';");
		while ($data=$this->db->readQ($q)) {
			$temp=str_replace('fbApp_','',$data->name);
			if (is_null($data->strValue) OR $data->strValue=='') 
				$propList[$temp]=$data->numValue;
			else
				$propList[$temp]=$data->strValue;			
		}	
		return $propList;
	}
	
	function resetAnnouncements() {
		// deletes all announcements
		$this->db->delete("SystemStatus","name='announcement'");
	}

	
} // end of class

?>