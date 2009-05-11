<?php

class MobilePage extends XHTMLpage {
	var $header = '';
	var $sidebar = '';
	var $content = '';
	var $footer = '';
	var $agent;
	var $googleAnalytics;
	var $keywordDescription;
	var $keywordList;
	var $isIE=false;
	
	function MobilePage($title='NewsCloud.com')
	{			
		// Construct the parent
		parent::XHTMLPage($title);	
		// main layout file
		$this->agent=$_SERVER['HTTP_USER_AGENT'];
		$this->isIE=(eregi("msie",$this->agent) && !eregi("opera",$this->agent));
		$this->googleAnalytics='<script type="text/javascript"> var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www."); document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));</script><script type="text/javascript"> var pageTracker = _gat._getTracker("'.$init[analytics].'"); pageTracker._initData(); pageTracker._trackPageview(); </script>';
		$this->keywordDescription='';
		$this->keywordList='';		
	}
	
	function display()
	{
		$this->add( $this->_genMain() );
		echo $this->getPage();
	}
	
	function addToHeader($string)
	{
		$this->header .= $string;
	}
	function addToContent($string)
	{
		$this->content .= $string;
	}
	function addToFooter($string)
	{
		$this->footer .= $string;
	}
		
	function _genMain()
	{
		$ret = '<div id="main"><!-- main -->';
		$ret .= $this->_genHeader();
		$ret .= $this->_genContent();
		$ret .= $this->_genFooter();		
		$ret .= '<!-- /main --></div>';
		return $ret;
	}

	function _genHeader()
	{
		return '<div id="header"><!-- header -->' . $this->header . '</div><!-- /header -->';
	}

	function _genContent()
	{
		return '<div id="content"><!-- content -->' . $this->content . '</div><!-- /content -->';
	}

	function _genFooter()
	{
		return '<div id="footer"><!-- footer -->'.$this->footer.'</div><!-- /footer -->';
	}
}

class XHTMLpage {
	
	var $title = '';
	var $description = '';
	var $siteTagline='Must read stories from around the Web';
	var $html = '';
	
	var $doctype_tag = '';
	var $onload = '';
	
	var $keywords    = array();
	var $stylesheets = array();
	var $scripts     = array();
	var $miscHead	 = array();
	var $rssfeeds	 = array();
	var $atomfeeds	 = array();
		
	var $page_time_start;
	
	function XHTMLPage($title = '')
	{
		$this->page_time_start = microtime();
		
		$this->setTitle($title);
		$this->doctype_tag = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	}
	
	function getPage()
	{
		$page = $this->doctype_tag;
		$page .= '<html>';
		$page .= $this->_genHead();
		$page .= $this->_genBody();		
		return $page . '</html><!-- Page built in ' . $this->getCurrentExecTime() . ' seconds. --> ';
	}
	
	function display()
	{
		echo $this->getPage();
	}
	
	function add($HTML)
	{
		$this->html .= $HTML;
	}
	
	function addStyle($stylesheet_path)
	{
		$this->stylesheets[] = trim($stylesheet_path);
	}
	
	function addHead($miscStr)
	{
		$this->miscHead[] = trim($miscStr);
	}
	
	function addScript($script_path)
	{
		$this->scripts[] = trim($script_path);		
	}
	
	function pkgScripts($page='default',$scripts) {		
		$this->scripts[]=URL_CACHE."&cf=".$page."_".$this->fetchPkgVersion($page,$scripts,'js',true).".js";
	}

	function pkgStyles($page='default',$sheets) {
		$this->stylesheets[]=URL_CACHE."&cf=".$page."_".$this->fetchPkgVersion($page,$sheets,'css',false).".css";
	}

	function fetchPkgVersion($page,$files,$mode='js',$jsCompress=false) {
	   $sDocRoot = $_SERVER['DOCUMENT_ROOT'];
	   define('JSMIN_COMMENTS', ''); // any comments to append to the top of the compressed output
	   define('JSMIN_AS_LIB', true); 
	  // get file last modified dates
	  $aLastModifieds = array();
	  foreach ($files as $sFile) {
	     $aLastModifieds[] = filemtime("$sDocRoot/$sFile");
	  }
	  // sort dates, newest first
	  rsort($aLastModifieds);
 	 $iETag=$aLastModifieds[0];	
       // create a directory for storing current and archive versions
      if (!is_dir("$sDocRoot/".ARCHIVE_FOLDER)) {
         mkdir("$sDocRoot/".ARCHIVE_FOLDER);
      }
      
      $sMergedFilename = "$sDocRoot/".ARCHIVE_FOLDER."/".$page."_".$iETag.".".$mode;
      // if it does not exist, we need to create a new merged package
      if (!file_exists($sMergedFilename)) {
         // get and merge code
         $sCode = '';
         $aLastModifieds = array();
         foreach ($files as $sFile) {
            $aLastModifieds[] = filemtime("$sDocRoot/$sFile");
            $sCode .= file_get_contents("$sDocRoot/$sFile");
         }
         // sort dates, newest first
         rsort($aLastModifieds);
         // reset iETag incase of late breaking file update
	 	 $iETag=$aLastModifieds[0];         
	      $sMergedFilename = "$sDocRoot/".ARCHIVE_FOLDER."/".$page."_".$iETag.".".$mode;
           $this->pkgWrite($sMergedFilename, $sCode);
           if ($jsCompress) {
			  require_once("$sDocRoot/".JSMIN_PATH."/jsmin.php");
              if (JSMIN_COMMENTS != '') {
                 $jsMin = new JSMin(file_get_contents($sMergedFilename), false, JSMIN_COMMENTS);
              } else {
                 $jsMin = new JSMin(file_get_contents($sMergedFilename), false);
              }
              $sCode = $jsMin->minify();              
              $this->pkgWrite($sMergedFilename, $sCode);
           }
      }
	  // return latest timestamp
	 return $iETag;
	}	
	
   function pkgWrite($sFilename, $sCode) {
      $oFile = fopen($sFilename, 'w');
      if (flock($oFile, LOCK_EX)) {
         fwrite($oFile, $sCode);
         flock($oFile, LOCK_UN);
      }
      fclose($oFile);
   }

	function addRSSFeed($rssfeed_path)
	{
		$this->rssfeeds[] = trim($rssfeed_path);
	}

	function addATOMFeed($atomfeed_path)
	{
		$this->atomfeeds[] = trim($atomfeed_path);
	}
	
	function setTitle($title='NewsCloud.com')
	{
		if ($title=='NewsCloud.com') {
			$title.=' - '.$this->siteTagline;
		}
		$this->title = $title;
	}
	
	function setDescription($description)
	{
		$this->description = strip_tags($description);
	}
	
	function setOnload($string)
	{
		$this->onload = $string;
	}

	function addKeywords($keywords)
	/**
	 * Takes either an array or a comma/whitespace seperated list of keywords
	 * and appends them to the local keywords array property
	 */
	{
		if ( is_array($keywords) ) {
			$new_array = $keywords;
		} else {
			$new_array = preg_split("([[:space:]]|,|;)", $keywords, -1, PREG_SPLIT_NO_EMPTY);
		}
		$this->keywords = array_unique(array_merge( $new_array,$this->keywords));
	}
	
	function _genHead()
	{
		$head = '<head><meta name="viewport" content="width=320"><meta HTTP-EQUIV="Refresh" CONTENT="1800"><meta HTTP-EQUIV="Expires" CONTENT="now"><meta http-equiv="Content-type" content="text/html;charset=UTF-8" />';
		$head .= $this->_genTitle();
		$head .= $this->_genDescription();
		$head .= $this->_genKeywords();
		$head .= $this->_genStylesheets();
		$head .= $this->_genFeeds();
		$head .= $this->_genMiscHead();
		$head .= $this->_genScripts();		
		return $head . '</head>';
	}
	
	function _genBody()
	{
		$body = '<body onload="' . $this->onload . '">';
		if (!strpos($_SERVER['HTTP_HOST'],'local'))
			$body.=$this->googleAnalytics;
		$body .= $this->html;
		return $body.'</body>';
	}
	
	function _genTitle()
	{
		$title = '<title>' . $this->title . '</title>';
		return $title;
	}
	
	function _genDescription()
	{
		return '<meta name="description" content="' . trim($this->description) . '" />';
	}
	
	function _genKeywords()
	{
		$words = '';
		foreach ($this->keywords as $key => $val) {
			$words .= $val . ", ";
		}
		$tag = '<meta name="keywords" content="' . substr($words, 0, -2) . '" />';
		return $tag;
	}

	function _genStylesheets()
	{
		$ret = '';
		foreach (array_unique($this->stylesheets) as $key => $val) {
			$ret .= '<link rel="stylesheet" href="' . $val . '" type="text/css" charset="utf-8" />';
		}
		$ret.='<link rel="Shortcut Icon" href="http://www.newscloud.com/images/favicon.ico" type="image/x-icon" />';
		return $ret;
	}

	function _genMiscHead()
	{
		$ret = '';
		foreach (array_unique($this->miscHead) as $key => $val) {
			$ret .= $val;
		}
		return $ret;
	}
	
	function _genFeeds() {
		$ret = '';
		foreach (array_unique($this->rssfeeds) as $key => $val) {
			$ret .= '<link rel="alternate" title="RSS" href="' . $val . '" type="application/rss+xml">';
		}
		foreach (array_unique($this->atomfeeds) as $key => $val) {
			$ret .= '<link rel="alternate" title="ATOM" href="' . $val . '" type="application/atom+xml">';
		}
		return $ret;		
	}
	
	function _genScripts()
	{
		$ret = '';
		foreach (array_unique($this->scripts) as $key => $val) {
			$ret .= '<script src="' . $val . '" type="text/javascript" language="javascript" charset="utf-8"></script>';			
		}

		return $ret;
	}
	
	function getCurrentExecTime()
	{
	    $page_time_end = microtime();
		return $page_time_end - $this->page_time_start;
	}
	
	function isRobot() {
		return (eregi("googlebot",$this->agent) || eregi("yahooseeker",$this->agent) || eregi("msnbot",$this->agent));		
	}
}
?>