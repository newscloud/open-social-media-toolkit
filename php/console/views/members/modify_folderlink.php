<div class="yui-g">
Update a folder<br /><br />

<form method="post" action="<? echo url_for('members', 'update_folderlink', $link['id']); ?>">
<input type="hidden" name="folder[id]" value="<?php echo $link['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $link['id']; ?>" />

<?php require('fields_folderlink.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
