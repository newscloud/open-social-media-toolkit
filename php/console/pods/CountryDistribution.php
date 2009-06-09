<?php
require_once('BasePod.php');


class CountryDistribution extends BasePod {
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
		$results = $this->model->db->query("SELECT userid, country from UserCollectives $where_str");

		$users = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$users[] = $row;

		$countries = array();

		$notIdentified = 0;

		$unknown = 0;
		foreach ($users as $user) {
			if ($user['country'] == '') {
				$unknown++;
			} else {
				if (isset($countries[$user['country']]))
					$countries[$user['country']] += 1;
				else
					$countries[$user['country']] = 1;
			}
		}

		$jsonarr = array('Stats' => array ('Totals' => array()));

		foreach ($countries as $country => $count)
			$jsonarr['Stats']['Totals'][] = array('name' => $country, 'count' => $count, 'label' => $country);

		$jsonarr['Stats']['Totals'][] = array('name' => 'Unknown', 'count' => $unknown, 'label' => 'Unknown');
		$jsonarr['Stats']['ActionName'] = "Country Distributions";
		$jsonarr['Stats']['ChartType'] = 'bar';

		return json_encode($jsonarr);
	}
			
}

?>
