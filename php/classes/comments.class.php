<?php

class comments {

	var $db;
	var $story;
	var $templateObj;
	var $utilObj;
	var $contentObj;
	
	function comments(&$db=NULL)
	{
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}
	
	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates('PHP');
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		require_once(PATH_CORE.'/classes/content.class.php');
		$this->contentObj=new content($this->db);
	}

	function buildComments($includeWrap=false,&$story=NULL) {
		$this->story=$story;
		$code='<!-- start comments --><a name="comments"></a>';
		$q=$this->templateObj->db->query("SELECT Comments.*,User.ncUid FROM Comments,User WHERE User.userid=Comments.postedById AND (siteContentId=".$this->story->siteContentId." OR contentid=".$this->story->contentid.");");
		if ($this->templateObj->db->countQ($q)>0) {
			$this->templateObj->db->setTemplateCallback('comments', array($this, 'decodeComment'), 'comments');
			$this->templateObj->db->setTemplateCallback('time_since', array($this->utilObj, 'time_since'), 'date');
			$this->templateObj->db->setTemplateCallback('userImage', array($this->templateObj, 'getUserImage'), 'ncUid');
			$this->templateObj->db->setTemplateCallback('memberName', array($this->templateObj, 'getMemberName'), 'postedByName');
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['commentList'],$this->templateObj->templates['commentItem']);
		} else {
			$code.='';
		}			
		$code.='<!-- end comments -->';
		$code.=$this->buildCommentForm();
		// include wrapper when not ajax
		if ($includeWrap) $code='<div id="commentThread">'.$code.'</div>';
		return $code;
	}
	
	function buildCommentForm() {
		$code='<!-- post comment form --><div id="postCommentForm"><a name="postComment">';
		if ($this->db->ui->isLoggedIn) {
			$code.='<h2>Post a comment</h2><p>Add your comments to the discussion here.</p><form name="commentForm" id="commentForm" action="javascript:return false();" method="post">';
			$code.='<input name="siteContentId" type="hidden" value="'.$siteContentId.'"><textarea name="comments" id="comments" cols="60" rows="12"></textarea><br /><br />';
			$code.='<input name="postComment" type="button" onclick="submitComment('.$this->story->siteContentId.');" value="Post Comment">';
			$code.='</form>';
		} else {
			$code.='<p>Please <a href="?p=signin&referPage='.urlencode('?p=readStory&permalink='.$this->story->permalink).'">sign in or register</a> in order to post comments.</p>';			
		}
		$code.='<br clear="all" /></div><!-- end comment form -->';
		return $code;
	}
		
	function postComment($contentid=0) {
	}

	/* template functions */
	function decodeComment($str='') {
		return html_entity_decode($str);
	}

}
/*
		$thread=$this->db->query("SELECT * FROM Comments WHERE contentid=$story->contentid ORDER BY commentid DESC LIMIT 99;");
		if ($this->db->countQ($thread)>0) {
			$code='<br clear="all" />';
			//$code.='<fb:wall>';
			while ($item=$this->db->readQ($thread)) {
				sscanf($item->date,"%4u-%2u-%2u %2u:%2u:%2u",$year,$month,$day,$hour,$min,$sec);
		        $tstamp=mktime($hour,$min,$sec,$month,$day,$year);	
				//$caption=stripslashes($this->stripAbstract($item->comments));
				$caption=$item->comments;
				//if (is_null($item->fbUid)) {
					// not posted by a facebook user
					// $photo -					
					$code.='<div style="margin:10px 0px 10px 0px;padding:0px;border-top:1px solid gray;"><a href="http://'.htmlentities($item->member_name).'.newscloud.com/"><img style="float:left;margin:0px 10px 10px 0px;padding:0px;border:0px;" src="http://www.newscloud.com/images/usericon.php?uid='.$item->uid.'" alt="'.$item->member_name.'"></a>On '.date('D M j, Y \\a\t g:i:s a',strtotime($item->date)).', <a href="http://'.htmlentities($item->member_name).'.newscloud.com/">'.$item->member_name.'</a> said:<br />'.$caption.'</div>';
					//$code.='<fb:wallpost uid="0" t="'.$tstamp.'">';
				} else {
					// posted by a facebook user
					$code.='<fb:wallpost uid="'.$item->uid.'" t="'.$tstamp.'">';
				}
				$code.=$caption.'</fb:wallpost>';
			}
			//$code.='</fb:wall>';
		}
		 else {
			$code='<br /><h3>Start the Discussion</h3><p>Be the first to comment on this story...</p>';
		}
*/
?>