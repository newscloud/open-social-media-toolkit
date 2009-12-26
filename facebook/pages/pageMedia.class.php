<?php

/* Crowdsharing media class */
class pageMedia {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $mObj;
	var $isAppTab=false;

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
		require_once(PATH_FACEBOOK.'/classes/media.class.php');
		$this->mObj=new Media($this->db,$this->templateObj,$this->session);
	}
	
	function fetch($option='view') {
		if ($option=='') $option='view'; // fix default pass thru for tabs
		if (defined('ENABLE_MEDIA_PROFILE'))
			$inside.=$this->buildSubNav($option);	
		$inside.='<div id="col_left"><!-- begin left side --><br />';
		switch ($option) {
			default: // view page
				if (isset($_GET['id'])) {
					$id=$_GET['id'];					
					if (!is_numeric($id)) $this->page->decloak();					
				} else {
					$id=0;
				}
				$media=$_GET['media'];				
				$inside.=$this->mObj->buildMediaView($id,$media);					
			break;
			case 'pro': // profile customization
				$inside.=$this->mObj->buildProfile();
			break;
		}
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->mObj->buildSidebar();		
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('media',$inside);		
		return $code;
	}
	
	function buildSubNav($currentSub='view') 
	{
		$pages = array(
		 'view' => 'Browse Media',
		 'pro'=> 'Customize Your Profile Picture'
		 );		
		 $tabs='<div id="subNav" class="tabs clearfix"><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		 $i=0;
		 foreach (array_keys($pages) as $pagename)
		 {		 	
		 	if ($i==0) {
		 		$clsName='class="first"';
		 	} else {
		 		$clsName='';	
		 	}
	 		$tabs.='<li '.$clsName.'><a id="subtab'.$pagename.'" href="?p=media&o='.$pagename.'" '.($pagename=='pro'?'requirelogin="1"':'').' '.($currentSub==$pagename?'class="selected"':'').'>'.$pages[$pagename].'</a></li>';	
		 	$i++;
		 }
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
	 	return $tabs;
 	}	
}	
?>