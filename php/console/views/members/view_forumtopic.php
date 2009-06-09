<div class="yui-g">
<h1>Title: <? echo $forumtopic['title']; ?></h1><br /><br />

<div class="forumtopic">
	<p>Intro: <? echo $forumtopic['intro']; ?></p>

<p>lastChanged: <? echo $forumtopic['lastChanged']; ?></p>
<p>numPostsToday: <? echo $forumtopic['numPostsToday']; ?></p>
<p>numViewsToday: <? echo $forumtopic['numViewsToday']; ?></p>
</div>

<br /><br />
<p>
<?php echo link_for('Return to Forum Topics', 'members', 'forumtopics'); ?>
</p>

</div>
