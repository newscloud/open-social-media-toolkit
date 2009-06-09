Title<br />
<input type="text" name="story[title]" value="<?php echo htmlentities($story['title']); ?>" />
<div class="spacer"></div>

Story Image ID<br />
<input type="text" name="story[imageid]" value="<?php echo htmlentities($story['imageid']); ?>" />
<div class="spacer"></div>

Date Posted<br />
<input type="text" name="story[date]" value="<?php echo htmlentities($story['date']); ?>" />
<div class="spacer"></div>

Source<br />
<input type="text" name="story[source]" value="<?php echo htmlentities($story['source']); ?>" />
<div class="spacer"></div>

URL<br />
<input type="text" name="story[url]" value="<?php echo htmlentities($story['url']); ?>" />
<div class="spacer"></div>

Posted By Id<br />
<input type="text" name="story[postedById]" value="<?php echo htmlentities($story['postedById']); ?>" />
<div class="spacer"></div>

Posted By Name<br />
<input type="text" name="story[postedByName]" value="<?php echo htmlentities($story['postedByName']); ?>" />
<div class="spacer"></div>

User Id<br />
<input type="text" name="story[userid]" value="<?php echo htmlentities($story['userid']); ?>" />
<div class="spacer"></div>

Caption<br />
<textarea name="story[caption]" cols="100" rows="15"><?php echo htmlentities(strip_tags($story['caption'])); ?></textarea>
<div class="spacer"></div>

Featured Story?<br />
<input type="radio" name="story[isFeatured]" value="0" <? if ($story['isFeatured'] == 0) echo 'checked'; ?> />No
<input type="radio" name="story[isFeatured]" value="1" <? if ($story['isFeatured'] == 1) echo 'checked'; ?> />Yes
<div class="spacer"></div>

Score<br />
<input type="text" name="story[score]" value="<?php echo htmlentities($story['score']); ?>" />
<div class="spacer"></div>

Number of Comments<br />
<input type="text" name="story[numComments]" disable="disabled" value="<?php echo htmlentities($story['numComments']); ?>" />
<div class="spacer"></div>

Video Id for Moderator Introduction<br />
<input type="text" name="story[videoIntroId]" value="<?php echo htmlentities($story['videoIntroId']); ?>" />
<div class="spacer"></div>
