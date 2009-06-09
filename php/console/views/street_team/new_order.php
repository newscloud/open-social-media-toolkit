<div class="yui-g">
Add a new order<br /><br />

<form method="post" action="<? echo url_for('street_team', 'create_order'); ?>">

<?php require('order_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
