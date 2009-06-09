<?php
require_once('BasePod.php');


class InterestDistribution extends BasePod {
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
		if ($params) {
			if (isset($params['membersOnly'])) {
				$where[] = 'isMember = 1';
			}
			if (isset($params['teamEligibleOnly'])) {
				$where[] = 'eligibility = "team"';
			}
			if (isset($params['siteid']) && $params['siteid'] != 0) {
				$where[] = 'siteid = "'.$params['siteid'].'"';
			}
		}
		if (count($where))
			$where_str = ' WHERE '.join(' AND ', $where);
		$this->model->db->selectdb('research');
		$results = $this->model->db->query("SELECT userid, researchImportance from UserCollectives $where_str");
		$users = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$users[] = $row;

		$totalUsers = 0;
		$interestLevels = array(0=>'How interested are you in climate change issues?',1=>'Extremely uninterested',2=>'Mostly uninterested',3=>'Somewhat uninterested',4=>'Neither interested nor uninterested',5=>'Somewhat interested',6=>'Mostly interested',7=>'Extremely interested');

		$interestCounts = array();
		for ($i = 0; $i < 8; $i++) $interestCounts[$i] = 0;
		foreach ($users as $user) {
			$interestCounts[$user['researchImportance']] += 1;
		}

		$jsonarr = array('Stats' => array ('Totals' => array()));

		for ($i = 0; $i < count($interestCounts); $i++) {
			if ($i == 0) continue;
			$jsonarr['Stats']['Totals'][] = array('name' => $interestLevels[$i], 'count' => $interestCounts[$i], 'label' => $interestLevels[$i]);
		}


		$jsonarr['Stats']['ActionName'] = "Research Question Interest Level";
		$jsonarr['Stats']['ChartType'] = 'pie';

		return json_encode($jsonarr);
	}
			
}

?>
