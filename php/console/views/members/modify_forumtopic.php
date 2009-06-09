<div class="yui-g">
Update a forum topic<br /><br />

<form method="post" action="<? echo url_for('members', 'update_forumtopic', $forumtopic['id']); ?>&foo">
<input type="hidden" name="forumtopic[id]" value="<?php echo $forumtopic['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $forumtopic['id']; ?>" />

<?php require('fields_forumtopic.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
