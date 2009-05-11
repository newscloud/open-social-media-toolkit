<div class="yui-g">
Update an outbound message<br /><br />

<form method="post" action="index.php?p=console&group=members&action=update_outboundmessage">
<input type="hidden" name="outboundmessage[id]" value="<?php echo $outboundmessage['id']; ?>" />

<?php require('outboundmessage_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
