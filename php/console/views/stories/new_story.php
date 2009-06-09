<div class="yui-g">
Add a new story<br /><br />

<form method="post" action="<? echo url_for('stories', 'create_story'); ?>">

<?php require('story_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
