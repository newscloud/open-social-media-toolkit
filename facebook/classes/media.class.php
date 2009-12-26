<?php
/*
 * Media page support class
 */

class media
{
	var $db;	
	var $comObj;
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
		require_once(PATH_FACEBOOK.'/classes/common.class.php');
		$this->comObj=new common();
		if (is_null($this->templateObj)) 
		{
			require_once(PATH_CORE.'/classes/template.class.php');
			$this->templateObj=new template($this->db);
		}
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'media');
		$this->initialized = true;
	}
	
	function buildSidebar() {
		$code='';
		$cacheName='sideLeaders';
		if ($this->templateObj->checkCache($cacheName,700)) {
			$temp=$this->templateObj->fetchCache($cacheName);
			$code.=$temp;
		}
		return $code;	
	}
	
	function buildProfile() {
		$error=false;
		$this->facebook=$this->session->app->loadFacebookLibrary();
		try {
			$resp = $this->facebook->api_client->users_getInfo($this->session->fbId, 'pic,pic_big,pic_small,pic_square');
		} catch (Exception $e) {
			$this->db->log($e);
			$error=true;
		}			
		include PATH_TEMPLATES.'/media.php';
		$profileUrl=$this->getLargestProfilePic($resp[0]);
		$proImage=imagecreatefromjpeg($profileUrl);
		$proX=imagesx($proImage);
		$proY=imagesY($proImage);
		$code.=$this->templateObj->templates['mediaProfileIntro'];
		$code.='<div id="mediaPro_left">';
		$code.=$this->buildProfilePreview();
		$code.='<!-- end leftside --></div>';		
		$code.='<div id="mediaPro_right">';
		if (count($imgList)>1) 
			$code.='<h3>Choose an image for your profile picture:</h3>';
		$code.='<div class="mediaGrid"><input type="hidden" value="'.count($imgList).'" id="numProfileImages" />';
		$i=0;
		foreach ($imgList as $thumb) {
			$code.='<div class="thumb"><img id="proImage_'.$i.'" '.($i==0?'class="selected"':'').' src="'.$imgList[$i].'" alt="thumbnail of overlay" onclick="changeProfileImage('.$i.');"></div>';			
			$i+=1;
		}
		$code.='<!-- end gridBlock of images --></div><br />';
		$code.=$this->ajaxBuildProfileForm(0,$profileUrl);
		$code.='<!-- end rightside --></div>';
		// URL_CALLBACK
		// build left side
		$code.='<br clear="all" />'.$this->fetchLinkProfileBox($imgLinkTitle,$imgLinkCaption,$imgList);
		$code.='<fb:js-string var="mediaAuthMsg">Please <a href="?p=media&o=pro" requirelogin="1">authorize '.SITE_TITLE.'</a> with Facebook so you we can access your profile photo.</fb:js-string>';
		return $code;
	}
	
	function ajaxMediaProfileUpload($tempName='') {
		$this->facebook=$this->app->loadFacebookLibrary();
		try {
			$resp = $this->facebook->api_client->photos_createAlbum('Photo album for '.SITE_TITLE,'Album used by the '.SITE_TITLE.', customize your profile image here: '.URL_CANVAS);
		} catch (Exception $e) {
			$this->db->log($e);
		}
		$albumid=$resp['aid'];
		try {
			$resp = $this->facebook->api_client->photos_upload(PATH_UPLOAD_IMAGES.$tempName,$albumid,'This profile photo customized via '.SITE_TITLE.' at '.URL_CANVAS.'?p=media&o=pro');
		} catch (Exception $e) {
			$this->db->log($e);
		}
		$link=$resp['link'];
		$code='<h2>Your new profile photo has been uploaded</h2><p>To make this your profile photo, <a target="_blank" href="'.$link.'">visit this page</a> and click <strong>Make Profile Picture</strong> as shown in the image below:</p><p><a target="_blank" href="'.$link.'"><img src="'.URL_CALLBACK.'?p=cache&img=helperUploadPhoto.jpg" alt="tip" /></a></p>';
		return $code;
	}
	
	function ajaxRefreshPreview($userid=0,$imageIndex=0,$alpha=0,$location='replace') {
		$code='';
		if (isset($_POST['profileImageUrl']) AND $_POST['profileImageUrl']<>'') {
			$profileImageUrl=$_POST['profileImageUrl'];
			// create new image
			include PATH_TEMPLATES.'/media.php';
			$overlayUrl=$imgList[$imageIndex];
			if ($location=='default') $location=$imgOptions[0];			
			$newImageUrl=$this->buildOverlay($userid,$profileImageUrl,$overlayUrl,$alpha,$location);	
			$code='<img src="'.URL_BASE.'/uploads/images/'.$newImageUrl.'" alt="customized profile image" />';
		}
		$data=new stdClass;
		$data->imgUrl=$code;
		$data->fileName=$newImageUrl;
		$jdata=json_encode($data);	
		return $jdata;
	}
	
	function buildProfilePreview() {
		// left side of profile preview
		if ($this->session->isLoggedIn) {
			$img='<fb:profile-pic uid="loggedinuser" size="n" linked="false">';
			$msg='';
		} else {
			$img='<img src="'.URL_CALLBACK.'?p=cache&img=anonymous_user.jpg" alt="anonymous user" />';			
			$msg='<p>Please log in to Facebook and <a href="'.URL_CANVAS.'?p=media&o=pro" requirelogin="1">authorize '.SITE_TITLE.'</a> so we can access your profile photo.</p>';
		}
		$img='<span id="previewImage">'.$img.'</span>';
		$code=$img;
		$code.=$msg;
		return $code;
	}
	
	function ajaxBuildProfileForm($imgIndex=0,$profileImageUrl='',$isAjax=false) {
		include PATH_TEMPLATES.'/media.php';
		$inside='';
		$inside.='<h3>How do you want to use or place the image?</h3>';
		$inside.='<select id="location" onchange="refreshProfilePreview();return false;">';
		$inside.='<option value="default" SELECTED>Please select an option below</option>';
		if (array_search('blend',$imgOptions[$imgIndex])!==false)
			$inside.='<option value="blend">Blend into your profile picture</option>';
		if (array_search('top',$imgOptions[$imgIndex])!==false)
			$inside.='<option value="top">Top banner</option>';
		if (array_search('bottom',$imgOptions[$imgIndex])!==false)
			$inside.='<option value="bottom">Bottom banner</option>';
		if (array_search('corners',$imgOptions[$imgIndex])!==false) {
			$inside.='<option value="bottomright">Bottom right corner</option>';			
			$inside.='<option value="bottomleft">Bottom left corner</option>';
			$inside.='<option value="topright">Top right corner</option>';
			$inside.='<option value="topleft">Top left corner</option>';
		}
		if (array_search('replace',$imgOptions[$imgIndex])!==false)
			$inside.='<option value="replace">Replace your profile picture</option>';
		$inside.='</select><br />';
		$inside.='<br /><h3>Would you like to adjust the transparency of the blended image? (optional)</h3>';
		$inside.='<select id="alpha" onchange="refreshProfilePreview();return false;">';
		// these are backwards due to wording of transparency question
		$inside.='<option value="100">No transparency</option>';
		$inside.='<option value="75">Slighly faded</option>';
		$inside.='<option value="50">More faded</option>';
		$inside.='<option value="25">Barely visible</option>';
		$inside.='</select><br />';
		if ($isAjax) return $inside;
		$code='<form requirelogin="1" ><br clear="left "/><br /><div id="mediaFormOptions">';
		$code.=$inside;
		// image option selector
		$code.='<!-- end mediaFormOptions --></div>';
		$code.='<input type="hidden" id="imageIndex" value="'.$imgIndex.'" />';
		$code.='<input type="hidden" id="previewImageFileName" value="" />';
		$code.='<input type="hidden" id="profileImageUrl" value="'.$profileImageUrl.'" />';
		$code.='<br /><input id="uploadButtonItself" type="button" class="btn_1" value="Upload your new image" onclick="uploadProfilePhoto();return false;"><span id="uploadButton"></span>';
		$code.='</form><!-- end profile build form -->';
		return $code;
	}
	
	function buildMediaView($id=0,$media='image') {
		$code='';
		require_once(PATH_CORE.'/classes/feed.class.php');
		if ($id==0 OR $id=='') {
			$q=$this->db->queryC("SELECT id FROM FeedMedia ORDER BY id DESC LIMIT 1;");	
			if (!$q) {
				$code.='<p>Invalid media request. Please try again or return to the <a href="?p=home">home page</a>.</p>';					
				return $code;
			} else {
				$d=$this->db->readQ($q);
				$id=$d->id;				
			}			
		}
		$mTable = new FeedMediaTable($this->db); 
		$m = $mTable->getRowObject();		
		$m->load($id);
		$code.=$this->buildMediaSlider();
		$code.='<h1>'.$m->title.'</h1>';
		$code.='<a href="'.$m->linkUrl.'" target="_blank"><img style="border:1px;max-width:480px;height:auto;margin:10px;" src="'.$m->imageUrl.'"></a>';
/*		$code.='<div id="ideaShare" class="'.($showShare?'':'hidden').'">';
		$temp='<form requirelogin="1" id="idea_share_form" action="?p=ideas&o=view&id='.$id.'" method="post"><p>To:<br /> <fb:multi-friend-input max="20" /></p><p class="bump10"><input class="btn_1" type="button" value="Send now" onclick="ideaShareSubmit('.$id.');return false;"></p></form>';		
		$temp ='<div class="panelBar clearfix"><h2>Share this idea with your friends</h2></div><br />' . $temp;
		$temp = '<div class="panel_2 clearfix">'. $temp . '</div>';
		$code.=$temp.'</div><br />';*/
		// display the comments to this media image
		$comTemp='<div id="mediaComments" >';
		$comTemp.=$this->buildCommentThread($id,false);
		$comTemp.='</div><br />';
		$code.= '<div class="panel_2 clearfix"><div class="panelBar clearfix"><h2>Comments</h2><!-- end panelBar--></div><br />'.$comTemp.'<!-- end panel_2 --></div>';
		// display the link to this idea box		
		$code.=$this->fetchLinkBox($m);
		return $code;
	}
	
	// ajax function
	function buildMediaSlider($panelNumber=1,$isAjax=false,$limit=7) {
		$inside='';
		$q=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM FeedMedia WHERE previewImageUrl<>'' AND t>date_sub(NOW(),INTERVAL ".(defined('MEDIA_INTERVAL')?MEDIA_INTERVAL:"2")." DAY) ORDER BY t DESC LIMIT ".(($panelNumber-1)*$limit).",$limit;");
		$rowTotal=$this->db->countFoundRows();
		if ($rowTotal>0) {
			while ($data=$this->db->readQ($q)) {
				$inside.='<a href="?p=media&media=image&id='.$data->id.'"><img title="'.$data->title.' by '.$data->author.'"   alt="'.$data->title.' by '.$data->author.'"  src="'.$data->previewImageUrl.'"></a>';
			 }
		}
		if ($isAjax) return $inside;
		$temp='<div class="imageStrip" ><div id="imageStripPanel" class="imageStripPanel">';
		$temp.=$inside;
		$temp.='<input id="panelNumber" type="hidden" value="'.$panelNumber.'" />';
		$temp.='</div><!-- end imageStripPanel --></div><!-- end imageStrip --><br clear="both" />';
		$code='<div class="panel_1">';
		$pageNav='Pages ';
		for ($i=1;$i<=min(floor($rowTotal/7),7);$i++) {
			$pageNav.='<span class="pipe">|</span> <a href="#" onclick="slideMediaPanel('.$i.');return false;">'.$i.'</a> ';
		}
		$code.=$this->comObj->buildPanelBar('Recent Photos',$pageNav,'Images related to '.SITE_TOPIC); 
		$code.=$temp;
		$code.='</div><!--end "panel_1"-->';						
		return $code;
	}
	
	function buildCommentThread($id=0,$isAjax=false) {
		$code='';
		$code.='<fb:comments xid="'.CACHE_PREFIX.'_medcom_'.$id.'" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=mediaRefreshComments&id='.$id.'" />';	
		$this->db->log($code);	
		if (!$isAjax) {
 			$code='<div id="commentList">'.$code.'</div>';
		}
		return $code;
	}
	
	function getLargestProfilePic($matrix=null) {
		if (isset($matrix['pic_big']) AND $matrix['pic_big']<>'')
			$url=$matrix['pic_big'];
		else if (isset($matrix['pic']) AND $matrix['pic']<>'')
			$url=$matrix['pic'];
		else if (isset($matrix['pic_small']) AND $matrix['pic_small']<>'')
			$url=$matrix['pic_small'];
		else if (isset($matrix['pic_square']) AND $matrix['pic_square']<>'')
			$url=$matrix['pic_square'];
		else
			$url=URL_CALLBACK.'?p=cache&img=anonymous_user.jpg';
		return $url;
	}

	// helper functions
	function fetchLinkProfileBox($imgLinkTitle,$imgLinkCaption,$imgList) {
		//global $imgLinkTitle,$imgLinkCaption,$imgList;
 		$mediaLink=URL_CANVAS.'?p=media&o=pro';
		$title=$this->templateObj->templates['mediaProfileShareTitle'];
		$caption=$this->templateObj->templates['mediaProfileShareCaption'];
		$metaImg='<link rel="image_src" href="'.$imgList[0].'"/>';
		$tweetStr=$this->templateObj->ellipsis($title,80).' '.URL_CANVAS.'?p=media&o=pro '.(defined('TWITTER_HASH')?TWITTER_HASH:'');
		return $this->finalLinkBox($title,$caption,$metaImg,$mediaLink,$tweetStr);
 	}

	function fetchLinkBox($m=null) {
 		$mediaLink=URL_CANVAS.'?p=media&o=view&media=image&id='.$m->id;
		$title=htmlentities($this->templateObj->ellipsis($m->title),ENT_QUOTES);
		//$caption=htmlentities($this->templateObj->ellipsis($m->caption,350),ENT_QUOTES);
		$caption='Another fun image via '.SITE_TITLE;
		$metaImg='<link rel="image_src" href="'.$m->previewImageUrl.'"/>';
		$tweetStr='Cool photo: '.$this->templateObj->ellipsis($m->title,80).' '.URL_CANVAS.'?p=media&o=view&media=image&id='.$m->id.' '.(defined('TWITTER_HASH')?TWITTER_HASH:'');
		return $this->finalLinkBox($title,$caption,$metaImg,$mediaLink,$tweetStr);
 	}

	function finalLinkBox($title='',$caption='',$metaImg='',$mediaLink='',$tweetStr='') {
		// to do - move to common
		$tweetThis='<a class="tweetButton" href="http://twitter.com/?status='.rawurlencode($tweetStr).'" target="_blank"><img src="'.URL_CALLBACK.'?p=cache&img=tweet_button.gif" alt="tweet this" /></a>';		
		$shareButton='<div style="float:left;padding:0px 5px 0px 0px;display:inline;"><fb:share-button class="meta"><meta name="title" content="'.$title.'"/><meta name="description" content="'.$caption.'" />'.$metaImg.'<link rel="target_url" href="'.$mediaLink.'"/></fb:share-button><!-- end share button wrap --></div>';
 		$code = '<div  id="actionLegend">'.$shareButton.'<p class="bold">'.$tweetThis.' Link to this page</p>';
          $code.= '<div class="pointsTable"><table cellspacing="0"><tbody>'.
				'<tr><td><input class="inputLinkNoBorder" type="text" value="'.$mediaLink.'" onfocus="this.select();" /></td></tr>'.
				'</tbody></table></div><!-- end points Table --></div><!-- end idea link box -->';
 		return $code;			
	}


	function buildOverlay($userid=0,$profileUrl='',$overlayUrl='',$alpha=0,$location='replace') {
		define ("SCALE_RATIO",.4);
		$overImage=imagecreatefromjpeg($overlayUrl);
		$dx=imagesx($overImage);
		$dy=imagesy($overImage);
		if ($location=='replace') {
			$srcImage = imagecreatetruecolor($dx, $dy);
			$white = imagecolorallocate($srcImage, 255, 255, 255);
			imagefill($srcImage, 0, 0, $white);			
			imagecopymerge($srcImage, $overImage, 0, 0, 0, 0, $dx, $dy, $alpha);								
		} else {
			$srcImage=imagecreatefromjpeg($profileUrl);
			$sx=imagesx($srcImage);
			$sy=imagesy($srcImage);
			if ($location=='top') {
				$scaleX=$sx;
				$scaleY=ceil(($scaleX/$dx)*$dy);
				$smallOverImage = imagecreatetruecolor($scaleX,$scaleY);
				imagecopyresized($smallOverImage, $overImage, 0, 0, 0, 0, $scaleX,$scaleY, $dx, $dy);
				$posX=0;				
				$posY=0;
				imagecopymerge($srcImage, $smallOverImage, $posX, $posY, 0, 0, $scaleX,$scaleY, 100);
			} else if ($location=='bottom') {
				$scaleX=$sx;
				$scaleY=ceil(($scaleX/$dx)*$dy);
				$smallOverImage = imagecreatetruecolor($scaleX,$scaleY);
				imagecopyresized($smallOverImage, $overImage, 0, 0, 0, 0, $scaleX,$scaleY, $dx, $dy);
				$posX=0;				
				$posY=$sy-$scaleY;
				imagecopymerge($srcImage, $smallOverImage, $posX, $posY, 0, 0, $scaleX,$scaleY, 100);
			} else {
				if ($location=='blend') {
					$scaleX=$sx; // scale to same image size
					$scaleY=$sy;					
				} else if ($sx<$sy) { // MUST BE a corner type
					// profile image wider than tall - portrait
					$scaleX=ceil($sx*SCALE_RATIO);
					$scaleY=ceil(($scaleX/$dx)*$dy);
				} else { // MUST BE a corner type
					// profile image landscape
					$scaleY=ceil($sy*SCALE_RATIO);
					$scaleX=ceil(($scaleY/$dy)*$dx);					
				}				
				$smallOverImage = imagecreatetruecolor($scaleX,$scaleY);
				imagealphablending($smallOverImage, true); 
				imagecopyresampled($smallOverImage, $overImage, 0, 0, 0, 0, $scaleX,$scaleY, $dx, $dy);
				switch ($location) {
					case 'blend':
					case 'topleft':
						$posX=0;
						$posY=0;
					break;
					case 'topright':
						$posX=$sx-$scaleX;
						$posY=0;
					break;
					case 'bottomleft':
						$posX=0;				
						$posY=$sy-$scaleY;
					break;
					case 'bottomright':
						$posX=$sx-$scaleX;
						$posY=$sy-$scaleY;
					break;
				}			
				imagecopymerge($srcImage, $smallOverImage, $posX, $posY, 0, 0, $scaleX,$scaleY, $alpha);
				imagealphablending($srcImage, true); 
			}			
		}
		foreach (glob(PATH_UPLOAD_IMAGES.'~mp_'.$userid."_*") as $filename) {
			if (file_exists($filename)) {
				unlink($filename);
			}
		}
		$tempName='~mp_'.$userid.'_'.hash ('md5',strval(time()).'mp'.SITE_TITLE).'.jpg';
		imagejpeg($srcImage, PATH_UPLOAD_IMAGES.$tempName);
		return $tempName;		
	}
	


	// template callback functions

	function buildScript(){
		$js="<script><!-- ";
		$js.="";
		$js.=" //--> </script>";
		return $js;
/*		$js.="\nfunction readMore(stage,sLink,hLink) {\n Animation(document.getElementById(stage)).to('height', 'auto').from('0px').to('width', 'auto').from('0px').to('opacity', 1).from(0).blind().show().go(); document.getElementById(sLink).setStyle('display', 'none'); document.getElementById(hLink).setStyle('display', 'inline');  } ";
		$js.="\n\nfunction seeLess(stage,sLink,hLink) {\n Animation(document.getElementById(stage)).to('height', '0px').to('width', '0px').to('opacity', 0).hide().go(); document.getElementById(hLink).setStyle('display', 'none'); document.getElementById(sLink).setStyle('display', 'inline');  } ";
		$js.="\nfunction changeCard(id) {\n var fb='card'+id; document.getElementById('selCardStage').setInnerFBML(details[fb]); document.getElementById('pickCard').setValue(id);  \n} ";
		$js.="\nfunction selectForAttach(id) {\n var fb='card'+id; document.getElementById('selCardStage').setInnerFBML(details[fb]); document.getElementById('id').setValue(id);  \n} ";		
*/	}

	// ajax functions
	// to do - common comment function
	// common like function
	
}
?>