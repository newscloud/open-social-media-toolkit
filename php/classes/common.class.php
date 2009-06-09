<?php

class common {
			
	var $db;
	
	function common(&$db=NULL)
	{
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;
	}

	function buildHeader() {
		$code='<div class="bar"></div>';
		if (defined('ADS_HOME_LARGE_BANNER')) {
			require_once(PATH_CORE.'/classes/adServer.class.php');
			$adObj=new adServer($this->db);
			$code.='<div class="adLargeBanner">'.$adObj->fetchAd('homeLargeBanner').'</div>';		
		}
		$code.=PHP_HEADER; // PHP_HEADER set in constants
		return $code;
	}

	function buildNavigation($current='Front Page') {
		$tabs='<div class="tabs clearfix"><center><div class="left_tabs"><ul class="toggle_tabs clearfix" id="toggle_tabs_unused">';
		$tabs.='<li class="first"><a href="?p=home" class="'.($current=='Front Page'?'selected':'').'">Front Page</a></li>';
		$tabs.='<li ><a href="?p=upcoming" class="'.($current=='Upcoming'?'selected':'').'">Upcoming Stories</a></li>';
		$tabs.='<li ><a href="?p=newswire" class="'.($current=='Newswire'?'selected':'').'">Newswire</a></li>';
		if (NAV_INCL_BLOGS) {
			$tabs.='<li ><a href="?p=blogs" class="'.($current=='Blogs'?'selected':'').'">Blogs</a></li>';
		}
		$tabs.='<li ><a href="?p=resources" class="'.($current=='Links'?'selected':'').'">Links</a></li>'; 
		$tabs.='<li class="last"><a href="?p=about" class="'.($current=='About'?'selected':'').'">About</a></li>';
		$tabs.='</ul></div>';
		$tabs.='<ul class="auth">';
		if ($this->db->ui->isLoggedIn===false) {
			$tabs.='<li  '.($currentPage=='Sign in'?'class="active"':'').'><a href="?p=signin">Sign in or Register</a></li>';
		} else {
			$tabs.='<li><a href="?p=signin&mode=signOut">Sign out</a></li>';
		}
		$tabs.='</ul></center></div>';
		return $tabs;	
	}
			
	function buildFooter() {
		$temp='<div class="bar"></div><ul>';
		$temp.='<li><li><a href="?p=rss"><img class="feedicon" src="?p=img&img=feed_icon_orange.png" alt="rss feed of front page"/></a> <a href="?p=rss">Top stories</a></li>';
		if (USE_TWITTER) {
			$temp.='<li><a href="http://twitter.com/'.TWITTER_USER.'"><img class="feedicon" src="http://twitter.com/favicon.ico" alt="twitter feed of top stories"/></a> <a href="http://twitter.com/'.TWITTER_USER.'">Twitter</a></li>';
		}		
		$temp.='</ul><ul><li>Powered by the <a href="http://www.newscloud.org/index.php/Social_media_toolkit">NewsCloud Social Media Toolkit</a></li></ul>';
		return $temp;
	}		

	function equalCols($numCols,$outerClass='ecRow',$class1='',$col1='',$class2='',$col2='',$class3='',$col3='',$class4='',$col4='',$class5='',$col5='',$class6='',$col6='') {
		// builds html for equal height columns
		$code='<div class="'.$outerClass.'"><div class="ecInner">';
		switch ($numCols) {
			case 2:
	      $code.='<div class="'.$class1.'">'.$col1.'</div>
	      <div class="col2 '.$class2.'">'.$col2.'</div>';
			break;
			case 3:
	      $code.='<div class="'.$class1.'">'.$col1.'</div>
	      <div class="col2 '.$class2.'">'.$col2.'</div>
	      <div class="col3 '.$class3.'">'.$col3.'</div>';
			break;
			case 4:
	      $code.='<div class="'.$class1.'">'.$col1.'</div>
	      <div class="col2 '.$class2.'">'.$col2.'</div>
	      <div class="col3 '.$class3.'">'.$col3.'</div><div class="col4 '.$class4.'">'.$col4.'</div>';
			break;
			case 5:
	      $code.='<div class="'.$class1.'">'.$col1.'</div>
	      <div class="col2 '.$class2.'">'.$col2.'</div>
	      <div class="col3 '.$class3.'">'.$col3.'</div><div class="col4 '.$class4.'">'.$col4.'</div><div class="col5 '.$class5.'">'.$col5.'</div>';
			break;
			case 6:
	      $code.='<div class="'.$class1.'">'.$col1.'</div>
	      <div class="col2 '.$class2.'">'.$col2.'</div>
	      <div class="col3 '.$class3.'">'.$col3.'</div><div class="col4 '.$class4.'">'.$col4.'</div><div class="col5 '.$class5.'">'.$col5.'</div><div class="col6 '.$class6.'">'.$col6.'</div>';
			break;
		}
		$code.='</div></div><p class="pClear"></p>';
		return $code;
	}
	
	function checkCache($filename,$age=15) {
		// checks if cached file is older then $age minutes
		// returns true if file is fresh
		$filename=PATH_CACHE.'/'.$filename.'.cac';
		if (file_exists($filename) AND !isset($_GET['nc'])) {
			// use last cache version for robots
			if ((time()-(60*$age))<filemtime($filename)) return true; // OR $page->isRobot()
		}
		return false;
	}
	
	function fetchCache($filename) {
		$filename=PATH_CACHE.'/'.$filename.'.cac';
		$fHandle=fopen($filename,'r');
		$fSize=filesize($filename);
		if ($fSize>0) 
			$contents = fread($fHandle, $fSize);
		else
			$contents='';
		fclose($fHandle);
		return $contents;
	}

	function cacheContent($filename,$html) {
		// writes the code in $html to $filename in cache directory
		$filename=PATH_CACHE.'/'.$filename.'.cac';
		$fHandle=fopen($filename,'w');
		if ($fHandle!==false) {
			fwrite($fHandle,$html);
			fclose($fHandle);
		}
	}	
}	
?>