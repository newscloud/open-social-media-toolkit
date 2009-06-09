<div class="yui-g">
<div class="comment">
<h2>Comment Details</h2>

<p id="comment[postedByName]">Posted By: <?php echo link_for($comment['postedByName'], 'members', 'view_member', $comment['userid']); ?></p>
<div class="spacer"></div>

<p id="comment[date]">Date Posted: <?php echo htmlentities($comment['date']); ?></p>
<div class="spacer"></div>

<p id="comment[siteContentId]">Site Content ID: <?php echo link_for($comment['siteContentId'], 'stories', 'view_story', $comment['siteContentId']); ?></p>
<div class="spacer"></div>

<p id="comment[contentid]">Content ID: <?php echo htmlentities($comment['contentid']); ?></p>
<div class="spacer"></div>

<p id="comment[comments]">Comment: <?php echo htmlentities(strip_tags($comment['comments'])); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new comment', 'ctrl' => 'stories', 'action' => 'new_comment'),
	array('title' => 'Edit this comment', 'ctrl' => 'stories', 'action' => 'modify_comment', 'id' => $comment['siteCommentId']),
	array('title' => 'Delete this comment', 'ctrl' => 'stories', 'action' => 'destroy_comment', 'id' => $comment['siteCommentId'])
);
    if ($comment['isBlocked'] == 0) {
        $link_list[] = array('title' => 'Block this comment', 'ctrl' => 'stories', 'action' => 'block_comment', 'id' => $comment['siteCommentId']);
    } else {
        $link_list[] = array('title' => 'Unblock this comment', 'ctrl' => 'stories', 'action' => 'unblock_comment', 'id' => $comment['siteCommentId']);
	}
	$link_list[] = array('title' => 'Back to Comments', 'ctrl' => 'stories', 'action' => 'comments');
	if (($links = build_link_list($link_list))) {
		echo $links;
	}
?>
</ul>
</div>
