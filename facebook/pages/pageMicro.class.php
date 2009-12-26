<?php

/* Micro Blog Room */

class pageMicro {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $mObj;

	function __construct(&$page) {
		$this->page=&$page;		
		$this->session=$this->page->session;
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'micro');               				
		require_once(PATH_FACEBOOK.'/classes/micro.class.php');
		$this->mObj=new micro($this->db,$this->templateObj,$this->session);
	}
	
	function fetch($option='browse',$id=0) {
		if ($option=='') $option='browse'; // fix default pass thru for tabs
		//$inside.=$this->buildSubNav($option);	
		$inside.='<div id="col_left"><!-- begin left side -->';
		switch ($option) {
			default: // browse page passthru
			break;
			case 'view': // show a single idea
				if ($id==0 AND isset($_GET['id'])) {
					$id=$_GET['id'];
					if (!is_numeric($id)) $this->page->decloak();					
				}
				if ($id>0) {
					$inside.='<h2>Highlighted Tweet</h2>';
					$inside.=$this->mObj->buildDisplay($id);	
				} 
			break;
		}
		$inside.=$this->buildBrowse();
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->buildSidebar();		
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('micro',$inside);		
		return $code;
	}		
	
	// option methods
	
	function buildBrowse() {
		if (isset($_GET['tagid'])) {
			$tagid=$_GET['tagid'];
			if (!is_numeric($tagid)) $this->page->decloak();					
		} 
	 	else
	 		$tagid=0;		
		$code=$this->templateObj->templates['microIntro'];					
		$code.=$this->mObj->fetchBrowse(false,$tagid,$view);
		return $code;
	}
		
	// sidebar methods

	function buildSidebar() {
		$code='';
		if ($this->session->isLoaded) {
			$comBox.='<div class="panel_1">';
			$comBox.=$this->page->buildPanelBar('Suggest New Twitterers','','');
			$comBox.=$this->mObj->buildCommentBox();
			$comBox.='</div><!--end "panel_1"-->';
			$code.=$comBox;			
		}
		$code.=$this->mObj->fetchLinkBox();
		$code.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=anySkyscraper" frameborder="0" scrolling="no" style="margin:0px 0px 0px -5px;padding:0px;width:170px;height:610px;"/>',$this->page->common['adWrapSkyscraper']);		
		return $code;	
	}
		
}	
?>