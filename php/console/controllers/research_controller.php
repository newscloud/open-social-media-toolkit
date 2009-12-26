<?php

Class ResearchController extends AppController {
	var $name = 'Research';

	public function index() {
				$this->render();
	}

	public function export_users() {
		global $init;
		require_once (PATH_CORE.'/classes/research.class.php');
		$rObj=new research();
		$str=$rObj->exportUsers();
		set_flash(array('notice' => $str));
		redirect(url_for($this->name, 'index'));
	}

	public function export_stories() {
		global $init;
		require_once (PATH_CORE.'/classes/research.class.php');
		$rObj=new research();
		$str=$rObj->exportStories();
		set_flash(array('notice' => $str));
		redirect(url_for($this->name, 'index'));
	}

	public function export_challenges() {
		global $init;
		require_once (PATH_CORE.'/classes/research.class.php');
		$rObj=new research();
		$str=$rObj->exportChallenges();
		set_flash(array('notice' => $str));
		redirect(url_for($this->name, 'index'));
	}
	
}

?>
