<?php
/*
 * Local Neighborhood News
 */


class local
{
	var $db;	
	var $utilObj;
	var $templateObj;
	var $session;
	var $initialized;
	var $app;
		
	function __construct(&$db=NULL,&$templateObj=NULL) 
	{
		$this->initialized=false;
		if (is_null($db)) 
		{ 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
		if (!is_null($templateObj)) $this->templateObj=$templateObj;
		$this->initObjs();
	}
	
	function initObjs() {
		if ($this->initialized)
			return true;
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		if (is_null($this->templateObj)) 
		{ 
			require_once(PATH_CORE.'/classes/template.class.php');
			$this->templateObj=new template($this->db);
		} 
		$this->initialized = true;
	}

	function ajaxUpdateHood($hood='',$userid) {
		if ($hood<>'') {
			// update userinfo table with chosen hood
			$uit = new UserInfoTable($this->db);
			$ui = $uit->getRowObject();		
			$ui->load($userid);
			$ui->neighborhood=$hood;
			$ui->update();						
			$hood = strtolower(preg_replace("/[^a-zA-Z]/", "", $hood));            
			$this->templateObj->registerTemplates(MODULE_ACTIVE, 'newswire');
			if ($hood=='all') 
				$q="select id,title,caption,source,url,wireid	from Newswire WHERE (select count(*) from Newswire as f WHERE f.feedid= Newswire.feedid and f.id > Newswire.id ) < 1 AND feedType='localBlog' ORDER BY id DESC LIMIT 7;";			
			else
				$q="SELECT id,title,caption,source,url,wireid FROM Newswire WHERE source IN (select title from Feeds WHERE FIND_IN_SET('".$hood."',tagList)) ORDER BY id DESC LIMIT 7;";
			$this->templateObj->db->result=$this->templateObj->db->query($q);			
			if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
				$this->templateObj->db->setTemplateCallback('safeTitle', array($this->utilObj, 'encodeCleanString'), array('title', 200));
				$this->templateObj->db->setTemplateCallback('safeCaption', array($this->utilObj, 'encodeCleanString'), array('caption', 500));
				$this->templateObj->db->setTemplateCallback('safeUrl', array($this->utilObj, 'encodeUrl'), 'url');
				$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['sideWireList'],$this->templateObj->templates['sideWireItem']);           
			} else {
				$temp='Could not find any stories.';
			}								
		}
		$this->templateObj->resetCache('sideLocal_'.$userid);		
		return $temp;
	}
}
?>