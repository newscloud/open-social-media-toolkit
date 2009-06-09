<div class="yui-g">
Assign a prize to winner(s)<br /><br />

<?php 


echo $userids;
?>		
<br/>


<form method="post" action="<? echo url_for('street_team', 'award_prize'); ?>">
		
		
<input type="hidden" name="userids" value="<?php echo $userids; ?>" />



<select name="prizeid">
	<?php 
	
	require_once(PATH_CORE .'/classes/prizes.class.php');
	$pt = new PrizeTable(); // so I get my own db connection
	
	if (isset($_GET['mode']))
		$mode = $_GET['mode'];
	else
		$mode = 'all';
	
	switch($mode)
	{	
		case 'weekly': $where = 'isWeekly=1'; break;
		case 'grand': $where = 'isGrand>0'; break;
		default: $where='1';
	}
		
	$prize = $pt->getRowObject();	  
	$prizes = $pt->getPrizeList($where,'dateEnd');
	//$prizes['test']=100;
	foreach ($prizes as $id => $title)
	{
		$prize->load($id);
		echo "<option value='$prize->id'>$prize->title - $prize->dateEnd</option>";
		
	}
	
	?>  
</select>	     


<div class="spacer"></div>

<input type="submit" value="Award" />

</form>
</div>
