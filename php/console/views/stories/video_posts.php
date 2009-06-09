<div class="yui-g">
<h1>Videos</h1>
<br /><br />

<?php if (count($video_posts) > 0) : ?>
	<table>
		<tr>
			<th>id</th>
			<th>title</th>
			<th>embedCode</th>
			<th>Commands</th>
		</tr>
		<?php foreach ($video_posts as $video): ?>
		<tr>
			<td><?php echo $video['id']; ?></td>
			<td><?php echo $video['title']; ?></td>
			<td><?php echo $video['embedCode']; ?></td>
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'stories', 'action' => 'view_video', 'id' => $video['id']),
					array('title' => 'Edit', 'ctrl' => 'stories', 'action' => 'modify_video', 'id' => $video['id']),
					array('title' => 'Remove', 'ctrl' => 'stories', 'action' => 'destroy_video', 'id' => $video['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no videos currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new video', 'stories', 'new_video'); ?></p>
</div>
