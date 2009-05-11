<div class="yui-g">
<h1>Initialize Cron Jobs</h1>
<?php 
	global $init;
	require_once(PATH_CORE.'/classes/cron.class.php');
	$cObj=new cron($init['apiKey']);
	$cObj->initJobs();
	echo 'Completed';
?>
<div class="spacer"></div><br /><br />
</div>
