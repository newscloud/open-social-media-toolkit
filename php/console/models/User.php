<?php
require_once('BaseModel.php');

class User extends BaseModel {
	var $name = 'User';
	var $tablename = 'User';
	var $idname = 'userid';

	function __construct() {
		parent::__construct('hotdish');
	}
}

?>
