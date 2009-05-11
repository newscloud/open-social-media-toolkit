<div class="yui-g">
Place widget in location<br /><br />


<form method="post" 
		action="index.php?p=console&group=stories&action=update_widget_location">
		
		<!-- Widget id below-->
<input type="hidden" name="id" value="<?php echo $id; ?>" />


<p>Assign widgetid <?php echo $id; ?> to location:<br />
<select name="locale">
	<option value="homeFeature">Feature area on cover</option>
	<option value="homeSidebar">Sidebar on cover</option>
</select>	     


<div class="spacer"></div>

<input type="submit" name="replace" value="Save" />
<!-- Place and overwrite in location<input type="submit" name="add" value="Add to location (sidebar only)" />-->

</form>
</div>
