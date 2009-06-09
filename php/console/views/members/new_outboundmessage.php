<div class="yui-g">
Create a new outbound message<br /><br />

<form method="post" action="<? echo url_for('members', 'create_outboundmessage'); ?>">

<?php require('outboundmessage_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Create" />

</form>
</div>
