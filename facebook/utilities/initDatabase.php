<?php

		echo "\n	Begin Initializing Street Team / Facebook tables\n";
		
		// Create the fbSessions table
		$manageObj->addTable("fbSessions","id","BIGINT(20) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("fbSessions","userid","BIGINT(20) default 0");
		$manageObj->addColumn("fbSessions","fbId","BIGINT(20) default 0");
		$manageObj->addColumn("fbSessions","fb_sig_session_key","varchar(255) default ''");
		$manageObj->addColumn("fbSessions","fb_sig_time","DATETIME");
		$manageObj->addColumn("fbSessions","fb_sig_expires","DATETIME");
		$manageObj->addColumn("fbSessions","fb_sig_profile_update_time","DATETIME");

		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','prizes.class.php')) {		
			require_once(PATH_CORE.'/classes/prizes.class.php');
			PrizeTable::createTable($manageObj);
			$prizeTable = new PrizeTable($manageObj->db);
		}
		
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','video.class.php')) {		
			// create video table
			require_once(PATH_CORE.'/classes/video.class.php');
			VideoTable::createTable($manageObj);
		}
	
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','photo.class.php')) {		
			// create photo table
			require_once(PATH_CORE.'/classes/photo.class.php');
			PhotoTable::createTable($manageObj);
		}
	
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','challenges.class.php')) {		
			require_once(PATH_CORE.'/classes/challenges.class.php');
			ChallengeTable::createTable($manageObj);
			ChallengeCompletedTable::createTable($manageObj);
			$challengeTable = new ChallengeTable($manageObj->db);
			$challengeTable->populateCommonChallenges();
		}
		
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','scores.class.php')) {		
			require_once(PATH_CORE.'/classes/scores.class.php');
			WeeklyScoresTable::createTable($manageObj);
		}
				
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','contactEmails.class.php')) {		
			// Create ContactEmail table for the contact us functions
			require_once(PATH_CORE.'/classes/contactEmails.class.php');
			ContactEmailTable::createTable($manageObj);
			$contactemailTable = new ContactEmailTable($manageObj->db);
			//$contactemailTable->testPopulate();
		}
		
		// Create FeaturedTemplate table for the contact us functions
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','featuredTemplate.class.php')) {
			require_once(PATH_CORE.'/classes/featuredTemplate.class.php');
			FeaturedTemplateTable::createTable($manageObj);
			$featuredTemplateTable = new FeaturedTemplateTable($manageObj->db);
			//$featuredTemplateTable->testPopulate();
		}	

		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','contentImages.class.php')) {		
			// Create ContentImages table for the contact us functions
			require_once(PATH_CORE.'/classes/contentImages.class.php');
			ContentImageTable::createTable($manageObj);
			$contentImagesTable = new ContentImageTable($manageObj->db);
			//$featuredTemplateTable->testPopulate();
		}	
		// UserMedia table 
		// id
		// type
		// submittingUserId
		// url
	
		
		// Media
		// content hosted elsewhere, but newscloud relevant - ignore for now 
				
		// Invitations Table -- need this to filter invite list and also help us assign credit when someone joins

		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','adTrack.class.php')) {		
			// Create ContentImages table for the contact us functions
			require_once(PATH_CORE.'/classes/adTrack.class.php');
			AdTrackTable::createTable($manageObj);
			$AdTrackTable = new AdTrackTable($manageObj->db);
		}
		
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','orders.class.php')) {		
			// Orders Table
			require_once(PATH_CORE.'/classes/orders.class.php');
			OrderTable::createTable($manageObj);
			$orderTable = new orderTable($manageObj->db);
		 	// test populate if desired
		}
		
		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','outboundMessages.class.php')) {		
			// Create OutboundMessages table for the contact us functions
			require_once(PATH_CORE.'/classes/outboundMessages.class.php');
			OutboundMessageTable::createTable($manageObj);
			$outboundMessagesTable = new OutboundMessageTable($manageObj->db);
			//$outboundMessagesTable->testPopulate();
		}

		echo "\n	End Initializing Street Team / Facebook tables\n";

		if (defined('ENABLE_CARDS')) {
			if ($manageObj->modifyLibrary(PATH_FACEBOOK.'/classes/','cards.class.php')) {		
				// Card Table
				require_once(PATH_FACEBOOK.'/classes/cards.class.php');
				CardTable::createTable($manageObj);
			}
		}

		if (defined('ENABLE_ASK')) {
			if ($manageObj->modifyLibrary(PATH_FACEBOOK.'/classes/','ask.class.php')) {		
				// Ask Tables
				require_once(PATH_FACEBOOK.'/classes/ask.class.php');
				askQuestionsTable::createTable($manageObj);
				askAnswersTable::createTable($manageObj);
			}
			$q=$manageObj->db->query("SHOW INDEX FROM AskQuestions;");
			if ($manageObj->db->countQ($q)==0) 
				$manageObj->db->query("ALTER TABLE AskQuestions ADD FULLTEXT INDEX related (question);");
		}

		if (defined('ENABLE_IDEAS')) {
			if ($manageObj->modifyLibrary(PATH_FACEBOOK.'/classes/','ideas.class.php')) {		
				// Ideas Table
				require_once(PATH_FACEBOOK.'/classes/ideas.class.php');
				ideasTable::createTable($manageObj);
			}
			$q=$manageObj->db->query("SHOW INDEX FROM Ideas;");
			if ($manageObj->db->countQ($q)==0) 
				$manageObj->db->query("ALTER TABLE Ideas ADD FULLTEXT INDEX related (idea);");
		}

		if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','tags.class.php')) {		
			require_once(PATH_CORE.'/classes/tags.class.php');
			TagsTable::createTable($manageObj);	
			TaggedObjectsTable::createTable($manageObj);	
			$tagsTable = new tagsTable($manageObj->db); 
			$tagsTable->initialize(); // set default tags		
			/* deprecated for now
			// switch to stuff database
			$manageObj->db->selectDB('stuff');
			$tagsTable->initializeStuff(); // set default tags		
			*/
			// return to other database
			$manageObj->db->selectDB($init['database']);
		}

/* deprecated for now
		// to do - note: might need to have init tag from array separate
		if (defined('ENABLE_STUFF')) {
			if ($manageObj->modifyLibrary(PATH_FACEBOOK.'/classes/','stuff.class.php')) {		
				// Stuff Tables
				require_once(PATH_FACEBOOK.'/classes/stuff.class.php');
				$manageObj->db->selectDB('stuff');
				itemsTable::createTable($manageObj);
				accessTable::createTable($manageObj);
				//$sObj=new stuff();
				//$sObj->updateImages();
				//$sObj->importLibraryItems();
				//$sObj->importLibraryUsers();
				//$sObj->patchAccess();
				// return to other database
				$manageObj->db->selectDB($init['database']);
			}
		}
		*/
		
		if (defined('ENABLE_MICRO')) {
			if ($manageObj->modifyLibrary(PATH_FACEBOOK.'/classes/','micro.class.php')) {		
				// Micro blog tables
				require_once(PATH_FACEBOOK.'/classes/micro.class.php');
				microAccountsTable::createTable($manageObj);
				microPostsTable::createTable($manageObj);
			}
		}

		if (defined('ENABLE_RESEARCH_STUDY')) {
					echo "\n	Initializing Research Tables\n";

					// Create object for the research table
					$manageResearchObj = new dbManage(false);
					$manageResearchObj->db->selectDB('research');
					
					// set up system status table for research database
					// to do - move this to a dbrowobject model
					$manageObj->addTable("SystemStatus","id","INT(4) unsigned NOT NULL auto_increment","MyISAM");
					$manageObj->addColumn("SystemStatus","name","VARCHAR(35) default ''");
					$manageObj->addColumn("SystemStatus","strValue","TEXT default ''");
					$manageObj->addColumn("SystemStatus","numValue","BIGINT(20) default 0");
					
				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchRawSession.class.php')) {		
					// Create Research -- RawSessions table for the contact us functions
					require_once(PATH_CORE.'/classes/researchRawSession.class.php');
					RawSessionTable::createTable($manageResearchObj);
					$rawSessionTable = new RawSessionTable($manageResearchObj->db);
					echo "\n	Inserting full data for RawSession table.\n";
				}
				
				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchSessionLength.class.php')) {		
					// Create Research -- SessionLengths table for the contact us functions
					require_once(PATH_CORE.'/classes/researchSessionLength.class.php');
					SessionLengthTable::createTable($manageResearchObj);
					$sessionLengthTable = new SessionLengthTable($manageResearchObj->db);
				}

				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchRawExtLink.class.php')) {		
					// Create Research -- RawExtLinks table for the contact us functions
					require_once(PATH_CORE.'/classes/researchRawExtLink.class.php');
					RawExtLinkTable::createTable($manageResearchObj);
					$rawExtLinkTable = new RawExtLinkTable($manageResearchObj->db);
				}

				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchUserCollective.class.php')) {		
					// Create Research -- UserCollectives table for the contact us functions
					require_once(PATH_CORE.'/classes/researchUserCollective.class.php');
					UserCollectiveTable::createTable($manageResearchObj);
					$userCollectiveTable = new UserCollectiveTable($manageResearchObj->db);
				}
				
				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchSites.class.php')) {	
					// Create Research -- Sites table for the contact us functions
					require_once(PATH_CORE.'/classes/researchSites.class.php');
					SiteTable::createTable($manageResearchObj);
					$siteTable = new SiteTable($manageResearchObj->db);
				}
				
				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchAdminDataStore.class.php')) {		
					// Create Research -- Admin_DataStore for the research console
					require_once(PATH_CORE.'/classes/researchAdminDataStore.class.php');
					AdminDataStoreTable::createTable($manageResearchObj);
					$adminDataStoreTable = new AdminDataStoreTable($manageResearchObj->db);
				}
				
				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchAdminUser.class.php')) {		
					// Create Research -- Admin_User for the research console
					require_once(PATH_CORE.'/classes/researchAdminUser.class.php');
					AdminUserTable::createTable($manageResearchObj);
					$adminUserTable = new AdminUserTable($manageResearchObj->db);
				}
				
				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchLogDump.class.php')) {		
					// Create Research -- Log Dumps for the research console
					require_once(PATH_CORE.'/classes/researchLogDump.class.php');
					LogDumpTable::createTable($manageResearchObj);
					$logDumpTable = new LogDumpTable($manageResearchObj->db);
				}
				
				if ($manageObj->modifyLibrary(PATH_CORE.'/classes/','researchSurveyMonkey.class.php')) {		
					// Create Research -- SurveyMonkeys for the research console
					require_once(PATH_CORE.'/classes/researchSurveyMonkey.class.php');
					SurveyMonkeyTable::createTable($manageResearchObj);
					$surveyMonkeyTable = new SurveyMonkeyTable($manageResearchObj->db);
				}

			//	}
					echo "\n	End Initializing Research Tables\n";			
		}			
?>