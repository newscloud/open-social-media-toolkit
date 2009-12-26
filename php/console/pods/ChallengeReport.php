<?php
require_once('BasePod.php');


class ChallengeReport extends BasePod {
	var $useButtons = true;
	var $useMemberFilters = true;
	var $useSiteFilters = true;
	var $useCSVExport = true;

	function __construct($podData) {
		parent::__construct($podData);
		$this->model->db->selectdb('research');
		$schema = $this->load_schema();
	}

	function render_ajax($view = 'pod') {
		//ob_start();
		require('views/statistics/base.php');
		//$data = ob_end_flush();
		//return $data;
		//return "<h1>Statistics Pod: {$this->podData['body']}</h1>";
		return json_encode(array('html' => $html, 'js' => $js));
	}

	function load_schema($view = 'pod') {
		if ($view == 'pod') {
			$schema = array('fields' => array(), 'labels' => array(), 'datasource' => "{$this->BASE_URL}&pod={$this->id}&pod_action=load_all&view=pod", 'numRows' => 50);
			$schema['fields'][] = 'title';
			$schema['fields'][] = 'type';
			$schema['fields'][] = 'requires';
			$schema['labels'][] = array('key' => 'title', 'label' => 'Title');
			$schema['labels'][] = array('key' => 'type', 'label' => 'Type');
			$schema['labels'][] = array('key' => 'requires', 'label' => 'Requires');
		} else if ($view == 'full') {
			$schema = array('fields' => array(), 'labels' => array(), 'datasource' => "{$this->BASE_URL}&pod={$this->id}&pod_action=load_all&view=full", 'numRows' => 50);
			$schema['fields'][] = 'title';
			$schema['fields'][] = 'siteid';
			$schema['fields'][] = 'type';
			$schema['fields'][] = 'requires';
			$schema['fields'][] = 'pointValue';
			$schema['fields'][] = 'numParticipants';
			$schema['fields'][] = 'numCompletedChallenge';
			$schema['fields'][] = 'dateEnd';
			$schema['labels'][] = array('key' => 'title', 'label' => 'Title');
			$schema['labels'][] = array('key' => 'siteid', 'label' => 'Site ID');
			$schema['labels'][] = array('key' => 'type', 'label' => 'Type');
			$schema['labels'][] = array('key' => 'requires', 'label' => 'Requires');
			$schema['labels'][] = array('key' => 'pointValue', 'label' => 'Point Value');
			$schema['labels'][] = array('key' => 'numParticipants', 'label' => 'Number of Participants');
			$schema['labels'][] = array('key' => 'numCompletedChallenge', 'label' => 'Number of Completed Challenges');
			$schema['labels'][] = array('key' => 'dateEnd', 'label' => 'End Date');
			//$schema['fields'][] = array('key' => 'cnt', 'parser' => 'number');
		}

		$this->currFields = $schema['fields'];
		$this->currLabels = $schema['labels'];

		return json_encode($schema);
	}

	function load($view = 'full') {
		return $this->render_ajax($view);
	}

	function load_all($view = 'full', $params = false) {
		$where = array();
		if (isset($_REQUEST['membersOnly'])) {
			//$where[] = 'U.isMember = 1';
		}
		if (isset($_REQUEST['teamEligibleOnly'])) {
			//$where[] = "U.optInStudy = 1 AND U.eligibility = 'team'";
		}
		if (isset($_REQUEST['startDate'])) {
			$where[] = "Log.t > '".$_REQUEST['startDate']." 00:00:00'";
			$startLabel = date('l jS \of F Y', strtotime($params['startDate']));
		} else {
			//$where[] = "Challenges.dateEnd > '".date("Y-m-d 00:00:00", time())."'";
			$where[] = "Log.t > '".date("2000-01-01 00:00:00", time())."'";
			$startLabel = date('l jS \of F Y', time());
		}
		if (isset($_REQUEST['endDate'])) {
			$where[] = "Log.t < '".$_REQUEST['endDate']." 23:59:59'";
			$endLabel = date('l jS \of F Y', strtotime($params['endDate']));
		} else {
			$endDate = false;
			$endLabel = 'Now';
		}
		if (isset($_REQUEST['siteid']) && $_REQUEST['siteid'] != 0) {
			$currSiteId = $_REQUEST['siteid'];
		} else {
			$currSiteId = false;
		}
		if (count($where)) {
			$wherestr = ' '.join(' AND ', $where);
		} else {
			$wherestr = '';
		}
		if ($view == 'pod') {
			$usersql = "
				SELECT id, title, requires, type, dateEnd
				FROM Challenges $wherestr
				ORDER BY dateEnd ASC
			";
		} else {
			$usersql = "
				SELECT id, title, requires, type, dateEnd, pointValue,
					(SELECT COUNT(1) FROM Log LEFT JOIN ChallengesCompleted ON Log.itemid = ChallengesCompleted.id WHERE $wherestr AND ChallengesCompleted.challengeid = Challenges.id AND action = 'completedChallenge') AS numCompletedChallenge,
					(SELECT count(distinct(userid)) from ChallengesCompleted WHERE ChallengesCompleted.challengeid = Challenges.id) as numParticipants
				FROM Challenges ORDER BY dateEnd ASC
			";
			//FROM Content $wherestr
		}
		$sites = array();
		$sites_res = $this->model->db->query("SELECT * FROM Sites");
		while (($row = mysql_fetch_assoc($sites_res)) !== false)
			$sites[$row['name']] = array('dbname' => $row['dbname'], 'siteid' => $row['id']);

		$jsonarr = array('Results' => array('replyCode' => '201', 'replyText' => 'Data Follows', 'data' => array()));
		foreach ($sites as $name => $dbinfo) {
			$dbname = $dbinfo['dbname'];
			$siteid = $dbinfo['siteid'];
			$this->model->db->selectdb($dbname);

			if ($currSiteId && $currSiteId != $siteid)
				continue;

			$results = $this->model->db->query($usersql);
			$stories = array();
			while (($row = mysql_fetch_assoc($results)) !== false)
				$stories[] = $row;
			foreach ($stories as $story) {
				//$baseurl = 'http://apps.facebook.com/hotdish/';
				//$title = sprintf("<a href=\"%s?p=read&cid=%s\">%s</a>", $baseurl, $story['itemid'], $story['title']);
				if ($view == 'pod') {
					$tmp = array(
						//'title' => $story['title'],
						'title' => util_link_for($story['title'], 'street_team', 'challenge_detail_report', $story['id']),
						'type' => $story['type'],
						'requires' => $story['requires']
					);
				} else if ($view == 'full') {
					$tmp = array(
						'title' => util_link_for($story['title'], 'street_team', 'challenge_detail_report', $story['id']),
						'siteid' => $name,
						'type' => $story['type'],
						'requires' => $story['requires'],
						'pointValue' => $story['pointValue'],
						'numParticipants' => $story['numParticipants'],
						'numCompletedChallenge' => $story['numCompletedChallenge'],
						'dateEnd' => $story['dateEnd']
					);
				} else if ($view=='csv') {
					$tmp = array(
						'title' => $story['title'].' id: '.$story['id'],
						'siteid' => $name,
						'type' => $story['type'],
						'requires' => $story['requires'],
						'pointValue' => $story['pointValue'],
						'numParticipants' => $story['numParticipants'],
						'numCompletedChallenge' => $story['numCompletedChallenge'],
						'dateEnd' => $story['dateEnd']
					);					
				}
				$jsonarr['Results']['data'][] = $tmp;
			}
		}
		
		//$jsonarr['Results']['SQL'] = stripslashes(preg_replace(array('/\n/', '/\t/'), array(' ', ' '), $usersql));
		if ($view != 'csv') {
			return json_encode($jsonarr);
		} else {
			$fields = array_keys($jsonarr['Results']['data'][0]);
			echo join(',', $fields)."\n";

			foreach ($jsonarr['Results']['data'] as $row) {
				$str = '';
				foreach ($fields as $field)
					$str .= '"'.str_replace('"', '\"', $row[$field]).'",';
				$str = substr($str, 0, -1);
				echo $str."\n";
			}

			exit;
		}
	}

	function load_statistics($view = 'pod') {
		/*
		$time = date("Y-m-d 00:00:00", time());
		$results = $this->model->load_all(array("action = 'sessionsHour'", "t like '".date("Y-m-d", time())."%'"), false);

		$stats = $results['Results']['data'];
		$counts = array();
		foreach ($stats as $stat) {
			$logtime = preg_replace('/^[0-9]{4}-[0-9]{2}-[0-9]{2} ([0-9]{2}:[0-9]{2}):[0-9]{2}$/', '$1', $stat['t']);
			$counts[$logtime] = $stat['itemid'];
		}
		*/


		$jsonarr = array('Stats' => array ('Totals' => array()));
		/*
		foreach ($counts as $name => $count) {
			$tmp = array();
			$tmp['name'] = $name;
			$tmp['count'] = $count;
			$tmp['label'] = $name;
			$jsonarr['Stats']['Totals'][] = $tmp;
		}
		*/
		$jsonarr['Stats']['ActionName'] = 'Challenge Report';
		$jsonarr['Stats']['ChartType'] = 'line';
		$jsonarr['Stats']['SQL'] = $usersql;

		return json_encode($jsonarr);
	}
			
}

?>
