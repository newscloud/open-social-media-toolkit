<?php
	/* initialize database and libraries */
	include_once ('initialize.php');

	/* process request variables */
	$error=false;
	if (isset($_GET['x']))
		$x=$_GET['x'];
	else
		$error=true;

	/* begin building the page */
	// convert tinyurl string to decimal
	$siteContentId=base_convert($x,36,10);
	$webpage_result = $db->queryC("SELECT siteContentId,permalink FROM Content WHERE siteContentId=$siteContentId LIMIT 1;");	
	if (TWITTER_MODULE_TARGET=='PHP') {
		if ($webpage_result===false OR $error) 
			header('Location: '.URL_HOME);
		else {
			$data = $db->readQ($webpage_result);
			header('Location: '.URL_HOME.'?p=readStory&permalink='.$data->permalink.'&viaTwitter');					
		}
	} else {
		// Facebook
		if ($webpage_result===false OR $error) 
			header('Location: '.URL_CANVAS);
		else {
			$data = $db->readQ($webpage_result);
			header('Location: '.URL_CANVAS.'?p=read&cid='.$data->siteContentId.'&viaTwitter');					
		}		
	}
?>