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
			<td><a href="index.php?p=console&group=stories&action=view_story&id=<?php echo $story['siteContentId']; ?>"><?php echo $story['title']; ?></a></td>			
			<td>
				<a href="index.php?p=console&group=stories&action=view_story&id=<? echo $story['siteContentId']; ?>">View</a> -- 
				<a href="index.php?p=console&group=stories&action=modify_story&id=<? echo $story['siteContentId']; ?>">Edit</a> -- 
    			<a href="index.php?p=console&group=stories&action=destroy_story&id=<?php echo $story['siteContentId'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">Remove</a> -- 
				<?php if ($story['isBlocked'] == 0): ?>
    				<a href="index.php?p=console&group=stories&action=block_story&id=<?php echo $story['siteContentId'] ?>" onclick="if(!confirm('Are you sure you want to block this item?')) return false">Block</a>
				<?php else: ?>
    				<a href="index.php?p=console&group=stories&action=unblock_story&id=<?php echo $story['siteContentId'] ?>" onclick="if(!confirm('Are you sure you want to unblock this item?')) return false">Unblock</a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no stories currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=stories&action=new_story">Create a new Story</a></p>
</div>
