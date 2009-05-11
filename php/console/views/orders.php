<div class="yui-g">
<h1>Orders</h1>
<p><a href="index.php?p=console&group=street_team&action=new_order">Create a new Order</a></p><br /><br />

<?php if (count($orders) > 0) : ?>
	<table>
		<tr>
			<th>User ID</th>
			<th>Prize ID</th>
			<th>Point Cost</th>
			<th>Date Submitted</th>
			<th>Date Approved</th>
			<th>Date Shipped</th>
			<th>Date Canceled</th>
			<th>Date Refunded</th>
			<th>Reviewed By</th>
			<th>Status</th>
			<th>Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Address 1</th>
			<th>Address 2</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($orders as $order): ?>
		<tr>
			<td><?php echo $order['userid']; ?></td>
			<td><a href="index.php?p=console&group=street_team&action=view_prize&id=<?php echo $order['prizeid']; ?>"><?php echo $order['prizeid']; ?></a></td>
			<td><?php echo $order['pointCost']; ?></td>
			<td><?php echo $order['dateSubmitted']; ?></td>
			<td><?php echo $order['dateApproved']; ?></td>
			<td><?php echo $order['dateShipped']; ?></td>
			<td><?php echo $order['dateCanceled']; ?></td>
			<td><?php echo $order['dateRefunded']; ?></td>
			<td><?php echo $order['reviewedBy']; ?></td>
			<td><?php echo $order['status']; ?></td>
			<td><?php echo $order['name']; ?></td>
			<td><?php echo $order['email']; ?></td>
			<td><?php echo $order['phone']; ?></td>
			<td><?php echo $order['address1']; ?></td>
			<td><?php echo $order['address2']; ?></td>
			<td>
				<a href="index.php?p=console&group=street_team&action=view_order&id=<? echo $order['id']; ?>">View</a> -- 
				<a href="index.php?p=console&group=street_team&action=modify_order&id=<? echo $order['id']; ?>">Edit</a> -- 
    			<a href="index.php?p=console&group=street_team&action=destroy_order&id=<?php echo $order['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">remove</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no orders currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=street_team&action=new_order">Create a new Order</a></p>
</div>
