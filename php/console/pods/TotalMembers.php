<?php
require_once('BasePod.php');


class TotalMembers extends BasePod {
	var $useButtons = false;
	var $useMemberFilters = true;
	var $useSiteFilters = true;

	function __construct($podData) {
		$podData['data']['useButtons'] = $this->useButtons;
		parent::__construct($podData);
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

	function load_statistics($view = 'pod', $params = false) {
		$where = array();
		$where_str = '';
		$where_sql = '';
		if ($params) {
			if (isset($params['membersOnly'])) {
				$where[] = 'isMember = 1';
			}
			if (isset($params['teamEligibleOnly'])) {
				$where[] = 'eligibility = "team"';
			}
			if (isset($params['siteid']) && $params['siteid'] != 0) {
				$where[] = 'siteid = "'.$params['siteid'].'"';
				$where_sql = ' AND siteid = "'.$params['siteid'].'"';
			}
		}
		if (count($where))
			$where_str = ' WHERE '.join(' AND ', $where);
		$this->model->db->selectdb('research');
		$member_sql = " SELECT 
			(SELECT count(1) FROM UserCollectives WHERE isMember = 0 $where_sql) as numVisitors,
			(SELECT count(1) FROM UserCollectives WHERE isMember = 1 $where_sql) as numMembers,
			(SELECT count(1) FROM UserCollectives WHERE optInStudy = 1 AND eligibility = 'team' AND ((rxConsentForm=1 AND age<18) OR age>17) $where_sql) as numInsideStudy
		";
		$results = $this->model->db->query($member_sql);
		$member_stats = array();
		if (($row = mysql_fetch_assoc($results)) !== false)
			$member_stats = $row;

		$jsonarr = array('Stats' => array ('Totals' => array()));

		$jsonarr['Stats']['Totals'][] = array('name' => 'numVisitors', 'count' => $member_stats['numVisitors'], 'label' => 'Number of Visitors');
		$jsonarr['Stats']['Totals'][] = array('name' => 'numMembers', 'count' => $member_stats['numMembers'], 'label' => 'Number of Members');
		$jsonarr['Stats']['Totals'][] = array('name' => 'numInsideStudy', 'count' => $member_stats['numInsideStudy'], 'label' => 'Number of Study Group Members');

		$jsonarr['Stats']['ActionName'] = "Total Members";
		$jsonarr['Stats']['ChartType'] = 'bar';
		$jsonarr['Stats']['results'] = print_r($member_stats, true);
		$jsonarr['Stats']['sql'] = $member_sql;

		return json_encode($jsonarr);
	}
			
}

?>
