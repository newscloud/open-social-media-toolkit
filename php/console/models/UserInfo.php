<?php
require_once('BaseModel.php');

class UserInfo extends BaseModel {
	var $name = 'UserInfo';
	var $tablename = 'UserInfo';
	var $idname = 'userid';

	function __construct() {
		parent::__construct('hotdish');
	}
}

?>
