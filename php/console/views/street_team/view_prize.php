<div class="yui-g">
<h1>Prize: <? echo $prize['title']; ?></h1><br /><br />

<div class="prize">
<? if ($prize['thumbnail'] != ''): ?>
	<img src="<? echo $prize['thumbnail']; ?>" alt="<? echo $prize['title']; ?>" />
<? endif; ?>

<p>Title: <? echo $prize['title']; ?></p>
<p>Short Name: <? echo $prize['shortName']; ?></p>
<p>Description: <? echo $prize['description']; ?></p>
<p>Thumbnail: <? echo $prize['thumbnail']; ?>
<img src="<?php echo URL_THUMBNAILS .'/'.$prize['thumbnail']; ?>"  /></p>
		
<p>Sponsor: <? echo $prize['sponsor']; ?></p>
<p>SponsorUrl: <? echo $prize['sponsorUrl']; ?></p>
<p>Initial Stock: <? echo $prize['initialStock']; ?></p>
<p>Current Stock: <? echo $prize['currentStock']; ?></p>
<p>Point Cost: <? echo $prize['pointCost']; ?></p>
<!--  <p>Category: <? echo $prize['category']; ?></p>-->
<p>Status: <? echo $prize['status']; ?></p>
<p>Start Date: <? echo $prize['dateStart']; ?></p>
<p>End Date: <? echo $prize['dateEnd']; ?></p>
<p>Eligibility: <? echo $prize['eligibility']; ?></p>
<p>userMaximum: <? echo $prize['userMaximum']; ?></p>
<p>orderFieldsNeeded: <? echo $prize['orderFieldsNeeded']; ?></p>
<p>isGrand: <? echo $prize['isGrand']; ?></p>
<p>isWeekly: <? echo $prize['isWeekly']; ?></p>
<p>isFeatured: <? echo $prize['isFeatured']; ?></p>
<p>dollarValue: <? echo $prize['dollarValue']; ?></p>


</div>

<br /><br />
<?php
$link_list = array(
array('title' => 'Edit', 'ctrl' => 'street_team', 'action' => 'modify_prize', 'id' => $prize['id']),
array('title' => 'Remove', 'ctrl' => 'street_team', 'action' => 'destroy_prize', 'id' => $prize['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
array('title' => 'Return to Prizes', 'ctrl' => 'street_team', 'action' => 'prizes')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>

</div>
