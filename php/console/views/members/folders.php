<div class="yui-g">
<h1>Manage folders</h1>
<br />
<?php if (count($folders) > 0) : ?>
	<table>
		<tr>
			<th>id</th>
			<th>Title</th>
			<th>Commands</th>			
		</tr>
		<?php foreach ($folders as $folder): ?>
		<tr>
			<td><?php echo $folder['id']; ?></td>
			<td><?php echo link_for($folder['title'], 'members', 'view_folders', $folder['id']); ?></td>			
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'members', 'action' => 'view_folders', 'id' => $folder['id']),
					array('title' => 'Edit', 'ctrl' => 'members', 'action' => 'modify_folder', 'id' => $folder['id']),
					array('title' => 'Remove', 'ctrl' => 'members', 'action' => 'destroy_folder', 'id' => $folder['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no folders currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new folder', 'members', 'new_folder'); ?></p>
</div>
