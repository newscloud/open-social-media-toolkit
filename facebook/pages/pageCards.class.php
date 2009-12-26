<?php

/* Provides Facebook-client specific action team help */
class pageCards {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $cardsObj;
	var $isAppTab=false;

	function __construct(&$page) {
		$this->page=&$page;		
		$this->session=$this->page->session;
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'teamactivity');               				
	//	$this->templateObj->registerTemplates(MODULE_ACTIVE,'cards');               				
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		require_once(PATH_FACEBOOK.'/classes/cards.class.php');
		$this->cardsObj=new cards($this->db);
	}
	
	function fetch($option='send') {
		if ($option=='') $option='send'; // fix default pass thru for tabs
		$inside.=$this->buildSubNav($option);	
		$inside.='<div id="col_left"><!-- begin left side --><br />';
		switch ($option) {
			default:
/*
				if (!$this->session->isAppAuthorized) 
				{
					$this->page->facebook=$this->page->app->loadFacebookLibrary();
					$user = $this->page->facebook->require_login();
					return false;
				}
				*/
				$inside.=$this->buildSend();
			break;
			case 'rx':
				$inside.=$this->buildReceived();
			break;
			case 'tx':
				$inside.=$this->buildSent();
			break;
			case 'display':
				$inside.=$this->buildDisplay();
			break;
		}
		$inside.=$this->cardsObj->buildFBJS();
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->teamObj->fetchSidePanel('leaders');		
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('cards',$inside);		
		return $code;
	}		

	function buildReceived() {
		$code=$this->cardsObj->buildCardList('rx',$this->session->fbId);
		return $code;
		
	}

	function buildSent() {
		$code=$this->cardsObj->buildCardList('tx',$this->session->userid);
		return $code;
	}
	
	function buildDisplay() {
		$error=false;
		$id=$_GET['id'];
		if ($id==0) {
			$error=true;
			$title='There was a problem displaying your '.CARDS_NAME;
			$temp=$this->makeFancyTitle($title);
			$temp.='Please <a href="mailto:'.SITE_EMAIL.'">email us</a> to request support.';			
			return $temp;	
		}
		$sender=$_GET['sid'];
		// look up message and check userid
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($this->db);
		$rx=$logObj->fetchExtra($id);
		if ($sender<>$rx->userid1 AND isset($_GET['sid'])) { // to do - remove isset soon
			// sender does not match
			if ($this->session->isLoaded) {
				$code=$this->cardsObj->buildCardList('rx',$this->session->fbId);
				return $code;				
			}  else {
				$code=$this->buildSend();
				return $code;				
			}
		}	
		$code=$this->cardsObj->buildCardDisplay($rx);
		return $code;
	}
	 	
	function buildSend() {
		$code.='<div >';
		if (isset($_GET['submit'])) {
				$this->db->log($_POST);
				//validate form
				$errorMessage='';
				if (sizeof($_POST['ids'])<1)
					$errorMessage='Please specify someone to send the '.CARDS_NAME.' to!';
				else if ($_POST['pickCard']=='' OR $_POST['pickCard']==0)
					$errorMessage=' You must select a '.CARDS_NAME.'!';
				if ($errorMessage!=''){
					$msgType='error';							
					$title='There was a problem...';
					$message=$errorMessage;
				}else{
					$postMsg=$_POST['msg'];
					if ($postMsg==$defaultMsg) $postMsg=''; // reset default msg to blank
						$checkDuplicate=$this->cardsObj->checkResubmit($this->session->userid,$_POST['pickCard'],$_POST['ids']);
						if (!$checkDuplicate){
							// look up card name
							$q=$this->db->query("SELECT name FROM Cards WHERE id={$_POST['pickCard']};");
							$ci=$this->db->readQ($q);
							require_once(PATH_CORE.'/classes/log.class.php');
							$logObj=new log($this->db);
							foreach ($_POST['ids'] as $id){
								// record sendCard in log table
								$logItem=$logObj->serialize(0,$this->session->userid,'sendCard',$_POST['pickCard'],$id);
								$inLog=$logObj->update($logItem);
								$lastId=$logObj->db->getId();
								if (is_numeric($lastId)) {
									// add postMsg to logExtra
									$xTable = new LogExtraTable($this->db);
									$le = $xTable->getRowObject();
									$le->logid=$lastId;
									$le->txt=$postMsg;
									$le->insert();								
								}
								$noteMsg=' sent you a <a href="'.URL_CANVAS.'?p=cards&o=display&id='.$lastId.'&sid='.$this->session->userid.'">'.$ci->name.' '.CARDS_NAME.'</a> via <a href="'.URL_CANVAS.'">'.SITE_TITLE.'</a>';
								$apiResult=$this->page->app->facebook->api_client->notifications_send($id, $noteMsg, 'user_to_user');
							}
							$message=''; // success - display msg in $code directly
							$code.=$this->cardsObj->makeFancyTitle(CARDS_NAME.' SENT SUCCESSFULLY', '500px');
							$rxList=$this->templateObj->buildFacebookUserList('',$_POST['ids']);
							$code.=$this->cardsObj->makeOneCard($_POST['pickCard'],$postMsg,$rxList);
							$code.='<h2><a href="?p=cards&o=send" requirelogin="1">Click here to send another '.CARDS_NAME.'</h2></a>';
							$code.='<br><h2><a href="?p=cards&o=tx">Click here to see all the '.CARDS_NAME.'s you have sent.</h2></a>';
							$code.='</div>';
							return $code;
						}else{
							$msgType='error';							
							$title='There was a problem...';
							$message="<b>You have already sent that ".CARDS_NAME." to one or more of these people.</b>";
						}
				}
		} 
	
		if ($message!=''){
			$code.=$this->page->buildMessage($msgType,$title,$message);
		}
		// prefill is user to send to
		if (isset($_GET['prefillId'])) $prefillId=$_GET['prefillId']; else $prefillId=0;
		$code.=$this->cardsObj->buildSendForm($prefillId);	
		$code.='</div>';
		return $code;
	}

	function buildSubNav($currentSub='send') 
	{
		$pages = array(
		 'send' => 'Send a '.CARDS_NAME,
		 'rx'=> 'Received',
		 'tx'=> 'Sent'
		 );
		 $tabs='<div id="subNav" class="tabs clearfix"><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		 $i=0;
		 foreach (array_keys($pages) as $pagename)
		 {		 	
		 	if ($i==0) {
		 		$clsName='class="first"';
		 	} else {
		 		$clsName='';	
		 	}
	 		$tabs.='<li '.$clsName.'><a id="subtab'.$pagename.'" href="?p=cards&o='.$pagename.'" '.($pagename=='send'?'requirelogin="1"':'').' '.($currentSub==$pagename?'class="selected"':'').'>'.$pages[$pagename].'</a></li>';	
		 	$i++;
		 }
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
	 	return $tabs;
 	}	
}	
?>