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

<script type="text/javascript">
function toggle_list(id) {
	var list = document.getElementById(id);

	if (list.style.display == 'block') {
		list.style.display = 'none';
	} else {
		list.style.display = 'block';
	}
}
</script>
<br /><div id="member_comments">
<h1>Comments (<?php echo count($comments); ?>)</h1>
<?php if (count($comments)): ?>
<p><a href="#" onclick="toggle_list('comments_table'); return false;">Toggle Comments</a></p><br />
<div class="spacer"></div>
<table id="comments_table" style="display: block;">
	<thead>
		<tr>
			<th>Story Title</th>
			<th>Date Posted</th>
			<th>Comment</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($comments as $comment): ?>
		<tr>
			<td><?php echo link_for($comment['title'], 'stories', 'view_story', $comment['siteContentId']); ?></td>
			<td><?php echo $comment['date']; ?></td>
			<td><?php echo $comment['comments']; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>This member has not yet posted any comments.</p>
<?php endif; ?>
</div><!-- end member_comments -->
<div class="spacer"></div>


<br /><div id="member_challenges">
<h1>Completed Challenges (<?php echo count($challenges); ?>)</h1>
<?php if (count($challenges)): ?>
<p><a href="#" onclick="toggle_list('challenges_table'); return false;">Toggle Challenges</a></p><br />
<div class="spacer"></div>
<table id="challenges_table" style="display: block;">
	<thead>
		<tr>
			<th>Challenge Title</th>
			<th>Status</th>
			<th>Points Awarded</th>
			<th>Date Submitted</th>
			<th>Evidence</th>
			<th>Comments</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($challenges as $challenge): ?>
		<tr>
			<td><?php echo link_for($challenge['title'], 'street_team', 'view_challenge', $challenge['challengeid']); ?></td>
			<td><?php echo $challenge['status']; ?></td>
			<td><?php echo $challenge['pointsAwarded']; ?></td>
			<td><?php echo $challenge['dateSubmitted']; ?></td>
			<td><?php echo $challenge['comments']; ?></td>
			<td><?php echo $challenge['evidence']; ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>This member has not yet posted any challenges.</p>
<?php endif; ?>
</div><!-- end member_challenges -->
<div class="spacer"></div>

<br /><div id="member_blogs">
<h1>Blogs (<?php echo count($blogs); ?>)</h1>
<?php if (count($blogs)): ?>
<p><a href="#" onclick="toggle_list('blogs_table'); return false;">Toggle Blogs</a></p><br />
<div class="spacer"></div>
<table id="blogs_table" style="display: block;">
	<thead>
		<tr>
			<th>Story Title</th>
			<th>Date Posted</th>
			<th>Score</th>
			<th>Num Comments</th>
			<th>Caption</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($blogs as $blog): ?>
		<tr>
			<td><a target="_cts" href="<? echo URL_CANVAS; ?>/?p=read&cid=<? echo $blog['siteContentId']; ?>"><? echo $blog['title']; ?></a></td>
			<td><?php echo $blog['date']; ?>
			<td><?php echo $blog['score']; ?>
			<td><?php echo $blog['numComments']; ?>
			<td>
			<?php if ($blog['url'] != ''): ?>
				<a target="_cts" href="<?php echo $blog['url']; ?>"><?php echo $blog['caption']; ?></a></td>
			<?php else: ?>
				<p><?php echo $blog['caption']; ?></p>
			<?php endif; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>This member has not yet posted any blogs.</p>
<?php endif; ?>
</div><!-- end member_blogs -->
<div class="spacer"></div>

<br /><div id="member_stories">
<h1>Posted Stories (<?php echo count($stories); ?>)</h1>
<?php if (count($stories)): ?>
<p><a href="#" onclick="toggle_list('stories_table'); return false;">Toggle Stories</a></p><br />
<div class="spacer"></div>
<table id="stories_table" style="display: block;">
	<thead>
		<tr>
			<th>Story Title</th>
			<th>Date Posted</th>
			<th>Score</th>
			<th>Num Comments</th>
			<th>Caption</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($stories as $story): ?>
		<tr>
			<td><?php echo link_for($story['title'], 'stories', 'view_story', $story['siteContentId']); ?></td>
			<td><?php echo $story['date']; ?>
			<td><?php echo $story['score']; ?>
			<td><?php echo $story['numComments']; ?>
			<td><a target="_cts" href="<?php echo $story['url']; ?>"><?php echo $story['caption']; ?></a></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>This member has not yet posted any stories.</p>
<?php endif; ?>
</div><!-- end member_stories -->
<div class="spacer"></div>





<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new member', 'ctrl' => 'members', 'action' => 'new_member'),
 	array('title' => 'Show friend invite credits ', 'ctrl' => 'members', 'action' => 'show_friend_invite_credits', 'id' => $member['userid']),
	array('title' => 'Authorize Member for Template Editing', 'ctrl' => 'members', 'action' => 'authorize_editing', 'id' => $member['userid']),
	array('title' => 'Edit this member', 'ctrl' => 'members', 'action' => 'modify_member', 'id' => $member['userid']),
	array('title' => 'Delete this member', 'ctrl' => 'members', 'action' => 'destroy_member', 'id' => $member['userid'])
);
if ($member['isBlocked'] == 1) {
	$link_list[] = array('title' => 'Unblock', 'ctrl' => 'members', 'action' => 'unblock_member', 'id' => $member['userid'], 'onclick' => "if(!confirm('Are you sure you want to unblock this item?')) return false");
} else {
	$link_list[] = array('title' => 'Block', 'ctrl' => 'members', 'action' => 'block_member', 'id' => $member['userid'], 'onclick' => "if(!confirm('Are you sure you want to block this item?')) return false");
}
$link_list[] = array('title' => 'Back to Members', 'ctrl' => 'members', 'action' => 'members');

if (($links = build_link_list($link_list))) {
	echo $links;
}
?>
</div>
