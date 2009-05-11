<?php

class photos {
	
	var $db;
		
	function __construct(&$db=NULL) 
	{
		
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
			
	//	$this->setupLibraries();
			
	}

	/*function fetchRecorder($service='seesmic',$videoid='') {
		switch ($service) {
			case 'seesmic':
				$code.='<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=seesmic&videoid='.$videoid.'" frameborder="0" width="400" height="400" scrolling="no" resizable="false"></fb:iframe>';		
			break;					
		}
		return $code;
	}
	*/
	
	/*function fetchPhotoUploadForm()
	{
		$code .= '
		            <br />Select your photo<br />
		            <input name="photo1" type="file" id="photo1">';
                                               
   	}
*/
	
	
	
	
}



require_once(PATH_CORE.'/classes/dbRowObject.class.php');


class Photo extends dbRowObject 
{
 
}

class PhotoTable 
{
	var $db;
	static $tablename="Photos";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "Photo";
	
	static $fields = array(		
			//"service"			=>"ENUM ('youtube','seesmic','facebook') default 'youtube'",
			"title"				=>"VARCHAR(255) default ''",
			"shortName"			=>"VARCHAR(25) default ''",
			"description"		=>"TEXT default ''",
			"dateCreated"		=>"DATETIME",
			"userid"			=>"INT(11) default 0",
			"status"			=>"ENUM ('approved','pending','blocked') default 'pending'",	
			"filename"			=>"VARCHAR(255)", // uuhhh oops? was i supposed to use this instead?
			"challengeCompletedId"		=>"INT(11) unsigned",
			"localFilename" 	=> "VARCHAR(255) default ''" // relative to submissions path for now
			);
	static $keydefinitions = array(); 	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
			
	function __construct(&$db=NULL) 
	{

		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db; 
	}
	
	// although many functions will be duplicated between table subclasses, having a parent class gets too messy
	
	function getRowObject()
	{
		$classname = self::$dbRowObjectClass; 
		return new $classname($this->db, self::$tablename, array_keys(self::$fields), self::$idname); 
	}
	
	// generic table creation routine, same for all *Table classes 		
	static function createTable($manageObj)
	{			
		$manageObj->addTable(self::$tablename,self::$idname,self::$idtype,"MyISAM");
		foreach (array_keys(self::$fields) as $key)
		{
			$manageObj->updateAddColumn(self::$tablename,$key,self::$fields[$key]);
		}	
		foreach (self::$keydefinitions as $keydef)
		{
			$manageObj->updateAddKey(self::$tablename,$keydef[0], $keydef[1], $keydef[2], $keydef[3]);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	
	function checkPhotoExistsById($id)
  	{
  		
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE id='$id'");
		return $chkDup;
  		
  	}

	function testPopulate()
	{
		
		echo '<p>PhotoTable::testPopulate()</p>';
		$video = $this->getRowObject();
		

		$video->title = 'sample photo submission';
	//	$video->service = 'seesmic';
		$video->shortName = 'photo1';
		$video->description = 'this is a submitted photo response';
		$video->dateCreated = date('Y-m-d H:i:s', time());
		$video->userid = 56;
		$video->status='pending';
	}
	
	function createPhotoForCompletedChallenge($userid, $challengeCompletedId, $filename, $title)
	{
		$photo = self::getRowObject();
		$photo->title = $title;
		$photo->userid = $userid;
		$photo->challengeCompletedId = $challengeCompletedId;
		$photo->localFilename = $filename;
		$photo->dateCreated = date('Y-m-d H:i:s', time());
		$photo->status = 'pending';
		
		return $photo->insert();
		
	}
	
	function getPhotosForCompletedChallenge($completedid)
	{
		
		//$db->setDebug(true);
		$q=$this->db->queryC("SELECT id FROM Photos WHERE challengeCompletedId=$completedid"); // invited within the invite interval means we want to exclude them

		$ids = array();
				
		if ($q) 
		{
			while($row = mysql_fetch_array($q)) // bit of a hack, but too lazy to look up exact behavior of the read() functions in this situation - just copied from db::processTemplates
				$ids []= $row[0];
		}
		return $ids;
		
	}
}


/////////////////
// photo uploader -- adapted by me
require_once PATH_FACEBOOK . '/lib/facebook.php';
	

	///////////////////////////////////////////////////////////////////
// snagged from http://wiki.auzigog.com/Facebook_Photo_Uploads
/*----------------------------------------------------------------------
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; version 2 of the License.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
----------------------------------------------------------------------*/



/*class FacebookPhotos extends Facebook {
  public function __construct($api_key, $secret) {
    $this->api_key = $api_key;
    $this->secret = $secret;

    $this->api_client = new FacebookPhotosRestClient($api_key, $secret);

    $this->validate_fb_params();
  }
}

class FacebookPhotosRestClient extends FacebookRestClient {
	*/

class FacebookPhotoUploader extends FacebookRestClient
{
		
  /**
   * Creates and returns a new album owned by the current session user.
   * @param string $name the album name
   * @param string $location Optional: the album location
   * @param string $description Optional: the album description
   * @return string a new album owned by the current session user
   */
  public function photos_createAlbum($name, $location = null, $description = null) 
  {
    return $this->call_method('facebook.photos.createAlbum',
      array('name' => $name,
            'location' => $location,
            'description' => $description));
  }
  
  /**
   * Uploads a photo owned by the current session user and returns the new photo.
   * @param integer $aid Optional: the album id of the destination album. If 
   *        no album is specified, the photo will be uploaded to a default
   *        album for the application, which will be created if necessary. 
   *        Regular albums have a size limit of 60 photos. Default 
   *        application albums have a size limit of 1000 photos.
   * http://developers.facebook.com/documentation.php?method=photos.upload
   * for more information.
   * @param string $caption Optional: the caption of the photo
   * @param string $image_url the url of the image you want to upload
   * @return string urls of the resulting image on Facebook's servers
   */
  public function photos_upload($filename, $aid = null, $caption = null) {
    return $this->call_method('facebook.photos.upload',
      array('filename' => $filename,
	        'aid' => $aid,
            'caption' => $caption));
  }
  
  public function post_request($method, $params) {
    $params['method'] = $method;
    $params['session_key'] = $this->session_key;
    $params['api_key'] = $this->api_key;
    $params['call_id'] = microtime(true);
    if ($params['call_id'] <= $this->last_call_id) {
      $params['call_id'] = $this->last_call_id + 0.001;
    }
    $this->last_call_id = $params['call_id'];
    if (!isset($params['v'])) {
      $params['v'] = '1.0';
    }
    
    foreach ($params as $key => $val) if (is_array($val)) $params[$key] = implode(',', $val);
    $secret = $this->secret;
    $params['sig'] = Facebook::generate_sig($params, $secret);
    
    $boundary = md5(time());
    $content = array();
    $content[] = '--' . $boundary;
    foreach ($params as $key => $val) {
      $content[] = 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n" . 
                  $val . "\r\n--" . $boundary;
    }
    if ($params['filename']) {
      $filename = $params['filename'];
      preg_match('/.*?\.([a-zA-Z]+)/', $filename, $match);
      $type = strtolower($match[1]);
      if ($type == 'jpg') $type = 'jpeg';
      
      $content[] = 'Content-Disposition: form-data; filename="' . $filename . '"' . "\r\n" . 
                   'Content-Type: image/' . $type . "\r\n\r\n" . 
                   file_get_contents($filename) . "\r\n--" . $boundary;
    }
    $content[] = array_pop($content) . '--';
    $content = implode("\r\n", $content);
    
	if (function_exists('curl_init')) {
		$url = parse_url($this->server_addr);
		
		$header = array('User-Agent: Facebook Photo API PHP5 Client 1.0 ' . phpversion(),
		'Content-Type: multipart/form-data; boundary=' . $boundary,
		'MIME-version: 1.0',
		'Content-Length: '. (strlen($content)) );
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->server_addr);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$result = curl_exec($ch);
		curl_close($ch);
	} else {
	    $header = 'User-Agent: Facebook Photo API PHP5 Client 1.0 ' . phpversion() . "\r\n" .
	              'Content-Type: multipart/form-data; boundary=' . $boundary . "\r\n" .
	              'MIME-version: 1.0' . "\r\n" .
	              'Content-length: ' . strlen($content) . "\r\n" .
	              'Keep-Alive: 300' . "\r\n" .
	              'Connection: keep-alive';
					
	    if (function_exists('fsockopen')) {
	      $url = parse_url($this->server_addr);
	      $sock = @fsockopen($url['host'], 80, $errno, $errstr, 5);
	      $header = 'POST ' . $url['path'] . ' HTTP/1.1' . "\r\n" .
	                'Host: ' . $url['host'] . "\r\n" . 
	                $header;
	      
	      fwrite($sock, $header . "\r\n\r\n" . $content);
	    }
	    else {
	      $context = array('http' => array('method' => 'POST',
	                                       'header' => $header,
	                                       'content' => $content));
	      $contextid=stream_context_create($context);
	      $sock = fopen($this->server_addr, 'r', false, $contextid);
	    }
	    if ($sock) {
	      $result='';
	      while (!feof($sock)) {
	        $temp = fgets($sock, 4096);
	        $result .= $temp;
	        if (!$temp) break; //wtf facebook? return feof already...
	      }
	      fclose($sock);
	    }
	}
    preg_match('/<.*>/s', $result, $match);
    return $match[0];
  }
}

	
	

?>