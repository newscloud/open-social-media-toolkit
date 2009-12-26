Name<br />
<input type="text" name="card[name]" value="<?php echo htmlentities($card['name']); ?>" />
<div class="spacer"></div>

Short Caption<br />
<textarea rows="4" cols="80" name="card[shortCaption]"><?php echo htmlentities($card['shortCaption']); ?></textarea>
<div class="spacer"></div>

Long Description<br />
<textarea rows="12" cols="80" name="card[longCaption]"><?php echo htmlentities($card['longCaption']); ?></textarea>
<div class="spacer"></div>

Date Available<br />
<input type="text" name="card[dateAvailable]" value="<?php echo htmlentities($card['dateAvailable']); ?>" />

<div class="spacer"></div>
