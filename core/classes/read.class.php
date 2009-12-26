<?php

define ("LENGTH_SHORT_CAPTION",350);
define ("LENGTH_LONG_CAPTION",700);

class read {
	
	var $db;
	var $session;
	var $templateObj;
	var $utilObj;
	var $pageObj;
	var $initialized = false;
		
	function read(&$db=NULL,&$session=NULL) {
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
		if (!is_null(&$session)) {
			$this->session=&$session;
		}
	}

	function initObjs() {
		if ($this->initialized)
			return true;
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'read');
		$this->initialized = true;
	}
	
	function setPageLink(&$pageObj) {
		$this->pageObj=&$pageObj;
	}

	function resetStoryCache($cid=0) {
		$this->initObjs();
		$this->templateObj->resetCache('read',$cid);
	}
	
	function fetchReadStory($cid=0, $nolongerused='') {
		$this->initObjs();
		$cacheName='read_'.$cid.'_top';
		if ($this->templateObj->checkCache($cacheName,30)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {		
			$code='';
			if (!is_numeric($cid)) exit ('error3');
			$q=$this->db->query("SELECT isBlogEntry,url,videoid,widgetid FROM Content WHERE siteContentId=$cid");
			$data=$this->db->readQ($q);
			$isBlogEntry=$data->isBlogEntry;
			$videoid=$data->videoid;
			$widgetid=$data->widgetid;
			$url=$data->url;
			$this->templateObj->db->setTemplateCallback('submitBy', array($this->templateObj, 'submitBy'), 'postedByName');
			$this->templateObj->db->setTemplateCallback('caption', array($this->templateObj, 'cleanString'), array('caption', LENGTH_LONG_CAPTION));
			$this->templateObj->db->setTemplateCallback('cmdVote', array($this->templateObj, 'commandVote'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('cmdComment', array($this->templateObj, 'commandComment'), 'siteContentId');
			$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'userid');
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), 'postedById');
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
			$this->templateObj->db->setTemplateCallback('cmdRead', array($this->templateObj, 'commandRead'), 'permalink');
			$this->templateObj->db->setTemplateCallback('videoIntro', array($this, 'getVideoIntro'), 'videoIntroId');
			$this->templateObj->db->result=$this->templateObj->db->query("SELECT Content.*,ContentImages.url as imageUrl,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid OR Content.postedById = UserInfo.userid) LEFT JOIN ContentImages ON (ContentImages.siteContentId=Content.siteContentId) WHERE Content.siteContentId=$cid");
			if ($isBlogEntry==0) {
				$this->templateObj->db->setTemplateCallback('storyImage', array($this->templateObj, 'getLargeStoryImageFromUrl'), 'imageUrl');
				$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['readStoryList'],$this->templateObj->templates['readStoryItem']);				
			} else {
				// blog entry
				if ($url=='') {
					// no url, leave out the more and read full story buttons
					$item=$this->templateObj->templates['blogStoryItem'];
				} else {
					// relabel the read full story button
					$item=str_replace('Read the full story','Read the related story',$this->templateObj->templates['readStoryItem']);
				}
				$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['readStoryList'],$item);
				require_once(PATH_CORE.'/classes/userBlogs.class.php');
				$ubTable = new UserBlogsTable($this->db);
				$ub = $ubTable->getRowObject();			
				$ub->loadWhere("siteContentId=".$cid);
				if ($ub->imageUrl=='') {					
					$temp=str_replace('{storyImage}','',$temp);
				} else {										
					$temp=str_replace('{storyImage}','<img  src="'.$ub->imageUrl.'" alt="blog image" />',$temp); // style="width:180px; height:auto;"
										
				}
				$code.=$temp;
				$code.='<div id="readBlog">';
				$code.=$ub->entry;
				$code.='<!-- end readBlog --></div>';
			}			
			if ($videoid>0) {
				// display embedded video
				require_once(PATH_CORE.'/classes/video.class.php');
				$videoTable = new VideoTable($this->db);
				$video = $videoTable->getRowObject();
				$video->load($videoid);	
				$code.='<div id="readVideo">'. videos::buildPlayerFromLink($video->embedCode,320,240) .'<!-- end of storyVideo --></div>';
			}
			if ($widgetid>0) {
					require_once(PATH_CORE.'/classes/widgets.class.php');
					$wt=new WidgetsTable($this->db);
					$code.=$wt->fetchWidgets($widgetid);			
			}
			$this->templateObj->cacheContent($cacheName,$code); 			
		}

		if (defined('ADS_ANY_SMALL_BANNER')) {
			$code.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=anySmallBanner" frameborder="0" scrolling="no" style="width:478px;height:70px;margin:-5px 0px 5px 0px;padding:0px;"/>',$this->pageObj->common['adWrapSmallBanner']);
		}							
		
		$cacheName='read_'.$cid.'_com_'.($this->session->isMember?'m':'n');
		if ($this->templateObj->checkCache($cacheName,30)) {
			// still current, get from cache
			$temp=$this->templateObj->fetchCache($cacheName);
		} else {					
			$temp = '<div id="commentList">';
			$temp .= $this->fetchComments($cid);
			$temp .= '</div><!-- end commentList -->';
			$this->templateObj->cacheContent($cacheName,$temp); 			
		}		
		$code.=$temp;
		$code.=$this->fetchLinkBox($cid);
		if (defined('ADS_ANY_LARGE_RECT')) {
			$code.=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=anyLargeRect" frameborder="0" scrolling="no" style="width:346px;height:290px;margin:0px;padding:0px;"/>',$this->pageObj->common['adWrapLargeRect']);
		}								
		return $code;
	}
	
	function getVideoIntro($videoIntroId=0) {
		if ($videoIntroId>0 ) {
			require_once(PATH_CORE.'/classes/video.class.php');
			$videoTable = new VideoTable($this->db);
			$video = $videoTable->getRowObject();
			$video->load($videoIntroId);	
			$str='<div class="videoIntro">'.videos::buildPlayerFromLink($video->embedCode, 320, 240).'</div><!-- end videoIntro -->';
			return $str;	
		} else 
			return '';
	}

	function fetchLinkBox($cid=0) {
		if ($this->session->isLoaded)
 			$referStr='&referid='.$this->session->userid;
 		else
 			$referStr='';
 		$storyLink=URL_CANVAS.'?p=read&cid='.$cid.$referStr;
		$q=$this->db->query("SELECT title,caption FROM Content WHERE siteContentId=$cid");
		$data=$this->db->readQ($q);
		$title=htmlentities($this->templateObj->ellipsis($data->title),ENT_QUOTES);
		$caption=htmlentities($this->templateObj->ellipsis($data->caption,LENGTH_SHORT_CAPTION),ENT_QUOTES);
		$tweetStr=$this->templateObj->ellipsis($data->title,80).' '.URL_HOME.'?x='.base_convert($cid,10,36).' '.(defined('TWITTER_HASH')?TWITTER_HASH:'');
		$tweetThis='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode($tweetStr).'" target="_blank"><img src="'.URL_CALLBACK.'?p=cache&img=tweet_button.gif" alt="tweet this" /></a>';		
		$shareButton='<div style="float:left;padding:0px 5px 0px 0px;display:inline;"><fb:share-button class="meta"><meta name="title" content="'.$title.'"/><meta name="description" content="'.$caption.'" /><link rel="target_url" href="'.$storyLink.'"/></fb:share-button><!-- end share button wrap --></div>';
 		$code = '<div  id="actionLegend">'.$shareButton.'<p class="bold">'.$tweetThis.' Link to this story </p>';
          $code.= '<div class="pointsTable"><table cellspacing="0"><tbody>'.
				'<tr><td><input class="inputLinkNoBorder" type="text" value="'.$storyLink.'" onfocus="this.select();" /></td></tr>'.
				'</tbody></table></div><!-- end points Table --></div><!-- end story link box -->';
 		return $code;	
 	}			

	function fetchReferComment(&$msg) {
		$this->initObjs();
		$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
		//$this->templateObj->db->setTemplateCallback('submitBy', array($this->templateObj, 'submitBy'), 'postedByName');
		$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'buildLinkedProfileName'), array('fbId', 'false'));
		$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'buildLinkedProfilePic'), array('fbId', 'square'));
		$code.=$this->templateObj->processRow($msg,$this->templateObj->templates['referItem'],$this->templateObj->db->template_callbacks);
		$code.= "<br />";
		return $code;		
	}

	function fetchComments($cid) 
	{
		$this->initObjs();
		$this->templateObj->db->result = $this->templateObj->db->query("SELECT Comments.*, UserInfo.fbId, Videos.embedCode FROM Comments LEFT JOIN UserInfo ON Comments.userid = UserInfo.userid LEFT JOIN Videos ON Comments.videoid=Videos.id WHERE siteContentId=$cid AND isBlocked = 0 ORDER BY date ASC");
		$commentTotal = $this->templateObj->db->countFoundRows();
		//$code .= "<p>Total Comments: $commentTotal</p>";
		if ($commentTotal == 0) {
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['noCommentList'],$this->templateObj->templates['noCommentItem']);
		} else {
			$this->templateObj->db->setTemplateCallback('timeSince', array($this->utilObj, 'time_since'), 'date');
			$this->templateObj->db->setTemplateCallback('submitBy', array($this->templateObj, 'submitBy'), 'postedByName');
			$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'userid');
			$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), array('postedById', 'square'));
			$this->templateObj->db->setTemplateCallback('video', array($this, 'fetchVideoComment'), array('embedCode', 160,100));
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['commentList'],$this->templateObj->templates['commentItem']);
			$code .= "<br /><br />";
		}
		$code .= '<div id="postComment" class="panel_1">'.'
					<div class="panelBar clearfix"><h2>Post a comment</h2>'.
						$this->fetchVideoCommentLink().
					'</div>';
		$code .= $this->fetchVideoCommentForm($cid);
		$code .= '<div class="panel_block">';
		//$code .= '<form name="commentForm" action="?p=read&o=comments&cid='.$cid.'" method="post">'; // </form>
		// REG_SIMPLE
		if ($this->session->isMember OR (defined('REG_SIMPLE') AND $this->session->isLoggedIn)) {
			$code .= '<div id="commentMsgDiv" style="overflow: hidden"><textarea name="commentMsg" class="formfield comments" id="commentMsg" rows="8"></textarea></div><br clear="all"/>';
			$code .= '<input type="button" class="btn_1" value="Post your comment" onclick="postComment('.$cid.');">';
		} else {
			$code .= '<a '.(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').' href="?p=signup'.(isset($_GET['referid'])?'&referid='.$_GET['referid']:'').'" class="btn_1">Join the '.SITE_TEAM_TITLE.' to post comments!</a>';
		}
		$code .= '</div><!-- end panelBar --></div><!-- end panel_1 -->';
		return $code;
	}
	
	function fetchVideoComment($videoURL, $width=160, $height=100)
	{
		require_once(PATH_CORE .'/classes/video.class.php');
		$code =  '<div style="text-align:center;">'. videos::buildPlayerFromLink($videoURL, $width, $height) .'</div>';
		return $code;
	}
	
	function fetchVideoCommentLink()
	{
		if (1)//$this->session->isAdmin)
		{
			$code .= "<script>
			
				var videoCommentState=false;
				function showPostVideoCommentForm(nodeid)
				{
				
					if (!videoCommentState)
					{
						//document.getElementById(nodeid).setClassName('');
						Animation(document.getElementById(nodeid)).to('height', 'auto').from('0px').to('width', 'auto').from('0px').to('opacity', 1).from(0).blind().show().go();
						Animation(document.getElementById('commentMsgDiv')).to('height', '0px').from('100%').to('opacity', 0).from(1).go(); // dont use .hide() so the display: block attr keeps the post button in the right place...lame css
						//document.getElementById('commentMsg').setClassName('hidden');
						
						videoCommentState=true;
					} else
					{
					
						//document.getElementById('commentMsg').setClassName('');

						Animation(document.getElementById('commentMsgDiv')).to('height', 'auto').from('0px').to('opacity', 1).from(0).blind().show().go();
						Animation(document.getElementById(nodeid)).to('height', '0px').from('100%').to('opacity', 0).from(1).go(); // dont use .hide() so the display: block attr keeps the post button in the right place...lame css
				
						
						//Animation(document.getElementById('commentMsgDiv')).to('height', 'auto').from('0px').to('opacity', 1).from(0).show().go();
						//Animation(document.getElementById(nodeid)).to('height', '0px').from('auto').to('width', '0px').from('auto').to('opacity', 0).from(1).blind().hide().go();
						//document.getElementById(nodeid).setClassName('hidden');
						
						videoCommentState=false;
					
					}
						
				}
				
				function popupVideoCommentServicePage(nodeid)
				{
					url = document.getElementById(nodeid).getValue();
					window.open(url, 'videoWindow');
					
				}
				
				
			
				
				</script>";			
			$code .= '<div class="bar_link clearfix"><a onclick="showPostVideoCommentForm(\'videoCommentForm\'); return false;">Post a Video Comment</a></div>'; 
	
		} else
			$code = ''; // hidden from non-admin for now
	//	$code .=' was here';
		return $code;
	}
	
	function fetchVideoCommentForm($cid)
	{
		//if (!$this->session->isAdmin) return '';
		// note: the facebook link is apparently hardcoded to an internal video recording application (?)
	/*	$code .= '<div id="videoCommentForm" class="hidden">'.
						'Record a video comment via'. 
						//'<input type="hidden" id="youtube_link" value="http://www.youtube.com/my_videos_quick_capture" />'.
						'<a href="http://www.youtube.com/my_videos_quick_capture" target="_blank">YouTube</a>'.						
						//'<span class="pipe">|</span>'.
						//'<input type="hidden" id="facebook_link" value="http://www.facebook.com/home.php?filter=app_2392950137" />'.
						' or '.
						'<a href="http://www.facebook.com/home.php?filter=app_2392950137"
								target="_blank">Facebook</a>'.
					'</div>';
		*/
				
		$code .= '<div id="videoCommentForm" class="hidden">';
		$code .='<fb:editor action="" labelwidth="150">';
		
		//$code .= '<table class="editorkit">';
			//$code .= '<tr class="width_setter"><th style="width: 220px;"/><td/></tr>'; // label width setting					
		 	$code .= '<input name="contentid" type="hidden" value="'. $cid . '"/>';
			$code .= $this->setHiddenSession($this->session->userid, $this->session->sessionKey); // so these can be passed through to the POST handler
		 	$code .= '<input name="debugSubmit" type="hidden" value="0"/>'; // set to 1 for submit debug output
		 	
			$code .='<fb:editor-custom label="Step 1. Record a video comment">'
						.'<a href="http://www.youtube.com/my_videos_quick_capture" target="_blank">YouTube</a>'.						
						//'<span class="pipe">|</span>'.
						//'<input type="hidden" id="facebook_link" value="http://www.facebook.com/home.php?filter=app_2392950137" />'.
						' or '.
						'<a href="http://www.facebook.com/video/?record"
								target="_blank" >Facebook</a>'.
						'</fb:editor-custom>';
			
			$code .= '<tr><th><label>Step 2. Enter your video URL or Embed Code:</label></th>
						<td class="editorkit_row"><input type="text" name="videoURL" id="videoURL" onChange="videoURLChanged();return false;"></td>
						<td class="right_padding"></td></tr>';
						
			//$code .='<fb:editor-text id="videoURL" label="Step 2. Enter your video URL" name="videoURL" onChange="videoURLChanged();return false;" value="'.														
				///		'" />';
			//$code .='<fb:editor-custom label="Step 3. Preview">'.
			//$code .= '<input type="button" class="btn_1" value="Preview" onclick="previewVideoComment('.$cid.');" />';
			//			'</fb:editor-custom>';
			
						//$code.='<fb:editor-buttonset>  
	        //   <fb:editor-button value="Post"/>   </fb:editor-buttonset>';
			$code .='</fb:editor>';	
	
			$code .='<div id="videoPreview"><div id="videoPreviewMsg">Video Preview</div></div>';
			
			$code .='<h3>Please only post video of your personal comments. This feature is experimental, please <a href="'.URL_CANVAS.'?p=contact">contact us</a> with questions, feedback and bug reports.</h3>';
			
			//$code .= '</table>';
		$code .= '</div>';
		
		return $code;
		
	}
	
	function setHiddenSession($userid, $sessionKey) {
		$code.='<input type="hidden" name="userid" value="'.$userid.'" />';
		$code.='<input type="hidden" name="sessionKey" value="'.$sessionKey.'" />';
		return $code;
	}

	function fetchReadSidePanel($cid, &$session,$isAjax=false) {
		$this->initObjs();
		$cacheName='read_'.$cid.'_bio';
		if ($this->templateObj->checkCache($cacheName,30)) {
			// still current, get from cache
			$code=$this->templateObj->fetchCache($cacheName);
		} else {					
			$story = mysql_fetch_assoc($this->templateObj->db->query("SELECT Content.*, UserInfo.fbId, UserInfo.bio FROM Content LEFT JOIN UserInfo ON (Content.userid = UserInfo.userid OR Content.postedById = UserInfo.userid) WHERE siteContentId=$cid"));
			$userid = $story['userid'];
			$fbId = $story['fbId'];
			if ($story['bio'] == '' || $story['bio'] == NULL) {
				$bio = 'Hi my name is <fb:name ifcantsee="Anonymous" uid="'.$fbId.'" capitalize="true" firstnameonly="false" linked="false" /> and I forgot to add my bio. Next time you see me remind me to update my bio information.';
			} else {
				$bio = $story['bio'];
			}
			if ($userid == 0)
				$postedById = $story['postedById'];
			else
				$postedById = false;
	
			if (!$postedById) {
				$id = $userid;
				$field = 'userid';
			} else {
				$id = $postedById;
				$field = 'postedById';
			}
	
	
			// Bio profile
			$code = '<div class="panel_1"><div class="panelBar clearfix">';
			$code .= '<h2>Posted by <a href="'.URL_CANVAS.'?p=profile&memberid='.$fbId.'" onclick="return switchPage(\'profile\', \'\', '.$fbId.');"><fb:name ifcantsee="Anonymous" uid="'.$fbId.'" capitalize="true" firstnameonly="false" linked="false" /></a></h2>';
			$code .= '</div><!-- end panelBar -->';
			$code .= '<div class="panel_block"><div class="thumb"><a href="'.URL_CANVAS.'?p=profile&memberid='.$fbId.'" onclick="return switchPage(\'profile\', \'\', '.$fbId.');"><fb:profile_pic uid="'.$fbId.'" linked="false" size="square" /></a></div><!--end "thumb"-->';
			$code .= '<div class="storyBlockWrap"><p>'.$bio.'</p></div>';
			$code .= '</div><!-- end panel_block --></div><!-- end panel_1 -->';
	
			// Other stories by this user panel
			if ($fbId<>'') { // fix for bug 252, when user no longer exists
				$this->templateObj->db->result = $this->templateObj->db->query("SELECT *, $fbId as fbId FROM Content WHERE $field = $id AND siteContentId != $cid ORDER BY date DESC LIMIT 10 ");
				$this->templateObj->db->setTemplateCallback('mbrLink', array($this->templateObj, 'memberLink'), 'userid');
				$code .= '<div class="panel_1"><div class="panelBar clearfix"><h2>Other posts by <a href="?p=profile&memberid='.$fbId.'" onclick="return switchPage(\'profile\', \'\', '.$fbId.';"><fb:name ifcantsee="Anonymous" uid="'.$fbId.'" capitalize="true" firstnameonly="false" linked="false" /></a></h2></div><!-- end panelbar --><br />';
				$code .= $this->templateObj->mergeTemplate($this->templateObj->templates['otherStoryList'],$this->templateObj->templates['otherStoryItem']);
				$code .= '</div><!-- end panel_1 -->';			
			}
			$this->templateObj->cacheContent($cacheName,$code); 			
		}

		if ($session->isLoaded) {
			$cacheName='read_friends_'.$cid.'_'.$session->userid;
			if ($this->templateObj->checkCache($cacheName,15)) {
				// still current, get from cache
				$temp=$this->templateObj->fetchCache($cacheName);
			} else {					
		  		// Friends who read this panel
				$temp .= '<div class="panel_1"><div class="panelBar clearfix"><h2>Friends who read this</h2></div>';
				$temp .= '<div class="panel_block">';
				$friends = $session->ui->memberFriends;
				if ($friends == $userid) {
					$friendsReadTotal = 0;
				} else {
					$friends = preg_replace('/'.$userid.',|,'.$userid.'/', '', $friends);				
					if ($friends<>'') {
						$this->templateObj->db->result = $this->templateObj->db->query("SELECT Log.userid1, UserInfo.fbId FROM Log LEFT JOIN UserInfo ON (Log.userid1 = UserInfo.userid) WHERE userid1 IN ($friends) AND action = 'readStory' AND itemid = $cid");					
						$friendsReadTotal = $this->templateObj->db->countFoundRows();					
					} 
					else {
						$friendsReadTotal = 0;
					}
				}
				if ($friendsReadTotal > 0) {
					$this->templateObj->db->setTemplateCallback('mbrImage', array($this->templateObj, 'memberImage'), array('userid1', 'square'));
					$temp .= $this->templateObj->mergeTemplate($this->templateObj->templates['otherFriendsList'],$this->templateObj->templates['otherFriendsItem']);
				} else {
					$temp .= '<p>None of your friends have read this story yet. <a href="#" onclick="shareStory(this,'.$cid.');return false;">Share it with your friends</a></p>';
				}
				$temp .= '</div><!-- end panel_block --></div><!-- end panel_1 -->';
				$this->templateObj->cacheContent($cacheName,$temp); 			
			}
			$code.=$temp;
			if ($this->session->isAppAuthorized) {
	  			$cacheName='read_chat_'.$cid.'_'.$session->userid;
				if ($this->templateObj->checkCache($cacheName,5)) {
					// still current, get from cache
					$temp=$this->templateObj->fetchCache($cacheName);
				} else {
					$temp = '<div class="panel_1"><div class="panelBar clearfix"><h2>Chat with friends</h2></div>';
					$temp .= '<div class="panel_block">';
					// look for cached online_presence list for this user
					$cacheName2='chat_'.$session->userid;
					if ($this->templateObj->checkCache($cacheName2,5)) {
						// still current, get from cache
						$liExcludeStr=$this->templateObj->fetchCache($cacheName2);
					} else {
						$this->session->facebook=$this->session->app->loadFacebookLibrary();
						$arExclude=$this->session->facebook->api_client->fql_query("select uid from user where online_presence='offline' AND uid IN (SELECT uid2 FROM friend WHERE uid1 = ".$this->session->ui->fbId." );");				
						if (count($arExclude)>0) {
							$excludeStr='';
							foreach ($arExclude as $arrfbId) {						
								$excludeStr.=$arrfbId['uid'].',';							
							}
							$liExcludeStr='exclude_ids="'.trim($excludeStr,',').'"';
							$this->templateObj->cacheContent($cacheName2,$liExcludeStr);	
						} else 
							$liExcludeStr='';
					}								
					$q=$this->db->query("SELECT Content.title FROM Content WHERE siteContentid=$cid;");
					$data=$this->db->readQ($q);
					$temp.='<div style="margin-left:18px;"><fb:chat-invite msg="Let\'s chat about '.htmlentities($data->title,ENT_QUOTES).' at '.SITE_TITLE.'. You can read the story here '.URL_CANVAS.'?p=read&cid='.$cid.'&referid='.$session->userid.'&chat" condensed="false" /></div>'; //'.$liExcludeStr.' 
					$temp .= '</div><!-- end panel_block --></div><!-- end panel_1 -->';	
					$this->templateObj->cacheContent($cacheName,$temp);			
				}									
				$code.=$temp;
			}
		}
		return $code;
	}

}

?>
