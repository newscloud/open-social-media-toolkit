<div class="yui-g">
<h1>Order: <? echo $order['id']; ?></h1><br /><br />

<div class="order">

<p>User ID: <? echo $order['userid']; ?></p>
<p>Prize ID: <a href="index.php?p=console&group=street_team&action=view_prize&id=<? echo $order['prizeid']; ?>"><? echo $order['prizeid']; ?></p></a>
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
<p>
<a href="index.php?p=console&group=street_team&action=modify_order&id=<? echo $order['id']; ?>">Edit</a> -- 
<a href="index.php?p=console&group=street_team&action=destroy_order&id=<?php echo $order['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">Remove</a> --
<a href="index.php?p=console&group=street_team&action=orders">Return to Orders</a>
</p>

</div>
