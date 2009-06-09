<?php 
/*
 * This file accepts parameters
 *  $friends - a list of friend arrays
 *    
 * 
 */

if (count($friends) > 0) : ?>

<form method="post" action="<? echo url_for('street_team', 'assign_prize'); ?>&multiple">
 
	<table>
		<tr>
			<th>Week #</th>
			<th>Date Registered</th>
			<th>Userid</th>
			<th>Name</th>
			<th>Email</th>
			<th>Action</th>
		</tr>
		<?php foreach ($friends as $friend):  
			?>
		<tr>
							
			<td><?php echo $friend['week']; ?></td>
			<td><?php echo $friend['dateRegistered']; ?></td>
			<td><?php echo $friend['userid']; ?></td>
			<td><?php echo $friend['name']; ?></td>
			<td><?php echo $friend['email']; ?></td>
			<td>
				<a href="<? echo URL_CANVAS .'?p=profile&memberid='.$friend['fbId']; ?>">View Profile</a> 												    		
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</form>
<?php else: ?>
<h2>No friends currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />

</div>
