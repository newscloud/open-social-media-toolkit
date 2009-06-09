<?php
	/* Cache file server */
	// serves CSS and JS files via HTTP
	if (isset($_GET['cf'])) {
		$cf=$_GET['cf'];
		exportFile(PATH_CACHE.'/'.$cf);		
	}
	
	function exportFile($path='')
	{
		if (file_exists($path)) {
			$pi=pathinfo($path); // ,PATHINFO_EXTENSION
			switch ($pi['extension']) {
				default:
					$contentType='text/html';
				break;
				case 'css':
					$contentType='text/css';
				break;
				case 'js':
					$contentType='text/javascript';
				break;
			}
		    header("Content-type: ".$contentType);
			readfile($path);
		} else {
			die();
		}
	}	
	
?>