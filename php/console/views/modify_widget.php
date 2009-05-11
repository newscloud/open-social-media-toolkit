<div class="yui-g">
Update a story<br /><br />

<form method="post" action="index.php?p=console&group=stories&action=update_widget">
<input type="hidden" name="widget[id]" value="<?php echo $widget['id']; ?>" />

<?php require('widget_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
