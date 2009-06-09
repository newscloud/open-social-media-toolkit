<?php




require_once(PATH_CORE.'/classes/dbRowObject.class.php');
class WeeklyScore extends dbRowObject  
{
 
};
	
class WeeklyScoresTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="WeeklyScores";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "WeeklyScore";
		
	static $fields = array(
		"userid" 		=> "BIGINT(20) default 0", 
		"weekOf"		=> "DATETIME",
		"eligibilityGroup"	=> "ENUM ('team','general','ineligible') default 'general'",
		"pointTotal" 	=> "INT(4) default 10" 
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
	
	function storeWeeklyPointsEarned($groups, $topN, $weekOf='')
	{
		require_once(PATH_CORE .'/classes/teamBackend.class.php');
		
		$tb = new teamBackend($this->db);

		
		$tb->updateCachedPointsAndChallenges(0,$weekOf); // recalculate ALL point values. Could take quite a while
	
		// store top 50 users from the group in the table
		if ($weekOf=='')
		{
			$weekOf = date('Y-m-d H:i:s',time()-60*60*24*7);
		}

		foreach ($groups as $group)
		{
			$q = $this->db->query("SELECT userid, cachedPointsEarnedLastWeek 
										FROM User 
										WHERE eligibility='$group' 										
										ORDER BY cachedPointsEarnedLastWeek DESC LIMIT 0,$topN;");
			
			$weeklyPoints = $this->getRowObject();
			$weeklyPoints->weekOf = $weekOf;
			$weeklyPoints->eligibilityGroup = $group;
			if($this->db->countQ($q)>0) 	
			{
				while ($data=$this->db->readQ($q))
				{
					$weeklyPoints->userid = $data->userid;
					$weeklyPoints->pointTotal = $data->cachedPointsEarnedLastWeek;
					$weeklyPoints->insert();
				}
				
			}
		}
	}
	
	

};
	






?>