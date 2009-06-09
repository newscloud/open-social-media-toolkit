<?php

Class FacebookController extends AppController {
	var $name = 'Facebook';
	public function registerfeedtemplates() {
		global $init;
		include_once PATH_FACEBOOK.'/lib/facebook.php';
		$facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);
		require_once (PATH_FACEBOOK.'/classes/profileBoxes.class.php');
		$proObj=new profileBoxes($this->db);
		$proObj->loadFacebook($facebook);
		$proObj->registerFeedTemplates();
		set_flash(array('notice' => "Path templates (".PATH_TEMPLATES."). Finished registering feed templates."));
		redirect(url_for($this->name, 'index'));
	}

	public function deletefeedtemplates() {
		global $init;
		include_once PATH_FACEBOOK.'/lib/facebook.php';
		$facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);
		require_once (PATH_FACEBOOK.'/classes/profileBoxes.class.php');
		$proObj=new profileBoxes($this->db);
		$proObj->loadFacebook($facebook);
		$proObj->deleteFeedTemplates();
		set_flash(array('notice' => "Path templates (".PATH_TEMPLATES."). Finished deleting feed templates."));
		redirect(url_for($this->name, 'index'));
	}

	public function initprofilebox() {
		global $init;
		include_once PATH_FACEBOOK.'/lib/facebook.php';
		$facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);
		require_once (PATH_FACEBOOK.'/classes/profileBoxes.class.php');
		$proObj=new profileBoxes($this->db);
		$proObj->loadFacebook($facebook);
		$proObj->initRefHandle();
		set_flash(array('notice' => "Finished initializing profile box."));
		redirect(url_for($this->name, 'index'));
	}

	public function downloadsettings() {
		global $init;
						
		/* initialize the SMT Facebook appliation class, NO Facebook library */
		require_once (PATH_CORE.'/classes/systemStatus.class.php');
		$ssObj=new systemStatus();
		require_once PATH_FACEBOOK."/classes/app.class.php";
		$app=new app(NULL,true);
		$facebook=&$app->loadFacebookLibrary();             
//		'default_action_fbml','default_fbml','post_install_url',
			$propList=array('application_name','authorize_url','base_domain','callback_url','dashboard_url','default_column','description','desktop','dev_mode','edit_url','email','help_url','info_changed_url','installable','ip_list','is_mobile','message_action','message_url','post_authorize_redirect_url','privacy_url','private_install','profile_tab_url','publish_action','publish_self_action','publish_self_url','publish_url','see_all_url','tab_default_name','uninstall_url','use_iframe','wide_mode');
		$props=$facebook->api_client->admin_getAppProperties($propList); 
		$this->set('props', $props);
		$this->set('ssObj', $ssObj);

		$this->render();
	}

	public function syncallocations() {
		global $init;                                                                        
		require_once(PATH_CORE.'/classes/cron.class.php');                                   
		$cObj=new cron($init['apiKey']);
		$cObj->forceJob('facebookAllocations');                                              
		echo 'Completed';                                                                    
		set_flash(array('notice' => "Finished synching allocations"));
		redirect(url_for($this->name, 'index'));
	}

	public function index() {
		$this->render();
	}

}

?>
