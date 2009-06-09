<?php
require_once('BasePod.php');


class CityDistribution extends BasePod {
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
		$results = $this->model->db->query("SELECT userid, city from UserCollectives $where_str");
		$users = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$users[] = $row;

		$cities = array();

		$notIdentified = 0;

		$unknown = 0;
		foreach ($users as $user) {
			$city = ucfirst($user['city']);
			if ($city == '') {
				$unknown++;
			} else {
				if (isset($cities[$city]))
					$cities[$city] += 1;
				else
					$cities[$city] = 1;
			}
		}

		$jsonarr = array('Stats' => array ('Totals' => array()));

		$totalCities = count($cities);
		$otherCities = 0;
		foreach ($cities as $city => $count) {
			$city = ucfirst($city);
			//if ($totalCities > 50 && $count < 4) continue;
			if ($totalCities > 100 && $count < 5) {
				$otherCities += $count;
				continue;
			} else if ($totalCities > 50 && $count < 3) {
				$otherCities += $count;
				continue;
			} else if ($totalCities > 30 && $count < 2) {
				$otherCities += $count;
				continue;
			}
			$jsonarr['Stats']['Totals'][] = array('name' => $city, 'count' => $count, 'label' => $city);
		}

		//if ($otherCities > 0)
			//$jsonarr['Stats']['Totals'][] = array('name' => 'Other Cities', 'count' => $otherCities, 'label' => 'Other Cities');

		//$jsonarr['Stats']['Totals'][] = array('name' => 'Unknown', 'count' => $unknown, 'label' => 'Unknown');
		$jsonarr['Stats']['ActionName'] = "City Distributions";
		$jsonarr['Stats']['ChartType'] = 'bar';

		return json_encode($jsonarr);
	}
			
}

?>
