<div class="yui-g">
<?php 

	$cc_types = array(	'Awaiting Review' => array_filter($completed_challenges, 'awaitingReview'),
		'Reviewed/Automatic' => array_filter($completed_challenges, 'isReviewed'));

	foreach ($cc_types as $cctitle => $cclist):
		echo '<h2>'.$cctitle.'</h2>';
		if (count($cclist) > 0) : ?>
		<table>
			<tr>
				<th>User ID</th>
				<th>Challenge ID</th>
				<th>Date Submitted</th>
				<th>Date Awarded</th>
				<th>Evidence</th>
				<th>Comments</th>
				
				<th>Status</th>
				<th>Points Awarded</th>
				<th>Actions</th>
			</tr>
			<?php 
			
			foreach ($cclist as $completed_challenge): ?>
			<tr>
				<td><?php echo link_for($completed_challenge['userid'], 'members', 'view_member', $completed_challenge['userid']); ?></td>
				<td><?php echo link_for($completed_challenge['challengeid'], 'street_team', 'view_challenge', $completed_challenge['challengeid']); ?></td>
				<td><?php echo $completed_challenge['dateSubmitted']; ?></td>
				<td><?php echo $completed_challenge['dateAwarded']; ?></td>
				<td><?php echo $completed_challenge['evidence']; ?></td>
				<td><?php echo $completed_challenge['comments']; ?></td>			
				<td><?php echo $completed_challenge['status']; ?></td>
				<td><?php echo $completed_challenge['pointsAwarded']; ?></td>
				<td>
				<?php
				$link_list = array(
					array('title' => 'Review', 'ctrl' => 'street_team', 'action' => 'view_completed_challenge', 'id' => $completed_challenge['id']),
					array('title' => 'Edit', 'ctrl' => 'street_team', 'action' => 'modify_completed_challenge', 'id' => $completed_challenge['id']),
	    			array('title' => 'remove', 'ctrl' => 'street_team', 'action' => 'destroy_completed_challenge', 'id' => $completed_challenge['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
				);
				if (($links = build_link_list($link_list))) {
					echo $links;
				}
				?>
				</td>
			</tr>
			<?php endforeach; 
			
			?>
		</table>
<?php else: ?>
<p>Category empty.</p>
<?php endif; endforeach; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Challenge', 'street_team', 'new_completed_challenge'); ?></p><br /><br />
</div>
