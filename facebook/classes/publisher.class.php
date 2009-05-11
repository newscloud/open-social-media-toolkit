<?php
class publisher
{
	var $session;
	var $db;
	var $app;
	
	var $debug = 0;
	function __construct(&$db, &$session, &$app)
	{
		$this->session=&$session;
		$this->db = &$db;
		$this->facebook = &$session->facebook;
		$this->app = &$app;
	
	}	
	
	function fetch($mode,$fbMode='publisher_getInterface') {
		switch ($fbMode){
			case 'publisher_getInterface':
				$fbml=$this->makePublisherDisplay($mode);
			break;
			
			case 'publisher_getFeedStory':
				$fbml=$this->makePost($mode);
			break;
			
			case 'emailAttach':
				if (!array_key_exists('message_sent', $_POST) || $_POST['message_sent'] < 1){
					$fbml=$this->makePublisherDisplay($mode);
				}else{
					$fbml=$this->makePost($mode);			
				}		
			break;
		}
		return $fbml;
	}
	
	function makePublisherDisplay($mode){
		require_once(PATH_FACEBOOK.'/classes/pages.class.php');
		$pagesObj=new pages($this->app);
		$code.='<style type="text/css">'.htmlentities(file_get_contents(PATH_FACEBOOK_STYLES.'/publisher.css', true)).'</style>';
		$code.=$this->buildFBJS();
		$code.='<div id="publisherContent">'.$this->buildPubTabs().'<div id="publisherList">'.$this->topStories().'</div><!--end "publisherList"--></div><!--end "publisherContent"-->';
		
		switch ($mode){
			case 'wall':
				$fbmlArray=array("content"=>array("fbml"=>$code,
											  "publishEnabled"=>true, 
											  "commentEnabled"=>true ),
							 "method"=>"publisher_getInterface"	
								);
								
				$fbml=json_encode($fbmlArray);
			break;
			
			case 'email':
		   		$code.= '<input type="hidden" name="url" value="'.URL_BASE.'/?p=ajax&m=emailAttach" />';
		    	$fbml=$code;
			break;
			
		}	

		return $fbml;
	}
	
	function makePost($mode){
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($this->db); 
		switch ($mode){
			case 'wall':	
				if ( isset($_GET['self']) ) {
					$idName='tbId_publishSelf';
				}else{
					$idName='tbId_publish';
				}
				require_once(PATH_CORE.'/classes/systemStatus.class.php');
				$ssObj=new systemStatus($this->db);	
				$templateId=$ssObj->getState($idName);	
				
				if (isset($_POST['app_params']['selId']) || $_POST['app_params']['pubType']=='invite'){
					if (isset($_POST['app_params']) && trim($_POST['app_params']['comment_text'])!=''){$msg=$_POST['app_params']['comment_text'];}
					$feedElements=$this->fetchFeedElements($_POST['app_params']['pubType'],$_POST['app_params']['selId'],$_POST['fb_sig_user']);
					$fbmlArray=array("content"=>array("feed"=>array("template_id"=>$templateId,
															"template_data"=>array("title"=>$feedElements['title'],
							  													   "headline"=>$feedElements['title'],
																				   "storyLink"=>$feedElements['storyLink'].'?&referfbid='.$_POST['fb_sig_user'],
																				   "url"=>URL_CANVAS.'/?referfbid='.$_POST['fb_sig_user'],
																				   "story"=>$feedElements['story'].'<br><br>'.$msg,
																				   "pubType"=>$feedElements['pubType'],
																				   "appName"=>SITE_TITLE,
																				   "refId"=>$_POST['fb_sig_user'],
																				   "storyImage"=>$feedElements['image'],
																				   "images"=>array(array('src'=>$feedElements['image'], 'href'=>$feedElements['storyLink'])) 
																				)
															)
											  ),

							 "method"=>"publisher_getFeedStory"	
								);

					$logObj->updateFromPublisher($logObj->serialize(0, $this->fetchUseridFromFbid($_POST['fb_sig_user']), 'publisherPost', $_POST['app_params']['selId']));
			
				}else{	
					$fbmlArray=array("errorCode"=>1,
								"errorTitle"=>'Wait!',
								"errorMessage"=>'You must select a story to post. Post cancelled.'
								);	
				}
				
				$fbml=json_encode($fbmlArray);		
			break;
			
			case 'email':
				if (isset($_POST['selId']) || $_POST['pubType']=='invite'){	//fb_sig_user	
					if 	($_POST['pubType']=='invite'){
						$idForLog=0;
					}else{
						$idForLog=$_POST['selId'];
					}
					$logObj->updateFromPublisher($logObj->serialize(0, $this->fetchUseridFromFbid($_POST['fb_sig_user']), 'messageAttach', $idForLog));									
					
					$feedElements=$this->fetchFeedElements($_POST['pubType'],$_POST['selId'],$_POST['fb_sig_user']);
					$fbml.=$feedElements['story'];
					if ($feedElements['storyLink']!='')
						$fbml.='<br /><a href="'.$feedElements['storyLink'].'?&referfbid='.$_POST['fb_sig_user'].'">Read More</a>';
						
				}else{
					$fbml='No content selected';
				}			
			break;
			
		}
		
		
		return $fbml;
		
	}
	
	function fetchFeedElements($pt='story',$selId=0,$referfbid=0){
		switch ($pt){
			case 'story':
				$postArray=array();
				require_once(PATH_CORE.'/classes/newswire.class.php');
				$nwObj=new newswire($this->db);
				$feedElements=$nwObj->fetchPostedStoryInfo($selId);
			break;
			case 'challenge':
				require_once(PATH_CORE. '/classes/challenges.class.php');
				$challengesObj = new challenges($this->db);		
				$feedElements= $challengesObj->fetchPostedChallengeInfo($selId);	
			break;
			case 'reward':
				require_once(PATH_CORE. '/classes/prizes.class.php');
				$rewardsObj = new rewards($this->db);		
				$feedElements= $rewardsObj->fetchPostedRewardInfo($selId);	
			break;
			case 'invite':
				$feedElements=array();
				$feedElements['title']='Check out '.SITE_TITLE;
				require_once(PATH_FACEBOOK. '/classes/actionTeam.class.php');
				$teamObj = new actionTeam($this->db);	
				if ($referfbid==0){
					$feedElements['story']=$teamObj->fetchProfileSummaryForProfileBox(0);
				}else{
					$feedElements['story']=$teamObj->fetchProfileSummaryForProfileBox(0,URL_CANVAS.'/?referfbid='.$referfbid);
				}
				//$feedElements['story']=$this->fetchInviteText();
				$feedElements['image']='';	
				$feedElements['storyLink']=URL_CANVAS;		
			break;
		}	
		$feedElements['pubType']=$pt;

		
		return $feedElements;	
	}

	function buildFBJS(){
		$js='<fb:js-string var="loading"><div id="loadingStatus"><img src="http://www.newscloud.com/static/facebook/images/loading.gif"><!-- end loading status div --></div></fb:js-string>';
		$js.="<script>\n <!--\n ";
		$js.='function setPublisherTab(newTab){ 
				var pubContent=document.getElementById("publisherList");
				pubContent.setInnerFBML(loading);
				var tabs = ["TopStories","MyStories","Challenges","Rewards","Invite"];
				for (i=0;i<tabs.length;i++){
					var t="tab"+tabs[i];
					var tTab = document.getElementById(t);
					if (newTab==tabs[i]){
						tTab.toggleClassName("selected");
					}else{
						tTab.setClassName("");
					}
				}
				var ajaxUrl="'.URL_BASE.'/index.php"; 
				var ajax = new Ajax(); 
				ajax.requireLogin = true;
				ajax.responseType = Ajax.FBML; 
				ajax.ondone = function(data) { 
									pubContent.setInnerFBML(data);
								  } 
				ajax.post(ajaxUrl+"?p=ajax&m=fetchPublisherPage&tab="+newTab); 
				return false; ' .
			"\n } ";	
		
		$js.="\n //--> \n</script>\n";
		return $js;
	}
		
	function buildPubTabs($current='Top Stories') {
		$tabs='<div class="microTabs clearfix">';
        $tabs.='<a id="tabTopStories" href="#topStories" onclick="setPublisherTab(\'TopStories\');return false;" class="selected">Top Stories</a>';
        $tabs.='<a id="tabMyStories" href="#myStories" onclick="setPublisherTab(\'MyStories\');return false;" class="">My Stories</a>';
        $tabs.='<a id="tabChallenges" 	style="'.(ENABLE_ACTION_CHALLENGES?'':'display:none').'" href="#challenges" onclick="setPublisherTab(\'Challenges\');return false;" class="">Challenges</a>';
        $tabs.='<a id="tabRewards" 		style="'.(ENABLE_ACTION_REWARDS?'':'display:none').'"href="#rewards" onclick="setPublisherTab(\'Rewards\');return false;" class="">Rewards</a>';
      	$tabs.='<a id="tabInvite" href="#invite" onclick="setPublisherTab(\'Invite\');return false;" class="">Invite</a>';
      	$tabs.='</div>';
        return $tabs;

	}	
	
	function fetchPublisherContent($tab){
		switch ($tab){
			case 'TopStories':
				$code=$this->topStories();
			break;
			case 'MyStories':
				$code=$this->myStories();
			break;
			case 'Challenges':
				$code=$this->fetchChallenges();
			break;
			case 'Rewards':
				$code=$this->fetchRewards();
			break;
			case 'Invite';
				$code='<h2>Invite Friends</h2><p class="bump10">Post this message to spread the word</p><div class="embed_message">'.$this->fetchInviteText().'</div>';
				$code.='<input type="hidden" name="pubType" value="invite">';
			break;	
		
		}
		return $code;	
	
	}
	
	function topStories() {
		require_once(PATH_CORE.'/classes/newswire.class.php');
		$nwObj=new newswire($this->db);
		$code.='<h2>Top Stories</h2>'.$nwObj->fetchPublisherStories('all','default',1);	
		$code.='<input type="hidden" name="pubType" value="story">';
		return $code;			
	}
	
	function myStories() {
		require_once(PATH_CORE.'/classes/newswire.class.php');
		$nwObj=new newswire($this->db);
		$code.='<h2>My Stories</h2>'.$nwObj->fetchPublisherStories('all','user', 1);	
		$code.='<input type="hidden" name="pubType" value="story">';
		return $code;			
	}
	
	function fetchChallenges(){
		require_once(PATH_CORE. '/classes/challenges.class.php');
		$challengesObj = new challenges($this->db);		
		$code .= '<h2>Challenges</h2>'.$challengesObj->fetchChallengesForPublisher('pointValue', 1, false);	
		$code.='<input type="hidden" name="pubType" value="challenge">';
		return $code;	
	}
	
	function fetchRewards(){
		require_once(PATH_CORE. '/classes/prizes.class.php');
		$rewards = new rewards($this->db);		
		$code .= '<h2>Rewards</h2>'.$rewards->fetchRewardsForPublisher('pointCost', 1, false);	
		$code.='<input type="hidden" name="pubType" value="reward">';
		return $code;	
	}
	
	function fetchInviteText(){
		$code='<h4>Join the '.SITE_TITLE.' App</h4>
			   <p class="bold">We can do more, and have more fun doing it!</p>
			   <p>Help spread the word, drive the discussion on hot topics, and compete for earth-happy prizes along the way.</p>
			   <p><a href="'.URL_CANVAS.'">Go to the '.SITE_TITLE.' application</a></p>';		
		return $code;		
	}
	
	function fetchUseridFromFbid($fbId){
		$query=$this->db->query("select userid from UserInfo where fbId=".$fbId." LIMIT 1");
		while ($u=$this->db->readQ($query)) {	
			return $u->userid;
		}
	}
	
}
?>