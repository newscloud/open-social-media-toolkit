<?php
	/* Cache file server */
	// serves CSS and JS files via HTTP

	if (isset($_GET['cf'])) {
		$cf=$_GET['cf'];
		// to do - reroute back to path_cache after we set up css and js packaging
		if (isset($_GET['type']) AND $_GET['type']=='css') {
			exportFile(PATH_CACHE.'/'.$cf);		
		} else 
			exportFile(PATH_CACHE.'/'.$cf);		
	} else if (isset($_GET['simg'])) 
	{
		$img=$_GET['simg'];	
		exportFile(PATH_SITE_IMAGES.'/'.$img);			
	} else if (isset($_GET['img'])) 
	{
		$img=$_GET['img'];	
		exportFile(PATH_IMAGES.'/'.$img);
	} else if (isset($_GET['pdf'])) {
		$pdf=$_GET['pdf'];	
		exportFile(PATH_TEMPLATES.'/'.$pdf.'.pdf');
	} else if (isset($_GET['erd'])) {
		//define ('PATH_TMP',SRC_ROOT.'/sites/tmp/');
		$erd=rawurldecode($_GET['erd']);	// export research data
/*
		if (file_exists($erd)) {
			$pi=pathinfo($erd);
			var_dump($pi);
		} else {
			echo $erd.'does not exst'; 
		}		*/
		exportFile($erd);
		unlink($erd);
	} else if (isset($_GET['m'])) {
		$m=$_GET['m'];
		switch ($m) {
			case 'widget':
				// local iframe - from widget
				if (isset($_GET['id'])) {
					if (!is_numeric($_GET['id'])) exit();
					require_once(PATH_CORE.'/classes/widgets.class.php');
					$wt=new WidgetsTable();					
					$code.=$wt->fetchWidgetCode($_GET['id']);								
				} else {
					$code='Error No widget specified.';
				}				
			break;
			case 'bookmarklet':
				require_once(PATH_CORE.'/classes/dynamicTemplate.class.php');
				$dynTemp = dynamicTemplate::getInstance();	
				include(PATH_TEMPLATES.'/bookmarklet.php');
			break;
			case 'ad':
				$locale=$_GET['locale'];
				if (strlen($locale)>35) exit;
				// deliver ad in iframe
				require_once(PATH_CORE.'/classes/adCode.class.php');
				$adObj=new AdCodeTable();
				$code=$adObj->fetch($locale);			
			break;
			case 'scaleImg':
				// scale image request
				require_once(PATH_CORE.'/classes/images.class.php');
				$img=new images();
				$img->fetchImage();
				exit;	
			break;
		}
		echo $code;
	}
	exit;
	
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
				case 'gif':
				case 'png':
				case 'jpg':
					$contentType='image/'.$pi['extension'];
				break;
				case 'pdf':
					$contentType='application/pdf';
					header('Content-Disposition: attachment; filename="'.$pi['filename'].'.'.$pi['extension'].'"');					
				break;
				case 'txt':
					$contentType='text/plain';					
					header('Content-Disposition: attachment; filename="'.$pi['filename'].'.'.$pi['extension'].'"');					
				break;
			}
		    header("Content-type: ".$contentType);
			readfile($path);
		} else {
		//	cachelog('cache - file not found: '.$path );
			die(/*'cache - file not found: '.$path*/);
		}
	}	
	/*
	// djm debugging, remove later
	function cachelog($str='Empty log string',$filename=PATH_LOGFILE) {
		// write to newscloud log file for debugging
		// must touch and permission file at PATH_LOGFILE for this to work		
		$fHandle=fopen($filename,'a');
		if ($fHandle!==false) {
			if (!is_object($str) AND !is_array($str)) {
				fwrite($fHandle,$str."\n");				
			} else {
				fwrite($fHandle,print_r($str,true)."\n");
			}			
			fclose($fHandle);
		}
	}*/
	
?>