<div class="yui-g">
Update a test<br /><br />

<form method="post" action="index.php?p=console&action=update_test">
<input type="hidden" name="test[id]" value="<?php echo $test['id']; ?>" />

<?php require('test_fields.php'); ?>

<div class="spacer"></div>

<input type="submit" value="Update" />

</form>
</div>
