<?phpclass twitter {	var $db;	var $utilObj;	var $statusObj;	var $twitterNode='http://twitter.com/statuses/update.xml';	function twitter(&$db)	{		if (is_null($db)) { 			require_once(PATH_CORE.'/classes/db.class.php');			$this->db=new cloudDatabase();		} else			$this->db=&$db;		require_once(PATH_CORE.'/classes/utilities.class.php');		$this->utilObj=new utilities($this->db);			require_once(PATH_CORE.'/classes/systemStatus.class.php');		$this->statusObj=new systemStatus($this->db); 	}		function postFeaturedStories() {		// post all featured stories		require_once(PATH_CORE.'/classes/log.class.php');		$logObj=new log($this->db);		$uid=1;		$this->db->result = $this->db->query("SELECT * FROM FeaturedTemplate WHERE id = 1");		$featured = mysql_fetch_assoc($this->db->result);		$story_ids = array();		foreach ($featured as $field => $value) {			if (preg_match('/^story_([0-9]+)_id$/', $field))				$story_ids[] = $value;						}		$stories = array();		$story_results = $this->db->query("SELECT * FROM Content WHERE siteContentId IN (".join(',', $story_ids).");");		//$this->db->log("SELECT * FROM Content WHERE siteContentId IN (".join(',', $story_ids).");");		while ($data = $this->db->readQ($story_results)) {			if (!$this->checkLog($uid,$data->siteContentId)) {					// post to twitter					$result=$this->update($data->siteContentId,$data->title);					if ($result) {						$logItem=$logObj->serialize(0,$uid,'postTwitter',$data->siteContentId); 						$logObj->add($logItem);						$this->statusObj->setState('lastTwitterPost',time());					}				}									}	} 		function postNextTopStory() {		// only post one story every three hours		$tstamp=$this->statusObj->getState('lastTwitterPost');		if (!isset($_GET['test']) AND time()-$tstamp<(60*TWITTER_INTERVAL_MINUTES)) return; 		//echo 'continuing';			require_once(PATH_CORE.'/classes/content.class.php');		$cObj=new content($this->db);		require_once(PATH_CORE.'/classes/log.class.php');		$logObj=new log($this->db);		$topStories=$cObj->fetchUpcomingStories();				$uid=1;		while ($data=$this->db->readQ($topStories)) {			if (!$this->checkLog($uid,$data->siteContentId) AND $data->score>=TWITTER_SCORE_THRESHOLD) {				// post to twitter				$result=$this->update($data->siteContentId,$data->title);				if ($result) {					$logItem=$logObj->serialize(0,$uid,'postTwitter',$data->siteContentId); 					$logObj->add($logItem);					$this->statusObj->setState('lastTwitterPost',time());				}				// only do one at a time				return;			}		}	}	function update($siteContentId=0,$title='',$useCurl=false) {		// twitter code adopted from http://paulstamatiou.com/2007/01/26/stammy-script-rss-to-twitter-using-php		$uname = TWITTER_USER;				$pwd = TWITTER_PWD;				$twitter_url = $this->twitterNode;		$tiny_url=URL_HOME.'?p=x&x='.base_convert($siteContentId,10,36);		$titleLength=140-strlen($tiny_url)-3;				// trim title to lenth with ellipsis		$title=$this->utilObj->shorten($title, $titleLength);				$status = $title . " " . $tiny_url;		echo $status.'<br />';		if ($useCurl) {			$curl_handle = curl_init();			curl_setopt($curl_handle,CURLOPT_URL,"$twitter_url");			curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);			curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);			curl_setopt($curl_handle,CURLOPT_POST,1);			curl_setopt($curl_handle,CURLOPT_POSTFIELDS,"status=$status");			curl_setopt($curl_handle,CURLOPT_USERPWD,"$uname:$pwd");			$buffer = curl_exec($curl_handle);			curl_close($curl_handle);			if (empty($buffer))				return false;			else				return true;		} else {			$status=urlencode($status);			 // adapted from http://pratham.name/post/39551949/twitter-php-script-without-curl			$out="POST http://twitter.com/statuses/update.json HTTP/1.1\r\n"			  ."Host: twitter.com\r\n"			  ."Authorization: Basic ".base64_encode ($uname.':'.$pwd)."\r\n"			  ."Content-type: application/x-www-form-urlencoded\r\n"			  ."Content-length: ".strlen ("status=$status")."\r\n"			  ."Connection: Close\r\n\r\n"			  ."status=$status";			$fp = fsockopen ('twitter.com', 80);			// this script doesn't check result			fwrite ($fp, $out);			fclose ($fp);			return true;				}	}		function checkLog($uid=0,$itemid=0) {			$result=$this->db->queryC("SELECT id FROM Log WHERE userid1=$uid AND action='postTwitter' AND itemid=$itemid;");		if ($result===false)			return false; // not in log		else			return true; // duplicate, already in log	}	}	// end class?>