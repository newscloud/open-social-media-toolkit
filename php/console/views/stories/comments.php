<div class="yui-g">
<h1>Comments</h1>
<br /><br />

<?php if (count($comments) > 0) : ?>
	<table>
		<tr>
			<th>Site Content ID</th>
			<th>Content ID</th>
			<th>Posted By</th>
			<th>Date</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($comments as $comment): ?>
		<tr>
			<td><?php echo link_for($comment['siteContentId'], 'stories', 'view_story', $comment['siteContentId']); ?></td>
			<td><?php echo $comment['contentid']; ?></td>
			<td><?php echo link_for($comment['postedByName'], 'members', 'view_member', $comment['userid']); ?></td>			
			<td><?php echo $comment['date']; ?></td>
			<td>
			<?php
			$link_list = array(
				array('title' => 'View', 'ctrl' => 'stories', 'action' => 'view_comment', 'id' => $comment['siteCommentId']),
				array('title' => 'Edit', 'ctrl' => 'stories', 'action' => 'modify_comment', 'id' => $comment['siteCommentId']),
    			array('title' => 'Remove', 'ctrl' => 'stories', 'action' => 'destroy_comment', 'id' => $comment['siteCommentId'], 'onclick' => "if(!confirm('Are you sure you want to remove this comment?')) return false")
			);
				if ($comment['isBlocked'] == 0) {
    				$link_list[] = array('title' => 'Block', 'ctrl' => 'stories', 'action' => 'block_comment', 'id' => $comment['siteCommentId'], 'onclick' => "if(!confirm('Are you sure you want to block this comment?')) return false");
				} else {
    				$link_list[] = array('title' => 'Unblock', 'ctrl' => 'stories', 'action' => 'unblock_comment', 'id' => $comment['siteCommentId'], 'onclick' => "if(!confirm('Are you sure you want to unblock this comment?')) return false");
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
<h2>Sorry no comments currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><?php echo link_for('Create a new Comment', 'stories', 'new_comment'); ?></p>
</div>
