<div class="yui-g">
Update a comment<br /><br />

<form method="post" action="<? echo url_for('stories', 'update_comment', $comment['siteCommentId']); ?>">
<input type="hidden" name="comment[id]" value="<?php echo $comment['siteCommentId']; ?>" />
<input type="hidden" name="id" value="<?php echo $comment['siteCommentId']; ?>" />

<?php require('comment_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
