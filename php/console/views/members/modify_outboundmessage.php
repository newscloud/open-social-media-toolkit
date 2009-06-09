<div class="yui-g">
Update an outbound message<br /><br />

<form method="post" action="<? echo url_for('members', 'update_outboundmessage', $outboundmessage['id']); ?>">
<input type="hidden" name="outboundmessage[id]" value="<?php echo $outboundmessage['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $outboundmessage['id']; ?>" />

<?php require('outboundmessage_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
