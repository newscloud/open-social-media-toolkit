<div class="yui-g">
<h1>Upload settings</h1>
<?php 
	global $init;
					
 	/* initialize the SMT Facebook appliation class, NO Facebook library */
	require_once (PATH_CORE.'/classes/systemStatus.class.php');
	$ssObj=new systemStatus();
	$propList=$ssObj->loadFacebookProperties();
	var_dump($propList);
	exit;
	require_once PATH_FACEBOOK."/classes/app.class.php";
	$app=new app(NULL,true);
	$facebook=&$app->loadFacebookLibrary();				
	$props=$facebook->api_client->admin_setAppProperties($propList); 
	echo 'Completed';
?>
<div class="spacer"></div><br /><br />
</div>
