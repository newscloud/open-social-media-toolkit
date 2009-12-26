<div class="yui-g">
Update a folder<br /><br />

<form method="post" action="<? echo url_for('members', 'update_folder', $folder['id']); ?>">
<input type="hidden" name="folder[id]" value="<?php echo $folder['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $folder['id']; ?>" />

<?php require('fields_folder.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
