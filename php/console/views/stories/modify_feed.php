<div class="yui-g">
Update a feed<br /><br />

<form method="post" action="<? echo url_for('stories', 'update_feed', $feed['id']); ?>">
<input type="hidden" name="feed[id]" value="<?php echo $feed['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $feed['id']; ?>" />

<?php require('fields_feed.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
