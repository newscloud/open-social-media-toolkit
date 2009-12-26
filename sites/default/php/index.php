<?php
	/* configure constants */	
	// check for constants.php file existence
	if (!defined('SITE_PATH_NAME')) {
		if (file_exists('../constants.php')) {
			include_once('../constants.php');
		} else {
			die('Please copy /sites/default/constants_sample.php to constants.php and change the settings to match your application and environment.');
		}		
	}
	/* include shared index file */
	include_once(PATH_PHP.'/index.php');
?>