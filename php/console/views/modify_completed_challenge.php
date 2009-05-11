<div class="yui-g">
Update a completed challenge<br /><br />

<form method="post" action="index.php?p=console&group=street_team&action=update_completed_challenge">
<input type="hidden" name="completed_challenge[id]" value="<?php echo $completed_challenge['id']; ?>" />

<?php require('completed_challenge_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
