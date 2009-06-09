<?php

define ("MIN_BLOG_LENGTH",250);
define ("MAX_BLOG_LENGTH",5000);
define ("CAPTION_LENGTH",250);
define ("MAX_CAPTION_LENGTH",350);

class pagePostStory {

	var $page;
	var $db;
	var $facebook;
	var $app;
	var $session;
	var $templateObj;
	var $fData;
	var $postTip=true;	
	var $titleLimit=200;
	var $logObj=NULL;
		
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->app=&$page->app;
		$this->session=&$page->session;
		$this->fData=NULL;
	}
	
	function setupLibraries() {
		if (is_null($this->logObj)) {
			require_once(PATH_CORE.'/classes/log.class.php');
			$this->logObj=new log($this->db);					
		}
	}
	
	function fetch($option='link') {		
		global $init;
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'postStory');
		// build the post story page
		if (defined('ENABLE_USER_BLOGGING') AND ENABLE_USER_BLOGGING===true) {
			$inside.=$this->buildSubNav($option).'<br /><br />';			
		} 
		switch ($option) {
			case 'auto':
				$inside.=$this->buildScriptInclude('auto');			
				if ($this->session->u->isModerator OR $this->session->u->isSponsor OR $this->session->u->isAdmin) {
					// auto posting for moderators - a quicker way to post and feature stories
					require_once(PATH_CORE.'/classes/newswire.class.php');					
					$nwObj=new newswire($this->db);	
					$stories=$nwObj->fetchRawStories();
					$inside.='<p>This is an experimental page viewable only by administrators to speed up posting stories and featuring them.</p><p><a href="'.URL_HOME.'?p=engine&force=syncLog&apiKey='.$init['apiKey'].'" target="_cts">Sync photos now</a></p>';
					$inside.='<div id="newswireWrap">';
					$inside.=$stories;
					$inside.='<!-- end newswireWrap --></div>';											
				} else {
					$inside.=$this->page->buildMessage('error','Access Denied','You do not have permission to view this page.');
				}
			break;
			case 'blog':
				$result=false;
				if (isset($_GET['editid'])) {
					$editid=$_GET['editid'];
					$this->fData=$this->loadBlogDraft($editid);
					if ($this->fData->status=='published') {
						// already published, redirect to story
						$this->facebook->redirect(URL_CANVAS.'?p=read&cid='.$fData->siteContentId);
						exit;						
					}
				} else {
					$editid=0;
				}
				if (isset($_GET['submit'])) {
					$this->validate('blog');
					if (!$this->fData->result) {
						$result=false;
						$inside.=$this->page->buildMessage('error','Problems with your blog entry', $this->fData->alert);
					} else {
						switch ($_POST['submit']) {
							default: // save as draft
								$this->fData->blogid=$this->saveBlogDraft($this->fData);
							break;
							case 'Publish':								
								$status=$this->publishBlog($this->fData);
								$result=$status[result];
								if ($result) {
									$this->facebook->redirect(URL_CANVAS.'?p=read&cid='.$status[siteContentId].'&justPosted'.(isset($_GET['popup'])?'&viaBookmarklet':''));
									exit;
								} else {
									$inside.=$this->page->buildMessage('error','Problems with your blog entry', $status[msg]);					
								}							
							break;							
						}
					}					
				}
				// if preview 
				if (!$result) {
					$inside.=$this->buildDraftList();					
					$inside.=$this->buildPostBlogForm($this->fData);
				} else {
					// process story submission
					$inside='Your blog entry has been published';
					// redirect to the story page
					// with message offering other options to share with friends
				}			
			break;
			default:
				$result=false;
				if (isset($_GET['submit'])) {
					$this->validate('link');
					if (!$this->fData->result) {
						$result=false;
						$inside.=$this->page->buildMessage('error','Problems with your post', $this->fData->alert);
					} else {
						$status=$this->addStory($this->fData);
						$result=$status[result];
						if ($result) {
							$this->facebook->redirect(URL_CANVAS.'?p=read&cid='.$status[siteContentId].'&justPosted'.(isset($_GET['popup'])?'&viaBookmarklet':''));
							exit;
						} else {
							$inside.=$this->page->buildMessage('error','Problems with your post', $status[msg]);					
						}
					}
				}
				// if preview OR
				if (!$result) {
					$inside.='<div id="col_left"><!-- begin left side -->';
					$inside.=$this->buildPostStoryForm($this->fData);
					$inside.='<!-- end left side --></div><div id="col_right">';
					$inside.=$this->buildSidePanel();
					$inside.='</div> <!-- end right side -->';					
				} else {
					// process story submission
					$inside='Story posted';
					// redirect to the story page
					// with message offering other options to share with friends
				}
			break;
		}
		if ($this->page->isAjax) return $inside;
		$code=$this->page->constructPage('postStory',$inside);
		return $code;
	}

	function buildSidePanel() {
		// whyPost side panel
		$code='<div id="introPanel">';
		$code.=$this->templateObj->templates['whyPost'];
		$code.='<!-- end of introPanel --></div>';
		if (!$this->fData->isBookmarklet) { 
			$code.='<div id="bookmarkletFrame"><fb:iframe src="'.URL_CALLBACK.'?p=cache&m=bookmarklet&'.CACHE_PREFIX.'" frameborder="0" width="250" height="300" scrolling="no" resizable="false"></fb:iframe><!-- end bookmarklet frame--></div>';
		}
		return $code;
	}
	
	function buildDraftList() {
		if (isset($_POST['blogid'])) 
			$excludeBlogId=$_POST['blogid'];
		else if (isset($_GET['editid']))
			$excludeBlogId=$_GET['editid'];
		else if (is_numeric($this->fData->blogid))
			$excludeBlogId=$this->fData->blogid;
		else
			$excludeBlogId=0;
		// check for blog drafts by this user		
		require_once(PATH_CORE.'/classes/userBlogs.class.php');
		$ubObj= new UserBlogs($this->db);
		$this->templateObj->db->result=$ubObj->getDraftsByUserId($this->session->userid,$excludeBlogId);		
		$rowTotal=$this->templateObj->db->countFoundRows();
		$code='';
		if ($rowTotal>0) {
			$temp=$this->templateObj->mergeTemplate($this->templateObj->templates['blogDraftList'],$this->templateObj->templates['blogDraftItem']);			
			$code='<div id="draftList" class="panel_1"><div class="panelBar clearfix"><h2>Your draft blog entries</h2><div class="bar_link"><!-- end bar_link--></div></div><!-- end panelBar-->';		
			$code.= '<div class="panel_block">'.$temp.'</div>';		
			$code.='</div><!--end "draftList" "panel_1"-->';
		}						
		return $code;		
	}
	
	function loadBlogDraft($editid=0) {
		if ($editid>0) {
			require_once(PATH_CORE.'/classes/userBlogs.class.php');
			$ubTable = new UserBlogsTable($this->db);
			$ub = $ubTable->getRowObject();			
			$ub->load($editid);
			// validate author			
			if ($ub->userid==$this->session->userid) {
				$fData=new stdClass;
				$fData->state='new';
				$fData->status=$ub->status;
				//$fData->entry=$ub->entry;
				$fData->entry=mysql_real_escape_string(stripslashes($ub->entry),$this->db->handle);				
				$fData->title=$ub->title;
				$fData->url=$ub->url;
				$fData->tags='';
				$fData->mediatype='text';			
				$fData->imageUrl=$ub->imageUrl;
				$fData->videoEmbed=$ub->videoEmbed;			
				$fData->isBookmarklet=false;
				$fData->showPreview=true;
				$fData->alert='';
				$fData->blogid=$editid;				
				$fData->siteContentId=$ub->$siteContentId;
				return $fData;				
			} else {
				return NULL;
			}
		}				
	}
	
	function saveBlogDraft($fData) {
		require_once(PATH_CORE.'/classes/userBlogs.class.php');
		$ubTable = new UserBlogsTable($this->db);
		$ub = $ubTable->getRowObject();
		$ub->blogid=$fData->blogid;
		$ub->siteContentId=$fData->siteContentId;						
		$ub->title=$fData->title;
		$ub->entry=$fData->entry;
		$ub->url=$fData->url;
		$ub->imageUrl=$fData->imageUrl;
		$ub->videoEmbed=$fData->videoEmbed;
		$ub->userid=$this->session->userid;
		$ub->status=$fData->status;
		// check if it exists already
		if ($fData->blogid==0) {
			// insert blog into drafts
			$fData->blogid=$ub->insert();
		} else {
			// update blog draft
			$ub->update();
		}
		return $fData->blogid;
	}
	
	function publishBlog($fData) {
		$this->setupLibraries();
		$status=array();
		$status['result']=false;
		// Check for duplicate item
		$duplicateStory = false;
 		$dups = $this->db->queryC("SELECT siteContentId FROM Content WHERE userid=".$this->session->userid." AND url = '".$fData->url."' AND title = '".$fData->title."'");
 		$dups=false;
 		if ($dups) {
			$duplicateStory = true;
			$dupResult = $this->db->readQ($dups);
		} else {
			// create temporary content item, temp permalink
			require_once(PATH_CORE.'/classes/content.class.php');
			$cObj=new content($this->db);
			$siteContentId=$cObj->createStoryContent($this->session,$fData,'blog');
		}
		// add to user's journal
		if ($siteContentId!==false && !$duplicateStory) {
			// update UserBlogs table entry
			$fData->siteContentId=$siteContentId;
			$fData->status='published';	
			$fData->blogid=$this->saveBlogDraft($fData);			
			$status[siteContentId]=$siteContentId;			
			// add to journal
			$logItem=$this->logObj->serialize(0,$this->session->userid,'postBlog',$siteContentId);
			$inLog=$this->logObj->update($logItem);
			if ($inLog) {
				$status['result']=true;
				$status['msg']='';
				$logItem=$this->logObj->serialize(0,$this->session->userid,'vote',$siteContentId);
				$inLog=$this->logObj->update($logItem);				
			} else {
				$status['result']=true;
				$status['msg']='Blog entry previously posted';				
			}			
		} else if ($duplicateStory) {
			$status['result']=false;
			$status['msg']='That blog entry already exists. You can view it <a href="?p=read&cid='.$dupResult->siteContentId.'">here</a>';
		} else {
			$status['result']=false;
			$status['msg']='We encountered a problem posting your blog entry.';				
		}		
		return $status;				
	}
		
	function addStory($fData) {
		$this->setupLibraries();	
		$status=array();
		$status['result']=false;

		// now done through log -- can I assume the bookmarklet is being used on this page or what?
		$this->logObj->update($this->logObj->serialize(0, $this->session->userid, 'addBookmarkTool', 0,  0));
	
		// Clean up data
		//$fData->title = mysql_real_escape_string(stripslashes($fData->title));
		//$fData->caption = mysql_real_escape_string(stripslashes($fData->caption));
		// Check for duplicate item
		$duplicateStory = false;
		$dups = $this->db->queryC("SELECT siteContentId FROM Content WHERE url = '".$fData->url."' AND title = '".$fData->title."'");
		if ($dups) {
			$duplicateStory = true;
			$dupResult = $this->db->readQ($dups);
		} else {
			// create temporary content item, temp permalink
			require_once(PATH_CORE.'/classes/content.class.php');
			$cObj=new content($this->db);
			$siteContentId=$cObj->createStoryContent($this->session,$fData);
		}
		// add to user's journal
		if ($siteContentId!==false && !$duplicateStory) {
			$status[siteContentId]=$siteContentId;
			// add to journal
			$logItem=$this->logObj->serialize(0,$this->session->userid,'postStory',$siteContentId);
			$inLog=$this->logObj->update($logItem);
			if ($inLog) {
				$status['result']=true;
				$status['msg']='';
				$logItem=$this->logObj->serialize(0,$this->session->userid,'vote',$siteContentId);
				$inLog=$this->logObj->update($logItem);
				
			} else {
				$status['result']=true;
				$status['msg']='Story previously published';				
			}
		} else if ($duplicateStory) {
			$status['result']=false;
			$status['msg']='That story already exists. You can view it <a href="?p=read&cid='.$dupResult->siteContentId.'">here</a>';
		} else {
			$status['result']=false;
			$status['msg']='We encountered a problem adding your story.';				
		}		
		return $status;
	}
	
	function buildPostBlogForm($fData=NULL) {
		if (is_null($fData)) {
			// initialize form data
			$fData=new stdClass;
			$fData->state='new';
			$fData->status='draft';
			$fData->entry='';
			$fData->title='';
			$fData->url='';
			$fData->tags='';
			$fData->mediatype='text';			
			$fData->imageUrl='';
			$fData->videoEmbed='';			
			$fData->isBookmarklet=false;
			$fData->showPreview=false;
			$fData->alert='';
			$fData->blogid=0;
			$this->fData=&$fData;
		} else {
			// on submit, remove second column
			$this->postTip=false;
		}		
		$code.=$this->buildScriptInclude('top');
		$code.='<h1>Write your own story</h1><h5>Compose a new blog entry below</h5><p>Guidelines: please only post your original ideas and content here. It\'s fine to expound on stories from another Web site. However, if you just have a short comment, please post it as a comment on another story. This is an experimental feature. Please <a href="'.URL_CANVAS.'?p=contact">tell us what you think</a> and how we can improve it.</p>';
		$code.='<fb:editor action="?p=postStory&o=blog&submit'.($this->fData->isBookmarklet?'&popup':'').'" labelwidth="100">'.
	   '<fb:editor-text label="Title (required)" name="title" id="title" value="'.stripslashes(htmlentities(strip_tags($this->fData->title), ENT_QUOTES)).'"/>';
	   	$code.='<fb:editor-custom label="Body (required)">';
	   	$code.='<p>The body of your post must be at least '.MIN_BLOG_LENGTH.' characters long. Allowed HTML tags include &lt;a&gt;, &lt;p&gt;, &lt;br /&gt;, &lt;em&gt;, &lt;i&gt;, &lt;strong&gt; and &lt;img&gt;.</p>';
		$code.='<textarea rows="25" name="entry" id="entry">'.stripslashes(htmlentities($this->fData->entry, ENT_QUOTES)).'</textarea><br /><a href="#" onclick="previewBlog();return false;" >refresh preview</a></fb:editor-custom>';
	   if ($this->fData->caption=='') {
	   	$code.='<input type="hidden" name="caption" id="caption" value="">';	  
	   } else {	   	
		$code.='<fb:editor-custom label="Summary (required)"><textarea rows="5" name="caption" id="caption">'.stripslashes(htmlentities($this->fData->caption, ENT_QUOTES)).'</textarea></fb:editor-custom>';
	   }	   
	   		$code.='<fb:editor-custom><p><strong>Optional elements</strong></p></fb:editor-custom><fb:editor-divider />'.
	   	'<fb:editor-custom label="Related Story Web Address"><input type="hidden" name="blogid" id="blogid" value="'.$this->fData->blogid.'" /><input style="width:70%;" type="text" name="url" id="url" value="'.$this->fData->url.'">&nbsp;<a href="#" onclick="loadBlogData();return false;">lookup photos</a></fb:editor-custom>'.	   
	    '<fb:editor-text label="Photo Web Address" id="imageUrl" name="imageUrl" value="'.$this->fData->imageUrl.'"/>';
	    $code.=$this->buildImageSelector('blog');
	   	$code.='<fb:editor-custom label="Video"><h3>Please paste the URL or embed code for a Facebook or YouTube Video. No other services are currently supported.</h3><input type="text" name="videoEmbed" id="videoEmbed" onChange="videoURLChanged();return false;" value="'.$this->fData->videoEmbed.'">';
	   	if ($fData->videoEmbed=='')
	   		$code.='<div id="videoPreview" class="hidden"><div id="videoPreviewMsg">Video Preview</div></div><!-- end of nested videoPreview -->';
	   	else {
			require_once(PATH_CORE .'/classes/video.class.php');		 	
			$videoURL = videos::getVideoURLFromEmbedCodeOrURL(stripslashes($fData->videoEmbed));
			$code.= '<div id="videoPreview"><div id="videoPreviewMsg">'.videos::buildPlayerFromLink($videoURL, 160, 120).'</div></div><!-- end of nested videoPreview -->';	   		
	   	} 		    
	   	$code.='</fb:editor-custom>';
		$code.='<fb:editor-buttonset>  ' .
				'<fb:editor-button name="submit" value="Save as Draft"/>'.
	           '<fb:editor-button name="submit" value="Publish"/>';
		if (!$this->fData->isBookmarklet) {
			$code.='<fb:editor-cancel href="'.URL_CANVAS.'"/>';
		} else {
			// to do: link that javascript close window
			$code.='<fb:editor-cancel value="Cancel" href="'.URL_CANVAS.'?p=home" />';
		}
		$code.='</fb:editor-buttonset>';
		$code.='</fb:editor>';	    	   		
		// Manage scripts	
		$code .= '<script type="text/javascript">';
		$code .= 'var url = document.getElementById("url");';
		$code .= 'url.addEventListener("blur", loadBlogData, false);';
		$code .= 'var title = document.getElementById("title");';
		$code .= 'title.addEventListener("blur", updateTitle, false);';
		$code .= 'var caption = document.getElementById("caption");';
		$code .= 'caption.addEventListener("blur", updateCaption, false);';
		$code .= 'var entry= document.getElementById("entry");';
		$code .= 'title.addEventListener("blur", updateBlogEntry, false);';		
		$code .= '</script>';
		return $code;		
	}
	
	function buildPostStoryForm($fData=NULL) {
		if (is_null($fData)) {
			// initialize form data
			$fData=new stdClass;
			$fData->state='new';
			$fData->body='';
			$fData->tags='';
			$fData->imageUrl='';
			$fData->videoEmbed='';
			$fData->mediatype='text';			
			if (isset($_GET['u'])) {
				$fData->isBookmarklet=true; 
				$fData->url  = urldecode($_GET['u']);
				isset($_GET['t'])  ? ($fData->title   = strip_tags(stripslashes(urldecode( $_GET['t'])))): $fData->title='';
				isset($_GET['c']) ? ($fData->caption = strip_tags(stripslashes(urldecode( $_GET['c'] )))): $fData->caption = '';
				//isset($_GET['t'])  ? ($fData->title = urldecode($_GET['t'])): $fData->title='';
				//isset($_GET['c']) ? ($fData->caption = urldecode($_GET['c'])): $fData->caption = '';
			} else {
				$fData->isBookmarklet=false;
				$fData->url= '';
				$fData->title='';
				$fData->caption='';					
			}
			$fData->isFeatureCandidate=0;
			$fData->showPreview=false;
			$fData->alert='';
			$this->fData=&$fData;
		} else {
			// on submit, remove second column
			$this->postTip=false;
		}		
		// bookmarklet javascript has to be embedded in an iframe
		// bookmarklet code is in templates directory for now
		// to do: allow user to hide this in the future
		$code.=$this->buildScriptInclude('top');
		// not embed style below is req to override fb editor style
		$code.='<h1>Submit a story from another news site</h1><h5>Add links to '.strtolower(SITE_TOPIC).' related stories you\'ve found from around the Web</h5>';
		$code.='<fb:editor action="?p=postStory&o=link&submit'.($fData->isBookmarklet?'&popup':'').'" labelwidth="100">
		<fb:editor-custom label="Story Web Address (required)"><input style="width:80%;" type="text" name="url" id="url" value="'.$this->fData->url.'">&nbsp;<a href="#" onclick="loadStoryData();return false;">refresh</a></fb:editor-custom>'.
	   '<fb:editor-text label="Story Headline (required)" name="title" id="title" value="'.stripslashes(htmlentities(strip_tags($this->fData->title), ENT_QUOTES)).'"/>
	   		<fb:editor-custom label="Summary (required)"><textarea rows="7" name="caption" id="caption">'.$this->fData->caption.'</textarea></fb:editor-custom>';
	   		$code.='<fb:editor-custom><p><strong>Optional elements</strong></p></fb:editor-custom><fb:editor-divider />'. 	   
	    '<fb:editor-text label="Photo Web Address" id="imageUrl" name="imageUrl" value=""/>';
		$code.=$this->buildImageSelector();	
	   	$code.='<fb:editor-custom label="Video"><h3>Please paste the URL or embed code for a Facebook or YouTube Video. No other services are currently supported.</h3><input type="text" name="videoEmbed" id="videoEmbed" onChange="videoURLChanged();return false;" value="'.$this->fData->videoEmbed.'"><div id="videoPreview" class="hidden"><div id="videoPreviewMsg">Video Preview</div></div></fb:editor-custom>'; 	
		if ($this->session->u->isAdmin) {
		   	$code.='<fb:editor-custom label="Feature Candidate?"><input type="checkbox" name="isFeatureCandidate" '.($this->fData->isFeatureCandidate==1?'CHECKED':'').'></fb:editor-custom>'; 	
		} else {
			$code.='<input type="hidden" name="isFeatureCandidate" value="off">';
		}
		// Button area
		$code.='<fb:editor-buttonset>  ' .
//				'<fb:editor-button name="preview" value="Preview"/>'.
	           '<fb:editor-button name="submit" value="Submit"/>';
		// <fb:editor-text label="Tags" name="tags" value=""/>
		if (!$this->fData->isBookmarklet) {
			$code.='<fb:editor-cancel href="'.URL_CANVAS.'"/>';
		} else {
			// to do: link that javascript close window
			$code.='<fb:editor-cancel value="Cancel" href="'.URL_CANVAS.'?p=home" />';
		}
		$code.='</fb:editor-buttonset>';
		$code.='</fb:editor>';
		// Manage scripts	
		$code .= '<script type="text/javascript">';
		$code .= 'var url = document.getElementById("url");';
		$code .= 'url.addEventListener("blur", loadStoryData, false);';
		$code .= 'var title = document.getElementById("title");';
		$code .= 'title.addEventListener("blur", updateTitle, false);';
		$code .= 'var caption = document.getElementById("caption");';
		$code .= 'caption.addEventListener("blur", updateCaption, false);';
		if ($fData->isBookmarklet AND $fData->url<>'') {
			// fbjs onload requires array push
			$code.='var onload = [];onload.push(function() {loadStoryData();});for(var a = 0;a < onload.length;a++) {onload[a]();}';
		}
		$code .= '</script>';
		return $code;		
	}
	
	function buildScriptInclude($mode='top') {
		switch ($mode) {
			default:
				$script='<script src="'.URL_CALLBACK.'?p=cache&type=js&cf=postStory_'.$this->page->fetchPkgVersion('postStory',array(PATH_SCRIPTS.'/loadStory.js'),'js',true).'.js" type="text/javascript" language="javascript" charset="utf-8"></script>';
			break;
			case 'event':
			break;
			case 'auto':
			$script='<script src="'.URL_CALLBACK.'?p=cache&type=js&cf=postStory_'.$this->page->fetchPkgVersion('postStory',array(PATH_SCRIPTS.'/loadStory.js'),'js',true).'.js" type="text/javascript" language="javascript" charset="utf-8"></script>';
			$script.='<script src="'.URL_CALLBACK.'?p=cache&type=js&cf=autoPost_'.$this->page->fetchPkgVersion('autoPost',array(PATH_SCRIPTS.'/autoPost.js'),'js',true).'.js" type="text/javascript" language="javascript" charset="utf-8"></script>';
			break;
		}
		return $script;
	}
	
	function validate($option='link') {
		$this->setupLibraries();
		$fData=new stdClass;
		$fData->result=true;
		$fData->state='validate';
		$fData->url= $_POST['url'];
		$fData->imageUrl= $_POST['imageUrl'];
		$fData->videoEmbed= $_POST['videoEmbed'];
		// Remove microsoft quotes
		$bad = array('`','’','„','‘','’','´');
		$good = array('\'','\'',',','\'','\'','\'');
		$title = str_replace($bad, $good, $_POST['title']);
		$fData->title=mysql_real_escape_string(stripslashes(strip_tags($title)), $this->db->handle);
		$fData->tags=$_POST['tags'];
		$fData->mediatype=$_POST['mediatype'];
		if (isset($_POST['isFeatureCandidate']) AND $_POST['isFeatureCandidate']=='on') {
			$fData->isFeatureCandidate=1;
		} else {
			$fData->isFeatureCandidate=0;
		}
		$fData->isBookmarklet=true; 
		$fData->showPreview=false;
		$fData->alert='';
		
		//$fData->title=mysql_real_escape_string(addslashes(stripslashes(strip_tags($_POST['title']))));
		//$fData->caption=mysql_real_escape_string(stripslashes($_POST['caption']), $this->db->handle);
		
		// begin option specific code and error checking
		switch($option) {
			default:
				$fData->caption=stripslashes(strip_tags($_POST['caption']));					
				if ($fData->url=='' ) {
					$fData->alert='Please provide a Web address (URL) for your story.';
					$fData->result=false;
				}
				if ($fData->caption=='') {
					$fData->alert='Please provide a short caption for your entry.';
					$fData->result=false;
				}
				if (strlen($fData->caption)>MAX_CAPTION_LENGTH) {
					$fData->alert='Please shorten your caption to '.MAX_CAPTION_LENGTH.' characters. Current length: '.strlen($fData->caption);
					$fData->result=false;
				}				
			break;
			case 'blog':
				if (isset($_POST['blogid'])) $fData->blogid=$_POST['blogid'];
				$fData->status='draft';				
				// only allowable html, fbml
				$fData->entry=mysql_real_escape_string(stripslashes(strip_tags($_POST['entry'],'<p><a><i><br><em><strong><img>')),$this->db->handle); // <fb:photo><fb:mp3><fb:swf><fb:flv><fb:silverlight>
				$fData->caption=mysql_real_escape_string(stripslashes(strip_tags($_POST['caption'])),$this->db->handle);
				if ($fData->entry=='' or strlen($fData->entry)<MIN_BLOG_LENGTH) {
					$fData->alert='Please compose a blog post of at least '.MIN_BLOG_LENGTH.' characters (not counting HTML tags). Current length: '.strlen($fData->entry);
					$fData->result=false;
					$lengthError=true;
				} else 
					$lengthError=false;
				if (strlen($fData->entry)>MAX_BLOG_LENGTH) {
					$fData->alert='Please shorten your blog entry to '.MAX_BLOG_LENGTH.' characters. Current length: '.strlen($fData->entry);
					$fData->result=false;
				}				
				if ($fData->caption<>'') {				
					// if it exists already, then check that it meets the minimum length requirements
					if (strlen($fData->caption)>MAX_CAPTION_LENGTH) {						
						$temp=$utilObj->shorten($_POST['caption'],MAX_CAPTION_LENGTH);
						$fData->caption=$temp;
					}
				} else {
					// if it doesn't exist, create
					require_once(PATH_CORE.'/classes/utilities.class.php');
					$utilObj=new utilities($this->db);
					$temp=$utilObj->shorten($fData->entry,CAPTION_LENGTH);
					if (!$lengthError AND strlen($temp)<100) {
						$fData->alert='Please compose a blog entry for a caption of at least '.MIN_BLOG_LENGTH.' characters (not counting HTML tags). Current length: '.strlen($temp);
						$fData->result=false;
					}
					$fData->caption=$temp;
				}			
			break;
		}
		
		// tags 
/*
 		if ($fData->tags=='') {
			$fData->alert='Please provide at least one tag.';
			$fData->result=false;
		}
* 
 */	
 		//title 
		if (strcmp(strtoupper($fData->title),$fData->title)==0) {
			$fData->title=$temp=ucwords(strtolower($fData->title));
			$fData->alert='We\'ve modified your headline so that it\'s not all uppercase. Please check it.';
			$fData->result=false;			
		}
		if ($fData->title=='') {
			$fData->alert='Please provide a short headline for your entry.';
			$fData->result=false;
		}
		if (strlen($fData->title)>$this->titleLimit) {
			$fData->alert='Please shorten your title to '.$this->titleLimit.' characters. Current length: '.strlen($fData->title);
			$fData->result=false;
		}

		/* We might want this later
		if ($fData->imageUrl=='' ) {
			$fData->alert='Please provide a Web address (IMAGE URL) for your story image.';
			$fData->result=false;
		}
		*/

		// url		
		if ($fData->url<>'') {
			$urlParts=parse_url($fData->url);
			// make sure url has http:// or other scheme in front of it
			if ($urlParts['scheme']=='')
				$fData->url='http://'.$fData->url;
			if (($urlParts['path']=='' OR $urlParts['path']=='/') AND $urlParts['query']=='') {
				$fData->alert='You seem to be writing about a Web site, not a particular story on a Web site. Please do not submit links to Web sites. Please only submit stories from Web sites and blogs.';
				$fData->result=false;
			}
			if (preg_match('/^http:\/\/www.facebook.com\/ext\/share.php/', $fData->url)) {
				$fData->alert='You seem to be posting a shared story from facebook. Please go to the actual story page and post again from there.';
				$fData->result=false;
			}			
		}
		
		// Check for rate limits on post story
		if (!($this->session->u->isAdmin || $this->session->u->isModerator || $this->session->u->isSponser)) {
			$resp=$this->logObj->checkLimits($this->session->userid,"(action = 'postStory' OR action = 'postBlog')",'posting');
			if ($resp!==false) {
				$fData->alert=$resp['msg'];
				$fData->result=false;
			}					
		}
		
		// validate the video
		if ($fData->videoEmbed<>'') {
			require_once(PATH_CORE .'/classes/video.class.php');
			$videoURL = videos::getVideoURLFromEmbedCodeOrURL(stripslashes($fData->videoEmbed));			
			if (!videos::validateVideoURL($fData->videoEmbed))
			{
				$fData->alert='Your video URL or embedding code is invalid. We only support Facebook and YouTube videos at this time.';
				$fData->result=false;
			}
		}
		
		$this->fData=&$fData;
		return $fData;
	} 
	
	function buildSubNav($currentSub='link') 
	{
		if ($currentSub=='' OR is_null($currentSub)) $currentSub='link';
		$pages = array();		
		$pages['link'] = 'Post a link to a story';
		$pages['blog']='Compose a blog entry';
		if ($this->session->u->isAdmin) {
			$pages['auto']='Publish from Feeds';
		}
		 
		 $tabs='<div id="subNav" class="tabs clearfix"><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		 $i=0;
		 foreach (array_keys($pages) as $pagename)
		 {		 	
		 	if ($i==0) {
		 		$clsName='class="first"';
		 	} else {
		 		$clsName='';	
		 	}
	 		$tabs.='<li '.$clsName.'><a id="subtab'.$pagename.'" href="?p=postStory&o='.$pagename.'" onclick="setPostTab(\''.$pagename.'\');return false;" '.($currentSub==$pagename?'class="selected"':'').'>'.$pages[$pagename].'</a></li>';	
		 	$i++;
		 }
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
	 	return $tabs;
 	}	

	function buildImageSelector($mode='link') {
		// Code for image selector
		$code = '<fb:editor-custom><div id="story_selector_box" class="attachment_stage" style="display: none;">';
		$code .= '<div id="attachment_stage_area" class="attachment_stage_area">';
		$code .= '<div class="stage_internal share_media">';
		$code .= '<div class="external_stage has_image">';
		$code .= '<div class="thumbnail_viewer" id="thumbnail_viewer993568">';
		$code .= '<div class="thumbnail_stage">';
		$code .= '<h4>Choose a Thumbnail</h4>';
		$code .= '<div class="selector clearfix">';
		$code .= '<div class="arrows clearfix">';
		$code .= '<span class="left"><a href="#" id="left_arrow" class="arrow disabled" onclick="return left_arrow_press();">&nbsp;</a></span><span class="right"><a href="#" id="right_arrow" class="arrow enabled" onclick="return right_arrow_press();">&nbsp;</a></span>';
		$code .= '</div>';
		$code .= '<div class="counter">';
		$code .= '<span id="image_counter">1 of 3</span>';
		$code .= '</div>';
		$code .= '</div> <!-- end selector clearfix -->';
		$code .= '<div id="thumb_container">';
		$code .= '</div> <!-- end thumb_container -->';
		$code .= '<label style="white-space: nowrap;"><input name="no_picture" type="checkbox" onclick="toggleImage();" />No Picture</label>';
		$code .= '</div>';
		$code .= '</div>';
		$code .= '<div class="summary_wrap">';
		$code .= '<h3><span id="story_title">Sample Story Headline</span</h3><span id="story_summary" class="summary">Story summary goes here.</span>';
		$code.='</div><!-- end summary_wrap -->';
		if ($mode=='blog') {
			$code .= '<br /><div class="summary_wrap">';
			$code .= '<span id="blog_preview" class="summary">Body preview goes here.</span>';
			$code.='</div><!-- end summary_wrap -->';
		}
		$code .= '</div>';
		$code .= '</div>';
		$code .= '</div><!-- end attachment_stage_area -->';
		$code .= '</div><!-- end story_selector_box --></fb:editor-custom>';
		return $code;
	}	
	
}

?>