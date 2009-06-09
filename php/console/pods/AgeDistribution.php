<?php
require_once('BasePod.php');


class AgeDistribution extends BasePod {
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
		$results = $this->model->db->query("SELECT userid, age from UserCollectives $where_str");
		$users = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$users[] = $row;

		//$users = $results['Results']['data'];
		$totalUsers = 0;
		$ages = array(
			'under16' 	=> 0,
			'under18'	=> 0,
			'between18_25'	=> 0,
			'between26_40'	=> 0,
			'over40'	=> 0,
		);

		$notIdentified = 0;

		foreach ($users as $user) {
			switch (true) {
				case ($user['age'] == 0):
					$notIdentified++;
				break;
				case ($user['age'] < 16):
					$ages['under16']++;
				break;
				case ($user['age'] < 18):
					$ages['under18']++;
				break;
				case ($user['age'] < 26):
					$ages['between18_25']++;
				break;
				case ($user['age'] < 40):
					$ages['between26_40']++;
				break;
				case ($user['age'] >= 40):
					$ages['over40']++;
				break;
				default:
				break;
			}
		}

		$jsonarr = array('Stats' => array ('Totals' => array()));
		foreach ($ages as $name => $count) {
			preg_match('/^([^0-9]+)([0-9]{2})_?([0-9]{2})?/', $name, $match);
			$label = ucfirst($match[1]).' '.$match[2];
			if (isset($match[3]) && $match[3] != '')
				$label .= ' and '.$match[3];
			$jsonarr['Stats']['Totals'][] = array('name' => $name, 'count' => $count, 'label' => $label);
		}
		$jsonarr['Stats']['Totals'][] = array('name' => 'notIdentified', 'count' => $notIdentified, 'label' => 'No age given');
		$jsonarr['Stats']['ActionName'] = "Age Distributions";
		$jsonarr['Stats']['ChartType'] = 'bar';

		return json_encode($jsonarr);
	}
			
}

?>
