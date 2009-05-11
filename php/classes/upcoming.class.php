<?php

class upcoming {

	var $db;
	var $rowsPerPage=7;
	var $templateObj;
	var $utilObj;
	
	function upcoming (&$db=NULL)
	{
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}
	
	function fetchUpcomingStories($userid=0,$currentPage=1,$rowsPerPage=7) {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates('PHP');
		
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		
		// userid is passed in because there is no session when refreshed with Ajax
		$code='';
		$ageRange=14; // no story older than this number of days
		$startRow=($currentPage-1)*$rowsPerPage;
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Content WHERE date>date_sub(NOW(), INTERVAL $ageRange DAY) ORDER BY date DESC LIMIT $startRow,".$rowsPerPage.";");
		$rowTotal=$this->templateObj->db->countFoundRows();
		$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$rowsPerPage,'?userid='.$userid.'&p=upcoming&currentPage=');			
		$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getStoryImage'), 'imageid');
		$this->templateObj->db->setTemplateCallback('time_since', array($this->utilObj, 'time_since'), 'date');
		$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanEllipsis'), 'caption');
		$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
		$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
		$this->templateObj->db->setTemplateCallback('cmdAdd', array($this->templateObj, 'commandAdd'), 'siteContentId');
		$this->templateObj->db->setTemplateCallback('cmdRead', array($this->templateObj, 'commandRead'), 'permalink');
		$code=$this->templateObj->mergeTemplate($this->templateObj->templates['upcomingListNoTitle'],$this->templateObj->templates['upcomingItem']);			
		$code.=$pagingHTML;
		return $code;
	}		

}
?>