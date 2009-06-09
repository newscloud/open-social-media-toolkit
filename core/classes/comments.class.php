<?php
/*
	// set up the Comments table
		$manageObj->addTable("Comments","siteCommentId","INT(11) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("Comments","commentid","INT(11) default 0");
		$manageObj->addColumn("Comments","siteContentId","INT(11) default 0");
		$manageObj->addColumn("Comments","contentid","INT(11) default 0");
		$manageObj->addColumn("Comments","comments","TEXT default ''");
		$manageObj->addColumn("Comments","postedByName","VARCHAR(255) default ''");
		$manageObj->addColumn("Comments","postedById","INT(11) default 0");
		$manageObj->addColumn("Comments","userid","INT(11) default 0");
		$manageObj->addColumn("Comments","date","DATETIME");

*/
require_once (PATH_CORE . '/classes/dbRowObject.class.php');
class CommentRow extends dbRowObject
{
	
}

class CommentTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Comments";
	static $idname = "siteCommentId";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "CommentRow";
		
	static $fields = array(		
		"commentid" 		=> "INT(11) default 0", // the newscloud id perhaps?
		"siteContentId" 	=> "INT(11) default 0",
		"contentid" 		=> "INT(11) default 0",
		"comments" 			=> "TEXT default ''",
		"postedByName" 		=> "VARCHAR(255) default ''",
		"postedById"		=> "INT(11) default 0",
		"userid" 			=> "INT(11) default 0",
		"date" 				=> "DATETIME",
		"isBlocked" 		=> "TINYINT(1) default 0",
		"videoid" 			=> "INT(11) default 0",
	
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
	
	// generic table creation routine, same for all *Table classes 		
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

		


class comments {
	
	var $db;
		
	function comments(&$db) {
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;
	}
	
	function updateCommentCount($siteContentId=0) {
		$q=$this->db->query("SELECT id FROM Comments WHERE siteContentId=$siteContentId;");
		$cnt=$this->db->countQ($q);
		$this->db->update("Content","numComments=$cnt","siteContentId=$siteContentId");
	}

	function add($comment) {
		// check for duplicate
		if ($comment->commentid<>0)
			$chkDup=$this->db->queryC("SELECT commentid FROM Comments WHERE commentid=$comment->commentid;");
		else
			$chkDup=$this->db->queryC("SELECT siteCommentId FROM Comments WHERE siteCommentId=$comment->siteCommentId;");
		if ($chkDup===false) {
			// insert the story into the table
			$comment_string = mysql_real_escape_string(preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$comment->comments));
			$this_query=$this->db->insert("Comments","siteContentId,commentid,contentid,comments,userid,postedById,postedByName,date",
				"$comment->siteContentId,$comment->commentid,$comment->contentid,'$comment_string',$comment->postedById,$comment->userid,'$comment->postedByName','$comment->date'");
				//"$comment->siteContentId,$comment->commentid,$comment->contentid,'$comment->comments',$comment->postedById,$comment->userid,'$comment->postedByName','$comment->date'");
			$newId=$this->db->getId();
			if ($comment->siteContentId>0) {
				// increment comment count	
				$this->db->update("Content","numComments=numComments+1","siteContentId=$comment->siteContentId");
			}
			return $newId;	
		} else 
			return false;
	}

	function remoteSerialize($data) {
		// take comment from NewsCloud
		// look up postedById and get local userid
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); 
		$user = $userTable->getRowObject();
		$user->load($data[uid]);
		if (!$user) {
			$userid=$user->userid;
		} else {
			$userid=0;
		}
		require_once(PATH_CORE.'/classes/content.class.php'); 
		$contentTable = new ContentTable($this->db); 
		$content=$contentTable->getRowObject();
		$content->load($data[contentid],'contentid');
		if (!$content) {
			$siteContentId=$content->siteContentId;
		} else {
			$siteContentId=0;
		}
	// look up contentid and get local siteContentId	
		$data=$this->serialize(0,$data[commentid],$siteContentId,$data[contentid],$data[comments],$data[uid],$userid,$data[member_name],$data[date]);
		return $data;
	}
	
	function serialize($siteCommentId=0,$commentid=0,$siteContentId=0,$contentid=0,$comments='',$postedById=0,$userid=0,$postedByName='',$date='',$isFeatured=0) {
		// creates an object for an action
		$data= new stdClass;
		$data->commentid = $commentid;
		$data->contentid = $contentid;
		$data->siteContentId = $siteContentId;
		$data->siteCommentId=$siteCommentId;
		$data->comments=$this->db->safe($comments);
		$data->postedById = $postedById;
		$data->userid = $userid;
		$data->postedByName = $postedByName;
		if ($date=='')
			$date=date("Y-m-d H:i:s");
		$data->date=$date;
		$data->isFeatured=$isFeatured;
		return $data;
	}	
	
	function setCommentNotification($userid=0) {
		
	}
	
	function onIgnoreList($uid=0,$commentPoster=0) {
	    // check if commenter is on the user's ignore list
	    $result=$this->db->queryC("SELECT id FROM UserIgnore WHERE uid=$uid AND ignoreUid=$commentPoster LIMIT 1;");
	    if ($result===false)
	        return false;
	    else
	        return true;
	}
	
	function ignoreCommenter($uid=0,$commentid=0) {
	    // look up comment
	    $getComment=$this->db->query("SELECT * FROM Comments WHERE commentid=$commentid;");
	    $data=$this->db->readQ($getComment);
	    $commentPoster=$data->postedbyid;
	    // check if they are already on the ignore list for this user
	    $result=$this->db->queryC("SELECT id FROM UserIgnore WHERE uid=$uid AND ignoreUid=$commentPoster LIMIT 1;");
	    if ($result===false) {
	        // if not, add them
	        $this->db->insert("UserIgnore","uid,ignoreUid","$uid,$commentPoster");
	    }       
	}

	function notifyOthers($comment,$story,&$app=NULL) {		
	    // sends notifications to users when people comment on a story they have posted or commented on
		if (MODULE_ACTIVE=='FACEBOOK') {
			// comment is an array of the posted comment
			// story is an object of the story the comment is posted on
			require_once(PATH_CORE .'/classes/user.class.php');		
			$userInfoTable = new UserInfoTable($this->db);			
			$ri = $userInfoTable->getRowObject(); // recipient info				
		    $commentPoster=$comment['userid'];
		    $commentPostedfbId=$comment['fbId'];
		    $siteContentId=$comment['siteContentId'];
		    $storyPoster=$story->userid;
		    //$ignoreLink="http://www.newscloud.com/ver/igCom/{safeEmail}/{actCode}/".$commentid;
		    $profileLink='<a href="'.URL_CANVAS.'?p=account&o=subscribe">Change notifications?</a>';
		    $storyLink='<a href="'.URL_CANVAS.'?p=read&cid='.$siteContentId.'&nc">'.$story->title.'</a>'; // nc for no cache
		    $app->loadFacebookLibrary();
	        // check that commenter is not the poster
		    if ($storyPoster<>$commentPoster) {
			    // notify poster of story
				// load the ui record for the fbId and if it succeeds, the corresponding user record
				if ($ri->load($storyPoster) AND $ri->noCommentNotify==0) { // AND !$this->onIgnoreList($storyPoster,$commentPoster)
					$msg=' commented on your story, '.$storyLink.' at '.SITE_TITLE.'. '.$profileLink;
					// $this->db->log($ri->fbId.' '.$msg);
					// To ignore future comments by this reader, click the link below: '.$ignoreLink.'										
		            // 	send notification		            
		            $apiResult=$app->facebook->api_client->notifications_send($ri->fbId, $msg, 'user_to_user');		            
				}
	        }
		    // notify other commenters
		    // member name just added a comment, click here to view it
			$msg=' replied to your comment from '.$storyLink.' at '.SITE_TITLE.' '.$profileLink;
			$rxList='';			
		    $listComments=$this->db->query("SELECT DISTINCT(userid) FROM Comments WHERE siteContentId=$siteContentId AND userid<>$storyPoster AND userid<>$commentPoster LIMIT 99;");
		    while ($data=$this->db->readQ($listComments)) {
		         if ($ri->load($data->userid) AND $ri->noCommentNotify==0) { // AND !$this->onIgnoreList($storyPoster,$commentPoster) {
		         	$rxList.=$ri->fbId.',';
		         }      
		    }   
		    $rxList=trim($rxList,',');    
		    if ($rxList<>'') {
		    	// $this->db->log($rxList.' '.$msg);
			    $apiResult=$app->facebook->api_client->notifications_send($rxList, $msg, 'user_to_user');			    			    	
		    }		    
	    }
	}
}	
?>