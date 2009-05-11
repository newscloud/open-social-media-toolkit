<div class="yui-g">
Update a video<br /><br />

<form method="post" action="index.php?p=console&group=stories&action=update_video">
<input type="hidden" name="video[id]" value="<?php echo $video['id']; ?>" />

<?php require('video_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
