<div class="yui-g">
Update a completed challenge<br /><br />

<form method="post" action="<? echo url_for('street_team', 'update_completed_challenge', $completed_challenge['id']); ?>">
<input type="hidden" name="completed_challenge[id]" value="<?php echo $completed_challenge['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $completed_challenge['id']; ?>" />

<?php require('completed_challenge_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
