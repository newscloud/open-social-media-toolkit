<?php
/* ::TODO:: CHANGE THIS */
//require_once('../db.php');
require_once('/var/www/grist/db.php');


class BaseModel {
	var $name = '';
	var $tablename = '';
	var $idname = 'id';
	var $dateField = 'date';
	var $db;
	var $params;

	function __construct($dbname = false) {
		$this->db = new DB($dbname);
		$this->params = $_REQUEST;
	}

	function load_all($conditions = false, $retjson = true, $limit = 50) {
		if (!is_numeric($limit)) {
			$limit = 50;
			$limitstr = "LIMIT $limit";
		} else if ($limit == 0) {
			$limitstr = '';
		} else {
			$limitstr = "LIMIT $limit";
		}
		$where = '';
		if (is_array($conditions) && count($conditions)) {
			$where = 'WHERE '.join(' AND ', $conditions);
		} else if (is_string($conditions) && strlen($conditions)) {
			$where = "WHERE $conditions";
		}

		$sql = "SELECT * FROM {$this->tablename} {$where} ORDER BY {$this->idname} ASC {$limitstr}";
		$songs = $this->db->query($sql);
		$json = '{"Results": {';
		$json .= '"replyCode": "201", "replyText": "Data Follows", "data":[';
		$jsonarr = array('Results' => array('replyCode' => '201', 'replyText' => 'Data Follows', 'data' => array()));
		while (($song = mysql_fetch_assoc($songs)) !== false) {
			$tmp = array();
			foreach ($song as $field => $value) {
				$tmp[$field] = $value;
			}
			$jsonarr['Results']['data'][] = $tmp;
		}
		$json = substr($json, 0, -1);
		$json .= ']} }';
		//return $json;
		return ($retjson) ? json_encode($jsonarr) : $jsonarr;
	}

	function load_item($id, $view, $podData) {
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

?>
