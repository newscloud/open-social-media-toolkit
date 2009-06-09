<div class="yui-g">
Update a member<br /><br />

<form method="post" action="<? echo url_for('members', 'update_member', $member['userid']); ?>&foo">
<input type="hidden" name="member[id]" value="<?php echo $member['userid']; ?>" />
<input type="hidden" name="id" value="<?php echo $member['userid']; ?>" />

<?php require('member_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
