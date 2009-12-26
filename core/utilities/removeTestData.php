<?php
	echo 'Beginning to remove test data<br />';
	// Remove stories
	$url  = mysql_real_escape_string('http://www.wired.com/vanish/2009/09/how-evan-ratliff-was-caught/');
	$db->delete("Content","url='$url'");
	$url  = mysql_real_escape_string('http://blog.newscloud.com/2009/09/research-findings-released-engaging-youth-in-social-media-is-facebook-the-new-media-frontier-.html');
	$db->delete("Content","url='$url'");
	$url  = mysql_real_escape_string('http://www.knightfdn.org/news/press_room/knight_press_releases/detail.dot?id=353701');
	$db->delete("Content","url='$url'");
	$url  = mysql_real_escape_string('http://www.grist.org/article/2009-05-12-facebook-efforts-real-change');
	$db->delete("Content","url='$url'");
	$db->delete("ContentImages","siteContentId NOT IN (select siteContentId from Content )");

	// Remove featured story
	$sql = sprintf("REPLACE INTO FeaturedTemplate SET id = 1, template = '%s', story_1_id = %s", 'template_1', 0);
	$q=$db->query($sql);	
	require_once(PATH_CORE.'/classes/template.class.php');
	$templateObj=new template($db);
	$templateObj->resetCache('home_feature');		
	
	// Remove widgets
	$db->delete("Widgets","title='evan' OR title='kfintro'");
	$db->delete("FeaturedWidgets");
	// Remove folders
	$db->delete("Folders","title='About NewsCloud' OR title='About Facebook'");
	// Remove links
	$db->delete("FolderLinks","folderid NOT IN (select folderid from Folders )");
	
	// Add ads
	//echo 'Removing advertisment(s)...<br />';	
	//$db->delete("AdCode","format='homeSmallBanner'");

	// Remove twitter accounts
	$db->delete("MicroAccounts");
	$db->delete("MicroPosts");

	// Add news feed
	// Fetch newswire articles
	$db->delete("Feeds","rss='http://www.csmonitor.com/rss/top.rss'");
	
	$db->delete("Newswire");
	require_once(PATH_CORE.'/classes/template.class.php');
	$templateObj=new template($db);
	$templateObj->resetCache('home_feature');		
	$templateObj->resetCache('newswire');		
	$templateObj->resetCache('home_ts');	
	$templateObj->resetCache('readAll');	

	// Remove Media stream
	$db->delete("Feeds","rss='http://api.flickr.com/services/feeds/photos_public.gne?id=44917946@N03&lang=en-us&format=atom'");
	$db->delete("FeedMedia");

	$db->delete("fbSessions");

	echo 'Completed remove test data<br />';	


?>