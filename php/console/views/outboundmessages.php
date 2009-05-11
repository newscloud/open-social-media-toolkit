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
			<td><a href="index.php?p=console&group=members&action=view_member&id=<?php echo $outboundmessage['userid']; ?>"><?php echo $outboundmessage['userid']; ?></a></td>			
			<td><?php echo $outboundmessage['userGroup']; ?></td>
			<td><?php echo $outboundmessage['subject'].'<br />'.$outboundmessage['msgBody']; ?></td>
			<td><?php echo $outboundmessage['shortLink']; ?></td>
			<td><?php echo $outboundmessage['status']; ?></td>
			<td><?php echo $outboundmessage['t']; ?></td>
			<td><?php echo $outboundmessage['numUsersReceived']; ?></td>
			<td><?php echo $outboundmessage['numUsersExpected']; ?></td>
			<td>
				<a href="index.php?p=console&group=members&action=view_outboundmessage&id=<? echo $outboundmessage['id']; ?>">View</a> -- 
				<a href="index.php?p=console&group=members&action=modify_outboundmessage&id=<? echo $outboundmessage['id']; ?>">Edit</a> -- 
    			<a href="index.php?p=console&group=members&action=destroy_outboundmessage&id=<?php echo $outboundmessage['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">Remove</a> -- 
    			<a href="index.php?p=console&group=members&action=send_outboundmessage&id=<?php echo $outboundmessage['id'] ?>" onclick="if(!confirm('Are you sure you want to send this message?')) return false">Send</a> 
    			<a href="index.php?p=console&group=members&action=send_outboundmessage&id=<?php echo $outboundmessage['id'] ?>&preview=true" onclick="if(!confirm('Are you sure you want to send (preview) this message?')) return false">Send Preview</a> 
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no outbound messages currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=members&action=new_outboundmessage">Create a new Outbound Message</a></p>
</div>
