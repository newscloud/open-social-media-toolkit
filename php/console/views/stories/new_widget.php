<div class="yui-g">
Add a new widget<br /><br />

<form method="post" action="<? echo url_for('stories', 'create_widget'); ?>">

<?php require('widget_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
