<?php
	echo 'Beginning to populate test data<br />';

	// Update Facebook application settings
	echo 'Adding Facebook settings for later uploading...<br />';
	$q=$db->delete("SystemStatus","name LIKE 'fbApp_%'");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_use_iframe',null,0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_wide_mode',null,1");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_installable',null,1");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_dashboard_url',".URL_CANVAS.",0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_privacy_url','".URL_CANVAS."?p=tos',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_help_url','".URL_CANVAS."?p=contact',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_callback_url','".URL_BASE."',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_application_name','".SITE_TITLE."',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_tab_default_name','".SITE_TITLE."',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_authorize_url','".URL_BASE."?p=postAuth&m=add',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_profile_tab_url','".URL_BASE."?p=ajax&m=appTab',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_uninstall_url','".URL_BASE."?p=postAuth&m=remove',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_email','".SUPPORT_EMAIL."',0");		
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_post_authorize_redirect_url','".URL_CANVAS."?p=team',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_publish_url',null,'".URL_BASE."?p=ajax&m=wallPublisher',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_publish_self_url','".URL_BASE."?p=ajax&m=wallPublisher&self',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_publish_self_action','".SITE_TITLE."',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_publish_action','".SITE_TITLE."',0");
	$db->insert("SystemStatus","name,strValue,numValue","'fbApp_message_action','".SITE_TITLE."',0");
	/* Unused settings for now
		$db->insert("SystemStatus","name,strValue,numValue","'fbApp_',null,0");
		 ('22','fbApp_info_changed_url','',null),
		 ('25','fbApp_edit_url','',null),
		 ('27','fbApp_desktop',null,'0'),
		 ('13','fbApp_private_install',null,'0'),
		 ('30','fbApp_default_column',null,'1'),
		 ('34','fbApp_base_domain','',null),
		*/

	// Add admin user
	$q=$db->query("SELECT * FROM User WHERE isAdmin=1");
	if ($db->countQ($q)==0) {
		echo 'Adding '.SUPPORT_ADMIN.' as administrator...<br />';
		require_once(PATH_CORE.'/classes/user.class.php'); 
		$userTable = new UserTable($db); // TODO: cache instances of the tables globally
		$userInfoTable = new UserInfoTable($db);
		$user = $userTable->getRowObject();
		$userInfo = $userInfoTable->getRowObject();
		// create new users
		$user->name=SITE_TITLE.' Administrator';
		$user->isAppAuthorized = 1;
		$user->votePower=1;
		$user->isAdmin=1;
		$user->authLevel='member';
		$user->eligibility='team';
		$user->email=SUPPORT_ADMIN;
		$user->ncUid=rand(0,99999); // deprecated column, but must be set
		if ($user->insert())
		{
			// inserted ok
			if ($userInfo->createFromUser($user, 0))
			{
				require_once(PATH_CORE.'/classes/subscriptions.class.php');
				$subTable = new SubscriptionsTable($db); 
				$sub = $subTable->getRowObject();
				$sub->userid=$user->userid;
				$sub->rxFeatures=1;
				$sub->rxMode='notification';
				$sub->insert();
			}
		}	
	}
	
	// Add stories
	require_once(PATH_CORE.'/classes/content.class.php');
	$cObj=new content($db);
	// load admin
	$q=$db->queryC("SELECT * FROM User,UserInfo WHERE User.userid=UserInfo.userid AND isAdmin=1 LIMIT 1;");
	if ($q!==false) {
		$ui=$db->readQ($q);
		$ui->u->name=$ui->name;
		$fData=new stdClass;
		$fData->body='';
		$fData->tags='';
		$fData->videoEmbed='';
		$fData->isFeatureCandidate=0;
		$fData->mediatype='text';		
		$fData->url  = mysql_real_escape_string('http://www.wired.com/vanish/2009/09/how-evan-ratliff-was-caught/');
		$fData->imageUrl=mysql_real_escape_string('http://www.wired.com/vanish/wp-content/uploads/2009/09/evanbald.jpg');
		$fData->title=mysql_real_escape_string('How Evan Ratliff Was Caught');	
		$fData->caption = mysql_real_escape_string('Naked Pizza snagged Evan. But the legwork was done by Jeff Reifman, who was using twitter under @vanishteam. He has just put up an <a href="http://blog.newscloud.com/2009/09/how-we-caught-evan-ratliff.html">amazing post</a> detailing exactly what he did and how he did it. The post should be read in full.');
		$siteContentId=$cObj->createStoryContent($ui,$fData);
		// Add featured story
		$q=$db->delete("FeaturedTemplate");
		$sql = sprintf("REPLACE INTO FeaturedTemplate SET id = 1, template = '%s', story_1_id = %s", 'template_1', $siteContentId);
		$q=$db->query($sql);
		$db->query("UPDATE Content set isFeatured = 0 WHERE isFeatured = 1");
		$db->query("UPDATE Content set isFeatured = 1 WHERE siteContentId = $siteContentId");
		// clear out the cache of the home top stories
		require_once(PATH_CORE.'/classes/template.class.php');
		$templateObj=new template($db);
		$templateObj->resetCache('home_feature');		
		$fData->url  = mysql_real_escape_string('http://blog.newscloud.com/2009/09/research-findings-released-engaging-youth-in-social-media-is-facebook-the-new-media-frontier-.html');
		$fData->imageUrl=mysql_real_escape_string('http://farm4.static.flickr.com/3619/3571720938_e30c9d4ab3.jpg');
		$fData->title=mysql_real_escape_string('Research Findings Released: Engaging Youth in Social Media - Is Facebook the New Media Frontier?');	
		$fData->caption = mysql_real_escape_string('Counter to the decline in young people’s (print‐based) reading for pleasure and traditional media consumption is a noted increase in out‐of‐school online reading and writing through online fan fiction and social network sites. Yet, according to the Pew research institute, over one third of people under 25 get no news on a daily basis. However, teens spend many hours a week online (a recent British study said 31), particularly on Facebook ‐‐ the most‐trafficked social media site in the world. Facebook has more than 250 million active members.');
		$siteContentId=$cObj->createStoryContent($ui,$fData);
		$fData->url  = mysql_real_escape_string('http://www.knightfdn.org/news/press_room/knight_press_releases/detail.dot?id=353701');
		$fData->imageUrl=mysql_real_escape_string('http://idealog.typepad.com/.a/6a00d83451b2ee69e2010536582475970b-800wi');
		$fData->title=mysql_real_escape_string('Could Facebook make America smarter?');	
		$fData->caption = mysql_real_escape_string('In an era in which 85 percent of American college students actively update Facebook profiles but more than one-third report paying no attention to current events on a daily basis, it’s natural that social networking sites could help educate young people on today’s most pressing issues. A new study from a University of Minnesota researcher found that a Facebook application focusing on social issues facilitated self-expression and critical conversation more than traditional news Web sites, suggesting new strategies for engaging young people in critical content.');
		$siteContentId=$cObj->createStoryContent($ui,$fData);
		$fData->url  = mysql_real_escape_string('http://www.grist.org/article/2009-05-12-facebook-efforts-real-change');
		$fData->imageUrl=mysql_real_escape_string('http://www.grist.org/i/assets/Hot-Dish-Captain-Planet-college-student_250x188.jpg');
		$fData->title=mysql_real_escape_string('Facebook app translates online efforts into real-world environmental change');	
		$fData->caption = mysql_real_escape_string('Climate change may take place in the offline world, but that doesn’t mean the online world is relegated to mere words and worry about it. A clear example is the dedicated crew of young eco-activists at Hot Dish, a climate news-n-action application on Facebook. Hot Dish aims to move people from online engagement with climate news to offline action in the world where climate change is taking place.  In a rush of impressive real-world environmental achievements, Hot Dish’s Action Team contest concluded last week. Battling for the crown of most climate conscious, Hot Dishers earned points for green efforts of all kinds: from sharing and discussing online environmental news to joining a CSA and writing to their representatives to even starting their own environmental groups');
		$siteContentId=$cObj->createStoryContent($ui,$fData);
	}
	
	// Add widgets
	$q=$db->query("SELECT * FROM Widgets WHERE title='evan' OR title='kfintro'");
	if ($db->countQ($q)==0) {	


$db->insert("Widgets","title,wrap,html,smartsize,width,height,style,type,isAd","'kfintro','<div id=\"featurePanel\" class=\"clearfix\">\r\n<div class=\"panelBar clearfix\">\r\n<h2>Featured Video</h2>\r\n<div class=\"bar_link\">About our funder, the <a href=\"http://knightfoundation.org\" target=\"_blank\">Knight Foundation</a></div>\r\n</div><div class=\"subtitle\"><a href=\"http://blog.newscloud.com/2008/12/knight_announce.html\" target=\"_blank\">Learn more</a> about NewsCloud\'s open source grant</div><!--end \"panelBar\"-->\r\n<div style=\"background-color:#FFFFFF;text-align:center;\">{widget}</div>\r\n</div><!--end \"featurePanel\"-->\r\n\r\n','<object width=\"400\" height=\"230\"><param name=\"allowfullscreen\" value=\"true\" /><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"movie\" value=\"http://vimeo.com/moogaloop.swf?clip_id=4358677&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" /><embed src=\"http://vimeo.com/moogaloop.swf?clip_id=4358677&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" allowscriptaccess=\"always\" width=\"400\" height=\"230\"></embed></object>','0','480','240','','script','0'");
	$kfId=$db->getId();
		
		$db->insert("Widgets","title,wrap,html,smartsize,width,height,style,type,isAd","'evan','<div id=\"featurePanel\" class=\"clearfix\">\r\n<div class=\"panelBar clearfix\">\r\n<h2>Featured Video</h2>\r\n<div class=\"bar_link\">Sometimes Daily Reports on Evan Ratliff\'s Capture</div>\r\n</div><!--end \"panelBar\"-->\r\n<center>{widget}</center>\r\n</div><!--end \"featurePanel\"-->\r\n\r\n','<embed src=\"http://blip.tv/play/g55igaDBcgI%2Em4v\" type=\"application/x-shockwave-flash\" width=\"480\" height=\"299\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed> ','0','480','310','','script','0'");
		$evanId=$db->getId();
		$db->update("Content","widgetid=$evanId","url='http://www.wired.com/vanish/2009/09/how-evan-ratliff-was-caught/'");

		$db->delete("FeaturedWidgets");
		$db->insert("FeaturedWidgets","widgetid,locale,position","$kfId,'homeFeature',1");
	}
	
	
	// Add folders and Links
	$db->delete("Folders","title='About NewsCloud' OR title='About Facebook'");
	$db->delete("FolderLinks","folderid NOT IN (select folderid from Folders )");
	require_once(SRC_ROOT.'/core/classes/resources.class.php');
	$rObj=new resources($db);
	$folder=$rObj->serializeFolder(0,0,'About NewsCloud');
	$folderid=$rObj->addFolder($folder);
	$li=$rObj->serializeLink(0,$folderid,0,'NewsCloud Blog','http://blog.newscloud.com','link','','');
	$rObj->addLink($li);
	$folder2=$rObj->serializeFolder(0,0,'About Facebook');
	$folderid2=$rObj->addFolder($folder2);
	$li2=$rObj->serializeLink(0,$folderid2,0,'Facebook','http://www.facebook.com','link','','');
	$rObj->addLink($li2);
	// Add ads
	echo 'Adding sample advertisment(s)...<br />';	
	$db->delete("AdCode");
	$db->insert("AdCode","site,clientid,format,locale,code,codeType,impRemaining,status","'smallBanner','0','smallBanner','homeSmallBanner','<html><head>
	<script type=\"text/javascript\" src=\"http://partner.googleadservices.com/gampad/google_service.js\">
	</script>
	<script type=\"text/javascript\">
	  GS_googleAddAdSenseService(\"ca-pub-9975156792632579\");
	  GS_googleEnableAllServices();
	</script>
	<script type=\"text/javascript\">
	  GA_googleAddSlot(\"ca-pub-9975156792632579\", \"Needle_Small\");
	</script>
	<script type=\"text/javascript\">
	  GA_googleFetchAds();
	</script>
	</head>
	<body>
	<script type=\"text/javascript\">
	  GA_googleFillSlot(\"Needle_Small\");
	</script>
	</body>
	</html>','iframe','0','active'");	
	$db->insert("AdCode","site,clientid,format,locale,code,codeType,impRemaining,status","'smallBanner','0','smallBanner','anySmallBanner','<html><head>
	<script type=\"text/javascript\" src=\"http://partner.googleadservices.com/gampad/google_service.js\">
	</script>
	<script type=\"text/javascript\">
	  GS_googleAddAdSenseService(\"ca-pub-9975156792632579\");
	  GS_googleEnableAllServices();
	</script>
	<script type=\"text/javascript\">
	  GA_googleAddSlot(\"ca-pub-9975156792632579\", \"Needle_Small\");
	</script>
	<script type=\"text/javascript\">
	  GA_googleFetchAds();
	</script>
	</head>
	<body>
	<script type=\"text/javascript\">
	  GA_googleFillSlot(\"Needle_Small\");
	</script>
	</body>
	</html>','iframe','0','active'");	
	$db->insert("AdCode","site,clientid,format,locale,code,codeType,impRemaining,status","'largeBanner','0','largeBanner','anyLargeBanner','<html><head>
	<script type=\"text/javascript\" src=\"http://partner.googleadservices.com/gampad/google_service.js\"></script>
	<script type=\"text/javascript\">
	  GS_googleAddAdSenseService(\"ca-pub-9975156792632579\");
	  GS_googleEnableAllServices();
	</script>
	<script type=\"text/javascript\">
	  GA_googleAddSlot(\"ca-pub-9975156792632579\", \"AnyLargeBanner\");
	</script>
	<script type=\"text/javascript\">
	  GA_googleFetchAds();
	</script>
	</head>
	<body>
	<script type=\"text/javascript\">
	  GA_googleFillSlot(\"AnyLargeBanner\");
	</script></body></html>','iframe','0','active'");
	$db->insert("AdCode","site,clientid,format,locale,code,codeType,impRemaining,status","'skyscraper','0','skyscraper','anySkyscraper','<html><head><script type=\'text/javascript\' src=\'http://partner.googleadservices.com/gampad/google_service.js\'>
	</script>
	<script type=\'text/javascript\'>
	  GS_googleAddAdSenseService(\'ca-pub-9975156792632579\');
	  GS_googleEnableAllServices();
	</script>
	<script type=\'text/javascript\'>
	  GA_googleAddSlot(\'ca-pub-9975156792632579\', \'Needly_Skyscraper\');
	</script>
	<script type=\'text/javascript\'>
	  GA_googleFetchAds();
	</script></head><body>
	<script type=\'text/javascript\'>
	  GA_googleFillSlot(\'Needly_Skyscraper\');
	</script></body></html>','iframe','0','active'");
	
	// Add news feed
	$db->delete("Feeds","rss='http://www.csmonitor.com/rss/top.rss'");
	$db->insert("Feeds","wireid,title,url,rss,lastFetch,feedType,specialType,loadoptions,userid,tagList","0,'Christian Science Monitor','http://www.csmonitor.com','http://www.csmonitor.com/rss/top.rss','date_sub(NOW(), INTERVAL 3 DAY)','wire','default','none',0,''");
	if (defined('ENABLE_IMAGES') AND ENABLE_IMAGES) {		
			$db->delete("Feeds","rss='http://api.flickr.com/services/feeds/photos_public.gne?id=44917946@N03&lang=en-us&format=atom'");
		$db->insert("Feeds","wireid,title,url,rss,lastFetch,feedType,specialType,loadoptions,userid,tagList","0,'Flickr Default Pool','http://www.flickr.com/photos/44917946@N03/','http://api.flickr.com/services/feeds/photos_public.gne?id=44917946@N03&lang=en-us&format=atom','date_sub(NOW(), INTERVAL 30 DAY)','images','flickrContent','all',0,''");
		
	}

	// Fetch newswire articles
	echo 'Reading in newswire feed...<br />';
	require_once(SRC_ROOT.'/core/classes/feed.class.php');
	$feedObj=new feed($db);
	$feedObj->fetchFeeds();			
	echo 'Completed newswire fetch...<br />';
	if (defined('ENABLE_IMAGES') AND ENABLE_IMAGES) {
		echo 'Reading in media flickr feed...<br />';
		$feedObj->fetchImages();	
		echo 'Completed media flickr fetch...<br />';
	}		

	// Populate Micro blog twitter room
	if (defined('ENABLE_MICRO')) {
		if (TWITTER_USER=='' OR TWITTER_PWD=='') {
			echo 'Please set TWITTER_USER and TWITTER_PWD in your constants.php file and/or .ini file <br />';
		} else {
			echo 'Reading in twitter accounts...<br />';
			// sync twitter service accounts for micro blog room - done daily
			require_once PATH_FACEBOOK."/classes/micro.class.php";
			$mObj=new micro();
			$mObj->cleanRoom();
			try {
				$mObj->syncFriends(false);
				$mObj->updateRoom();
			} catch (Exception $e) {
			}							
			echo 'Completed twitter sync...<br />';
		}
	}	
	
	echo 'Completed populate test data<br />';	
?>