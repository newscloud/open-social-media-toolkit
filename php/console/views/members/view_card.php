<div class="yui-g">
<div class="card">
<h2>Card Details: <? echo $card['slug']; ?></h2>

<p id="card[name]">Short Caption:<br /> <?php echo htmlentities($card['name']); ?></p>
<div class="spacer"></div>

<p id="card[shortCaption]">Short Caption:<br /> <?php echo htmlentities($card['shortCaption']); ?></p>
<div class="spacer"></div>

<p id="card[longCaption]">Long Caption:<br /> <?php echo htmlentities($card['longCaption']); ?></p>
<div class="spacer"></div>

<p id="card[dateAvailable]">Width: <?php echo htmlentities($card['dateAvailable']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new card', 'ctrl' => 'members', 'action' => 'new_card'),
	array('title' => 'Edit this card', 'ctrl' => 'members', 'action' => 'modify_card', 'id' => $card['id']),
	array('title' => 'Delete this card', 'ctrl' => 'members', 'action' => 'destroy_card', 'id' => $card['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
   array('title' => 'Back to cards', 'ctrl' => 'members', 'action' => 'cards')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>
</div>
