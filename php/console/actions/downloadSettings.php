<div class="yui-g">
<h1>Download settings</h1>
<?php 
	global $init;
					
 	/* initialize the SMT Facebook appliation class, NO Facebook library */
	require_once (PATH_CORE.'/classes/systemStatus.class.php');
	$ssObj=new systemStatus();
	require_once PATH_FACEBOOK."/classes/app.class.php";
	$app=new app(NULL,true);
	$facebook=&$app->loadFacebookLibrary();				
	$propList=array('application_name','authorize_url','base_domain','callback_url','dashboard_url','default_action_fbml','default_column','default_fbml','description','desktop','dev_mode','edit_url','email','help_url','info_changed_url','installable','ip_list','is_mobile','message_action','message_url','post_authorize_redirect_url','post_install_url','privacy_url','private_install','profile_tab_url','publish_action','publish_self_action','publish_self_url','publish_url','see_all_url','tab_default_name','uninstall_url','use_iframe','wide_mode');
	$props=$facebook->api_client->admin_getAppProperties($propList); 
	foreach ($props as $k => $val) {
	    echo "Key: $k; Value: $val<br />";
		$ssObj->setState('fbApp_'.$k,$val);
	}	
	echo 'Completed';
?>
<div class="spacer"></div><br /><br />
</div>
