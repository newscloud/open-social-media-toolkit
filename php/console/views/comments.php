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
			<td><a href="index.php?p=console&group=stories&action=view_story&id=<?php echo $comment['siteContentId']; ?>"><?php echo $comment['siteContentId']; ?></a></td>
			<td><?php echo $comment['contentid']; ?></td>
			<td><a href="index.php?p=console&group=members&action=view_member&id=<?php echo $comment['userid']; ?>"><?php echo $comment['postedByName']; ?></a></td>			
			<td><?php echo $comment['date']; ?></td>
			<td>
				<a href="index.php?p=console&group=stories&action=view_comment&id=<? echo $comment['siteCommentId']; ?>">View</a> -- 
				<a href="index.php?p=console&group=stories&action=modify_comment&id=<? echo $comment['siteCommentId']; ?>">Edit</a> -- 
    			<a href="index.php?p=console&group=stories&action=destroy_comment&id=<?php echo $comment['siteCommentId'] ?>" onclick="if(!confirm('Are you sure you want to remove this comment?')) return false">Remove</a> -- 
				<?php if ($comment['isBlocked'] == 0): ?>
    				<a href="index.php?p=console&group=stories&action=block_comment&id=<?php echo $comment['siteCommentId'] ?>" onclick="if(!confirm('Are you sure you want to block this comment?')) return false">Block</a>
				<?php else: ?>
    				<a href="index.php?p=console&group=stories&action=unblock_comment&id=<?php echo $comment['siteCommentId'] ?>" onclick="if(!confirm('Are you sure you want to unblock this comment?')) return false">Unblock</a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no comments currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=stories&action=new_comment">Create a new Comment</a></p>
</div>
