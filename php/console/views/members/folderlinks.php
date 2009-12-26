<div class="yui-g">
<h1>Manage Resource Links</h1>
<br />
<?php if (count($folderlinks) > 0) : ?>
	<table>
		<tr>
			<th>id</th>
			<th>Title</th>
			<th>Commands</th>			
		</tr>
		<?php foreach ($folderlinks as $link): ?>
		<tr>
			<td><?php echo $link['id']; ?></td>
			<td><?php echo link_for($link['title'], 'members', 'view_folderlink', $link['id']); ?></td>			
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'members', 'action' => 'view_folderlink', 'id' => $link['id']),
					array('title' => 'Edit', 'ctrl' => 'members', 'action' => 'modify_folderlink', 'id' => $link['id']),
					array('title' => 'Remove', 'ctrl' => 'members', 'action' => 'destroy_folderlink', 'id' => $link['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no links currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new link', 'members', 'new_folderlink'); ?></p>
</div>
