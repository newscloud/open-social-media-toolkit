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
		if (!is_numeric($topic)) $this->page->decloak();		
		$ft=$this->forumObj->loadAndTouchForumTopic($topic);
		$inside.='<div id="col_left"><!-- begin left side -->';
		$inside.='<h5><a href="?p=wall">Topics &raquo;</a> '.$ft->title.'</h5>';
		if ($ft->intro<>'') {
			$inside.='<p>'.$ft->intro.'</p>';		
		}
		$inside.='<fb:comments xid="'.CACHE_PREFIX.'_wall_'.$topic.'" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=wall&topic='.$topic.'"></fb:comments>';	
		$inside.='<br />'.$this->fetchLinkBox($ft);
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->teamObj->fetchSidePanel('leaders');		
		$inside.='</div> <!-- end right side -->';
//		if ($mode=='teamWrap') return $inside;
//		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('wall',$inside);		
		return $code;		
	}

	function fetchLinkBox($ft=null) {
 		$topicLink=URL_CANVAS.'?p=wall&topic='.$ft->id;
		$title=htmlentities($this->templateObj->ellipsis($ft->title),ENT_QUOTES);
		$caption=htmlentities($this->templateObj->ellipsis($ft->intro,350),ENT_QUOTES);
		$shareButton='<div style="float:left;padding:0px 5px 0px 0px;display:inline;"><fb:share-button class="meta"><meta name="title" content="'.$title.'"/><meta name="description" content="'.$caption.'" /><link rel="target_url" href="'.$topicLink.'"/></fb:share-button><!-- end share button wrap --></div>';
 		$code = '<div  id="actionLegend">'.$shareButton.'<p class="bold">Link to this topic</p>';
          $code.= '<div class="pointsTable"><table cellspacing="0"><tbody>'.
				'<tr><td><input class="inputLinkNoBorder" type="text" value="'.$topicLink.'" onfocus="this.select();" /></td></tr>'.
				'</tbody></table></div><!-- end points Table --></div><!-- end topic link box -->';
 		return $code;	
 	}
	
	function fetchTopics ($mode='',$option='') {
		$inside.='<div id="col_left"><!-- begin left side -->';
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT * FROM ForumTopics WHERE isHidden=0 ORDER BY title ASC;");
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
	
	function fetch($mode='fullPage',$option='alltime',$arg3='') {
			if (isset($_GET['topic'])) {
				return $this->fetchConversation($_GET['topic']);
			} 	if ($arg3<>'') {
					return $this->fetchConversation($arg3);
			} else
				return $this->fetchTopics($mode='fullPage',$option='alltime');			
		/*
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
		*/
	}		

}	
?>