<div class="yui-g">
<h1>Orders</h1>
<p><?php echo link_for('Create a new Order', 'street_team', 'new_order'); ?></p><br /><br />

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
			<td><?php echo link_for($order['prizeid'], 'street_team', 'view_prize', $order['prizeid']); ?></td>
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
			<?php
			$link_list = array(
				array('title' => 'View', 'ctrl' => 'street_team', 'action' => 'view_order', 'id' => $order['id']),
				array('title' => 'Edit', 'ctrl' => 'street_team', 'action' => 'modify_order', 'id' => $order['id']),
    			array('title' => 'remove', 'ctrl' => 'street_team', 'action' => 'destroy_order', 'id' => $order['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
			);
			if (($links = build_link_list($link_list))) {
				echo $links;
			}
			?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no orders currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Order', 'street_team', 'new_order'); ?></p><br /><br />
</div>
