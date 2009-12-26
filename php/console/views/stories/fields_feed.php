Title<br />
<input type="text" name="feed[title]" value="<?php echo htmlentities($feed['title']); ?>" />
<div class="spacer"></div>

Web Site URL<br />
<input type="text" name="feed[url]" value="<?php echo htmlentities($feed['url']); ?>" />
<div class="spacer"></div>

RSS URL<br />
<input type="text" name="feed[rss]" value="<?php echo htmlentities($feed['rss']); ?>" />
<div class="spacer"></div>

Feed Type<br />
<select name="feedType">
	<option value="blog">Blog</option>
	<option value="wire">News feed</option>
</select>
<div class="spacer"></div>
