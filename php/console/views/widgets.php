<div class="yui-g">
<h1>Widgets</h1>
<br /><br />

<p><a href="index.php?p=console&group=stories&action=new_widget">Create a new Widget</a> | <a href="index.php?p=console&group=stories&action=reset_widget_cover">Reset cover feature</a> | <a href="index.php?p=console&group=stories&action=reset_widget_sidebar">Reset cover sidebar</a></p>
<?php if (count($widgets) > 0) : ?>
	<table>
		<tr>
			<th>Title</th>
			<th>Commands</th>			
		</tr>
		<?php foreach ($widgets as $widget): ?>
		<tr>
			<td><a href="index.php?p=console&group=stories&action=modify_widget&id=<?php echo $widget['id']; ?>"><?php echo $widget['title']; ?></a></td>			
			<td>
				<a href="index.php?p=console&group=stories&action=modify_widget&id=<? echo $widget['id']; ?>">Edit</a> -- 
				<a href="index.php?p=console&group=stories&action=add_story_widget&id=<? echo $widget['id']; ?>">Add to story</a> --
				<a href="index.php?p=console&group=stories&action=place_widget&id=<? echo $widget['id']; ?>">Place in other location</a> --
				<a href="index.php?p=console&group=stories&action=remove_widget_from_stories&id=<? echo $widget['id']; ?>">Remove from all stories</a> -- 
    			<a href="index.php?p=console&group=stories&action=destroy_widget&id=<?php echo $widget['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">Delete</a> -- 
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<h2>Sorry no widgets currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<p><a href="index.php?p=console&group=stories&action=new_widget">Create a new Widget</a> | <a href="index.php?p=console&group=stories&action=reset_widget_cover">Reset cover feature</a> | <a href="index.php?p=console&group=stories&action=reset_widget_sidebar">Reset cover sidebar</a></p>
</div>
