<div class="yui-g">
<div class="story">
<h2>Story Details: <? echo $story['title']; ?></h2>

<p id="story[imageid]">Story Image: </p>
<img width="180" height="135" src="<? echo URL_BASE.'/index.php?p=scaleImg&id='.$story['imageid'].'&x=185&y=130&fixed=x&crop'; ?>"/>
<div class="spacer"></div><br />

<p id="story[date]">Date Posted: <?php echo htmlentities($story['date']); ?></p>
<div class="spacer"></div>

<p id="story[source]">Source: <?php echo htmlentities($story['source']); ?></p>
<div class="spacer"></div>

<p id="story[url]">Url: <a href="<?php echo htmlentities($story['url']); ?>"><?php echo htmlentities($story['url']); ?></a></p>
<div class="spacer"></div>

<p id="story[postedByName]">Posted By: <?php echo link_for($story['postedByName'], 'members', 'view_member', $story['userid']); ?></p>
<div class="spacer"></div>

<p id="story[caption]">Caption: <?php echo htmlentities(strip_tags($story['caption'])); ?></p>
<div class="spacer"></div>

<p id="story[isFeatured]">Featured story?: <?php echo ($story['isFeatured']) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="story[score]">Score: <?php echo htmlentities($story['score']); ?></p>
<div class="spacer"></div>

<p id="story[numComments]">Number of Comments: <?php echo htmlentities($story['numComments']); ?></p>
<div class="spacer"></div>

<script type="text/javascript">
function toggle_list(id) {
	var list = document.getElementById(id);

	if (list.style.display == 'block') {
		list.style.display = 'none';
	} else {
		list.style.display = 'block';
	}
}
</script>
<br /><div id="story_comments">
<h1>Comments (<?php echo count($comments); ?>)</h1>
<?php if (count($comments)): ?>
<p><a href="#" onclick="toggle_list('comments_table'); return false;">Toggle Comments</a></p><br />
<div class="spacer"></div>
<table id="comments_table" style="display: block;">
	<thead>
		<tr>
			<th>User</th>
			<th>Date Posted</th>
			<th>Comment</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($comments as $comment): ?>
		<tr>
			<td><?php echo link_for($comment['postedByName'], 'members', 'view_member', $comment['userid']); ?></td>
			<td><?php echo $comment['date']; ?></td>
			<td><?php echo $comment['comments']; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>This story does not yet have any comments.</p>
<?php endif; ?>
</div><!-- end story_comments -->
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new story', 'ctrl' => 'stories', 'action' => 'new_story'),
	array('title' => 'Edit this story', 'ctrl' => 'stories', 'action' => 'modify_story', 'id' => $story['siteContentId']),
	array('title' => 'Delete this story', 'ctrl' => 'stories', 'action' => 'destroy_story', 'id' => $story['siteContentId'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false")
);
    if ($story['isBlocked'] == 0) {
        $link_list[] = array('title' => 'Block this story', 'ctrl' => 'stories', 'action' => 'block_story', 'id' => $story['siteContentId'], 'onclick' => "if(!confirm('Are you sure you want to block this item?')) return false");
	} else {
        $link_list[] = array('title' => 'Unblock this story', 'ctrl' => 'stories', 'action' => 'unblock_story', 'id' => $story['siteContentId'], 'onclick' => "if(!confirm('Are you sure you want to unblock this item?')) return false");
	}
	$link_list[] = array('title' => 'Back to Stories', 'ctrl' => 'stories', 'action' => 'story_posts');
	if (($links = build_link_list($link_list))) {
		echo $links;
	}
?>
</div>
