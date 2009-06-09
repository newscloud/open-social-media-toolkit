<?php

class cleanupFacebook {
			
	var $db;
	var $nwObj;
	
	function __construct(&$db=NULL,$mode='daily')
	{
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
		switch ($mode) {
			case 'daily':				
				// delete old newswire stories
				/*require_once('newswire.class.php');	
				$this->nwObj=new newswire($this->db);
				$this->nwObj->cleanup();*/
			break;
			case 'weekly':
			break;
			case 'monthly':
			break;
			default:
			break;
		}
	}
	
	function flushDatabase() 
	{
		$this->db->delete("Prizes");
		$this->db->delete("UserInfo");
		$this->db->delete("Challenges");
		$this->db->delete("Orders");
		$this->db->delete("fbSessions");
		$this->db->delete("UserInvites");
		$this->db->delete("CompletedChallenges");
	
	}
}	
?>