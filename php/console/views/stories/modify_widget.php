<div class="yui-g">
Update a story<br /><br />

<form method="post" action="<? echo url_for('stories', 'update_widget', $widget['id']); ?>">
<input type="hidden" name="widget[id]" value="<?php echo $widget['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $widget['id']; ?>" />

<?php require('widget_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
