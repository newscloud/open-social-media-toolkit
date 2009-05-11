<?php
	/* PHP Newsroom AJAX Server */
	/* process request variables */
	if (isset($_GET['page']))
		$page=$_GET['page'];
	else
		$page='home';		
	if (isset($_GET['cmd']))
		$cmd=$_GET['cmd'];
	else
		$cmd='fetchPage';
	define(SIGNIN_LINK,'<a href="'.URL_PREFIX.'?p=signin">Sign in or register please</a>');
		
	/* process the ajax request */		
	$error=false;
	$errorMsg='';
	$code='';
	switch ($page) {
		case 'auth':
			switch ($cmd) {
				case 'checkMemberName':
					if (isset($_GET['memberName'])) {
						$memberName=$_GET['memberName'];
						require_once (PATH_CORE.'/classes/apiCloud.class.php');
						$apiObj=new apiCloud($db,$init['apiKey']);
						$rsp=$apiObj->userCheckMemberName(SITE_CLOUDID,$memberName);
						$code=$rsp['message'];
						if ($rsp['result']===true)
							$code='This member name is available.';
						else
							$code='Sorry, this member name is taken! Please try another.';
					} else {
						$error=true;
						$errorMsg='Your member name is empty. Please enter a proposed member name for yourself.';
					}
				break;
			}
		break;
		case 'upcoming':
			switch ($cmd) {
				case 'fetchPage':
					if (isset($_GET['currentPage']))
						$currentPage=$_GET['currentPage'];
					else
						$currentPage=1;
					include_once ('initialize.php');
					require_once PATH_PHP.'classes/upcoming.class.php';
					$upObj=new upcoming($db);
					$code=$upObj->fetchUpcomingStories(0,$currentPage);
				break;
			}			
		break;			
		case 'newswire':
			switch ($cmd) {
				case 'fetchPage':
					if (isset($_GET['currentPage']))
						$currentPage=$_GET['currentPage'];
					else
						$currentPage=1;
					include_once ('initialize.php');
					require_once PATH_PHP.'classes/newswire.class.php';
					$nwObj=new newswire($db);
					$code=$nwObj->fetchNewswirePage(0,$currentPage);
				break;
			}			
		break;
		case 'readStory':
			define ("INIT_SESSION",true);
			include_once ('initialize.php');
			switch ($cmd) {
				case 'addComment':
					if (isset($_GET['siteContentId']) AND isset($_GET['comments'])) {
						$comments=$_GET['comments'];
						$siteContentId=$_GET['siteContentId'];
						if ($comments<>'') {							
							require_once PATH_CORE.'classes/comments.class.php';
							$comObj=new comments($db);
							$comInfo=$comObj->serialize(0,0,$siteContentId,0,$comments,0,$db->ui->userid,$db->ui->memberName,'',0); // $db->ui->uid
							$siteCommentId=$comObj->add($comInfo);
							
							require_once(PATH_CORE.'/classes/log.class.php');
							$logObj=new log($db);
							$logItem=$logObj->serialize(0,$db->ui->userid,'comment',$siteContentId);
							$logItem->itemid2 = $siteCommentId; // djm: hack for now so I have it
							$inLog=$logObj->add($logItem);
							$code='Your comment has been posted!';						
						} else {
							$error=true;
							$errorMsg='Please enter a valid comment.';
						}
					} else { 
						$error=true;
						$errorMsg='There was a problem posting your comment.';
					}
				break;
				case 'refreshComments':
					if (isset($_GET['siteContentId'])) {
						$siteContentId=$_GET['siteContentId'];
						require_once PATH_PHP.'classes/comments.class.php';
						$comObj=new comments($db);
						$comObj->setupLibraries();
						$story=$comObj->contentObj->getById($siteContentId);						
						$code=$comObj->buildComments(false,$story);
					} else {
						$code='Error finding comment thread';
					}
				break;
			}
		break;
		case 'common':
			define ("INIT_SESSION",true);
			include_once ('initialize.php');
			switch ($cmd) {
				case 'recordVote':
					if (isset($_GET['siteContentId'])) {
						$siteContentId=$_GET['siteContentId'];
						// record story read by this user
						if ($db->ui->isLoggedIn) {
							require_once(PATH_CORE.'/classes/log.class.php');
							$logObj=new log($db);
							$logItem=$logObj->serialize(0,$db->ui->userid,'vote',$siteContentId);
							$inLog=$logObj->update($logItem);
							if ($inLog) {
								require_once(PATH_CORE.'/classes/content.class.php');
								$cObj=new content($db);
								$cObj->updateScore($siteContentId,$db->ui->votePower);
								$code=$cObj->getScore($siteContentId).' votes';
							} else {
								$code='You already voted.';
							}
						} else {
							$code=SIGNIN_LINK;
						}						
					} else
						$error=true;
				break;			
				case 'addToJournal':
					if (isset($_GET['siteContentId'])) {
						$siteContentId=$_GET['siteContentId'];
						if ($db->ui->isLoggedIn) {
							require_once(PATH_CORE.'/classes/log.class.php');
							$logObj=new log($db);
							$logItem=$logObj->serialize(0,$db->ui->userid,'publishStory',$siteContentId);
							$inLog=$logObj->update($logItem);
							if ($inLog)
								$code='Story added!';
							else
								$code='Story already added!';
						} else {
							$code.=SIGNIN_LINK;
						}
					} else
						$error=true;
				break;
				case 'publishWire':
					if (isset($_GET['itemid'])) {
						$itemid=$_GET['itemid'];
						if ($db->ui->isLoggedIn) {
							require_once(PATH_CORE.'/classes/log.class.php');
							$logObj=new log($db);
							$logItem=$logObj->serialize(0,$db->ui->userid,'publishWire',$itemid);
							$inLog=$logObj->update($logItem);
							if ($inLog)
								$code='Story published!';
							else
								$code='Story already published!';
							// create temporary content item, temp permalink
							require_once(PATH_CORE.'/classes/newswire.class.php');
							$nwObj=new newswire($db);
							$db->log('call tempcontent');
							$siteContentId=$nwObj->createTempContent($db->ui,$itemid);
							// add to user's journal
							if ($siteContentId!==false) {
								// add to journal
								$logItem=$logObj->serialize(0,$db->ui->userid,'publishStory',$siteContentId);
								$inLog=$logObj->update($logItem);
								$logItem=$logObj->serialize(0,$db->ui->userid,'vote',$siteContentId);
								$inLog=$logObj->update($logItem);
								// add siteContentId into itemid2 of publishWire log entry
								$db->update("Log","itemid2=$siteContentId","action='publishWire' AND itemid='$itemid' AND userid1=".$db->ui->userid);								
							}
						} else {
							$code=SIGNIN_LINK;
						}
					} else
						$error=true;
				break;
			}
		break;	
	}
	if ($error) {
		$code='Sorry, we encountered an error.';
		if ($errorMsg<>'')
			$code.=' '.$errorMsg;
	}
	
	/* return the response */
	echo $code;
?>
