<?php

class template {
			
	var $db;
	var $templates;
	
	function template(&$db=NULL)
	{
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
	}

	function populateTemplates() // simulate invocation of of all templates to help populate Templates table 
	{
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
	
		$dir    = PATH_TEMPLATES;
		$templateFiles = scandir($dir);
		foreach ($templateFiles as $fname)
		{
			if(preg_match("/\\.(php?)$/", $fname))
			{
				
				$inside .= "Including ".PATH_TEMPLATES ."/$fname...<br/>";
			
				include (PATH_TEMPLATES .'/'.$fname);
			}
		
		}
		$inside .= "Done.";
		return $inside;
	}
	
	function registerTemplates($module='',$group='') {
		
		// new: (small hack may go better elsewhere) 
		// make sure dynamic template engine is available
		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		$dynTemp = dynamicTemplate::getInstance($this->db);
		
		switch ($module) {
			case 'PHP':
				switch ($group) {
					case 'profile':
						include PATH_TEMPLATES.'/profileBox.php';
					break;
					default:
						include PATH_TEMPLATES.'/templates.php';
					break;
				}
			break;
			case 'FACEBOOK':
				switch ($group) {
					case 'newswire':
						include PATH_TEMPLATES.'/home.php';
						include PATH_TEMPLATES.'/newswire.php';
					break;
					case 'home':
					case 'appTab':
						include_once PATH_TEMPLATES.'/home.php';
					break;
					case 'featuredStories':
						include PATH_TEMPLATES.'/featuredStories.php';
					break;
					case 'common':
						include PATH_TEMPLATES.'/common.php';
					break;
					case 'read':
						include PATH_TEMPLATES.'/read.php';
						include PATH_TEMPLATES.'/comments.php';
					break;
					case 'rewards':
						include PATH_TEMPLATES.'/rewards.php';
					break;
					case 'challenges':
						include PATH_TEMPLATES.'/challenges.php';
					break;
					case 'resources':
						include PATH_TEMPLATES.'/resources.php';
					break;
					case 'teamactivity':
						include PATH_TEMPLATES.'/teamactivity.php';
					break;
					case 'forum':
						include PATH_TEMPLATES.'/forum.php';
					break;
					case 'signup':
						include PATH_TEMPLATES.'/signup.php';
					break;
					case 'promos':
						include PATH_TEMPLATES.'/promos.php';
					break;
					case 'rules':
						include PATH_TEMPLATES.'/rules.php';
					break;
					case 'postStory':
						include PATH_TEMPLATES.'/postStory.php';
					break;
					case 'publisher':
						include PATH_TEMPLATES.'/publisher.php';
					break;
					case 'invite':
						include PATH_TEMPLATES.'/invite.php';
					break;
					case 'research':
						include PATH_TEMPLATES.'/research.php';
					break;
					case 'winEmail':
						include PATH_TEMPLATES.'/winEmail.php';
					break;
					default:
						// TODO: Add default template for facebook
						// do nothing for now
					break;	
				}
				
			break;
		}
	}
	
	function mergeTemplate($listTemplate,$itemTemplate,$listTitle='', $rowlimit=99) {
		$items = $this->db->processTemplate($itemTemplate,$rowlimit);		 // djm: perpetuating a terrible hack
		$list = str_replace('{items}', $items, $listTemplate);
		if ($listTitle<>'') 
			$list = str_replace('{listTitle}', $listTitle, $list);
		return $list;
	}

	/* miniMerge and processRow replace template tokens based on a single row of data */
	function miniMergeTemplate($listTemplate,$items,$listTitle='') {
		// merge template after tokens in items were replaced by processrow
		$list = str_replace('{items}', $items, $listTemplate);
		if ($listTitle<>'') 
			$list = str_replace('{listTitle}', $listTitle, $list);
		return $list;
	}

	function processRow($row,$html_template,&$callbacks)
	{
		/**
		 * Takes a string (HTML template) 
		 * and replaces any merge fields in form of {field}
		 * if a match is found in a query result set
		 * <div id='{idrow}'>{firstname}</div> becomes:
		 * <div id='1234'>Chadwick</div>
		 * custom callback functions can be defined with $this->setTemplateCallback
		 */
		$ret = '';
		$new_html = $html_template;
		// replace merge-fields with function callbacks
		foreach ($callbacks as $merge => $cb) {
			// Process column data
			$params = array();
			if (is_array($cb['col'])) {
				foreach ($cb['col'] as $param) {
					if (array_key_exists($param, $row))
						$params[] = $row[$param];
					else
						$params[] = $param;
				}
			} else {
				$params = $row[$cb['col']];
			}
			$new_html = str_replace("{" . $merge . "}", call_user_func_array($cb['func'], $params), $new_html);
		} 
		foreach ($row as $key => $val) {
			$new_html = str_replace("{" . $key . "}", $val, $new_html);
		}				
		$ret .= $new_html;
		return $ret;
	}
		
	/* Callback functions */
	
	// to do these functions should be in the template class for the module

	function getUserImage($ncUid=0) {
		// return '<a href="/journal/{memberName}/"><img src="/images/usericon.php?uid='.$ncUid" alt="user photo" /></a>';
		return '<img src="'.URL_SMT_SERVER.'/images/usericon.php?uid='.$ncUid.'" alt="user photo" />';		
	}

	function getMemberName($str='') {
		return $str;
	}
	
	function getLargeStoryImage($imageid=0) {
		if ($imageid>0) {
			$temp='<a href="{url}" onclick="quickLog(\'extLink\',\'read\',{siteContentId},\'{url}\');" target="_cts"><img src="http://www.newscloud.com/images/scaleImage.php?id='.$imageid.'&x=640&y=480&fixed=x&crop" alt="story image" /></a>';
		} else {
			$temp='';
		}
		return $temp;
	}

	function getStoryImage($imageid=0) {
		if ($imageid>0) {
			$temp='<a href="'.URL_PREFIX.'?p=read&{siteContentId}" onclick="readStory({siteContentId});return false;"><img src="http://www.newscloud.com/images/scaleImage.php?id='.$imageid.'&x=120&y=120&fixed=x&crop" alt="story image" /></a>';
		} else {
			$temp='<img src="'.URL_CALLBACK.'?p=cache&simg=watermark.jpg" alt="spacer" />';
		}
		return $temp;
	}
		
	function commandVote($siteContentId=0) {		
		$score=$this->db->row['score'];
		/*
		 * switch ($score) {
			case 0:
				$voteStr='vote';
			break;
			case 1:
				$voteStr='vote';
			break;
			default:
				$voteStr='votes';
			break;
		}
		*/
		$temp='<span id="vl_'.$siteContentId.'" class="btn_left vl_'.$siteContentId.'"><a href="#" class="voteLink" onclick="return recordVote('.$siteContentId.');" title="vote for this story">Vote it up</a> '.$score.'</span>';		
		return $temp;	
	}
	
	function commandComment($siteContentId=0) {
		// to do: how many comments
		// either read comments or post a comment
		$numComments=$this->db->row['numComments'];
	/*
	 * 	switch ($numComments) {
			case 0:
				$commentStr='Post a comment';
			break;
			case 1:
				$commentStr='1 comment';
			break;
			default:
				$commentStr=$numComments.' comments';
			break;
		}
		
	 */
	 $temp='<a href="'.URL_CANVAS.'?p=read&cid={siteContentId}&o=comments" onclick="readStory({siteContentId});return false;" title="comment on this story">Post a comment</a> '.$numComments;
		return $temp;
	}

	function commandAdd($siteContentId=0) {
		$temp='<span class="aj_'.$siteContentId.'"><a href="#" onclick="addToJournal(\'story\',0,{siteContentId});"">Add to journal</a></span>';
		return $temp;
	}

	function commandRead($permalink='') {
		$temp='<a href="'.URL_PREFIX.'?p=process&action=jumpToStory&itemid={siteContentId}" target="story">Jump to story</a>';
		return $temp;
	}

	function memberImage($userid = 0, $size = false) {
		$fbId = $this->db->row['fbId'];
		$userid = $this->db->row['userid'];
		$postedById = $this->db->row['postedById'];
		$postedByName = $this->db->row['postedByName'];
		if ($userid == 0 && $fbId == 0 && $postedById == 0)
			return false;

		if ($size)
			$sizeStr = " size=\"$size\"";
		else
			$sizeStr = '';

		if ($fbId > 0) {
			$temp = $this->buildLinkedProfilePic($fbId, $sizeStr);
		} else {
			$temp = '<a href="http://www.newscloud.com/journal/{postedByName}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {postedByName}" /></a>';
		}

		return $temp;
	}
	
	
	// TODO: SWITCH TO USING FBID 
	function memberLink($userid = 0) {
		$fbId = $this->db->row['fbId'];
		$userid = $this->db->row['userid'];
		$postedById = $this->db->row['postedById'];
		$postedByName = $this->db->row['postedByName'];
		if ($userid == 0 && $fbId == 0 && $postedById == 0)
			return false;

		if ($fbId > 0) {
			$temp = $this->buildLinkedProfileName($fbId,false);
		} else {
			$temp = '<a href="http://www.newscloud.com/journal/'.$postedByName.'">'.$postedByName.'</a>';
		}

		return $temp;
	}
	
	//
	function getAbsoluteStoryImage($imageid=0) {
		if ($imageid>0) {
			$theLink=$this->getAbsoluteStoryLink();
			$temp='<a href="'.$theLink.'"><img src="http://www.newscloud.com/images/scaleImage.php?id='.$imageid.'&x=75&y=75&fixed=x&crop" alt="story image" /></a>';
		} else {
			$temp='';
		}
		return $temp;
	}
	function getAbsoluteStoryLink() {
		$link=URL_CANVAS.'/?p=read&o=comments&cid={siteContentId}&record';
		return $link;
	}

	// TODO: move to core/template
	static function buildLinkedProfileName($fbId=0,$capitalize=true)
	{
		return self::buildLocalProfileLink(
						'<fb:name ifcantsee="Anonymous" uid="'.$fbId.'" capitalize="'.($capitalize?'true':'false').'" linked="false" />',
						$fbId);
		
	}
	
	static function buildLinkedProfilePic($fbId, $size = '')
	{
		return self::buildLocalProfileLink(
						'<fb:profile-pic uid="'.$fbId.'" linked="false" '.$size.' />',
						$fbId);
	}
	
	static function buildLocalProfileLink($content, $fbId)
	{
		return '<a href="?p=profile&memberid='.$fbId.
					'" onclick="switchPage(\'profile\',\'\','.$fbId.'); return false;">' . $content.'</a>';
	}
	
	
	static function buildLinkedChallengePic($challengeid, $thumbnail, $width=150)
	{
		$thumbnail = $thumbnail=='' ? URL_CALLBACK.'?p=cache&img=watermark.jpg' : $thumbnail;
		return self::buildChallengeLink('<img src="' . URL_THUMBNAILS.'/'.$thumbnail.'" width="'.$width.'" />', $challengeid);
	}
	
	static function buildChallengeLink($content, $challengeid, $class="")
	{
		return '<a class="'.$class.'" href="?p=challenges&id='.$challengeid.
					'" onclick="setTeamTab(\'challenges\','.$challengeid.'); return false;">' . $content.'</a>';
	}

	static function buildChallengeSubmitLink($content, $challengeid, $class="")
	{
	//	return '<a class="'.$class.'" href="?p=challenges&id='.$challengeid.
	//				'" onclick="showChallengeSubmitDialog('.$challengeid.'); return false;">' . $content.'</a>';
		return '<a class="'.$class.'" href="?p=challengeSubmit&id='.$challengeid.
					'" onclick="setTeamTab(\'challengeSubmit\','.$challengeid.'); return false;">' . $content.'</a>';
	}	
	
	static function buildLinkedRewardPic($prizeid, $thumbnail, $width=150)
	{
		$thumbnail = $thumbnail=='' ? URL_CALLBACK.'?p=cache&img=watermark.jpg' : $thumbnail;
		//return self::buildRewardLink('<img src="'. URL_THUMBNAILS.'/'.$thumbnail.'" width="'.$width.'" />', $prizeid);
		return self::buildRewardLink('<img src="'. URL_CALLBACK."?p=cache&f=$thumbnail&x=$width&y=$width&m=scaleImg&path=uploads&fixed=x".'" />', $prizeid);
	}
	
	static function buildRewardLink($content, $prizeid)
	{
		return '<a href="?p=rewards&id='.$prizeid.
					'" onclick="setTeamTab(\'rewards\','.$prizeid.'); return false;">' . $content.'</a>';
	}

	static function buildStoryLink($content, $storyid)
	{
		return '<a href="?p=read&o=comments&record&cid='.$storyid.
					'" onclick="readStory('.$storyid.'); return false;">' . $content.'</a>';
	}

	static function buildLinkedStoryImage($imageid, $storyid) // very similar to getStoryImage but static and designed to be callable directly rather than needing template intepretation 
	{
		if ($imageid>0) {
			$temp='<a href="'.URL_PREFIX.'?p=read&cid='.$storyid.'" onclick="readStory('.$storyid.');return false;">
			<img src="http://www.newscloud.com/images/scaleImage.php?id='.$imageid.'&x=120&y=120&fixed=x&crop" 
				alt="story image" /></a>';
		} else {
			$temp='';
		}
		return $temp;
		
	}
	
	
	/*static function buildLinkedStoryImage($storyid)
	{
		$templateObj = new template();
		return 
	}
	*/
	
	static function getIndefiniteArticle($text)
	{
		//$text = ($text);
		switch ($text[0])
		{
			case 'a':
			case 'e':
			case 'i':
			case 'o':
			case 'u':
				return 'an';
				break;
			default: return 'a';
				
			
		}	
	}
	
	function countStoryComments($contentid=0) {
		$get_comments = mysql_query ("SELECT count(*) as ccount FROM Comments WHERE contentid=$contentid AND isverified=1 and isdeleted=0;");
		$comments_query=mysql_fetch_object($get_comments);	
		switch ($comments_query->ccount) {
			case 1:
				return '1 comment';
			break;
			default:
				return $comments_query->ccount.' comments';
			break;
		}	
	}

	function refreshVoteLink($contentid=0,$wrap=true) {
		$scoreQuery=mysql_query("SELECT (votesYea-VotesNay) as score FROM Content WHERE contentid=$contentid;");
		$data=mysql_fetch_object($scoreQuery);
		$code='<a onclick="voteUp('.$contentid.');" href="javascript:void(0);" title="vote story up in prominence">'.$data->score.' votes</a>';
		if ($wrap) $code='<span class="vl_'.$contentid.' voteLink">'.$code.'</span>';
		return $code;
	}

	function refreshCommentLink($contentid=0,$wrap=true) {
		$get_comments = mysql_query ("SELECT count(*) as ccount FROM Comments WHERE contentid=$contentid AND isverified=1 and isdeleted=0;");
		if ($get_comments) {
			$comments_query=mysql_fetch_object($get_comments);	
			switch ($comments_query->ccount) {
				case 0:
					$str='<a href="/read/'.$contentid.'/?showComments" title="Share your thoughts about this story">Post a comment</a>';
				break;
				default:
					$str='<a href="/read/'.$contentid.'/?showComments" title="read other\'s comments">Read comments ('.$comments_query->ccount.')</a>';
				break;
			}
			if ($wrap) $str='<span class="mcl_'.$contentid.'">'.$str.'</span>';
		} else 
			$str='';
		return $str;
	}

	function getSmallImage($imageid=0) {
		if ($imageid<>0)
			return '<a href="/read/{contentid}/"><img src="/images/scaleImage.php?id='.$imageid.'&amp;x=50&amp;y=50" alt="thumbnail" /></a>';
		else
			return '';
	}

	function formatDateStr($date='') {
		return date('g:i a n/d/y',strtotime($date));
	}
	
	function submitBy($str='') {
		// replace submitBy token with link to Facebook journal (if member of Facebook site)
		// if newscloud member, don't link for now
		// to do: can look up postedById
		return $str;
	}

	function cleanEllipsis($str='',$cnt=250) {
		// adds an ellipsis past the nth char where n=$cnt
		// strips tags as well
		return $this->ellipsis(strip_tags($str),$cnt);
	}

	function cleanString($str='',$cnt=250) {
		// returns substring with tags stripped
		// used for functions with &hellip;&nbsp; added
		return substr(strip_tags($str), 0, ($cnt - 1));
	}

	function ellipsis($str='',$cnt=250) {
		// adds an ellipsis past the nth char where n=$cnt
		if (strlen($str)>$cnt) {
			$str=mb_substr($str,0,($cnt-1)).'...';
		}
		return $str;			
	}

	function addTemplate($name='',$template='') {
		$this->templates[$name]=$template;
	}
	
	function addTemplateDynamic($dynTemp, $name, $template, $helpString='', $category='', $ajaxEdit=true)
	{
		
		$template = $dynTemp->useDBTemplate($name,$template, $helpString, false, $category, $ajaxEdit); // refresh should be true for item and list templates...
		$this->addTemplate($name, $template);
	}
	
	// minor hack to make disabled dynamic template call signatures compatible with static ones and make it easy to switch them back on
	function addTemplateStatic($dynTemp, $name, $template, $helpString='', $category='', $ajaxEdit=true)
	{
		$this->addTemplate($name, $template);		
	}
		
	/* end of callback functions */
	
	function buildFacebookUserList($title='',$arrUserList) {
		$rxCnt=count($arrUserList);
		if ($rxCnt>0) {
			// needs to look up if people are members and build link to proper url - facebook or application hotdish
			$nameList='';
			$picList='';
			$i=0;
			foreach ($arrUserList as $fbId){				
				// if not a member, then link to profile page with standard link
				// links need to close dialog
				$name='<fb:name ifcantsee="Anonymous" uid="'.$fbId.'" capitalize="true" linked="false" />';
				$name='<fb:if-is-app-user uid="'.$fbId.'"><a href="?p=profile&memberid='.$fbId.'" onclick="hideDialog();switchPage(\'profile\',\'\','.$fbId.'); return false;">'.$name.'</a><fb:else><a href="http://www.facebook.com/profile.php?id='.$fbId.'" target="_blank">'.$name.'</a></fb:else></fb:if-is-app-user>';
				$pic='<fb:profile-pic uid="'.$fbId.'" linked="false" size="thumb" />';
				$pic='<fb:if-is-app-user uid="'.$fbId.'"><a href="?p=profile&memberid='.$fbId.'" onclick="hideDialog();switchPage(\'profile\',\'\','.$fbId.'); return false;">'.$pic.'</a><fb:else><a href="http://www.facebook.com/profile.php?id='.$fbId.'" target="_blank">'.$pic.'</a></fb:else></fb:if-is-app-user>';
				$i+=1;				
				if ($nameList!='')
					$nameList.=', ';
				if ($i==$rxCnt AND $rxCnt>1) $nameList.=' and ';						
				$nameList.=$name;
				$picList.=$pic.' ';
			}
		 	return $title.$nameList.'<br />'.$picList;
		} else {
			return '';
		}
	}
	
		
	function paging($pageCurrent=1,$rowTotal=0,$rowLimit=7,$link='',$jscriptFunction='',$ajaxOn=false,$nav=NULL) {
		switch (MODULE_ACTIVE) {
			default:
				$hrefCode='href="javascript:void(0);"';
			break;
			case 'FACEBOOK':
				$hrefCode='href="#"';
			break;
		}
		// $link is the url that the page navigation will point to - this functions add the page offset as the suffix
		// e.g. $link ='/search/keyword/tag/sort/' ... pages will link to '/search/keyword/tag/sort/pagenumber/'
		// previous query must use SQL_CALC_FOUND_ROWS
		$pageTotal=ceil($rowTotal/$rowLimit);
		$nav->last=$pageTotal;
		$nav->current=$pageCurrent;
		$pageStart=($pageCurrent-4)>0 ? ($pageCurrent-4) : 1;
		$pageEnd=($pageCurrent+4)>$pageTotal ? $pageTotal : ($pageCurrent+4);
		$ellipsis='<span>...</span>';
		if ($rowTotal==0)
			return '';
		$text='<div class="pages">';
		// previous page
		if ($pageCurrent>1) {
			$text.='<a '.$hrefCode.' class="nextprev" onclick="return refreshPage('.($pageCurrent-1).');">&#171; Previous</a>'; 
			$nav->previous=$pageCurrent-1;
		} else {
			$text.='<span class="nextprev">&#171; Previous</span>';
			$nav->previous=1;
		}
		// page 1 & 2
		if ($pageCurrent>5)
			$text.='<a '.$hrefCode.' onclick="return refreshPage(1);">1</a><a href="javascript:void(0);" onclick="refreshPage(2);">2</a>'.$ellipsis; 
		// current nine pages
		for ($i=$pageStart;$i<=$pageEnd;$i++) {
			if ($i==$pageCurrent)
				$text.='<span class="current">'.$i.'</span>';
			else
				$text.='<a '.$hrefCode.' onclick="return refreshPage('.$i.');" >'.$i.'</a>';
		}
		if (($pageTotal-$pageCurrent)>5)
			$text.=$ellipsis.'<a '.$hrefCode.' onclick="return refreshPage('.($pageTotal-1).');">'.($pageTotal-1).'</a><a '.$hrefCode.' onclick=" return refreshPage('.$pageTotal.');">'.$pageTotal.'</a>'; 
		// next page
		if ($pageCurrent<$pageTotal) {
			$text.='<a '.$hrefCode.' class="nextprev" onclick="return refreshPage('.($pageCurrent+1).');">Next &#187;</a>'; 
			$nav->next=$pageCurrent+1;
		} else {
			$nav->next=$pageCurrent;
			$text.='<span class="nextprev">Next &#187;</span>';
		}
		$text.='</div>';
		return $text;
	}


	function resetCache($area='',$id=0) {
		switch ($area) {
			default:
			break;
			case 'read': // delete comment thread 
				$this->deleteCachePrefix('read_'.$id.'_top');
				$this->deleteCachePrefix('read_'.$id.'_com_m');
				$this->deleteCachePrefix('read_'.$id.'_com_n');
				$this->deleteCachePrefix('pc_read_'.$id.'_anon');		
			break;
			case 'newswire':
				$this->deleteCachePrefix('nw_');
			break;
			case 'home_ts':
				$this->deleteCacheFile('home_ts');
			break;
			case 'home_feature':
				$this->deleteCacheFile('home_feature');
			break;
		}
	}
	
	function deleteCachePrefix($prefix) {
		foreach (glob(PATH_CACHE."/".CACHE_PREFIX.'_'.$prefix."*.cac") as $filename) {
			if (file_exists($filename)) unlink($filename);
		}
	}
	function deleteCacheFile($filename) {
		$filename=PATH_CACHE.'/'.CACHE_PREFIX.'_'.$filename.'.cac';
		if (file_exists($filename)) unlink($filename);
	}

	function checkCache($filename,$age=15) {
		if (ENABLE_TEMPLATE_EDITS) return false; // dont allow any cache fetches during edit mode 
		
		// checks if cached file is older then $age minutes
		// returns true if file is fresh
		$filename=PATH_CACHE.'/'.CACHE_PREFIX.'_'.$filename.'.cac';
		if (file_exists($filename) AND !isset($_GET['nc'])) {
			// use last cache version for robots
			if ((time()-(60*$age))<filemtime($filename)) return true; // OR $page->isRobot()
		}
		return false;
	}
	
	function fetchCache($filename) {
		//if (ENABLE_TEMPLATE_EDITS) return 'Error - cannot fetch from cache while site in edit mode'; // dont allow any cache fetches during edit mode 
		
		$filename=PATH_CACHE.'/'.CACHE_PREFIX.'_'.$filename.'.cac';
		$fHandle=fopen($filename,'r');
		$fSize=filesize($filename);
		if ($fSize>0) 
			$contents = fread($fHandle, $fSize);
		else
			$contents='';
		fclose($fHandle);
		return $contents;
	}

	function cacheContent($filename,$html) {
		if (ENABLE_TEMPLATE_EDITS) return; 
		
		// writes the code in $html to $filename in cache directory
		$filename=PATH_CACHE.'/'.CACHE_PREFIX.'_'.$filename.'.cac';
		$fHandle=fopen($filename,'w');
		if ($fHandle!==false) {
			fwrite($fHandle,$html);
			fclose($fHandle);
		}
	}	


	function safeFilename($filename) 
	{	
		$patterns=array		 ('/#/','/ /','/\'/','/"/','/&/','%/%','/\\\\/','/\?/');
		$replacements = array('_'  ,'_','_' ,'_','_','_','_' ,'_');
				
		$filename = preg_replace($patterns,$replacements,$filename);
	
		return $filename;
	
	}
	
	
}	
?>