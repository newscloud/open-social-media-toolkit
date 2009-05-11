<?php
	/* FACEBOOK Module Templates for Comments Sections*/
	$commentItemStr = '<div class="panel_block"><div class="thumb">{mbrImage}</div>'
	.'<div class="storyBlockWrap"><div class="storyBlockMeta">'
	.'<h3>Posted by {mbrLink}, {timeSince} ago.</h3>'
	.'</div><!-- end storyBlockMeta -->'
	.'<blockquote><div class="quotes">{video}{comments}</div></blockquote>'
	.'</div><!-- end storyBlockWrap --></div><!-- end panel_block -->';
	$referItemStr='<div class="panel_block"><div class="thumb">{mbrImage}</div>'
	.'<div class="storyBlockWrap"><div class="storyBlockMeta">'
	.'<h3>Shared by {mbrLink}, {timeSince} ago.</h3>'
	.'</div><!-- end storyBlockMeta -->'
	.'<blockquote><div class="quotes">{comments}</div></blockquote>'
	.'</div><!-- end storyBlockWrap --></div><!-- end panel_block -->';
	
	// #topOfComments is actually at the bottom of the comment thread, top of the post comment bar
	$this->addTemplate('commentList','<div id="commentThread" class="panel_1"><div class="panelBar clearfix"><h2>Comments</h2></div>{items}</div><a id="topOfComments" name="topOfComments" />');
	$this->addTemplate('noCommentList','<div id="commentThreadEmpty" class="empty_panel_1">{items}</div><a id="topOfComments" name="topOfComments" />');
	$this->addTemplate('commentItem', $commentItemStr);
	$this->addTemplate('referItem', $referItemStr);
	$this->addTemplate('noCommentItem','<p>There are currently no comments. Add a new comment!</p>');

?>
