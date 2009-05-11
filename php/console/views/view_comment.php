<div class="yui-g">
<div class="comment">
<h2>Comment Details</h2>

<p id="comment[postedByName]">Posted By: <a href="index.php?p=console&group=members&action=view_member&id=<? echo htmlentities($comment['userid']); ?>"><? echo htmlentities($comment['postedByName']); ?></a></p>
<div class="spacer"></div>

<p id="comment[date]">Date Posted: <?php echo htmlentities($comment['date']); ?></p>
<div class="spacer"></div>

<p id="comment[siteContentId]">Site Content ID: <a href="index.php?p=console&group=stories&action=view_story&id=<?php echo htmlentities($comment['siteContentId']); ?>"><?php echo htmlentities($comment['siteContentId']); ?></a></p>
<div class="spacer"></div>

<p id="comment[contentid]">Content ID: <?php echo htmlentities($comment['contentid']); ?></p>
<div class="spacer"></div>

<p id="comment[comments]">Comment: <?php echo htmlentities(strip_tags($comment['comments'])); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<ul>
	<li><a href="index.php?p=console&group=stories&action=new_comment">Create a new comment</a></li>
	<li><a href="index.php?p=console&group=stories&action=modify_comment&id=<? echo $comment['siteCommentId']; ?>">Edit this comment</a></li>
	<li><a href="index.php?p=console&group=stories&action=destroy_comment&id=<? echo $comment['siteCommentId']; ?>">Delete this comment</a></li>
    <?php if ($comment['isBlocked'] == 0): ?>
        <li><a href="index.php?p=console&group=stories&action=block_comment&id=<? echo $comment['siteCommentId']; ?>">Block this comment</a></li>
    <?php else: ?>
        <li><a href="index.php?p=console&group=stories&action=unblock_comment&id=<? echo $comment['siteCommentId']; ?>">Unblock this comment</a></li>
    <?php endif; ?>
	<li><a href="index.php?p=console&group=stories&action=comments">Back to Comments</a></li>
</ul>
</div>
