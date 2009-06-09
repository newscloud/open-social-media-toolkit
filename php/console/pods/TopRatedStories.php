<?php
require_once('BasePod.php');


class TopRatedStories extends BasePod {
	var $useButtons = true;
	var $useMemberFilters = true;

	function __construct($podData) {
		$podData['data']['useButtons'] = $this->useButtons;
		$this->numPodRows = 8;
		$this->numFullRows = 50;
		parent::__construct($podData);
		$this->numPodRows = 8;
		$this->numFullRows = 50;
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
		$schema['fields'][] = array('key' => 'score', 'parser' => 'number');
		$schema['fields'][] = array('key' => 'cnt', 'parser' => 'number');
		$schema['labels'][] = array('key' => 'itemid', 'label' => 'ID');
		$schema['labels'][] = array('key' => 'title', 'label' => 'Title');
		$schema['labels'][] = array('key' => 'cnt', 'label' => '# of Votes');
		$schema['labels'][] = array('key' => 'score', 'label' => 'Score');

		$this->currFields = $schema['fields'];
		$this->currLabels = $schema['labels'];

		return json_encode($schema);
	}

	function load($view = 'full') {
		return $this->render_ajax($view);
	}

	function load_all($view = 'full', $params = false) {
		if (!$params) $params = $this->model->params;
		$membersOnly = false;
		$teamEligiblity = false;
		if ($params) {
			if (isset($params['membersOnly'])) {
				$membersOnly = true;
			}
			if (isset($params['teamEligibleOnly'])) {
				$teamEligibility = true;
			}
		}
		if ($params && isset($params['startDate'])) {
			$startDate = $params['startDate'].' 00:00:00';
			$startLabel = date('l jS \of F Y', strtotime($params['startDate']));
		} else {
			$startDate = date("Y-m-d 00:00:00", time());
			$startLabel = date('l jS \of F Y', time());
		}
		if ($params && isset($params['endDate'])) {
			$endDate = $params['endDate'].' 23:59:59';
			$endLabel = date('l jS \of F Y', strtotime($params['endDate']));
		} else {
			$endDate = false;
			$endLabel = 'Now';
		}
		$where = " AND t > '$startDate' ";
		if ($endDate)
			$where .= " AND t < '$endDate'";
		//$results = $this->model->db->query("SELECT itemid,title, count(Log.id) as cnt, Content.score as score FROM Log,Content where Log.itemid=siteContentId AND action='vote' $where GROUP BY itemid ORDER BY cnt DESC");
		$filters = build_filters_from_params($this->model, $params);
		$wheref = '';
		if (count($filters))
			$wheref = ' AND '.join(' AND ', $filters);
		if ($wheref == '')
			$limit = " LIMIT 5";
		//$results = $this->model->db->query("SELECT itemid,title, count(Log.id) as cnt, Content.score as score FROM Log,Content where Log.itemid=siteContentId AND action='vote' $wheref GROUP BY itemid ORDER BY cnt DESC $limit");
		$results = $this->db->query("SELECT itemid,title, count(Log.id) as cnt, Content.score as score FROM Log,Content where Log.itemid=siteContentId AND action='vote' $wheref GROUP BY itemid ORDER BY cnt DESC $limit");
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
				'cnt' => $story['cnt'],
				'score' => $story['score']
			);
			$jsonarr['Results']['data'][] = $tmp;
		}
		$jsonarr['Stats']['Filters'] = print_r($filters, true);
		$jsonarr['Stats']['Params'] = print_r($params, true);
		
		return json_encode($jsonarr);
	}

	function load_statistics($view = 'pod') {
		$filters = build_filters_from_params($this->model, $_REQUEST);
		if (count($filters))
			$wheref = ' AND '.join(' AND ', $filters);
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
		$jsonarr['Stats']['ActionName'] = 'Top Rated Stories';
		$jsonarr['Stats']['ChartType'] = 'line';

		return json_encode($jsonarr);
	}
			
}

?>
