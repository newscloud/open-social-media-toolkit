<div class="yui-g">
Add a new completed challenge<br /><br />

<form method="post" action="<? echo url_for('street_team', 'create_completed_challenge'); ?>">

<?php require('completed_challenge_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
