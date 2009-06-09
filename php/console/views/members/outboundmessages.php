<div class="yui-g">
<h1>Outbound Messages</h1>
<br /><br />

<?php if (count($outboundmessages) > 0) : ?>
	<table>
		<tr>
			<th>Message Type</th>
			<th>Created By (User ID)</th>
			<th>User Group</th>			
			<th>Subject</th>			
			<th>Short Link</th>
			<th>Status</th>
			<th>Date Created</th>
			<th>Num Users Notified</th>
			<th>Num Users Expected</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($outboundmessages as $outboundmessage): ?>
		<tr>
			<td><?php echo $outboundmessage['msgType']; ?></td>
			<td><?php echo link_for($outboundmessage['userid'], 'members', 'view_member', $outboundmessage['userid']); ?></td>			
			<td><?php echo $outboundmessage['userGroup']; ?></td>
			<td><?php echo $outboundmessage['subject'].'<br />'.$outboundmessage['msgBody']; ?></td>
			<td><?php echo $outboundmessage['shortLink']; ?></td>
			<td><?php echo $outboundmessage['status']; ?></td>
			<td><?php echo $outboundmessage['t']; ?></td>
			<td><?php echo $outboundmessage['numUsersReceived']; ?></td>
			<td><?php echo $outboundmessage['numUsersExpected']; ?></td>
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'members', 'action' => 'view_outboundmessage', 'id' => $outboundmessage['id']),
					array('title' => 'Edit', 'ctrl' => 'members', 'action' => 'modify_outboundmessage', 'id' => $outboundmessage['id']),
					array('title' => 'Remove', 'ctrl' => 'members', 'action' => 'destroy_outboundmessage', 'id' => $outboundmessage['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
					array('title' => 'Send', 'ctrl' => 'members', 'action' => 'send_outboundmessage', 'id' => $outboundmessage['id'], 'onclick' => "if(!confirm('Are you sure you want to send this message?')) return false"),
    				array('title' => 'Send Preview', 'ctrl' => 'members', 'action' => 'send_outboundmessage', 'id' => $outboundmessage['id'], 'onclick' => "if(!confirm('Are you sure you want to send (preview) this message?')) return false", 'extra_params' => array('preview' => 'true')),
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
<h2>Sorry no outbound messages currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Outbound Message', 'members', 'new_outboundmessage'); ?></p>
</div>
