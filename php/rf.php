<?php
	/* initialize database and libraries */
	define ("INIT_COMMON",true);
	define ("INIT_PAGE",true);
	define ("INIT_AJAX",true);
	define ("INIT_SESSION",true);
	include_once ('initialize.php');
	require_once(PATH_CORE.'/classes/content.class.php');
	$cObj=new content($db);
	require_once(PATH_CORE.'/classes/template.class.php');
	$templateObj=new template($db);
	require_once(PATH_CORE.'/classes/utilities.class.php');
	$utilObj=new utilities($db);
	require_once(PATH_PHP.'/classes/comments.class.php');
	$commentsObj=new comments($db);

	/* process request variables */
	if (isset($_GET['permalink']))
		$permalink=$_GET['permalink'];
	else {
		// go to 404 error page
		header("Location: ".URL_ROOT);
		exit;
	}		
	$story=$cObj->getByPermalink($permalink);

	// record story read by this user
/*	if ($db->ui->isLoggedIn) {
		require_once(PATH_CORE.'/classes/log.class.php');
		$logObj=new log(&$db);
		$logItem=$logObj->serialize(0,$db->ui->userid,'readStory',$story->siteContentId); // note this is the local contentid
 		$logObj->update($logItem);
	}	*/
		
	/* begin building the page */
	$page->setTitle($story->title);
	//$page->pkgStyles(CACHE_PREFIX.'nrStory',array(PATH_PHP_STYLES.'/newsroom.css',PATH_PHP_STYLES.'/tabs.css'));
	//$page->pkgScripts(CACHE_PREFIX.'nrStory',array(PATH_PHP_SCRIPTS.'/comments.js',PATH_PHP_SCRIPTS.'/voting.js',PATH_PHP_SCRIPTS.'/journal.js'));					
	$page->addToHeader('');
	$code=buildTop($story->url);
	$page->addToFooter('');
	//$page->addRSSFeed(URL_HOME.'?p=rss');		
/*
	$templateObj->registerTemplates('PHP');	
	$templateObj->db->result=$cObj->getByPermalink($permalink,true);
	$templateObj->db->setTemplateCallback('time_since', array($utilObj, 'time_since'), 'date');
	$templateObj->db->setTemplateCallback('caption', array($templateObj, 'cleanEllipsis'), 'caption');
	$templateObj->db->setTemplateCallback('cmdVote', array($templateObj, 'commandVote'), 'siteContentId');
	$templateObj->db->setTemplateCallback('cmdAdd', array($templateObj, 'commandAdd'), 'siteContentId');
	$templateObj->db->setTemplateCallback('cmdRead', array($templateObj, 'commandRead'), 'permalink');
	$templateObj->db->setTemplateCallback('storyImage', array($templateObj, 'getLargeStoryImage'), 'imageid');	
	$code.=$templateObj->mergeTemplate($templateObj->templates['readStoryContainer'],$templateObj->templates['readStoryContent']);			
	// display comments
	$commentsObj->setupLibraries();
	$code.=$commentsObj->buildComments(true,$story);
	*/
	$page->addToContent($code);
	$page->display();	
	
	function buildTop($url=''){
		$code='	<table id="page_table" class="page_table" cellpadding="0" cellspacing="0">
				<tr>
					<td class="header_cell" id="header_cell">
						<div class="header_content clearfix">
							<div class="UIOneOff_Container">
								<div id="header_info" class="header_info">
									<a href="http://www.facebook.com/IndecisionForever" class="header_thumb_link"><img src="http://profile.ak.fbcdn.net/object3/1620/71/q107020456862_9735.jpg" alt="" class="header_thumb_img" style="height:30px;width:30px;" /></a>
									<div class="header_info_text">
										<div class="header_title_text">
											<a href="http://www.facebook.com/IndecisionForever" class="owner">Comedy Centrals Indecision</a> posted a link. <span class="header_info_timestamp">5 hours ago</span>
										</div>
										<div class="header_comment" id="elink_comment_wrapper">
											<span id="abbrev_comment" class="inline_comment">When the McCain campaign officials think youre... <a onclick="return wait_for_load(this, event, function() { toggleDisplayNone(\'abbrev_comment\',\'full_comment\');FramedPageController.resizeIframe(); return false; });" class="expando">Show More</a></span><span id="full_comment" class="inline_comment" style="display:none">When the McCain campaign officials think youre an idiot, then youre probably a danger to yourself. <a onclick="return wait_for_load(this, event, function() { toggleDisplayNone(\'abbrev_comment\',\'full_comment\');FramedPageController.resizeIframe(); return false; });" class="expando">Show Less</a></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="header_actions">
							<a icon="" href="/posted.php?id=107020456862&amp;share_id=106839083642#s106839083642" onclick="return wait_for_load(this, event, function() { var a = new AsyncRequest(&quot;\/ajax\/share_comments_dialog.php?sid=106839083642&quot;).setMethod(&quot;GET&quot;).setReadOnly(true);new Dialog().setAsync(a).show(); return false; });" class="toolbar_button has_icon"><span class="toolbar_button_label"><span class="toolbar_button_icon" style="background-image:url(http://static.ak.fbcdn.net/images/icons/comments.gif?8:154251);">&nbsp;</span>29 Comments</span></a><a href="/share.php?u=http%3A%2F%2Fwww.indecisionforever.com%2F2009%2F07%2F24%2Fbirther-scandal-did-not-meet-mccain-campaigns-high-standards-of-intelligence%2F" icon="" target="_blank" onclick="return wait_for_load(this, event, function() { return share_internal_config(&quot;s=99&amp;appid=2309869772&amp;p[]=1154274279&amp;p[]=106839083642&quot;); });" class="toolbar_button has_icon"><span class="toolbar_button_label"><span class="toolbar_button_icon" style="background-image:url(http://static.ak.fbcdn.net/images/icons/favicon.gif?8:27651);">&nbsp;</span>Share</span></a>
						</div>
						<div class="url_bar known_url">
							<a title="Remove Frame" class="remove_link" href="http://www.indecisionforever.com/2009/07/24/birther-scandal-did-not-meet-mccain-campaigns-high-standards-of-intelligence/">&nbsp;</a>Viewing: <a title="Remove frame and go to this web address." class="url_fragment" href="http://www.indecisionforever.com/2009/07/24/birther-scandal-did-not-meet-mccain-campaigns-high-standards-of-intelligence/">http://<span class="domain_name">www.indecisionforever.com</span>/20...</a>
						</div>
						<div class="url_bar unknown_url">
							<a title="Remove Frame" class="remove_link" href="http://www.indecisionforever.com/2009/07/24/birther-scandal-did-not-meet-mccain-campaigns-high-standards-of-intelligence/">&nbsp;</a>Return to: <a title="Remove frame and go to this web address." class="url_fragment" href="http://www.indecisionforever.com/2009/07/24/birther-scandal-did-not-meet-mccain-campaigns-high-standards-of-intelligence/">http://<span class="domain_name">www.indecisionforever.com</span>/20...</a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="content_cell" id="content_cell" style="height:100%;">
						<div class="content_cell_inner_shadow" style="width:100%;">
							&nbsp;
						</div><iframe id="content_iframe" class="content_iframe" name="content_iframe"  src="'.$url.'" frameborder="0" scrolling="auto" style="width:100%;"></iframe>
					</td>
				</tr>
			</table>
';
			return $code;
	}
?>