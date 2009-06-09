<?php


require_once(PATH_CORE . '/classes/dbRowObject.class.php');
require_once(PATH_CORE . '/classes/prizes.class.php');

class pageDBTest 
{

	var $page;
	var $db;
	var $facebook;
	var $fbApp;
	
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
	}

	function fetch() 
	{
		echo "DBTest Page\n";
	
		$code .= $this->userTest();			
	
		return $code;
		
		// make a new data access object
		$prizeTable = new PrizeTable($this->db);
		$prize = $prizeTable->getRowObject();

		
	//	$prize = new Prize($this->db);
		
		$prize->{'title'} = "Shiny Ball Toy";
		$prize->{'description'} = "A shiny ball";
		$prize->{'initialStock'} = 3;
		$prize->{'currentStock'} = 1;
		$prize->shortName = "ASHortName";
		$id = $prize->insert();
		 		
		echo ("New prize id = $id\n");
		
		echo "<pre>";
		print_r($this->fetchTableRowById("Prizes", $id));
		echo "</pre>";
		
		// now change the record we created and update it
		$prize->{'description'} = "A shiny ball WITH SPIKES";
		$prize->{'currentStock'} = 666;
		$prize->update();

		
		// use a handy logic function we added to the derived class
		$prize->decreaseStock();
		
		echo "<pre>";
		print_r($this->fetchTableRowById("Prizes", $id));
		echo "</pre>";
		
		// load a different record by given id
		$prize->load(2);

		echo "<pre>";
		print_r($this->fetchTableRowById("Prizes", 3));
		
		print_r($prize);
		echo "</pre>";
	
		// now delete the one we created earlier
		
		$prize->id = $id;
		$prize->delete();

		// try to fetch it, find out its gone!
		echo "<pre>";
		print_r($this->fetchTableRowById("Prizes", $id));
		
		echo "</pre>";
	
		
		$code.='<div id="pageContent">';	
		
		$code.='</div><!-- end pageContent -->';	
		return $code;
	}
	
	function userTest()
	{
		echo "\nDBTest User Test Page\n";
	
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); // TODO: cache instances of the tables globally
		$userInfoTable = new UserInfoTable($this->db);
		
		$user = $userTable->getRowObject();
		$userInfo = $userInfoTable->getRowObject();
		
		$isAppAuthorized = 0;
		$fbId = 666669;
		// create a test user
		$user->isAppAuthorized = $isAppAuthorized;
		if ($user->insert())
		{
			// inserted ok
			echo '<p>created $user:<pre>'. print_r($user, true).'</pre>';
			//$name = 'userid';
			//$user->{$name} = 1001;
			//$user->{'userid'} = 1001;
			echo "userid = {$user->userid}\n";
			
			if ($userInfo->createFromUser($user, $fbId))
			{
				echo 'Created new user info\n';
				echo "Primary keys should be equal: " . $user->userid . " ?= " . $userInfo->userid . ".\n";
				echo '<p>$userInfo:<pre>'. print_r($userInfo, true).'</pre>';
				
				$userInfoTest = $userInfoTable->getRowObject();
				$userInfoTest->loadFromFbId($fbId);
					
				echo '<p>loaded user info debug: $userInfo:<pre>'. print_r($userInfoTest, true).'</pre>';
				

				$userInfoTest->age = 111;
				$userInfoTest->update();
				
				$userInfoTest->loadFromFbId($fbId);
					
				echo '<p>updated user info debug: $userInfo:<pre>'. print_r($userInfoTest, true).'</pre>';								

				$user->name = "Roger Rabit";
				$user->update();
					// inserted ok
				echo '<p>updated $user:<pre>'. print_r($user, true).'</pre>';
	
			} else
			{
				echo "Failed to create UserInfo row\n";
			}
		} else
		{
			echo "Failed to insert user!\n";
		}
		
		
		
		
		return $code;	
		
		
	}
	
	// helper to verify our operations
	function fetchTableRowById($table, $id=0) 
	{
		echo "SELECT * FROM $table WHERE id=$id;";
		$q=$this->db->query("SELECT * FROM $table WHERE id=$id;");
		if ($q!==false) {
			$data=$this->db->readQ($q);
			return $data;
		} else 
			return false;	
	}
}

?>