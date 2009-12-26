<?php

class videos {
	
	var $db;
		
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
			
	//	$this->setupLibraries();
			
	}

	function fetchRecorder($service='seesmic',$videoid='') {
		switch ($service) {
			case 'seesmic':
				$code.='<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=seesmic&videoid='.$videoid.'" frameborder="0" width="400" height="400" scrolling="no" resizable="false"></fb:iframe>';		
			break;					
		}
		return $code;
	}

		
	static function validateVideoURL($url)
	{
		// do some validation
	
		preg_match('@^(?:http://)?([^/]+)@i',
	    "$url", $matches);

		$host = $matches[1];
				
		// get last two segments of host name
		preg_match('/[^.]+\.[^.]+$/', $host, $matches);		
		
		if($matches[0] == "youtube.com")
			return true;
	
		if($matches[0] == "facebook.com")
			return true;
			
		// TODO: add support for facebook videos
		

		return false;
	}
	
	// extract url from an entry field that could be a url or an embed code
	static function getVideoURLFromEmbedCodeOrURL($entry)
	{
				/* Example facebook embed code
		 * <object width="365" height="228" >
		 * 	<param name="allowfullscreen" value="true" />
		 *  <param name="allowscriptaccess" value="always" />
		 *  <param name="movie" value="http://www.facebook.com/v/85056144904" />
		 *  <embed src="http://www.facebook.com/v/85056144904" 
		 * 		type="application/x-shockwave-flash" allowscriptaccess="always" 
		 * 		allowfullscreen="true" width="365" height="228">
		 * 	</embed>
		 * </object>
		 */
		
		/* Example youtube embed code
		 * 
		 * <object width="560" height="340">
		 * 	<param name="movie" value="http://www.youtube.com/v/KIEWo_xJ5nU&hl=en&fs=1"></param>
		 * 	<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
		 * 	<embed src="http://www.youtube.com/v/KIEWo_xJ5nU&hl=en&fs=1" 
		 * 		type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" 
		 * 		width="560" height="340">
		 * 	</embed>
		 * </object>
		 * 
		 * 
		 */
		
		if (preg_match('@<embed.*src="(.*)"@i', $entry, $matches))
		{
			return $matches[1];
		} else
			return $entry;
				
	}
	
	 	
	static function buildPlayerFromLink($url, $width=160, $height=100)
	{
		$code .= '<div class="videoSmall">';
		
		if (!strlen($url))
		{ 
			//$url = "http://www.youtube.com/watch?v=kL0WFcygdWY";
			return '';
		//	$url = "http://www.facebook.com/video/video.php?v=56972139904";
		}
		 // get host name from URL
		//$url = "http://www.youtube.com/watch?v=Mue__7EgbtE";
		
		preg_match('@^(?:http://)?([^/]+)@i',
		    "$url", $matches);

		$host = $matches[1];
				
		// get last two segments of host name
		preg_match('/[^.]+\.[^.]+$/', $host, $matches);		
		
		if($matches[0] == "youtube.com")
		{
		    //check if is a video
		    $action= preg_match("/v=[a-z0-9_\\.-]+/i", "^$url", $matchs2);
		
		    foreach($matchs2 as $matchs)
		    {
		        $do = preg_match("/[^=]+[a-z0-9_\\.-]+/i", $matchs, $match);
		        foreach($match as $id)
		        {
		            $code .= "
							<fb:swf 
							    width=$width height=$height
							    swfsrc='http://www.youtube.com/v/$id' 
							     
							     
							     />
							    
							<br>";
		
		            
		        }
	    	}		
		} else if ($matches[0] == "facebook.com")
		{
   			//check if is a video
		    $action= preg_match("@v/[a-z0-9_\\.-]+@i", "^$url", $matchs2);
		
		    // i apologize for these hacks, this code adapted from another source.
		    foreach($matchs2 as $matchs)
		    {
		        $do = preg_match("@[^v/]+([a-z0-9_\\.-]+)@i", $matchs, $match);
		       // foreach($match as $id)
		       $id = $match[0];
		        {
		            $code .= "<center>
							<fb:swf 
							    width=$width height=$height
							    flashvars='movie=http://www.facebook.com/v/85056144904'
							    swfsrc='http://www.facebook.com/v/$id>' 
							     
							     
							     />
							    
							</center><br>";
		   
		        }
	    	}	
			
		} else
		{
			$code.= "<div class='hidden'> Error: could not match url (".htmlentities($url).") with a known service. </div>";
		}
		
		//echo 'PLAYER CODE:<pre>' . htmlentities($code) .'</pre>';
		$code .= '</div>';
		return $code;
	}	
}

require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class Video extends dbRowObject 
{
 
}

class VideoTable 
{
	var $db;
	
	static $tablename="Videos";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "Video";
	
	static $fields = array(		
			"service"			=>"ENUM ('youtube','seesmic','facebook') default 'youtube'",
			"title"				=>"VARCHAR(255) default ''",
			"shortName"			=>"VARCHAR(25) default ''",
			"description"		=>"TEXT default ''",
			"dateCreated"		=>"DATETIME",
			"userid"			=>"INT(11) default 0",
			"status"			=>"ENUM ('approved','pending','blocked') default 'pending'",
			"challengeCompletedId"		=>"INT(11) unsigned", // possibly null field indicating association with a particular challenge submission
			"embedCode" 		=>"TEXT default ''" // unfortunately named field, has come to contain the video URL rather than the embedCode.
				
			);
	static $keydefinitions = array(); 	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
			
	function __construct(&$db=NULL) 
	{

		if (is_null($db)) { 
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
	
	static function getSupportedVideoServices()
	{
		$matches = array();
		preg_match(self::$fields['service'], "/\(('\w+')+\)/", $matches);
		return $matches;
	}
	
	function checkVideoExistsById($id)
  	{
  		
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE id='$id'");
		return $chkDup;
  		
  	}

	function testPopulate()
	{
		
		echo '<p>VideoTable::testPopulate()</p>';
		$video = $this->getRowObject();
		

		$video->title = 'sample video response';
		$video->service = 'seesmic';
		$video->shortName = 'video1';
		$video->description = 'this is a video response';
		$video->dateCreated = date('Y-m-d H:i:s', time());
		$video->userid = 1;
		$video->status='pending';
	}
	
	function createVideoForCompletedChallenge($userid, $challengeCompletedId, $embedcode, $title)
	{
		$video = self::getRowObject();
		$video->title = $title;
		$video->userid = $userid;
		$video->challengeCompletedId = $challengeCompletedId;
		$video->embedCode = $embedcode;
		$video->dateCreated = date('Y-m-d H:i:s', time());
		$video->status = 'pending';
		
		// service is left to default since an embed code was specified, we dont really need to know...
		
		return $video->insert();
		
	}
	
	function createVideoForComment($userid, $embedcode, $title)
	{
		$video = self::getRowObject();
		$video->title = $title;
		$video->userid = $userid;
		//$video->challengeCompletedId = $challengeCompletedId;
		$video->embedCode = $embedcode;
		$video->dateCreated = date('Y-m-d H:i:s', time());
		$video->status = 'pending';
		
		// service is left to default since an embed code was specified, we dont really need to know...
		
		return $video->insert();
		
	}

	function createVideoForContent($userid,$embedcode,$title)
	{
		$video = self::getRowObject();
		$video->title = $title;
		$video->userid = $userid;
		$video->embedCode = $embedcode;
		$video->dateCreated = date('Y-m-d H:i:s', time());
		$video->status = 'pending';
		
		// service is left to default since an embed code was specified, we dont really need to know...
		
		return $video->insert();		
	}

	function createVideoForIdea($userid=0,$embedcode='',$title='Idea Video')
	{
		$video = self::getRowObject();
		$video->title = $title;
		$video->userid = $userid;
		$video->embedCode = $embedcode;
		$video->dateCreated = date('Y-m-d H:i:s', time());
		$video->status = 'pending';
		// look for duplicate
		$chkDup=$this->db->queryC("SELECT id FROM Videos WHERE embedCode='$embedcode' AND userid=$userid");
		if ($chkDup===false) {
			// service is left to default since an embed code was specified, we dont really need to know...
			return $video->insert(); // returns id of new video
		} else {
			$d=$this->db->readQ($chkDup);
			return $d->id;
		}
  			
	}
	
	function getVideosForCompletedChallenge($completedid)
	{
		
		//$db->setDebug(true);
		$q=$this->db->queryC("SELECT id FROM Videos WHERE challengeCompletedId=$completedid"); // invited within the invite interval means we want to exclude them

		$ids = array();
				
		if ($q) 
		{
			while($row = mysql_fetch_array($q)) // bit of a hack, but too lazy to look up exact behavior of the read() functions in this situation - just copied from db::processTemplates
				$ids []= $row[0];
		}
		return $ids;
		
	}
}

?>