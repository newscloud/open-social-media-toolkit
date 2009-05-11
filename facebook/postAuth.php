<?php
	// Facebook calls this page when users add or remove authorization for the application
	
	/* 
	 * on add
	 * fb_sig_authorize=>1
fb_sig_user=>693311688
fb_sig_added=>1
fb_sig_locale=>en_US
fb_sig_in_new_facebook=>1
fb_sig_time=>1230754581.3165
fb_sig_profile_update_time=>1230143899
fb_sig_expires=>1230843600
fb_sig_session_key=>2.6Vq_UMu3Sy0UkEg0Be8sdQ__.86400.1230843600-693311688
fb_sig=>ca49f5e47cd8b287dbe17bf4872c5c48

	 on remove:
	   fb_sig_uninstall=>1
fb_sig_user=>693311688
fb_sig_added=>0
fb_sig_locale=>en_US
fb_sig_in_new_facebook=>1
fb_sig_time=>1230754526.8214
fb_sig=>576c85dc673f91553231de8e9dc47880

	 */
	/* Process incoming variable requests */	
	if (isset($_GET['m'])) {
		$method=$_GET['m'];
	} else 
		$method='remove';

	if (isset($_GET['referid'])) {
		$referid=$_GET['referid'];
	} else 
		$referid=0;

	require_once (PATH_CORE.'/classes/db.class.php');
	$db=new cloudDatabase();
//	$db->log('===');
//	$db->log('method:'.$method);
//	foreach ($_POST as $key=>$item)
//		$db->log($key.'=>'.$item);

	if (isset($_POST['fb_sig_user']) OR isset($_GET['force']))
		$fbId = $_POST['fb_sig_user'];
	else
		exit;

	if (isset($_GET['force'])) {
		$fbId=$_GET['fbId'];		
	}
	require_once(PATH_CORE.'/classes/user.class.php');
	$userTable = new UserTable($db);
	$user = $userTable->getRowObject();	
	$userInfoTable = new UserInfoTable($db);
	$userinfo = $userInfoTable->getRowObject();
	$db->log("postAuth $method, $fbId: entered");	 // TODO: figure out why either a) cant find table record or b) session isnt being called if there really isnt one
	
	if (!$userinfo->loadFromFbId($fbId))
	{
		$db->log("postAuth $method: no userinfo entry for fbId $fbId");
		initializeUserInfo($fbId);	
		if (!$userinfo->loadFromFbId($fbId)) {
			$db->log("die on second postAuth $method: no userinfo entry for fbId $fbId");
			exit;
		}
		
	}
	
	//check if it's a Page
	if (isset($_POST['fb_sig_page_id']))
	{
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log($db);
		$logItem=$logObj->serialize(0,0,'pageAdd',$_POST['fb_sig_page_id']);
		$logObj->update($logItem);
		$db->log("postAuth $method: added app page " . $_POST['fb_sig_page_id']);
		exit;
	}

	switch ($method) {
		case 'add':
			$userinfo->isAppAuthorized = 1;			
			if ($referid>0) {
				$userinfo->refuid=$referid;
			}	 		
			$db->log("postAuth $method: added fbId $fbId" . "refuid=".$referid);
			$userinfo->update();
		break;
		case 'remove':
			// to do: set isAppAuthorized =0
			$userinfo->isAppAuthorized = 0;
			$userinfo->update();			
			$db->log("postAuth $method: removed fbId $fbId");	 
			
		break;
	}

	// copied from session class
	function initializeUserInfo($fbId=0) {
		global $db;
		// adds a record for this user in the Facebook app userinfo table
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($db); // TODO: cache instances of the tables globally
		$userInfoTable = new UserInfoTable($db);
		
		$user = $userTable->getRowObject();
		$userInfo = $userInfoTable->getRowObject();
		
		//dbRowObject::$debug = true;
		$debug = true;
		if ($debug) echo 'entered postAuth::initializeUserInfo()\n';
		// create new users
		$user->isAppAuthorized = 1;
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
	}	
?>
