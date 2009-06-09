User ID<br />
<input type="text" name="completed_challenge[userid]" value="<? echo $completed_challenge['userid']; ?>" />

<div class="spacer"></div>

Challenge ID<br />
<input type="text" name="completed_challenge[challengeid]" value="<? echo $completed_challenge['challengeid']; ?>" />

<div class="spacer"></div>

Date Submitted<br />
<input type="text" name="completed_challenge[dateSubmitted]" value="<? echo $completed_challenge['dateSubmitted']; ?>" />

<div class="spacer"></div>

Date Awarded<br />
<input type="text" name="completed_challenge[dateAwarded]" value="<? echo $completed_challenge['dateAwarded']; ?>" />

<div class="spacer"></div>

Evidence<br />
<textarea name="completed_challenge[evidence]" cols="100" rows="10"><?php echo htmlentities($completed_challenge['evidence']); ?></textarea>

<div class="spacer"></div>

Comments (This is what appears in the feed and blog!)<br />
<textarea name="completed_challenge[comments]" cols="100" rows="10"><?php echo htmlentities($completed_challenge['comments']); ?></textarea>

<div class="spacer"></div>

Status<br />
<input type="text" name="completed_challenge[status]" value="<? echo $completed_challenge['status']; ?>" />

<div class="spacer"></div>

Points Awarded<br />
<input type="text" name="completed_challenge[pointsAwarded]" value="<? echo $completed_challenge['pointsAwarded']; ?>" />

<div class="spacer"></div>
