<div class="yui-g">
Update a member<br /><br />

<form method="post" action="index.php?p=console&group=members&action=update_member">
<input type="hidden" name="member[id]" value="<?php echo $member['userid']; ?>" />

<?php require('member_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
