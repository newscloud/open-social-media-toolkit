<div class="yui-g">
<div class="video">
<h2>Video Details: <? echo $video['title']; ?></h2>

<p id="video[imageid]"> <? echo $video['id']; ?></p>
<div class="spacer"></div><br />

<p id="video[embedCode]">Embed Code: <?php echo htmlentities($video['embedCode']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<ul>
	<li><a href="index.php?p=console&group=stories&action=new_video">Create a new video</a></li>
	<li><a href="index.php?p=console&group=stories&action=modify_video&id=<? echo $video['id']; ?>">Edit this video</a></li>
	<li><a href="index.php?p=console&group=stories&action=destroy_video&id=<? echo $video['id']; ?>">Delete this video</a></li>
   <li><a href="index.php?p=console&group=stories&action=video_posts">Back to videos</a></li>
</ul>
</div>
