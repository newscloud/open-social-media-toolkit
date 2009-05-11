<?php
	/* mobile page generator */
	
	/* configure constants */
	include_once('../../constants.php');

	/* site request handler */
	if (isset($_GET['p']))
		$p=$_GET['p'];
	else
		$p='default';
	switch ($p) {
		default:
			include_once(PATH_PHP.'/mobileHome.php');
		break;
	}	
?>