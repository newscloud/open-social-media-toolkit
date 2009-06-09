<div class="yui-g">
Add a new comment<br /><br />

<form method="post" action="<? echo url_for('stories', 'create_comment'); ?>">

<?php require('comment_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
