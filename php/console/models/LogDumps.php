<?php
require_once('BaseModel.php');

class LogDumps extends BaseModel {
	var $name = 'LogDumps';
	var $tablename = 'LogDumps';
	var $idname = 'id';
	var $dateField = 't';

	function __construct() {
		parent::__construct('hotdish');
	}
}

?>
