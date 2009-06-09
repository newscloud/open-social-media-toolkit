<?php
require_once('BaseModel.php');

class Log extends BaseModel {
	var $name = 'Log';
	var $tablename = 'Log';
	var $idname = 'id';
	var $dateField = 't';

	function __construct() {
		parent::__construct('hotdish');
	}
}

?>
