<div class="yui-g">
<h1>Order: <? echo $order['id']; ?></h1><br /><br />

<div class="order">

<p>User ID: <? echo $order['userid']; ?></p>
<p>Prize ID: <?php echo link_for($order['prizeid'], 'street_team', 'view_prize', $order['prizeid']); ?></p>
<p>Point Cost: <? echo $order['pointCost']; ?></p>
<p>Date Submitted: <? echo $order['dateSubmitted']; ?></p>
<p>Date Approved: <? echo $order['dateApproved']; ?></p>
<p>Date Shipped: <? echo $order['dateShipped']; ?></p>
<p>Date Canceled: <? echo $order['dateCanceled']; ?></p>
<p>Date Refunded: <? echo $order['dateRefunded']; ?></p>
<p>Reviewed By: <? echo $order['reviewedBy']; ?></p>
<p>Status: <? echo $order['status']; ?></p>
<p>Name: <? echo $order['name']; ?></p>
<p>Email: <? echo $order['email']; ?></p>
<p>Phone: <? echo $order['phone']; ?></p>
<p>Address 1: <? echo $order['address1']; ?></p>
<p>Address 2: <? echo $order['address2']; ?></p>
</div>

<br /><br />
<?php
$link_list = array(
array('title' => 'Edit', 'ctrl' => 'street_team', 'action' => 'modify_order', 'id' => $order['id']),
array('title' => 'Remove', 'ctrl' => 'street_team', 'action' => 'destroy_order', 'id' => $order['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
array('title' => 'Return to Orders', 'ctrl' => 'street_team', 'action' => 'orders')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>

</div>
