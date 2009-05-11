<div class="yui-g">
<div class="member">
<h2>Member Details: <? echo $member['name']; ?></h2>

<p id="member[email]">Email: <?php echo htmlentities($member['email']); ?></p>
<div class="spacer"></div>

<p id="member[votePower]">Vote Power: <?php echo htmlentities($member['votePower']); ?></p>
<div class="spacer"></div>

<p id="member[isAdmin]">Admin?: <?php echo ($member['isAdmin'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[isMember]">Member?: <?php echo ($member['isMember'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[isModerator]">Moderator?: <?php echo ($member['isModerator'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[isBlocked]">Blocked?: <?php echo ($member['isBlocked'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[dateRegistered]">Date Registered: <?php echo htmlentities($member['dateRegistered']); ?></p>
<div class="spacer"></div>

<p id="member[userLevel]">User Level: <?php echo htmlentities($member['userLevel']); ?></p>
<div class="spacer"></div>

<p id="member[cachedPointTotal]">Cached Point Total: <?php echo htmlentities($member['cachedPointTotal']); ?></p>
<div class="spacer"></div>

<p id="member[cachedStoriesPosted]">Cached Stories Posted: <?php echo htmlentities($member['cachedStoriesPosted']); ?></p>
<div class="spacer"></div>

<p id="member[cachedCommentsPosted]">Cached Comments Posted: <?php echo htmlentities($member['cachedCommentsPosted']); ?></p>
<div class="spacer"></div>

<p id="member[remoteStatus]">Remote Status: <?php echo htmlentities($member['remoteStatus']); ?></p>
<div class="spacer"></div>

<p id="member[eligibility]">Eligibility: <?php echo htmlentities($member['eligibility']); ?></p>
<div class="spacer"></div>

<p id="member[optInStudy]">Opt in Study: <?php echo ($member['optInStudy'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[optInEmail]">Opt in Email: <?php echo ($member['optInEmail'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[optInProfile]">Opt in Profile: <?php echo ($member['optInProfile'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[optInFeed]">Opt in Feed: <?php echo ($member['optInFeed'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[optInSMS]">Opt in SMS: <?php echo ($member['optInSMS'] == 1) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="member[address1]">Address 1: <?php echo htmlentities($member['address1']); ?></p>
<div class="spacer"></div>

<p id="member[address2]">Address 2: <?php echo htmlentities($member['address2']); ?></p>
<div class="spacer"></div>

<p id="member[city]">City: <?php echo htmlentities($member['city']); ?></p>
<div class="spacer"></div>

<p id="member[state]">State: <?php echo htmlentities($member['state']); ?></p>
<div class="spacer"></div>

<p id="member[zip]">Zipcode: <?php echo htmlentities($member['zip']); ?></p>
<div class="spacer"></div>

<p id="member[country]">Country: <?php echo htmlentities($member['country']); ?></p>
<div class="spacer"></div>

<p id="member[phone]">Phone: <?php echo htmlentities($member['phone']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<ul>
	<li><a href="index.php?p=console&group=members&action=new_member">Create a new member</a></li>
 	 <li><a href="index.php?p=console&group=members&action=show_friend_invite_credits&id=<? echo $member['userid']; ?>">Show friend invite credits </a></li>
	<li><a href="index.php?p=console&group=members&action=authorize_editing&id=<? echo $member['userid']; ?>">Authorize Member for Template Editing</a></li>
	<li><a href="index.php?p=console&group=members&action=modify_member&id=<? echo $member['userid']; ?>">Edit this member</a></li>
	<li><a href="index.php?p=console&group=members&action=destroy_member&id=<? echo $member['userid']; ?>">Delete this member</a></li>
	<?php if ($member['isBlocked'] == 0): ?>
		<li><a href="index.php?p=console&group=members&action=block_member&id=<? echo $member['userid']; ?>">Block this member</a></li>
	<?php else: ?>
		<li><a href="index.php?p=console&group=members&action=unblock_member&id=<? echo $member['userid']; ?>">Unblock this member</a></li>
	<?php endif; ?>
	<li><a href="index.php?p=console&group=members&action=members">Back to Members</a></li>
</ul>
</div>
