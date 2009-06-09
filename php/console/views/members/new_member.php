<div class="yui-g">
Add a new member<br /><br />

<form method="post" action="<? echo url_for('members', 'create_member'); ?>">

<?php require('member_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
