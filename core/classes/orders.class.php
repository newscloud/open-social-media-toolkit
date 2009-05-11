<?php

require_once(PATH_CORE.'/classes/dbRowObject.class.php');
class Order extends dbRowObject  
{
 
}

/*	$table = "Orders";
		$manageObj->addTable($table,"id","INT(11) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn($table,"userid","BIGINT(20) default 0");  
		$manageObj->addColumn($table,"prizeid","INT(11) default 0");
		$manageObj->addColumn($table,"dateSubmitted","DATETIME");
		$manageObj->addColumn($table,"dateApproved","DATETIME");
		$manageObj->addColumn($table,"dateShipped","DATETIME");
		$manageObj->addColumn($table,"dateCanceled","DATETIME");
		$manageObj->addColumn($table,"dateRefunded","DATETIME");		
		$manageObj->addColumn($table,"reviewedBy","varchar(255) default ''");
		$manageObj->addColumn($table,"status","ENUM ('submitted','approved','shipped','canceled','refunded') default 'submitted'");				

	*/	
		
		
class OrderTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Orders";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "Order";
		
	static $fields = array(		
		"userid" => 		"BIGINT(20) default 0",  
		"prizeid" => 		"INT(11) default 0",
		"pointCost" =>		"INT(8) default 0",
		"dateSubmitted" => 	"DATETIME",
		"dateApproved" => 	"DATETIME",
		"dateShipped" => 	"DATETIME",
		"dateCanceled" => 	"DATETIME",
		"dateRefunded" => 	"DATETIME",		
		"reviewedBy" => 	"varchar(255) default ''",
		"status" => 		"ENUM ('submitted','approved','shipped','canceled','refunded') default 'submitted'"				
	
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
	
	function getNumberUserOutstandingOrdersForPrize($prizeid, $userid)
	{
	
	  	$q = $this->db->query(
		"SELECT SQL_CALC_FOUND_ROWS * 
			FROM Orders WHERE prizeid=$prizeid 
							AND userid=$userid
							AND status IN ('submitted', 'approved', 'shipped')
				;");										
									
		return $this->db->countQ($q);
	
	}
	
	function getNumberUserOrdersLast24Hours($userid)
	{
	  	$q = $this->db->query(
		"SELECT SQL_CALC_FOUND_ROWS * 
			FROM Orders WHERE userid=$userid
							AND dateSubmitted>=DATE_SUB(CURDATE(),INTERVAL 1 DAY); 
				;");										
									
		return $this->db->countQ($q);
		
	}
};
		
?>