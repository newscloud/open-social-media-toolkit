<div class="yui-g">
<h1>Sync Facebook Allocations</h1>
<?php 
	global $init;
	require_once(PATH_CORE.'/classes/cron.class.php');
	$cObj=new cron($init['apiKey']);
	$cObj->forceJob('facebookAllocations');
	echo 'Completed';
?>
<div class="spacer"></div><br /><br />
</div>
