<div class="yui-g">
Add a new feed<br /><br />

<form method="post" action="<? echo url_for('stories', 'create_feed'); ?>">

<?php require('fields_feed.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
