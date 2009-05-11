<div class="yui-g">
<h1>Leaders</h1>
<p>Week Selector would go here...</p><br /><br />


<?php 
/*
 * This file accepts parameters
 *  $leaders - a list of leader arrays
 *  $prefix - a category prefix to further differentiate button groups, e.g. 'weekly' or 'alltime'  
 * 
 */

if (count($leaders) > 0) : ?>

<form method="post" action="index.php?p=console&group=street_team&action=assign_prize&multiple">
 
	<table>
		<tr>
			<th colspan=2>+/-<br>-like</th>
			<th></th>
			<th>Week #</th>
			<th>Week Containing</th>
			<th>User</th>
			<th>Points Earned</th>
			<th>Eligibility</th>
			<th>Action</th>
		</tr>
		<?php foreach ($leaders as $leader):  
			$localCategory = $prefix.'_'.$leader['week'].'_'.$leader['eligibility']?>
		<tr>
			<td><input type="button" value="+" onclick="selectLike('<?php echo $localCategory; ?>',true);return false;" /></td>
			<td><input type="button" value="-" onclick="selectLike('<?php echo $localCategory; ?>',false);return false;" /></td>	
					
					
			<td>
				<input type="checkbox" 
					class="<?php echo $localCategory?>" 
					id="check_<?php echo $localCategory.'_'.$leader['userid']; ?>" 
					name="check_<?php echo $localCategory.'_'.$leader['userid']; ?>" 
					value="<?php echo $leader['userid']; ?>" /></td>
			
		
			<td><?php echo $leader['weekNum']; ?></td>
			<td><?php echo $leader['week']; ?></td>

			<td><?php echo $leader['name']; ?></td>
			<td><?php echo $leader['pointTotal']; ?></td>
			<td><?php echo $leader['eligibility']; ?></td>
			<td>
				<a href="<? echo URL_CANVAS .'?p=profile&memberid='.$leader['fbId']; ?>">View Profile</a> -- 
									
				<a href="index.php?p=console&group=street_team&action=assign_prize&mode=weekly&id=
					<? echo $leader['userid']; ?>">Award Weekly Prize</a> -- 

				<a href="index.php?p=console&group=street_team&action=assign_prize&mode=grand&id=
					<? echo $leader['userid']; ?>">Award Grand Prize</a> -- 
				
    			
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	Award to selected: :
	<input type="submit" name="mode" value="weekly" />
	<input type="submit" name="mode" value="grand" />
</form>
<?php else: ?>
<h2>Sorry no leaders currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />

</div>
