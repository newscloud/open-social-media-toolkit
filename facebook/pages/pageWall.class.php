<?php

/* Provides Facebook-client specific action team help */
class pageWall {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $utilObj;
	var $forumObj;
	var $isAppTab=false;

	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'teamactivity');               				
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'forum');               				
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);		
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		require_once(PATH_CORE.'/classes/forum.class.php');
		$this->forumObj=new Forums($this->db);
	}

	function fetchConversation($topic=0) {
		// to do 
		// increment views
		// add last viewed by user to UserForumHistory
		$ft=$this->forumObj->loadAndTouchForumTopic($topic);
		$inside.='<div id="col_left"><!-- begin left side -->';
		$inside.='<h5><a href="?p=wall">Topics &raquo;</a> '.$ft->title.'</h5>';
		if ($ft->intro<>'') {
			$inside.='<p>'.$ft->intro.'</p>';		
		}
		$inside.='<fb:comments xid="'.CACHE_PREFIX.'_wall_'.$topic.'" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=wall&topic='.$topic.'"></fb:comments>';	
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->teamObj->fetchSidePanel('leaders');		
		$inside.='</div> <!-- end right side -->';
//		if ($mode=='teamWrap') return $inside;
//		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('wall',$inside);		
		return $code;		
	}
	
	function fetchTopics ($mode='',$option='') {
		$inside.='<div id="col_left"><!-- begin left side -->';
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT * FROM ForumTopics ORDER BY title ASC;");
		$this->templateObj->db->setTemplateCallback('lastChanged', array($this->utilObj, 'time_since'), 'lastChanged');
		$inside.=$this->templateObj->mergeTemplate($this->templateObj->templates['forumTopicsList'],$this->templateObj->templates['forumTopicsListItem']);
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->teamObj->fetchSidePanel('leaders');		
		$inside.='</div> <!-- end right side -->';
//		if ($mode=='teamWrap') return $inside;
//		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('wall',$inside);		
		return $code;
	}
	
	function fetch($mode='fullPage',$option='alltime') {
		if (CACHE_PREFIX=='sea' OR CACHE_PREFIX=='hd') {
			if (isset($_GET['topic'])) {
				return $this->fetchConversation($_GET['topic']);
			} else
				return $this->fetchTopics($mode='fullPage',$option='alltime');			
		}
		$tabs.=$this->teamObj->buildSubNav('wall');	
		$inside.='<div id="col_left"><!-- begin left side -->';
		$inside.='<fb:comments xid="'.CACHE_PREFIX.'_wall" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=wall"></fb:comments>';	
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->teamObj->fetchSidePanel('leaders');		
		$inside.='</div> <!-- end right side -->';
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside);		
		return $code;
	}		

}	
?>