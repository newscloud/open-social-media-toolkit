<?php
	echo 'Beginning database initialization<br />';
	// Set up clouds architecture local database
	require_once(PATH_CORE.'/classes/dbManage.class.php');
	$manageObj=new dbManage(false);
	$manageObj->db->selectDB($init['database']);
	echo 'Using database: '.$init['database'].'<br />';	
	if ($action=='resetDB') {
		echo 'cleaning up tables<br />';
		require_once(PATH_CORE.'/classes/cleanup.class.php');
		$cleanupObj=new cleanup($db,'');
		$cleanupObj->flushDatabase(); 
		echo 'Database cleansed<br />';		
	} else {
		
		//////////////////////////////////////////////////////////////////////////////////////////
		// news-specific tables

		require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
		TemplateTable::createTable($manageObj);
	
		
		// Create the User table
		
		require_once(PATH_CORE.'/classes/user.class.php');
		UserTable::createTable($manageObj);
		
		
		// set up newswire table
		$manageObj->addTable("Newswire","id","INT(11) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("Newswire","title","VARCHAR(255) default ''");
		$manageObj->addColumn("Newswire","caption","TEXT default ''");
		$manageObj->addColumn("Newswire","source","VARCHAR (150) default ''");
		$manageObj->addColumn("Newswire","url","VARCHAR(255) default ''");
		$manageObj->addColumn("Newswire","date","DATETIME");
		$manageObj->addColumn("Newswire","wireid","INT(11) default 0");

		require_once(PATH_CORE.'/classes/content.class.php');
		ContentTable::createTable($manageObj);
		
		require_once(PATH_CORE.'/classes/comments.class.php');
		CommentTable::createTable($manageObj);

		require_once(PATH_CORE.'/classes/cron.class.php');
		CronJobsTable::createTable($manageObj);
			
		// set up system status table
		$manageObj->addTable("SystemStatus","id","INT(4) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("SystemStatus","name","VARCHAR(35) default ''");
		$manageObj->addColumn("SystemStatus","strValue","TEXT default ''");
		$manageObj->addColumn("SystemStatus","numValue","BIGINT(20) default 0");
		
		require_once(PATH_CORE.'/classes/log.class.php');
		LogTable::createTable($manageObj);
			
		// Resources for Folders and Links
		$manageObj->addTable("Folders","id","INT(11) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("Folders","folderid","INT(11) NOT NULL default 0");
		$manageObj->addColumn("Folders","uid","INT(11) NOT NULL default 0");
		$manageObj->addColumn("Folders","title","VARCHAR(50) default ''");
		
		$manageObj->addTable("FolderLinks","id","INT(11) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("FolderLinks","linkid","INT(11) NOT NULL default 0");
		$manageObj->addColumn("FolderLinks","folderid","INT(11) NOT NULL default 0");
		$manageObj->addColumn("FolderLinks","title","VARCHAR(255) default ''");
		$manageObj->addColumn("FolderLinks","url","varchar(255) default ''");
		$manageObj->addColumn("FolderLinks","notes","VARCHAR(255) default ''");
		$manageObj->addColumn('FolderLinks','linkType',"enum ('link','product')");		
		$manageObj->addColumn('FolderLinks','imageUrl',"VARCHAR(255) default '';");		
		
		// database updates for alpha v.11
		

		// Feeds table
		$manageObj->addTable("Feeds","id","INT(11) unsigned NOT NULL auto_increment","MyISAM");
		$manageObj->addColumn("Feeds","wireid","INT(11) default 0");
		$manageObj->addColumn("Feeds","title","VARCHAR(255) default ''");
		$manageObj->addColumn("Feeds","url","VARCHAR(255) default ''");
		$manageObj->addColumn("Feeds","rss","VARCHAR(255) default ''");
		$manageObj->addColumn("Feeds","lastFetch","timestamp");
		$manageObj->addColumn("Feeds","feedType","ENUM('blog','wire') default 'wire'");

		// update newswire table
		$manageObj->addColumn("Newswire","feedType","ENUM('blog','wire') default 'wire'");
		$manageObj->addColumn("Newswire","mediaUrl","VARCHAR(255) default ''");
		$manageObj->addColumn("Newswire","imageUrl","VARCHAR(255) default ''");
		$manageObj->addColumn("Newswire","embed","TEXT");

		echo 'Completed database initialization<br />';
	}
	
	// create Notifications and NotificationMessages Tables
	require_once(PATH_CORE.'/classes/notifications.class.php');
	NotificationsTable::createTable($manageObj);
	NotificationMessagesTable::createTable($manageObj);

	// Set up some default SystemStatus variables
	require_once (PATH_CORE.'/classes/systemStatus.class.php');
	$ssObj=new systemStatus($manageObj->db);
	$ssObj->setState('cloudid',SITE_CLOUDID);
	//$ssObj->name=$this->getState('name');
	//$ssObj->permalink=$this->getState('permalink');
	$ssObj->setState('max_sessions',MAX_SESSIONS_ACTIVE);

	// create UserBlogs
	require_once(PATH_CORE.'/classes/userBlogs.class.php');
	UserBlogsTable::createTable($manageObj);	

	// create Notifications and NotificationMessages Tables
	require_once(PATH_CORE.'/classes/widgets.class.php');
	WidgetsTable::createTable($manageObj);
	FeaturedWidgetsTable::createTable($manageObj);	

	// create AdCode Table
	require_once(PATH_CORE.'/classes/adCode.class.php');
	AdCodeTable::createTable($manageObj);	

	require_once(PATH_CORE.'/classes/subscriptions.class.php');
	SubscriptionsTable::createTable($manageObj);	

	// create ForumTopics Table
	require_once(PATH_CORE.'/classes/forum.class.php');
	ForumTopicsTable::createTable($manageObj);	
		
		if (MODULE_FACEBOOK) {
			include_once(PATH_FACEBOOK.'/utilities/initDatabase.php');		
		}		
	
?>