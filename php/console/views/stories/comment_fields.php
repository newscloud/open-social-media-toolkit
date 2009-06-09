Posted By Name<br />
<input type="text" name="comment[postedByName]" value="<?php echo htmlentities($comment['postedByName']); ?>" />
<div class="spacer"></div>

Posted By Id<br />
<input type="text" name="comment[postedById]" value="<?php echo htmlentities($comment['postedById']); ?>" />
<div class="spacer"></div>

User Id<br />
<input type="text" name="comment[userid]" value="<?php echo htmlentities($comment['userid']); ?>" />
<div class="spacer"></div>

Date Posted<br />
<input type="text" name="comment[date]" value="<?php echo htmlentities($comment['date']); ?>" />
<div class="spacer"></div>

Site Content Id<br />
<input type="text" name="comment[siteContentId]" value="<?php echo htmlentities($comment['siteContentId']); ?>" />
<div class="spacer"></div>

Content Id<br />
<input type="text" name="comment[contentid]" value="<?php echo htmlentities($comment['contentid']); ?>" />
<div class="spacer"></div>

Comment<br />
<textarea name="comment[comments]" cols="100" rows="15"><?php echo htmlentities(strip_tags($comment['comments'])); ?></textarea>
<div class="spacer"></div>
