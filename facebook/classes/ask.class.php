<?php
/*
 * Ask questions and get answers
 */

/* To do 

	- Check session for new members when posting a question. see if fbId is available on post - or request log in first with link back to question page

*/

require_once (PATH_CORE . '/classes/dbRowObject.class.php');
class questionRow extends dbRowObject
{
	
}

class askQuestionsTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="AskQuestions";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "questionRow";
	static $fields = array(		
		"userid" => "BIGINT(20) unsigned default 0",
		"question" 		=> "VARCHAR(255) default ''",
		"details" 		=> "TEXT default NULL",
		"tagid" 		=> "INT(11) default 0",
		"videoid" 		=> "INT(11) default 0",
		"numLikes" 		=> "INT(4) default 0",
		"numComments" 		=> "INT(4) default 0",
		"numAnswers" 		=> "INT(4) default 0",
		"dt" 				=> "datetime"
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
	
	//  table creation routine, same for all *Table classes 		
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
}

class answerRow extends dbRowObject
{
	
}

class askAnswersTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="AskAnswers";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "answerRow";
	static $fields = array(		
		"questionid" 		=> "INT(11) default 0",
		"userid" => "BIGINT(20) unsigned default 0",
		"answer" 		=> "TEXT default NULL",
		"videoid" 		=> "INT(11) default 0",
		"numLikes" 		=> "INT(4) default 0",
		"numComments" 		=> "INT(4) default 0",
		"dt" 				=> "datetime"
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
	
	//  table creation routine, same for all *Table classes 		
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
}


class ask
{
	var $db;	
	var $utilObj;
	var $templateObj;
	var $session;
	var $initialized;
	var $app;
		
	function __construct(&$db=NULL,&$templateObj=NULL,&$session=NULL) 
	{
		$this->initialized=false;
		if (is_null($db)) 
		{ 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
		if (!is_null($templateObj)) $this->templateObj=$templateObj;
		if (!is_null($session)) $this->session=$session;
		$this->initObjs();
	}
	
	function setAppLink(&$app) {
		$this->app=$app;
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
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'ask');
		$this->initialized = true;
	}
	
	function buildAskQueries($query='',$returnStr=false,$view='recent',$tagid=0,$userid=0,$keyword='',$startRow=0,$limit=7) {
		$where=array();
		if ($tagid>0) {
			if (!is_numeric($tagid)) exit ('error4');
			$where[]='tagid='.$tagid;						
		} 
		switch ($query) {
			case 'listQuestions':
				$sortStr='dt DESC';
				switch ($view) {
					default:
						// do nothing
						$where[]='AskQuestions.userid<>'.$userid;
					break;
					case 'popular':
						$where[]='dt>=DATE_SUB(CURDATE(),INTERVAL '.ASK_POPULAR_INTERVAL.' DAY)';
						// continues to greatest
					case 'popularAllTime':
						// pass thru
					case 'greatest':
						$sortStr='numLikes DESC';
						$where[]='AskQuestions.userid<>'.$userid;
					break;
					case 'noanswers':
						$where[]='numAnswers=0';
						$where[]='AskQuestions.userid<>'.$userid;
					break;
					case 'friends':  // coming thru ajax call - therefore no session loaded
						if ($userid<>0) {
							// get list of friends for this user
							$q1=$this->db->query("SELECT friends FROM UserInfo WHERE userid=$userid;");
							$d1=$this->db->readQ($q1);
							$friendList=$d1->friends; // csv list of fbids
							// include list in query
							if ($friendList<>'')
								$where[]="FIND_IN_SET(UserInfo.fbId,'$friendList')";							
							else 
								$where[]="1=2"; // find nothing
						} else
							$where[]="1=2"; // find nothing
					break;
					case 'me': // my questions
						$where[]='AskQuestions.userid='.$userid;
					break;
				}
				$q="SELECT AskQuestions.*,UserInfo.fbId,Tags.raw_tag as category FROM AskQuestions LEFT JOIN UserInfo ON AskQuestions.userid=UserInfo.userid LEFT JOIN Tags ON AskQuestions.tagid=Tags.id ".$this->db->buildWhereStr($where)." ORDER BY $sortStr LIMIT $startRow,$limit;";
				//$this->db->log($q);
			break;
			case 'relatedQuestions':
				// to do - addslashes to keyword, limit length
				$where[]='MATCH (question) AGAINST (\''.addslashes($keyword).'\')';
				$q="SELECT id,question FROM AskQuestions ".$this->db->buildWhereStr($where).' LIMIT '.$limit;
			break;
			case 'relatedNews':
				unset($where); // remove tagid
				$where=array();
				$where[]='MATCH (title) AGAINST (\''.addslashes($keyword).'\')';
				$q="SELECT siteContentId,title,url FROM Content ".$this->db->buildWhereStr($where).' LIMIT '.$limit;
			break;
		}
		if ($returnStr) 
			return $q;
		else
			return $this->db->query($q);
	}

	function fetchRelatedNews($qr,$limit=7) {
		// displays news stories possibly related to question
		$keyword=$qr->question;
		// to do - also look for stories related to the tag
		$q=$this->buildAskQueries('relatedNews',true,'',$qr->tagid,0,$keyword,0,$limit);
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['askQuestionList'],$this->templateObj->templates['askNewsItem']);			
			$temp ='<div class="panelBar clearfix"><h2>Related news stories</h2></div><br />' . $temp;
			$code = '<div class="panel_2 clearfix">'. $temp . '</div>';
		} else {
			// no related stories
			$code='';
		}
		return $code;
	}	

	function fetchBrowseQuestions($isAjax=false,$tagid=0,$userid=0,$view='recent') {
		$inside=$this->listQuestions($view,$tagid,$userid,0,99);
		if ($isAjax) {
			return $inside;
		}
		$code=$this->fetchBrowseFilter($tagid,$view);
		$code.='<div id="questionList">';
		$code.=$inside;
		$code.='<!-- end questionList --></div>';
		//$code.='<input type="hidden" id="pagingFunction" value="fetchBrowseQuestionsPage">';				
		return $code;
	}

	function fetchBrowseFilter($tagid=0,$view='recent') {
		// display the filter for browsing questions
		$code='';
		if ($tagid==0) {
			$category='All';
			$catStr.='&nbsp;&nbsp;Category: <a id="askViewCategoryAll" class="feedFilterButton selected" href="#">All</a>';
		} else {
			require_once(PATH_CORE.'/classes/tags.class.php');			
			$tagsTable = new tagsTable($this->db); 
			$tag = $tagsTable->getRowObject();		
			$tag->load($tagid);
			$category=$tag->raw_tag;
			$catStr.='&nbsp;&nbsp;Category: <a id="askViewCategoryAll" class="feedFilterButton" href="#" onclick="askResetCategory();return false;">All</a><a id="askViewCategoryTopic" class="feedFilterButton selected" href="#" onclick="askSetCategory('.$tagid.');return false;">'.$category.'</a>';
		}
		$code.='<div id="navFilter"><input type="hidden" id="filter" value="'.$view.'"><input type="hidden" id="tagid" value="'.$tagid.'"><!-- end navFilter --></div>';
		$code.='<div class="subFilter">View: ';
		$code.='<a id="askViewNoAnswers" class="feedFilterButton '.(($view=='noanswers')?'selected':'').'" href="#" onclick="askSetView(\'noanswers\');return false;">Unanswered</a>'; 
		$code.='<a id="askViewRecent" class="feedFilterButton '.(($view=='recent')?'selected':'').'" href="#" onclick="askSetView(\'recent\');return false;">Recent</a>';
		$code.='<a id="askViewPopular" class="feedFilterButton '.(($view=='popular')?'selected':'').'" href="#" onclick="askSetView(\'popular\');return false;">Popular</a>'; 
		if ($this->session->isLoaded) 
			$code.='<a id="askViewFriends" class="feedFilterButton '.(($view=='friends')?'selected':'').'" href="#" onclick="askSetView(\'friends\');return false;">Friends</a>';
		else 
			$code.='<span id="askViewFriends" class="hidden"></span>';
		$code.='<a id="askViewGreatest" class="feedFilterButton '.(($view=='greatest')?'selected':'').'" href="#" onclick="askSetView(\'greatest\');return false;">Greatest</a>'; 
		$code.=$catStr;
        $code.='</div><!--end "subfilter"-->';
		return $code;
	}
		
	function listQuestions($view='recent',$tagid=0,$userid=0,$startRow=0,$limit=5) {
		// displays a list of questions
		$q=$this->buildAskQueries('listQuestions',true,$view,$tagid,$userid,'',$startRow,$limit);
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		$cnt=$this->db->countQ($this->templateObj->db->result);
		if ($view=='popular' AND $cnt==0) {
			// for home page, try all time
			$q=$this->buildAskQueries('listQuestions',true,'popularAllTime',$tagid,$userid,'',$startRow,$limit);
			$this->templateObj->db->result=$this->templateObj->db->query($q);
			$cnt=$this->db->countQ($this->templateObj->db->result);			
		}
		if ($cnt>0) {
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'dt');
			$this->templateObj->db->setTemplateCallback('category', array($this, 'cbAskTag'), 'tagid');
			$this->templateObj->db->setTemplateCallback('cmdLike', array($this, 'cbCommandLike'), 'id');
			$this->templateObj->db->setTemplateCallback('cmdAnswer', array($this, 'cbCommandAnswer'), 'id');			
			$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'buildLinkedProfileName'), array('fbId', 'false'));
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', 'square'));			
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['askQuestionList'],$this->templateObj->templates['askQuestionItemMedium']);
		} else {
			$temp='<br /><fb:explanation message="No questions found">We found no questions matching your search criteria. Perhaps try another category or <a href="?p=ask" requirelogin="1">ask a new question?</a></fb:explanation>';					
		}
		return $temp;
	}
	
	function fetchAskTags() {
		global $crowdTags;
		$q="select id,raw_tag as tag FROM Tags WHERE FIND_IN_SET(tag,'".implode(',',$crowdTags)."') ORDER BY raw_tag ASC;";
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['askTagList'],$this->templateObj->templates['askTagItem']);
		} else {
			$temp='There are no categories';
		}					
		$code ='<div class="panelBar clearfix"><h2>Categories</h2></div><br />' . $temp;	// <div class="bar_link"><a href="?p=stories&o=raw" onclick="switchPage(\'stories\',\'raw\');return false;">See all</a></div>
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		return $code;	
	}	

	function fetchSidebarItem($item='') {
		switch ($item) {
			case 'askUnansweredQuestions':
				$q=$this->buildAskQueries('listQuestions',true,'noanswers',0,0,'',0,5);
				$title='Unanswered Questions';
			break;
			case 'askPopularQuestions':
				$q=$this->buildAskQueries('listQuestions',true,'popular',0,0,'',0,5);
				$title='This Week\'s Top Questions';
			break;
		}
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', false));			
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['askQuestionList'],$this->templateObj->templates['askQuestionItem']);
		} else {
			$temp='No recent questions have been posted';
		}					
		$code ='<div class="panelBar clearfix"><h2>'.$title.'</h2></div><br />' . $temp;	// <div class="bar_link"><a href="?p=stories&o=raw" onclick="switchPage(\'stories\',\'raw\');return false;">See all</a></div>
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		return $code;	
	}	
	
	function buildQuestionDisplay($id=0,$showShare=false) {
		$code='';
		// display the question
		$q="SELECT AskQuestions.*,UserInfo.fbId,Tags.raw_tag as category FROM AskQuestions LEFT JOIN UserInfo ON AskQuestions.userid=UserInfo.userid LEFT JOIN Tags ON AskQuestions.tagid=Tags.id WHERE AskQuestions.id=$id;";			
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'dt');
		$this->templateObj->db->setTemplateCallback('category', array($this, 'cbAskTag'), 'tagid');
		$this->templateObj->db->setTemplateCallback('cmdLike', array($this, 'cbCommandLike'), 'id');
		$this->templateObj->db->setTemplateCallback('showAnswer', array($this, 'cbShowAnswer'), 'id');			
		$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'buildLinkedProfileName'), array('fbId', 'false'));
		$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', 'normal'));			
		$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['askQuestionList'],$this->templateObj->templates['askQuestionItemDetail']);
		$code.='<br />';
		$aqTable = new askQuestionsTable($this->db); 
		$qr = $aqTable->getRowObject();		
		$qr->load($id);
		$code.='<div id="askShare" class="'.($showShare?'':'hidden').'">';
		$temp='<form requirelogin="1" id="ask_share_form" action="?p=ask&o=view&id='.$id.'" method="post"><p>To:<br /> <fb:multi-friend-input max="20" /></p><p class="bump10"><input class="btn_1" type="button" value="Send now" onclick="askShareSubmit('.$id.');return false;"></p></form>';		
		$temp ='<div class="panelBar clearfix"><h2>Share this question with your friends</h2></div><br />' . $temp;
		$temp = '<div class="panel_2 clearfix">'. $temp . '</div>';
		$code.=$temp.'</div><br />';		
		// build the answer form
		$code.='<div id="answerForm" '.(!isset($_GET['answerNow'])?'class="hidden"':'').'>';
		// to do - check that user hasn't answered question
		if ($this->hasUserAnsweredQuestion($this->session->userid,$id))  {
			$code.='<fb:error message="Sorry, you already answered this">You can only answer a question one time.</fb:error>';					
		} else if ($this->session->userid<>$qr->userid) {
			if ($this->session->isMember OR (defined('REG_SIMPLE') AND $this->session->isLoggedIn)) {
				$code.='<form requirelogin="1"><p>Please type your answer below:<br/>
					<textarea class="inputTextarea" name="details" id="answerDetails"></textarea></p>
					<input class="btn_1" type="button" value="Submit Your Answer" onclick="askPostAnswer('.$id.');"></form>';
			} else {
				$code .= '<a '.(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').' href="?p=ask&o=question&id='.$id.'" class="btn_1">Please authorize '.SITE_TEAM_TITLE.' so you can begin answering questions!</a>';
			}
		} else {
			$code.='<fb:error message="Sorry, that\'s your question">You can\'t answer your own questions. Only other community members in '.SITE_TITLE.' can post answers to your question.</fb:error>';					
			
		}				
		$code.='</div><br />';
		// display the answers to this question
		$code.=$this->buildAnswerThread($id,false,$qr->numAnswers);
		// display the link to this question box		
		$code.=$this->fetchLinkBox($qr);
		$code.=$this->fetchRelatedNews($qr);
		return $code;
	}
	
	function buildAnswerThread($id=0,$isAjax=false,$numAnswers=0) {
		$code='';
		// list all answers
		if (!is_numeric($id)) return false;
		$r=$this->templateObj->db->query("SELECT AskAnswers.*,UserInfo.fbId FROM AskAnswers LEFT JOIN UserInfo ON AskAnswers.userid=UserInfo.userid WHERE questionid=$id ORDER BY numLikes DESC;");
		if ($this->db->countQ($r)>0) {
			// Good example below of using processRow and miniMergeTemplates
			// Appropriate for when you want to iterate through a query but do other stuff in between rows (in this place, place comment threads)
			while ($lData=mysql_fetch_array($r)) {
				// anchor to answerid
				$code.='<a name="aa_'.$lData['id'].'" ></a>';
				// for each answer - build from template				
				$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'dt');
				$this->templateObj->db->setTemplateCallback('cmdLikeAnswer', array($this, 'cbCommandLikeAnswer'), array('id', 'numLikes'));
				$this->templateObj->db->setTemplateCallback('cmdCommentsAnswer', array($this, 'cbCommandCommentsAnswer'), array('id', 'numComments'));			
				$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'buildLinkedProfileName'), array('fbId', 'false'));
				$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', 'normal'));			
				$temp=$this->templateObj->processRow($lData,$this->templateObj->templates['askAnswerItem'],$this->templateObj->db->template_callbacks);
				$code.=$this->templateObj->miniMergeTemplate($this->templateObj->templates['askQuestionList'],$temp);
				// show commands incl. like, comment - toggle comment thread div
				// notification link to an answer passes in answer id - so don't hide comments for this answer when this occurs
				if (isset($_GET['answerid']) AND $_GET['answerid']==$lData['id'])
					$hideStr='';
				else
					$hideStr='hidden';
				$code.='<div id="answer_'.$lData['id'].'_comments" class="askAnswerCommentThread '.$hideStr.'">'; 
				$code.='<fb:comments xid="'.CACHE_PREFIX.'_askAnswer_'.$lData['id'].'" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=askRefreshAnswerComments&id='.$lData['id'].'" ></fb:comments>';	
				// show fb:comment in hidden div	
				$code.='<!-- end of answer_'.$lData['id'].' comments --></div>';			
			}
		}
		if ($isAjax) $numAnswers+=1; // ajax refresh answers, add one
		if ($numAnswers>0) {
			if ($numAnswers==1)
				$code='<h2>The first and only answer is shown below:</h2>'.$code;
			else
				$code='<h2>All '.$numAnswers.' answers are shown below:</h2>'.$code;
		}
		if (!$isAjax) {
 			$code='<div id="answerList">'.$code.'</div>';
		}
		return $code;
	}

	function buildQuestionForm($tag='all') {
		global $crowdTags;
		$code='<h1>Ask a Question</h1>';
		$code.='<form requirelogin="1" name="ask_question" action="?p=ask&o=askSubmit" method="post">
			<p><input autocomplete="off" type="text" class="inputText askInputQuestion" id="question" name="question" value="" onfocus="new askAhead(document.getElementById(\'question\'));"></p>
			<div id="fullQuestionForm" class="hidden">
			<div id="askRelated"></div>
	  		<p><b>Please choose a category</b> <select name="tagid">';  
		$q=$this->db->query("select id,raw_tag FROM Tags WHERE FIND_IN_SET(tag,'".implode(',',$crowdTags)."') ORDER BY raw_tag ASC;");
		while ($data=$this->db->readQ($q)) {
			$code.='<option value="'.$data->id.'" '.(($tag==$data->raw_tag)?'SELECTED':'').'>'.$data->raw_tag.'</option>';
		}
	  	$code.='</select></p>			
			<p><b>Please elaborate a bit more</b> (optional) <br /><textarea class="inputTextarea" name="details"></textarea><br /></p>
			<input class="btn_1" type="submit" value="Submit Your Question"></div>
			</form>';
		return $code;
	}
		
	function processQuestionForm($userid=0) {
		$resp=array();
		$resp['error']=false;
		$question=$_POST['question'];
		$details=$_POST['details'];
		$tagid=$_POST['tagid'];
		if ($question=='') {
			$resp['error']=true;
			$resp['msg']='Sorry, we did not get your question. Please try again.';
		}
		if ($tagid=='' OR $tagid==0) {
			$resp['error']=true;
			$resp['msg']='Please specify a category. Please try again.';
		}
		$isDup=$this->isDup($question);
		if ($isDup!==false) {
			// it is a duplicate
			$resp['error']=true;
			$resp['msg']='Sorry, <a href="?p=ask&o=question&id='.$isDup.'">that question has already been asked here</a>.';
		} else {
			$aqTable = new askQuestionsTable($this->db); 
			$qr = $aqTable->getRowObject();		
			$qr->question=$question;
			$qr->details=$details;
			$qr->tagid=$tagid;
			$qr->userid=$userid;
			$qr->dt= date('Y-m-d H:i:s', time());
			$qr->numLikes=1;
			$qr->insert();		
			require_once(PATH_CORE.'/classes/log.class.php');
			$logObj=new log($this->db);
			$logItem=$logObj->serialize(0,$userid,'askQuestion',$qr->id);
			$inLog=$logObj->update($logItem);
			// add like for this question when user posts
			$logItem=$logObj->serialize(0,$userid,'likeQuestion',$qr->id);
			$inLog=$logObj->update($logItem);
			$resp['id']=$qr->id;			
		}
		return $resp;
	}
	
	// helper functions
	function hasUserAnsweredQuestion($userid=0,$id=0) {
		$q=$this->db->query("SELECT id FROM Log WHERE userid1=$userid AND itemid=$id AND action='answerQuestion';");
		if ($this->db->countQ($q)>0)
			return true; // user has answered already
		else
			return false;
	}
	
    function isDup($question=''){
		// check to see if question exists
		$this->db->log("SELECT * FROM AskQuestions WHERE question='$question';");
    	$q=$this->db->query("SELECT * FROM AskQuestions WHERE question='$question';");
		if ($this->db->countQ($q)>0) {
			$data=$this->db->readQ($q);
			return $data->id;
		}
     	return false;
    }		

	function fetchLinkBox($qr=null) {
 		$askLink=URL_CANVAS.'?p=ask&o=question&id='.$qr->id;
		$title=htmlentities($this->templateObj->ellipsis($qr->question),ENT_QUOTES);
		$caption=htmlentities($this->templateObj->ellipsis($qr->details,350),ENT_QUOTES);
		$tweetStr=$this->templateObj->ellipsis($qr->question,80).' '.URL_HOME.'?p=ask&o=question&id='.$qr->id.' '.(defined('TWITTER_HASH')?TWITTER_HASH:'');
		$tweetThis='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode($tweetStr).'" target="_blank"><img src="'.URL_CALLBACK.'?p=cache&img=tweet_button.gif" alt="tweet this" /></a>';
		$shareButton='<div style="float:left;padding:0px 5px 0px 0px;display:inline;"><fb:share-button class="meta"><meta name="title" content="'.$title.'"/><meta name="description" content="'.$caption.'" /><link rel="target_url" href="'.$askLink.'"/></fb:share-button><!-- end share button wrap --></div>';
 		$code = '<div  id="actionLegend">'.$shareButton.'<p class="bold">'.$tweetThis.' Link to this question </p>';
          $code.= '<div class="pointsTable"><table cellspacing="0"><tbody>'.
				'<tr><td><input class="inputLinkNoBorder" type="text" value="'.$askLink.'" onfocus="this.select();" /></td></tr>'.
				'</tbody></table></div><!-- end points Table --></div><!-- end question link box -->';
 		return $code;	
 	}

	// template callback functions
	
	function cbCommandCommentsAnswer($id=0,$score=0) {
		switch ($score) {
				case 0:
					$commentStr='Comment on this answer';
				break;
				case 1:
					$commentStr='1 comment';
				break;
				default:
					$commentStr=$score.' comments';
				break;		
			}		
		 $temp='<a href="#" onclick="toggleAnswerComments('.$id.');return false;" title="comment on this answer" >'.$commentStr.'</a>';
		return $temp;
	}

	function cbCommandLikeAnswer($id=0,$score=0) {
		$temp='<span id="la_'.$id.'" class="btn_left la_'.$id.'"><a href="#" class="voteLink" onclick="return askRecordLike(\'answer\','.$id.');" title="like this answer">Like</a> '.$score.'</span>';				
		return $temp;		
	}
	
	function cbShowAnswer($id=0) {
		$temp=$this->cbCommandAnswer($id,true);
		return $temp;
	}
	
	function cbCommandAnswer($id=0,$onclick=false) {
		$score=$this->templateObj->db->row['numAnswers'];
		switch ($score) {
				case 0:
					$commentStr='Answer this question';
				break;
				case 1:
					$commentStr='1 answer';
				break;
				default:
					$commentStr=$score.' answers';
				break;		
			}
		 if ($onclick) {
			$jStr='onclick="showAnswerForm();return false;"';
			$href='href="#"';
			$commentStr='Answer this question'; // to do - just override above for now when in detail mode
		} else {
			$jStr='';
			$href='href="?p=ask&o=question&id={id}&answerNow"';
		}
		 $temp='<a '.$href.' title="answer this question" '.$jStr.'>'.$commentStr.'</a>';
		return $temp;
	}		

	function cbCommandLike($id=0) {
		$score=$this->templateObj->db->row['numLikes'];
		$temp='<span id="ll_'.$id.'" class="btn_left ll_'.$id.'"><a href="#" class="voteLink" onclick="return askRecordLike(\'question\','.$id.');" title="like this question">Like</a> '.$score.'</span>';				
		return $temp;
	}
	
	function cbAskTag($tagid=0) {
		$category = $this->templateObj->db->row['category'];
		$temp='<a href="?p=ask&o=browse&tagid='.$tagid.'">'.$category.'</a>';
		return $temp;
	}
	
	// ajax functions
	
	function ajaxShareSubmit($userid=0,$id=0,$ids='') {
		if (count($ids)>0) {
			// build csv list of fbId recipients
			$idList=implode(',',$ids);
			// load question
			$qTable = new askQuestionsTable($this->db); 
			$qr = $qTable->getRowObject();		
			$qr->load($id);
			// load facebook library
			$facebook=$this->app->loadFacebookLibrary();
		    $profileLink='<a href="'.URL_CANVAS.'?p=account&o=subscribe">Change notifications?</a>';		
		    $qLink='<a href="'.URL_CANVAS.'?p=ask&o=view&id='.$id.'">'.htmlentities($qr->question).'</a>';
			$msg=' shared this question, '.$qLink.', from '.SITE_TITLE.'. '.$profileLink;
			$apiResult=$facebook->api_client->notifications_send($idList, $msg, 'user_to_user');
			$code=$this->templateObj->buildFacebookUserList('<p>This question was sent to: </p>',$ids);	
		} else {
			$code='<p>You didn\'t select any friends</p>';			
		}
		return $code;
	}
	
	function ajaxAskCommentPosted($isSessionValid=false,$answerid=0,$change=1) {
		// load answer
		$aaTable = new askAnswersTable($this->db); 
		$ar = $aaTable->getRowObject();		
		$ar->load($answerid);
		// increment comments for this answer
		$ar->numComments+=$change;
		$ar->update();		
		if ($change>0) { // if comment added
			// load question 
			$aqTable = new askQuestionsTable($this->db); 
			$qr = $aqTable->getRowObject();		
			$qr->load($ar->questionid);
			// notify others
			if ($isSessionValid) $this->ajaxAnswerNotifyOthers('comment',$ar->userid,$ar->questionid,$qr,$answerid);	// to do - send app_to_user notification if not
		}
		$code='<fb:comments xid="'.CACHE_PREFIX.'_askAnswer_'.$answerid.'" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=askRefreshAnswerComments&id='.$answerid.'"></fb:comments>';
		return $code;
	}
	
	function ajaxAnswerNotifyOthers($mode='answer',$userid=0,$id=0,$qr=NULL,$answerid=0) {
		// set up facebook framework library
		$facebook=$this->app->loadFacebookLibrary(); // needed for api call below and requires setAppLink to be called before
		require_once(PATH_CORE .'/classes/user.class.php');		
		$userInfoTable = new UserInfoTable($this->db);
		$fbTx='';
	    $profileLink='<a href="'.URL_CANVAS.'?p=account&o=subscribe">Change notifications?</a>';
	    $qLink='<a href="'.URL_CANVAS.'?p=ask&o=question&id='.$id.'">'.htmlentities($qr->question).'</a>'; 
		switch ($mode) {
			case 'answer':
				// answer - find question poster, answer posters and all comment posters - no dups
				// id is questionid
				// userid posted the answer
				// get list of people who answered this question
				$fbTx=$this->db->buildIdList("SELECT fbId AS id FROM AskAnswers LEFT JOIN UserInfo ON AskAnswers.userid=UserInfo.userid WHERE questionid=$id ORDER BY id DESC LIMIT 25;");
				$msg=' answered '.$qLink.' at '.SITE_TITLE.'. '.$profileLink;
			break;
			case 'comment':
				// send notification to person who posted original answer
				$answerPoster = $userInfoTable->getRowObject(); // recipient info				
				$answerPoster->load($userid);
				// fb:comments also notifies recent comment posters on the thread automatically
			    $qLink='<a href="'.URL_CANVAS.'?p=ask&o=question&id='.$id.'&answerid='.$answerid.'#aa_'.$answerid.'">'.htmlentities($qr->question).'</a>'; // nc for no cache
				// send this message to poster of the answer
				$msg=' commented on your answer to '.$qLink.' at '.SITE_TITLE.'. '.$profileLink;										
		       	$apiResult=$facebook->api_client->notifications_send($answerPoster->fbId, $msg, 'user_to_user');
				// send different msg to question poster
				$msg=' commented on an answer to '.$qLink.' at '.SITE_TITLE.'. '.$profileLink;
				// pass thru - below will send notify to user who posted question
			break;
		}
		// get fbId of original question poster
		// to do - only send to question poster if qr->fbId <> fb:comment poster fbId
		$ri = $userInfoTable->getRowObject(); // recipient info				
		$ri->load($qr->userid);
		if ($fbTx<>'') $fbTx.=',';
		$fbTx.=$ri->fbId;
		// send notifications
       	$apiResult=$facebook->api_client->notifications_send($fbTx, $msg, 'user_to_user');
	}

	function ajaxAskRefreshAnswers($id=0) {
		$code=$this->buildAnswerThread($id,true);
		return $code;
	}

	function ajaxAskPostAnswer($userid=0,$id=0) {
		$error=false;
		if (isset($_POST['answerDetails']) and $_POST['answerDetails']<>'') {
			$answerDetails = strip_tags($answerDetails, '<i><b><p>');
			$answerDetails = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$_POST['answerDetails']);
			$answerDetails = nl2br($answerDetails);
		} else {
			$error=true;
			$errorMsg='You need to provide an answer!';
		}	
		if (!$error) {
			// insert answer to database
			$aaTable = new askAnswersTable($this->db); 
			$ar = $aaTable->getRowObject();		
			$ar->answer=$answerDetails;
			$ar->userid=$userid;
			$ar->questionid=$id;
			$ar->numLikes=1;
			$ar->numComments=0;
			$ar->dt= date('Y-m-d H:i:s', time());
			$ar->insert();		
			// increment question's answer count
			$aqTable = new askQuestionsTable($this->db); 
			$qr = $aqTable->getRowObject();		
			$qr->load($id);
			$qr->numAnswers+=1;
			$qr->update();			
			require_once(PATH_CORE.'/classes/log.class.php');
			$logObj=new log($db);
			// log answerQuestion
			$logItem=$logObj->serialize(0,$userid,'answerQuestion',$id,0); 															
			$inLog=$logObj->add($logItem);
			// log likeAnswer
			$logItem=$logObj->serialize(0,$userid,'likeAnswer',$ar->id,0);
			$inLog=$logObj->add($logItem);
			$code='<div id="dialogMessage"><h2>Your answer has been posted successfully.</h2><p>Thank you for participating in '.SITE_TITLE.' community</p></div>';
			// send out notifications - answer posted by userid, original questionid id
			$this->ajaxAnswerNotifyOthers('answer',$userid,$id,$qr);
		} else {
			$code='<div id="dialogMessage">Sorry, there was a problem publishing your answer. Error: '.$errorMsg.'</div>';
		}		
		return $code;
	}

	function ajaxAskRecordLike($isSessionValid=false,$mode='question',$userid=0,$id=0) {
		if ($isSessionValid) {
			require_once(PATH_CORE.'/classes/log.class.php');
			$logObj=new log($this->db);
			if ($mode=='question') {
				// record the like in the log
				$logItem=$logObj->serialize(0,$userid,'likeQuestion',$id);
				$inLog=$logObj->update($logItem);
				if ($inLog) {
					$aqTable = new askQuestionsTable($this->db); 
					$qr = $aqTable->getRowObject();		
					$qr->load($id);
					$qr->numLikes+=1;
					$qr->update();
					$code='<a href="#" class="voteLink" onclick="return askRecordLike(\''.$mode.'\','.$id.');" title="like this question">Like</a> '.$qr->numLikes;
				} else {
					$code='You already liked this!';
				}					
			} else {
				// mode : answer
				$logItem=$logObj->serialize(0,$userid,'likeAnswer',$id);
				$inLog=$logObj->update($logItem);
				if ($inLog) {
					$aaTable = new askAnswersTable($this->db); 
					$ar = $aaTable->getRowObject();		
					$ar->load($id);
					$ar->numLikes+=1;
					$ar->update();
					$code='<a href="#" class="voteLink" onclick="return askRecordLike(\''.$mode.'\','.$id.');" title="like this question">Like</a> '.$ar->numLikes;
				} else {
					$code='You already liked this!';
				}					
			}							
		} else {
			$code='<a href="'.URL_CANVAS.'?p=ask" requirelogin="1">Please authorize '.SITE_TITLE.' with Facebook before continuing.</a>';
		}
		return $code;
	}
	
	function findRelatedQuestions($str='',$limit=7) {
		// displays questions similar to the one being typed
		$q=$this->buildAskQueries('relatedQuestions',true,'',0,0,$str,0,$limit);
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['askQuestionList'],$this->templateObj->templates['askQuestionItemNoPic']);
			$temp ='<div class="panelBar clearfix"><h2>Has your question already been answered?</h2></div><br />' . $temp;
			$code = '<div class="panel_2 clearfix">'. $temp . '</div>';
		} else {
			$code='';
		}
		return $code;
	}	
}
?>