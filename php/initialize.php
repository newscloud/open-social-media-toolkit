<?php 
	/* initialize database */
	require_once (PATH_CORE.'classes/db.class.php');
	$db=new cloudDatabase();
	
	if (INIT_SESSION) {
		// set up user info as a data structure
		session_start();
		if (isset($_SESSION['userid'])) {
			$db->ui->isLoggedIn=true;
			$db->ui->ncUid=$_SESSION['ncUid'];
			$db->ui->userid=$_SESSION['userid'];
			$db->ui->memberName=$_SESSION['memberName'];
			$db->ui->votePower=$_SESSION['votePower'];	
		} else {
			$db->ui->isLoggedIn=false;
			$db->ui->userid=0;
			$db->ui->ncUid=0;
			$db->ui->memberName='';
			$db->ui->votePower=0;
		}
	}

	// must follow session initialization
	if (INIT_COMMON) {
		require_once ('classes/common.class.php');
		$common=new common($db);	
	}

	if (INIT_PAGE) {
		require_once (PATH_PHP.'/classes/page.class.php');
		$page=new newsroomPage();
		
		if (INIT_AJAX) {			
			$page->addScript('http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js');
			$page->addScript('http://ajax.googleapis.com/ajax/libs/scriptaculous/1.8.1/scriptaculous.js');
		}
	} else if (INIT_MOBILE_PAGE) {
		require_once (PATH_CORE.'/classes/pageMobile.class.php');
		$page=new MobilePage(SITE_TITLE);
	}
	

?>