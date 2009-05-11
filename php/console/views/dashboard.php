<div class="yui-g">
<h1>Dashboard Page for the Management Console</h1>
<h2>This is your main portal to the functions of the console</h2>
<h2>This page will provide you with an overview of the status of your site.</h2>
<br /><br />

<div class="new_contact_emails">
<h2>New Contact Emails</h2>
<hr />
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
			<a href="index.php?p=console&group=members&action=view_member_email&id=<? echo $email['id']; ?>">View</a>
			</td>
			
		</tr>
	<?php endforeach; ?>
	</table>
	<hr />
<?php else: ?>
	<p>Currently there are no unread contact emails.</p>
<?php endif; ?>
</div>
</div>
