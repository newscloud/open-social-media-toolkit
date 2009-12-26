<?php
// template definitions for stuff module
$category = 'stuff';

// tag cloud
$this->addTemplateDynamic($dynTemp,'stuffTagList', '<div ><ul id="tag_cloud">{items}</ul></div>','',$category);
$this->addTemplateDynamic($dynTemp,'stuffTagItem', '<li> &nbsp;<a class="tag4" href="?p=things&o=search&tagid={id}">{tag}</a>&nbsp; </li>','',$category); // trailing space required

// stuff lists
$this->addTemplateDynamic($dynTemp,'stuffList', '<div class="list_stories clearfix"><ul>{items}</ul></div>','',$category);
$this->addTemplateDynamic($dynTemp,'stuffItem', '<li><div class="profilePic">{mbrImage}</div><a href="?p=things&o=view&id={id}">{title}</a></li>','',$category);
$this->addTemplateDynamic($dynTemp,'stuffItemNoPic', '<li><a href="?p=things&o=view&id={id}">{title}</a></li>','',$category);
$this->addTemplateDynamic($dynTemp,'relatedStuffItem', '<li><a href="?p=things&o=view&id={id}">{title}</a> <strong><a href="#" onclick="copyItemDetails({id});return false;">Copy</a></strong></li>','list of items yours may be similar too',$category);
$stuffItem = '<li class="stuffWrap">';
$stuffItem .= '<div class="storyBlockWrap">';
$stuffItem .= '<div class="itemThumb"><a href="?p=things&o=view&id={id}">{itemImage}</a></div>';
$stuffItem .= '<p class="storyHead"><a href="?p=things&o=view&id={id}">{title}</a></p>';
$stuffItem .= '<div class="profilePic">{mbrImage}</div>';
$stuffItem .= '<div class="storyBlockMeta">';
$stuffItem .= '<p>Offered by {mbrLink} in {category}, {timeSince} ago.</p>';
$stuffItem .= '<p>{cmdLike}<span class="btn_mid">{cmdComment}</span><span class="btn_right">{cmdContact}</span></p>'; //<span class="btn_right"><a href="#" onclick="return stuffShareQuestion({id});">Share it</a></span>;  
$stuffItem .= '</div><!-- end storyBlockMeta -->';
$stuffItem .= '</div><!-- end storyBlockWrap -->';
$stuffItem .= '</li>';
$this->addTemplateDynamic($dynTemp,'stuffItemMedium', $stuffItem,'',$category);

$stuffItem = '<li>';
$stuffItem .= '<div class="storyBlockWrap">';
$stuffItem .= '<div class="itemThumb"><a href="{linkLearnMore}" target="_aws">{itemImage}</a></div>';
$stuffItem .= '<p class="storyHead"><a href="{linkLearnMore}" target="_aws">{title}</a></p>';
$stuffItem .= '<p class="storyCaption">{caption}</p>';
$stuffItem .= '<div class="profilePic">{mbrImage}</div>';
$stuffItem .= '<div class="storyBlockMeta">';
$stuffItem .= '<p>Offered by {mbrLink} in {category}, {timeSince} ago.</p>';
$stuffItem .= '<p>{cmdLike}<span class="btn_right">{cmdContact}</span>{cmdLearnMore}</p>';
$stuffItem .= '</div><!-- end storyBlockMeta -->';
$stuffItem .= '</div><!-- end storyBlockWrap -->';
$stuffItem .= '</li>';
$this->addTemplateDynamic($dynTemp,'stuffItemDetail', $stuffItem,'',$category);

/*
	$this->addTemplateDynamic($dynTemp,'stuffIntroAnswer', '<h1>Answer Questions</h1><p>Help out others in '.SITE_TITLE.' community.</p>','The intro paragraph on the main page for answering questions',$category);
		$stuffItem = '<li>';
		$stuffItem .= '<div class="storyBlockWrap">';
		$stuffItem .= '<div class="profilePicLarger">{mbrImage}</div>';
		$stuffItem .= '<p class="storyCaption">{answer}</p>';
		$stuffItem .= '<div class="storyBlockMeta">';
		$stuffItem .= '<p>Answered by {mbrLink}, {timeSince} ago.</p>';
		$stuffItem .= '<p>{cmdLikeAnswer}<span class="btn_right">{cmdCommentsAnswer}</span></p>';	
	 	$stuffItem .= '</div><!-- end storyBlockMeta -->';
		$stuffItem .= '</div><!-- end storyBlockWrap -->';
		$stuffItem .= '</li>';
		$this->addTemplateDynamic($dynTemp,'stuffAnswerItem', $stuffItem,'',$category);
		// // to do - place in common, remove from ideas as well
		$this->addTemplateDynamic($dynTemp,'stuffNewsItem','<li><a href="?p=read&cid={siteContentId}" onclick="readStory({siteContentId});return false;">{title}</a></li>','display headlines of related news stories',$category)
		*/
?>