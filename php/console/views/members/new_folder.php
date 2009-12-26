<div class="yui-g">
Add a new folder<br /><br />

<form method="post" action="<? echo url_for('members', 'create_folder'); ?>">

<?php require('fields_folder.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
