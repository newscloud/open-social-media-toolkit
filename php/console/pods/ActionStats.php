<?php
require_once('BasePod.php');


class ActionStats extends BasePod {
	var $useButtons = true;
	var $useMemberFilters = true;
	var $useSiteFilters = true;

	function __construct($podData) {
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

	function load_statistics($view = 'pod', $params = false) {
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
			$where[] = 'UserCollectives.siteid = "'.$_REQUEST['siteid'].'"';
		}
		if (count($where)) {
			$wherestr = ' WHERE '.join(' AND ', $where);
		} else {
			$wherestr = '';
		}

		$results = $this->model->db->query("SELECT LogDumps.id, action FROM LogDumps LEFT JOIN UserCollectives ON LogDumps.userid1 = UserCollectives.userid $wherestr");

		$totalActions = 0;
		$actionTypes = array(
			'vote'			=> 'numVotes',
			'comment'		=> 'numComments',
			'shareStory'		=> 'numSharedStories',
			'postStory'		=> 'numPostedStories',
			'publishWire'		=> 'numPublishedWireStories',
			'readStory'		=> 'numReadStories',
			'completedChallenge'	=> 'numCompletedChallenges',
			'chatStory'		=> 'numChatStories',
			'postBlog'		=> 'numPostedBlogs'
		);
		$counts = array(
			'numVotes'		=> 0,
			'numComments'		=> 0,
			'numSharedStories'	=> 0,
			'numPostedStories'	=> 0,
			'numPublishedWireStories'	=> 0,
			'numReadStories'	=> 0,
			'numCompletedChallenges'	=> 0,
			'numChatStories'	=> 0,
			'numPostedBlogs'	=> 0,
		);
		//foreach ($stats as $stat) {
		while (($stat = mysql_fetch_assoc($results)) !== false) {
			if (array_key_exists($stat['action'], $actionTypes)) {
				$totalActions++;
				$counts[$actionTypes[$stat['action']]] += 1;
			}
		}

		$jsonarr = array('Stats' => array ('Totals' => array()));
		foreach ($counts as $name => $count) {
			$tmp = array();
			$tmp['name'] = $name;
			$tmp['count'] = $count;
			$tmp['label'] = $name;
			$jsonarr['Stats']['Totals'][] = $tmp;
		}
		$jsonarr['Stats']['ActionName'] = "Action Stats $startLabel -- $endLabel";
		$jsonarr['Stats']['ChartType'] = 'pie';
		//$jsonarr['Stats']['UserCounts'] = count($userData);
		$jsonarr['Stats']['TotalActions'] = $totalActions;
		//$jsonarr['Stats']['Counts'] = print_r($userData, true);
		$jsonarr['Stats']['where'] = $wherestr;

		return json_encode($jsonarr);
	}
			
}

?>
