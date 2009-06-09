<?php

Class AdminController extends AppController {
	var $name = 'Admin';
	public function cronjobs() {
				$cronJobs = $this->db->load_all();
				$this->set('cronJobs', $cronJobs);
				$this->render();
	}

	public function run_cronjob() {
		global $init;
		require_once(PATH_CORE.'/classes/cron.class.php');
		$cObj=new cron($init['apiKey']);
		if (isset($_GET['task'])) {
			$task = $_GET['task'];
			$cObj->forceJob($task);
			set_flash(array('notice' => "Cron job ($task) completed."));
			redirect(url_for($this->name, 'cronjobs'));
		} else {
			set_flash(array('error' => "Invalid cron job -- aborted."));
			redirect(url_for($this->name, 'cronjobs'));
		}
	}

	public function initcronjobs() {
		global $init;
		require_once(PATH_CORE.'/classes/cron.class.php');                                   
		$cObj=new cron($init['apiKey']);
		$cObj->initJobs();
		set_flash(array('notice' => "Finished initializing cron jobs."));
		redirect(url_for($this->name, 'cronjobs'));
	}

	public function cloud_properties() {
				$this->render('not_implemented');
	}

	public function database() {
				$this->render('not_implemented');
	}

	public function feed_list() {
				$this->render('not_implemented');
	}

	public function index() {
				$this->render();
	}

	public function flushfeeds() {
		global $init;
		require_once(PATH_CORE.'/classes/newswire.class.php');
		$nwObj=new newswire();
		$nwObj->cleanup(0);
		set_flash(array('notice' => "Finished flushing feeds."));
		redirect(url_for($this->name, 'index'));
	}

	public function sitestatus() {
		global $init;
		require_once(PATH_CORE.'/classes/systemStatus.class.php');
		$ssObj=new systemStatus();
		$siteStatus=$ssObj->getState('siteStatus');
		if ($siteStatus=='offline')
			$siteStatus='online';
		else 
			$siteStatus='offline';
		$ssObj->setState('siteStatus',$siteStatus);
		$q=$ssObj->db->query("select email from User where isAdmin=1;");
		while ($data=$ssObj->db->readQ($q)) {
			// Notify the admins        
			mail($data->email, SITE_TITLE.' Site Status: '.$siteStatus, 'Someone just toggled '.SITE_TITLE.' site status. The site is now '.$siteStatus, 'From: support@newscloud.com'."\r\n");     
		}
		set_flash(array('notice' => "Site Status Toggled! New site status is $siteStatus"));
		redirect(url_for($this->name, 'index'));
	}

	public function insert_survey_monkey_data() {
		require_once(PATH_CORE.'/classes/researchSurveyMonkey.class.php');
		$surveyMonkeyTable = new SurveyMonkeyTable();

		$result = $surveyMonkeyTable->loadCSV();

		if (substr($result, 0, 5) == 'ERROR') {
			set_flash(array('error' => $result));
		} else {
			set_flash(array('notice' => $result));
		}

		redirect(url_for($this->name, 'index'));
	}

}

?>
