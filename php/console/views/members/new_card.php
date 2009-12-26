<div class="yui-g">
Add a new card<br /><br />

<form method="post" action="<? echo url_for('members', 'create_card'); ?>">

<?php require('fields_card.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
