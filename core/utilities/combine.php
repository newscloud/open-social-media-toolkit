<?php
   // Written by Ed Eliot (www.ejeliot.com) - provided as-is, use at your own risk, then customized for NewsCloud
   define('CACHE_LENGTH', 31356000); // length of time to cache output file, default approx 1 year
   define('ARCHIVE_FOLDER', 'cache'); // location to store archive, don't add starting or trailing slashes
	if (isset($_GET['page']) AND isset($_GET['mode']) AND isset($_GET['version'])) {
		$page=$_GET['page'];
		$mode=$_GET['mode'];
		$iETag = (int)$_GET['version'];
	} else die ('Missing arguments');
	if ($mode=='js') {
	   define('FILE_TYPE', 'text/javascript'); 
	} else {
	   define('FILE_TYPE', 'text/css'); 
	}   
  $sLastModified = gmdate('D, d M Y H:i:s', $iETag).' GMT';      
  // see if the user has an updated copy in browser cache
   if (
     (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $sLastModified) ||
     (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $iETag)
  ) {
     header("{$_SERVER['SERVER_PROTOCOL']} 304 Not Modified");
     exit;
  }
   $sDocRoot = $_SERVER['DOCUMENT_ROOT']; 
   $sMergedFilename = "$sDocRoot/".ARCHIVE_FOLDER."/".$page."_".$iETag.".".$mode;
  $sCode = file_get_contents($sMergedFilename);
  // output merged code
  // send HTTP headers to ensure aggressive caching
  $ETag=$page.$mode.$iETag;
  header('Expires: '.gmdate('D, d M Y H:i:s', time() + CACHE_LENGTH).' GMT'); // 1 year from now
  header('Content-Type: '.FILE_TYPE);
  header('Content-Length: '.strlen($sCode));
  header("Last-Modified: $sLastModified");
  header("ETag: $ETag");
  header('Cache-Control: max-age='.CACHE_LENGTH);   
  echo $sCode;
?>