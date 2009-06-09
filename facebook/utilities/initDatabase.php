<?php

	//echo "\n	Entered Street Team / Facebook tables init\n";
	
	// hack: replicate same structure as core/utility version for now
	/*if ($action=='resetDB') {
		echo 'cleaning up tables<br />';
		require_once(PATH_FACEBOOK.'/classes/cleanup.class.php');
		$cleanupObj=new cleanup($db,'');
		$cleanupObj->flushDatabase(); 
		echo 'Database cleansed<br />';		
	} else {

	*/
		echo "\n	Begin Initializing Street Team / Facebook tables\n";
		
		// Create the fbSessions table
		$manageObj->addTable("fbSessions","id","BIGINT(20) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("fbSessions","userid","BIGINT(20) default 0");
		$manageObj->addColumn("fbSessions","fbId","BIGINT(20) default 0");
		$manageObj->addColumn("fbSessions","fb_sig_session_key","varchar(255) default ''");
		$manageObj->addColumn("fbSessions","fb_sig_time","DATETIME");
		$manageObj->addColumn("fbSessions","fb_sig_expires","DATETIME");
		$manageObj->addColumn("fbSessions","fb_sig_profile_update_time","DATETIME");
	
		
		require_once(PATH_CORE.'/classes/user.class.php');
		UserInfoTable::createTable($manageObj);
		$userInfoTable = new UserInfoTable($manageObj->db);
	 	UserInviteTable::createTable($manageObj);
	 	
		require_once(PATH_CORE.'/classes/prizes.class.php');
		PrizeTable::createTable($manageObj);
		$prizeTable = new PrizeTable($manageObj->db);
		
		// create video table
		require_once(PATH_CORE.'/classes/video.class.php');
		VideoTable::createTable($manageObj);
	
		// create photo table
		require_once(PATH_CORE.'/classes/photo.class.php');
		PhotoTable::createTable($manageObj);
	
		require_once(PATH_CORE.'/classes/challenges.class.php');
		ChallengeTable::createTable($manageObj);
		ChallengeCompletedTable::createTable($manageObj);
		$challengeTable = new ChallengeTable($manageObj->db);
		$challengeTable->populateCommonChallenges();
		
		require_once(PATH_CORE.'/classes/scores.class.php');
		WeeklyScoresTable::createTable($manageObj);
				
		// Create ContactEmail table for the contact us functions
		require_once(PATH_CORE.'/classes/contactEmails.class.php');
		ContactEmailTable::createTable($manageObj);
		$contactemailTable = new ContactEmailTable($manageObj->db);
		//$contactemailTable->testPopulate();
		
		// Create FeaturedTemplate table for the contact us functions
		require_once(PATH_CORE.'/classes/featuredTemplate.class.php');
		FeaturedTemplateTable::createTable($manageObj);
		$featuredTemplateTable = new FeaturedTemplateTable($manageObj->db);
		//$featuredTemplateTable->testPopulate();

		// Create ContentImages table for the contact us functions
		require_once(PATH_CORE.'/classes/contentImages.class.php');
		ContentImageTable::createTable($manageObj);
		$contentImagesTable = new ContentImageTable($manageObj->db);
		//$featuredTemplateTable->testPopulate();

		// UserMedia table 
		// id
		// type
		// submittingUserId
		// url
	
		
		// Media
		// content hosted elsewhere, but newscloud relevant - ignore for now 
				
		// Invitations Table -- need this to filter invite list and also help us assign credit when someone joins

		// Create ContentImages table for the contact us functions
		require_once(PATH_CORE.'/classes/adTrack.class.php');
		AdTrackTable::createTable($manageObj);
		$AdTrackTable = new AdTrackTable($manageObj->db);
		
		// Orders Table
		require_once(PATH_CORE.'/classes/orders.class.php');
		OrderTable::createTable($manageObj);
		$orderTable = new orderTable($manageObj->db);
	 	// test populate if desired
		
		// Create OutboundMessages table for the contact us functions
		require_once(PATH_CORE.'/classes/outboundMessages.class.php');
		OutboundMessageTable::createTable($manageObj);
		$outboundMessagesTable = new OutboundMessageTable($manageObj->db);
		//$outboundMessagesTable->testPopulate();

	
		echo "\n	End Initializing Street Team / Facebook tables\n";


		echo "\n	Initializing Research Tables\n";
		
		// Create object for the research table
		$manageResearchObj = new dbManage(false);
		$manageResearchObj->db->selectDB('research');
	
		
		// Create Research -- RawSessions table for the contact us functions
		require_once(PATH_CORE.'/classes/researchRawSession.class.php');
		RawSessionTable::createTable($manageResearchObj);
		$rawSessionTable = new RawSessionTable($manageResearchObj->db);
		echo "\n	Inserting full data for RawSession table.\n";

		// Create Research -- SessionLengths table for the contact us functions
		require_once(PATH_CORE.'/classes/researchSessionLength.class.php');
		SessionLengthTable::createTable($manageResearchObj);
		$sessionLengthTable = new SessionLengthTable($manageResearchObj->db);

		// Create Research -- RawExtLinks table for the contact us functions
		require_once(PATH_CORE.'/classes/researchRawExtLink.class.php');
		RawExtLinkTable::createTable($manageResearchObj);
		$rawExtLinkTable = new RawExtLinkTable($manageResearchObj->db);

		// Create Research -- UserCollectives table for the contact us functions
		require_once(PATH_CORE.'/classes/researchUserCollective.class.php');
		UserCollectiveTable::createTable($manageResearchObj);
		$userCollectiveTable = new UserCollectiveTable($manageResearchObj->db);

		// Create Research -- Sites table for the contact us functions
		require_once(PATH_CORE.'/classes/researchSites.class.php');
		SiteTable::createTable($manageResearchObj);
		$siteTable = new SiteTable($manageResearchObj->db);

		// Create Research -- Admin_DataStore for the research console
		require_once(PATH_CORE.'/classes/researchAdminDataStore.class.php');
		AdminDataStoreTable::createTable($manageResearchObj);
		$adminDataStoreTable = new AdminDataStoreTable($manageResearchObj->db);

		// Create Research -- Admin_User for the research console
		require_once(PATH_CORE.'/classes/researchAdminUser.class.php');
		AdminUserTable::createTable($manageResearchObj);
		$adminUserTable = new AdminUserTable($manageResearchObj->db);

		// Create Research -- Log Dumps for the research console
		require_once(PATH_CORE.'/classes/researchLogDump.class.php');
		LogDumpTable::createTable($manageResearchObj);
		$logDumpTable = new LogDumpTable($manageResearchObj->db);

		// Create Research -- SurveyMonkeys for the research console
		require_once(PATH_CORE.'/classes/researchSurveyMonkey.class.php');
		SurveyMonkeyTable::createTable($manageResearchObj);
		$surveyMonkeyTable = new SurveyMonkeyTable($manageResearchObj->db);

//	}
		echo "\n	End Initializing Research Tables\n";

/*
	to do - remove this, updatefriends should take care of this from now on
 	 	// temporary update numFriends and numMemberFriends
	$q=$manageObj->db->query("select  userid, (LENGTH(friends) - LENGTH(REPLACE(friends, ',', ''))) as cntFriends, (LENGTH(memberFriends) - LENGTH(REPLACE(memberFriends, ',', ''))) as cntMemberFriends
 from UserInfo order by cntFriends DESC");
 while ($data=$manageObj->db->readQ($q)) {
 	$manageObj->db->query("UPDATE UserInfo SET numFriends=$data->cntFriends,numMemberFriends=$data->cntMemberFriends WHERE userid=$data->userid;");
 }
* 
 */
		
	
?>