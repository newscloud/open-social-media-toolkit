<div class="yui-g">
<h1>Challenge: <? echo $challenge['title']; ?></h1><br /><br />

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

<br /><br />
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
