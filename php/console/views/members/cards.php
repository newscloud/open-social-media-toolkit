<div class="yui-g">
<h1>Manage Cards</h1>
<br />
<?php if (count($cards) > 0) : ?>
	<table>
		<tr>
			<th>id</th>
			<th>Name</th>
			<th>Commands</th>			
		</tr>
		<?php foreach ($cards as $card): ?>
		<tr>
			<td><?php echo $card['id']; ?></td>
			<td><?php echo link_for($card['name'], 'members', 'view_card', $card['id']); ?></td>			
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'members', 'action' => 'view_card', 'id' => $card['id']),
					array('title' => 'Edit', 'ctrl' => 'members', 'action' => 'modify_card', 'id' => $card['id']),
					array('title' => 'Remove', 'ctrl' => 'members', 'action' => 'destroy_card', 'id' => $card['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no cards currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new card', 'members', 'new_card'); ?></p>
</div>
<?php echo var_dump($cards); ?>