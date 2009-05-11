<div class="yui-g">
<!-- <h1>Completed Challenges</h1>
<p><a href="index.php?p=console&group=street_team&action=new_completed_challenge">Create a new Completed Challenge</a></p><br /><br />
-->
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
				<td><a href="index.php?p=console&group=members&action=view_member&id=<?php echo $completed_challenge['userid']; ?>"><?php echo $completed_challenge['userid']; ?></a></td>
				<td><a href="index.php?p=console&group=street_team&action=view_challenge&id=<?php echo $completed_challenge['challengeid']; ?>"><?php echo $completed_challenge['challengeid']; ?></a></td>
				<td><?php echo $completed_challenge['dateSubmitted']; ?></td>
				<td><?php echo $completed_challenge['dateAwarded']; ?></td>
				<td><?php echo $completed_challenge['evidence']; ?></td>
				<td><?php echo $completed_challenge['comments']; ?></td>			
				<td><?php echo $completed_challenge['status']; ?></td>
				<td><?php echo $completed_challenge['pointsAwarded']; ?></td>
				<td>
					<a href="index.php?p=console&group=street_team&action=view_completed_challenge&id=<? echo $completed_challenge['id']; ?>">Review</a> -- 
					<a href="index.php?p=console&group=street_team&action=modify_completed_challenge&id=<? echo $completed_challenge['id']; ?>">Edit</a> -- 
	    			<a href="index.php?p=console&group=street_team&action=destroy_completed_challenge&id=<?php echo $completed_challenge['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">remove</a>
				</td>
			</tr>
			<?php endforeach; 
			
			?>
		</table>
<?php else: ?>
<p>Category empty.</p>
<?php endif; endforeach; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=street_team&action=new_completed_challenge">Create a new Completed Challenge</a></p>
</div>
