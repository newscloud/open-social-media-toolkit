<div class="yui-g">
Update a challenge<br /><br />

<form method="post" enctype="multipart/form-data" action="<? echo url_for('street_team', 'update_challenge', $challenge['id']); ?>">
<input type="hidden" name="challenge[id]" value="<?php echo $challenge['id']; ?>" />

<?php require('challenge_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
