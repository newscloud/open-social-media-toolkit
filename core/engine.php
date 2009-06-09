<?php
	// process cron jobs for cloud api
	echo 'Starting CRON engine.<br />';	
	/* Process incoming variable requests */	
	if (isset($_GET['apiKey'])) {
		$apiKey=$_GET['apiKey'];
	} else {
		echo 'missing apiKey';
		die();
	}
	// verify api key
	if ($init['apiKey']<>$apiKey) {
		echo 'Invalid access!';
		die();
	}

	// override default time limit
	set_time_limit(300);
	
	require_once(PATH_CORE.'/classes/cron.class.php');
	$cObj=new cron($init['apiKey']);
	// when you set init variable, we populate the job list for the first time
	if (isset($_GET['init'])) {
		echo 'Initialize cron jobs...';
		$cObj->initJobs();
		$cObj->resetJobs();
		$cObj->fetchJobs();
		echo 'exiting now...';
		exit;		
	}
	if (isset($_GET['reset'])) {
		echo 'Reset cron jobs...';
		$cObj->resetJobs();
		echo 'exiting now...';
		exit;		
	}
	if (isset($_GET['force'])) {
		// only run this one job
		$cObj->forceJob($_GET['force']);
	} else {
		// run the jobs
		$cObj->fetchJobs();
	}
	$cObj->hasDeadTasks();				
	echo 'Exiting CRON engine.<br />';
?>