<?php
require_once('BasePod.php');


class MostShared extends BasePod {
	var $useButtons = false;

	function __construct($podData) {
		$podData['data']['useButtons'] = $this->useButtons;
		parent::__construct($podData);
		$schema = $this->load_schema();
	}

	function render_ajax($view = 'pod') {
		//ob_start();
		require('views/statistics/base.php');
		//$data = ob_end_flush();
		//return $data;
		//return "<h1>Statistics Pod: {$this->podData['body']}</h1>";
		return json_encode(array('html' => $html, 'js' => $js));
	}

	function load_schema($view = 'pod') {
		$schema = array('fields' => array(), 'labels' => array(), 'datasource' => "{$this->BASE_URL}&pod={$this->id}&pod_action=load_all&view=$view", 'numRows' => 50);
		$schema['fields'][] = 'itemid';
		$schema['fields'][] = 'title';
		$schema['fields'][] = array('key' => 'cnt', 'parser' => 'number');
		$schema['labels'][] = array('key' => 'itemid', 'label' => 'ID');
		$schema['labels'][] = array('key' => 'title', 'label' => 'Title');
		$schema['labels'][] = array('key' => 'cnt', 'label' => '# of Times Shared');

		$this->currFields = $schema['fields'];
		$this->currLabels = $schema['labels'];

		return json_encode($schema);
	}

	function load($view = 'full') {
		return $this->render_ajax($view);
	}

	function load_all($view = 'full', $params = false) {
		//$results = $this->model->db->query("select itemid2 as itemid, count(Log.id) as cnt,title from Log,Content where Log.itemid2=siteContentId AND action='comment' AND t>0 GROUP BY itemid2 ORDER BY cnt DESC");
		$results = $this->db->query("select itemid2 as itemid, count(Log.id) as cnt,title from Log,Content where Log.itemid2=siteContentId AND action='comment' AND t>0 GROUP BY itemid2 ORDER BY cnt DESC");

		$stories = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$stories[] = $row;
		$jsonarr = array('Results' => array('replyCode' => '201', 'replyText' => 'Data Follows', 'data' => array()));
		foreach ($stories as $story) {
			$baseurl = 'http://apps.facebook.com/hotdish/';
			$title = sprintf("<a href=\"%s?p=read&cid=%s\">%s</a>", $baseurl, $story['itemid'], $story['title']);
			$tmp = array(
				'itemid' => $story['itemid'],
				'title' => $title,
				'cnt' => $story['cnt']
			);
			$jsonarr['Results']['data'][] = $tmp;
		}
		
		return json_encode($jsonarr);
	}

	function load_statistics($view = 'pod') {
		$time = date("Y-m-d 00:00:00", time());
		$results = $this->model->load_all(array("action = 'sessionsHour'", "t like '".date("Y-m-d", time())."%'"), false);

		$stats = $results['Results']['data'];
		$counts = array();
		foreach ($stats as $stat) {
			$logtime = preg_replace('/^[0-9]{4}-[0-9]{2}-[0-9]{2} ([0-9]{2}:[0-9]{2}):[0-9]{2}$/', '$1', $stat['t']);
			$counts[$logtime] = $stat['itemid'];
		}


		$jsonarr = array('Stats' => array ('Totals' => array()));
		foreach ($counts as $name => $count) {
			$tmp = array();
			$tmp['name'] = $name;
			$tmp['count'] = $count;
			$tmp['label'] = $name;
			$jsonarr['Stats']['Totals'][] = $tmp;
		}
		$jsonarr['Stats']['ActionName'] = 'Session Stats';
		$jsonarr['Stats']['ChartType'] = 'line';

		return json_encode($jsonarr);
	}
			
}

?>
