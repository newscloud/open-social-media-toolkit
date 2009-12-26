<?php
/*
 * Ideas ideas and get answers
 */

/* To do 

	- Check session for new members when posting a idea. see if fbId is available on post - or request log in first with link back to idea page

*/

require_once (PATH_CORE . '/classes/dbRowObject.class.php');
class ideaRow extends dbRowObject
{
	
}

class ideasTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Ideas";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "ideaRow";
	static $fields = array(		
		"userid" => "BIGINT(20) unsigned default 0",
		"idea" 		=> "VARCHAR(255) default ''",
		"details" 		=> "TEXT default NULL",
		"tagid" 		=> "INT(11) default 0",
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

class ideas
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
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'ideas');
		$this->initialized = true;
	}
	
	function buildIdeaQueries($query='',$returnStr=false,$view='recent',$tagid=0,$userid=0,$keyword='',$startRow=0,$limit=7) {
		$where=array();
		if ($tagid>0) 
			$where[]='tagid='.$tagid;			
		switch ($query) {
			case 'listIdeas':
				$sortStr='dt DESC';
				switch ($view) {
					default: // recent
						// do nothing
					break;
					case 'nocomment':
						$where[]='numComments=0';
					break;		
					case 'popular':
						$where[]='dt>=DATE_SUB(CURDATE(),INTERVAL '.IDEAS_POPULAR_INTERVAL.' DAY)';
						// continues to greatest
					case 'popularAllTime':
						// pass thru
					case 'greatest':
						$sortStr='numLikes DESC';
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
					case 'me': // my ideas
						$where[]='Ideas.userid='.$userid;
					break;
				}
				$q="SELECT Ideas.*,UserInfo.fbId,Tags.raw_tag as category FROM Ideas LEFT JOIN UserInfo ON Ideas.userid=UserInfo.userid LEFT JOIN Tags ON Ideas.tagid=Tags.id ".$this->db->buildWhereStr($where)." ORDER BY $sortStr LIMIT $startRow,$limit;";
			break;
			case 'relatedIdeas':
				// to do - addslashes to keyword, limit length
				$where[]='MATCH (idea) AGAINST (\''.addslashes($keyword).'\')';
				$q="SELECT id,idea FROM Ideas ".$this->db->buildWhereStr($where).' LIMIT '.$limit;
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

	function fetchRelatedNews($ir,$limit=7) {
		// displays news stories possibly related to idea
		$keyword=$ir->idea;
		// to do - also look for stories related to the tag
		$q=$this->buildIdeaQueries('relatedNews',true,'',$ir->tagid,0,$keyword,0,$limit);
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['ideaList'],$this->templateObj->templates['ideaNewsItem']);			
			$temp ='<div class="panelBar clearfix"><h2>Related news stories</h2></div><br />' . $temp;
			$code = '<div class="panel_2 clearfix">'. $temp . '</div>';
		} else {
			// no related stories
			$code='';
		}
		return $code;
	}	

	function fetchBrowseIdeas($isAjax=false,$tagid=0,$userid=0,$view='recent') {
		$inside=$this->listIdeas($view,$tagid,$userid,0,99);
		if ($isAjax) {
			return $inside;
		}
		$code=$this->fetchBrowseFilter($tagid,$view);
		$code.='<div id="ideaList">';
		$code.=$inside;
		$code.='<!-- end ideaList --></div>';
		//$code.='<input type="hidden" id="pagingFunction" value="fetchBrowseIdeasPage">';				
		return $code;
	}

	function fetchBrowseFilter($tagid=0,$view='recent') {
		// display the filter for browsing ideas
		$code='';
		if ($tagid==0) {
			$category='All';
			$catStr.='&nbsp;&nbsp;Category: <a id="ideasViewCategoryAll" class="feedFilterButton selected" href="#" onclick="return false;">All</a>';
		} else {
			require_once(PATH_CORE.'/classes/tags.class.php');			
			$tagsTable = new tagsTable($this->db); 
			$tag = $tagsTable->getRowObject();		
			$tag->load($tagid);
			$category=$tag->raw_tag;
			$catStr.='&nbsp;&nbsp;Category: <a id="ideaViewCategoryAll" class="feedFilterButton" href="#" onclick="ideaResetCategory();return false;">All</a><a id="ideaViewCategoryTopic" class="feedFilterButton selected" href="#" onclick="ideaSetCategory('.$tagid.');return false;">'.$category.'</a>';
		}
		$code.='<div id="navFilter"><input type="hidden" id="filter" value="'.$view.'"><input type="hidden" id="tagid" value="'.$tagid.'"><!-- end navFilter --></div>';
		$code.='<div class="subFilter">View: ';
		$code.='<a id="ideaViewNoComment" class="feedFilterButton '.(($view=='nocomment')?'selected':'').'" href="#" onclick="ideaSetView(\'nocomment\');return false;">No comments</a>';
		$code.='<a id="ideaViewRecent" class="feedFilterButton '.(($view=='recent')?'selected':'').'" href="#" onclick="ideaSetView(\'recent\');return false;">Recent</a>';
		$code.='<a id="ideaViewPopular" class="feedFilterButton '.(($view=='popular')?'selected':'').'" href="#" onclick="ideaSetView(\'popular\');return false;">Popular</a>'; 
		if ($this->session->isLoaded) 
			$code.='<a id="ideaViewFriends" class="feedFilterButton '.(($view=='friends')?'selected':'').'" href="#" onclick="ideaSetView(\'friends\');return false;">Friends</a>';
		else 
			$code.='<span id="ideaViewFriends" class="hidden"></span>';
		$code.='<a id="ideaViewGreatest" class="feedFilterButton '.(($view=='greatest')?'selected':'').'" href="#" onclick="ideaSetView(\'greatest\');return false;">Greatest</a>'; 
		$code.=$catStr;
        $code.='</div><!--end "subfilter"-->';
		return $code;
	}
		
	function listIdeas($view='recent',$tagid=0,$userid=0,$startRow=0,$limit=5) {
		// displays a list of ideas
		$q=$this->buildIdeaQueries('listIdeas',true,$view,$tagid,$userid,'',$startRow,$limit);
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		$cnt=$this->db->countQ($this->templateObj->db->result);
		if ($view=='popular' AND $cnt==0) {
			// for home page, try all time
			$q=$this->buildIdeaQueries('listIdeas',true,'popularAllTime',$tagid,$userid,'',$startRow,$limit);
			$this->templateObj->db->result=$this->templateObj->db->query($q);
			$cnt=$this->db->countQ($this->templateObj->db->result);			
		}
		if ($cnt>0) {
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'dt');
			$this->templateObj->db->setTemplateCallback('category', array($this, 'cbIdeasTag'), 'tagid');
			$this->templateObj->db->setTemplateCallback('cmdLike', array($this, 'cbCommandLike'), 'id');
			$this->templateObj->db->setTemplateCallback('cmdComment', array($this, 'cbCommandComment'), 'id');			
			$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'buildLinkedProfileName'), array('fbId', 'false'));
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', 'square'));			
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['ideaList'],$this->templateObj->templates['ideaItemMedium']);
		} else {
			$temp='<br /><fb:explanation message="No ideas found">We found no ideas matching your search criteria. Perhaps try choosing a category to the right or <a href="?p=ideas" requirelogin="1">suggest a new idea?</a></fb:explanation>';					
		}
		return $temp;
	}
	
	function fetchIdeasTags() {
		global $crowdTags;
		$q="select id,raw_tag as tag FROM Tags WHERE FIND_IN_SET(tag,'".implode(',',$crowdTags)."') ORDER BY raw_tag ASC;";
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['ideasTagList'],$this->templateObj->templates['ideasTagItem']);
		} else {
			$temp='There are no categories';
		}					
		$code ='<div class="panelBar clearfix"><h2>Categories</h2></div><br />' . $temp;	// <div class="bar_link"><a href="?p=stories&o=raw" onclick="switchPage(\'stories\',\'raw\');return false;">See all</a></div>
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		return $code;	
	}	

	function fetchSidebarItem($item='') {
		switch ($item) {
			case 'ideasNew':
				$q=$this->buildIdeaQueries('listIdeas',true,'recent',0,0,'',0,5);
				$title='Recent Ideas';
			break;
			case 'ideasPopular':
				$q=$this->buildIdeaQueries('listIdeas',true,'popular',0,0,'',0,5);
				$title='This Week\'s Top Ideas';
			break;
		}
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', false));			
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['ideaList'],$this->templateObj->templates['ideaItem']);
		} else {
			$temp='No recent ideas have been posted';
		}					
		$code ='<div class="panelBar clearfix"><h2>'.$title.'</h2></div><br />' . $temp;	// <div class="bar_link"><a href="?p=stories&o=raw" onclick="switchPage(\'stories\',\'raw\');return false;">See all</a></div>
		$code = '<div class="panel_2 clearfix">'. $code . '</div>';
		return $code;	
	}	
	
	function buildIdeaDisplay($id=0,$showShare=false) {
		$code='';
		// display the idea
		$q="SELECT Ideas.*,UserInfo.fbId,Tags.raw_tag as category FROM Ideas LEFT JOIN UserInfo ON Ideas.userid=UserInfo.userid LEFT JOIN Tags ON Ideas.tagid=Tags.id WHERE Ideas.id=$id;";			
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'dt');
		$this->templateObj->db->setTemplateCallback('category', array($this, 'cbIdeasTag'), 'tagid');
		$this->templateObj->db->setTemplateCallback('cmdLike', array($this, 'cbCommandLike'), 'id');
		//$this->templateObj->db->setTemplateCallback('showAnswer', array($this, 'cbShowAnswer'), 'id');			
		$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'buildLinkedProfileName'), array('fbId', 'false'));
		$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', 'normal'));			
		$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['ideaList'],$this->templateObj->templates['ideaItemDetail']);
		$code.='<br />';
		$iTable = new ideasTable($this->db); 
		$ir = $iTable->getRowObject();		
		$ir->load($id);
		if ($ir->videoid<>0) {
			// display embedded video
			require_once(PATH_CORE.'/classes/video.class.php');
			$videoTable = new VideoTable($this->db);
			$video = $videoTable->getRowObject();
			$video->load($ir->videoid);	
			$code.='<div id="readVideo">'. videos::buildPlayerFromLink($video->embedCode,320,240) .'<!-- end of readVideo --></div>';
		}		
		$code.='<div id="ideaShare" class="'.($showShare?'':'hidden').'">';
		$temp='<form requirelogin="1" id="idea_share_form" action="?p=ideas&o=view&id='.$id.'" method="post"><p>To:<br /> <fb:multi-friend-input max="20" /></p><p class="bump10"><input class="btn_1" type="button" value="Send now" onclick="ideaShareSubmit('.$id.');return false;"></p></form>';		
		$temp ='<div class="panelBar clearfix"><h2>Share this idea with your friends</h2></div><br />' . $temp;
		$temp = '<div class="panel_2 clearfix">'. $temp . '</div>';
		$code.=$temp.'</div><br />';
		// display the comments to this idea
		$comTemp='<div id="ideaComments" >';
		$comTemp.=$this->buildCommentThread($id,false,$ir->numComments);
		$comTemp.='</div><br />';
		$code.= '<div class="panel_2 clearfix"><div class="panelBar clearfix"><h2>Comments</h2><!-- end panelBar--></div><br />'.$comTemp.'<!-- end panel_2 --></div>';
		// display the link to this idea box		
		$code.=$this->fetchLinkBox($ir);
		$code.=$this->fetchRelatedNews($ir);
		return $code;
	}
	
	function buildCommentThread($id=0,$isAjax=false) {
		$code='';
		$code.='<fb:comments xid="'.CACHE_PREFIX.'_ideaComments_'.$id.'" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=ideasRefreshComments&id='.$id.'"></fb:comments>';	
		if (!$isAjax) {
 			$code='<div id="commentList">'.$code.'</div>';
		}
		return $code;
	}

	// build and process add idea form 
	function buildIdeaForm($tag='all') {
		global $crowdTags;
		$code='<h1>What\'s your idea for '.SITE_TOPIC.'?</h1>';
		$code.='<form requirelogin="1" name="ideas_add" action="?p=ideas&o=addSubmit" method="post">
			<p><input autocomplete="off" type="text" class="inputText ideasInputIdea" id="idea" name="idea" value="" onfocus="new ideaAhead(document.getElementById(\'idea\'));"></p>
			<div id="fullIdeaForm" class="hidden">
			<div id="ideaRelated"></div>
	  		<p><b>Please choose a category</b> <select name="tagid">';  
		$q=$this->db->query("select id,raw_tag FROM Tags WHERE FIND_IN_SET(tag,'".implode(',',$crowdTags)."') ORDER BY raw_tag ASC;");
		while ($data=$this->db->readQ($q)) {
			$code.='<option value="'.$data->id.'" '.(($tag==$data->raw_tag)?'SELECTED':'').'>'.$data->raw_tag.'</option>';
		}
	  	$code.='</select></p><p><b>Please elaborate a bit more on your idea</b> (optional) <br /><textarea class="inputTextareaShort" name="details"></textarea><br /></p>';
		$code.=$this->fetchVideoFormBlock();	
		$code.='<input class="btn_1" type="submit" value="Submit Your Idea"></div>';
		$code.='</form>';
		return $code;
	}
		
	function processIdeaForm($userid=0) {
		$resp=array();
		$resp['error']=false;
		$idea=$_POST['idea'];
		$details=$_POST['details'];
		$tagid=$_POST['tagid'];
		if ($idea=='') {
			$resp['error']=true;
			$resp['msg']='Sorry, we did not get your idea. Please try again.';
		}
		if ($tagid=='' OR $tagid==0) {
			$resp['error']=true;
			$resp['msg']='Please specify a category. Please try again.';
		}
		if (isset($_POST['videoURL']) and $_POST['videoURL']<>'')
		{
			require_once(PATH_CORE .'/classes/video.class.php');
			$videoURL = videos::getVideoURLFromEmbedCodeOrURL(stripslashes($_POST['videoURL']));				
			if (videos::validateVideoURL($videoURL))
			{
				$vt = new VideoTable($db);
				$videoid = $vt->createVideoForIdea($userid,$videoURL,"Idea video by $userid");					
			} else
			{
				$resp['error']=true;						
				$resp['msg']='Unsupported or invalid video URL';
			}
		} else {
			$videoid=0;
		}
		if (!$resp['error']) {
			$isDup=$this->isDup($idea);
			if ($isDup!==false) {
				// it is a duplicate
				$resp['error']=true;
				$resp['msg']='Sorry, <a href="?p=ideas&o=view&id='.$isDup.'">that idea has already been added here</a>.';
			} else {
				$iTable = new ideasTable($this->db); 
				$ir = $iTable->getRowObject();		
				$ir->idea=$idea;
				$ir->details=$details;
				$ir->tagid=$tagid;
				$ir->userid=$userid;
				$ir->dt= date('Y-m-d H:i:s', time());
				$ir->numLikes=1;
				$ir->videoid=$videoid;
				$ir->insert();		
				// add like for this idea when user posts
				require_once(PATH_CORE.'/classes/log.class.php');
				$logObj=new log($this->db);
				$logItem=$logObj->serialize(0,$userid,'likeIdea',$ir->id);
				$inLog=$logObj->update($logItem);
				$resp['id']=$ir->id;
			}			
		}
		return $resp;
	}
	
	function fetchVideoFormBlock()
	{
		$code = '<div id="videoCommentForm" >';	
		$code .='<p><b>Record a video of your idea</b> (optional)</p>';
		$code.='<p>Step 1: Record a video of your idea on <u><a href="http://www.youtube.com/my_videos_quick_capture" target="_blank">YouTube</a></u> or <u><a href="http://www.facebook.com/video/?record"
				target="_blank" >Facebook</a></u></p>';
		$code .='<h3>Please only post video of you personally sharing your idea.</h3>';	
		$code.='<p>Step 2: Enter the video URL or Embed Code</p>';	
		$code .= '<p><input class="inputText" type="text" name="videoURL" id="videoURL" onfocus="showVideoPreview();return false;" onchange="videoURLChanged();return false;"></p>';
		$code .='<div id="videoPreview" class="hidden"><div id="videoPreviewMsg">Video Preview</div></div>';
		$code .= '</div>';		
		return $code;		
	}	
	
	// helper functions
	
    function isDup($idea=''){
		// check to see if idea exists
    	$q=$this->db->query("SELECT * FROM Ideas WHERE idea='$idea';");
		if ($this->db->countQ($q)>0) {
			$data=$this->db->readQ($q);
			return $data->id;
		}
     	return false;
    }		

	function fetchLinkBox($ir=null) {
 		$ideasLink=URL_CANVAS.'?p=ideas&o=view&id='.$ir->id;
		$title=htmlentities($this->templateObj->ellipsis($ir->idea),ENT_QUOTES);
		$caption=htmlentities($this->templateObj->ellipsis($ir->details,350),ENT_QUOTES);
		$tweetStr='Cool idea: '.$this->templateObj->ellipsis($ir->idea,80).' '.URL_HOME.'?p=ideas&o=view&id='.$ir->id.' '.(defined('TWITTER_HASH')?TWITTER_HASH:'');
		$tweetThis='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode($tweetStr).'" target="_blank"><img src="'.URL_CALLBACK.'?p=cache&img=tweet_button.gif" alt="tweet this" /></a>';
		$shareButton='<div style="float:left;padding:0px 5px 0px 0px;display:inline;"><fb:share-button class="meta"><meta name="title" content="'.$title.'"/><meta name="description" content="'.$caption.'" /><link rel="target_url" href="'.$ideasLink.'"/></fb:share-button><!-- end share button wrap --></div>';
 		$code = '<div  id="actionLegend">'.$shareButton.'<p class="bold">'.$tweetThis.' Link to this idea </p>';
          $code.= '<div class="pointsTable"><table cellspacing="0"><tbody>'.
				'<tr><td><input class="inputLinkNoBorder" type="text" value="'.$ideasLink.'" onfocus="this.select();" /></td></tr>'.
				'</tbody></table></div><!-- end points Table --></div><!-- end idea link box -->';
 		return $code;	
 	}

	// template callback functions
	
	function cbCommandComment($id=0,$onclick=false) {
		$score=$this->templateObj->db->row['numComments'];
		switch ($score) {
				case 0:
					$commentStr='Comment on this';
				break;
				case 1:
					$commentStr='1 comment';
				break;
				default:
					$commentStr=$score.' comments';
				break;		
			}
			$jStr='';
			$href='href="'.URL_CANVAS.'?p=ideas&o=view&id={id}"';
		 $temp='<a '.$href.' title="comment on this idea this idea" '.$jStr.'>'.$commentStr.'</a>';
		return $temp;
	}		

	function cbCommandLike($id=0) {
		$score=$this->templateObj->db->row['numLikes'];
		$temp='<span id="li_'.$id.'" class="btn_left li_'.$id.'"><a href="#" class="voteLink" onclick="return ideaRecordLike('.$id.');" title="like this idea">Like</a> '.$score.'</span>';				
		return $temp;
	}
	
	function cbIdeasTag($tagid=0) {
		$category = $this->templateObj->db->row['category'];
		$temp='<a href="?p=ideas&o=browse&tagid='.$tagid.'">'.$category.'</a>';
		return $temp;
	}
	
	// ajax functions
	
	function ajaxShareSubmit($userid=0,$id=0,$ids='') {
		if (count($ids)>0) {
			// build csv list of fbId recipients
			$idList=implode(',',$ids);
			// load idea
			$iTable = new ideasTable($this->db); 
			$ir = $iTable->getRowObject();		
			$ir->load($id);
			// load idea title
			// load facebook library
			$facebook=$this->app->loadFacebookLibrary();
		    $profileLink='<a href="'.URL_CANVAS.'?p=account&o=subscribe">Change notifications?</a>';		
		    $qLink='<a href="'.URL_CANVAS.'?p=ideas&o=view&id='.$id.'">'.htmlentities($ir->idea).'</a>';
			$msg=' shared an idea '.$qLink.' from '.SITE_TITLE.'. '.$profileLink;
			$apiResult=$facebook->api_client->notifications_send($idList, $msg, 'user_to_user');
			$code=$this->templateObj->buildFacebookUserList('<p>This idea was sent to: </p>',$ids);	
		} else {
			$code='<p>You didn\'t select any friends</p>';			
		}
		return $code;
	}
	
	function ajaxIdeasPostComment($isSessionValid=false,$id=0,$change=1) {
		// load idea
		$iTable = new ideasTable($this->db); 
		$ir = $iTable->getRowObject();		
		$ir->load($id);
		// increment comments for this answer
		$ir->numComments+=$change;
		$ir->update();		
		if ($change>0) { // if comment added
			// notify others		
			// only notify user to user if authorized, otherwise try app to user
			if ($isSessionValid) $this->ajaxNotifyOthers($ir->userid,$id,$ir);	// to do - send app_to_user notification if not
		}
		$code=$this->buildCommentThread($id,true);
		return $code;
	}
	
	function ajaxNotifyOthers($userid=0,$id=0,$ir=NULL) {
		// set up facebook framework library
		$facebook=$this->app->loadFacebookLibrary(); // needed for api call below and requires setAppLink to be called before
	    $profileLink='<a href="'.URL_CANVAS.'?p=account&o=subscribe">Change notifications?</a>';
	    $qLink='<a href="'.URL_CANVAS.'?p=ideas&o=view&id='.$id.'">'.htmlentities($ir->idea).'</a>';
		$msg=' commented on your idea '.$qLink.' at '.SITE_TITLE.'. '.$profileLink;
		require_once(PATH_CORE .'/classes/user.class.php');		
		$userInfoTable = new UserInfoTable($this->db);
		$ideaPoster = $userInfoTable->getRowObject(); // recipient info
		// send notification to person who posted original idea
		$ideaPoster->load($userid);
		// fb:comments also notifies recent comment posters on the thread automatically
       	$apiResult=$facebook->api_client->notifications_send($ideaPoster->fbId, $msg, 'user_to_user');
	}


	function ajaxIdeaRecordLike($isSessionValid=false,$userid=0,$id=0) {
		//$this->db->log('inside ajaxidearecordlike');
		if ($isSessionValid) {
			require_once(PATH_CORE.'/classes/log.class.php');
			$logObj=new log($this->db);
			// record the like in the log
			$logItem=$logObj->serialize(0,$userid,'likeIdea',$id);
			$inLog=$logObj->update($logItem);
			if ($inLog) {
				$iTable = new ideasTable($this->db); 
				$ir = $iTable->getRowObject();		
				$ir->load($id);
				$ir->numLikes+=1;
				$ir->update();
				$code='<a href="#" class="voteLink" onclick="return ideaRecordLike('.$id.');" title="like this idea">Like</a> '.$ir->numLikes;
			} else {
				$code='You already liked this!';
			}												
		} else {
			$code='<a href="'.URL_CANVAS.'?p=ideas" requirelogin="1">Please authorize '.SITE_TITLE.' with Facebook before continuing.</a>'; 
		}
		return $code;
	}
	
	function findRelatedIdeas($str='',$limit=7) {
		// displays ideas similar to the one being typed
		$q=$this->buildIdeaQueries('relatedIdeas',true,'',0,0,$str,0,$limit);
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->templateObj->db->countQ($this->templateObj->db->result)>0) {
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['ideaList'],$this->templateObj->templates['ideaItemNoPic']);
			$temp ='<div class="panelBar clearfix"><h2>Has your idea already been posted?</h2></div><br />' . $temp;
			$code = '<div class="panel_2 clearfix">'. $temp . '</div>';
		} else {
			$code='';
		}
		return $code;
	}	
}
?>