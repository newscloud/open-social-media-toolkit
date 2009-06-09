<div class="yui-g">
<h1>Widgets</h1>
<br /><br />

<?php $front_link_lists = array(
	array('title' => 'Create a new Widget', 'ctrl' => 'stories', 'action' => 'new_widget'),
	array('title' => 'Reset cover feature', 'ctrl' => 'stories', 'action' => 'reset_widget_cover'),
	array('title' => 'Reset cover sidebar', 'ctrl' => 'stories', 'action' => 'reset_widget_sidebar')
);
if (($front_links = build_link_list($front_link_lists))) {
	echo $front_links;
}
?>
<?php if (count($widgets) > 0) : ?>
	<table>
		<tr>
			<th>Title</th>
			<th>Commands</th>			
		</tr>
		<?php foreach ($widgets as $widget): ?>
		<tr>
			<td><?php echo link_for($widget['title'], 'stories', 'modify_widget', $widget['id']); ?></td>			
			<td>
			<?php
				$link_list = array(
					array('title' => 'Edit', 'ctrl' => 'stories', 'action' => 'modify_widget', 'id' => $widget['id']),
					array('title' => 'Add to story', 'ctrl' => 'stories', 'action' => 'add_story_widget', 'id' => $widget['id']),
					array('title' => 'Place in other location', 'ctrl' => 'stories', 'action' => 'place_widget', 'id' => $widget['id']),
					array('title' => 'Remove from all stories', 'ctrl' => 'stories', 'action' => 'remove_widget_from_stories', 'id' => $widget['id']),
					array('title' => 'Delete', 'ctrl' => 'stories', 'action' => 'destroy_widget', 'id' => $widget['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
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
<h2>Sorry no widgets currently</h2>
<?php endif; ?>
<div class="spacer"></div><br /><br />
<?php $bottom_link_lists = array(
	array('title' => 'Create a new Widget', 'ctrl' => 'stories', 'action' => 'new_widget'),
	array('title' => 'Reset cover feature', 'ctrl' => 'stories', 'action' => 'reset_widget_cover'),
	array('title' => 'Reset cover sidebar', 'ctrl' => 'stories', 'action' => 'reset_widget_sidebar')
);
if (($bottom_links = build_link_list($bottom_link_lists))) {
	echo $bottom_links;
}
?>
</div>
