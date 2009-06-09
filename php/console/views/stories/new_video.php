<div class="yui-g">
Add a new video<br /><br />

<form method="post" action="<? echo url_for('stories', 'create_video'); ?>">

<?php require('video_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
