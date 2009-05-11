<div class="yui-g">
Update a story<br /><br />

<form method="post" action="index.php?p=console&group=stories&action=update_story">
<input type="hidden" name="story[id]" value="<?php echo $story['siteContentId']; ?>" />

<?php require('story_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
