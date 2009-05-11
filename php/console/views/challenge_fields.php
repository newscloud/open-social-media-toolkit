Title<br />
<input type="text" name="challenge[title]" value="<? echo $challenge['title']; ?>" />

<div class="spacer"></div>

Short Name - may link to internal challenges, currently <p>
[<?php 
require_once(PATH_CORE .'/classes/log.class.php');
echo implode(', ', array_keys(Log::$siteChallengeActions));

?>]</p><br />
<input type="text" name="challenge[shortName]" value="<? echo $challenge['shortName']; ?>" />

<div class="spacer"></div>

Description<br />
<textarea name="challenge[description]" cols="50" rows="10"><?php echo htmlentities($challenge['description']); ?></textarea>

<div class="spacer"></div>

Thumbnail Image<br />
Current: <img src="<?php echo URL_THUMBNAILS .'/'.$challenge['thumbnail']; ?>" width="100" /><br />
New: 
<input name="thumbnail" type="file" value="<? echo $challenge['thumbnail']; ?>" />			

<div class="spacer"></div>


Initial Completions, global (0=no limit)<br />
<input type="text" name="challenge[initialCompletions]" value="<? echo $challenge['initialCompletions']; ?>" />

<div class="spacer"></div>

Remaining Completions, global<br />
<input type="text" name="challenge[remainingCompletions]" value="<? echo $challenge['remainingCompletions']; ?>" />

<div class="spacer"></div>

Max User Completions (all time) (0=no limit)<br />
<input type="text" name="challenge[maxUserCompletions]" value="<? echo $challenge['maxUserCompletions']; ?>" />

<div class="spacer"></div>

Max User Completions Per Day (0=no limit)<br />
<input type="text" name="challenge[maxUserCompletionsPerDay]" value="<? echo $challenge['maxUserCompletionsPerDay']; ?>" />

<div class="spacer"></div>

Point Value<br />
<input type="text" name="challenge[pointValue]" value="<? echo $challenge['pointValue']; ?>" />

<div class="spacer"></div>

Type ('submission' or 'automatic')<br />
<input type="text" name="challenge[type]" value="<? echo $challenge['type']; ?>" />

<div class="spacer"></div>

Status ('enabled' or 'disabled')<br />
<input type="text" name="challenge[status]" value="<? echo $challenge['status']; ?>" />

<div class="spacer"></div>

Start Date<br />
<input type="text" name="challenge[dateStart]" value="<? echo $challenge['dateStart']; ?>" />

<div class="spacer"></div>

End Date<br />
<input type="text" name="challenge[dateEnd]" value="<? echo $challenge['dateEnd']; ?>" />

<div class="spacer"></div>

Eligibility ('team' or 'general')<br />
<input type="text" name="challenge[eligibility]" value="<? echo $challenge['eligibility']; ?>" />

<div class="spacer"></div>

Featured? (0=no, 1=yes)<br />
<input type="text" name="challenge[isFeatured]" value="<? echo $challenge['isFeatured']; ?>" />

<div class="spacer"></div>


Requires for submission (can include the words 'text', 'photo', and 'video'. 
If in includes 'photo' and 'video', only one or the other needs to be submitted)<br />
<input type="text" name="challenge[requires]" value="<? echo $challenge['requires']; ?>" />

<div class="spacer"></div>