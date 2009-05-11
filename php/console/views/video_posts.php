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
				<a href="index.php?p=console&group=stories&action=view_video&id=<? echo $video['id']; ?>">View</a> -- 
				<a href="index.php?p=console&group=stories&action=modify_video&id=<? echo $video['id']; ?>">Edit</a> -- 
    			<a href="index.php?p=console&group=stories&action=destroy_video&id=<?php echo $video['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">Remove</a> -- 
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no videos currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=stories&action=new_video">Create a new video</a></p>
</div>
