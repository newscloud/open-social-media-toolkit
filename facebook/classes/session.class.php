<?php 

class session {
	
	var $app;
	var $db;
	var $facebook;
	var $maxSessionsReached=false;
	var $authLevel;
	var $userid; 
	var $fbId;
	var $sessionKey;
	var $sessionExpires;
	var $user;
	var $name;
	var $birthday;
	var $age;
	var $sex;
	var $locale;
	var $canvas_user;
	var $isLoggedIn;
	var $isAppAuthorized;
	var $isMember;
	var $isAdmin;
	var $isBlocked;
	var $ncUid;
	var $votePower;
	var $isExpired;
	var $isLoaded;
	
	//var $u; // user row object
	var $ui; // userinfo row object
	
	function __construct(&$app) {
		// requires database connection
		$this->app=&$app;
		$this->db=&$app->db;
		$this->facebook=&$app->facebook;
	}

	function validateSession($userid=0,$sessionKey='') {
		$this->isLoaded=false;
		$this->isExpired=false;
		if (is_numeric($userid))
			$q=$this->db->queryC("SELECT *,UNIX_TIMESTAMP(fb_sig_expires) as unixExpire FROM fbSessions WHERE userid=$userid AND fb_sig_session_key='".$sessionKey."' LIMIT 1;");
		else
			return false;			
		if (!$q) {
			// to do - no session
			return false;
		} else {
			// ajax session validated			
			$data=$this->db->readQ($q);
			if ($data->unixExpire>time() OR $data->unixExpire==0) { // 0 is infinite session				
				$this->userid=$data->userid;
				$this->fbId=$data->fbId;							
				$this->isLoggedIn = true; 
				$this->lookupUserByFacebookAndCacheUserInfo($data->fbId,true); // sets up session->u and session->ui	
				$this->votePower=$this->u->votePower; 
				$this->name=$this->u->name;
				$this->isExpired=false;	
				$this->isLoaded=true;	
				$this->app->quickLog($data,'session');		
				
				return true;
			} else {
				$this->isExpired=true;
				return false;			
			}
		}			
	}
	
	function setupSession() {
		// Set Facebook user id, will be false if not logged in
		// is app authorized
		if (isset($_POST['fb_sig_session_key'])) {
			$this->sessionKey=$_POST['fb_sig_session_key'];
			$this->sessionExpires=$_POST['fb_sig_expires'];
		}
		if ($_POST['fb_sig_added']) { 
			$this->isAppAuthorized=true;
			//$this->fbId=$this->facebook->get_loggedin_user();
			$this->fbId=$_POST['fb_sig_user'];
			$this->user=$this->fbId;
		} else {
			$this->isAppAuthorized=false;
			//$this->fbId=$this->facebook->get_canvas_user();
			$this->fbId=$_POST['fb_sig_canvas_user'];
			$this->canvas_user=$this->fbId;
		}					
		// check if user has logged in to facebook
		if ($this->fbId!==false and is_numeric($this->fbId)) {
			// user is logged in to facebook
			$this->isLoggedIn=true;
			$data=$this->lookupUserByFacebookAndCacheUserInfo($this->fbId,false);
			if ($data===false) {				
				// user doesn't exist, so create them
				$data=$this->initializeUserInfo($this->fbId,$this->isAppAuthorized);
			} 
			// fetch or create new session db entry
			if ($this->fetch($data,$this->sessionKey)!==false) {
				if ($data->isMember==0) {
					$this->isMember=false;		
				} else {
					$this->isMember=true;
				}								
				if ($data->isBlocked==0) {
					$this->isBlocked=false;		
				} else {
					$this->isBlocked=true;
				}
				if ($data->isAdmin==0)
					$this->isAdmin=false;
				else
					$this->isAdmin=true;								
				$this->isLoaded=true;
				$this->isExpired=false;				
			}
		} else {
			// user is not signed in to facebook - we know nothing about them
			$this->setEmptySession();
		}
		$this->setAuthLevel();
		
	}
	
	function setEmptySession() {
		$this->isLoggedIn=false;
		$this->isAppAuthorized=false;
		$this->isMember=false;
		$this->ncUid=0;
		$this->fbId=0;	
		$this->userid=0;		
		$this->isBlocked=false;
		$this->isLoaded=false;
		$this->isExpired=true;
	}

	function fetch($userinfo=NULL,$sessionKey='') {
		// look up session record for this local user
		// no session record, create one
		$chkDup=$this->db->queryC("SELECT *,UNIX_TIMESTAMP(fb_sig_expires) as unixExp FROM fbSessions WHERE userid=$userinfo->userid LIMIT 1;");
		if (!$chkDup) {
			if ($this->checkSessionLoad()) {
				$session=$this->serialize(0,$this->userid,$this->fbId,$_POST['fb_sig_session_key'],$_POST['fb_sig_time'],$_POST['fb_sig_expires'],$_POST['fb_sig_profile_update_time']);
				$q=$this->db->insert("fbSessions","userid,fbId,fb_sig_session_key,fb_sig_time,fb_sig_expires","$userinfo->userid,$userinfo->fbId,'$sessionKey',FROM_UNIXTIME($session->fb_sig_time),FROM_UNIXTIME($session->fb_sig_expires)");				
			} else
				return false;
		} else {
			// existing session record, update it
 			$session=$this->db->readQ($chkDup); 			
 			$tempExpire=$_POST['fb_sig_expires'];
 			// $this->db->log($session->unixExp.'-'.time()); 			
 			if ($session->unixExp<time()) {
 				if ($this->checkSessionLoad()===false) return false; // if session is expired, check load	
 			}
 			if ($tempExpire==0) {  
				$tempExpire=mktime(0,0,0,12,31,35);			
			}
			$this_query=$this->db->update("fbSessions","fb_sig_time=FROM_UNIXTIME(".$_POST['fb_sig_time'].",'%Y-%m-%d %H:%i:%s'),fb_sig_session_key='".$_POST['fb_sig_session_key']."',fb_sig_expires=FROM_UNIXTIME($tempExpire)","userid=$userinfo->userid");			 
 		}
 		$this->app->quickLog($session,'session');
		return $session;
	}

	function checkSessionLoad() {
		// return active # of sessions
		$q=$this->db->query("SELECT count(id) as cnt FROM fbSessions WHERE fb_sig_time>=date_sub(NOW(), INTERVAL 1 HOUR);");
		$data=$this->db->readQ($q);		
		//$this->db->log('session load:'.$data->cnt);
		if ($data->cnt>$this->app->max_sessions) {
			$this->maxSessionsReached=true;
			$this->setEmptySession();
			//$this->db->log('session load maxed out');
			return false;
		}
		//$this->db->log('session load ok');
		return true;			
	}
	
	function setAuthLevel() {
		// determines authLevel setting from current session
		// anonymous = unknown, not logged in to facebook
		// notAuthorized = logged in to FB, not yet authorized 
		// notMember = authorized app, not yet signed up
		// member = all signed up
		if ($this->fbId!==false AND $this->fbId>0) {
			if ($this->isAppAuthorized) {
				if ($this->isMember) {
					$this->authLevel='member';
				} else {
					$this->authLevel='notMember';
				}
			} else {
				$this->authLevel='notAuthorized';
			}
		} else {
			$this->authLevel='anonymous';
		}
	}
	
	function syncFacebookData($fbId=0) {
		// alternate approach		$fbInfo=$this->facebook->api_client->users_getInfo(array($this->fbLib->user),array('name'));
		// see also http://wiki.developers.facebook.com/index.php/User_(FQL)
		if ($fbId<>0 AND is_numeric($fbId)) {
			$info=$this->facebook->api_client->fql_query("SELECT first_name,last_name,name,locale,sex,birthday,proxied_email FROM user WHERE uid = $fbId;");
			//vardump($info);
			if (is_array($info)) 
			{
				$this->name=$info[0]['name'];			
				$this->sex=$info[0]['sex'];
				$this->locale=$info[0]['locale'];
				$this->birthday=$info[0]['birthday'];
				// to do: process birthday and set default as unknown
				$this->age='unknown';		
				$this->proxied_email=$info[0]['proxied_email'];
			} 			
		}
	}
	
	function lookupUserByFacebook($fbId=0) // old version 
	{		
		// look up user by Facebook Id
		$q=$this->db->queryC("SELECT * FROM User,UserInfo WHERE User.userid=UserInfo.userid AND fbId=$fbId;");
		if ($q!==false) {
			$data=$this->db->readQ($q);
			$this->userid=$data->userid;
			$this->isMember=$data->isMember;
			return $data;
		} else {
			return false;
		}			
	}
	
	function lookupUserByFacebookAndCacheUserInfo($fbId=0,$isAjax=false) // djm: same interface as the original but now caches user info in session and updates member friends
	{
		//$before = memory_get_usage();
		
		// get User and UserInfo table instances so we can get row objects
		require_once(PATH_CORE .'/classes/user.class.php');
		
		$userTable = new UserTable($this->db); // TODO: cache instances of the tables globally
		$userInfoTable = new UserInfoTable($this->db);
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();
		
		// load the ui record for the fbId and if it succeeds, the corresponding user record
		if ($userinfo->loadFromFbId($fbId) && $user->load($userinfo->userid))
		{				
			if (!$isAjax) {
				// run update friends if needed
				if (time() - strtotime($userinfo->lastUpdated) >24*60*60)
				{		
					//$this->db->log("about to update friends...POST contains: ".print_r($_POST,true).""); // djm temporary debugging						
					if (isset($_POST['fb_sig_friends']) AND $_POST['fb_sig_friends']<>'') {
						 
						$userinfo->updateFriends(explode(',', $_POST['fb_sig_friends']));
					} else {
						$this->facebook=$this->app->loadFacebookLibrary();
						
						if (!is_null($this->facebook->api_client->friends_list))
							$userinfo->updateFriends($this->facebook->api_client->friends_list);
					}				
				}				
			}
			
			// cache row objects for consistent easy use throughout pages
			$this->ui = $userinfo;
			$this->u = $user;
			
			// fill in the existing fields of session to make it compatible with old code
			$this->userid=$this->ui->userid;
			$this->isMember=$this->u->isMember;

			
			// make function return compatible with old code
			$data->votePower		=$user->votePower;
			$data->isAppAuthorized = $userinfo->isAppAuthorized;			
			$data->isMember			=$user->isMember;
			$data->isBlocked		=$user->isBlocked;
			$data->isAdmin			=$user->isAdmin;
			$data->name				=$userinfo->name;
			$data->userid			=$userinfo->userid;
			$data->fbId				=$userinfo->fbId;
			
			//$after = memory_get_usage();
			//$this->db->log("session: lookupUserByFacebookAndCacheUserInfo: memory bytes before: $before, after: $after, delta: ". ($after-$before));
			return $data;
		} else
			return false;
	}
	
	function initializeUserInfo($fbId=0,$isAppAuthorized=0) {
		// adds a record for this user in the Facebook app userinfo table
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($this->db); // TODO: cache instances of the tables globally
		$userInfoTable = new UserInfoTable($this->db);
		
		$user = $userTable->getRowObject();
		$userInfo = $userInfoTable->getRowObject();
		
		//dbRowObject::$debug = true;
		$debug = false;
		if ($debug) echo 'entered session::initializeUserInfo()\n';
		// create new users
		$user->isAppAuthorized = $isAppAuthorized;
		$user->votePower=1;
		
		if ($user->insert())
		{
			// inserted ok
			if ($debug) echo '<p>created $user:<pre>'. print_r($user, true).'</pre>';
			
			if ($userInfo->createFromUser($user, $fbId))
			{
			//*
				if ($debug) 
				{	
				  	echo 'Created new user info\n';
					echo '<p>$userInfo:<pre>'. print_r($userInfo, true).'</pre>';
				}
				//*/
				$userInfoTest = $userInfoTable->getRowObject();
				$userInfoTest->loadFromFbId($fbId);
					
				if ($debug) echo '<p>fetched user info debug: $userInfo:<pre>'. print_r($userInfoTest, true).'</pre>';				

				// populate subscription settings for the new user
				require_once(PATH_CORE.'/classes/subscriptions.class.php');
				$subTable = new SubscriptionsTable($this->db); 
				$sub = $subTable->getRowObject();
				$sub->userid=$user->userid;
				$sub->rxFeatures=1;
				$sub->rxMode='notification';
				$sub->insert();
			} else
			{
				if ($debug)
				{
					echo "Failed to create UserInfo row:<br>";				
					echo '<p>$userInfo:<pre>'. print_r($userInfo, true).'</pre>';
				}
			}
		} else
		{
			if ($debug) echo "Failed to insert user!\n";
		}
		
		
		
		// merge necessary session data into a results object and return it 
		$data->isMember		=$user->isMember;
		$data->isBlocked	=$user->isBlocked;
		$data->isAdmin		=$user->isAdmin;
		$data->name			= $userInfo->name;
		$data->userid=$userInfo->userid;
		// .. etc
		return $data;
		
	    /*   
		echo "<p>session->fbId: {$this->app->session->fbId}</p>";
		$userInfo->loadFromFbId($this->app->session->fbId);
		
		echo '<p>$userInfo:<pre>'. print_r($userInfo).'</pre>';
		
		if ($userInfo->userid && $user->load($userInfo->userid))
			{
			$code .= 'Found a user...';
	*/
		
		
	} 	
	
	function fetchExpired() {
		$q=$this->db->query("SELECT * FROM fbSessions WHERE fb_sig_expires<NOW();");
		return $q;
	}

	function fetchCurrent() {
		$q=$this->db->query("SELECT * FROM fbSessions WHERE fb_sig_expires>NOW();");
		return $q;
	}
	
	function serialize($id=0,$userid=0,$fbId=0,$fb_sig_session_key='',$fb_sig_time='',$fb_sig_expires='',$fb_sig_profile_update_time='') {
		// creates an object for a facebook session key
		$data= new stdClass;
		$data->id=0;
		$data->userid = $userid;
		$data->fbId = $fbId;
		$data->fb_sig_session_key=$fb_sig_session_key;
		$data->fb_sig_time=$fb_sig_time;
		if ($fb_sig_expires==0)
			$data->fb_sig_expires=mktime(0,0,0,12,31,35);
		else
			$data->fb_sig_expires=$fb_sig_expires;
		$data->fb_sig_profile_update=$fb_sig_profile_update;
		return $data;
	}	

	function debug() {
		$code='<p><b>Session Debug output:</b><br />';
		$code.='AuthLevel: '.$this->authLevel.'<br />';
		$code.='Facebook ID: '.$this->fbId.'<br />';
		$code.='UserID: '.$this->userid.'<br />';
		$code.='NewsCloud User Id: '.$this->ncUid.'<br />';
 		$code.='Is App Authorized: '.($this->isAppAuthorized?'yes':'no').'<br />';
		$code.='Is Member:'.($this->isMember?'yes':'no').'<br />';
		$code.='Is Admin:'.($this->isAdmin?'yes':'no').'<br />';
		$code.='Is Blocked:'.($this->isBlocked?'yes':'no').'<br />';
		$code.='</p>';
		//var_dump($_POST);
		return $code;
	}
	
	

}	
?>