<?php

class pageStories {

	var $page;
	var $db;
	var $facebook;
	var $teamObj;
	
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
	}
	
	function fetch($option='all',$filter='all') {
		// override for if index sends default blank string
		if ($option=='') $option='all';
		if ($filter=='') $filter='all';
		if (isset($_GET['currentPage']))
			$currentPage=$_GET['currentPage'];
		else
			$currentPage=1;
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);		
		require_once(PATH_CORE.'/classes/newswire.class.php');
		// call before widetip to register template
		$nwObj=new newswire($this->db);	
		$stories=$nwObj->fetchNewswire($option,$filter,$this->page->session->ui->memberFriends,$currentPage);
		// build the newswire page
		$inside='<div id="col_left"><!-- begin left side -->';
		if ($this->page->session->ui->hideTipStories==0) {
			$inside.=$nwObj->templateObj->templates[wideTip];			
		}
		$inside.=$nwObj->buildStoriesTabs($option);
		$inside.='<div id="newswireWrap">';
		$inside.=$stories;
		$inside.='<!-- end newswireWrap --></div>';		
		$inside.='<!-- end left side --></div><div id="col_right">';
		$inside.='<div id="introPanel">';
		$inside.='<p><a href="?p=postStory" onclick="switchPage(\'postStory\');return false;" class="btn_1">Post a story</a></p>';			
		$inside.='<!-- end of introPanel --></div>';
		
		$inside.=$this->teamObj->fetchSidePanel('stories');
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;
		$code=$this->page->constructPage('stories',$inside);
		return $code;
	}	
}
?>