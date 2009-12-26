<div class="yui-g">
<div class="feed">
<h2>Feed Details: <? echo $feed['title']; ?></h2>

<p id="feed[url]">Web site URL:<br /> <?php echo htmlentities($feed['url']); ?></p>
<div class="spacer"></div>

<p id="feed[rss]">RSS URL:<br /> <?php echo htmlentities($feed['rss']); ?></p>
<div class="spacer"></div>

<p id="feed[feedType]">Width: <?php echo htmlentities($feed['feedType']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new feed', 'ctrl' => 'stories', 'action' => 'new_feed'),
	array('title' => 'Edit this feed', 'ctrl' => 'stories', 'action' => 'modify_feed', 'id' => $feed['id']),
	array('title' => 'Delete this feed', 'ctrl' => 'stories', 'action' => 'destroy_feed', 'id' => $feed['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
   array('title' => 'Back to Feeds', 'ctrl' => 'stories', 'action' => 'list_feed')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>
</div>
