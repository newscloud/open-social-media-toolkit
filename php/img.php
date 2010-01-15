<?php
	/* Image server */
	// serves image files via HTTP
	if (isset($_GET['img'])) {
		$img=$_GET['img'];
		streamImage(PATH_PHP_IMAGES.$img);		
	}
	
	function streamImage($path='')
	{
		if (file_exists($path)) {
			$pi=pathinfo($path); // ,PATHINFO_EXTENSION
			switch ($pi['extension']) {
				default:
					$contentType='image/jpeg';
				break;
				case 'gif':
					$contentType='image/gif';
				break;
				case 'png':
					$contentType='image/png';
				break;
			}		
		    header("Content-type: ".$contentType);
			readfile($path);
		} else 
			die();
	}	
?>