<?php
	// sync the log
	// this generates the outbound log file for the remote server
	$error=false;
	$errorMsg='';
	/* Process incoming variable requests */	
	if (isset($_GET['apiKey'])) {
		$apiKey=$_GET['apiKey'];
	} else {
		$error=true;
		$errorMsg='Missing API Key';
	}
	if (isset($_GET['timestamp'])) {
		$timestamp=$_GET['timestamp'];
	} else {
		$timestamp=time()-(7*24*3600); // get one week worth 
	}
	// verify api key
	if ($init['apiKey']<>$apiKey) {
		$error=true;
		$errorMsg='Invalid API Key';
	} else {	
		require_once PATH_CORE."/classes/log.class.php";
		$logObj=new log($db);
		$result=$logObj->transmit($timestamp);
	}
	if (!$error) {
		$response='<response><result>ok</result><transmission>'.$result.'</transmission></response>';
	} else {
		$response='<response><result>error</result><msg>'.$errorMsg.'</msg></response>';		
	}
	echo $response;
?>