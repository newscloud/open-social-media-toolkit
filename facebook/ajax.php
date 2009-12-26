<?php
	/* Process incoming variable requests */		
	if (isset($_GET['m'])) {
		$method=$_GET['m'];
	} else 
		$method='home';
	if (isset($_GET['currentPage']))
		$currentPage=$_GET['currentPage'];
	else
		$currentPage=1;
	if ($method<>'switchPage' AND ((isset($_GET['id']) AND !is_numeric($_GET['id'])))) { exit; }
	/* begin building ajax response */
	$sessionNotReqMethods=array('appTab','wall','fetchVideoPreview','fetchChallenges', 'fetchFeed', 'fetchLeaders','fetchFeedPage', 'wallPublisher','fetchPublisherPage','emailAttach','showSponsor', 'parseStory','fetchDynamicDialog','askRelated','askRefreshAnswers','ideaRelated','stuffRelated','searchAws','stuffCopyItem','stuffCopyAwsItem','slideMediaPanel','microFetchBrowse'); 		 
	$sessionOptionalMethods=array('common','shareStory','shareStorySubmit','switchPage','switchTeamTab','fetchNewswire','fetchNewswireWrap','fetchNewswirePage','askFetchBrowseQuestions','ideaFetchBrowse','stuffRecordLike','ideaRecordLike','askRecordLike','stuffRefreshComments','stuffRefreshSearch','ideasRefreshComments','askRefreshAnswerComments','ideaShareSubmit','askShareSubmit');
	// NOTE: AJAX methods that do not require a session must be added to the above array
	if (array_search($method,$sessionNotReqMethods)!==false) {
		// session not required 
		switch ($method) {
			case 'slideMediaPanel':
				if (isset($_GET['pg'])) {
					$pg=$_GET['pg'];
					$app=setupAppFramework();
					require_once(PATH_FACEBOOK."/classes/media.class.php");
					$mObj=new media($db);
					$code=$mObj->buildMediaSlider($pg,true);
				}			
			break;
			case 'microFetchBrowse': // microBlogs
				if (isset($_GET['tag'])) {
					$tag=$_GET['tag'];
					$page=$_GET['page'];
					$app=setupAppFramework();
					require_once(PATH_FACEBOOK."/classes/micro.class.php");
					$mObj=new micro($db);
					$code=$mObj->listMessages('recent',$tag,0,0,99);
				}
			break;
			case 'stuffCopyAwsItem':
				if (isset($_POST['asin'])) {
					$asin=$_POST['asin'];
					$app=setupAppFramework();
					require_once(PATH_FACEBOOK."/classes/stuff.class.php");
					$stuffObj=new stuff($db);
					$code=$stuffObj->ajaxGetAwsItem($asin);
				}			
			break;
			case 'stuffCopyItem':
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
					$app=setupAppFramework();
					require_once(PATH_FACEBOOK."/classes/stuff.class.php");
					$stuffObj=new stuff($db);
					$code=$stuffObj->ajaxGetItem($id);
				}			
			break;
			case 'searchAws':
			// find stuff at Aws
			$app=setupAppFramework();
			require_once(PATH_FACEBOOK."/classes/stuff.class.php");
			$stuffObj=new stuff($db);
			if (isset($_POST['keyword'])) 
				$keyword=$_POST['keyword'];
		 	else
		 		$keyword='';
			$code=$stuffObj->ajaxSearchAws($keyword);
			break;
			case 'askRefreshAnswers':
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
				} else {
					$id=0;						
					$error=true;
					$errorMsg='Invalid question id';
				}
				require_once(PATH_FACEBOOK."/classes/ask.class.php");
				$askObj=new ask();
				$code=$askObj->ajaxAskRefreshAnswers($id);								
			break;
			case 'stuffRelated':
				// find related questions
				$app=setupAppFramework();
				require_once(PATH_FACEBOOK."/classes/stuff.class.php");
				$stuffObj=new stuff($db);
				$qStr=$_POST['qStr'];
				$code=$stuffObj->findRelatedStuff($qStr);
			break;
			case 'askRelated':
				// find related questions
				$app=setupAppFramework();
				require_once(PATH_FACEBOOK."/classes/ask.class.php");
				$askObj=new ask($db);
				$qStr=$_POST['qStr'];
				$code=$askObj->findRelatedQuestions($qStr);
			break;
			case 'ideaRelated':
				// find related questions
				$app=setupAppFramework();
				require_once(PATH_FACEBOOK."/classes/ideas.class.php");
				$iObj=new ideas($db);
				$qStr=$_POST['qStr'];
				$code=$iObj->findRelatedIdeas($qStr);
			break;
			case 'wall':
				$topic=requestInt('topic');				
				if ($topic==0)
					$code='<fb:comments xid="'.CACHE_PREFIX.'_wall" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=wall"></fb:comments>';
				else {
					$app=setupAppFramework();
					$code='<fb:comments xid="'.CACHE_PREFIX.'_wall_'.$topic.'" canpost="true" candelete="true" numposts="25" callbackurl="'.URL_CALLBACK.'?p=ajax&m=wall&topic='.$topic.'"></fb:comments>';
					$db->update("ForumTopics","lastChanged=NOW(),numPostsToday=numPostsToday+1","id=$topic");							
				}
			break;
			case 'fetchDynamicDialog':
				require_once(PATH_CORE ."/classes/template.class.php");
				$tObj = new template();
				$tObj->registerTemplates(MODULE_ACTIVE,$_GET['t']);
				$code=$tObj->templates[$_GET['d']];			
			break;
			case 'fetchChallenges':
			 	if (isset($_GET['sort']))
					$sort=$_GET['sort'];
				else
					$sort='default';
				require_once(PATH_CORE ."/classes/challenges.class.php");
				$chObj = new challenges();
				$code = $chObj->fetchChallenges($sort, $currentPage, true);
				
			break;
			case 'fetchLeaders':
			 	if (isset($_GET['view']))
					$view=$_GET['view'];
				else
					$view='alltime';	
			 	if (isset($_GET['filter']))
					$filter=$_GET['filter'];
				else
					$filter='inside';	
				require_once(PATH_FACEBOOK.'/classes/pages.class.php');
				$pagesObj=new pages($app,0,false);				
	            require_once(PATH_FACEBOOK.'/pages/pageLeaders.class.php');
	            $leadersObj=new pageLeaders($pagesObj);
	            $code= $leadersObj->fetchLeaders($view,$filter,true);           
			break;			
			case 'fetchVideoPreview':
				require_once(PATH_CORE .'/classes/video.class.php');
			 	if (isset($_POST['videoURL']))
					$videoURL = videos::getVideoURLFromEmbedCodeOrURL(stripslashes($_POST['videoURL']));
				$code =  videos::buildPlayerFromLink($videoURL, 160, 120);				
			break;
			case 'fetchFeed':
			 	if (isset($_GET['filter']))
					$filter=$_GET['filter'];
				else
					$filter='all';		
			 	if (isset($_GET['filter_userid']))
					$filter_userid=$_GET['filter_userid'];
				else
					$filter_userid=0;		
				if (isset($_GET['filter_challengeid']))
					$filter_challengeid=$_GET['filter_challengeid'];
				else
					$filter_challengeid=0;		
					
				require_once(PATH_FACEBOOK ."/classes/actionFeed.class.php");
				$feedObj = new actionFeed();
				$code = $feedObj->fetchFeed($filter, $currentPage, $filter_userid,$filter_challengeid, true);
				
			break;
		/*	case 'fetchFeedPage':
			if (isset($_GET['filter']))
					$filter=$_GET['filter'];
				else
					$filter='all';		
 				if (isset($_GET['filter_userid']))
					$filter_userid=$_GET['filter_userid'];
				else
					$filter_userid=0;		
		
					
				require_once(PATH_FACEBOOK ."/classes/actionFeed.class.php");
				$feedObj = new actionFeed();
				$code = $feedObj->fetchFeedPage($filter, $currentPage, $filter_userid);
				$code .= 'feed page here';
				
			break;
			*/
			case 'wallPublisher':
				setupAppFramework();
				require_once(PATH_FACEBOOK.'/classes/publisher.class.php');
				//$pubObj=new publisher($db,$session);
				$pubObj=new publisher($db,$session,&$app);		
				if (isset($_POST['method'])){$fbMethod=$_POST['method'];}else{$fbMethod='';}	
				$code=$pubObj->fetch('wall',$fbMethod);
			break;
			case 'fetchPublisherPage':
				setupAppFramework();
				require_once(PATH_FACEBOOK.'/classes/publisher.class.php');
				//$pubObj=new publisher($db,$session);
				$pubObj=new publisher($db,$session,&$app);	
				$code=$pubObj->fetchPublisherContent($_GET['tab']);
			break;
			case 'parseStory':
				if (isset($_POST['url']) && $_POST['url'] != '')
					$url = $_POST['url'];
				else
					return false;
				require_once(PATH_CORE.'/classes/parseStory.class.php');
				$psObj = new parseStory($url);
				$code = $psObj->parse();
			break;
				
			case 'emailAttach':
				$app=setupAppFramework();
				require_once(PATH_FACEBOOK.'/classes/publisher.class.php');
				$pubObj=new publisher($db,$session,&$app);	
				$code=$pubObj->fetch('email','emailAttach');
			break;
			case 'appTab':
				if (isset($_POST['fb_sig_profile_user']))
					$fbUserPageId = $_POST['fb_sig_profile_user'];
				else
					exit('<h2>Invalid facebook user</h2>');
				$app=setupAppFramework();
				// TO DO: might later move this out of AJAX
				require_once(PATH_FACEBOOK.'/classes/pages.class.php');
				$pagesObj=new pages($app,0,false);
	 			require_once(PATH_FACEBOOK.'/pages/pageAppTab.class.php');
				$tabObj=new pageAppTab($pagesObj, $fbUserPageId);
				$code=$tabObj->fetch();
			break;
		}		
	} else if (array_search($method,$sessionOptionalMethods)!==false) {
		// session optional
	  	if (isset($_GET['userid']))
			$userid=$_GET['userid'];
		else
			$userid=0;		
	  	if (isset($_GET['sessionKey']))
			$sessionKey=$_GET['sessionKey'];
		else
			$sessionKey='noSessionKey';		
		$app=setupAppFramework();
		$isSessionValid=$session->validateSession($userid,$sessionKey);			
		switch ($method) {
			case 'askRefreshAnswerComments':
				$change=0;
				if(isset($_POST['fb_sig_xid_action'])) {
					$action = $_POST['fb_sig_xid_action'];
					if($action == 'delete'){
					 $change = -1;
					}else{
					 $change = 1;
					}				
				}
				$answerid=requestInt('id');							
				require_once(PATH_FACEBOOK."/classes/ask.class.php");
				$askObj=new ask($db);
				$askObj->setAppLink($app);
				$code=$askObj->ajaxAskCommentPosted($isSessionValid,$answerid,$change);
			break;			
			case 'ideasRefreshComments':
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
				} else {
					$id=0;						
					$error=true;
					$errorMsg='Invalid idea id';
				}
				$change=0;
				if(isset($_POST['fb_sig_xid_action'])) {
					$action = $_POST['fb_sig_xid_action'];
					if($action == 'delete'){
					 $change = -1;
					}else{
					 $change = 1;
					}				
				}
				require_once(PATH_FACEBOOK."/classes/ideas.class.php");
				$iObj=new ideas($db);
				$iObj->setAppLink($app);
				$code=$iObj->ajaxIdeasPostComment($isSessionValid,$id,$change);								
			break;			
			case 'stuffRefreshComments':
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
				} else {
					$id=0;						
					$error=true;
					$errorMsg='Invalid item id';
				}
				$change=0;
				if(isset($_POST['fb_sig_xid_action'])) {
					$action = $_POST['fb_sig_xid_action'];
					if($action == 'delete'){
					 $change = -1;
					} else {
					 $change = 1;
					}				
				}
				require_once(PATH_FACEBOOK."/classes/stuff.class.php");
				$stuffObj=new stuff($db);
				$stuffObj->setAppLink($app);
				$code=$stuffObj->ajaxStuffPostComment($isSessionValid,$id,$change);		
			break;			
			case 'stuffRefreshSearch':
				if (isset($_GET['tagid'])) 
					$tagid=$_GET['tagid'];
			 	else
			 		$tagid=0;
				if (isset($_GET['view'])) 
					$view=$_GET['view'];
			 	else
			 		$view='all';
				if (isset($_GET['type'])) 
					$type=$_GET['type'];
			 	else
			 		$type='share';
				if (isset($_GET['status'])) 
					$status=$_GET['status'];
			 	else
			 		$status='all';
				// keyword posted thru queryParams
				if (isset($_POST['keyword'])) 
					$keyword=$_POST['keyword'];
			 	else
			 		$keyword='';
				require_once(PATH_FACEBOOK."/classes/stuff.class.php");
				$sObj=new stuff();
				if ($isSessionValid) {
					$fbId=$session->fbId;
					// list of friends of this user
					$sObj->setFriends($session->ui->friends);
				} else
					$fbId=0;
				$code=$sObj->fetchSearchPage(true,$fbId,$tagid,$view,$type,$status,$keyword);
			break;						
			case 'stuffRecordLike': // record stuff like
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
					require_once(PATH_FACEBOOK."/classes/stuff.class.php");
					$stuffObj=new stuff($db);
					$code=$stuffObj->ajaxRecordLike($isSessionValid,$userid,$id);
				} 
			break;
			case 'askRecordLike': // record question like		
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
					$mode=$_GET['mode'];
					require_once(PATH_FACEBOOK."/classes/ask.class.php");
					$askObj=new ask($db);
					$code=$askObj->ajaxAskRecordLike($isSessionValid,$mode,$userid,$id);
				} 
			break;				
			case 'ideaRecordLike': // record idea like
				//$db->log('inside ajax.php idearecordlike'.$userid.' - '.$_GET['id']);
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
					require_once(PATH_FACEBOOK."/classes/ideas.class.php");
					$iObj=new ideas($db);
					$code=$iObj->ajaxIdeaRecordLike($isSessionValid,$userid,$id);
				} 
			break;																		
			case 'ideaFetchBrowse':
				if (isset($_GET['tagid'])) 
					$tagid=$_GET['tagid'];
			 	else
			 		$tagid=0;
				if (isset($_GET['view'])) 
					$view=$_GET['view'];
			 	else
			 		$view='recent';
				require_once(PATH_FACEBOOK."/classes/ideas.class.php");
				$iObj=new ideas();
				$code=$iObj->fetchBrowseIdeas(true,$tagid,$userid,$view);				
			break;			
			case 'askFetchBrowseQuestions':
				if (isset($_GET['tagid'])) 
					$tagid=$_GET['tagid'];
			 	else
			 		$tagid=0;
				if (isset($_GET['view'])) 
					$view=$_GET['view'];
			 	else
			 		$view='noanswers';
				require_once(PATH_FACEBOOK."/classes/ask.class.php");
				$askObj=new ask();
				$code=$askObj->fetchBrowseQuestions(true,$tagid,$userid,$view);				
			break;			
			case 'switchPage':
 				$name=requestStr('name');
				$option=requestStr('option');
				$arg3 = requestStr('arg3');
				require_once(PATH_FACEBOOK.'/classes/pages.class.php');
				$pagesObj=new pages($app,$user,true);
				// hybrid session requirement - some pages need session
				$publicPages=array('home','stories','read','team','rewards','challenges','rules','leaders','404','static','links','micro','stuff','ask','ideas','media','wall','predict');
				if ($isSessionValid OR array_search($name,$publicPages)!==false) {
					$code=$pagesObj->fetch($name,$option,$arg3);
				} else {
					$code = fetchSessionAlert();	
				}								
			break;				
			case 'switchTeamTab':
 				$tab=requestStr('tab');
 				$id=requestStr('id');
				require_once(PATH_FACEBOOK.'/classes/pages.class.php');
				$pagesObj=new pages($app,$user,true);
				$publicPages=array('team','rewards','challenges','rules','leaders', '404');
				if (($isSessionValid && $session->isMember) OR array_search($tab,$publicPages)!==false) {
					$code=$pagesObj->fetchTeam('teamWrap',$tab,$id);
				} else {
					$code = fetchSessionAlert();	
				}				
			break;	
			case 'fetchNewswire':
				// change story list when filter changed
			  	if (isset($_GET['o']))
					$o=$_GET['o'];
				else
					$o='all';		
			  	if (isset($_GET['filter']))
					$filter=$_GET['filter'];
				else
					$filter='all';
				$memberFriends=$session->ui->memberFriends;
				require_once PATH_CORE."/classes/newswire.class.php";
				$nwObj=new newswire();
				$code=$nwObj->fetchNewswirePage($o,$filter,$memberFriends);
			break;
			case 'fetchNewswireWrap':
				// change tab from stories to feeds
				$memberFriends=$session->ui->memberFriends;
			  	if (isset($_GET['tab']))
					$tab=$_GET['tab'];
				else
					$tab='all';						
				require_once PATH_CORE."/classes/newswire.class.php";
				$nwObj=new newswire();
				$code=$nwObj->fetchNewswire($tab,'all',$memberFriends);	
			break;
			case 'fetchNewswirePage':
				// replace just storyList, for paging number functions
			  	if (isset($_GET['o']))
					$o=$_GET['o'];
				else
					$o='all';		
			  	if (isset($_GET['filter']))
					$filter=$_GET['filter'];
				else
					$filter='all';	
				$memberFriends=$session->ui->memberFriends;	
				require_once PATH_CORE."/classes/newswire.class.php";
				$nwObj=new newswire();
				$code=$nwObj->fetchNewswirePage($o,$filter,$memberFriends,$currentPage);
			break;
			case 'shareStory':
				if ($isSessionValid) {
					$itemid=requestInt('itemid');
					$returnPage=requestStr('returnPage');
					require_once(PATH_FACEBOOK.'/classes/shareStories.class.php');
					$ssObj=new shareStories($app);
					$code=$ssObj->buildShareDialog($itemid,$returnPage);
				} else {
					// to do: improve this
					$code = fetchSessionAlert();
				}					
			break;
			case 'shareStorySubmit':
				if ($isSessionValid) {
					$embed=$_POST['embed'];
					$nextPage=$_POST['nextPage'];
					$subject=$_POST['subject'];
					$msg=$_POST['msg'];
					$ids=$_POST['ids'];
					$siteContentId=$_POST['siteContentId'];
					require_once(PATH_FACEBOOK.'/classes/shareStories.class.php');
					$ssObj=new shareStories($app);
					$result=$ssObj->buildShareSubmit($userid,$siteContentId,$subject,$ids,$msg,$embed,$nextPage);
					if ($result[error]) {
						$code=$result[errMsg];
					} else {
						$code=$result[code];
					}					
				} else {
					// to do: improve this
					$code = fetchSessionAlert();
				}					
			break;
			case 'ideaShareSubmit':
				if ($isSessionValid) {
					if (isset($_GET['id'])) {
						$id=$_GET['id'];
						$app=setupAppFramework();
						require_once(PATH_FACEBOOK."/classes/ideas.class.php");
						$iObj=new ideas($db);
						$iObj->setAppLink($app);						
						$ids=$_POST['ids'];
						$code=$iObj->ajaxShareSubmit($userid,$id,$ids);
					}
				} else {
					// to do: improve this
					$code = fetchSessionAlert();
				}					
			break;
			case 'askShareSubmit':
				if ($isSessionValid) {
					if (isset($_GET['id'])) {
						$id=$_GET['id'];
						$app=setupAppFramework();
						require_once(PATH_FACEBOOK."/classes/ask.class.php");
						$aObj=new ask($db);
						$aObj->setAppLink($app);						
						$ids=$_POST['ids'];
						$code=$aObj->ajaxShareSubmit($userid,$id,$ids);
					}
				} else {
					// to do: improve this
					$code = fetchSessionAlert();
				}					
			break;
			case 'common':
				if (isset($_GET['cmd'])) $cmd = $_GET['cmd'];
				switch ($cmd) {
					case 'recordVote':
						if ($isSessionValid AND isset($_GET['siteContentId'])) {
							$siteContentId=$_GET['siteContentId'];
							// record story read by this user
							require_once(PATH_CORE.'/classes/log.class.php');
							$logObj=new log($db);
							$logItem=$logObj->serialize(0,$userid,'vote',$siteContentId);
							//$db->log('recordvote '.$userid.' '.$siteContentId.' '.$session->votePower);
							$inLog=$logObj->update($logItem);
							if ($inLog) {
								require_once(PATH_CORE.'/classes/content.class.php');
								$cObj=new content($db);
								$cObj->updateScore($siteContentId,$session->votePower);
								$code=$cObj->getScore($siteContentId).' votes';
								$filename=PATH_CACHE.'/read_'.$siteContentId.'_top.cac';
								if (file_exists($filename)) unlink($filename);									
							} else {
								$code='You already voted.';
							}
						} else {
							$code = fetchSmallAlert($session->isExpired);
						}
					break;
				}
			break;
		}				
	} else {
		// session required
	  	if (isset($_GET['userid']))
			$userid=$_GET['userid'];
		else
			$userid=0;		
	  	if (isset($_GET['sessionKey']))
			$sessionKey=$_GET['sessionKey'];
		else
			$sessionKey='noSessionKey';		
		$app=setupAppFramework();
		if ($session->validateSession($userid,$sessionKey)!==false) {			
			switch ($method) {		
				case 'askPostAnswer':
				if (isset($_GET['id'])) {
					$id=$_GET['id'];
					require_once(PATH_FACEBOOK."/classes/ask.class.php");
					$askObj=new ask($db);
					$askObj->setAppLink($app);
					$code=$askObj->ajaxAskPostAnswer($userid,$id);
				} 
				break;				
			case 'fetchRewards':
			 	if (isset($_GET['sort']))
					$sort=$_GET['sort'];
				else
					$sort='default';
					
			 	if (isset($_GET['filter']))
					$filter=$_GET['filter'];
				else
					$filter='redeemable';		
					
				require_once(PATH_CORE ."/classes/prizes.class.php");
				$rwObj = new rewards();
				$code = $rwObj->fetchRewards($sort, $filter, $currentPage, true,$session->u->eligibility);
			break;
			
			case 'fetchRewardsPage':
				if (isset($_GET['sort']))
					$sort=$_GET['sort'];
				else
					$sort='default';		
				require_once(PATH_CORE ."/classes/prizes.class.php");
				$rwObj = new rewards();
				$code = $rwObj->fetchRewardsPage($sort, $currentPage,true,'',$session->u->eligibility);
				break;
			case 'fetchWinners':
				
					require_once(PATH_CORE ."/classes/prizes.class.php");
					$rwObj = new rewards();
					$code = $rwObj->fetchWinners('', $currentPage); // hack: wont work w/ custom wherestring
					
				break;
			case 'hideTip':
				$tip=requestStr('tip');
				require_once(PATH_CORE.'/classes/user.class.php');
				$userObj=new UserInfoTable($db);
				$userObj->hideTip($userid,$tip);
				$code='';								
			break;
				case 'log':
					$action=requestStr('action');
					$itemid=requestInt('itemid');
					require_once(PATH_CORE.'/classes/log.class.php');
					$logObj=new log($db);				
					$logItem=$logObj->serialize(0,$userid,$action,$itemid);
					$inLog=$logObj->update($logItem);
				break;										
				case 'quickLog':
					$log=requestStr('log');
					$entry=new stdClass;
					$entry->action=requestStr('action');
					$entry->itemid=requestInt('itemid');
					$entry->str=requestStr('str');										
					$app->quickLog($entry,$log);
				break;			
				case 'fetchBioEditor':						
					require_once(PATH_FACEBOOK ."/pages/pageProfile.class.php");
					//$feedObj = new pageProfile();
					$code = pageProfile::fetchBioEditor($session->ui->fbId);
					
				break;			
				case 'fetchBioAreaAndSaveBio':
					if (isset($_POST['bioText']) ) 
					{
						$bioText = $_POST['bioText'];
						$bioText = strip_tags($bioText, '<p><b><i><strong><em>');
						$bioText = substr($bioText, 0, 500);
						$session->ui->bio=$bioText;
						$session->ui->update();
					}
					
					require_once(PATH_FACEBOOK ."/pages/pageProfile.class.php");
					
					//$feedObj = new pageProfile();
					$code = pageProfile::fetchBioArea(true, $session->ui->fbId);
				break;
				
				case 'fetchTemplateEditorDialog':
					if (isset($_POST['shortName']) ) 
					{
						$shortName = $_POST['shortName'];
						//$helpString = $_POST['helpString'];
						//$theCode = $_POST['code'];
						
						require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');
						
						$dynTemp = dynamicTemplate::getInstance($db);
												
						$theCode= $dynTemp->fetchDBTemplate($shortName);
						$helpString = $dynTemp->fetchDBTemplateHelpstring($shortName);
						// TODO: authorize user again
						
						//$code = "<fb:editor><div>$helpString</div>".
						//"<fb:editor-textarea id='templateEditorCode' rows='10' cols='60'>".
						//		"$theCode</fb:editor-textarea></fb:editor>";
						$code = "<div>Help Text: $helpString<br>".
								"<textarea name='templateEditorCode' id='templateEditorCode' rows='25' cols='55'>$theCode</textarea></div>";
							
					} else
						$code = 'Error, template name not specified';

					
					break;
				case 'saveTemplate':
					if (isset($_POST['shortName']) ) 
					{
						$shortName = $_POST['shortName'];
						$newCode = $_POST['code'];

						if (get_magic_quotes_gpc()) // fucking magic quotes! 
						{
							$newCode = stripslashes($newCode);
						}
						
					}
					require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');
					
					$dynTemp = dynamicTemplate::getInstance($db);
					if (!$dynTemp->editEnabled)  $dynTemp->authEnableEditMode($session);
					$dynTemp->updateAddDBTemplate($shortName,$newCode);
					
					$code = $newCode; // potential security risk?
				break;
				case 'clearTemplate':
					if (isset($_GET['shortName']) ) 
					{
						$shortName = $_GET['shortName'];
					}
					require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');
					
					$dynTemp = dynamicTemplate::getInstance($db);
					if (!$dynTemp->editEnabled) $dynTemp->authEnableEditMode($session);
					if ($dynTemp->deleteDBTemplate($shortName))
						$code = "Template cleared from database.";
					else
						$code = "Error clearing template.";
						
				case 'repopulateTemplate':
					if (isset($_GET['shortName']) ) 
					{
						$shortName = $_GET['shortName'];
					}
					require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');
					
					$dynTemp = dynamicTemplate::getInstance($db);
					if (!$dynTemp->editEnabled) $dynTemp->authEnableEditMode($session);
					if ($dynTemp->deleteDBTemplate($shortName))
					{	
						$code = "Template cleared from database.";
					
						require_once(PATH_CORE .'/classes/template.class.php');
						$templateObj = new template($db);
						$templateObj->populateTemplates(); // hack: reloads ALL template files, SLOW
	
						$code = $dynTemp->useDBTemplate($shortName, 'Error, failed to repopulate.');
					}
					else
						$code = "Error clearing template.";
					
				break;
				case 'previewPublish':
					require_once(PATH_FACEBOOK.'/classes/autoPost.class.php');
					$apObj=new autoPost($app);
					$id=requestInt('id');
					$code=$apObj->fetchPostForm($id);
					$code.='end of form';					
				break;
				case 'postPublish':
					require_once(PATH_FACEBOOK.'/classes/autoPost.class.php');
					$apObj=new autoPost($app);
					$id=requestInt('id');
					//$code=$apObj->fetchPostForm($id);
					$code='all done';
				break;
				case 'addRawToJournal':
					$error = false;
					$limitSql = "SELECT COUNT(1) AS total FROM Log WHERE action = 'publishWire' AND userid1 = $userid AND t > '".date("Y-m-d H:i:s", time() - (12 * 60 * 60))."'";
					$results = $db->query($limitSql);
					$resultsArr = mysql_fetch_assoc($results);
					$numPosted = $resultsArr['total'];
					$max_posts_per_half_day = 5;
					if ($numPosted >= $max_posts_per_half_day) {
						$error = true;
						$code = 'Too many posts. Try again in 12 hours.';
					}
					if (!$error && isset($_GET['itemid'])) {
						$itemid=$_GET['itemid'];
						require_once(PATH_CORE.'/classes/log.class.php');
						$logObj=new log($db);
						$logItem=$logObj->serialize(0,$userid,'publishWire',$itemid);
						$inLog=$logObj->update($logItem);
						if ($inLog)
							$code='Story published!';
						else
							$code='Story already published!';
						// create temporary content item, temp permalink
						require_once(PATH_CORE.'/classes/newswire.class.php');
						$nwObj=new newswire($db);
						$siteContentId=$nwObj->createTempContent($session->u,$itemid);
						// add to user's journal
						if ($siteContentId!==false) {
							// add to journal
							$logItem=$logObj->serialize(0,$userid,'publishStory',$siteContentId);
							$inLog=$logObj->update($logItem);
							$logItem=$logObj->serialize(0,$userid,'vote',$siteContentId);
							$inLog=$logObj->update($logItem);
							// add siteContentId into itemid2 of publishWire log entry
							$db->update("Log","itemid2=$siteContentId","action='publishWire' AND itemid='$itemid' AND userid1=".$userid);								
						}
					} else
						$error=true;
				break;
				case 'showChallengeSubmitDialog':
		            $challengeid=requestInt('challengeid');
	                //require_once(PATH_CORE.'/classes/content.class.php');
	                //$cObj=new content($db);
	                //require_once(PATH_CORE.'/classes/utilities.class.php');
	                ///$uObj=new utilities($db);               
	                //$story=$cObj->getById($itemid);
	                require_once(PATH_FACEBOOK.'/pages/pageChallenges.class.php');
	                $code.= pageChallenges::fetchSubmissionDialogForm($db, $challengeid, $userid, $sessionKey);           
	            /*    $content = "<fb:name uid=\"".$session->fbId."\" firstnameonly=\"true\" shownetwork=\"false\"/> wants to share a story with you: <a href=\"".URL_CANVAS."?p=read&cid='.$itemid.'&referid='.$session->fbId.'\">".$story->title."</a>\n". "<fb:req-choice url=\"".URL_CANVAS."?p=read\" label=\"Read the story\"/>";
	                $code='<fb:request-form action="'.URL_CANVAS.'?p=share" method="post" type="'.SITE_TITLE_SHORT.'" content="'.htmlentities($content).'" image="'.$image.'">  <fb:multi-friend-selector bypass="cancel" showborder="false" actiontext="Select friends you wish to share the story with below:" exclude_ids="" /> </fb:request-form> ';
	                */
	            /*    $code.='<form id="dialog_form" name="dialog_form">' //onsubmit="return false;">';
	                $code .= '<p><strong>To:</strong> <fb:multi-friend-input width="350px" border_color="#8496ba" /></p>';
	                 $code.='</form>';*/
	                
	                // TODO: php redirect after submit
		        break;		
		        
		        
		        
				case 'fetchTeamFriendsList':
				 	if (isset($_GET['state']))
						$state=$_GET['state'];
					else
						$state='collapsed';		
					require_once(PATH_FACEBOOK ."/pages/pageTeam.class.php");
					$db->log($userid);
					$code = pageTeam::fetchTeamFriendList($fb->db,$userid, $state,true);
				break;		
				case 'fetchHomePage':
					// replace just storyList
/*
 * 			  	if (isset($_GET['userid']))
						$userid=$_GET['userid'];
					else
						$userid='default';		
	
 */	
 			require_once PATH_CORE."/classes/home.class.php";
					$homeObj=new home();
					$code=$homeObj->fetchHomePage($currentPage);
				break;
				case 'dialogPublish':
					$error=false;
					if (isset($_GET['mode']))
						$mode=$_GET['mode'];
					else
						$mode='wire';
					if (isset($_GET['itemid']))
						$itemid=$_GET['itemid'];
					else
						$itemid = 0;
					if ($userid==0) {
						$error=true;
						$errorMsg='No user id';
					} else {
					}
					switch ($mode) {
						case 'wire':
							// publish a wire story
							require_once $_SERVER['DOCUMENT_ROOT']."/newsroom/core/classes/log.class.php";
							$logObj=new log($fb->db);
							$info=$logObj->serialize(0,$userid,'publishWire',$itemid);
							$logObj->update($info);					
							$storyOption='<li><a href="?p=read&cid='.$contentid.'" target="_cts">Read the story</a></li>';
						break;
						case 'ncStory':
							// publish a journal story
							$fb->publishNewsCloudStory('publishStory',$itemid);
							$contentid=$itemid;
							$storyOption='<li><a href="?p=read&cid='.$contentid.'" target="_cts">Read the story</a></li>';
						break;
					}
					if (!$error) {
						$code='<div id="dialogMessage"><p>Your story has been published successfully.</p><p>What would you like to do next?</p>';
						$code.='<ul><li><a href="?p=read&cid='.$contentid.'" onclick="hideDialog(); readStory('.$contentid.');return false;">Add a comment</a></li>'.$storyOption.'<li><a href="#" onclick="hideDialog(); return shareStory(this,'.$cid.');">Share story with friends</a>Share story with friends</a></li><li><a href="?p=invite" onclick="hideDialog(); switchPage(\'invite\',\'\');return false;">Invite friends to '.SITE_TITLE.'</a></li></ul></div>';
					} else {
						$code='<div id="dialogMessage">Sorry, there was a problem publishing your story. Error: '.$errorMsg.'</div>';
					}
				break;	
				case 'newswire':
					require_once(PATH_FACEBOOK.'/pages/newswire.class.php');
					$nwObj=new newswire($this);
					$code=$nwObj->fetchNewswirePage($userid,$currentPage);
				break;
				case 'refreshComments':
					if (isset($_GET['cid']) AND is_numeric($_GET['cid'])) {
						$cid=$_GET['cid'];
					} else {
						$cid=0;						
						$error=true;
						$errorMsg='No story id';
					}
					if (!$error) {
						require_once(PATH_CORE.'/classes/read.class.php');
						$comObj=new read($db, $session);
						$code=$comObj->fetchComments($cid);
						$comObj->resetStoryCache($cid);
					} else {
						$code='<p>There was an error refreshing the comments: '.$errorMsg.'</p>';
					}
				break;
				case 'postComment':
					$error=false;
					if (isset($_GET['cid']) AND is_numeric($_GET['cid'])) 
						$cid=$_GET['cid'];
					else {
						$cid=0;
						$error=true;
						$errorMsg='No story id';
					}
					// Make sure user has not exceeded their rate limits
					$commentLimits = array(
						'nickel'	=> 5,
						'hour'		=> 25,
						'day'			=> 100
					);
					$limitSql = "SELECT COUNT(CASE WHEN t > '".date("Y-m-d H:i:s", time() - (5 * 60))."' THEN 1 ELSE null END) AS nickel, COUNT(CASE WHEN t > '".date("Y-m-d H:i:s", time() - (60 * 60))."' THEN 1 ELSE null END) as hour, COUNT(CASE WHEN t > '".date("Y-m-d 00:00:00", time())."' THEN 1 ELSE null END) as day FROM Log WHERE userid1 = $userid AND action = 'comment'";
					$results = $db->query($limitSql);
					$commentTotals = mysql_fetch_assoc($results);
					if ($session->u->isAdmin != 1 && $session->u->isModerator != 1 && $session->u->isSponsor && $session->u->isResearcher) {
						if ($commentTotals['day'] >= $commentLimits['day']) {
							$error = true;
							$errorMsg = 'You have exceeded your rate limit for commenting. Please try again in one day.';
						} else if ($commentTotals['hour'] >= $commentLimits['hour']) {
							$error = true;
							$errorMsg = 'You have exceeded your rate limit for commenting. Please try again in one hour.';
						} else if ($commentTotals['nickel'] >= $commentLimits['nickel']) {
							$error = true;
							$errorMsg = 'You have exceeded your rate limit for commenting. Please try again in 5 mins.';
						}
					}

					// TODO: grab videoURL, validate it, stuff it in to the $comment structure					
					if (isset($_POST['videoURL']) and $_POST['videoURL']<>'')
					{
						require_once(PATH_CORE .'/classes/video.class.php');
						$videoURL = videos::getVideoURLFromEmbedCodeOrURL(stripslashes($_POST['videoURL']));
						
						if (videos::validateVideoURL($videoURL))
						{
							$vt = new VideoTable($db);
							$videoid = $vt->createVideoForComment($userid,$videoURL,"Video Comment on story $cid");
							
						} else
						{
							$error=true;						
							$errorMsg='Unsupported or invalid video URL';
						}
										
					} else if (isset($_POST['commentMsg']) and $_POST['commentMsg']<>'') {
						$commentMsg = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$_POST['commentMsg']);
						//$commentMsg = strip_tags($_POST['commentMsg'], '<a><i><b><p>');
						$commentMsg = strip_tags($commentMsg, '<a><i><b><p>');
						$commentMsg = nl2br($commentMsg);
						// TODO: GET THIS WORKING.
						//$comments = mysql_real_escape_string($_POST['commentMsg'], $db->handle);
					} else {
						$error=true;
						$errorMsg='Comment empty';
					}	
					$comment = array(
							'siteContentId'	=> $cid,
							'fbId'			=> $session->ui->fbId,
							'userid'		=> $userid,
							'comments'		=> $commentMsg,
							'videoid'		=> $videoid
							);
					require_once(PATH_CORE.'/classes/content.class.php');
					$cObj=new content($db);
					if (!$error) {
						$result = $cObj->postComment($comment,$app);
						if (!preg_match('/^[0-9]+$/', $result)) {
							$error = true;
							$errorMsg = " comment save error: $result";
						} else {
							$siteCommentId = $result;
							$filename=PATH_CACHE.'/read_'.$cid.'_com_m.cac';
							if (file_exists($filename)) unlink($filename);	
							$filename=PATH_CACHE.'/read_'.$cid.'_com_n.cac';
							if (file_exists($filename)) unlink($filename);	
						}
						$cObj->updateCommentCount($cid);
					}
					
					if (!$error) {						
						require_once(PATH_CORE.'/classes/log.class.php');
						$logObj=new log($db);
						$logItem=$logObj->serialize(0,$userid,'comment',$siteCommentId,0,$cid); 															
						$inLog=$logObj->add($logItem); 				
						$code='<div id="dialogMessage"><h2>Your comment has been published successfully.</h2><p>What would you like to do next?</p>';
						$code.='<ul class="bullet_list">'.$storyOption.'<li><a href="#" onclick="hideDialog(); return shareStory(this,'.$cid.');">Share story with friends</a></li><li><a href="?p=invite" onclick="hideDialog(); return switchPage(\'invite\');">Invite friends to '.SITE_TITLE.'</a></li></ul></div>';
					} else {
						$code='<div id="dialogMessage">Sorry, there was a problem publishing your comment. Error: '.$errorMsg.'</div>';
					}					
				break;
				case 'requestVerify':
					// send it
					// to do - this code is duped in pagesignup, move to account.class.php
					global $init;
					// ask NewsCloud to send an email verification request
					/*
					require_once (PATH_CORE.'/classes/systemStatus.class.php');
					$ssObj=new systemStatus($db);
					$partnerid=$ssObj->getState('partnerid');				
					if ($partnerid==0) {
						$db->log('ERROR: The site administrator hasn\'t properly configured this site with NewsCloud - missing partner registration.');				
					} else {			
						require_once (PATH_CORE.'/classes/apiCloud.class.php');
						$apiObj=new apiCloud($db,$init[apiKey]);
						$db->log($session->u->email);
						$resp=$apiObj->sendVerifyEmailRequest(SITE_CLOUDID,$session->u->email,$partnerid); 
					}
					*/		
				break;
				case 'mediaProfileUpload':			
				if (isset($_POST['tempName'])) {
					$tempName=$_POST['tempName'];
					require_once(PATH_FACEBOOK."/classes/media.class.php");
					$mObj=new media($db);
					$mObj->setAppLink($app);
					$code=$mObj->ajaxMediaProfileUpload($tempName);
				} else {
					$error=true;
					$errorMsg='Invalid call';
				}							
				break;
				case 'mediaRefreshProfile':
					if (isset($_GET['imageIndex'])) {
						$imageIndex=$_GET['imageIndex'];
						$alpha=$_GET['alpha'];
						$location=$_GET['location'];
						require_once(PATH_FACEBOOK."/classes/media.class.php");
						$mObj=new media($db);
						$mObj->setAppLink($app);
						$code=$mObj->ajaxRefreshPreview($userid,$imageIndex,$alpha,$location);
					} else {
						$error=true;
						$errorMsg='Invalid call';
					}							
				break;				
				case 'mediaRefreshProfileForm':
					if (isset($_GET['imageIndex'])) {
						$imageIndex=$_GET['imageIndex'];
						require_once(PATH_FACEBOOK."/classes/media.class.php");
						$mObj=new media($db);
						$mObj->setAppLink($app);
						$code=$mObj->ajaxBuildProfileForm($imageIndex,'',true);
					} else {
						$error=true;
						$errorMsg='Invalid call';
					}							
				break;				
				case 'stuffSetStatus':
					if (isset($_GET['id'])) {
						$id=$_GET['id'];
						$newStatus=$_GET['newStatus'];
						require_once(PATH_FACEBOOK."/classes/stuff.class.php");
						$sObj=new stuff($db);
						$code=$sObj->ajaxSetStatus($id,$newStatus);
					}
				break;
				case 'stuffSetVisibility':
					if (isset($_GET['id'])) {
						$id=$_GET['id'];
						$newVis=$_GET['newVis'];
						require_once(PATH_FACEBOOK."/classes/stuff.class.php");
						$sObj=new stuff($db);
						$code=$sObj->ajaxSetVisibility($id,$newVis);
					}
				break;
				case 'banStoryPoster':
					if (isset($_GET['cid']) AND is_numeric($_GET['cid'])) {
						$cid=$_GET['cid'];
						require_once(PATH_CORE."/classes/content.class.php");
						$cObj=new content($db);
						$code=$cObj->ajaxBanStoryPoster($app,$cid,$userid);
					}
				break;
				case 'chooseHood':
				if (isset($_GET['hood'])) {
					$hood=$_GET['hood'];
					require_once(PATH_FACEBOOK."/classes/local.class.php");
					$lObj=new local($db);
					$code=$lObj->ajaxUpdateHood($hood,$userid);
				}											
				break;
			}				
		} else {
			// session is NOT valid
			$code = fetchSessionAlert();			
		}
	}		

	echo $code;
	
	function setupAppFramework() {
	 	/* initialize the SMT Facebook appliation class, NO Facebook library */
		require_once PATH_FACEBOOK."/classes/app.class.php";
		global $app,$db,$session;
		$app=new app(NULL,true);
		if ($app->siteStatus=='offline') {
			include(PATH_TEMPLATES.'/offline.php');
			echo $static;
			exit;
		};		
		// caution: do not assign globals by reference
		$db=$app->db;
		// to do - clean up when arrays get posted
		$session=$app->session;	
		if (isset($_POST['ids'])) {
			$tempArr=$_POST['ids'];
			$_POST = $db->mysql_real_escape_array($_POST);
			$_POST['ids']=$tempArr;
		} else {
			$_POST = $db->mysql_real_escape_array($_POST);	
		}
		$_GET = $db->mysql_real_escape_array($_GET);
		return $app;		
	}
	
	function requestStr($key) {
		if (isset($_GET[$key]))
			$val=rawurldecode($_GET[$key]);
		else
			$val='';
		return $val;
	}

	function requestInt($key) {
		if (isset($_GET[$key]))
			$val=$_GET[$key];
		else
			$val=0;
		settype($val,"int");
		return $val;
	}

	function fetchSmallAlert($isExpired) {
		if ($isExpired) {
			$code='<p>Session expired. Please revisit the <a href="?p=home">home page</a> to reactivate it.</p>';
		} else {
			if (!defined('REG_SIMPLE'))
				$code='<p>Please <a href="?p=signup" '.(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').'>sign up</a> so you can do this activity.</p>';		
			else	
				$code='<p>Please <a href="?p=home" requirelogin="1">authorize '.SITE_TITLE.' application</a> with Facebook so you can do this activity.</p>';			
		}
		return $code;
	}
	
	function fetchSessionAlert() {
		global $app,$session;
		$facebook=$app->loadFacebookLibrary(); // needed for api call below
		$prompt='<p>You may want to <fb:prompt-permission perms="offline_access">tell Facebook to make your '.SITE_TITLE.' session permanent</fb:prompt-permission>.</p>';
		if ($session->isLoaded AND $session->isExpired) {
			$code='<fb:error><fb:message>Your '.SITE_TITLE.' session has expired</fb:message>'.$prompt.'<p>Your session seems to have expired. Please revisit our <a href="'.URL_CANVAS.'?p=home">home page</a> or <a href="'.$facebook->get_login_url('?p=home','canvas').'">sign in to Facebook</a> to reactivate it.</p></fb:error>';			
		} else {
			// other problem, not loaded, not member
			$code='<fb:error><fb:message>License and registration please</fb:message><p>Your session may have expired or you may need to <a href="?p=signup" '.(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').'>sign up for '.SITE_TITLE.'</a>.</p>'.$prompt.'<p>Please revisit our <a href="'.URL_CANVAS.'?p=home">home page</a> or <a href="'.$facebook->get_login_url('?p=home','canvas').'">sign in to Facebook</a> to reactivate your session.</p></fb:error>';			
		}
		return $code;
	}	
	
?>
