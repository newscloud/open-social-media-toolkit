<div class="yui-g">
<h1>Flush the Newswire table</h1>
<?php 
	global $init;
	require_once(PATH_CORE.'/classes/newswire.class.php');
	$nwObj=new newswire();
	$nwObj->cleanup(0);
	echo 'Completed';
?>
<div class="spacer"></div><br /><br />
</div>
