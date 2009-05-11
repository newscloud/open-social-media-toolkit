<div class="yui-g">
<h1>Members</h1>
<br /><br />

<?php if (count($members) > 0) : ?>
	<table>
		<tr>
			<th>Name</th>
			<th>Email</th>
			<th>Vote Power</th>			
			<th>Admin?</th>
			<th>Member?</th>
			<th>Moderator?</th>
			<th>Blocked?</th>
			<th>Date Registered</th>
			<th>User Level</th>
			<th>Point Total</th>
			<th>Stories Posted</th>
			<th>Comments Posted</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($members as $member): ?>
		<tr>
			<td><a href="index.php?p=console&group=members&action=view_member&id=<?php echo $member['userid']; ?>"><?php echo $member['name']; ?></a></td>			
			<td><?php echo $member['email']; ?></td>
			<td><?php echo $member['votePower']; ?></td>
			<td><?php echo ($member['isAdmin'] == 1) ? 'Yes' : 'No'; ?></td>
			<td><?php echo ($member['isMember'] == 1) ? 'Yes' : 'No'; ?></td>
			<td><?php echo ($member['isModerator'] == 1) ? 'Yes' : 'No'; ?></td>
			<td><?php echo ($member['isBlocked'] == 1) ? 'Yes' : 'No'; ?></td>
			<td><?php echo $member['dateRegistered']; ?></td>
			<td><?php echo $member['userLevel']; ?></td>
			<td><?php echo $member['cachedPointTotal']; ?></td>
			<td><?php echo $member['cachedStoriesPosted']; ?></td>
			<td><?php echo $member['cachedCommentsPosted']; ?></td>
			<td>
				<a href="index.php?p=console&group=members&action=view_member&id=<? echo $member['userid']; ?>">View</a> -- 
				<a href="index.php?p=console&group=members&action=modify_member&id=<? echo $member['userid']; ?>">Edit</a> -- 
    			<a href="index.php?p=console&group=members&action=destroy_member&id=<?php echo $member['userid'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">Remove</a> -- 
				<?php if ($member['isBlocked'] == 1): ?>
    				<a href="index.php?p=console&group=members&action=unblock_member&id=<?php echo $member['userid'] ?>" onclick="if(!confirm('Are you sure you want to unblock this item?')) return false">Unblock</a>
				<?php else: ?>
    				<a href="index.php?p=console&group=members&action=block_member&id=<?php echo $member['userid'] ?>" onclick="if(!confirm('Are you sure you want to block this item?')) return false">Block</a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no members currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=members&action=new_member">Create a new Member</a></p>
</div>
