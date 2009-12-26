<?php
	if (file_exists('./constants.php')) {
		include_once('./constants.php');
	} else {
		die('Please copy /sites/default/constants_sample.php to constants.php and change the settings to match your application and environment.');
	}
	include_once ('./php/index.php');
?>