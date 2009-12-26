<div class="yui-g">
<div class="folder">
<h2>Folder Details: <? echo $link['title']; ?></h2>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new folder', 'ctrl' => 'members', 'action' => 'new_folderlink'),
	array('title' => 'Edit this folder', 'ctrl' => 'members', 'action' => 'modify_folderlink', 'id' => $link['id']),
	array('title' => 'Delete this folder', 'ctrl' => 'members', 'action' => 'destroy_folderlink', 'id' => $link['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
   array('title' => 'Back to folders', 'ctrl' => 'members', 'action' => 'folderlinks')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>
</div>
