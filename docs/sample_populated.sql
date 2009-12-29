SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `AdCode`;

CREATE TABLE `AdCode` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `site` varchar(50) default '',
  `clientid` int(11) default '0',
  `format` varchar(50) default '',
  `locale` varchar(100) default '',
  `code` text,
  `codeType` enum('html','iframe') default 'iframe',
  `impRemaining` bigint(20) default '0',
  `status` enum('active','completed','pending') default 'pending',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

insert into `AdCode` values('1','smallBanner','0','smallBanner','homeSmallBanner','<html><head>\n	<script type=\"text/javascript\" src=\"http://partner.googleadservices.com/gampad/google_service.js\">\n	</script>\n	<script type=\"text/javascript\">\n	  GS_googleAddAdSenseService(\"ca-pub-9975156792632579\");\n	  GS_googleEnableAllServices();\n	</script>\n	<script type=\"text/javascript\">\n	  GA_googleAddSlot(\"ca-pub-9975156792632579\", \"Needle_Small\");\n	</script>\n	<script type=\"text/javascript\">\n	  GA_googleFetchAds();\n	</script>\n	</head>\n	<body>\n	<script type=\"text/javascript\">\n	  GA_googleFillSlot(\"Needle_Small\");\n	</script>\n	</body>\n	</html>','iframe','0','active'),
 ('2','smallBanner','0','smallBanner','anySmallBanner','<html><head>\n	<script type=\"text/javascript\" src=\"http://partner.googleadservices.com/gampad/google_service.js\">\n	</script>\n	<script type=\"text/javascript\">\n	  GS_googleAddAdSenseService(\"ca-pub-9975156792632579\");\n	  GS_googleEnableAllServices();\n	</script>\n	<script type=\"text/javascript\">\n	  GA_googleAddSlot(\"ca-pub-9975156792632579\", \"Needle_Small\");\n	</script>\n	<script type=\"text/javascript\">\n	  GA_googleFetchAds();\n	</script>\n	</head>\n	<body>\n	<script type=\"text/javascript\">\n	  GA_googleFillSlot(\"Needle_Small\");\n	</script>\n	</body>\n	</html>','iframe','0','active'),
 ('3','largeBanner','0','largeBanner','anyLargeBanner','<html><head>\n	<script type=\"text/javascript\" src=\"http://partner.googleadservices.com/gampad/google_service.js\"></script>\n	<script type=\"text/javascript\">\n	  GS_googleAddAdSenseService(\"ca-pub-9975156792632579\");\n	  GS_googleEnableAllServices();\n	</script>\n	<script type=\"text/javascript\">\n	  GA_googleAddSlot(\"ca-pub-9975156792632579\", \"AnyLargeBanner\");\n	</script>\n	<script type=\"text/javascript\">\n	  GA_googleFetchAds();\n	</script>\n	</head>\n	<body>\n	<script type=\"text/javascript\">\n	  GA_googleFillSlot(\"AnyLargeBanner\");\n	</script></body></html>','iframe','0','active'),
 ('4','skyscraper','0','skyscraper','anySkyscraper','<html><head><script type=\'text/javascript\' src=\'http://partner.googleadservices.com/gampad/google_service.js\'>\n	</script>\n	<script type=\'text/javascript\'>\n	  GS_googleAddAdSenseService(\'ca-pub-9975156792632579\');\n	  GS_googleEnableAllServices();\n	</script>\n	<script type=\'text/javascript\'>\n	  GA_googleAddSlot(\'ca-pub-9975156792632579\', \'Needly_Skyscraper\');\n	</script>\n	<script type=\'text/javascript\'>\n	  GA_googleFetchAds();\n	</script></head><body>\n	<script type=\'text/javascript\'>\n	  GA_googleFillSlot(\'Needly_Skyscraper\');\n	</script></body></html>','iframe','0','active');

DROP TABLE IF EXISTS `AdTrack`;

CREATE TABLE `AdTrack` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `source` varchar(150) default '',
  `userid` bigint(20) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AskAnswers`;

CREATE TABLE `AskAnswers` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `questionid` int(11) default '0',
  `userid` bigint(20) unsigned default '0',
  `answer` text,
  `videoid` int(11) default '0',
  `numLikes` int(4) default '0',
  `numComments` int(4) default '0',
  `dt` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AskQuestions`;

CREATE TABLE `AskQuestions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned default '0',
  `question` varchar(255) default '',
  `details` text,
  `tagid` int(11) default '0',
  `videoid` int(11) default '0',
  `numLikes` int(4) default '0',
  `numComments` int(4) default '0',
  `numAnswers` int(4) default '0',
  `dt` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Challenges`;

CREATE TABLE `Challenges` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) default '',
  `shortName` varchar(25) default '',
  `description` text,
  `dateStart` datetime default NULL,
  `dateEnd` datetime default NULL,
  `initialCompletions` int(4) default '0',
  `remainingCompletions` int(4) default '0',
  `maxUserCompletions` int(4) default '0',
  `maxUserCompletionsPerDay` int(4) default '0',
  `type` enum('automatic','submission') default 'automatic',
  `pointValue` int(4) default '10',
  `eligibility` enum('team','general') default 'team',
  `status` enum('enabled','disabled') default 'enabled',
  `thumbnail` varchar(255) default 'default_challenge_thumb.png',
  `requires` varchar(25) default 'text',
  `isFeatured` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

insert into `Challenges` values('1','Vote on a story','vote','Like a story someone\'s posted? Vote for it and let everyone know.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1000','0','automatic','5','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('2','Comment on an article','comment','Comment on an article.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','300','0','automatic','10','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('3','Post a story','postStory','Post a news story that hasn\'t been posted yet.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','100','0','automatic','10','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('4','Post a blog entry','postBlog','Write your own story as a blog post','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','100','0','automatic','10','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('5','Share a story','shareStory','Share a Default Site Title story with your friends.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','500','0','automatic','25','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('6','Invite friends','invite','Earn points just by <a href=\"?p=invite\" onclick=\"switchPage(\'invite\'); return false;\">inviting your friends to use Default Site Title</a> ','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1000','0','automatic','25','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('7','Add bookmark tool','addBookmarkTool','Add the Default Site Title bookmark tool to your browser to make it easy to post articles while browsing the web. It\'s easy: just go to the Post a Story tab and drag the orange \"Post to Default Site Title\" button up to the links bar in your browser. (In IE, you may have to drag it to the actual bookmarks tab in the bar and hit save.)','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1','0','automatic','25','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('8','Default Site Title sign up','signup','As soon as you sign up to use Default Site Title, voila! Instant points!','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','0','0','automatic','200','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('9','Friend sign up','friendSignup','Invite a friend to add the Default Site Title app...and then if they add it, we\'ll shower some points on you.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1000','0','automatic','100','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('10','Receive email from Default Site Title!','optInEmail','Allow Default Site Title to send you occasional updates via email. Go to \"Settings\" and click on \"Would you like to receive email from us through facebook? (50 pts)\" and wait for the magic to happen.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1','0','automatic','50','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('11','Receive SMS updates from Default Site Title','optInSMS','Receive announcements and feature updates via SMS (aka text messages). Click on \"Settings\" and then \"Would you like to receive sms notifications from us through facebook? (50 pts)\" and follow the instructions.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1','0','automatic','50','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('12','Level up!','levelIncrease','As you accumulate points, your User Level increases. When you reach a new level, we give you bonus points!','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','6','0','automatic','200','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('13','Reader referral','referReader','Earn points when someone reads a story you shared!','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','300','0','automatic','5','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('14','Chat about a story in Default Site Title','chatStory','Strike up a conversation about a story with one of your Facebook friends using the chat widget on the story page sidebar. This is a great way to introduce friends to climate change issues! Fine print: Your friend must click through to read the story. If they are not Default Site Title members, they will be required to authorize the application for you to receive credit.','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1000','0','automatic','25','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('15','Add App Tab to profile','addAppTab','Add the Default Site Title application tab to your Facebook profile so your friends can see what you\'ve been up to. See <a href=\"http://apps.facebook.com/defaultapp/?p=faq\">the FAQ</a> for details. Then send us a screenshot of the tab on your profile! File size must be under 2mb. (Don\'t know how to take a screenshot? Once again, <a href=\"http://apps.facebook.com/defaultapp/?p=faq\">hit up the FAQ</a>.)','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1','0','submission','100','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('16','Add profile box to your wall','profileBoxWall','To add the profile box to your profile, visit settings -> application settings, then Edit Settings for \".SITE_TITLE.\". Click the Profile tab and \"add\" next to Box to add the profile box to your profile. Then, move the profile box to your wall page. (If you don\'t see the button, you may have already added this.) Send us a screenshot (file size under 2mb) of the box on your profile! (Don\'t know how to take a screenshot? <a href=\"http://apps.facebook.com/defaultapp/?p=faq\">Hit up the FAQ</a>.)\n						<div><fb:add-section-button section=\"profile\" /><br clear=\"all\" /></div>','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','1','0','submission','100','team','enabled','default_challenge_thumb.jpg','text','0'),
 ('17','Blog about Default Site Title','blog','Forget blogging about what you had for lunch. We know you love Default Site Title -- so why not write a post about it on your blog and send us the link?','2009-12-28 20:30:21','2019-12-26 20:30:21','0','0','25','0','submission','75','team','enabled','default_challenge_thumb.jpg','text','0');

DROP TABLE IF EXISTS `ChallengesCompleted`;

CREATE TABLE `ChallengesCompleted` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userid` bigint(20) default '0',
  `challengeid` int(11) default '0',
  `dateSubmitted` datetime default NULL,
  `dateAwarded` datetime default NULL,
  `evidence` text,
  `comments` text,
  `status` enum('submitted','awarded','rejected') default 'submitted',
  `pointsAwarded` int(4) default '10',
  `logid` bigint(20) unsigned default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Comments`;

CREATE TABLE `Comments` (
  `siteCommentId` int(11) unsigned NOT NULL auto_increment,
  `commentid` int(11) default '0',
  `siteContentId` int(11) default '0',
  `contentid` int(11) default '0',
  `comments` text,
  `postedByName` varchar(255) default '',
  `postedById` int(11) default '0',
  `userid` int(11) default '0',
  `date` datetime default NULL,
  `isBlocked` tinyint(1) default '0',
  `videoid` int(11) default '0',
  PRIMARY KEY  (`siteCommentId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ContactEmails`;

CREATE TABLE `ContactEmails` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(255) default '',
  `subject` varchar(255) default '',
  `message` text,
  `userid` bigint(20) unsigned default '0',
  `is_read` tinyint(1) default '0',
  `replied` tinyint(1) default '0',
  `topic` enum('general','editorial','team','feedback','bug') default 'general',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Content`;

CREATE TABLE `Content` (
  `siteContentId` int(11) unsigned NOT NULL auto_increment,
  `contentid` int(11) default '0',
  `title` varchar(255) default '',
  `caption` text,
  `source` varchar(150) default '',
  `url` varchar(255) default '',
  `permalink` varchar(255) default '',
  `postedById` int(11) default '0',
  `postedByName` varchar(255) default '',
  `date` datetime default NULL,
  `score` int(4) default '0',
  `numComments` int(2) default '0',
  `isFeatured` tinyint(1) default '0',
  `userid` int(11) default '0',
  `imageid` int(11) default '0',
  `videoIntroId` int(11) default '0',
  `isBlocked` tinyint(1) default '0',
  `videoid` int(11) default '0',
  `widgetid` int(11) default '0',
  `isBlogEntry` tinyint(1) default '0',
  `isFeatureCandidate` tinyint(1) default '0',
  PRIMARY KEY  (`siteContentId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

insert into `Content` values('1','0','How Evan Ratliff Was Caught','Naked Pizza snagged Evan. But the legwork was done by Jeff Reifman, who was using twitter under @vanishteam. He has just put up an <a href=\"http://blog.newscloud.com/2009/09/how-we-caught-evan-ratliff.html\">amazing post</a> detailing exactly what he did and how he did it. The post should be read in full.','www.wired.com','http://www.wired.com/vanish/2009/09/how-evan-ratliff-was-caught/','How_Evan_Ratliff_Was_Caught','60524','Default Site Title Administrator','2009-12-28 20:33:27','1','0','1','1','1','0','0','0','2','0','0'),
 ('2','0','Research Findings Released: Engaging Youth in Social Media - Is Facebook the New Media Frontier?','Counter to the decline in young people’s (print‐based) reading for pleasure and traditional media consumption is a noted increase in out‐of‐school online reading and writing through online fan fiction and social network sites. Yet, according to the Pew research institute, over one third of people under 25 get no news on a daily basis. However, teens spend many hours a week online (a recent British study said 31), particularly on Facebook ‐‐ the most‐trafficked social media site in the world. Facebook has more than 250 million active members.','blog.newscloud.com','http://blog.newscloud.com/2009/09/research-findings-released-engaging-youth-in-social-media-is-facebook-the-new-media-frontier-.html','Research_Findings_Released_Engaging_Youth_in_Social_Media_Is_Facebook_the_New_Media_Frontier','60524','Default Site Title Administrator','2009-12-28 20:33:27','1','0','0','1','2','0','0','0','0','0','0'),
 ('3','0','Could Facebook make America smarter?','In an era in which 85 percent of American college students actively update Facebook profiles but more than one-third report paying no attention to current events on a daily basis, it’s natural that social networking sites could help educate young people on today’s most pressing issues. A new study from a University of Minnesota researcher found that a Facebook application focusing on social issues facilitated self-expression and critical conversation more than traditional news Web sites, suggesting new strategies for engaging young people in critical content.','www.knightfdn.org','http://www.knightfdn.org/news/press_room/knight_press_releases/detail.dot?id=353701','Could_Facebook_make_America_smarter','60524','Default Site Title Administrator','2009-12-28 20:33:27','1','0','0','1','3','0','0','0','0','0','0'),
 ('4','0','Facebook app translates online efforts into real-world environmental change','Climate change may take place in the offline world, but that doesn’t mean the online world is relegated to mere words and worry about it. A clear example is the dedicated crew of young eco-activists at Hot Dish, a climate news-n-action application on Facebook. Hot Dish aims to move people from online engagement with climate news to offline action in the world where climate change is taking place.  In a rush of impressive real-world environmental achievements, Hot Dish’s Action Team contest concluded last week. Battling for the crown of most climate conscious, Hot Dishers earned points for green efforts of all kinds: from sharing and discussing online environmental news to joining a CSA and writing to their representatives to even starting their own environmental groups','www.grist.org','http://www.grist.org/article/2009-05-12-facebook-efforts-real-change','Facebook_app_translates_online_efforts_into_realworld_environmental_change','60524','Default Site Title Administrator','2009-12-28 20:33:27','1','0','0','1','4','0','0','0','0','0','0');

DROP TABLE IF EXISTS `ContentImages`;

CREATE TABLE `ContentImages` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `url` varchar(255) default '',
  `siteContentId` int(11) unsigned default '0',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

insert into `ContentImages` values('1','http://www.wired.com/vanish/wp-content/uploads/2009/09/evanbald.jpg','1','2009-12-28 20:33:27'),
 ('2','http://farm4.static.flickr.com/3619/3571720938_e30c9d4ab3.jpg','2','2009-12-28 20:33:27'),
 ('3','http://idealog.typepad.com/.a/6a00d83451b2ee69e2010536582475970b-800wi','3','2009-12-28 20:33:27'),
 ('4','http://www.grist.org/i/assets/Hot-Dish-Captain-Planet-college-student_250x188.jpg','4','2009-12-28 20:33:27');

DROP TABLE IF EXISTS `FeaturedTemplate`;

CREATE TABLE `FeaturedTemplate` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `template` varchar(255) default '',
  `story_1_id` int(10) unsigned default '0',
  `story_2_id` int(10) unsigned default '0',
  `story_3_id` int(10) unsigned default '0',
  `story_4_id` int(10) unsigned default '0',
  `story_5_id` int(10) unsigned default '0',
  `story_6_id` int(10) unsigned default '0',
  `t` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

insert into `FeaturedTemplate` values('1','template_1','1','0','0','0','0','0','2009-12-28 20:33:27');

DROP TABLE IF EXISTS `FeaturedWidgets`;

CREATE TABLE `FeaturedWidgets` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `widgetid` int(11) unsigned default NULL,
  `locale` varchar(100) default '',
  `position` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

insert into `FeaturedWidgets` values('1','1','homeFeature','1');

DROP TABLE IF EXISTS `FeedMedia`;

CREATE TABLE `FeedMedia` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) default '',
  `previewImageUrl` varchar(255) default '',
  `imageUrl` varchar(255) default '',
  `linkUrl` varchar(255) default '',
  `author` varchar(99) default '',
  `caption` text,
  `isFeatured` tinyint(1) default '0',
  `numLikes` int(4) default '0',
  `numComments` int(4) default '0',
  `mediaType` enum('image','video') default 'image',
  `feedid` int(11) default '0',
  `fbId` bigint(20) unsigned default '0',
  `t` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

insert into `FeedMedia` values('1','HopePineGrove2','http://farm3.static.flickr.com/2700/4166615121_a7a6237a3a_m.jpg','http://farm3.static.flickr.com/2700/4166615121_9b8f504cb9_o.jpg','http://www.flickr.com/photos/44917946@N03/4166615121/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:47:11'),
 ('2','HopeMarthaMillerCenter3','http://farm3.static.flickr.com/2495/4167371650_d191bb233b_m.jpg','http://farm3.static.flickr.com/2495/4167371650_44c0f4bd0e_o.jpg','http://www.flickr.com/photos/44917946@N03/4167371650/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:46:14'),
 ('3','HopeGraves4','http://farm3.static.flickr.com/2596/4167370576_e9526df079_m.jpg','http://farm3.static.flickr.com/2596/4167370576_f833fbe90e_o.jpg','http://www.flickr.com/photos/44917946@N03/4167370576/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:45:50'),
 ('4','HopeDimnentChapel9','http://farm3.static.flickr.com/2680/4167369206_d6e313bba6_m.jpg','http://farm3.static.flickr.com/2680/4167369206_58949b2ef5_o.jpg','http://www.flickr.com/photos/44917946@N03/4167369206/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:45:20'),
 ('5','HopeDimnentChapel7','http://farm3.static.flickr.com/2535/4167367578_765c8f4da1_m.jpg','http://farm3.static.flickr.com/2535/4167367578_3a5f518651_o.jpg','http://www.flickr.com/photos/44917946@N03/4167367578/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:44:45'),
 ('6','HopeDimnentChapel2','http://farm3.static.flickr.com/2734/4166607469_75bf7450c1_m.jpg','http://farm3.static.flickr.com/2734/4166607469_30f1e73cf1_o.jpg','http://www.flickr.com/photos/44917946@N03/4166607469/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:44:18'),
 ('7','HopeDimnentChapel10','http://farm3.static.flickr.com/2792/4166606109_8117839456_m.jpg','http://farm3.static.flickr.com/2792/4166606109_126e4d8eba_o.jpg','http://www.flickr.com/photos/44917946@N03/4166606109/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:43:46'),
 ('8','HopeDeVosFieldhouse2','http://farm3.static.flickr.com/2760/4167363466_12e902fc96_m.jpg','http://farm3.static.flickr.com/2760/4167363466_b699e8acae_o.jpg','http://www.flickr.com/photos/44917946@N03/4167363466/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:43:09'),
 ('9','HopeAnchor3','http://farm5.static.flickr.com/4038/4166603433_cb4c6a03b5_m.jpg','http://farm5.static.flickr.com/4038/4166603433_95475c61f7_o.jpg','http://www.flickr.com/photos/44917946@N03/4166603433/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-12-07 12:42:39'),
 ('10','HopeArch1','http://farm3.static.flickr.com/2516/4129066468_8ef6d8fba5_m.jpg','http://farm3.static.flickr.com/2516/4129066468_1ff4bd8ce6_o.jpg','http://www.flickr.com/photos/44917946@N03/4129066468/','The Anchor Photo Pool','The Anchor Photo Pool posted a photo:<br />\n','0','0','0','image','0','0','2009-11-23 12:03:36');

DROP TABLE IF EXISTS `Feeds`;

CREATE TABLE `Feeds` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `wireid` int(11) default '0',
  `title` varchar(255) default '',
  `url` varchar(255) default '',
  `rss` varchar(255) default '',
  `lastFetch` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `feedType` enum('blog','wire','images','miniblog','bookmarks','allowed','localBlog') default 'wire',
  `specialType` enum('flickrContent','default') default 'default',
  `loadOptions` enum('all','matches','none') default 'none',
  `userid` bigint(20) default '0',
  `tagList` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

insert into `Feeds` values('1','0','Christian Science Monitor','http://www.csmonitor.com','http://www.csmonitor.com/rss/top.rss','2009-12-28 20:48:29','wire','default','none','0',''),
 ('2','0','Flickr Default Pool','http://www.flickr.com/photos/44917946@N03/','http://api.flickr.com/services/feeds/photos_public.gne?id=44917946@N03&lang=en-us&format=atom','2009-12-28 20:48:29','images','flickrContent','all','0','');

DROP TABLE IF EXISTS `FolderLinks`;

CREATE TABLE `FolderLinks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `linkid` int(11) NOT NULL default '0',
  `folderid` int(11) NOT NULL default '0',
  `title` varchar(255) default '',
  `url` varchar(255) default '',
  `notes` varchar(255) default '',
  `linkType` enum('link','product') default NULL,
  `imageUrl` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

insert into `FolderLinks` values('1','1','1','NewsCloud Blog','http://blog.newscloud.com','','link',''),
 ('2','2','2','Facebook','http://www.facebook.com','','link','');

DROP TABLE IF EXISTS `Folders`;

CREATE TABLE `Folders` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `folderid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `title` varchar(50) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

insert into `Folders` values('1','1','0','About NewsCloud'),
 ('2','2','0','About Facebook');

DROP TABLE IF EXISTS `ForumTopics`;

CREATE TABLE `ForumTopics` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) default '',
  `intro` text,
  `lastChanged` datetime default NULL,
  `numPostsToday` int(4) default '0',
  `numViewsToday` int(4) default '0',
  `isHidden` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

insert into `ForumTopics` values('1','General','Talk about anything related to News','2009-12-28 20:30:20','0','0','0'),
 ('2','Feedback','Please share your feedback with us for Default Site Title','2009-12-28 20:30:20','0','0','0');

DROP TABLE IF EXISTS `Ideas`;

CREATE TABLE `Ideas` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userid` bigint(20) unsigned default '0',
  `idea` varchar(255) default '',
  `details` text,
  `tagid` int(11) default '0',
  `videoid` int(11) default '0',
  `numLikes` int(4) default '0',
  `numComments` int(4) default '0',
  `dt` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Log`;

CREATE TABLE `Log` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `userid1` bigint(20) default '0',
  `action` enum('vote','comment','readStory','readWire','invite','postStory','publishWire','publishStory','shareStory','referReader','referToSite','postTwitter','signup','acceptedInvite','redeemed','wonPrize','completedChallenge','addedWidget','addedFeedHeadlines','friendSignup','addBookmarkTool','levelIncrease','sessionsRecent','sessionsHour','pageAdd','chatStory','postBlog','sendCard','askQuestion','answerQuestion','likeQuestion','likeAnswer','likeIdea','likeStuff','addStuff','storyFeatured','madePredict') default 'readStory',
  `itemid` int(11) default '0',
  `itemid2` int(11) default '0',
  `userid2` bigint(20) default '0',
  `ncUid` bigint(20) default '0',
  `t` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `dateCreated` datetime default NULL,
  `status` enum('pending','ok','error') default 'pending',
  `isFeedPublished` enum('pending','complete') default 'pending',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `LogExtra`;

CREATE TABLE `LogExtra` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `logid` bigint(20) default '0',
  `txt` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `MicroAccounts`;

CREATE TABLE `MicroAccounts` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `sid` bigint(20) unsigned default '0',
  `shortName` varchar(150) default '',
  `friendlyName` varchar(150) default '',
  `tag` varchar(150) default '',
  `profile_image_url` varchar(255) default '',
  `service` enum('twitter') default 'twitter',
  `userid` bigint(20) unsigned default '0',
  `isTokenValid` tinyint(1) default '0',
  `token` varchar(60) default '',
  `tokenSecret` varchar(60) default '',
  `lastSync` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

insert into `MicroAccounts` values('1','62550691','jdgatz','James Donald Gatz','default','http://a3.twimg.com/profile_images/365844643/spect2_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('2','7690982','gottesmd','gottesmd','default','http://a3.twimg.com/profile_images/74656039/Face23_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('3','67475249','SurelyHolmes','Quin Shirk-Luckett','default','http://s.twimg.com/a/1261078355/images/default_profile_2_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('4','13016562','JTinAtlanta','JTinAtlanta','default','http://a3.twimg.com/profile_images/376732849/emu_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('5','21001022','saladlee','Sally Rosen','default','http://a3.twimg.com/profile_images/587557937/Photo_306_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('6','21912364','shelleydubois','Shelley DuBois','default','http://a3.twimg.com/profile_images/183215065/headshot_normal.jpeg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('7','67692726','evan_ratliff','Evan Ratliff','default','http://a3.twimg.com/profile_images/374481019/evan_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('8','52662485','riptek','Rip Kimball','default','http://s.twimg.com/a/1262036730/images/default_profile_1_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('9','71178795','GatzMorang','Gatz Morang','default','http://a1.twimg.com/profile_images/405907758/Gatz_Headshot_768x768_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('10','15398651','reifman','Jeff Reifman','default','http://a1.twimg.com/profile_images/530548900/jeff_lake_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('11','70839412','Dusky_Wireworm','Cole Optera','default','http://a1.twimg.com/profile_images/393840650/3936_12clickbeetle_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('12','69405795','realsnacktime','CTG','default','http://s.twimg.com/a/1261519751/images/default_profile_4_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('13','14649318','sometimesdaily','sometimesdaily','default','http://a1.twimg.com/profile_images/54741788/sdlogo_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('14','17567944','MrTumnus','Mr. Tumnus','default','http://s.twimg.com/a/1262036730/images/default_profile_2_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('15','1399231','amazingamanda','Amanda Congdon','default','http://a3.twimg.com/profile_images/388996889/twitteravatar_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('16','69901307','Viamarguta51','Joe Bradley','default','http://s.twimg.com/a/1261519751/images/default_profile_0_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('17','28959238','how2stalk','how2stalk.com','default','http://a1.twimg.com/profile_images/260537976/st_normal.JPG','twitter','0','0','','','0000-00-00 00:00:00'),
 ('18','42677342','mattysf1','Matthew Gilreath','default','http://a1.twimg.com/profile_images/586084754/14742_237595279879_558509879_4291496_440417_n_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('19','28737287','socillion','socillion','default','http://a1.twimg.com/profile_images/444371712/Anarchy-red_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('20','67522486','RatliffPatrol','Ratliff Tracker','default','http://a1.twimg.com/profile_images/373500082/Hi_Evan_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('21','9166512','labfly','Jan Libby','default','http://a3.twimg.com/profile_images/555047351/bwjan_normal.jpeg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('22','68192441','runningratliff','Evan','default','http://a1.twimg.com/profile_images/377798294/10-Pursuit-DVDcover_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('23','36143107','Juggle4Food','Scott McKelvey','default','http://s.twimg.com/a/1262036730/images/default_profile_0_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('24','40293541','devitry','devitry','default','http://a3.twimg.com/profile_images/384995915/dave_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('25','68152924','ratliffevan','   Evan Ratliff ','default','http://s.twimg.com/a/1261519751/images/default_profile_0_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('26','17801002','alanbly','Adam McCormick','default','http://a1.twimg.com/profile_images/352036318/twitterProfilePhoto_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('27','67083577','duncebucket','John Doe','default','http://s.twimg.com/a/1261519751/images/default_profile_1_normal.png','twitter','0','0','','','0000-00-00 00:00:00'),
 ('28','66408266','FindEvanRatliff','FindEvenRatliff','default','http://a3.twimg.com/profile_images/366659781/Evan_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('29','66554977','VanishingAct01','Macgyver','default','http://a1.twimg.com/profile_images/367563684/EDR_1__normal.JPG','twitter','0','0','','','0000-00-00 00:00:00'),
 ('30','66773483','JohnsonThrimp','Thrimp Johnson','default','http://a1.twimg.com/profile_images/375556778/Untitled_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('31','66686885','EvanOffGrid','Evan Off Grid','default','http://a1.twimg.com/profile_images/368363970/yakuza_goldshades_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('32','17468481','theatavist','Evan Ratliff','default','http://a1.twimg.com/profile_images/218345802/Pic_251_sm_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('33','65934899','EvansVanished','Teeuwynn Woodruff','default','http://a3.twimg.com/profile_images/366530859/Teeuwynn_Headshot_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('34','16892481','nxthompson','Nicholas Thompson','default','http://a1.twimg.com/profile_images/406783772/soccer3_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00'),
 ('35','68707917','TrackEvan','TrackEvan.com','default','http://a3.twimg.com/profile_images/381302071/track_evan_logo_normal.jpg','twitter','0','0','','','0000-00-00 00:00:00');

DROP TABLE IF EXISTS `MicroPosts`;

CREATE TABLE `MicroPosts` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `statusid` bigint(20) unsigned default '0',
  `sid` bigint(20) unsigned default '0',
  `msg` text,
  `numLikes` int(4) default '0',
  `isFavorite` tinyint(1) default '0',
  `dt` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

insert into `MicroPosts` values('1','7139282490','15398651','Perhaps expecting too much from creator of Titanic RT @kleinmatic: I enjoyed Avatar, but Annalee Newitz is right as usual. http://ow.ly/QwXx','0','0','2009-12-28 15:44:43'),
 ('2','7139187284','9166512','v awesome RT @clementyeung One word: #Awesome = http://dailybooth.com/','0','0','2009-12-28 15:41:17'),
 ('3','7138993774','1399231','the logistics of next month 4 SD r making my head spin. Can\'t compute! Sedona, Ventura, 4 different HI islands, LA, Vegas & finally Santa Fe','0','0','2009-12-28 15:34:24'),
 ('4','7138875847','9166512','today was fantastic :) spent the entire day hanging & catching up w/friends, telling stories & lottalotta giggling','0','0','2009-12-28 15:30:16'),
 ('5','7135686295','1399231','My love goes out to Jay Smooth 4 this video. Charlie Sheen is bringing domestic violence back into the spotlight it seems. http://ow.ly/QwMq','0','0','2009-12-28 13:38:27'),
 ('6','7130057865','1399231','a litte sad that my time in Phoenix is nearly over...','0','0','2009-12-28 10:14:46'),
 ('7','7125971767','9166512','10 most anticipated games for 2010 (boingboing) http://www.boingboing.net/2009/12/28/ten-for-2010-the-10.html','0','0','2009-12-28 07:50:46'),
 ('8','7113139264','42677342','@labfly same to you! ;-)','0','0','2009-12-27 21:08:26'),
 ('9','7100614936','15398651','Thanks for -finally- adding Fage, @iheartfage, @pcc! Please add 35 oz. containers! Maybe I can end my anti-union @wholefoods affaire :)','0','0','2009-12-27 13:15:53'),
 ('10','7100608560','9166512','quite a football day - my pats r looking better (yay!) but wha up w/those saints? maybe they\'ll come roaring back in post season :)','0','0','2009-12-27 13:15:38'),
 ('11','7100471519','9166512','@mattysf1 that sounds too delicious! :) happy merry & cheers to 2010, matty!','0','0','2009-12-27 13:10:03'),
 ('12','7099661192','42677342','Mmmm BBQ chicken & pulled pork sammies! Fab sauces & sides. at Blue Ribbon Barbeque http://loopt.us/4zvdpA.t (PIC)','0','0','2009-12-27 12:35:59'),
 ('13','7099609300','15398651','Worth reading: NYT on end of life care, pain relief & sedation http://ow.ly/Qer9 Kudos to Dr.\'s willing to share their views. Well reported.','0','0','2009-12-27 12:33:47'),
 ('14','7097405110','15398651','In 2010, run barefoot! Earlier this year, I began running and working out barefoot http://ow.ly/Q2nx','0','0','2009-12-27 11:01:02'),
 ('15','7096676121','15398651','#healthcare fuzzy logic: Premera rejects chiropractor for being out of network but deducts visit from my allotted 12 #businessmodels','0','0','2009-12-27 10:30:23'),
 ('16','7096468556','1399231','Note to all women: Do NOT marry Charlie Sheen. His next wife may not be so lucky.','0','0','2009-12-27 10:21:35'),
 ('17','7096221824','1399231','Charlie Sheen is disgusting. Denise Richards must be laughing her ass off. Now maybe ppl will start believing her about what a psycho he is.','0','0','2009-12-27 10:11:15');

DROP TABLE IF EXISTS `Newswire`;

CREATE TABLE `Newswire` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) default '',
  `caption` text,
  `source` varchar(150) default '',
  `url` varchar(255) default '',
  `date` datetime default NULL,
  `wireid` int(11) default '0',
  `feedid` int(11) default '0',
  `feedType` enum('blog','wire','images','miniblog','bookmarks','allowed','localBlog') default 'wire',
  `mediaUrl` varchar(255) default '',
  `imageUrl` varchar(255) default '',
  `embed` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

insert into `Newswire` values('1','Echoes of 2001 shoe bomber in Detroit attack','<p>In both cases, passengers and crew subdued the alleged bomber before the explosive material could fully ignite. In the Detroit attack, passengers heard popping noises and saw suspect Umar Farouk Abdulmutallab\'s pants on fire when they intervened, according to court documents.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/qn7QpHx-o5N2yYw9yTLUh-WMRDc/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/qn7QpHx-o5N2yYw9yTLUh-WMRDc/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/qn7QpHx-o5N2yYw9yTLUh-WMRDc/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/qn7QpHx-o5N2yYw9yTLUh-WMRDc/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JxELp5WZ5Vs:Z0MIjwYchtI:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JxELp5WZ5Vs:Z0MIjwYchtI:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JxELp5WZ5Vs:Z0MIjwYchtI:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=JxELp5WZ5Vs:Z0MIjwYchtI:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JxELp5WZ5Vs:Z0MIjwYchtI:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JxELp5WZ5Vs:Z0MIjwYchtI:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/JxELp5WZ5Vs\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/USA/Justice/2009/1228/Echoes-of-2001-shoe-bomber-in-Detroit-attack','2009-12-28 11:27:21','0','1','wire','','',null),
 ('2','US military is meeting recruitment goals with video games – but at what cost?','<p>Amid a soaring suicide rate among soldiers, it’s worth looking at how the Army’s aggressive video games distort our impressions of war.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/-Q9avRdvBSLEL_bMygni0R8LSJM/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/-Q9avRdvBSLEL_bMygni0R8LSJM/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/-Q9avRdvBSLEL_bMygni0R8LSJM/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/-Q9avRdvBSLEL_bMygni0R8LSJM/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=OQ_NvWEPvPo:f-jTXTN27cU:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=OQ_NvWEPvPo:f-jTXTN27cU:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=OQ_NvWEPvPo:f-jTXTN27cU:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=OQ_NvWEPvPo:f-jTXTN27cU:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=OQ_NvWEPvPo:f-jTXTN27cU:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=OQ_NvWEPvPo:f-jTXTN27cU:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/OQ_NvWEPvPo\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Commentary/Opinion/2009/1228/US-military-is-meeting-recruitment-goals-with-video-games-but-at-what-cost','2009-12-28 10:47:43','0','1','wire','','',null),
 ('3','Was Umar Farouk Abdulmutallab radicalized in London?','<p>The religious background and motivations of Umar Farouk Abdulmutallab, the Nigerian national accused of trying to blow up Northwest Airlines flight 253, are still unclear. But experts say his time in London may have helped fuel a militant world view. </p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/RmEFnONVxxY865dyxnzgmkaSkCk/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/RmEFnONVxxY865dyxnzgmkaSkCk/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/RmEFnONVxxY865dyxnzgmkaSkCk/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/RmEFnONVxxY865dyxnzgmkaSkCk/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=jI_3vdgebiY:NZKNzn-G8EI:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=jI_3vdgebiY:NZKNzn-G8EI:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=jI_3vdgebiY:NZKNzn-G8EI:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=jI_3vdgebiY:NZKNzn-G8EI:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=jI_3vdgebiY:NZKNzn-G8EI:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=jI_3vdgebiY:NZKNzn-G8EI:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/jI_3vdgebiY\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Europe/2009/1228/Was-Umar-Farouk-Abdulmutallab-radicalized-in-London','2009-12-28 10:38:42','0','1','wire','','',null),
 ('4','Skin whitening cream finds new popularity among Palestinian women','<p>Palestinian women are using skin whitening treatments as popular media are reasserting a \'fair-is-beautiful\' bid. But the message is not new and can be found even in old Arabic poetry.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/ranUIDkmuDDlH0SqHyh7J3DSV7Y/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/ranUIDkmuDDlH0SqHyh7J3DSV7Y/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/ranUIDkmuDDlH0SqHyh7J3DSV7Y/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/ranUIDkmuDDlH0SqHyh7J3DSV7Y/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=5IR-h3cBiWw:BOIUqzkh1wM:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=5IR-h3cBiWw:BOIUqzkh1wM:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=5IR-h3cBiWw:BOIUqzkh1wM:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=5IR-h3cBiWw:BOIUqzkh1wM:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=5IR-h3cBiWw:BOIUqzkh1wM:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=5IR-h3cBiWw:BOIUqzkh1wM:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/5IR-h3cBiWw\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Middle-East/2009/1228/Skin-whitening-cream-finds-new-popularity-among-Palestinian-women','2009-12-28 09:57:36','0','1','wire','','',null),
 ('5','TSA regulations vague on gadgets after Detroit incident','<p>Travelers were prohibited from using electronics on international flights this weekend, and in-flight WiFi and TV were banned, but TSA regulations keep changing.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/wNFLsGuRLaBWfTziT5hcq4FZt8o/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/wNFLsGuRLaBWfTziT5hcq4FZt8o/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/wNFLsGuRLaBWfTziT5hcq4FZt8o/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/wNFLsGuRLaBWfTziT5hcq4FZt8o/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=UmtvRqZQvxU:DqgvwDzeeq4:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=UmtvRqZQvxU:DqgvwDzeeq4:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=UmtvRqZQvxU:DqgvwDzeeq4:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=UmtvRqZQvxU:DqgvwDzeeq4:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=UmtvRqZQvxU:DqgvwDzeeq4:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=UmtvRqZQvxU:DqgvwDzeeq4:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/UmtvRqZQvxU\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Innovation/Horizons/2009/1228/TSA-regulations-vague-on-gadgets-after-Detroit-incident','2009-12-28 09:50:02','0','1','wire','','',null),
 ('6','Onetime foes, companies and activists find ways to cooperate','<p>Companies and activists are partnering on environmental, health, and other issues. Labor initiatives are more problematic.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/gZ546PXz0A_oocSOyuDnkzq_1BU/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/gZ546PXz0A_oocSOyuDnkzq_1BU/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/gZ546PXz0A_oocSOyuDnkzq_1BU/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/gZ546PXz0A_oocSOyuDnkzq_1BU/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=lLG4StPj6pQ:rH9FUpnxoC4:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=lLG4StPj6pQ:rH9FUpnxoC4:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=lLG4StPj6pQ:rH9FUpnxoC4:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=lLG4StPj6pQ:rH9FUpnxoC4:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=lLG4StPj6pQ:rH9FUpnxoC4:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=lLG4StPj6pQ:rH9FUpnxoC4:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/lLG4StPj6pQ\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Money/2009/1228/Onetime-foes-companies-and-activists-find-ways-to-cooperate','2009-12-28 09:30:47','0','1','wire','','',null),
 ('7','Gaza war anniversary: How one group helps victims overcome trauma','<p>The Healing the Wounds of War (HWW) program trains Gazans to use alternative nonmedical techniques to cope with stress from last year\'s Gaza war.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/dhmJ_uDIhj-P8ooRIu8IAn0jfOk/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/dhmJ_uDIhj-P8ooRIu8IAn0jfOk/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/dhmJ_uDIhj-P8ooRIu8IAn0jfOk/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/dhmJ_uDIhj-P8ooRIu8IAn0jfOk/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=r-OgX3IMTik:GQV793xoMTw:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=r-OgX3IMTik:GQV793xoMTw:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=r-OgX3IMTik:GQV793xoMTw:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=r-OgX3IMTik:GQV793xoMTw:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=r-OgX3IMTik:GQV793xoMTw:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=r-OgX3IMTik:GQV793xoMTw:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/r-OgX3IMTik\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Middle-East/2009/1228/Gaza-war-anniversary-How-one-group-helps-victims-overcome-trauma','2009-12-28 09:00:24','0','1','wire','','',null),
 ('8','D.C. Decoder: Where do your tax dollars actually go?','<p>In a recent survey, Americans said slashing money for space exploration would help balance the federal budget. But the bulk of tax dollars go to other programs.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/E-B9pf0GGwJtFk4kR2JSJNS_X0w/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/E-B9pf0GGwJtFk4kR2JSJNS_X0w/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/E-B9pf0GGwJtFk4kR2JSJNS_X0w/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/E-B9pf0GGwJtFk4kR2JSJNS_X0w/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=dN13AgCNahQ:7Xm8LlCCm90:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=dN13AgCNahQ:7Xm8LlCCm90:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=dN13AgCNahQ:7Xm8LlCCm90:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=dN13AgCNahQ:7Xm8LlCCm90:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=dN13AgCNahQ:7Xm8LlCCm90:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=dN13AgCNahQ:7Xm8LlCCm90:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/dN13AgCNahQ\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/USA/Politics/2009/1228/D.C.-Decoder-Where-do-your-tax-dollars-actually-go','2009-12-28 08:56:52','0','1','wire','','',null),
 ('9','Markets fail. That’s why we need markets.','<p>It may sound odd, but only free and open markets, not government regulation, can quickly and effectively clean up the mess that markets sometimes make.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/tdzkfkkPycq6IHfxeNav07Z4n68/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/tdzkfkkPycq6IHfxeNav07Z4n68/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/tdzkfkkPycq6IHfxeNav07Z4n68/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/tdzkfkkPycq6IHfxeNav07Z4n68/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=vXFiEYxNL04:YRREQc9kFHI:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=vXFiEYxNL04:YRREQc9kFHI:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=vXFiEYxNL04:YRREQc9kFHI:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=vXFiEYxNL04:YRREQc9kFHI:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=vXFiEYxNL04:YRREQc9kFHI:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=vXFiEYxNL04:YRREQc9kFHI:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/vXFiEYxNL04\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Commentary/Opinion/2009/1228/Markets-fail.-That-s-why-we-need-markets','2009-12-28 08:49:00','0','1','wire','','',null),
 ('10','James Hoggan talks about global warming','<p>James Hoggan, coauthor of \'Climate Cover-Up: The Crusade to Deny Global Warming,\' talks about what he calls the PR campaign to discredit global warming.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/z9B_Q5WoAdFokATIwHBsX_WSyTU/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/z9B_Q5WoAdFokATIwHBsX_WSyTU/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/z9B_Q5WoAdFokATIwHBsX_WSyTU/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/z9B_Q5WoAdFokATIwHBsX_WSyTU/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=evF2OlBlNgQ:CX_x1k1vcPo:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=evF2OlBlNgQ:CX_x1k1vcPo:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=evF2OlBlNgQ:CX_x1k1vcPo:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=evF2OlBlNgQ:CX_x1k1vcPo:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=evF2OlBlNgQ:CX_x1k1vcPo:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=evF2OlBlNgQ:CX_x1k1vcPo:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/evF2OlBlNgQ\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Environment/Bright-Green/2009/1228/James-Hoggan-talks-about-global-warming','2009-12-28 08:30:10','0','1','wire','','',null),
 ('11','Pragmatism spurs Russia and Georgia toward smoother relations','<p>Signs of a thaw between Russia and Georgia include the reopening of one border post on the major Caucasus highway and a possible move to resume direct air links. Relations between Russia and Georgia behave been in a freeze since last year\'s war over breakaway Georgian territories.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/y-2rc8v0RbPDvcOcL0jyLcuM4Bs/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/y-2rc8v0RbPDvcOcL0jyLcuM4Bs/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/y-2rc8v0RbPDvcOcL0jyLcuM4Bs/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/y-2rc8v0RbPDvcOcL0jyLcuM4Bs/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=byrSkGj7bdE:n7JSlw2j1w8:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=byrSkGj7bdE:n7JSlw2j1w8:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=byrSkGj7bdE:n7JSlw2j1w8:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=byrSkGj7bdE:n7JSlw2j1w8:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=byrSkGj7bdE:n7JSlw2j1w8:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=byrSkGj7bdE:n7JSlw2j1w8:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/byrSkGj7bdE\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Europe/2009/1228/Pragmatism-spurs-Russia-and-Georgia-toward-smoother-relations','2009-12-28 07:55:35','0','1','wire','','',null),
 ('12','Four ways to tax Wall Street’s rich','<p>As a first step, Congress may extend the estate tax. There are faster methods, too.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/1bRmrfM9-mGzj0B9zrCd7AP-sk0/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/1bRmrfM9-mGzj0B9zrCd7AP-sk0/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/1bRmrfM9-mGzj0B9zrCd7AP-sk0/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/1bRmrfM9-mGzj0B9zrCd7AP-sk0/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=aEYoFaWbgRs:B0QY3gtb7UA:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=aEYoFaWbgRs:B0QY3gtb7UA:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=aEYoFaWbgRs:B0QY3gtb7UA:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=aEYoFaWbgRs:B0QY3gtb7UA:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=aEYoFaWbgRs:B0QY3gtb7UA:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=aEYoFaWbgRs:B0QY3gtb7UA:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/aEYoFaWbgRs\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Commentary/David-R.-Francis/2009/1228/Four-ways-to-tax-Wall-Street-s-rich','2009-12-28 07:54:55','0','1','wire','','',null),
 ('13','All Kindle, all the time','<p>Book headlines are starting to look like all Kindle, all the time.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/jt6RzAi9PIQjs8DGFLJyNQs5nHY/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/jt6RzAi9PIQjs8DGFLJyNQs5nHY/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/jt6RzAi9PIQjs8DGFLJyNQs5nHY/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/jt6RzAi9PIQjs8DGFLJyNQs5nHY/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=wq2TklJWO9k:ziGsAfl2enM:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=wq2TklJWO9k:ziGsAfl2enM:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=wq2TklJWO9k:ziGsAfl2enM:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=wq2TklJWO9k:ziGsAfl2enM:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=wq2TklJWO9k:ziGsAfl2enM:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=wq2TklJWO9k:ziGsAfl2enM:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/wq2TklJWO9k\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Books/chapter-and-verse/2009/1228/All-Kindle-all-the-time','2009-12-28 07:29:38','0','1','wire','','',null),
 ('14','After Sunday clashes in Iran, \'Green Movement\' supporters take stock','<p>Following Iran clashes on Sunday between Green Movement supporters and Iranian security forces left at least 10 people dead, reformists say hundreds of supporters have been arrested. Now supporters of change are speculating about what comes next.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/wqnQNI-PpXUtTG9dsUC7PPnOpUM/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/wqnQNI-PpXUtTG9dsUC7PPnOpUM/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/wqnQNI-PpXUtTG9dsUC7PPnOpUM/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/wqnQNI-PpXUtTG9dsUC7PPnOpUM/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=M1GzPqtMjaA:Bqic1QSItFw:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=M1GzPqtMjaA:Bqic1QSItFw:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=M1GzPqtMjaA:Bqic1QSItFw:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=M1GzPqtMjaA:Bqic1QSItFw:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=M1GzPqtMjaA:Bqic1QSItFw:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=M1GzPqtMjaA:Bqic1QSItFw:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/M1GzPqtMjaA\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Middle-East/2009/1228/After-Sunday-clashes-in-Iran-Green-Movement-supporters-take-stock','2009-12-28 06:23:37','0','1','wire','','',null),
 ('15','Nigerian terror attack suspect: a life of privilege and elite schools','<p>Nigerian terror attack suspect Umar Farouk Abdulmutallab attended a British school in West Africa and then studied in London. He had been estranged from his family before the attack.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/55uEhzEvAxzhZtzgQTv4JuWrfSI/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/55uEhzEvAxzhZtzgQTv4JuWrfSI/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/55uEhzEvAxzhZtzgQTv4JuWrfSI/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/55uEhzEvAxzhZtzgQTv4JuWrfSI/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Z1I9JUwvuEk:idbCCAl44-s:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Z1I9JUwvuEk:idbCCAl44-s:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Z1I9JUwvuEk:idbCCAl44-s:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=Z1I9JUwvuEk:idbCCAl44-s:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Z1I9JUwvuEk:idbCCAl44-s:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Z1I9JUwvuEk:idbCCAl44-s:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/Z1I9JUwvuEk\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/2009/1228/Nigerian-terror-attack-suspect-a-life-of-privilege-and-elite-schools','2009-12-28 06:02:33','0','1','wire','','',null),
 ('16','In Cheap We Trust','<p>A cheapskate herself, Lauren Weber considers how and why Americans spend.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/guZKDHMSRXu-uLgMLKuNjDiFw98/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/guZKDHMSRXu-uLgMLKuNjDiFw98/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/guZKDHMSRXu-uLgMLKuNjDiFw98/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/guZKDHMSRXu-uLgMLKuNjDiFw98/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JNYGWf-Jjis:YJPqLPRplAs:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JNYGWf-Jjis:YJPqLPRplAs:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JNYGWf-Jjis:YJPqLPRplAs:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=JNYGWf-Jjis:YJPqLPRplAs:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JNYGWf-Jjis:YJPqLPRplAs:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=JNYGWf-Jjis:YJPqLPRplAs:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/JNYGWf-Jjis\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Books/2009/1228/In-Cheap-We-Trust','2009-12-28 05:59:24','0','1','wire','','',null),
 ('17','Detroit terrorist attack on Flight 253: How Obama must respond','<p>The suspected jet bomber, Umar Farouk Abdulmutallab, says he was trained in Yemen, the lawless land of Al Qaeda affliates. Obama might be able to prevent more suicide bombers with preemptive action in that growing terrorist haven.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/_nGWeSF7zRbvf07tyY7ZvCgTQUI/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/_nGWeSF7zRbvf07tyY7ZvCgTQUI/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/_nGWeSF7zRbvf07tyY7ZvCgTQUI/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/_nGWeSF7zRbvf07tyY7ZvCgTQUI/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=sx-8vHNl0uA:wULfu_cEMto:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=sx-8vHNl0uA:wULfu_cEMto:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=sx-8vHNl0uA:wULfu_cEMto:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=sx-8vHNl0uA:wULfu_cEMto:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=sx-8vHNl0uA:wULfu_cEMto:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=sx-8vHNl0uA:wULfu_cEMto:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/sx-8vHNl0uA\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Commentary/the-monitors-view/2009/1228/Detroit-terrorist-attack-on-Flight-253-How-Obama-must-respond','2009-12-28 05:55:40','0','1','wire','','',null),
 ('18','Yemen arrests 29 Al Qaeda, gets increased US military support','<p>Yemen is allegedly becoming a hub for Al Qaeda militants and is garnering increased US military support. A Nigerian national who attempted to bomb a Northwest passenger flight on Christmas claimed ties to Al Qaeda in Yemen.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/vDT7loU1AfCmnRfFP601P1r0PlA/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/vDT7loU1AfCmnRfFP601P1r0PlA/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/vDT7loU1AfCmnRfFP601P1r0PlA/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/vDT7loU1AfCmnRfFP601P1r0PlA/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=bCAx-qAy2BI:b-eIfkL5ZGw:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=bCAx-qAy2BI:b-eIfkL5ZGw:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=bCAx-qAy2BI:b-eIfkL5ZGw:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=bCAx-qAy2BI:b-eIfkL5ZGw:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=bCAx-qAy2BI:b-eIfkL5ZGw:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=bCAx-qAy2BI:b-eIfkL5ZGw:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/bCAx-qAy2BI\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/terrorism-security/2009/1228/Yemen-arrests-29-Al-Qaeda-gets-increased-US-military-support','2009-12-28 04:58:38','0','1','wire','','',null),
 ('19','Why did US let Abdulmutallab get on a plane to Detroit?','<p>The father of Umar Farouk Abdulmutallab says he told US officials months ago that his son might be a terrorist threat. Some lawmakers say the Obama administration missed the warning signs – just as it did before the Fort Hood attack.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/r_qUet_nuLASP4iUBR4RB_WVLO0/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/r_qUet_nuLASP4iUBR4RB_WVLO0/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/r_qUet_nuLASP4iUBR4RB_WVLO0/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/r_qUet_nuLASP4iUBR4RB_WVLO0/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=S6jTQzgWud8:aqty-xip7jY:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=S6jTQzgWud8:aqty-xip7jY:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=S6jTQzgWud8:aqty-xip7jY:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=S6jTQzgWud8:aqty-xip7jY:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=S6jTQzgWud8:aqty-xip7jY:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=S6jTQzgWud8:aqty-xip7jY:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/S6jTQzgWud8\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/USA/2009/1227/Why-did-US-let-Abdulmutallab-get-on-a-plane-to-Detroit','2009-12-27 12:58:57','0','1','wire','','',null),
 ('20','Detroit attack: terrorist once again confounds airport security','<p>From 9/11 to the Christmas Day attack on Northwest Flight 253 over Detroit, terrorists have changed their tactics to get around airport security. </p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/7klBqKICZWMk8ILBqvAiFWRb8Vs/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/7klBqKICZWMk8ILBqvAiFWRb8Vs/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/7klBqKICZWMk8ILBqvAiFWRb8Vs/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/7klBqKICZWMk8ILBqvAiFWRb8Vs/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=7Bt0OAmci7Y:wOPrDQwpEMg:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=7Bt0OAmci7Y:wOPrDQwpEMg:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=7Bt0OAmci7Y:wOPrDQwpEMg:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=7Bt0OAmci7Y:wOPrDQwpEMg:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=7Bt0OAmci7Y:wOPrDQwpEMg:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=7Bt0OAmci7Y:wOPrDQwpEMg:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/7Bt0OAmci7Y\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/USA/2009/1227/Detroit-attack-terrorist-once-again-confounds-airport-security','2009-12-27 10:19:21','0','1','wire','','',null),
 ('21','Will the West\'s criticism of China for jailing top dissident backfire?','<p>The Chrismas Day sentencing of literary critic Liu Xiaobo to 11 years in prison has drawn unusually strong criticism from Western governments, but some experts say that may only result in China taking a harder line.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/IL5p7c_BqXU8dKMZaU9YGFLYClI/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/IL5p7c_BqXU8dKMZaU9YGFLYClI/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/IL5p7c_BqXU8dKMZaU9YGFLYClI/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/IL5p7c_BqXU8dKMZaU9YGFLYClI/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Qv-RjnYMfVE:yCNtxc0VfIU:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Qv-RjnYMfVE:yCNtxc0VfIU:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Qv-RjnYMfVE:yCNtxc0VfIU:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=Qv-RjnYMfVE:yCNtxc0VfIU:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Qv-RjnYMfVE:yCNtxc0VfIU:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=Qv-RjnYMfVE:yCNtxc0VfIU:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/Qv-RjnYMfVE\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Asia-Pacific/2009/1227/Will-the-West-s-criticism-of-China-for-jailing-top-dissident-backfire','2009-12-27 10:11:26','0','1','wire','','',null),
 ('22','Hello, 2010. Goodbye, Decade of Cringe','<p>Who could have predicted the twists and turns of the past decade -- from hanging chads to 9/11, the bubble economy to Bernie Madoff? It is tempting to believe anything will be better than the Decade of Cringe. But the next 10 years will dismay and delight as well.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/EAAZHKX9nmxgUvrl4yhI615DCYQ/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/EAAZHKX9nmxgUvrl4yhI615DCYQ/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/EAAZHKX9nmxgUvrl4yhI615DCYQ/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/EAAZHKX9nmxgUvrl4yhI615DCYQ/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=3PAnDYvKa_M:mTvnkmfEPnQ:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=3PAnDYvKa_M:mTvnkmfEPnQ:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=3PAnDYvKa_M:mTvnkmfEPnQ:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=3PAnDYvKa_M:mTvnkmfEPnQ:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=3PAnDYvKa_M:mTvnkmfEPnQ:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=3PAnDYvKa_M:mTvnkmfEPnQ:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/3PAnDYvKa_M\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/Commentary/editors-blog/2009/1227/Hello-2010.-Goodbye-Decade-of-Cringe','2009-12-27 10:03:23','0','1','wire','','',null),
 ('23','Umar Farouk Abdulmutallab: Bomb suspect\'s teacher and family shocked','<p>Nigerian Information Minister Dora Akunyili told reporters Sunday that Umar Farouk Abdulmutallab passed through Nigeria for only one day before attempting to blow up a Northwest flight headed for Detroit via Amsterdam.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/G1PwIMdTFCnXB3Yfd0prk0Bbg60/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/G1PwIMdTFCnXB3Yfd0prk0Bbg60/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/G1PwIMdTFCnXB3Yfd0prk0Bbg60/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/G1PwIMdTFCnXB3Yfd0prk0Bbg60/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=g75rfBg5Qkc:8-E3_35XIp4:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=g75rfBg5Qkc:8-E3_35XIp4:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=g75rfBg5Qkc:8-E3_35XIp4:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=g75rfBg5Qkc:8-E3_35XIp4:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=g75rfBg5Qkc:8-E3_35XIp4:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=g75rfBg5Qkc:8-E3_35XIp4:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/g75rfBg5Qkc\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Africa/2009/1227/Umar-Farouk-Abdulmutallab-Bomb-suspect-s-teacher-and-family-shocked','2009-12-27 09:36:38','0','1','wire','','',null),
 ('24','West Bank killings could scupper Shalit prisoner deal','<p>On the eve of the anniversary of the Gaza war, Israeli forces killed three suspected Palestinian militants accused of being responsible for the killing of an Israeli motorist. The suspected militants were once Israeli prisoners, authorities say.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/5DfCtUFUJFy--xYCnHYmiPHQM0I/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/5DfCtUFUJFy--xYCnHYmiPHQM0I/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/5DfCtUFUJFy--xYCnHYmiPHQM0I/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/5DfCtUFUJFy--xYCnHYmiPHQM0I/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=YKvVNHSEC4w:0n33vINgwn4:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=YKvVNHSEC4w:0n33vINgwn4:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=YKvVNHSEC4w:0n33vINgwn4:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=YKvVNHSEC4w:0n33vINgwn4:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=YKvVNHSEC4w:0n33vINgwn4:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=YKvVNHSEC4w:0n33vINgwn4:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/YKvVNHSEC4w\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Middle-East/2009/1227/West-Bank-killings-could-scupper-Shalit-prisoner-deal','2009-12-27 09:08:39','0','1','wire','','',null),
 ('25','Al Qaeda rises in West Africa','<p>Umar Farouk Abdulmutallab – the terror suspect who allegedly tried to blow up a plane over Detroit on Christmas Day – hails from Nigeria in West Africa. The Monitor takes a look at how the fight against Al Qaeda is going in the region.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/OCZLfNsiNdHJEJf4oeJcQ6-b8AI/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/OCZLfNsiNdHJEJf4oeJcQ6-b8AI/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/OCZLfNsiNdHJEJf4oeJcQ6-b8AI/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/OCZLfNsiNdHJEJf4oeJcQ6-b8AI/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=BzU_cs8sL7U:33FZAq8vYqA:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=BzU_cs8sL7U:33FZAq8vYqA:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=BzU_cs8sL7U:33FZAq8vYqA:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=BzU_cs8sL7U:33FZAq8vYqA:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=BzU_cs8sL7U:33FZAq8vYqA:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=BzU_cs8sL7U:33FZAq8vYqA:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/BzU_cs8sL7U\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/Africa/2009/1228/Al-Qaeda-rises-in-West-Africa','2009-12-27 08:38:06','0','1','wire','','',null),
 ('26','Reports: Iran protesters killed by government forces','<p>Increasingly vocal opposition groups are now calling for an end to the Islamic Republic after a weekend of violent clashes during the Shiite holiday of Ashura.</p>\n<p><a href=\"http://feedads.g.doubleclick.net/~a/4y062SDo02CWmJyAPTCUtC0BVMs/0/da\"><img src=\"http://feedads.g.doubleclick.net/~a/4y062SDo02CWmJyAPTCUtC0BVMs/0/di\" border=\"0\" ismap=\"true\"></img></a><br/>\n<a href=\"http://feedads.g.doubleclick.net/~a/4y062SDo02CWmJyAPTCUtC0BVMs/1/da\"><img src=\"http://feedads.g.doubleclick.net/~a/4y062SDo02CWmJyAPTCUtC0BVMs/1/di\" border=\"0\" ismap=\"true\"></img></a></p><div>\n<a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=IVtQtD10UGI:Slu8KrB1ts4:yIl2AUoC8zA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=yIl2AUoC8zA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=IVtQtD10UGI:Slu8KrB1ts4:7Q72WNTAKBA\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=7Q72WNTAKBA\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=IVtQtD10UGI:Slu8KrB1ts4:V_sGLiPBpWU\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?i=IVtQtD10UGI:Slu8KrB1ts4:V_sGLiPBpWU\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=IVtQtD10UGI:Slu8KrB1ts4:qj6IDK7rITs\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=qj6IDK7rITs\" border=\"0\"></img></a> <a href=\"http://rss.csmonitor.com/~ff/feeds/top?a=IVtQtD10UGI:Slu8KrB1ts4:dnMXMwOfBR0\"><img src=\"http://feeds.feedburner.com/~ff/feeds/top?d=dnMXMwOfBR0\" border=\"0\"></img></a>\n</div><img src=\"http://feeds.feedburner.com/~r/feeds/top/~4/IVtQtD10UGI\" height=\"1\" width=\"1\" />','Christian Science Monitor','http://www.csmonitor.com/World/terrorism-security/2009/1227/Reports-Iran-protesters-killed-by-government-forces','2009-12-27 06:56:38','0','1','wire','','',null);

DROP TABLE IF EXISTS `NotificationMessages`;

CREATE TABLE `NotificationMessages` (
  `msgid` int(11) unsigned NOT NULL auto_increment,
  `userid` int(11) default '0',
  `type` enum('sharedStory') default 'sharedStory',
  `itemid` int(11) default '0',
  `subject` varchar(255) default '',
  `message` text,
  `embed` text,
  `dateCreated` datetime default NULL,
  `lastAttempt` datetime default NULL,
  `status` enum('sent','pending','blocked','approved') default 'pending',
  PRIMARY KEY  (`msgid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Notifications`;

CREATE TABLE `Notifications` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `msgid` int(11) default '0',
  `status` enum('sent','pending','error','opened') default 'pending',
  `userid` int(11) default '0',
  `dateSent` datetime default NULL,
  `toUserId` int(11) default '0',
  `toFbId` bigint(20) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Orders`;

CREATE TABLE `Orders` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userid` bigint(20) default '0',
  `prizeid` int(11) default '0',
  `pointCost` int(8) default '0',
  `dateSubmitted` datetime default NULL,
  `dateApproved` datetime default NULL,
  `dateShipped` datetime default NULL,
  `dateCanceled` datetime default NULL,
  `dateRefunded` datetime default NULL,
  `reviewedBy` varchar(255) default '',
  `status` enum('submitted','approved','shipped','canceled','refunded') default 'submitted',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `OutboundMessages`;

CREATE TABLE `OutboundMessages` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userIntro` varchar(255) default '',
  `msgType` enum('notification','announce') default 'announce',
  `subject` varchar(255) default '',
  `msgBody` text,
  `buttonLinkText` varchar(255) default '',
  `closingLinkText` varchar(255) default '',
  `shortLink` varchar(25) default '',
  `userGroup` varchar(255) default '',
  `userid` bigint(20) unsigned default '0',
  `t` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `status` enum('sent','pending','hold','incomplete') default 'pending',
  `usersReceived` text,
  `numUsersReceived` int(11) unsigned default '0',
  `numUsersExpected` int(11) unsigned default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Photos`;

CREATE TABLE `Photos` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) default '',
  `shortName` varchar(25) default '',
  `description` text,
  `dateCreated` datetime default NULL,
  `userid` int(11) default '0',
  `status` enum('approved','pending','blocked') default 'pending',
  `filename` varchar(255) default NULL,
  `challengeCompletedId` int(11) unsigned default NULL,
  `localFilename` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Prizes`;

CREATE TABLE `Prizes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) default '',
  `shortName` varchar(25) default '',
  `description` text,
  `sponsor` varchar(150) default '',
  `sponsorUrl` varchar(255) default '',
  `dateStart` datetime default NULL,
  `dateEnd` datetime default NULL,
  `initialStock` int(4) default '0',
  `currentStock` int(4) default '0',
  `pointCost` int(4) default '1000',
  `eligibility` enum('team','general') default 'team',
  `userMaximum` int(4) default '0',
  `status` enum('enabled','disabled','hold') default 'enabled',
  `orderFieldsNeeded` varchar(150) default 'name address phone email',
  `thumbnail` varchar(255) default 'default_prize_thumb.png',
  `isWeekly` tinyint(1) default '0',
  `isGrand` tinyint(1) default '0',
  `isFeatured` tinyint(1) default '0',
  `dollarValue` int(6) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Subscriptions`;

CREATE TABLE `Subscriptions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userid` bigint(20) default '0',
  `rxFeatures` tinyint(1) default '0',
  `rxMode` enum('notification','sms','email') default 'notification',
  `lastFeatureSent` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

insert into `Subscriptions` values('1','1','1','notification','0000-00-00 00:00:00');

DROP TABLE IF EXISTS `SystemStatus`;

CREATE TABLE `SystemStatus` (
  `id` int(4) unsigned NOT NULL auto_increment,
  `name` varchar(35) default '',
  `strValue` text,
  `numValue` bigint(20) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

insert into `SystemStatus` values('1','dm_dynamicTemplate.class.php',null,'1262061017'),
 ('2','dm_user.class.php',null,'1262061017'),
 ('3','dm_content.class.php',null,'1262061019'),
 ('4','dm_comments.class.php',null,'1262061019'),
 ('5','dm_cron.class.php',null,'1262061019'),
 ('6','dm_log.class.php',null,'1262061019'),
 ('7','dm_feed.class.php',null,'1262061020'),
 ('8','dm_notifications.class.php',null,'1262061020'),
 ('9','cloudid',null,'1'),
 ('10','max_sessions',null,'500'),
 ('11','dm_userBlogs.class.php',null,'1262061020'),
 ('12','dm_widgets.class.php',null,'1262061020'),
 ('13','dm_adCode.class.php',null,'1262061020'),
 ('14','dm_subscriptions.class.php',null,'1262061020'),
 ('15','dm_forum.class.php',null,'1262061020'),
 ('16','dm_prizes.class.php',null,'1262061021'),
 ('17','dm_video.class.php',null,'1262061021'),
 ('18','dm_photo.class.php',null,'1262061021'),
 ('19','dm_challenges.class.php',null,'1262061021'),
 ('20','dm_scores.class.php',null,'1262061021'),
 ('21','dm_contactEmails.class.php',null,'1262061022'),
 ('22','dm_featuredTemplate.class.php',null,'1262061022'),
 ('23','dm_contentImages.class.php',null,'1262061022'),
 ('24','dm_adTrack.class.php',null,'1262061022'),
 ('25','dm_orders.class.php',null,'1262061022'),
 ('26','dm_outboundMessages.class.php',null,'1262061022'),
 ('27','dm_ask.class.php',null,'1262061022'),
 ('28','dm_ideas.class.php',null,'1262061022'),
 ('29','dm_tags.class.php',null,'1262061022'),
 ('30','dm_micro.class.php',null,'1262061022'),
 ('31','fbApp_use_iframe',null,'0'),
 ('32','fbApp_wide_mode',null,'1'),
 ('33','fbApp_installable',null,'1'),
 ('34','fbApp_privacy_url','http://apps.facebook.com/defaultapp/?p=tos','0'),
 ('35','fbApp_help_url','http://apps.facebook.com/defaultapp/?p=contact','0'),
 ('36','fbApp_callback_url','http://default.newsi.us/facebook','0'),
 ('37','fbApp_application_name','Default Site Title','0'),
 ('38','fbApp_tab_default_name','Default Site Title','0'),
 ('39','fbApp_authorize_url','http://default.newsi.us/facebook?p=postAuth&m=add','0'),
 ('40','fbApp_profile_tab_url','http://default.newsi.us/facebook?p=ajax&m=appTab','0'),
 ('41','fbApp_uninstall_url','http://default.newsi.us/facebook?p=postAuth&m=remove','0'),
 ('42','fbApp_email','support@default.com','0'),
 ('43','fbApp_post_authorize_redirect_url','http://apps.facebook.com/defaultapp/?p=team','0'),
 ('44','fbApp_publish_self_url','http://default.newsi.us/facebook?p=ajax&m=wallPublisher&self','0'),
 ('45','fbApp_publish_self_action','Default Site Title','0'),
 ('46','fbApp_publish_action','Default Site Title','0'),
 ('47','fbApp_message_action','Default Site Title','0');

DROP TABLE IF EXISTS `TaggedObjects`;

CREATE TABLE `TaggedObjects` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `tagid` int(10) unsigned NOT NULL default '0',
  `userid` bigint(20) unsigned NOT NULL default '0',
  `itemid` bigint(20) unsigned NOT NULL default '0',
  `itemType` enum('story','ask','idea','stuff') default 'story',
  `dt` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Tags`;

CREATE TABLE `Tags` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `tag` varchar(50) default '',
  `raw_tag` varchar(75) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

insert into `Tags` values('1','education','education'),
 ('2','health','health'),
 ('3','music','music'),
 ('4','technology','technology'),
 ('5','food','food'),
 ('6','politics','politics'),
 ('7','transportation','transportation'),
 ('8','lifestyle','lifestyle'),
 ('9','arts','arts'),
 ('10','sports','sports'),
 ('11','business','business'),
 ('12','gardening','gardening'),
 ('13','travel','travel'),
 ('14','recreation','recreation'),
 ('15','government','government'),
 ('16','environment','environment');

DROP TABLE IF EXISTS `Templates`;

CREATE TABLE `Templates` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `shortName` varchar(255) default NULL,
  `code` blob,
  `category` varchar(128) default NULL,
  `helpString` blob,
  `hasChanged` tinyint(1) default '0',
  `lastChange` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

insert into `Templates` values('1','microIntro',0x3c68313e526563656e746c79205477656574656420696e204e6577733c2f68313e3c703e486572652069732077686174206f75722073656c656374696f6e206f66204e65777320747769747465726572732061726520736179696e67207269676874206e6f772e3c2f703e,'micro',0x54686520696e74726f20706172616772617068206f6e20746865204d6963726f426c6f672070616765,'0','0000-00-00 00:00:00'),
 ('2','microShareTitle',0x3c703e412073756d6d617279206f66205477697474657265727320666f72204e6577733c2f703e,'micro',0x5469746c6520696e206c696e6b20666f72204d6963726f2070616765,'0','0000-00-00 00:00:00'),
 ('3','microShareCaption',0x3c703e44656661756c742053697465205469746c65206d616b6573206974206561737920746f2066696e6420746865206d6f73742072656c6576616e7420547765657465727320666f722053656174746c6520616c6c20696e206f6e6520706c616365213c2f703e,'micro',0x43617074696f6e20696e206c696e6b20666f72204d6963726f2070616765,'0','0000-00-00 00:00:00'),
 ('4','microList',0x3c64697620636c6173733d226c6973745f73746f7269657320636c656172666978223e3c756c3e7b6974656d737d3c2f756c3e3c2f6469763e,'micro','','0','0000-00-00 00:00:00'),
 ('5','microItem',0x3c6c6920636c6173733d226d6963726f506f737457726170223e3c64697620636c6173733d227468756d62223e3c6120687265663d22687474703a2f2f747769747465722e636f6d2f7b73686f72744e616d657d22207461726765743d2274776974746572223e3c696d67207372633d227b70726f66696c655f696d6167655f75726c7d2220616c743d2270686f746f206f66207b73686f72744e616d657d223e3c2f613e3c2f6469763e3c64697620636c6173733d2273746f7279426c6f636b57726170223e3c7020636c6173733d226d6963726f48656164223e3c7374726f6e673e3c6120687265663d22687474703a2f2f747769747465722e636f6d2f7b73686f72744e616d657d22207461726765743d2274776974746572223e7b73686f72744e616d657d3c2f613e3c2f7374726f6e673e207b706f73747d3c2f703e3c64697620636c6173733d2273746f7279426c6f636b4d657461223e3c703e506f7374656420696e207b7461677d2c207b74696d6553696e63657d2061676f3c2f703e3c7370616e20636c6173733d2273746f7279436f6d6d616e6473223e3c7461626c652063656c6c73706163696e673d2230223e3c74626f64793e3c74723e3c74643e7b636d645265706c797d3c2f74643e3c74643e7b636d64526574776565747d3c2f74643e3c74643e7b636d64444d7d3c2f74643e3c74643e7b636d6453686172657d3c2f74643e3c2f74723e3c2f74626f64793e3c2f7461626c653e3c2f7370616e3e3c2f6469763e3c212d2d20656e642073746f7279426c6f636b4d657461202d2d3e3c2f6469763e3c212d2d20656e642073746f7279426c6f636b57726170202d2d3e3c2f6c693e,'micro',0x4974656d2074656d706c61746520666f72204d6963726f626c6f67206974656d73,'0','0000-00-00 00:00:00'),
 ('6','microItemHome',0x3c6c6920636c6173733d226d6963726f506f737457726170223e3c64697620636c6173733d227468756d62223e3c6120687265663d223f703d747765657473266f3d766965772669643d7b69647d22206f6e636c69636b3d227377697463685061676528276d6963726f272c2776696577272c7b69647d293b2072657475726e2066616c73653b22207461726765743d2274776974746572223e3c696d67207372633d227b70726f66696c655f696d6167655f75726c7d2220616c743d2270686f746f206f66207b73686f72744e616d657d223e3c2f613e3c2f6469763e3c64697620636c6173733d2273746f7279426c6f636b57726170223e3c7020636c6173733d226d6963726f48656164223e3c7374726f6e673e3c6120687265663d223f703d747765657473266f3d766965772669643d7b69647d22206f6e636c69636b3d227377697463685061676528276d6963726f272c2776696577272c7b69647d293b2072657475726e2066616c73653b22207461726765743d2274776974746572223e7b73686f72744e616d657d3c2f613e3c2f7374726f6e673e207b706f73747d3c2f703e3c64697620636c6173733d2273746f7279426c6f636b4d657461223e3c703e506f7374656420696e207b7461677d2c207b74696d6553696e63657d2061676f3c2f703e3c2f6469763e3c212d2d20656e642073746f7279426c6f636b4d657461202d2d3e3c2f6469763e3c212d2d20656e642073746f7279426c6f636b57726170202d2d3e3c2f6c693e,'micro',0x4974656d2074656d706c61746520666f72204d6963726f626c6f67206974656d73206f6e2074686520686f6d652070616765,'0','0000-00-00 00:00:00');

DROP TABLE IF EXISTS `User`;

CREATE TABLE `User` (
  `userid` bigint(20) unsigned NOT NULL auto_increment,
  `ncUid` bigint(20) default '0',
  `name` varchar(255) default '',
  `email` varchar(255) default '',
  `isAdmin` tinyint(1) default '0',
  `isBlocked` tinyint(1) default '0',
  `votePower` int(2) default '1',
  `remoteStatus` enum('noverify','verified','purged') default 'noverify',
  `isMember` tinyint(1) default '0',
  `isModerator` tinyint(1) default '0',
  `isSponsor` tinyint(1) default '0',
  `isEmailVerified` tinyint(1) default '0',
  `isResearcher` tinyint(1) default '0',
  `acceptRules` tinyint(1) default '0',
  `optInStudy` tinyint(1) default '1',
  `optInEmail` tinyint(1) default '1',
  `optInProfile` tinyint(1) default '1',
  `optInFeed` tinyint(1) default '1',
  `optInSMS` tinyint(1) default '1',
  `dateRegistered` datetime default NULL,
  `eligibility` enum('team','general','ineligible') default 'team',
  `cachedPointTotal` int(4) default '0',
  `cachedPointsEarned` int(4) default '0',
  `cachedPointsEarnedThisWeek` int(4) default '0',
  `cachedPointsEarnedLastWeek` int(4) default '0',
  `cachedStoriesPosted` int(4) default '0',
  `cachedCommentsPosted` int(4) default '0',
  `userLevel` varchar(25) default 'reader',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

insert into `User` values('1','60524','Default Site Title Administrator','admin@default.com','1','0','1','','0','0','0','0','0','0','0','0','0','0','0','0000-00-00 00:00:00','team','0','0','0','0','0','0','');

DROP TABLE IF EXISTS `UserBlogs`;

CREATE TABLE `UserBlogs` (
  `blogid` int(11) unsigned NOT NULL auto_increment,
  `siteContentId` int(11) default '0',
  `userid` int(11) default '0',
  `title` varchar(255) default '',
  `entry` text,
  `url` varchar(255) default '',
  `imageUrl` varchar(255) default '',
  `videoEmbed` varchar(255) default '',
  `status` enum('draft','published') default 'draft',
  PRIMARY KEY  (`blogid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `UserInfo`;

CREATE TABLE `UserInfo` (
  `userid` bigint(20) unsigned NOT NULL,
  `fbId` bigint(20) default '0',
  `isAppAuthorized` tinyint(1) default '0',
  `networkid` int(11) default '0',
  `birthdate` datetime default NULL,
  `age` tinyint(1) default '0',
  `rxConsentForm` tinyint(1) default '0',
  `gender` enum('male','female','other') default NULL,
  `researchImportance` tinyint(1) default '0',
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `lastUpdated` datetime default NULL,
  `friends` text,
  `memberFriends` text,
  `numFriends` int(4) default '0',
  `numMemberFriends` int(4) default '0',
  `lastInvite` datetime default NULL,
  `lastProfileUpdate` datetime default NULL,
  `lastRemoteSyncUpdate` datetime default NULL,
  `interests` text,
  `bio` text,
  `phone` varchar(255) default '',
  `address1` varchar(255) default '',
  `address2` varchar(255) default '',
  `city` varchar(255) default 'Unknown',
  `state` varchar(255) default '',
  `country` varchar(255) default '',
  `zip` varchar(255) default '',
  `neighborhood` varchar(100) default '',
  `groups` text,
  `networks` text,
  `refuid` bigint(20) unsigned default '0',
  `lastNetSync` datetime default NULL,
  `cachedFriendsInvited` int(4) default '0',
  `cachedChallengesCompleted` int(4) default '0',
  `hideTipStories` tinyint(1) default '0',
  `hideTeamIntro` tinyint(1) default '0',
  `noCommentNotify` tinyint(1) default '0',
  `lastUpdateLevels` datetime default NULL,
  `lastUpdateSiteChallenges` datetime default NULL,
  `lastUpdateCachedPointsAndChallenges` datetime default NULL,
  `lastUpdateCachedCommentsAndStories` datetime default NULL,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `UserInfo` values('1','0','0','0','0000-00-00 00:00:00','0','0','','0','0000-00-00 00:00:00','0000-00-00 00:00:00','','','0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','','','','','','','','','','','','','0','0000-00-00 00:00:00','0','0','0','0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');

DROP TABLE IF EXISTS `UserInvites`;

CREATE TABLE `UserInvites` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) default '0',
  `friendFbId` bigint(20) default '0',
  `dateInvited` datetime default NULL,
  `dateAccepted` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Videos`;

CREATE TABLE `Videos` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `service` enum('youtube','seesmic','facebook') default 'youtube',
  `title` varchar(255) default '',
  `shortName` varchar(25) default '',
  `description` text,
  `dateCreated` datetime default NULL,
  `userid` int(11) default '0',
  `status` enum('approved','pending','blocked') default 'pending',
  `challengeCompletedId` int(11) unsigned default NULL,
  `embedCode` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `WeeklyScores`;

CREATE TABLE `WeeklyScores` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userid` bigint(20) default '0',
  `weekOf` datetime default NULL,
  `eligibilityGroup` enum('team','general','ineligible') default 'general',
  `pointTotal` int(4) default '10',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Widgets`;

CREATE TABLE `Widgets` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) default '',
  `wrap` text,
  `html` text,
  `smartsize` tinyint(1) default '0',
  `width` int(2) default '0',
  `height` int(2) default '0',
  `style` varchar(255) default '',
  `type` enum('fbml','src','script') default 'fbml',
  `isAd` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

insert into `Widgets` values('1','kfintro','<div id=\"featurePanel\" class=\"clearfix\">\r\n<div class=\"panelBar clearfix\">\r\n<h2>Featured Video</h2>\r\n<div class=\"bar_link\">About our funder, the <a href=\"http://knightfoundation.org\" target=\"_blank\">Knight Foundation</a></div>\r\n</div><div class=\"subtitle\"><a href=\"http://blog.newscloud.com/2008/12/knight_announce.html\" target=\"_blank\">Learn more</a> about NewsCloud\'s open source grant</div><!--end \"panelBar\"-->\r\n<div style=\"background-color:#FFFFFF;text-align:center;\">{widget}</div>\r\n</div><!--end \"featurePanel\"-->\r\n\r\n','<object width=\"400\" height=\"230\"><param name=\"allowfullscreen\" value=\"true\" /><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"movie\" value=\"http://vimeo.com/moogaloop.swf?clip_id=4358677&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" /><embed src=\"http://vimeo.com/moogaloop.swf?clip_id=4358677&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" allowscriptaccess=\"always\" width=\"400\" height=\"230\"></embed></object>','0','480','240','','script','0'),
 ('2','evan','<div id=\"featurePanel\" class=\"clearfix\">\r\n<div class=\"panelBar clearfix\">\r\n<h2>Featured Video</h2>\r\n<div class=\"bar_link\">Sometimes Daily Reports on Evan Ratliff\'s Capture</div>\r\n</div><!--end \"panelBar\"-->\r\n<center>{widget}</center>\r\n</div><!--end \"featurePanel\"-->\r\n\r\n','<embed src=\"http://blip.tv/play/g55igaDBcgI%2Em4v\" type=\"application/x-shockwave-flash\" width=\"480\" height=\"299\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed> ','0','480','310','','script','0');

DROP TABLE IF EXISTS `cronJobs`;

CREATE TABLE `cronJobs` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `task` varchar(64) default '',
  `comments` varchar(150) default '',
  `nextRun` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `status` enum('enabled','disabled') default 'enabled',
  `freqMinutes` int(2) default '0',
  `dayOfWeek` varchar(3) default '',
  `hourOfDay` varchar(2) default '',
  `lastExecTime` int(10) default '0',
  `isRunning` tinyint(1) default '0',
  `lastStart` datetime default NULL,
  `lastItemTime` datetime default NULL,
  `failureNoticeSent` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

insert into `cronJobs` values('19','updateCache','Update cached content','2009-12-28 20:30:23','disabled','15','','','0','0',null,null,'0'),
 ('20','calcTeamLeaders','Update team leaders','2009-12-28 20:30:23','enabled','60','','','0','0',null,null,'0'),
 ('21','fetchFeeds','Fetch blog feeds locally','2009-12-28 20:30:23','enabled','15','','','0','0',null,null,'0'),
 ('22','cleanup','Cleanup unused tables','2009-12-28 20:30:23','enabled','1440','','','0','0',null,null,'0'),
 ('23','updateTwitter','Update top stories in Twitter','2009-12-28 20:30:23','enabled','30','','','0','0',null,null,'0'),
 ('24','microBlog','Update microblog','2009-12-28 20:30:23','enabled','10','','','0','0',null,null,'0'),
 ('25','microAccountsSync','Update microblog accounts','2009-12-28 20:30:23','enabled','1440','','','0','0',null,null,'0'),
 ('26','facebookProfileBoxes','Update Facebook profile boxes','2009-12-28 20:30:23','enabled','15','','','0','0',null,null,'0'),
 ('27','facebookMinifeed','Publish facebook minifeed stories','2009-12-28 20:30:23','enabled','15','','','0','0',null,null,'0'),
 ('28','facebookEmailEngine','Send Facebook emails','2009-12-28 20:30:23','enabled','15','','','0','0',null,null,'0'),
 ('29','facebookAllocations','Check Facebook allocations nightly','2009-12-28 20:30:23','enabled','1440','','','0','0',null,null,'0'),
 ('30','updateUserLevels','Update userLevel fields to match their points','2009-12-28 20:30:23','enabled','60','','','0','0',null,null,'0'),
 ('31','logHourlyStats','Update hourly statistics in log','2009-12-28 20:30:23','enabled','60','','','0','0',null,null,'0'),
 ('32','updateCachedPointsAndChallenges','Recalculate cached user point and challenge totals for internal consistency','2009-12-28 20:30:23','enabled','180','','','0','0',null,null,'0'),
 ('33','updateSiteChallenges','Detects and awards challenges for site internal functions that cant be detected as they happen','2009-12-28 20:30:23','enabled','180','','','0','0',null,null,'0'),
 ('34','calcWeeklyLeaders','Recalculates all points for all users, stores weekly leaders in WeeklyScores table, run MANUALLY for each week after scoring is finished','2009-12-28 20:30:23','disabled','10080','Mon','02','0','0',null,null,'0'),
 ('35','facebookSendNotifications','Deliver notifications','2009-12-28 20:30:23','enabled','20','','','0','0',null,null,'0'),
 ('36','facebookSendPromos','Send promotions to new users','2009-12-28 20:30:23','enabled','1440','','00','0','0',null,null,'0');

DROP TABLE IF EXISTS `fbSessions`;

CREATE TABLE `fbSessions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` bigint(20) default '0',
  `fbId` bigint(20) default '0',
  `fb_sig_session_key` varchar(255) default '',
  `fb_sig_time` datetime default NULL,
  `fb_sig_expires` datetime default NULL,
  `fb_sig_profile_update_time` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS = 1;
