<div class="yui-g">
<h1>Run Cron Job</h1>
<?php 
	global $init;
	require_once(PATH_CORE.'/classes/cron.class.php');
	$cObj=new cron($init['apiKey']);
	if (isset($_GET['task'])) {
		$task = $_GET['task'];
		$cObj->forceJob($task);
		echo 'Completed';
	} else {
		echo 'job invalid - aborted';	
	}
?>
<div class="spacer"></div><br /><br />
</div>
