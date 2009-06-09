<?php
require_once('BasePod.php');


class SessionStatistics extends BasePod {
	var $useButtons = true;
	var $useMemberFilters = true;
	var $useSiteFilters = true;

	function __construct($podData) {
		//$podData['data']['useButtons'] = $this->useButtons;
		parent::__construct($podData);
		$this->model->db->selectdb('research');
	}

	function render_ajax($view = 'pod') {
		//ob_start();
		require('views/statistics/base.php');
		//$data = ob_end_flush();
		//return $data;
		//return "<h1>Statistics Pod: {$this->podData['body']}</h1>";
		return json_encode(array('html' => $html, 'js' => $js));
	}

	function load($view = 'full') {
		return $this->render_ajax($view);
	}

	function load_statistics($view = 'pod') {
		$where = array();
		if (isset($_REQUEST['membersOnly'])) {
			$where[] = 'UserCollectives.isMember = 1';
		}
		if (isset($_REQUEST['teamEligibleOnly'])) {
			$where[] = "UserCollectives.optInStudy = 1 AND UserCollectives.eligibility = 'team'";
		}
		if (isset($_REQUEST['startDate'])) {
			$where[] = "t > '".$_REQUEST['startDate']." 00:00:00'";
			$startLabel = date('l jS \of F Y', strtotime($params['startDate']));
		} else {
			$where[] = "t > '".date("Y-m-d 00:00:00", time())."'";
			$startLabel = date('l jS \of F Y', time());
		}
		if (isset($_REQUEST['endDate'])) {
			$where[] = "t < '".$_REQUEST['endDate']." 23:59:59'";
			$endLabel = date('l jS \of F Y', strtotime($params['endDate']));
		} else {
			$endDate = false;
			$endLabel = 'Now';
		}
		if (isset($_REQUEST['siteid']) && $_REQUEST['siteid'] != 0) {
			$where[] = 'LogDumps.siteid = "'.$_REQUEST['siteid'].'"';
		}
		if (count($where)) {
			$wherestr = ' AND '.join(' AND ', $where);
		} else {
			$wherestr = '';
		}



		$time = date("Y-m-d 00:00:00", time());
		//$results = $this->model->load_all(array("action = 'sessionsHour'", "t like '".date("Y-m-d", time())."%'"), false);
		$results = $this->model->db->query("SELECT LogDumps.id, LogDumps.t, LogDumps.itemid FROM LogDumps LEFT JOIN UserCollectives ON LogDumps.userid1 = UserCollectives.userid WHERE action = 'sessionsHour' $wherestr ORDER BY LogDumps.t ASC");

		$stats = $results['Results']['data'];
		$counts = array();
		//foreach ($stats as $stat) {
		while (($stat = mysql_fetch_assoc($results)) !== false) {
			preg_match('/^[0-9]{4}-([0-9]{2}-[0-9]{2}) (([0-9]{2}):[0-9]{2}):[0-9]{2}$/', $stat['t'], $match);
			if ($match[3] == '00') {
				$logtime = "{$match[1]} {$match[3]}:00";
			} else {
				$logtime = "{$match[1]} {$match[3]}:00";
				//$logtime = $match[2];
			}
			//$logtime = preg_replace('/^[0-9]{4}-[0-9]{2}-[0-9]{2} ([0-9]{2}:[0-9]{2}):[0-9]{2}$/', '$1', $stat['t']);
			if (!isset($counts[$logtime]))
				$counts[$logtime] = $stat['itemid'];
			else
				$counts[$logtime] += $stat['itemid'];
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
		$jsonarr['Stats']['total'] = count($counts);

		return json_encode($jsonarr);
	}
			
}

?>
