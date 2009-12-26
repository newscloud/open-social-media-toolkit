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
	$this->addTemplateDynamic($dynTemp, 'promo', '<div id="introPanel"><p>'.SITE_TITLE.' is a community site for '.SITE_TOPIC.'. Share news, talk up issues, and get in on the action.</p></div>', '', 'home');
	$this->addTemplateStatic($dynTemp, 'storyList','<div class="list_stories clearfix"><ul>{items}</ul></div>','','home');
	$this->addTemplateStatic($dynTemp,'storyItem', $storyItem, '','home');

?>
