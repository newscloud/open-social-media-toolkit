<div class="yui-g">
Update a order<br /><br />

<form method="post" action="index.php?p=console&group=street_team&action=update_order">
<input type="hidden" name="order[id]" value="<?php echo $order['id']; ?>" />

<?php require('order_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
