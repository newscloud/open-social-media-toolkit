<?php
// template definitions for micro module
$category = 'micro';
$this->addTemplateDynamic($dynTemp,'microIntro', '<h1>Recently Tweeted in '.SITE_TOPIC.'</h1><p>Here is what our selection of '.SITE_TOPIC.' twitterers are saying right now.</p>','The intro paragraph on the MicroBlog page',$category);
$this->addTemplateDynamic($dynTemp,'microShareTitle', '<p>A summary of Twitterers for '.SITE_TOPIC.'</p>','Title in link for Micro page',$category);
$this->addTemplateDynamic($dynTemp,'microShareCaption', '<p>'.SITE_TITLE.' makes it easy to find the most relevant Tweeters for Seattle all in one place!</p>','Caption in link for Micro page',$category);

$this->addTemplateDynamic($dynTemp,'microList', '<div class="list_stories clearfix"><ul>{items}</ul></div>','',$category);
	
	$microItem = '<li class="microPostWrap">';
	$microItem .= '<div class="thumb"><a href="http://twitter.com/{shortName}" target="twitter"><img src="{profile_image_url}" alt="photo of {shortName}"></a></div>';
	$microItem .= '<div class="storyBlockWrap">';
	$microItem .= '<p class="microHead"><strong><a href="http://twitter.com/{shortName}" target="twitter">{shortName}</a></strong> {post}</p>';
	$microItem .= '<div class="storyBlockMeta">';
	$microItem .= '<p>Posted in {tag}, {timeSince} ago</p>';
	$microItem .= '<span class="storyCommands"><table cellspacing="0"><tbody><tr>';
	$microItem .= '<td>{cmdReply}</td><td>{cmdRetweet}</td><td>{cmdDM}</td><td>{cmdShare}</td>';
	$microItem .= '</tr></tbody></table></span>';
 	$microItem .= '</div><!-- end storyBlockMeta -->';
	$microItem .= '</div><!-- end storyBlockWrap -->';
	$microItem .= '</li>';
	$this->addTemplateDynamic($dynTemp,'microItem', $microItem,'Item template for Microblog items',$category);

	$microItem = '<li class="microPostWrap">';
	$microItem .= '<div class="thumb"><a href="?p=tweets&o=view&id={id}" onclick="switchPage(\'micro\',\'view\',{id}); return false;" target="twitter"><img src="{profile_image_url}" alt="photo of {shortName}"></a></div>';
	$microItem .= '<div class="storyBlockWrap">';
	$microItem .= '<p class="microHead"><strong><a href="?p=tweets&o=view&id={id}" onclick="switchPage(\'micro\',\'view\',{id}); return false;" target="twitter">{shortName}</a></strong> {post}</p>';
	$microItem .= '<div class="storyBlockMeta">';
	$microItem .= '<p>Posted in {tag}, {timeSince} ago</p>';
 	$microItem .= '</div><!-- end storyBlockMeta -->';
	$microItem .= '</div><!-- end storyBlockWrap -->';
	$microItem .= '</li>';
	$this->addTemplateDynamic($dynTemp,'microItemHome', $microItem,'Item template for Microblog items on the home page',$category);
	
?>