Title<br />
<input type="text" name="prize[title]" value="<? echo $prize['title']; ?>" />

<div class="spacer"></div>

Short Name<br />
<input type="text" name="prize[shortName]" value="<? echo $prize['shortName']; ?>" />

<div class="spacer"></div>

Description<br />
<textarea name="prize[description]" cols="50" rows="10"><?php echo htmlentities($prize['description']); ?></textarea>

<div class="spacer"></div>

Thumbnail Image<br />
Current: <img src="<?php echo URL_THUMBNAILS .'/'.$prize['thumbnail']; ?>" width="100" /><br />
New: 
<input name="thumbnail" type="file" value="<? echo $prize['thumbnail']; ?>" />			

<div class="spacer"></div>



Sponsor<br />
<input type="text" name="prize[sponsor]" value="<? echo $prize['sponsor']; ?>" />

<div class="spacer"></div>

SponsorUrl (must begin with <pre>http://</pre>)<br />
<input type="text" name="prize[sponsorUrl]" value="<? echo $prize['sponsorUrl']; ?>" />

<div class="spacer"></div>

Initial Stock<br />
<input type="text" name="prize[initialStock]" value="<? echo $prize['initialStock']; ?>" />

<div class="spacer"></div>

Current Stock<br />
<input type="text" name="prize[currentStock]" value="<? echo $prize['currentStock']; ?>" />

<div class="spacer"></div>

Point Cost<br />
<input type="text" name="prize[pointCost]" value="<? echo $prize['pointCost']; ?>" />

<div class="spacer"></div>

<!-- Category<br />
<input type="text" name="prize[category]" value="<? echo $prize['category']; ?>" />

<div class="spacer"></div>
-->
Status ('enabled' or 'disabled')<br />
<input type="text" name="prize[status]" value="<? echo $prize['status']; ?>" />

<div class="spacer"></div>

Start Date<br />
<input type="text" name="prize[dateStart]" value="<? echo $prize['dateStart']; ?>" />

<div class="spacer"></div>

End Date<br />
<input type="text" name="prize[dateEnd]" value="<? echo $prize['dateEnd']; ?>" />

<div class="spacer"></div>

Eligibility ('team' or 'general')<br />
<input type="text" name="prize[eligibility]" value="<? echo $prize['eligibility']; ?>" />

<div class="spacer"></div>

User Maximum<br />
<input type="text" name="prize[userMaximum]" value="<? echo $prize['userMaximum']; ?>" />

<div class="spacer"></div>

Order Fields Needed (valid options are 'phone', 'address' e.g. "phone" or "phone address" or "address")<br />
<input type="text" name="prize[orderFieldsNeeded]" value="<? echo $prize['orderFieldsNeeded']; ?>" />

<div class="spacer"></div>

is Grand Prize? (1 or 0)<br />
<input type="text" name="prize[isGrand]" value="<? echo $prize['isGrand']; ?>" />

<div class="spacer"></div>

is Weekly Prize? (1 or 0)<br />
<input type="text" name="prize[isWeekly]" value="<? echo $prize['isWeekly']; ?>" />

<div class="spacer"></div>

is Featured Prize? (1 or 0)<br />
<input type="text" name="prize[isFeatured]" value="<? echo $prize['isFeatured']; ?>" />

<div class="spacer"></div>

Dollar Value (aids sorting)<br />
<input type="text" name="prize[dollarValue]" value="<? echo $prize['dollarValue']; ?>" />

<div class="spacer"></div>

