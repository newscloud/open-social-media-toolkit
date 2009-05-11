<div class="yui-g">
Update a prize<br /><br />

<form method="post" enctype="multipart/form-data" 
		action="index.php?p=console&group=street_team&action=update_prize">
<input type="hidden" name="prize[id]" value="<?php echo $prize['id']; ?>" />

<?php require('prize_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
