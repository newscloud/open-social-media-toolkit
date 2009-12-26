<?php
	/* FACEBOOK Module Templates for Read Page*/
	$readStoryItem = '<div id="readStoryList">';
	$readStoryItem .= '<div class="thumb">{storyImage}</div>';
	$readStoryItem .= '<div class="storyBlockWrap">';
	$readStoryItem .= '<p class="storyHead"><a href="{url}" onclick="quickLog(\'extLink\',\'read\',{siteContentId},\'{url}\');" target="_cts">{title}</a></p>';
	$readStoryItem .= '<div class="storyBlockMeta">';
	$readStoryItem .= '<h3>Via <a href="#">{source}</a> on {date}</h3>';
	$readStoryItem .= '<h3>Posted by {mbrLink}, {timeSince} ago.</p>';
	$readStoryItem .= '<span class="storyCommands">';
	$readStoryItem .= '<table cellspacing="0"><tbody><tr>';
	$readStoryItem .= '<td>{cmdVote}</td>';
	$readStoryItem .= '<td class="commentLink"><a href="#topOfComments">Post a comment</a></td>';
	$readStoryItem .= '<td><a href="#" onclick="return shareStory(this,{siteContentId});">Share it</a></td>';
	$readStoryItem .= '</tr></tbody></table></span>';
	$readStoryItem .= '</div><!-- end storyBlockMeta -->';
	$readStoryItem .= '</div><!-- end storyBlockWrap -->';
	$blogStoryItem=$readStoryItem;
	$blogStoryItem .= '</div><!-- end readStoryList -->';
	$this->addTemplate('blogStoryItem', $blogStoryItem);	
	$readStoryItem .= '<p class="storyCaption">{caption}<a href="{url}" target="_cts"> &hellip;&nbsp;more</a></p>';
	$readStoryItem .= '<p class="float_right"><a href="{url}" onclick="quickLog(\'extLink\',\'read\',{siteContentId},\'{url}\');" target="_cts" class="btn_1">Read the full story</a></p>';
	$readStoryItem .= '</div><!-- end readStoryList -->';
	$this->addTemplate('readStoryList','<div id="readStoryList">{items}</div>');
	$this->addTemplate('readStoryItem', $readStoryItem);

	$this->addTemplate('otherStoryList','<div id="otherStoryList">{items}</div>');
	$this->addTemplate('otherStoryItem','<p><a href="?p=read&cid={siteContentId}" onclick="switchPage(\'read\', \'comments\', {siteContentId});return false;">{title}</a></p>');

	$this->addTemplate('otherFriendsList','<div id="otherFriendsList">{items}</div>');
	$this->addTemplate('otherFriendsItem','<div class="friend">{mbrImage}</div>');

?>
