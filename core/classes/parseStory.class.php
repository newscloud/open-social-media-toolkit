<?php
define ("LENGTH_LONG_CAPTION",500);

class parseStory {
	var $url;

	function parseStory($url='') {
		if ($url<>'') {
			if (!preg_match('/^http:\/\//', $url))
				$url = 'http://'.$url;
			$this->url = $url;			
		}
	}
	
	function refreshUrl($url='') {
		if (!preg_match('/^http:\/\//', $url))
			$url = 'http://'.$url;
		$this->url = $url;					
	}

	function parse() {
		require_once(PATH_CORE.'/classes/remotefile.class.php');
		$rfObj = new remotePageProperty($this->url);
		$data = array();
		$data['title'] = $this->cleanTitle($rfObj->getPageTitle());
		$page = $rfObj->page_content;
		// to do - grab description from meta tag or story content
		if (preg_match('/<meta name="description"[^>]*content="([^"]+)"/i', $page, $match))
			$data['description'] = $match[1];
		else {
			$temp=$rfObj->getPageParagraphs();
			require_once(PATH_CORE.'/classes/utilities.class.php');
			$utilObj=new utilities();
			$temp=$utilObj->shorten($temp,LENGTH_LONG_CAPTION);					
			$data['description'] = $temp;
			//$this->log('Caption from gPP: '.$temp);
		}			
		$data['images']=$this->parseImages($rfObj);
		// to do - grab content
		// to do - use my code to grab keywords using semantic library
		return $this->jsonData($data);
	}
	
	function parseImages(&$rfObj=null,$minSize=3500) {
		$imgArr = $rfObj->getPageImages();
		$images = array();
		$sizes=array();
		foreach ($imgArr as $index => $image) {
			$filesize=$rfObj->remote_filesize($image);
			//$size = getimagesize($image);				
			//$this->log('w: '.$size[0].' h:'.$size[1] );
			// eliminate images with small dimensions
			//if ($size[0] < 75 || $size[1] < 50)
			//	continue;
			// eliminate images with small filesize
			if ($filesize<$minSize) continue;
			$images[] = $image;
			$sizes[]=$filesize;
		}
		array_multisort($sizes,SORT_DESC,$images);
		return $images;
	}
	
	function cleanUrl($url='') {
		$temp=str_ireplace(array('?rss','from=rss','source=rss','syndication=rss','ana=from_rss','src=RSS_PUBLIC'),'',$url);
		$temp=trim($temp,'&?');
		return $temp;
	}
	
	function cleanTitle($temp='') {
		$original=$temp;
		// remove generic tags
		// to do - look for prefix or suffix, or in paren or in bracket or with :  or pipe or dash
		$genericTags=array('The Blotter | ','Business & Technology','News |',' (News)','Local News: ', 'Sports: ' ,'Business: ' ,'News: '  ,'UPDATE:','Health |','Top Stories','Politics','Science | ','Current Affairs | ', 'VIDEO: ','BREAKING: ','Video: ','[VIDEO]','Observatory -','Crime & courts','Seattle |','Elections |','Seattle News', 'Local News', 'Breaking News',', weather');
		foreach ($genericTags as $tag) {
			$temp=str_ireplace($tag,'',$temp);
		}
		// remove site tags
		$siteTags= array('Seattle PostGlobe |','OPB News','Seattle Times Newspaper','NYTimes.com','- New York Times' ,'The Seattle Times: ' ,'CNN.com - ' ,'Macworld: ' ,'Wired News: ' ,' - Personal Finance - MSNBC.com' ,'ABC News: ' ,'- The Economic Times' ,' - washingtonpost.com Highlights - ' ,'SPACE.com -- ' ,'MSNBC.com' ,' - Attacks on London' ,'| Grist',' | Grist Magazine | Muckraker ' ,'Nation & World: ' ,'- Cancer ' ,'- Politics ' ,'- U.S. News - ' ,'WWMT - ' ,'DoD News: ' ,' - Yahoo! News' ,'BBC NEWS |' ,' Europe | ' ,'CBC Arts: ' ,'- U.S. News' ,' - FORTUNE - Page' ,'Democracy Now! |' ,' - Crime & Punishment - MSNBC.com' ,'t r u t h o u t | ' ,'Seattlest:','In Pictures |','INDEPTH |','Yahoo! Sports - ',' | csmonitor.com','Magazine |','In-Q-Tel chief:','Editorial: ','| SpaceRef - Your Space Reference','(Washington Post)','Freight Trane:','New Scientist News - ','The New Yorker:','National Geographic Magazine','Middle East |','|| kurohin.org','Africa |','Here and Now :','On Deadline:','- The Boston Globe',' - Boston.com',' - Your Money - Business',' - Robin Goods Latest News','WorldChanging: ','Another World Is Here: ','PluggedIn: ','MediaGuardian.co.uk | ','Radio |','- Forbes.com',': Nature','Slashdot | ','AppleInsider | ','| The Huffington Post','Treehugger:','Analysis:','Report:','Poll:','Study:','Daily Lush Magazine:','New Scientist |','Macleans.ca  ','The New York Review of Books: ','NCHS Report:','News-Record.com -','| The Register',' - Hurricanes\' Aftermath','HorsesAss.Org','Guardian Unlimited | ','Boing Boing:','The Globe and Mail:','PanAfrica:','South Africa:','Aljazeera.Net -','[Media Matters]','Science/Nature |','MercuryNews.com |','Herald Sun:','.:thebusinessonline.com:.','Talking Points:','Scotsman.com News - ','AlterNet:','FRONTLINE:','| PBS','Newsday.com:','TIME.com:','Guardian Unlimited |',  'Deutsche Welle |','- Council on Foreign Relations','| EnergyBulletin.net | Peak Oil News Clearinghouse','Guardian Unlimited Business |','Business latest |','Capitol Hill Blue: ','Salon.com |','(AP)','(Reuters)','(AFP)','Time:','washingtonpost.com','Time.com','Radio National:','CBS News','Entertainment |','The Raw Story |','New York Magazine','AP: ','CLIPS: ','Telegraph |','The Raw Story','BBC NEWS _','Americas _','The Raw Story _','Slashdot _','United Press World','NewsTrack','The News is NowPublic.com','USATODAY.com','- The Elkhart Project','KING5.com','Serving the University of Minnesota Community Since 1900');
		foreach ($siteTags as $tag) {
			$temp=str_ireplace($tag,'',$temp);
		}
		// remove stray characters
		$temp=trim($temp,' |-_:@');
		return $temp;
	}
	
	function jsonData($data) {
		return json_encode($data);
	}

	function log($str='Empty log string',$filename='parse.log') {
		$filename=PATH_SERVER_LOGS.$filename;
		$fHandle=fopen($filename,'a');
		if ($fHandle!==false) {
			if (!is_object($str) AND !is_array($str)) {
				fwrite($fHandle,$str."\n");				
			} else {
				fwrite($fHandle,print_r($str,true)."\n");
			}			
			fclose($fHandle);
		}
	}
}

?>
