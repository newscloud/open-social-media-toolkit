<div class="yui-g">
<h1>Member Emails</h1>
<div class="new_contact_emails">
<p style="float: right"><?php echo link_for('Clean up emails','members','clean_up_member_emails', null, "if(!confirm('Are you sure you want to remove all member emails?')) return false;"); ?></p>
<div class="clear" style="clear: both;"></div>
	<?php if (count($new_contact_emails) > 0): ?>
		<table>
			<tr>
				<th>From Email</th>
				<th>User ID</th>
				<th>Subject</th>
				<th>Topic</th>
				<th>Read?</th>
				<th>Replied?</th>
				<th>Date</th>
				<th>Actions</th>
			</tr>
		<?php foreach ($new_contact_emails as $email): ?>
			<tr>
				<td><? echo $email['email']; ?></td>
				<td><? echo $email['userid']; ?></td>
				<td><? echo $email['subject']; ?></td>
				<td><? echo $email['topic']; ?></td>
				<td><? echo ($email['is_read']) ? '<span class="read_email" style="color: green;">read</span>' : '<span class="unread_email" style="color: red;">unread</span>'; ?></td>
				<td><? echo ($email['replied']) ? '<span class="replied_email" style="color: green;">replied</span>' : '<span class="unreplied_email" style="color: red;">unreplied</span>'; ?></td>
				<td><? echo $email['date']; ?></td>

				<td>
				<?php echo link_for('View', 'members', 'view_member_email', $email['id']); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no member emails currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
</div>
