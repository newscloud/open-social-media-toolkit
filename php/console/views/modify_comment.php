<div class="yui-g">
Update a comment<br /><br />

<form method="post" action="index.php?p=console&group=stories&action=update_comment">
<input type="hidden" name="comment[id]" value="<?php echo $comment['siteCommentId']; ?>" />

<?php require('comment_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
