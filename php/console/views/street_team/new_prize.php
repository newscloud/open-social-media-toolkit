<div class="yui-g">
Add a new prize<br /><br />

<form method="post" enctype="multipart/form-data" action="<? echo url_for('street_team', 'create_prize'); ?>">

<?php require('prize_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
