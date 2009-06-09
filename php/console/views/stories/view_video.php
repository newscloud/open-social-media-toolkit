<div class="yui-g">
<div class="video">
<h2>Video Details: <? echo $video['title']; ?></h2>

<p id="video[imageid]"> <? echo $video['id']; ?></p>
<div class="spacer"></div><br />

<p id="video[embedCode]">Embed Code: <?php echo htmlentities($video['embedCode']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new video', 'ctrl' => 'stories', 'action' => 'new_video'),
	array('title' => 'Edit this video', 'ctrl' => 'stories', 'action' => 'modify_video', 'id' => $video['id']),
	array('title' => 'Delete this video', 'ctrl' => 'stories', 'action' => 'destroy_video', 'id' => $video['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
   array('title' => 'Back to videos', 'ctrl' => 'stories', 'action' => 'video_posts')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>
</div>
