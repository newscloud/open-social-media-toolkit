<div class="yui-g">
Update a Card<br /><br />

<form method="post" action="<? echo url_for('members', 'update_card', $card['id']); ?>">
<input type="hidden" name="card[id]" value="<?php echo $card['id']; ?>" />
<input type="hidden" name="id" value="<?php echo $card['id']; ?>" />

<?php require('fields_card.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
