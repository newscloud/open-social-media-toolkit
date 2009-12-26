<?php
/*
 * Micro blogging room
 */

require_once (PATH_CORE . '/classes/dbRowObject.class.php');
class microAccountRow extends dbRowObject
{
	function isDup($sid=0) {
  		$chkDup=$this->db->queryC("SELECT id FROM MicroAccounts WHERE sid=$sid;");
		return $chkDup;		
	}	
}

class microAccountsTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="MicroAccounts";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "microAccountRow";
	static $fields = array(		
		"sid" 		=> "BIGINT(20) unsigned default 0", // service id e.g. twitter id
		"shortName" 		=> "VARCHAR(150) default ''",
		"friendlyName" 		=> "VARCHAR(150) default ''",
		"tag" 		=> "VARCHAR(150) default ''",
		"profile_image_url" 		=> "VARCHAR(255) default ''",
		"service" => "ENUM ('twitter') default 'twitter'",
		"userid" => "BIGINT(20) unsigned default 0",
		"isTokenValid" => "TINYINT(1) default 0",
		"token"=>"VARCHAR(60) default ''",
		"tokenSecret" =>"VARCHAR(60) default ''",
		"lastSync"=>"TIMESTAMP DEFAULT 0"
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

class microPostRow extends dbRowObject
{
	
}

class microPostsTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="MicroPosts";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "microPostRow";
	static $fields = array(
		"statusid" => "BIGINT(20) unsigned default 0",
		"sid" => "BIGINT(20) unsigned default 0",
		"msg" 		=> "TEXT default NULL",
		"numLikes" 		=> "INT(4) default 0",
		"isFavorite" 		=> "TINYINT(1) default 0",
		"dt" 				=> "DATETIME"
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
	
	function isDup($statusid=0) {
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE statusid=$statusid;");
		return $chkDup;		
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////	
}

class microAccessRow extends dbRowObject
{
	// says which microaccts can view which microposts
	function isDup($statusid=0,$sid=0) {
  		$chkDup=$this->db->queryC("SELECT id FROM MicroAccess WHERE sid=$sid AND statusid=$statusid;");
		return $chkDup;		
	}	
}

class microAccessTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="MicroAccess";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "microAccessRow";
	static $fields = array(
		"statusid" => "BIGINT(20) unsigned default 0",
		"sid" => "BIGINT(20) unsigned default 0",
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
	
}


class microLinksRow extends dbRowObject
{
	function isDup($shortUrl='') {
  		$chkDup=$this->db->queryC("SELECT shortUrl FROM MicroLinks WHERE shortUrl='$shortUrl';");
		return $chkDup;		
	}	
}

class microLinksTable
{	
	// standard table fields
	var $db;	
	static $tablename="MicroLinks";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "microLinksRow";
	static $fields = array(		
		"shortUrl" 		=> "VARCHAR(255) default ''",
		"shortService" => "ENUM ('bit.ly','tr.im','is.gd','ow.ly','none','na') default 'na'",
		"longUrl" 		=> "VARCHAR(150) default ''",
		"title" 		=> "VARCHAR(255) default ''",
		"thumb" 		=> "VARCHAR(255) default ''",
		"caption" 		=> "TEXT default NULL",
		"media_type"	=> "ENUM ('news','video','image','na') default 'na'",
		"numTweets" => "INT(4) default 0",
		"isProcessed" => "TINYINT(1) default 0",
		"checkedTweetMeme" => "TINYINT(1) default 0",
		"checkedShortService" => "TINYINT(1) default 0",
		"checkedLongUrl" => "TINYINT(1) default 0",		
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
}

class microLinkMentionsRow extends dbRowObject
{
	function isDup($statusid=0,$linkid=0) {
  		$chkDup=$this->db->queryC("SELECT id FROM MicroLinkMentions WHERE statusid=$statusid AND linkid=$linkid;");
		return $chkDup;		
	}		
}

class microLinkMentionsTable
{	
	// standard table fields
	var $db;	
	static $tablename="MicroLinkMentions";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "microLinkMentionsRow";
	static $fields = array(		
		"t"=>"TIMESTAMP DEFAULT CURRENT_TIMESTAMP",		
		"statusid" 		=> "BIGINT(20) unsigned default 0", // service id e.g. twitter id
		"linkid" 		=> "INT(11) unsigned default 0"
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
}

class micro
{
	var $initialized;
	var $db;	
	var $session;
	var $app;
	var $utilObj;
	var $templateObj;
		
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
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'micro');
		$this->initialized = true;
	} 
	
	function resolveLinks() {
		// resolve next set of links
		// check tweet meme
		$this->db->log('check tweet meme');
		$q=$this->db->queryC("SELECT shortUrl,id FROM MicroLinks WHERE checkedTweetMeme=0 ORDER BY id DESC LIMIT 10;");
		$this->checkLinksTweetMeme($q);
		// check short services
		$this->db->log('check short services');
		$q=$this->db->queryC("SELECT shortUrl,id FROM MicroLinks WHERE checkedTweetMeme=1 AND checkedShortService=0 AND (title='' OR longUrl='') ORDER BY RAND() LIMIT 5;");
		$this->checkLinksShortService($q);
		// check full site urls 
		$q=$this->db->queryC("SELECT shortUrl,longUrl,id FROM MicroLinks WHERE checkedTweetMeme=1 AND checkedShortService=1 AND checkedLongUrl=0 AND title='' AND media_type<>'image' ORDER BY RAND() LIMIT 5;");		
		$this->checkLinksFull($q);
	}
	
	function resolveLinksByUser($userid=0) {
		if (!is_numeric($userid)) return false;
		$this->db->log('resolveLinksByUser: '.$userid);
		// count up how many items without titles
		// resolve set of links for a single user
		// look up account by userid
		$q=$this->db->queryC("SELECT sid FROM MicroAccounts WHERE isTokenValid=1 AND userid=$userid;");
		if (!$q) return false; // no valid account
		$d=$this->db->readQ($q);
		$serviceid=$d->sid;
		$q=$this->db->queryC("SELECT count(MicroLinks.id) as cnt FROM MicroLinks LEFT JOIN MicroLinkMentions ON MicroLinks.id=MicroLinkMentions.linkid WHERE title='' AND statusid IN (SELECT statusid FROM MicroAccess WHERE sid=$serviceid) AND media_type<>'image' ;");
		$d=$this->db->readQ($q);
		$this->db->log('TweetLink Status: Userid - '.$userid.' Missing titles: '.$d->cnt);
		// find all link mentions and links with that sid
		$q=$this->db->queryC("SELECT * FROM MicroLinks LEFT JOIN MicroLinkMentions ON MicroLinks.id=MicroLinkMentions.linkid WHERE checkedTweetMeme=0 AND statusid IN (SELECT statusid FROM MicroAccess WHERE sid=$serviceid) AND media_type<>'image' GROUP BY MicroLinks.id ORDER BY MicroLinks.id DESC LIMIT 25;");
		// check tweet meme
		$this->checkLinksTweetMeme($q);
		// check other services
		$q=$this->db->queryC("SELECT * FROM MicroLinks LEFT JOIN MicroLinkMentions ON MicroLinks.id=MicroLinkMentions.linkid WHERE checkedTweetMeme=1 AND checkedShortService=0 AND (title='' OR longUrl='') AND statusid IN (SELECT statusid FROM MicroAccess WHERE sid=$serviceid) AND media_type<>'image' GROUP BY MicroLinks.id ORDER BY MicroLinks.id DESC  LIMIT 25;");
		$this->checkLinksShortService($q);
		// check full site urls 
		$q=$this->db->queryC("SELECT shortUrl,longUrl,id FROM MicroLinks WHERE checkedTweetMeme=1 AND checkedShortService=1 AND checkedLongUrl=0 AND title='' AND media_type<>'image' ORDER BY MicroLinks.id DESC LIMIT 25;");
		$this->checkLinksFull($q);
	}
	
	function isExtAllowed($url='') {
		$bad=array('mp3','mp4','mov','png','gif','jpg','txt','exe','js','pdf','mpg','mpeg','ico');
		$ext = substr($url, strrpos($url, '.') + 1);
		if (in_array($ext,$bad)) 
			return false;
		else
			return true; // ok
	}
	
	function checkLinksTweetMeme($q=NULL) { 
		if (!$q) return false;
		$node_count = $this->db->cnt;
		$urlIdList = array();
		$shortUrlList = array();
		$curl_arr = array();
		$master = curl_multi_init();
		$i=0;
		while ($d=$this->db->readQ($q)) {	
			if (!$this->isExtAllowed($d->shortUrl)) {
				$this->db->update("UPDATE MicroLinks SET checkedTweetMeme=1,checkedLongUrl=1,checkedShortService=1 WHERE id=".$d->id);
				continue;			
			}
			$url ='http://api.tweetmeme.com/url_info.json?url='.$d->shortUrl;
			$curl_arr[$i] = curl_init($url);
			$urlIdList[$i]=$d->id;
			$shortUrlList[$i]=$d->shortUrl;
			curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($master, $curl_arr[$i]);			
			$i+=1;
		}
		do {
		    curl_multi_exec($master,$running);
		} while($running > 0);
		$mlTable = new microLinksTable($this->db);
		$ml = $mlTable->getRowObject();		
		$this->db->log('nodecount'.$node_count);
		$this->db->log('idlist');
		$this->db->log($urlIdList);
		
		for($i = 0; $i < $node_count; $i++)
		{
			$results = curl_multi_getcontent  ( $curl_arr[$i]  );
			// json decode each
			$l=json_decode($results);
			// print_r($l);
			// check $l->status for failure
			if (!is_numeric($urlIdList[$i])) continue;
			if ($ml->load($urlIdList[$i])!==false) {
				if (isset($l->story->media_type))
					$ml->media_type=$l->story->media_type;
				if (isset($l->story->url_count))
					$ml->numTweets=$l->story->url_count;
				if (isset($l->story->url))
					$ml->longUrl=$l->story->url;
				if (isset($l->story->title))
					$ml->title=$l->story->title;
				if (isset($l->story->excerpt))
					$ml->caption=str_ireplace ( $shortUrlList[$i] , '', $l->story->excerpt);
				if (isset($l->story->thumbnail))
					$ml->thumb=$l->story->thumbnail;
				$ml->checkedTweetMeme=1;				
				$ml->update();			
			}
		}
	}
	
	function checkLinksShortService($q=NULL) { 
		global $init;
		if (!$q) return false;
		$mlTable = new microLinksTable($this->db);
		$ml = $mlTable->getRowObject();		
		while ($d=$this->db->readQ($q)) {			
			// identify service
			$service=false;
			$parts=parse_url($d->shortUrl);
			if (stristr($parts['host'],'bit.ly')!==false) {
				$apiUrl='http://api.bit.ly/expand?version=2.0.1&shortUrl='.$d->shortUrl.'&login='.$init['bitlyuser'].'&apiKey='.$init['bitly'];				
				$resp=$this->fetchUrlByCurl($apiUrl);
				$hash=trim($parts['path'],'/');
				$l=json_decode($resp);
				if (!$l->errorCode) {
					if ($ml->load($d->id)!==false) {
						$arrL=(array)$l->results; // cast obj to array for reference by value
						$ml->longUrl=$arrL[$hash]->longUrl;
						$ml->caption=str_ireplace ( $d->shortUrl , '', $ml->caption);
						$ml->checkedShortService=1;				
						$ml->update();			
					}
				}
			} else 	if (stristr($parts['host'],'tr.im')!==false) {
					$hash=trim($parts['path'],'/');
					$apiUrl='http://http://api.tr.im/api/trim_destination.json?trimpath='.$hash;				
					$resp=$this->fetchUrlByCurl($apiUrl);
					$l=json_decode($resp);
					$this->db->log('tr.im'.$apiUrl);
					//if (!$l->errorCode) {
						
						/*
						if ($ml->load($d->id)!==false) {
							$arrL=(array)$l->results; // cast obj to array for reference by value
							$ml->longUrl=$arrL[$hash]->longUrl;
							$ml->caption=str_ireplace ( $d->shortUrl , '', $ml->caption);
							$ml->checkedShortService=1;				
							$ml->update();			
						}
						*/
					//}
				}
			 
		}
	}
	
	function checkLinksFull($q=NULL) {
		// look up longUrl for title
		if (!$q) return false;
		$this->db->log('in checkLinksFull');
		require_once(PATH_CORE.'/classes/remotefile.class.php');
		$mlTable = new microLinksTable($this->db);
		$ml = $mlTable->getRowObject();
		while ($d=$this->db->readQ($q)) {
			if ($d->longUrl=='')
				$url=$d->shortUrl;
			else
				$url=$d->longUrl;
			$this->db->log('curl '.$url);
			$resp=$this->fetchUrlByCurl($url);
			if ($resp) {
				$this->db->log('length '.strlen($resp));
				preg_match_all("/<title[^>]*>([^<]+)<\/title>/i", $resp, $matches);
				if ($matches[1][0]) {
					$title= $matches[1][0];
				} else {
				        $title = "";
				}			
				$this->db->log('title '.$title);
				if (preg_match('/<meta name="description"[^>]*content="([^"]+)"/i', $resp, $match))
					$caption= $match[1];
				else {
					$caption='';
				} 
				unset($match);
				unset($matches);				
				$this->db->log('title'.$title);
			} else {
				$this->db->log('curlfailed');
			}
			//$this->db->log('caption'.$caption);
			if ($ml->load($d->id)!==false) {
				if ($ml->longUrl=='') $ml->longUrl=$url;
				if ($ml->title=='') $ml->title=$title;
				if ($ml->caption=='') $ml->caption=$caption;
				$ml->checkedLongUrl=1;
				$ml->update();			
			} 
		}
	}

	function fetchUrlByCurl($url='') {
		if (!$this->isExtAllowed($url)) return false;
	      $useragent = 'NewsCloud (curl) ' . phpversion();
	      $ch = curl_init($url);
	      curl_setopt($ch,CURLOPT_FOLLOWLOCATION ,true );
	      curl_setopt($ch,CURLOPT_MAXREDIRS , 5);
	      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	      curl_setopt($ch, CURLOPT_USERAGENT, $useragent);	
	      curl_setopt($ch, CURLOPT_AUTOREFERER, true);	
	      $result = curl_exec($ch);		
	      curl_close($ch);
		return $result;
	}

	function syncUserAccount($userid=0) {
		if (!is_numeric($userid)) return false;
		// fetch micro account for one user
		$q=$this->db->queryC("SELECT *, (SELECT max(statusid) FROM MicroPosts WHERE MicroPosts.sid=MicroAccounts.sid) as lastStatus FROM MicroAccounts WHERE isTokenValid=1 AND userid=$userid;");
		if ($q!==false) {
			$this->db->log('syncAccountsByQuery: '.$userid);
			$q2=$this->db->query("select count(MicroAccess.sid) as cnt FROM MicroAccess,MicroAccounts WHERE MicroAccess.sid=MicroAccounts.sid AND userid=$userid;");
			$d2=$this->db->readQ($q2);	
			if ($d2->cnt==0) 
				$firstTime=true;		
			else
				$firstTime=false;
			$this->syncAccountsByQuery($q,$firstTime); 
		}
	}

	function syncAccounts() {
		// fetch all micro accounts
		$q=$this->db->query("SELECT *, (SELECT max(statusid) FROM MicroPosts WHERE MicroPosts.sid=MicroAccounts.sid) as lastStatus FROM MicroAccounts WHERE isTokenValid=1 ORDER BY lastSync ASC LIMIT 3;");
		$this->syncAccountsByQuery($q);
	}
	
	function syncAccountsByQuery($q=NULL,$firstTime=false) {		
		global $init;
		require_once(PATH_CORE.'/utilities/twitterOAuth.php');				
		require_once PATH_CORE.'/utilities/twitter.php';
		$twObj=new Twitter($init['twitterUsr'],$init['twitterPwd'],$this->db);
		$twObj->setOAuth(true);
		while ($d=$this->db->readQ($q)) {
			/*
						$resp=$tObj->getRateLimitStatus();
						if (isset($resp['error'])) {
							continue;
						}
						*/
		    $to = new TwitterOAuth($init['consumerKey'], $init['consumerSecret'],$d->token, $d->tokenSecret);
			if (!$firstTime)
				$args=$twObj->getFriendsTimeline($d->lastStatus); // to do - put back  
			else
				$args=$twObj->getFriendsTimeline(NULL,NULL,100); // first time, get larger # of updates
		    /* Run request on twitter API as user. */
		    $resp = $to->OAuthRequest('https://twitter.com/'.$args['url'], $args['params'], $args['method']);
			$xml = @simplexml_load_string($resp);			
			if($xml!== false) {
				$this->db->update("MicroAccounts","lastSync=NOW()","sid=$d->sid");				
				// add or update user tokens
				$maTable = new microAccountsTable($this->db);
				$ma = $maTable->getRowObject();
				$mpTable = new microPostsTable($this->db);
				$mp = $mpTable->getRowObject();
				$mlTable = new microLinksTable($this->db);
				$ml = $mlTable->getRowObject();
				$accessTable = new microAccessTable($this->db);
				$access = $accessTable->getRowObject();
				$mentionsTable = new microLinkMentionsTable($this->db);
				$mention = $mentionsTable->getRowObject();
				foreach ($xml as $item) {
					$s=$twObj->statusXMLToArray($item, true);
					$mp->statusid=$s['id'];
					$mp->msg=$s['text'];
					$mp->isFavorite=$s['isFavorite'];
					$mp->sid=$s['user']['id'];
					$mp->dt=date('Y-m-d H:i:s',$s['created_at']);
					if ($mpTable->isDup($mp->statusid)===false) {
						$mp->insert();	
					}
					if ($access->isDup($mp->statusid,$d->sid)===false) {
						$access->statusid=$mp->statusid;
						$access->sid=$d->sid;	// the microaccount serviceid		
						$access->insert();
					}
					// update microaccount row for user
					if (!$ma->isDup($s['user']['id'])) {
						$ma->sid=$s['user']['id'];
						$ma->shortName=$s['user']['screen_name'];
						$ma->friendlyName=$s['user']['name'];
						$ma->profile_image_url=$s['user']['profile_image_url'];
						$ma->isTokenValid=0;
						$ma->insert();
					} else {
						$ma->loadWhere("sid=".$s['user']['id']);
						$ma->profile_image_url=$s['user']['profile_image_url'];
						$ma->update();
					}
					// scan for links					
					preg_match_all("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i",$s['text'],$links);
					foreach ($links[0] as $url) {
						if (!$this->isExtAllowed($url)) continue;
						if (!$ml->isDup($url)) {
							$ml->shortUrl=$url;
							if (!stristr($url,'twitpic.com') && !stristr($url,'flickr.com') && !stristr($url,'tweetphoto.com')) {
								$ml->numTweets=1;
								$ml->insert();
								$idExist=$mention->isDup($mp->statusid,$ml->id);
								if (!$idExist) {
									$mention->linkid=$ml->id;
									$mention->statusid=$mp->statusid;
									$mention->t=date('Y-m-d H:i:s',$s['created_at']);
									$mention->insert();
								} else {
									$mention->load($idExist);
									$mention->statusid=$mp->statusid;
									$mention->update();
								}
							}
						}
					}
				}
			}
		}
	}
	
	function buildQueries($query='',$returnStr=false,$view='recent',$tagid=0,$userid=0,$keyword='',$startRow=0,$limit=25) {
		$where=array();
		if ($tagid<>'all') 
			$where[]="MicroAccounts.tag='$tagid'";			
		if (isset($_GET['all']))
			$limit=99999;
		switch ($query) {
			case 'listPosts':
				$sortStr='dt DESC';
				$q="SELECT MicroPosts.*,tag,MicroAccounts.sid as serviceid,shortName,profile_image_url FROM MicroPosts LEFT JOIN MicroAccounts ON MicroPosts.sid=MicroAccounts.sid ".$this->db->buildWhereStr($where)." ORDER BY $sortStr LIMIT $startRow,$limit;";
			break;
		}
		if ($returnStr) 
			return $q;
		else
			return $this->db->query($q);
	}

	function fetchBrowse($isAjax=false,$tagid='all',$userid=0,$view='recent') {
		$cnt=99;
		$inside=$this->listMessages($view,$tagid,$userid,0,$cnt);
		if ($isAjax) {
			return $inside;
		}
		$code=$this->fetchBrowseFilter($tagid,$view);
		$code.='<div id="postList">';
		$code.=$inside;
		$code.='<!-- end postList --></div>';
		//$code.='<input type="hidden" id="pagingFunction" value="fetchBrowseIdeasPage">';				
		return $code;
	}

	function fetchBrowseFilter($tagid='all',$view='recent') {
		// display the filter for browsing ideas
		$code='';
		$catList=explode(',',$this->db->buildIdList("SELECT DISTINCT tag as id FROM MicroAccounts ORDER BY tag ASC;"));
		$catStr='&nbsp;Filter by: ';
		$catStr.='<select id="microCat" onchange="microCatChange();return false;">';
		$catStr.='<option value="all">all</option>';			
		foreach ($catList as $item) {
			$catStr.='<option value="'.$item.'">'.$item.'</option>';			
		}
		$catStr.='</select>';
		$code.='<div id="navFilter"><!-- end navFilter --></div>';
		$code.='<div class="subFilter">';
		$code.=$catStr;
        $code.='</div><!--end "subfilter"-->';
		return $code;
	}

	function homeList($tag='all') {
		$code=$this->listMessages('recent',$tag,0,0,3,true);
		return $code;
	}
	
	function listMessages($view='recent',$tagid='all',$userid=0,$startRow=0,$limit=5,$forHome=false) {
		// displays a list of ideas
		$q=$this->buildQueries('listPosts',true,$view,$tagid,$userid,'',$startRow,$limit);
		$this->templateObj->db->result=$this->templateObj->db->query($q);
		if ($this->db->countQ($this->templateObj->db->result)>0) {
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'dt');
			if (!$forHome) {
				$this->templateObj->db->setTemplateCallback('post', array($this, 'cbPost'), 'msg');
				$this->templateObj->db->setTemplateCallback('cmdShare', array($this, 'cbCommandShare'), 'id');
				$this->templateObj->db->setTemplateCallback('cmdReply', array($this, 'cbCommandReply'), 'shortName');
				$this->templateObj->db->setTemplateCallback('cmdRetweet', array($this, 'cbCommandRetweet'), 'shortName');
				$this->templateObj->db->setTemplateCallback('cmdDM', array($this, 'cbCommandDM'), 'shortName');
				$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['microList'],$this->templateObj->templates['microItem']);				
			} else {
				$this->templateObj->db->setTemplateCallback('post', array($this, 'cbPostFilter'), 'msg');
				$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['microList'],$this->templateObj->templates['microItemHome']);								
			}
		} else {
			$temp='<br /><fb:explanation message="No tweets found">We found no tweets. Perhaps try choosing a different filter</a></fb:explanation>';					
		}
		return $temp;
	}
		
	function buildDisplay($id=0) {
		// display the one micropost
		$code='';
		$q="SELECT MicroPosts.*,tag,MicroAccounts.sid as serviceid,shortName,profile_image_url FROM MicroPosts LEFT JOIN MicroAccounts ON MicroPosts.sid=MicroAccounts.sid WHERE MicroPosts.id=$id ;";
		$this->templateObj->db->result=$this->templateObj->db->query($q);		
		if ($this->db->countQ($this->templateObj->db->result)>0) {
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'dt');
			$this->templateObj->db->setTemplateCallback('post', array($this, 'cbPost'), 'msg');
			$this->templateObj->db->setTemplateCallback('cmdShare', array($this, 'cbCommandShare'), 'id');
			$this->templateObj->db->setTemplateCallback('cmdReply', array($this, 'cbCommandReply'), 'shortName');
			$this->templateObj->db->setTemplateCallback('cmdRetweet', array($this, 'cbCommandRetweet'), 'shortName');
			$this->templateObj->db->setTemplateCallback('cmdDM', array($this, 'cbCommandDM'), 'shortName');
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['microList'],$this->templateObj->templates['microItem']);
			$code.='<br />';
		}
		return $code;
/*		// display the comments to this idea
		$comTemp='<div id="ideaComments" >';
		$comTemp.=$this->buildCommentThread($id,false,$ir->numComments);
		$comTemp.='</div><br />';
		$code.= '<div class="panel_2 clearfix"><div class="panelBar clearfix"><h2>Comments</h2><!-- end panelBar--></div><br />'.$comTemp.'<!-- end panel_2 --></div>';
		*/
	}
	
	// cron functions
	
	function resetFriends($deleteAll=false) {
		require_once PATH_CORE.'/utilities/twitter.php';
		$tObj=new Twitter(TWITTER_USER,TWITTER_PWD,$this->db);		
		if ($deleteAll) {
			$resp=$tObj->getFriendIds(TWITTER_USER);
			foreach ($resp as $id) {
				$tObj->deleteFriendship($id);
			}					
		}
		$mt = new microAccountsTable($this->db);
		$ma = $mt->getRowObject();	
		$q=$this->db->query("SELECT * FROM MicroAccounts WHERE sid=0;");
		while ($d=$this->db->readQ($q)) {
			$ma->load($d->id);
			$this->db->log('Process:'.$ma->shortName);
			if ($ma->sid==0) {
				if ($tObj->existsFriendship(TWITTER_USER,$d->shortName)) {
					$resp=$tObj->getFriendship(TWITTER_USER,$d->shortName);
					$resp=$tObj->getUser($resp['target']['id']);
				} else {
					$resp=$tObj->createFriendship($d->shortName);
				}
				if ($resp!==false) {
					$this->db->log($resp);
					$ma->sid=$resp['id'];
					$ma->friendlyName=$resp['name'];
					$ma->profile_image_url=$resp['profile_image_url'];
					$ma->update();					
				} else {
					$this->db->log('FALSE');
				}
			}				
		}
	}
	
	function cleanRoom() {
		//$this->db->delete("MicroPosts","dt<date_sub(now(),INTERVAL 7 DAY)"); // delete posts older than one week
	}
	
	function syncFriends() {
		$mt = new microAccountsTable($this->db);
		$ma = $mt->getRowObject();		
		// get list of friends
		require_once PATH_CORE.'/utilities/twitter.php';
		$tObj=new Twitter(TWITTER_USER,TWITTER_PWD,$this->db);		
		$friends=$tObj->getFriendIds(TWITTER_USER);
		foreach ($friends as $id) {
			// get info for anyone not in the table
			$resp=$tObj->getUser($id);
			if ($resp!==false) {
	//			$this->db->log($resp);
				// check if they exist
				$ma->sid=$resp['id'];
				$ma->friendlyName=$resp['name'];
				$ma->profile_image_url=$resp['profile_image_url'];
				$q=$this->db->queryC("SELECT * FROM MicroAccounts WHERE sid=".$ma->sid);
				if (!$q) {
					// does not exist, add them
					$ma->shortName=$resp['screen_name'];
					$ma->tag='default';
					$ma->service='twitter';
					$ma->insert();
				} else {
					$ma->update();									
				}
			} else {
	//			$this->db->log('FALSE');
			}
		}
		// delete anyone no longer listed - set status - friends vs. not friends
		
	}
	
	function updateRoom() {
		require_once PATH_CORE.'/utilities/twitter.php';
		$tObj=new Twitter(TWITTER_USER,TWITTER_PWD,$this->db);
		$resp=$tObj->getRateLimitStatus();
		if (isset($resp['error'])) {
			return false;
		}
		/*
		if ($this->db->queryC("SELECT * FROM MicroAccounts;")===false) {
			// no accounts yet - so remove all followers
			$this->resetFriends(true);
			return;
		}
		*/
		$resp=$tObj->getFriendsTimeline();
		$mt = new microPostsTable($this->db);
		$mp = $mt->getRowObject();
		$maTable = new microAccountsTable($this->db);
		$ma = $maTable->getRowObject();
		foreach ($resp as $item) {
			$mp->statusid=$item['id'];
			$mp->msg=$item['text'];
			$mp->sid=$item['user']['id'];
			$mp->dt=date('Y-m-d H:i:s',$item['created_at']);
			if ($mt->isDup($mp->statusid)===false) {
				$mp->insert();				
			} else {
			}
			// update microaccount row for user
			if (!$ma->isDup($item['user']['id'])) {
				// to do 
				// in facebook app, these should always exist
			} else {
				// get latest image
				$ma->loadWhere("sid=".$item['user']['id']);
				$ma->profile_image_url=$item['user']['profile_image_url'];
				$ma->update();
			}
			
		}
	}
	
	// helper functions

	function buildCommentBox($isAjax=false) {
		$code='';
		require_once(PATH_CORE.'/classes/user.class.php');
		$userTable 	= new UserTable($this->db);
		$userInfoTable 	= new UserInfoTable($this->db);
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();
		$user->loadWhere("isAdmin=1");
		$userinfo->load($user->userid);
		$code.='<fb:comments xid="'.CACHE_PREFIX.'_microComments" canpost="true" candelete="true" simple="true" numposts="3" showform="true" publish_feed="false" quiet="true" send_notification_uid="'.$userinfo->fbId.'"></fb:comments>';	// callbackurl="'.URL_CALLBACK.'?p=ajax&m=ideasRefreshComments&id='.$id.'"
		if (!$isAjax) {
 			$code='<div id="commentBox">'.$code.'</div>';
		}
		return $code;
	}
	
	function fetchLinkBox() {
 		$microLink=URL_CANVAS.'?p=tweets';
		$title=htmlentities($this->templateObj->templates['microShareTitle'],ENT_QUOTES);
		$caption=htmlentities($this->templateObj->templates['microShareCaption'],ENT_QUOTES);
		$tweetStr=strip_tags($this->templateObj->templates['microShareTitle']).' '.URL_HOME.'?p=tweets '.(defined('TWITTER_HASH')?TWITTER_HASH:'');
		$tweetThis='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode($tweetStr).'" target="twitter"><img src="'.URL_CALLBACK.'?p=cache&img=tweet_button.gif" alt="tweet this" /></a>';
		$shareButton='<div style="float:left;padding:0px 5px 0px 0px;display:inline;"><fb:share-button class="meta">';
		$shareButton.='<meta name="title" content="'.$title.'"/><meta name="description" content="'.$caption.'" /><link rel="target_url" href="'.$microLink.'"/>';
		$shareButton.='</fb:share-button><!-- end share button wrap --></div>';
 		$code = '<div  id="actionLegend"><p class="bold">Link to this page </p>'.$shareButton.' '.$tweetThis.'';
          $code.= '<div class="pointsTable"><table cellspacing="0"><tbody>'.
				'<tr><td><input class="inputLinkNoBorder" type="text" value="'.$microLink.'" onfocus="this.select();" /></td></tr>'.
				'</tbody></table></div><!-- end points Table --></div><!-- end idea link box -->';
 		return $code;	
 	}

	// template callback functions
	
	function cbCommandShare($id=0) {
		$msg=$this->templateObj->db->row['msg'];
		$shortName=$this->templateObj->db->row['shortName'];
		$imgUrl=$this->templateObj->db->row['profile_image_url'];
		$temp='<fb:share-button class="meta"><meta name="medium" content="blog" /><meta name="title" content="Tweet from '.$shortName.' via '.SITE_TITLE.'" /><meta name="description" content="'.htmlentities(strip_tags($msg),ENT_QUOTES).'" /><link rel="image_src" href="'.$imgUrl.'" /><link rel="target_url" href="'.URL_CANVAS.'?p=tweets&o=view&id='.$id.'" /></fb:share-button>';
		return $temp;
	}

	function cbCommandRetweet($shortName='') {
		$msg=$this->templateObj->db->row['msg'];
		$str='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode('RT @'.$shortName.' '.$msg).'" target="twitter">retweet</a>';		
		return $str;	
	}

	function cbCommandReply($shortName='') {
		$str='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode('@'.$shortName).'" target="twitter">reply</a>';		
		return $str;	
	}

	function cbCommandDM($shortName='') {
		$str='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode('D '.$shortName).'" target="twitter">message</a>';		
		return $str;	
	}

	function cbPost($str='') {
		$str=preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$str);
		return $str;	
	}

	function cbPostFilter($str='') {
		$id=$this->templateObj->db->row['id'];
		$str=preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"twitter\" onclick=\"switchPage('micro','view',$id); return false;\" href=\"".URL_CANVAS."?p=tweets&o=view&id=$id\">$1</a>",$str);
		return $str;	
	}
		
	function cbCommandLike($id=0) {
		$score=$this->templateObj->db->row['numLikes'];
		$temp='<span id="li_'.$id.'" class="btn_left li_'.$id.'"><a href="#" class="voteLink" onclick="return ideaRecordLike('.$id.');" title="like this idea">Like</a> '.$score.'</span>';				
		return $temp;
	}
	
	// ajax functions
	

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
}
?>