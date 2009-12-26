<?php

class teamBackend { 
			
	var $db;
	
	function __construct(&$db=NULL)
	{				
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;	
			
		require_once('systemStatus.class.php');
		$this->ssObj=new systemStatus($this->db);
	}
	
	function log($string)
	{
		echo $string.'<br />';
		$this->db->log($string, PATH_CRONLOG);		
	}
		
	/*
	 * Prepares current user base for contest beginning without destroying all of their content and user records
	 * - fresh signup will be required
	 * 
	 */
	
	function testResetAdmins()
	{
		$admins = $this->db->buildIdList("SELECT userid AS id FROM User WHERE isAdmin=1 or isModerator=1;");
		
		$admins = explode(',', $admins);
		
		foreach ($admins as $admin)
		{
			echo 'Resetting contest for ' . $admin . '<br />'; 
			$this->prepareContest($admin);
		}
		
	}
	
	function prepareContest($userid=0) // option to do partial reset for one user
	{
		$this->deleteRecordTables($userid); 
		
		$this->resetUsers($userid);
		
		$this->resetPrizes();
		$this->resetChallenges();
		
		
	}
	
	function deleteRecordTables($userid = 0)
	{
		// ChallengesCompleted, Log, UserInvites, ChallengesCompleted, Comments, ContactEmails, 
		// Notifications, NotificationMessages, Orders, Photos, Videos, WeeklyScores, fbSessions
		
		$recordTables = array(
		 'ChallengesCompleted', 'Log', 'UserInvites', 'Comments', 'ContactEmails', 
		 'Notifications', 'NotificationMessages', 'Orders', 'Photos', 'Videos', 'WeeklyScores', 'fbSessions'
		
		);

		
		foreach($recordTables as $table)
		{
			echo 'Cleaning up '.$table. '...<br />'; 
			
			if ($table == 'Log' && $userid) // ugly hack: log doesnt use the same fieldname, so the call to delete records for a specific user is different than that to delete the table
				$this->cleanupLog($userid);
			else
			{   
				$this->db->delete($table, $userid ? "userid=$userid" : '');
			}
		}
		
	}
	
	
	function resetUsers($testuserid=0)
	{
		echo 'Resetting User...';
		$q = $this->db->query(
			"UPDATE User SET
				email='',
				votePower=1,
				isMember=0,
				isEmailVerified=0, /* not sure about this one... */
				optInStudy=0,
				dateRegistered=NULL,
				eligibility='',
				cachedPointTotal=0 ,
				cachedPointsEarned=0,
				cachedPointsEarnedThisWeek=0,
				cachedPointsEarnedLastWeek=0,
				cachedStoriesPosted=0,
				cachedCommentsPosted=0,
				userLevel=''" . ($testuserid ? " WHERE userid=$testuserid;" : ";"));

		//     UserInfo: dateCreated=NOW(), 
		//				lastUpdate=NOW(), 
		//				lastInvite=??,
		//				lastProfileUpdate=NOW(),c
		//				cachedChallengesCompleted=0,
		echo 'Resetting UserInfo...';
		$q = $this->db->query(
			"UPDATE UserInfo SET
				researchImportance=0,			
				lastUpdated=NULL, 
				lastInvite=NULL, 
				lastProfileUpdate=NULL, 
				lastRemoteSyncUpdate=NULL, 
				cachedFriendsInvited=0, 
				cachedChallengesCompleted=0,
				lastUpdateLevels=NULL,
				lastUpdateSiteChallenges=NULL, 
				lastUpdateCachedPointsAndChallenges=NULL, 
				lastUpdateCachedCommentsAndStories=NULL 			
				". ($testuserid ? " WHERE userid=$testuserid;" : ";"));
	}
	
	function resetChallenges()
	{
		$q = $this->db->query(
				"UPDATE Challenges SET
					remainingCompletions=initialCompletions;");
		
		
	}
	
	
	function resetPrizes()
	{
		$q = $this->db->query(
				"UPDATE Prizes SET
					currentStock=initialStock;");
		
		
	}
	
	
	function getOrphanedUsers()	
	{
		$q = $this->db->query("SELECT DISTINCT userid1 FROM Log WHERE userid1 NOT IN(SELECT userid from User);");
		if ($this->db->countQ($q))
		{
			while($data=$this->db->readQ($q))
			{	
				$list [] = $data->userid1;
			}
		}	
		return $list;
	}
	
	function cleanupOrphanedUsers($confirm=true)
	{
		if ($confirm)
			echo 'Cleaning up users';
		else
			echo 'The following orphaned userids will be cleaned up:';
			
		$orphans = $this->getOrphanedUsers();
	//	echo '<pre>'.print_r($orphans, true) .'</pre>';
		
		foreach ($orphans as $orphan)
		{
			if ($confirm) $this->cleanupUser($orphan);
			else { echo $orphan . '<br>'; }
		}
		
		if (!$confirm) 
			echo '<a href="?p=config&action=cleanupOrphans&apiKey='.$_GET['apiKey'].'&confirm=1">
						Click here to proceed with cleanup</a>';
		
	}
	
	function cleanupLog($userid)
	{
		$this->db->delete("Log", "userid1=$userid");
		$this->db->delete("Log", "userid2=$userid");
		
	}
	
	function cleanupUser($userid)
	{
		if (!$userid)
		{ echo 'userid param was empty.<br>'; return false;}
		
		// log
		
		// challenges
		echo "Cleaning up userid $userid...<br>";
	
		echo "User,UserInfo,Log,ChallengesCompleted...<br>";
		
		$this->db->delete("User", "userid=$userid"); 
		$this->db->delete("UserInfo", "userid=$userid");
		$this->db->delete("ChallengesCompleted", "userid=$userid"); 
		$this->cleanupLog($userid);
		// comments, stories, etc?
		
		// remove session for uer		
		$this->db->delete("fbSessions","userid=".$this->page->session->userid);
		
		return $code; 
		
		
		
	}
	
	
	
	
	function setUserLevels() {
		// HACK: duplicated from teamactivity
	/*	$userLevels = new stdClass;
		$userLevels->pointLevels=array(0,100,1000,2500,5000,10000);
	  	$userLevels->nameLevels =array('Quick-Learner' => 0,
									   	'Sign-Holder' => 1,
									   	'Town-Crier' => 2,
									   	'Rabble-Rouser' => 3,
									  	'Mover-and-Shaker' => 4,
									  	'Climate Czar' => 5);
		$userLevels->votePowerLevels=array(1,2,3,4,5,5,5,5); // extra levels for NC admins
*/
		$userLevels = $GLOBALS['userLevels'];
		return $userLevels;		
	}
	
	function updateUserLevels($limit=100)
	{
			
		
		// scan every use account and adjust the user level to match their cached points
		require_once(PATH_CORE .'/classes/user.class.php');

		if ($limit == 0)
		{	
			$useridList=$this->db->query( 
				"SELECT SQL_CALC_FOUND_ROWS	userid FROM UserInfo;"); // $this->page->rowsPerPage
		} else
		{
			$useridList=$this->db->query( 
				"SELECT SQL_CALC_FOUND_ROWS	userid FROM UserInfo ORDER BY lastUpdateLevels ASC LIMIT 0,$limit"); // $this->page->rowsPerPage			
		}
		
		
		$userTable = new UserTable($this->db);
		$userInfoTable = new UserInfoTable($this->db);
		$user = $userTable->getRowObject();
		$userinfo = $userInfoTable->getRowObject();

		$userLevels=$this->setUserLevels();
					
		$pointLevels = $userLevels->pointLevels;
		$nameLevels = array_keys($userLevels->nameLevels);

	
		$this->log('<pre>'. print_r($pointLevels, true). print_r($nameLevels, true) .'</pre>');
		$levels = array_combine($pointLevels, $nameLevels);
	
		$this->log('updateUserLevels...');
		if ($this->db->countQ($useridList)>0) 
		{
			while($data=$this->db->readQ($useridList))
			{	
				if ($user->load($data->userid) && $userinfo->load($data->userid))
				{
					$x=0;
					if (is_array($levels)) {
						foreach ($levels as $val => $name)
						{
							if ($user->cachedPointsEarned >= $val)
							{
								$userLevel = $name;
								$pointLevel = $val;
								$votePower= $userLevels->votePowerLevels[$x];
							}						
							$x+=1;

						}						
					}
					
					// minor hack: assume user levels only increase. deducting earned points would require revoking challenges
					// also, the limit on redemptions of the levelIncrease challenge should limit potential for abuse
					// AND checking for duplicate log entries with itemid=pts will also prevent multiple level ups at the same level 
					if ($user->userLevel != $userLevel && ($userLevel != $nameLevels[0])) // no points for reaching level 0 :) 
					{
						
						require_once(PATH_CORE.'/classes/log.class.php');
						$log=new log($this->db);
						$log->update($log->serialize(0, $user->userid, 'levelIncrease', array_search($pointLevel, $pointLevels), 0));
						$user->votePower=$votePower;
						$this->db->log('VotePower increase '.$user->userid.' '.$userLevel.' '.$votePower);
					}
					$user->userLevel = $userLevel;
					$user->update();
					
					$userinfo->lastUpdateLevels =  date('Y-m-d H:i:s', time());
					$userinfo->update();
					
					//$this->log('updated user '. $data->userid.'<br/>');
				} else
				{
					$this->log('updateUserLevels: couldnt load user '. $data->userid);
				}
				
			}
			
			
		} else {
			$this->log('updateUserLevels: got no user records!');
		}			
				
	}
	
	
	function updateCachedPointsAndChallenges($limit=1000, $weekOf='')
	{
			
		$this->log('updateCachedPointsAndChallenges...');
	
		if ($weekOf != '')
		{	
			$this->log('Warning: weekOf = '. $weekOf .', specifying a date different from NOW() 
				will cache weekly totals for a different week than the frontend expects. Rerun without weekOf to reset this.');
		}
		// fields that need to be updated offline include
		/*
		 * cachedPointTotal
		 * cached
		 */

		// other fields that might need/want to be updated include comments and stories, however these arent critical because 
		
		// scan every use account and adjust the user level to match their cached points
		require_once(PATH_CORE .'/classes/user.class.php');

		if ($limit == 0)
		{	
			$useridList=$this->db->query(
				"SELECT SQL_CALC_FOUND_ROWS	userid FROM UserInfo;"); // $this->page->rowsPerPage
		} else
		{
			$useridList=$this->db->query( 
				"SELECT SQL_CALC_FOUND_ROWS	userid FROM UserInfo ORDER BY lastUpdateCachedPointsAndChallenges ASC LIMIT 0,$limit"); // $this->page->rowsPerPage			
		}
		
		
		$userTable = new UserTable($this->db);
		$user = $userTable->getRowObject();

		$userinfoTable = new UserInfoTable($this->db);
		$userinfo = $userinfoTable->getRowObject();
	
		if ($this->db->countQ($useridList)>0) 
		{
			while($data=$this->db->readQ($useridList))
			{	
				if ($userinfoTable->updateUserCachedPointsAndChallenges($data->userid, &$user, &$userinfo,$weekOf))				
				{	
					//$this->log('updated user '. $data->userid.'<br/>'); 
				}
				else
					$this->log('updateCachedPointsAndChallenges: couldnt update for user '. $data->userid);				
								
			}			
			
		} else 
		{
			$this->log('updateCachedPointsAndChallenges: got no user records!');
		}			
				
	}
		
	function getFacebookAPI()
	{
		
		$ssObj=new systemStatus($this->db);				
	 	/* initialize the SMT Facebook appliation class, NO Facebook library */
		require_once PATH_FACEBOOK."/classes/app.class.php";
		$app=new app(NULL,true);
		$facebook=&$app->loadFacebookLibrary();
		return $facebook;

	}
	
	// runs all scoring on all users, making the cached scores current for everyone
	function updateScores()
	{
		$this->updateSiteChallenges(0);
		$this->updateCachedPointsAndChallenges(0);
	}
	
	function updateSiteChallenges($limit=1000)
	{
		$this->log('updateSiteChallenges...');
		$facebook=$this->getFacebookAPI();
		// check for profile box, sms, email optins
	// scan every use account and adjust the user level to match their cached points
		require_once(PATH_CORE .'/classes/user.class.php');

		if ($limit == 0)
		{	
			$useridList=$this->db->query(
				"SELECT SQL_CALC_FOUND_ROWS	userid,fbId FROM UserInfo;"); // $this->page->rowsPerPage
		} else
		{
			$useridList=$this->db->query( 
				"SELECT SQL_CALC_FOUND_ROWS	userid,fbId FROM UserInfo ORDER BY lastUpdateSiteChallenges ASC LIMIT 0,$limit"); // $this->page->rowsPerPage			
		}

		if ($this->db->countQ($useridList)==0) 
		{
			$this->log('updateSiteChallenges: got no user records!');
			return;
		}
		
		$userlist = array();
		while($data=$this->db->readQ($useridList))
		{	
			$userlist[$data->fbId] = $data->userid;
		}
	//	$this->log("<pre>".print_r($userlist,true)."</pre>");
			
		
		$fqlquery ="SELECT email,sms,uid FROM permissions WHERE uid IN (".implode(',',array_keys($userlist)).");";
		$this->log($fqlquery); 
		
		try 
		{
			$permissions_info=$facebook->api_client->fql_query($fqlquery);
		} catch (Exception $e)
		{
			$this->log($e->getMessage());
			$this->log($e->getTraceAsString());
			$this->log("updateSiteChallenges aborting safely");			
		}
	
	//	$this->log("<pre>".print_r($permissions_info,true)."</pre>");

		
		$userTable = new UserTable($this->db);
		$user = $userTable->getRowObject();

		$userinfoTable = new UserInfoTable($this->db);
		$userinfo = $userinfoTable->getRowObject();
	
		
		if (is_array($permissions_info)) {
			foreach ($permissions_info as $permdata)
			{
				if ($user->load($userlist[$permdata['uid']]) /*&& $userinfo->load($userlist[$permdata['uid']])*/)
				{

					// ask facebook whether they have - 
					//  - added to profile box? -- no way to detect this here :(
					//  - authorized email
					//  - authorized sms
					//  - anything else we cant detect as it happens
				
					/////////////////////////////////////////////////////					
					// email
				
					$fbEmail = $permdata['email'];
					$this->awardOrRevokeChallenge('optInEmail', $user->userid, $user->optInEmail, $fbEmail);
					$user->optInEmail = $fbEmail;
			 													
					// sms
					$fbSMS = $permdata['sms'];
					$this->awardOrRevokeChallenge('optInSMS', $user->userid, $user->optInSMS, $fbSMS);
					$user->optInSMS = $fbSMS;
				
				
										
					// more...?
				
					/////////////////////////////////////////////////////
				
					$user->lastUpdateSiteChallenges = date('Y-m-d H:i:s', time());
					$user->update();
				
					//$this->log('updated user '. $user->userid.'');
				} else
				{
					$this->log('updateSiteChallenges: couldnt load user '. $user->userid.'\n');
				}	
			}			
		}
			
				
	}
	
	// awards or revokes the specified auto challenge based on current state and new state, returns false if the challenge cant be awarded or revoked
	function awardOrRevokeChallenge($shortname, $userid, $currentState, $newState)
	{
		// award challenge if adding
		if (!$currentState && $newState)
		{
			require_once(PATH_CORE .'/classes/challenges.class.php');
			$ct = new ChallengeCompletedTable($db);
			if (!$ct->submitAutomaticChallenge($userid, $shortname,&$statuscode,true)) // returns false if it couldnt be approved						
			{
				$this->log($statuscode); // TODO: take this out when done testing
				return false;
			}
			$this->db->log("Awarded points for $shortname to $userid");
			
		}
				
		// revoke points if removing
		if ($currentState && !$newState)
		{
			require_once(PATH_CORE .'/classes/challenges.class.php');
			$ct = new ChallengeCompletedTable($db);
			if (!$ct->revokeAutomaticChallengeAward($userid, $shortname)) // returns false if it couldnt be revoked for some reason
			{
				$this->db->log("Error, couldnt revoke points for $shortname for userid $userid");
				return false;  						
			}
			$this->db->log("Revoked points for $shortname from $userid");
			
		}
		return true;
	   	
	}
	
	function calcWeeklyLeaders()
	{
		$this->log('calcLeaders...');
	
		require_once(PATH_CORE.'/classes/scores.class.php');
		$scores = new WeeklyScoresTable($this->db);
		$scores->storeWeeklyPointsEarned(array('team','general','ineligible'), 350, '');
			
	}
	
	
	
	function rewardBetaTesters()
	{
		
		$testers = array();
		
		require_once(PATH_CORE .'/classes/user.class.php');
		require_once(PATH_CORE .'/classes/challenges.class.php');
		$ct = new ChallengeCompletedTable($this->db);
		$cc = $ct->getRowObject();

		$challengeTable = new ChallengeTable($this->db);
		$betaTestChallenge = $challengeTable->getRowObject();
		if (!$betaTestChallenge->loadWhere("shortName='betaTest'"))
		{
			echo "Couldn't find betaTest challenge";
			return false;
		}
		
		
		$userTable = new UserTable($this->db);
		$user = $userTable->getRowObject();
		
		$backdate = "2009-02-28 00:00:00";
		foreach ($testers as $email)
		{
			if ($user->loadWhere("email='$email'"))
			{
				echo "User $user->name, $email found...";
				$statuscode = '';
				if (!$ct->submitAutomaticChallenge($user->userid, 'betaTest', &$statuscode, false))
							 // returns false if it couldnt be approved						
				{
					echo "Challenge approval failure: $statuscode";
								
				} else
				{
					
					echo $statuscode; // TODO: take this out when done testing
					//$this->db->log($statuscode);

					
					// now backdate it!
					if ($cc->loadWhere("userid={$user->userid} AND challengeid={$betaTestChallenge->id}"))
					{	
						//$cc->dateSubmitted = $backdate; // can leave this probably
						$cc->dateAwarded = $backdate;
						$cc->update();
						echo "...backdated succesfully";
					} else
					{
						echo "Couldnt find CC to backdate!";
					}
						
				}
				
				
			} else
			{
				echo "No user found with email $email."; 
			}
			echo "<br />";
			
		}
		
	}
}	
?>