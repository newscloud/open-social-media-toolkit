<div class="yui-g">
<h1>Challenges</h1>
<p><?php echo link_for('Create a new Challenge', 'street_team', 'new_challenge'); ?></p><br /><br />

<?php if (count($challenges) > 0) : ?>
	<table>
		<tr>
			<th>Thumbnail</th>
			<th>Title</th>
			<th>Short Name</th>
			<th>Initial Completions</th>
			<th>Remaining Completions</th>
			<th>Max User Completions</th>
			<th>Max User Completions Per Day</th>
			<th>Point Value</th>
			<th>Type</th>
			<th>Status</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>isFeatured</th>
			<th>Requires</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($challenges as $challenge): ?>
		<tr>
			<td><img src="<?php echo URL_THUMBNAILS .'/'.$challenge['thumbnail']; ?>" width="100" /></td>
			<td><?php echo $challenge['title']; ?></td>
			<td><?php echo $challenge['shortName']; ?></td>
			<td><?php echo $challenge['initialCompletions']; ?></td>
			<td><?php echo $challenge['remainingCompletions']; ?></td>
			<td><?php echo $challenge['maxUserCompletions']; ?></td>
			<td><?php echo $challenge['maxUserCompletionsPerDay']; ?></td>
			<td><?php echo $challenge['pointValue']; ?></td>
			<td><?php echo $challenge['type']; ?></td>
			<td><?php echo $challenge['status']; ?></td>
			<td><?php echo $challenge['dateStart']; ?></td>
			<td><?php echo $challenge['dateEnd']; ?></td>
			<td><?php echo $challenge['isFeatured']; ?></td>
			<td><?php echo $challenge['requires']; ?></td>
			<td>
			<?php
			$link_list = array(
				array('title' => 'View', 'ctrl' => 'street_team', 'action' => 'view_challenge', 'id' => $challenge['id']),
				array('title' => 'Edit', 'ctrl' => 'street_team', 'action' => 'modify_challenge', 'id' => $challenge['id']),
    			array('title' => 'remove', 'ctrl' => 'street_team', 'action' => 'destroy_challenge', 'id' => $challenge['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no challenges currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Challenge', 'street_team', 'new_challenge'); ?></p><br /><br />
</div>
