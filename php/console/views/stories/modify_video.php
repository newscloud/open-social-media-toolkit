<div class="yui-g">
Update a video<br /><br />

<form method="post" action="<? echo url_for('stories', 'update_video', $video['id']); ?>">
<input type="hidden" name="video[id]" value="<?php echo $video['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $video['id']; ?>" />

<?php require('video_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
