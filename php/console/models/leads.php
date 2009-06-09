<?php
require_once('BaseModel.php');

class leads extends BaseModel {
	var $name = 'leads';
	var $tablename = 'leads';
	var $idname = 'id';

	function __construct() {
		parent::__construct();
		//$this->db = mysql_connect('localhost', 'russell', '1234qwer');
		//mysql_select_db('russell');
	}

	function fload_all() {
		$sql = "SELECT * FROM {$this->tablename} ORDER BY id ASC";
		$songs = mysql_query($sql);
		$json = '{"Results": {';
		$json .= '"replyCode": "201", "replyText": "Data Follows", "data":[';
		$jsonarr = array('Results' => array('replyCode' => '201', 'replyText' => 'Data Follows', 'data' => array()));
		while (($song = mysql_fetch_assoc($songs)) !== false) {
			$json .= '{';
			$tmp = array();
			foreach ($song as $field => $value) {
				$tmp[$field] = $value;
				//$jsonarr['Results']['data'][$field] = $value;
				//$value = rawurlencode($value);
				$json .= "\"$field\": \"$value\",";
			}
			$jsonarr['Results']['data'][] = $tmp;
			$json = substr($json, 0, -1);
			$json .= '},';
			//$json .= sprintf('{"id": "%s", "title": "%s", "artist": "%s", "desired": "%s", "downloaded": "%s"},', $song['id'], $song['title'], $song['artist'], $song['desired'], $song['downloaded']);
		}
		$json = substr($json, 0, -1);
		$json .= ']} }';
		//return $json;
		return json_encode($jsonarr);
	}
}
