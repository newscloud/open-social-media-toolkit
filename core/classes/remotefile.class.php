<?php

class remotePageProperty extends remoteFileProperty {
	/**
	 * Get images from remote page
	 * get title from remote page
	 */
	var $parsed_response = array();
	var $page_url = "";
	var $page_content = "";
	var $page_parsed_xml = array();

	function remotePageProperty($url, $open_socket_on_instantiation = true, $timeout = 10)
	{
		$this->page_url = $url;
		if ($open_socket_on_instantiation) {
			$this->openPage();
		}
	}
	
	function openPage() {
		// construct parent class
		$this->remoteFileProperty($this->page_url, true);
		// parse the response headers and store as an attribute
		$this->parsed_response = $this->_parseResponseHeader($this->headers_raw);
		// now get the content
		$this->_getPageContent();
	}
	
	function getPageTitle() 
	{
		preg_match_all("/<title>([^<]+)<\/title>/i", $this->page_content, $matches);
		//var_dump($matches);
		if ($matches[1][0]) {
			return $matches[1][0];
		} else {
			return false;
		}
	}

	function getPageFeed() 
	{
		preg_match_all("/<link rel\=\"alternate\" type\=\"application\/rss[\+|\ ]xml\" (title=\"[^\"]+\"\ ){0,1}href=\"([^\"]+)\"/i", $this->page_content, $matches);
		if ($matches[2][0]) {
			return $matches[2][0];
		} else {
			return false;
		}
	}

	function getPageImages($dropQueryString=false)
	{
		$allimages = $this->_parsePageImages();
		$ret = array();
		foreach ($allimages as $key => $attrib) {
			$src = trim($attrib['src']);
			$p = parse_url($src);
			//var_dump($p);
			if ( isset($p['scheme']) && isset($p['host']) ) {
				$src = $src;
			} else if ( isset($p['path']) && isset($p['query']) && !$dropQueryString) {
				$src = $this->parsed_url['scheme'] . "://" . $this->parsed_url['host'] .  $p['path'] . "?" . $p['query'];
			} else if (isset($p['path']) && isset($p['query']) && $dropQueryString) {
				$src = $this->parsed_url['scheme'] . "://" . $this->parsed_url['host'] .  $p['path'];
			} else if ( isset($p['path']) && !isset($p['query'])) {
				$src = $this->parsed_url['scheme'] . "://" . $this->parsed_url['host'] .  $p['path'];
			}
			$ret[] = $src; // . "HOST: " . $this->parsed_url['host'];
		}
		
		// remove duplicates
		$ret = array_unique($ret);
		// get only JPGs
		$ret = preg_grep("/\.jpg$/", $ret);
		
		return $ret;
	}	

	function _parsePageImages()
	// returns a nested array of page images, with attributes 
	// urls will need to be cleaned up by another function, hence: _private
	{
		$images = array();
		$stickem = array();
		// regex finds image elements
		// BUG: IS CASE-SENSITIVE
		preg_match_all("/<img([^>]+)/i", $this->page_content, $matches);
		foreach ($matches[1] as $key => $val) {
			// regex finds 'attribute=value'
			preg_match_all("/([\w]+)=([^\s]+)/i", $val, $att_matches);
			foreach ($att_matches[1] as $key => $attribute) {
				$stickem[$attribute] = $this->cleanQuotes($att_matches[2][$key]); // that last ugly is the value
			}
			// only count it if the 'src' attribute is set
			if (isset($stickem['src'])) {
				$images[] = $stickem;
			}
		}
		return $images;
	}
	
	function _repairImageURL($url) 
	{
		//$uri = 'http://some-domain-name.org';
		if(preg_match('/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}((:[0-9]{1,5})?\/.*)?$/i', $url)) {
  			// url looks ok, return unmodified
  			return $url;
		} else {
  			
  		}
	}
	
	function cleanQuotes($string) 
	{
		return trim(trim(trim($string), "'"), '"'); 
	}
	
	function _getPageContent($timeout = 10)
	/**
	 *
	 */ 
	{	
		$handle = fopen($this->page_url, "r");
		if ($handle) {
   			while (!feof($handle)) {
       			$this->page_content .= fgets($handle, 4096);
   			}
   		fclose($handle);
  		} else {
  			$this->parsed_reponse['status'] == 'DEAD';
   			//die( "fopen failed for $filename" ) ; // problem opening url
  		}
	}

	
	function isAlive()
	/**
	 * checks for basic aliveness only -- doesn't account for redirects
	 */
	{
		if ($this->parsed_response['status'] == 'OK') {
			return true;
		} else {
			return false;
		} 
	}
	
	function hasMoved()
	{
		if ($this->parsed_response['response_class'] == '3') {
			return true;
		} else {
			return false;
		}
	}
	
	function getRedirect()
	{
		if (isset($this->headers['Location']) && $this->hasMoved())	 {
			return $this->headers['Location'];
		} else {
			return false;
		}
	}
	
	function _parseResponseHeader($headerstring) 
	{
		$ret = array();
		preg_match_all("/^HTTP\/([^\s]+)\s([0-9]+)\s([^\s]+)/", $headerstring, $matches);
		$ret['version']  = $matches[1][0];
		$ret['response'] = $matches[2][0];
		$ret['response_class'] = substr($matches[2][0], 0, 1);
		$ret['status']   = $matches[3][0];
		
		return $ret;
	}

}

class remoteFileLinkStatus extends remoteFileProperty {
	/**
	 * class to get further status of a remote file, including redirects
	 */
	var $parsed_response = array();
	
	function remoteFileLinkStatus($url, $live = true, $timeout = 10)
	{
		// construct parent class
		$this->remoteFileProperty($url, $live, $timeout);
		// parse the response headers and store as an attribute
		$this->parsed_response = $this->_parseResponseHeader($this->headers_raw);
	}
	
	function isAlive()
	/**
	 * checks for basic aliveness only -- doesn't account for redirects
	 */
	{
		if ($this->parsed_response['status'] == 'OK') {
			return true;
		} else {
			return false;
		} 
	}
	
	function hasMoved()
	{
		if ($this->parsed_response['response_class'] == '3') {
			return true;
		} else {
			return false;
		}
	}
	
	function getRedirect()
	{
		if (isset($this->headers['Location']) && $this->hasMoved())	 {
			return $this->headers['Location'];
		} else {
			return false;
		}
	}
	
	function _parseResponseHeader($headerstring) 
	{
		$ret = array();
		preg_match_all("/^HTTP\/([^\s]+)\s([0-9]+)\s([^\s]+)/", $headerstring, $matches);
		$ret['version']  = $matches[1][0];
		$ret['response'] = $matches[2][0];
		$ret['response_class'] = substr($matches[2][0], 0, 1);
		$ret['status']   = $matches[3][0];
		
		return $ret;
	}
	
}

class remoteFileProperty {
	/**
	 * build info for a remote file via HEAD response headers
	 * warning: opens socket when instantiated (speed is dependent on network conditions)
	 */
	var $file_url = "";
	var $parsed_url = array();
	var $headers = array();
	var $headers_raw = "";
	var $error = false;
	
	function remoteFileProperty($url, $open_socket_on_instantiation = true, $timeout = 10)
	{
		$this->file_url = $url;
		$this->parsed_url = parse_url($url); // split url into components
		if ($open_socket_on_instantiation) {
			$this->headers = $this->_getHTTPHeaders($timeout);
		}
	}

	function _getHTTPHeaders($timeout = 10)
	/**
	 * returns an array of all HTTP response headers for the provided url path
	 * format is array{ ['headername'] => ['headervalue'] }
	 * timeout is in seconds, and optional
	 */ 
	{	
		$parsed = $this->parsed_url;
		$ret = array();
		if ( strtolower($parsed['scheme']) == "http" && isset($parsed['host']) ) { 
			$fp = fsockopen($parsed['host'], 80, $errno, $errstr, $timeout);
			if ($fp) {
				// attempt to add path if none
				if ($parsed['path'] == "") $parsed['path'] = "/";
				stream_set_timeout($fp, $timeout);
				// HEAD requests headers only, no content
				fputs($fp,"HEAD " . $parsed['path'] ." HTTP/1.1\r\n");
				fputs($fp,"Host: " . $parsed['host'] . "\r\n");
				fputs($fp,"Connection: close\r\n\r\n");		
				while (!feof($fp)) {
	       			$line = fgets($fp, 128);
	       			// keep a copy of raw response
	       			$this->headers_raw .= $line;
	       			// regex to split returned headers
	       			preg_match_all("/(^[^:]*):([^$]*)/", $line, $matches);
	       			// build output array
	       			if (isset($matches[1][0]) && isset($matches[2][0])) {
		       			$ret[trim($matches[1][0])] = trim($matches[2][0]);       			
	       			}
	       		}
			} else {
				$this->error = "Problem with connection"; 
				return false; // problem with the socket
			} 
	       	return $ret; // all is well, return the array
		} else {
			$this->error = "Problem parsing URL";
			return false; // url is invalid or not http
		}
	}
	
	function getSize()
	/**
	 * return file size int in bytes
	 */
	{
		if ( isset($this->headers['Content-Length']) ) {
			return $this->headers['Content-Length'];
		/*
		} else if ($this->headers['Content-Length'] == 0) {
			return 1;
		*/
		} else {
			return $this->_guessSize();
		}		
	}
	
	function _guessSize()
	{
		$dummy = 1000000;
		return $dummy;
	}
	
	function getMIMEType()
	/*
	 * return MIME type string
	 */ 
	{
		if ( isset($this->headers['Content-Type']) ) {
			return $this->headers['Content-Type'];
		} else {
			// if header is missing type, try guessing
			return $this->_guessMIMEType();
		}
		
	}
	
	function _guessMIMEType() 
	{
		$types = array(
				"wma" => "audio/x-ms-wma", 
				"mp3" => "audio/x-mpeg", 
				"mov" => "video/quicktime", 
				"ram" => "audio/x-pn-realaudio", 
				"wmv" => "video/x-ms-wmv", 
				"aac" => "audio/aac", 
				"aiff" => "audio/x-aiff", 
				"wav" => "audio/x-wav", 
				"asx" => "video/x-ms-asf", 
				"avi" => "video/msvideo", 
				"doc" => "application/msword", 
				"dcr" => "application/x-director", 
				"dmg" => "application/octet-stream", 
				"gif" => "image/gif", 
				"gz" => "application/x-gzip", 
				"jpg" => "image/jpeg", 
				"mp4" => "video/mp4", 
				"mpg" => "video/mpeg", 
				"ogg" => "application/ogg", 
				"pdf" => "application/pdf", 
				"ppt" => "application/vnd.ms-powerpoint", 
				"qt" => "video/quicktime", 
				"ra" => "audio/x-pn-realaudio", 
				"swf" => "application/x-shockwave-flash", 
				"tar" => "application/x-tar", 
				"txt" => "text/plain", 
				"xls" => "application/vnd.ms-excel", 
				"zip" => "application/zip");
				
		// match it with types array
		return $types[$this->getFileExtension()];
	}
	
	function getFileExtension()
	{
		// regex to get ext in 'filename.ext'
		preg_match_all("/\.([^\.]+)$/", $this->parsed_url['path'], $ext_match);
		return $ext_match[1][0];		
	}
	
}
?>