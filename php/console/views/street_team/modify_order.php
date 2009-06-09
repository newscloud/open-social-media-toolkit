<div class="yui-g">
Update a order<br /><br />

<form method="post" action="<? echo url_for('street_team', 'update_order', $order['id']); ?>">
<input type="hidden" name="order[id]" value="<?php echo $order['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $order['id']; ?>" />

<?php require('order_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
