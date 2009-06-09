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
			<td><?php echo link_for($member['name'], 'members', 'view_member', $member['userid']); ?></td>			
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
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'members', 'action' => 'view_member', 'id' => $member['userid']),
					array('title' => 'Edit', 'ctrl' => 'members', 'action' => 'modify_member', 'id' => $member['userid']),
					array('title' => 'Remove', 'ctrl' => 'members', 'action' => 'destroy_member', 'id' => $member['userid'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
				);
				if ($member['isBlocked'] == 1) {
    				$link_list[] = array('title' => 'Unblock', 'ctrl' => 'members', 'action' => 'unblock_member', 'id' => $member['userid'], 'onclick' => "if(!confirm('Are you sure you want to unblock this item?')) return false");
				} else {
    				$link_list[] = array('title' => 'Block', 'ctrl' => 'members', 'action' => 'block_member', 'id' => $member['userid'], 'onclick' => "if(!confirm('Are you sure you want to block this item?')) return false");
				}
				if (($links = build_link_list($link_list))) {
					echo $links;
				}
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no members currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Member', 'members', 'new_member') ?></p>
</div>
