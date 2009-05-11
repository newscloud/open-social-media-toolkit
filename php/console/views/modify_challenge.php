<div class="yui-g">
Update a challenge<br /><br />

<form method="post" enctype="multipart/form-data"  
	action="index.php?p=console&group=street_team&action=update_challenge">
<input type="hidden" name="challenge[id]" value="<?php echo $challenge['id']; ?>" />

<?php require('challenge_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
