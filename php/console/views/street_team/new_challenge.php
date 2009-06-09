<div class="yui-g">
Add a new challenge<br /><br />

<form method="post" action="<? echo url_for('street_team', 'create_challenge'); ?>">

<?php require('challenge_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
