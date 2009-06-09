<div class="yui-g">
<h1>Forum Topics</h1>
<br /><br />

<?php if (count($forumtopics) > 0) : ?>
	<table>
		<tr>
			<th>Title</th>
			<th>Intro</th>
			<th>Last Changed</th>
			<th>Number of Posts</th>			
			<th>Number of Views</th>			
			<th>Actions</th>
		</tr>
		<?php foreach ($forumtopics as $forumtopic): ?>
		<tr>
			<td><?php echo $forumtopic['title']; ?></td>
			<td><?php echo $forumtopic['intro']; ?></td>
			<td><?php echo $forumtopic['lastChanged']; ?></td>
			<td><?php echo $forumtopic['numPostsToday']; ?></td>
			<td><?php echo $forumtopic['numViewsToday']; ?></td>
			<td>
				<?php
				$link_list = array(
					array('title' => 'View', 'ctrl' => 'members', 'action' => 'view_forumtopic', 'id' => $forumtopic['id']),
					array('title' => 'Edit', 'ctrl' => 'members', 'action' => 'modify_forumtopic', 'id' => $forumtopic['id']),
					array('title' => 'Remove', 'ctrl' => 'members', 'action' => 'destroy_forumtopic', 'id' => $forumtopic['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no forum topics currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Forum Topic', 'members', 'new_forumtopic'); ?></p>
</div>
