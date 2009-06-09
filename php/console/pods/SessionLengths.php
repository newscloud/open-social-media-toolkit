<?php
require_once('BasePod.php');


class SessionLength extends BasePod {
	var $filters = array('dateFilters', 'siteFilters', 'memberFilters');

	function __construct($podData) {
		parent::__construct($podData);
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
			$where[] = "start_session > '".$_REQUEST['startDate']." 00:00:00'";
			$startLabel = date('l jS \of F Y', strtotime($params['startDate']));
		} else {
			$startDate = date("2009-01-01 00:00:00", time());
			$startLabel = date('l jS \of F Y', time());
		}
		if (isset($_REQUEST['endDate'])) {
			$where[] = "end_session < '".$_REQUEST['endDate']." 23:59:59'";
			$endLabel = date('l jS \of F Y', strtotime($params['endDate']));
		} else {
			$endDate = false;
			$endLabel = 'Now';
		}
		if (isset($_REQUEST['siteid']) && $_REQUEST['siteid'] != 0) {
			$where[] = 'UserCollectives.siteid = "'.$_REQUEST['siteid'].'"';
		}
		if (count($where)) {
			$wherestr = ' AND '.join(' AND ', $where);
		} else {
			$wherestr = '';
		}
		$time = date("Y-m-d 00:00:00", time());
		//$results = $this->model->load_all(array("action = 'sessionsHour'", "t like '".date("Y-m-d", time())."%'"), false);
		$results = $this->model->db->query("select avg(session_length) as avg_session_length, DATE_FORMAT(start_session, '%m-%d-%Y') as date from SessionLengths LEFT JOIN UserCollectives ON SessionLengths.userid = UserCollectives.userid where session_length != 0 $wherestr group by date");

		//$stats = $results['Results']['data'];
		$counts = array();
		$f = 0;
		$d = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$counts[$row['date']] = round($row['avg_session_length'] / 60, 2);


		$jsonarr = array('Stats' => array ('Totals' => array()));
		foreach ($counts as $name => $count) {
			$tmp = array();
			$tmp['name'] = $name;
			$tmp['count'] = $count;
			$tmp['label'] = $name . " (minutes)";
			$jsonarr['Stats']['Totals'][] = $tmp;
		}
		$jsonarr['Stats']['ActionName'] = 'Session Length Stats -- Total average: '.(round(array_sum($counts) / count($counts), 2)) . ' Minutes';
		$jsonarr['Stats']['ChartType'] = 'line';
		$jsonarr['Stats']['Where'] = print_r($where, true);
		$jsonarr['Stats']['sql'] = "select avg(session_length) as avg_session_length, DATE_FORMAT(start_session, '%m-%d-%Y') as date from SessionLengths LEFT JOIN UserCollectives ON SessionLengths.userid = UserCollectives.userid where session_length != 0 $wherestr group by date";

		return json_encode($jsonarr);
	}
			
}

?>
