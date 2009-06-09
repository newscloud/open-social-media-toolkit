<?php
require_once('BasePod.php');


class GenderDistribution extends BasePod {
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
		$results = $this->model->db->query("SELECT userid, gender from UserCollectives $where_str");

		$users = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$users[] = $row;

		//$users = $results['Results']['data'];
		$totalUsers = 0;
		$actionTypes = array(
			'male'			=> 'numMales',
			'female'		=> 'numFemales',
			'other'			=> 'numOthers',
		);
		$counts = array(
			'numMales'		=> 0,
			'numFemales'		=> 0,
			'numOthers'		=> 0,
		);
		foreach ($users as $user) {
			if (array_key_exists($user['gender'], $actionTypes)) {
				$totalUsers++;
				$counts[$actionTypes[$user['gender']]] += 1;
			}
		}

		$malePer = round($counts['numMales'] / $totalUsers, 2);
		$femalePer = round($counts['numFemales'] / $totalUsers, 2);
		$otherPer = round($counts['numOthers'] / $totalUsers, 2);
		$jsonarr = array('Stats' => array ('Totals' => array()));
		$jsonarr['Stats']['Totals'][] = array('name' => 'numMales', 'count' => $malePer, 'label' => 'Number of Males');
		$jsonarr['Stats']['Totals'][] = array('name' => 'numFemales', 'count' => $femalePer, 'label' => 'Number of Females');
		$jsonarr['Stats']['Totals'][] = array('name' => 'numOthers', 'count' => $otherPer, 'label' => 'Number of Others');
		$jsonarr['Stats']['ActionName'] = "Gender Distributions";
		$jsonarr['Stats']['ChartType'] = 'pie';

		return json_encode($jsonarr);
	}
			
}

?>
