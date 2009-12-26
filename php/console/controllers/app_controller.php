<?php

class AppController {
	var $name;
	var $params;
	var $filters = false;
	var $db;
	var $render_vars;
	var $helpers = array('application');
	var $skip_render = false;

	function __construct() {
		global $params;
		global $db;
		global $controller_name;
		global $action_name;
		$this->params = $params;
		$this->action = $params['action'];
		$this->db = $db;
		$controller_name = strtolower($this->name);
		$action_name = $this->action;
		$this->render_vars = array();

		// Run before_filter
		$this->before_filter();
	}

	function render($view = false) {
		if (!$view)
			$view = $this->action;

		if (!$this->skip_render) {
			// Set some local view variables
			$this->set_class_vars();
			// Load helper files
			foreach ($this->helpers as $helper) {
				$file = PATH_CONSOLE.'/helpers/'.$helper.'_helper.php';
				require_once($file);
			}

			if (file_exists(PATH_CONSOLE.'/views/'.strtolower($this->name).'/'.$view.'.php')) {
				foreach ($this->render_vars as $name => $value)
					$$name = $value;
				disp_header();
				require(PATH_CONSOLE.'/views/'.strtolower($this->name).'/'.$view.'.php');
				disp_footer();
			} else {
				set_flash(array('error' => 'Action does not exist.'));
				redirect(url_for($BASE_URL)); // $this->name
			}
		}

		// Run after filter
		$this->after_filter();
	}

	protected function set($name, &$value) {
		$this->render_vars[$name] = $value;
	}

	private function set_class_vars() {
		$this->set('controller_name', strtolower($this->name));
		$this->set('action_name', $this->action);
	}

	protected function before_filter() {
	}

	protected function after_filter() {
	}

}

?>
