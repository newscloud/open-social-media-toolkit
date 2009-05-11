<div class="yui-g">
<h1>Toggle site status</h1>
<?php 
	global $init;
	require_once(PATH_CORE.'/classes/systemStatus.class.php');
	$ssObj=new systemStatus();
	$siteStatus=$ssObj->getState('siteStatus');
	if ($siteStatus=='offline') 
		$siteStatus='online';
	else 
		$siteStatus='offline';
	$ssObj->setState('siteStatus',$siteStatus);
	echo 'New site status: '.$siteStatus;
	$q=$ssObj->db->query("select email from User where isAdmin=1;");
	while ($data=$ssObj->db->readQ($q)) {
		// Notify the admins		
		mail($data->email, SITE_TITLE.' Site Status: '.$siteStatus, 'Someone just toggled '.SITE_TITLE.' site status. The site is now '.$siteStatus, 'From: support@newscloud.com'."\r\n");		
	}

?>
<div class="spacer"></div><br /><br />
</div>
