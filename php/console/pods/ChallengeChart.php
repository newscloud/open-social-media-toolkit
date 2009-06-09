<?php
require_once('BasePod.php');


class ChallengeChart extends BasePod {
	var $useButtons = false;
	var $useMemberFilters = true;
	var $useSiteFilters = true;

	function __construct($podData) {
		parent::__construct($podData);
	}

	function load_statistics($view = 'pod', $params = false) {
		$where = array();
		$where_str = '';
		if ($_REQUEST) {
			if (isset($_REQUEST['membersOnly'])) {
				//$where[] = 'isMember = 1';
			}
			if (isset($_REQUEST['teamEligibleOnly'])) {
				$where[] = 'eligibility = "team"';
			}
			if (isset($_REQUEST['siteid']) && $_REQUEST['siteid'] != 0) {
				$where[] = 'UserCollectives.siteid = "'.$_REQUEST['siteid'].'"';
				$where[] = 'LogDumps.siteid = "'.$_REQUEST['siteid'].'"';
			}
		}
		if (count($where))
			$where_str = ' AND '.join(' AND ', $where);
		$this->model->db->selectdb('research');
		$chal_sql = "select action,count(distinct(userid1)) as cnt,count(distinct(userid1))/(select count(userid) FROM UserCollectives WHERE eligibility='team')*100 as percent FROM LogDumps,UserCollectives WHERE LogDumps.userid1=UserCollectives.userid AND UserCollectives.eligibility='team' $where_str AND FIND_IN_SET(action,'shareStory,comment,addBookmarkTool,postStory,vote,readStory,invite,completedChallenge,friendSignup,wonPrize,signup') GROUP BY action order by cnt DESC";
		$results = $this->model->db->query($chal_sql);
		$stats = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$stats[] = $row;

		$jsonarr = array('Stats' => array ('Totals' => array()));

		$totalCities = count($cities);
		$otherCities = 0;
		foreach ($stats as $stat) {
			$jsonarr['Stats']['Totals'][] = array('name' => $stat['action'], 'count' => $stat['cnt'], 'label' => "{$stat['action']} -- ".round($stat['percent'], 2)."%");
		}
		//$jsonarr['Stats']['Totals'][] = array('name' => 'Unknown', 'count' => $unknown, 'label' => 'Unknown');
		$jsonarr['Stats']['ActionName'] = "Percentages of Users who Completed Challenges";
		$jsonarr['Stats']['ChartType'] = 'bar';
		//$jsonarr['Stats']['SQL'] = $chal_sql;

		return json_encode($jsonarr);
	}
			
}

?>
