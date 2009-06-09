<div class="yui-g">
Add a Widget to a story<br /><br />


<form method="post" action="<? echo url_for('stories', 'assign_widget', $widget['id']); ?>">
		
<input type="hidden" name="id" value="<?php echo $widget['id']; ?>" />


<p>Assign widgetid <?php echo $id; ?> to story:<br />
<select name="siteContentId">
	<?php 
	$q=$db->query("SELECT siteContentId,title FROM Content ORDER BY siteContentId DESC LIMIT 50;");
	while ($data=$db->readQ($q))	
	{		
		echo '<option value="'.$data->siteContentId.'">'.$data->title.'</option>';
	}
	
	?>  
</select>	     


<div class="spacer"></div>

<input type="submit" value="Assign Widget" />

</form>
</div>
