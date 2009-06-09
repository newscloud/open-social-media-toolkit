<?php

class rss {

	var $db;
	var $utilObj;			
	var $storyList;
	var $feedTitle;
	var $feedDescription;
	var $feedUrl;
	var $feedRssUrl;
	var $baseUrl;	

	function rss(&$db,$baseUrl='')
	{
		if (is_null($db)) { 
			require_once(PATH_CORE.'/classes/db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=&$db;
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($db); 
					
		$this->storyList=array();
		$this->storyList[]=0;
		$this->storyLimit=15;
		$this->baseUrl=$baseUrl;
	}
	
	function build($action='TopStories',$mode='rss') {
		require_once(PATH_CORE.'/classes/content.class.php');
		$cObj=new content($this->db);
		// if $mode is rss, then echo the output
		// if $mode is api, then return the query
		switch ($action) {
			default:
				// do nothing
			break;
			case 'TopStories':
				$this->feedTitle=SITE_TITLE.' Top Stories';
				$this->feedDescription=' Top rated stories from '.SITE_TITLE;
				$this->feedUrl=$this->baseUrl;
				$this->feedRssUrl=$this->baseUrl.'?p=rss&action=TopStories';				
				$query=$cObj->fetchUpcomingStories('',RSS_NUMBER_STORIES);
			break;
		}
		// output the xml feed
		if ($mode=='rss') {
			$code=$this->createXML($this->feedTitle,$this->feedUrl,$this->feedRssUrl,$this->feedDescription,$query);
			return $code;
		} else
			return $query;
	}

	function createXML ($title='',$url='',$rssurl='',$description='',$query=NULL) {
		require_once(PATH_CORE.'/utilities/feedcreator.class.php');
		$rssFeed = new UniversalFeedCreator(); 
		$rssFeed->useCached(); // use cached version if age<1 hour
		$rssFeed->title =$title; 
		$rssFeed->link = $url; 
		$rssFeed->syndicationURL = $rssFeedurl; 
		$rssFeed->description = $description; 
		$rssFeed->descriptionTruncSize = 500;
		$rssFeed->descriptionHtmlSyndicated = true;
		$rssFeed->language = "en-us";
		$number = $this->db->countQ($query);
		while ($data = $this->db->readQ($query)) {
			    $item = new FeedItem(); 	    
			    $temptitle = str_replace("\\'","'",$data->title);
			    $temptitle = str_replace('\\"','"',$temptitle);
			    $item->title = $temptitle;
	    		$item->link = $this->baseUrl.'?p=read&cid='.$data->siteContentId.'&viaRSS';
			    $tempdesc = str_replace("\\'","'",$data->caption);
			    $tempdesc = str_replace('\\"','"',$tempdesc);
			    $tempdesc= $this->utilObj->shorten($tempdesc,500);
			    $item->description = $this->safexml($tempdesc); 
			    $item->descriptionTruncSize = 500;
			    $item->descriptionHtmlSyndicated = true;			    
			    $item->date = $data->mydate; 
			    $item->source = $this->baseUrl;
			    $rssFeed->addItem($item);		
		}
		$temp_file = PATH_CACHE.'/rss'.rand(1,1000).'.tmp';
		$text=$rssFeed->saveFeed("RSS2.0", $temp_file,false);	
		return $text;
	}

	function safexml($temp) {
		// from http://www.analysisandsolutions.com/code/phpxml.htm
		 // Escape ampersands that aren't part of entities.
	    $temp = preg_replace('/&(?!\w{2,6};)/', '&amp;', $temp);
	    // Remove all non-visible characters except SP, TAB, LF and CR.
    	$temp = preg_replace('/[^\x20-\x7E\x09\x0A\x0D]/', "\n", $temp);
    	$temp='<![CDATA['.$temp.']]>';
		return $temp;
	}
	
} // end of class rss

?>
