<?php

class pageStuff {
	
	var $page;
	var $db;
	var $session;
	var $templateObj;
	var $stuffObj;

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
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'stuff');               				
		require_once(PATH_FACEBOOK.'/classes/stuff.class.php');
		$this->stuffObj=new stuff($this->db,$this->templateObj,$this->session);
		$this->stuffObj->setPage($this->page);
	}
	
	function fetch($option='search') {
		// if session is loaded, get list of user affiliations
		$this->fetchNetworks();
		if ($option=='') $option='search'; // fix default pass thru for tabs
		$inside.=$this->buildSubNav($option);	
		$inside.='<div id="col_left"><!-- begin left side --><br />';
		switch ($option) {
			default: // search page					
				$inside.=$this->buildSearch();
			break;
			case 'add': // add stuff
				if (!$this->session->isLoaded)
					$inside.=$this->page->buildMessage('explanation','Knowing your geographic location would be helpful!','Please <a href="?p=things" requirelogin="1">authorize '.SITE_TITLE.' with Facebook</a> so we can show you stuff nearby you.'); 			
				else if ($this->session->ui->city=='')
					$inside.=$this->page->buildMessage('explanation','Knowing your geographic location would be helpful!','Please <a href="?p=account&o=settings" requirelogin="1">tell us which city you live in</a> so we can add your item with the correct geographic information.'); 
				$inside.=$this->stuffObj->buildAddForm();
			break;
			case 'addSubmit':
				$inside.=$this->buildAddSubmit();			
			break;
			case 'me': // my stuff
				$inside.=$this->buildMyStuff();
			break;
			case 'view':
				$id=$_GET['id'];
				if (!is_numeric($id)) $this->page->decloak();				
				$inside.=$this->stuffObj->buildStuffDisplay($id);
			break;
		}
		$inside.='</div><!-- end left side --><div id="col_right">';
		$inside.=$this->stuffObj->buildSidebar();		
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('stuff',$inside);		
		return $code;
	}		

	function fetchNetworks() {
		// get location, networks and groups
		if ($this->session->isLoaded) {			
			if (is_null($this->session->ui->lastNetSync) OR (time() - strtotime($userinfo->lastNetSync) >(7*24*60*60))) {
				$queries = '{
				  "networks":"SELECT affiliations, current_location FROM user WHERE uid='.$this->session->fbId.'",
					"groups":"SELECT gid,name FROM group WHERE gid IN (SELECT gid FROM group_member WHERE uid ='.$this->session->fbId.')"
				}';
				$this->facebook=$this->session->app->loadFacebookLibrary();
				try {
					$resp=$this->facebook->api_client->fql_multiquery($queries);
					$r=$this->parseMulitquery($resp);
					require_once(PATH_CORE .'/classes/user.class.php');		
					$userInfoTable = new UserInfoTable($this->db);
					$userinfo = $userInfoTable->getRowObject();
					if ($userinfo->loadFromFbId($this->session->fbId)) {
						$userinfo->updateNetworks($r);
					}					
					$this->session->ui->groups=$userinfo->groups;
					$this->session->ui->networks=$userinfo->networks;
				} catch (Exception $e) {
					$this->db->log($e);
				}							
			} 
		}
	}

	function parseMulitquery($resp) {
		$result=array();
		foreach ($resp as $item) {
			$result[$item['name']]=$item['fql_result_set'];
		}
		return $result;
	}
		
	function buildAddSubmit() {
		$code='';
		// check if we have a session
		if (!$this->session->isLoaded) {
			
			$this->facebook=$this->session->app->loadFacebookLibrary();
			$user = $this->facebook->require_login();
		} 
		$resp=$this->stuffObj->processAddForm($this->session->userid,$this->session->fbId);		
		if ($resp['error']===true) {
			$code.=$this->page->buildMessage('error','There was a problem adding your item',$resp['msg']);
			$code.=$this->stuffObj->buildAddForm();
		} else {
			$code.=$this->page->buildMessage('success','Success!','Your item has been posted! Thank you for contributing.');
			$code.=$this->stuffObj->buildStuffDisplay($resp['id']);
		}
		return $code;
	}
		
	function buildMyStuff() {
		$code=$this->stuffObj->listStuff('me',0,$this->session->fbId,'all','all',$keyword,0,99);
		return $code;
	}
		 
	function buildSearch() {
		if (isset($_GET['tagid'])) {
			$tagid=$_GET['tagid'];
			if (!is_numeric($tagid)) $this->page->decloak();					
		} else
	 		$tagid=0;
		if (isset($_GET['view'])) {
			$view=$_GET['view'];
			if (strlen($view)>20) $this->page->decloak();
		} else
	 		$view='all';
		if (isset($_GET['type'])) 
			$type=$_GET['type'];
	 	else
	 		$type='share';
		if (isset($_GET['status'])) 
			$status=$_GET['status'];
	 	else
	 		$status='available';
		if ($this->session->isLoaded)
			$this->stuffObj->setFriends($this->session->ui->friends);
		else {
			$code.=$this->page->buildMessage('explanation','Knowing your geographic location would be helpful!','Please <a href="?p=things" requirelogin="1">authorize '.SITE_TITLE.' with Facebook</a> so we can show you stuff nearby you.'); 			
		}
		$code.=$this->stuffObj->fetchSearchPage(false,$this->session->fbId,$tagid,$view,$type,$status);		
		return $code;
	}

	function buildSubNav($currentSub='search') 
	{
		$pages = array(
		 'search' => 'Search for Things',
		 'add'=> 'Add Things',
		 'me'=> 'My Things'
		 );
		// to do - conditionally add my questions
		
		 $tabs='<div id="subNav" class="tabs clearfix"><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		 $i=0;
		 foreach (array_keys($pages) as $pagename)
		 {		 	
		 	if ($i==0) {
		 		$clsName='class="first"';
		 	} else {
		 		$clsName='';	
		 	}
	 		$tabs.='<li '.$clsName.'><a id="subtab'.$pagename.'" href="?p=things&o='.$pagename.'" '.($pagename=='me'?'requirelogin="1"':'').' '.($currentSub==$pagename?'class="selected"':'').'>'.$pages[$pagename].'</a></li>';	
		 	$i++;
		 }
		$tabs.='</ul><!-- end left_tabs --></div><!-- end subNav --></div>';
	 	return $tabs;
 	}	
}	
?>