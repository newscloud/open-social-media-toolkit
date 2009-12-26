<?php
// template definitions for ideas module
$category = 'ideas';
$this->addTemplateDynamic($dynTemp,'ideasIntroAdd', '<h1>Browse Other Ideas</h1><p>Like an idea to increase its popularity.</p>','The intro paragraph on the add an idea page',$category);

$this->addTemplateDynamic($dynTemp,'ideasTagList', '<div ><ul id="tag_cloud">{items}</ul></div>','',$category);
$this->addTemplateDynamic($dynTemp,'ideasTagItem', '<li><a class="tag4" href="'.URL_CANVAS.'?p=ideas&o=browse&tagid={id}">{tag}</a> </li>','',$category); // trailing space required
$this->addTemplateDynamic($dynTemp,'ideaList', '<div class="list_stories clearfix"><ul>{items}</ul></div>','',$category);
$this->addTemplateDynamic($dynTemp,'ideaItem', '<li><div class="profilePic">{mbrImage}</div><a href="'.URL_CANVAS.'?p=ideas&o=view&id={id}">{idea}</a></li>','',$category);
$this->addTemplateDynamic($dynTemp,'ideaItemNoPic', '<li><a href="'.URL_CANVAS.'?p=ideas&o=view&id={id}">{idea}</a></li>','',$category);

	$ideaItem = '<li>';
	$ideaItem .= '<div class="storyBlockWrap">';
	$ideaItem .= '<div class="profilePicLarger">{mbrImage}</div>';
	$ideaItem .= '<p class="storyHead"><a href="'.URL_CANVAS.'?p=ideas&o=view&id={id}">{idea}</a></p>';
	$ideaItem .= '<p class="storyCaption">{details}</p>';
	$ideaItem .= '<div class="storyBlockMeta">';
	$ideaItem .= '<p>Asked by {mbrLink} in {category}, {timeSince} ago.</p>';
	$ideaItem .= '<p>{cmdLike}<span class="btn_right"><a href="#" onclick="ideaShare({id});return false;">Share it</a></span></p>';
 	$ideaItem .= '</div><!-- end storyBlockMeta -->';
	$ideaItem .= '</div><!-- end storyBlockWrap -->';
	$ideaItem .= '</li>';
	$this->addTemplateDynamic($dynTemp,'ideaItemDetail', $ideaItem,'',$category);
	
	$ideaItem = '<li class="askQuestionWrap">';
	$ideaItem .= '<div class="storyBlockWrap">';
	$ideaItem .= '<p class="askQuestionHead"><a href="'.URL_CANVAS.'?p=ideas&o=view&id={id}">{idea}</a></p>';
	$ideaItem .= '<div class="profilePic">{mbrImage}</div>';
	$ideaItem .= '<div class="storyBlockMeta">';
	$ideaItem .= '<p>Asked by {mbrLink} in {category}, {timeSince} ago.</p>';
	$ideaItem .= '<p>{cmdLike}<span class="btn_mid">{cmdComment}</span><span class="btn_right"><a href="'.URL_CANVAS.'?p=ideas&o=view&id={id}&share">Share</a></span></p>';  
 	$ideaItem .= '</div><!-- end storyBlockMeta -->';
	$ideaItem .= '</div><!-- end storyBlockWrap -->';
	$ideaItem .= '</li>';
	$this->addTemplateDynamic($dynTemp,'ideaItemMedium', $ideaItem,'',$category);
	
	// to do - place in common
	$this->addTemplateDynamic($dynTemp,'ideaNewsItem','<li><a href="'.URL_CANVAS.'?p=read&cid={siteContentId}" onclick="readStory({siteContentId});return false;">{title}</a></li>','display headlines of related news stories',$category);

?>