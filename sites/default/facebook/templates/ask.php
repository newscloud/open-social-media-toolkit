<?php
// template definitions for ask module
$category = 'ask';
$this->addTemplateDynamic($dynTemp,'askTagList', '<div ><ul id="tag_cloud">{items}</ul></div>','',$category);
$this->addTemplateDynamic($dynTemp,'askTagItem', '<li><a class="tag4" href="?p=ask&o=browse&tagid={id}">{tag}</a> </li>','',$category); // trailing space required
$this->addTemplateDynamic($dynTemp,'askQuestionList', '<div class="list_stories clearfix"><ul>{items}</ul></div>','',$category);
$this->addTemplateDynamic($dynTemp,'askQuestionItem', '<li><div class="profilePic">{mbrImage}</div><a href="?p=ask&o=question&id={id}">{question}</a></li>','',$category);
$this->addTemplateDynamic($dynTemp,'askQuestionItemNoPic', '<li><a href="?p=ask&o=question&id={id}">{question}</a></li>','',$category);

	$askItem = '<li>';
	$askItem .= '<div class="storyBlockWrap">';
	$askItem .= '<div class="profilePicLarger">{mbrImage}</div>';
	$askItem .= '<p class="storyHead"><a href="?p=ask&o=question&id={id}">{question}</a></p>';
	$askItem .= '<p class="storyCaption">{details}</p>';
	$askItem .= '<div class="storyBlockMeta">';
	$askItem .= '<p>Asked by {mbrLink} in {category}, {timeSince} ago.</p>';
	$askItem .= '<p>{cmdLike}<span class="btn_mid">{showAnswer}</span><span class="btn_right"><a href="#" onclick="askShare({id});return false;">Share</a></span></p>';
 	$askItem .= '</div><!-- end storyBlockMeta -->';
	$askItem .= '</div><!-- end storyBlockWrap -->';
	$askItem .= '</li>';
	$this->addTemplateDynamic($dynTemp,'askQuestionItemDetail', $askItem,'',$category);
	$askItem = '<li class="askQuestionWrap">';
	$askItem .= '<div class="storyBlockWrap">';
	$askItem .= '<p class="askQuestionHead"><a href="?p=ask&o=question&id={id}">{question}</a></p>';
	$askItem .= '<div class="profilePic">{mbrImage}</div>';
	$askItem .= '<div class="storyBlockMeta">';
	$askItem .= '<p>Asked by {mbrLink} in {category}, {timeSince} ago.</p>';
	$askItem .= '<p>{cmdLike}<span class="btn_mid">{cmdAnswer}</span><span class="btn_right"><a href="?p=ask&o=question&id={id}&share">Share</a></span></p>';  
 	$askItem .= '</div><!-- end storyBlockMeta -->';
	$askItem .= '</div><!-- end storyBlockWrap -->';
	$askItem .= '</li>';
	$this->addTemplateDynamic($dynTemp,'askQuestionItemMedium', $askItem,'',$category);
	$this->addTemplateDynamic($dynTemp,'askIntroAnswer', '<h1>Answer Questions</h1><p>Help out others in '.SITE_TITLE.' community.</p>','The intro paragraph on the main page for answering questions',$category);
		$askItem = '<li>';
		$askItem .= '<div class="storyBlockWrap">';
		$askItem .= '<div class="profilePicLarger">{mbrImage}</div>';
		$askItem .= '<p class="storyCaption">{answer}</p>';
		$askItem .= '<div class="storyBlockMeta">';
		$askItem .= '<p>Answered by {mbrLink}, {timeSince} ago.</p>';
		$askItem .= '<p>{cmdLikeAnswer}<span class="btn_right">{cmdCommentsAnswer}</span></p>';	
	 	$askItem .= '</div><!-- end storyBlockMeta -->';
		$askItem .= '</div><!-- end storyBlockWrap -->';
		$askItem .= '</li>';
		$this->addTemplateDynamic($dynTemp,'askAnswerItem', $askItem,'',$category);
		// // to do - place in common, remove from ideas as well
		$this->addTemplateDynamic($dynTemp,'askNewsItem','<li><a href="?p=read&cid={siteContentId}" onclick="readStory({siteContentId});return false;">{title}</a></li>','display headlines of related news stories',$category)
?>