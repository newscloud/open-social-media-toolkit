<?php
require_once('BaseModel.php');

class Songs extends BaseModel {
	var $name = 'Songs';
	var $tablename = 'Songs';
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
				//$value = htmlentities($value, ENT_QUOTES);
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

	function fload_item($id, $view, $podData) {
		$fields = $podData['fields'];
		if (!is_numeric($id))
			return false;
		$sql = "SELECT * FROM {$this->tablename} WHERE {$this->idname} = $id";
		$result = mysql_query($sql);
		$jsonarr = array('Results' => array('replyCode' => '201', 'replyText' => 'Data Follows', 'data' => array()));
		$jsonarr['data'] = mysql_fetch_assoc($result);
		$html = '<div class="'.$this->name.'-item">';

		foreach ($jsonarr['data'] as $field => $value)
			if ($fields[$field][$view])
				$html .= "{$fields[$field]['label']}<br /><p>$value</p>";

		$html .= '</div>';

		return array('header' => $this->name.' Item', 'body' => $html, 'footer' => '');
		//return $jsonarr;
	}

}
