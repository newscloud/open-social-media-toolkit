<div class="yui-g">
<div class="story">
<h2>Story Details: <? echo $story['title']; ?></h2>

<p id="story[imageid]">Story Image: </p>
<img width="180" height="135" src="http://www.newscloud.com/images/scaleImage.php?id=<? echo $story['imageid']; ?>&x=185&y=130&fixed=x&crop"/>
<div class="spacer"></div><br />

<p id="story[date]">Date Posted: <?php echo htmlentities($story['date']); ?></p>
<div class="spacer"></div>

<p id="story[source]">Source: <?php echo htmlentities($story['source']); ?></p>
<div class="spacer"></div>

<p id="story[url]">Url: <a href="<?php echo htmlentities($story['url']); ?>"><?php echo htmlentities($story['url']); ?></a></p>
<div class="spacer"></div>

<p id="story[postedByName]">Posted By: <a href="index.php?p=console&group=members&action=view_member&id=<? echo htmlentities($story['userid']); ?>"><? echo htmlentities($story['postedByName']); ?></a></p>
<div class="spacer"></div>

<p id="story[caption]">Caption: <?php echo htmlentities(strip_tags($story['caption'])); ?></p>
<div class="spacer"></div>

<p id="story[isFeatured]">Featured story?: <?php echo ($story['isFeatured']) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="story[score]">Score: <?php echo htmlentities($story['score']); ?></p>
<div class="spacer"></div>

<p id="story[numComments]">Number of Comments: <?php echo htmlentities($story['numComments']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<ul>
	<li><a href="index.php?p=console&group=stories&action=new_story">Create a new story</a></li>
	<li><a href="index.php?p=console&group=stories&action=modify_story&id=<? echo $story['siteContentId']; ?>">Edit this story</a></li>
	<li><a href="index.php?p=console&group=stories&action=destroy_story&id=<? echo $story['siteContentId']; ?>">Delete this story</a></li>
    <?php if ($story['isBlocked'] == 0): ?>
        <li><a href="index.php?p=console&group=stories&action=block_story&id=<? echo $story['siteContentId']; ?>">Block this story</a></li>
    <?php else: ?>
        <li><a href="index.php?p=console&group=stories&action=unblock_story&id=<? echo $story['siteContentId']; ?>">Unblock this story</a></li>
    <?php endif; ?>
	<li><a href="index.php?p=console&group=stories&action=story_posts">Back to Stories</a></li>
</ul>
</div>
