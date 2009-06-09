<?php
require_once('BasePod.php');


class Statistics extends BasePod {

	function __construct($podData) {
		parent::__construct($podData);
	}

	function render_ajax($view = 'pod') {
		//ob_start();
		require('views/statistics/base.php');
		//$data = ob_end_flush();
		//return $data;
		//return "<h1>Statistics Pod: {$this->podData['body']}</h1>";
		return json_encode(array('html' => $html, 'js' => $js));
	}

	function load($view = 'full') {
		return $this->render_ajax($view);
	}
}

?>
