<?php

class home {

	var $db;
	var $rowsPerPage=10;
	var $templateObj;
	var $utilObj;
	
	function home(&$db=NULL)
	{
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}

	function fetchHomePage($userid=0,$currentPage=1) {
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		require_once(PATH_CORE.'classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates('PHP');
		// userid is passed in because there is no session when refreshed with Ajax
		$code='';
		$startRow=($currentPage-1)*$this->rowsPerPage;
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Content WHERE date<now() AND date > date_sub(NOW(), INTERVAL 44 DAY) ORDER BY date DESC LIMIT $startRow,".$this->rowsPerPage.";");
		$rowTotal=$this->templateObj->db->countFoundRows();		
		$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$this->rowsPerPage,'?p=home&currentPage=');			
		$this->templateObj->db->setTemplateCallback('time_since', array($this->utilObj, 'time_since'), 'date');
		$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanEllipsis'), 'caption');
		$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
		$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['upcomingList'],$this->templateObj->templates['upcomingItem']);			
		$code.=$pagingHTML;
		return $code;
	}		
	
	function fetchHomeMatrix() {
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		require_once(PATH_CORE.'classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates('PHP');
		require_once PATH_CORE.'classes/feed.class.php';
		$feedObj=new feed($db);
		$cols=array();
		$colIndex=0; 
		$rowIndex=0;
		$q=$this->db->queryC("SELECT * FROM Feeds WHERE feedType='blog' ORDER BY title ASC;");
		if ($q) {
			$cnt=$this->db->countQ($q);			
			while ($blog=$this->db->readQ($q)) {
				// for each blog, do this
				$block='';				
				$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanEllipsis'), 'caption');
				$this->templateObj->db->setTemplateCallback('time_since', array($this->utilObj, 'time_since'), 'date');
				$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Home WHERE date<now() AND wireid=$blog->wireid ORDER BY date DESC LIMIT ".$this->rowsPerPage.";");		
				$block.=$this->templateObj->mergeTemplate($this->templateObj->templates['blogList'],$this->templateObj->templates['blogItem'],$blog->title);
				$cols[($colIndex%3)].=$block;
				$colIndex+=1;
				if (($colIndex%3)==0) {
					$rowIndex+=1;
					$cols[0].='<br />';
					$cols[1].='<br />';
					$cols[2].='<br />';
				}	
			}
		} else 
			$cnt=0;
		return $cols;
	}	
	
}
?>