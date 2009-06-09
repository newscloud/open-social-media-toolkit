<?php
require_once('BaseModel.php');

class SessionLengths extends BaseModel {
	var $name = 'SessionLengths';
	var $tablename = 'SessionLengths';
	var $idname = 'id';

	function __construct() {
		parent::__construct();
	}
}

?>
