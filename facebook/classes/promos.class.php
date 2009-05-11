<?php

/* Used for sending promotions */

class promos {
	
	var $db;	
	var $templateObj;
		
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
	}	

	function send()
	{
		// only at 12 am hour
		// gets a list of those who have registered between day x and day y
		// sends them a promo notification in blocks of 100		
		$this->db->log('inside promo send');
		require_once(PATH_CORE.'/classes/template.class.php');
		require_once PATH_FACEBOOK."/classes/app.class.php";
		$this->templateObj=new template($this->db);						
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'promos');
		$app=new app(NULL,true);
		$facebook=&$app->loadFacebookLibrary();
		$whereStr=$this->buildQueryString(3);
		$q=$this->db->query("SELECT count(userid) as cnt FROM User WHERE $whereStr;");		
		$data=$this->db->readQ($q);
		$cnt=$data->cnt;
		if ($cnt>0) {
			// send cbd ringtones promo
			$x=0;
			while ($x<(ceil($cnt/100))) {
				$idList=$this->db->buildIdList("SELECT fbId as id FROM User,UserInfo WHERE User.userid=UserInfo.userid AND $whereStr LIMIT ".($x*100).",100;");							
				$this->db->log('Send CBD promo notifications to'.$idList);
				$apiResult=$facebook->api_client->notifications_send($idList,$this->templateObj->templates['cbdPromo'] , 'app_to_user'); 	
				$x+=1;					
			}						
		}	
		// send EqEx Promo
		$whereStr=$this->buildQueryString(6);
		$q=$this->db->query("SELECT count(userid) as cnt FROM User WHERE $whereStr;");		
		$data=$this->db->readQ($q);
		$cnt=$data->cnt;
		if ($cnt>0) {
			// send eqex ringtones promo
			$x=0;
			while ($x<(ceil($cnt/100))) {
				$idList=$this->db->buildIdList("SELECT fbId as id FROM User,UserInfo WHERE User.userid=UserInfo.userid AND $whereStr LIMIT ".($x*100).",100;");							
				$this->db->log('Send EqEx promo notifications to'.$idList);
				$apiResult=$facebook->api_client->notifications_send($idList,$this->templateObj->templates['eqexPromo'] , 'app_to_user'); 	
				$x+=1;					
			}						
		}	
		
	}
	
	function buildQueryString($interval=3) {
		$queryStr='isMember=1 AND date_sub(NOW(), INTERVAL '.($interval+1).' DAY)<dateRegistered AND date_sub(NOW(), INTERVAL '.$interval.' DAY)>=dateRegistered';
		return $queryStr;				
	} 
}
	
?>