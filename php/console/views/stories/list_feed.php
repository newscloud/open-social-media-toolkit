<div class="yui-g">
<h1>Feeds</h1>
<br /><br />

<?php if (count($list_feed) > 0) : ?>
	<table>
		<tr>
			<th>id</th>
			<th>Title</th>
			<th>Commands</th>			
		</tr>
		<?php foreach ($list_feed as $feed): ?>
		<tr>
			<td><?php echo $feed['id']; ?></td>
			<td><?php echo link_for($feed['title'], 'stories', 'view_feed', $feed['id']); ?></td>			
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'stories', 'action' => 'view_feed', 'id' => $feed['id']),
					array('title' => 'Edit', 'ctrl' => 'stories', 'action' => 'modify_feed', 'id' => $feed['id']),
					array('title' => 'Remove', 'ctrl' => 'stories', 'action' => 'destroy_feed', 'id' => $feed['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no Feeds currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Feed', 'stories', 'new_feed'); ?></p>
</div>
