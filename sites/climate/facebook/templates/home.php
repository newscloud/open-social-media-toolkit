<?php
	/* FACEBOOK Module Templates for Home Page*/
	$storyItem = '<li>';
	$storyItem .= '<div class="thumb">{storyImage}</div>';
	$storyItem .= '<div class="storyBlockWrap">';
	$storyItem .= '<p class="storyHead"><a href="?p=read&o=comments&cid={siteContentId}&record" onclick="readStory({siteContentId});return false;">{title}</a></p>';
	$storyItem .= '<p class="storyCaption">{caption}<a class="more_link" href="?p=read&o=comments&cid={siteContentId}&record" onclick="readStory({siteContentId});return false;">&hellip;&nbsp;more</a></p>';
	$storyItem .= '<div class="profilePic">{mbrImage}</div>';
	$storyItem .= '<div class="storyBlockMeta">';
	$storyItem .= '<p>Posted by {mbrLink}, {timeSince} ago.</p>';
	$storyItem .= '<p>{cmdVote}'.
'<span class="btn_mid">{cmdComment}</span><span class="btn_right"><a href="#" onclick="return shareStory(this,{siteContentId});">Share it</a></span></p>';	
 $storyItem .= '</div><!-- end storyBlockMeta -->';
	$storyItem .= '</div><!-- end storyBlockWrap -->';
	$storyItem .= '</li>';
	$this->addTemplate('promo', '<div id="introPanel"><p>The editors at <a href="http://www.grist.org" class="more_link" target="_blank">'.SITE_SPONSOR.'.org</a> ensure '.SITE_TITLE.' always serves up the hottest news and action on climate change. Share news, talk up solutions, and get in on the action. Yeah, you.</p></div>');
	$this->addTemplate('storyList','<div class="list_stories clearfix"><ul>{items}</ul></div>');
	$this->addTemplate('storyItem', $storyItem);

?>
