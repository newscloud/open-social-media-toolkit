<div class="yui-g">
Update a story<br /><br />

<form method="post" action="<? echo url_for('stories', 'update_story', $story['siteContentId']); ?>">
<input type="hidden" name="story[id]" value="<?php echo $story['siteContentId']; ?>" />
<input type="hidden" name="id" value="<?php echo $story['siteContentId']; ?>" />

<?php require('story_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
