<?php
require_once('BasePod.php');


class EmailDistribution extends BasePod {
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
		$results = $this->model->db->query("SELECT userid, email from UserCollectives $where_str");
		$users = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$users[] = $row;

		$unknown = 0;
		foreach ($users as $user) {
			if ($user['email'] == '') {
				$unknown++;
			} else {
				preg_match('/@(.*)$/', $user['email'], $match);
				if ($match[1] != '') {
					$domain = $match[1];
					if (isset($emails[$domain]))
						$emails[$domain] += 1;
					else
						$emails[$domain] = 1;
				} else {
					$unknown++;
				}
			}
		}

		$jsonarr = array('Stats' => array ('Totals' => array()));

		$totalUsers = count($users);
		foreach ($emails as $email => $count) {
			if ($totalUsers > 100 && $count < 5) continue;
			$jsonarr['Stats']['Totals'][] = array('name' => $email, 'count' => $count, 'label' => $email);
		}

		$jsonarr['Stats']['Totals'][] = array('name' => 'Unknown', 'count' => $unknown, 'label' => 'Unknown');
		$jsonarr['Stats']['ActionName'] = "Email Distributions";
		$jsonarr['Stats']['ChartType'] = 'bar';

		return json_encode($jsonarr);
	}
			
}

?>
