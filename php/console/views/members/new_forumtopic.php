<div class="yui-g">
Create a new forum topic<br /><br />

<form method="post" action="<? echo url_for('members', 'create_forumtopic'); ?>">

<?php require('fields_forumtopic.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
