<?php

// to do - UserForumTopics table
// topic id, last visit
// code to get topic list table for a specific user showing new posts since last visit
	
require_once(PATH_CORE.'/classes/dbRowObject.class.php');
class ForumTopics extends dbRowObject  
{
 
}
		
class ForumTopicsTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="ForumTopics";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "ForumTopics";
		
	static $fields = array(		
		"title" => 			"VARCHAR(255) default ''",
		"intro" => 			"TEXT default ''",
		"lastChanged" => 		"DATETIME",
		"numPostsToday" => 	"INT(4) default 0",
		"numViewsToday" => 	"INT(4) default 0"
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
	
	function testPopulate()
	{
		
		$Forum = $this->getRowObject();
		$Forum->title = 'Rescue a cute animal from a politically incorrect predicament';
		$Forum->pointValue = 100;
		$Forum->dateStart = date('Y-m-d H:i:s', time());
		if (!self::checkForumExistsByTitle($Forum->title)) $Forum->insert();
		
	}
	
	
	function checkForumExistsByTitle($title)
  	{
  		
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE title='$title'");
		return $chkDup;
  		
  	}
 	
};		

class Forums {
	
	var $db;
	var $templateObj;
		
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
		$this->setupLibraries();
			
	}
	
	function loadAndTouchForumTopic($id=0) {
		$this->touchForumTopic($id);
		$ftTable = new ForumTopicsTable($this->db);		
		$ft = $ftTable->getRowObject();
		$ft->load($id);
		return $ft;
	}

	function touchForumTopic($id=0) {
		$this->db->update("ForumTopics","numViewsToday=numViewsToday+1","id=$id");		
	}
	
	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);		
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'Forums');               
	}
	
	function fetchForumList($sort, $currentPage=1) 
	{
				
    $cacheName='chList_'.$sort.'_'.$currentPage;
    if ($this->templateObj->checkCache($cacheName,15)) {
        // still current, get from cache
        $code=$this->templateObj->fetchCache($cacheName);
    } else {
		// to do - take out rows per page
	
		$where= "WHERE status='enabled'";
		
		if ($sort=='isFeatured')
			$where.=' AND isFeatured=1';
		
		if ($sort =='pointValue')
			$sort = "$sort DESC";
		
		$code='';
		$rowsPerPage = 2*ROWS_PER_PAGE;
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		$ForumList=$this->templateObj->db->query(
		"SELECT SQL_CALC_FOUND_ROWS 
				thumbnail, title, pointValue, id, 
				MONTHNAME(dateStart) AS monthstart,
				DAY(dateStart) AS daystart,
				MONTHNAME(dateEnd) AS monthend,
				DAY(dateEnd) AS dayend,description,
				(CASE type WHEN 'automatic' THEN 'hidden' 
							WHEN 'submission' THEN ''
							END) AS submissionStyle
				FROM Forums $where ORDER BY type DESC, $sort LIMIT $startRow,".$rowsPerPage.";"); 
		
		// to do - later we'll move these template defs
		if ($this->templateObj->db->countQ($ForumList)>0) 
		{		
			$rowTotal=$this->templateObj->db->countFoundRows();
			$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$rowsPerPage,'&p=Forums&currentPage='); // later put back page->rowsPerPage
			// $this->templateObj->db->setTemplate`('comments', array($this, 'decodeComment'), 'comments');
			$this->templateObj->db->setTemplateCallback('pointValue', array($this, 'getPointValue'), 'pointValue');
			//$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['commentList'],$this->templateObj->templates['commentItem']);
			$code.=$pagingHTML;
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates[ForumPanelList],$this->templateObj->templates[ForumPanelItem]);
			$code.=$pagingHTML;
		} else {
			$code.='No Forums found.';
		}			

        $this->templateObj->cacheContent($cacheName,$code);
    }
		return $code;
		
	}
			
}

?>