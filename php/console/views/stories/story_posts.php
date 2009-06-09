<div class="yui-g">
<h1>Stories</h1>
<br /><br />

<?php if (count($story_posts) > 0) : ?>
	<table>
		<tr>
			<th>siteContentId</th>
			<th>ContentId</th>
			<th>Title</th>
			<th>Commands</th>			
		</tr>
		<?php foreach ($story_posts as $story): ?>
		<tr>
			<td><?php echo $story['siteContentId']; ?></td>
			<td><?php echo $story['contentid']; ?></td>
			<td><?php echo link_for($story['title'], 'stories', 'view_story', $story['siteContentId']); ?></td>			
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'stories', 'action' => 'view_story', 'id' => $story['siteContentId']),
					array('title' => 'Edit', 'ctrl' => 'stories', 'action' => 'modify_story', 'id' => $story['siteContentId']),
					array('title' => 'Remove', 'ctrl' => 'stories', 'action' => 'destroy_story', 'id' => $story['siteContentId'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
				);
				if ($story['isBlocked'] == 0) {
    				$link_list[] = array('title' => 'Block', 'ctrl' => 'stories', 'action' => 'block_story', 'id' => $story['siteContentId'], 'onclick' => "if(!confirm('Are you sure you want to block this item?')) return false");
				} else {
    				$link_list[] = array('title' => 'Unblock', 'ctrl' => 'stories', 'action' => 'unblock_story', 'id' => $story['siteContentId'], 'onclick' => "if(!confirm('Are you sure you want to unblock this item?')) return false");
				}
				if (($links = build_link_list($link_list))) {
					echo $links;
				}
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no stories currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Story', 'stories', 'new_story'); ?></p>
</div>
