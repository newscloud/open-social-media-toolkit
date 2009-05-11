<?php

class pageInvite {

	var $page;
	var $db;
	var $session;
	var $facebook;
	var $templateObj;
	
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->session=&$page->session;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'invite');
	}

	function fetch() {
		// this page requires a session, so a user must be logged in. die if not logged in
		// note: these are here for back up, pages.class.php should take care of this
		if ($this->page->isAjax) {
			$this->facebook=&$this->page->app->loadFacebookLibrary();
		}
		$inside=$this->buildInvitePage();			
		if ($this->page->isAjax) return $inside;
		$code=$this->page->constructPage('invite',$inside);					
			// Daniel - not sure if this should be here 
			//$code.= $this->checkAddNewStreetTeamUser();
		return $code;
	}
		
	function buildInvitePage() {	
		// not tested since dropped into invite.class.php
		// REQUIRES Facebook session
		// some code taken from http://wiki.developers.facebook.com/index.php/Fb:request-forms
		
		/*echo "<pre>";			
		echo '$this->facebook:'; print_r($this->facebook);
		echo '$_GET:'; print_r( $_GET);
		echo "</pre>";
			*/
		
		require_once(PATH_CORE .'/classes/user.class.php');
		
		$userid = $this->session->userid;
		$fbId = $this->session->fbId;
		if (isset($_GET['submit'])) {
			// Process posted invitation ids
			if (count($_POST['ids'])>0) {
				$result=$this->templateObj->buildFacebookUserList('',$_POST['ids']);
				$result.='<h1>Nice work!</h1><h5>What do you want to do now?</h5><div class="bullet_list"><ul>'.
'<li><a href="?p=home" onclick="switchPage(\'home\');return false;">Go to the home page</a>?</li>'.
'<li><a href="?p=team" onclick="switchPage(\'team\');return false;">Visit the '.SITE_TEAM_TITLE.'</a>?</li>'.
'</ul></div><!--end "bullet_list"-->';
				$code.=$this->page->buildMessage('success','Your invitations have been sent',$result);
				// Insert Invitations into table 
				$inviteTable = new UserInviteTable($this->db);
				$invite = $inviteTable->getRowObject();
				
				$invite->userid = $userid;
				$invite->dateInvited = date('Y-m-d H:i:s', time());
				
				foreach ($_POST['ids'] as $id)
				{
					$invite->friendFbId = $id;
					$inviteid=UserInviteTable::checkExists($userid,$id);
					if ($inviteid===false)
						$invite->insert();
					else {
						$invite->id=$inviteid;
						// date will be updated
						$invite->update();
					}
					
					//$this->session->ui->cachedFriendsInvited++; // now done through log
					$log = $this->page->app->getActivityLog();
					$log->update($log->serialize(0, $this->session->userid, 'invite', $invite->friendFbId, 0)); // using itemid since userid2 implies the type is a userid, which it isnt				
				}			
				//$this->session->ui->update();
			} else {
				$code.=$this->page->buildMessage('error','Problem sending invitations','We encountered a problem sending your invitations. <a href="?p=invite" onclick="switchPage(\'invite\');return false;">Please try again</a>.');
			}
		} else {	
			// Exclude users who have added the application already
			
			// TODO: do i use the fbApp action db for invitations?
			// Exclude users invited in the last 14 days by this user

			$userid = $this->session->userid;
			$inviteInterval = 3600*24*14; // 2 weeks in seconds
		
			$debug = false;
			
			$userInfoTable = new UserInfoTable($this->db);
			
			require_once(PATH_CORE .'/classes/user.class.php');

			if ($debug) echo 'session userinfo: <pre>'.print_r($this->session->ui, true). '</pre>';
			
			$allFriends = explode(',',$this->session->ui->friends);
			
			if ($debug) echo 'session memberFriends: <pre>'.print_r($this->session->ui->memberFriends, true). '</pre>';
			
			
			
			$memberFriends = explode(',',$this->session->ui->memberFriends); // now cached
			
			
			if ($debug) echo 'memberFriends uids: <pre>'.print_r($memberFriends, true). '</pre>';
			
			$memberFriends = $userInfoTable->getFbIdsForUsers($memberFriends);
			
			if ($debug) echo 'memberFriends: <pre>'.print_r($memberFriends, true). '</pre>';
			
			//$invitedFriends = array();				
			$invitedFriends = UserInviteTable::getRecentlyInvitedFriends($this->db, $userid, $inviteInterval);
			if ($debug) echo 'invitedFriends: <pre>'.print_r($invitedFriends, true). '</pre>';
		
			if (is_null($invitedFriends)) $invitedFriends = array();
			if (is_null($memberFriends)) $memberFriends = array();
			
			$excludedFriends = array_merge($invitedFriends,$memberFriends);
			$excludedFriends = array_unique($excludedFriends);
			if ($debug) echo 'excludedFriends: <pre>'.print_r($excludedFriends, true). '</pre>';
			
			$cntExcludedFriends =count($encludedFriends);
			//$allFriends=$this->facebook->api_client->friends_get();

			if (count($allFriends)<=$cntExcludedFriends)
			{
				//all friends are signed up or invited already
				$code.='<h2>All your friends currently have the  '.SITE_TITLE.' application added or have been invited within the past two weeks.</h2><p>Thank you for supporting the '.SITE_TITLE.'.</p>';
				$code.='<p><a href="?p=home">Continue to home page</a></p>';
			} else {
				//  Construct a next url for referrals
				$nextUrl=$this->facebook->get_add_url("referfbid=".$fbId."&referid=".$userid."&viaInvite");
				//$this->db->log($nextUrl);
				$inviteText=$this->templateObj->templates['inviteText'] . 
				"<fb:req-choice url=\"".$nextUrl."\" label=\"Add ".SITE_TITLE."!\" />"; // need to have this local jeff!
				$actionStr=$this->templateObj->templates['actionStr'];
				$code.='<fb:request-form action="?p=invite&c=skipped&submit" method="POST" invite="true" type="'.SITE_TITLE.'" content="'.htmlentities($inviteText).'">';
				if (is_numeric($this->page->app->notifications_per_day))
					$maxRequests=$this->page->app->notifications_per_day;
				else
					$maxRequests=20;	
				$code.='<fb:multi-friend-selector rows="5" max="'.$maxRequests.'" exclude_ids="'.join(',',$excludedFriends).'" showborder="false"  actiontext="'.$actionStr.'"> </fb:request-form>';
			}
		}
		return $code;		
	}
	
		
}

?>