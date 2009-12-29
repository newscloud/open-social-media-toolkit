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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ContentImages`;

CREATE TABLE `ContentImages` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `url` varchar(255) default '',
  `siteContentId` int(11) unsigned default '0',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `FeaturedWidgets`;

CREATE TABLE `FeaturedWidgets` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `widgetid` int(11) unsigned default NULL,
  `locale` varchar(100) default '',
  `position` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Folders`;

CREATE TABLE `Folders` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `folderid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `title` varchar(50) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `SystemStatus`;

CREATE TABLE `SystemStatus` (
  `id` int(4) unsigned NOT NULL auto_increment,
  `name` varchar(35) default '',
  `strValue` text,
  `numValue` bigint(20) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

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
 ('30','dm_micro.class.php',null,'1262061022');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
