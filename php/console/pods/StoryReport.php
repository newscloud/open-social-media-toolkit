<?php
require_once('BasePod.php');


class StoryReport extends BasePod {
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
			$schema['fields'][] = 'numComments';
			$schema['fields'][] = 'score';
			$schema['labels'][] = array('key' => 'title', 'label' => 'Title');
			$schema['labels'][] = array('key' => 'numComments', 'label' => 'Number of Comments');
			$schema['labels'][] = array('key' => 'score', 'label' => 'Score');
		} else if ($view == 'full') {
			$schema = array('fields' => array(), 'labels' => array(), 'datasource' => "{$this->BASE_URL}&pod={$this->id}&pod_action=load_all&view=full", 'numRows' => 50);
			$schema['fields'][] = 'siteContentId';
			$schema['fields'][] = 'sitename';
			$schema['fields'][] = 'title';
			$schema['fields'][] = 'numComments';
			$schema['fields'][] = 'score';
			$schema['fields'][] = 'numReadStory';
			$schema['fields'][] = 'numFullReadStory';
			$schema['fields'][] = 'numSharedStory';
			$schema['fields'][] = 'numVotes';
			$schema['fields'][] = 'storyUrl';
			$schema['labels'][] = array('key' => 'siteContentId', 'label' => 'Story ID');
			$schema['labels'][] = array('key' => 'sitename', 'label' => 'Site Name');
			$schema['labels'][] = array('key' => 'title', 'label' => 'Title');
			$schema['labels'][] = array('key' => 'numComments', 'label' => 'Number of Comments');
			$schema['labels'][] = array('key' => 'numReadStory', 'label' => 'Number of Readers');
			$schema['labels'][] = array('key' => 'numFullReadStory', 'label' => 'Number of Readers Full Story');
			$schema['labels'][] = array('key' => 'numSharedStory', 'label' => 'Number of Times Shared');
			$schema['labels'][] = array('key' => 'numVotes', 'label' => 'Number of Times Voted Up');
			$schema['labels'][] = array('key' => 'score', 'label' => 'Score');
			$schema['labels'][] = array('key' => 'storyUrl', 'label' => 'Story URL');
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
			$where[] = "Content.date > '".$_REQUEST['startDate']." 00:00:00'";
			$startLabel = date('l jS \of F Y', strtotime($params['startDate']));
		} else {
			//$where[] = "Content.date > '".date("Y-m-d 00:00:00", time())."'";
			$where[] = "Content.date > '".date("2000-01-01 00:00:00", time())."'";
			$startLabel = date('l jS \of F Y', time());
		}
		if (isset($_REQUEST['endDate'])) {
			$where[] = "Content.date < '".$_REQUEST['endDate']." 23:59:59'";
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
			$wherestr = ' WHERE '.join(' AND ', $where);
		} else {
			$wherestr = '';
		}
		$usersql = "
			SELECT siteContentId, title, url, numComments, score,
				(SELECT COUNT(1) FROM Log WHERE Log.itemid = Content.siteContentId AND action = 'readStory') AS numReadStory,
				(SELECT COUNT(1) FROM Log WHERE Log.itemid = Content.siteContentId AND action = 'shareStory') AS numSharedStory,
				(SELECT COUNT(1) FROM Log WHERE Log.itemid = Content.siteContentId AND action = 'vote') AS numVotes
			FROM Content $wherestr
			ORDER BY siteContentId DESC
		";
			//FROM Content $wherestr
		$sites = array();
		$sites_res = $this->model->db->query("SELECT * FROM Sites");
		while (($row = mysql_fetch_assoc($sites_res)) !== false)
			$sites[$row['shortname']] = array('dbname' => $row['dbname'], 'siteid' => $row['id'], 'siteurl' => $row['url'], 'sitename' => $row['name']);

		$jsonarr = array('Results' => array('replyCode' => '201', 'replyText' => 'Data Follows', 'data' => array()));
		foreach ($sites as $name => $dbinfo) {
			$dbname = $dbinfo['dbname'];
			$siteid = $dbinfo['siteid'];
			$sitename = $dbinfo['sitename'];
			$siteurl = $dbinfo['siteurl'];
			$this->model->db->selectdb($dbname);

			if ($currSiteId && $currSiteId != $siteid)
				continue;

			$results = $this->model->db->query($usersql);
			$stories = array();
			while (($row = mysql_fetch_assoc($results)) !== false)
				$stories[] = $row;
			foreach ($stories as $story) {
				$this->model->db->selectdb('research');
				$ext_read_sql = "SELECT count(1) as fullReadCount FROM RawExtLinks WHERE siteid = $siteid AND action = 'read' AND itemid = {$story['siteContentId']}";
				$read_results = $this->model->db->query($ext_read_sql);
				$read_array = mysql_fetch_assoc($read_results);
				$story['numFullReadStory'] = $read_array['fullReadCount'];
				$title = sprintf("<a target=\"_cts\" href=\"%s?p=read&cid=%s\">%s</a>", $siteurl, $story['siteContentId'], $story['title']);
				if ($view == 'pod') {
					$tmp = array(
						//'title' => $story['title'],
						'title' => util_link_for($story['title'], 'stories', 'view_story', $story['siteContentId']),
						'numComments' => $story['numComments'],
						'score' => $story['score']
					);
				} else if ($view == 'full' || $view == 'csv') {
					$tmp = array(
						//'siteContentId' => $story['siteContentId'],
						'siteContentId' => util_link_for($story['siteContentId'], 'stories', 'view_story', $story['siteContentId']),
						'sitename' => $sitename,
						//'title' => "<a target=\"_cts\" href=\"{$story['title']}\">{$story['title']}</a>",
						'title' => util_link_for($story['title'], 'stories', 'view_story', $story['siteContentId']),
						//'title' => $title,
						'numComments' => $story['numComments'],
						'score' => $story['score'],
						'numReadStory' => $story['numReadStory'],
						'numFullReadStory' => $story['numFullReadStory'],
						'numSharedStory' => $story['numSharedStory'],
						'numVotes' => $story['numVotes'],
						'storyUrl' => "<a target=\"_cts\" href=\"{$story['url']}\">{$story['url']}</a>",
					);
				}
				$jsonarr['Results']['data'][] = $tmp;
			}
		}
		
		$jsonarr['Results']['SQL'] = stripslashes(preg_replace(array('/\n/', '/\t/'), array(' ', ' '), $usersql));

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
		$jsonarr['Stats']['ActionName'] = 'Story Report';
		$jsonarr['Stats']['ChartType'] = 'line';
		$jsonarr['Stats']['SQL'] = $usersql;

		return json_encode($jsonarr);
	}
			
}

?>
