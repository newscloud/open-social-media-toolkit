<div class="yui-g">
Update a prize<br /><br />

<form method="post" enctype="multipart/form-data" action="<? echo url_for('street_team', 'update_prize', $prize['id']); ?>">
<input type="hidden" name="prize[id]" value="<?php echo $prize['id']; ?>" />

<?php require('prize_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
