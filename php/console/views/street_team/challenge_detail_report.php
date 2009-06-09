<div class="yui-g">
<h1>Challenge: <? echo $challenge['title']; ?> Detail Report</h1><br /><br />

<div class="challenge">
<? if ($challenge['thumbnail'] != ''): ?>
	<img src="<? echo PATH_SITE.'../facebook/uploads/images/'.$challenge['thumbnail']; ?>" alt="<? echo $challenge['title']; ?>" />
<? endif; ?>

<p>Title: <? echo $challenge['title']; ?></p>
<p>Short Name: <? echo $challenge['shortName']; ?></p>
<p>Description: <? echo $challenge['description']; ?></p>
<p>Thumbnail: <? echo $challenge['thumbnail']; ?>
<img src="<?php echo URL_THUMBNAILS .'/'.$challenge['thumbnail']; ?>"  /></p>

<p>Initial Completions: <? echo $challenge['initialCompletions']; ?></p>
<p>Remaining Completions: <? echo $challenge['remainingCompletions']; ?></p>
<p>Max User Completions: <? echo $challenge['maxUserCompletions']; ?></p>
<p>Max User Completions Per Day: <? echo $challenge['maxUserCompletionsPerDay']; ?></p>
<p>Point Value: <? echo $challenge['pointValue']; ?></p>
<p>Type: <? echo $challenge['type']; ?></p>
<p>Status: <? echo $challenge['status']; ?></p>
<p>Start Date: <? echo $challenge['dateStart']; ?></p>
<p>End Date: <? echo $challenge['dateEnd']; ?></p>
<p>Eligibility: <? echo $challenge['eligibility']; ?></p>
<p>isFeatured: <? echo $challenge['isFeatured']; ?></p>
<p>Requires: <? echo $challenge['requires']; ?></p>
</div>

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
<br /><br />

<br /><div id="completed_challenges">
<h1>Completed Challenge (<?php echo count($completed_challenges); ?>)</h1>
<?php if (count($completed_challenges)): ?>
<p><a href="#" onclick="toggle_list('completed_challenges_list'); return false;">Toggle Comments</a></p><br />
<div class="spacer"></div>
<div id="completed_challenges_list">
<?php foreach ($completed_challenges as $cc): ?>
<div id="completed_challenge_<? echo $cc['id']; ?>" class="completed_challenge" style="border: 1px solid black; padding: 10px; width: 80%;">
<p>User: <? echo link_for($cc['userid'], 'members', 'view_member', $cc['userid']); ?> completed this challenge on: <? echo $cc['datedSubmitted']; ?></p>
<p>Completed Evidence: <? echo $cc['evidence']; ?></p>
<p>Completed Challenge Status: <? echo $cc['status']; ?></p>
<p>This user was awarded <? echo $cc['pointsAwarded']; ?> points.</p>
<p>Comments for this completed challenge: <? echo $cc['comments']; ?>
<p>Image (optional): <? echo (is_null($cc['imgPath']) ? '' : "<a target=\"_cts\" href=\"{$cc['imgPath']}\">{$cc['imgPath']}</a>"); ?></p>
<p>Video (optional): <? echo (is_null($cc['videoPath']) ? '' : "<a target=\"_cts\" href=\"{$cc['videoPath']}\">{$cc['videoPath']}</a>"); ?></p>
</div>
<br />
<div class="spacer"></div>
<?php endforeach; ?>
</div>
<?php else: ?>
<p>This challenge has not yet been completed.</p>
<?php endif; ?>
</div><!-- end member_comments -->
<div class="spacer"></div>
<?php
$link_list = array(
	array('title' => 'Edit', 'ctrl' => 'street_team', 'action' => 'modify_challenge', 'id' => $challenge['id']),
	array('title' => 'Remove', 'ctrl' => 'street_team', 'action' => 'destroy_challenge', 'id' => $challenge['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
	array('title' => 'Return to Challenges', 'ctrl' => 'street_team', 'action' => 'challenges')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>

</div>
