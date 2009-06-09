<?php
require_once('BaseModel.php');

class OutboundMessages extends BaseModel {
	var $name = 'OutboundMessages';
	var $tablename = 'OutboundMessages';
	var $idname = 'userid';

	function __construct() {
		parent::__construct('hotdish');
	}
}

?>
