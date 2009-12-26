<?php
require_once('BasePod.php');
ini_set('memory_limit', '64M');


class UserReport extends BasePod {
	//var $useButtons = true;
	var $useMemberFilters = true;
	var $useSiteFilters = true;
	var $useCSVExport = true;
	var $studyEndDate = array('siteid-1' => '2009-05-04 00:00:00', 'siteid-2' => '2009-06-08 00:00:00'); // set to false to disable

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
			$schema['fields'][] = 'userid';
			$schema['fields'][] = 'email';
			$schema['fields'][] = 'age';
			$schema['labels'][] = array('key' => 'userid', 'label' => 'User ID');
			$schema['labels'][] = array('key' => 'email', 'label' => 'Email');
			$schema['labels'][] = array('key' => 'age', 'label' => 'Age');
		} else if ($view == 'full' || $view == 'csv') {
			$schema = array('fields' => array(), 'labels' => array(), 'datasource' => "{$this->BASE_URL}&pod={$this->id}&pod_action=load_all&view=full", 'numRows' => 50);
			$schema['fields'][] = 'userid';
			$schema['fields'][] = 'siteid';
			$schema['fields'][] = 'email';
			$schema['fields'][] = 'dateRegistered';
			$schema['fields'][] = 'age';
			$schema['fields'][] = 'gender';
			$schema['fields'][] = 'state';
			$schema['fields'][] = 'country';
			$schema['fields'][] = 'interestLevel';
			$schema['fields'][] = 'voteCount';
			$schema['fields'][] = 'postStoryCount';
			$schema['fields'][] = 'postCommentCount';
			$schema['fields'][] = 'postBlogCount';
			$schema['fields'][] = 'readStoryCount';
			$schema['fields'][] = 'completedChallengeCount';
			$schema['fields'][] = 'wonPrizeCount';
			$schema['fields'][] = 'chatStoryCount';
			$schema['fields'][] = 'inviteFriendsCount';
			$schema['fields'][] = 'shareStoryCount';
			$schema['fields'][] = 'cachedPointTotal';
			$schema['fields'][] = 'tweetCount';
			$schema['fields'][] = 'bookmarkToolAdded';
			$schema['fields'][] = 'friendsSignUpCount';
			$schema['fields'][] = 'visitsCount';
			$schema['fields'][] = 'daysOnSiteCount';
			$schema['fields'][] = 'avgPageViewVisit';
			$schema['fields'][] = 'avgSessionTime';
			$schema['fields'][] = 'medianSessionTime';
			$schema['fields'][] = 'usageClass';
			$schema['labels'][] = array('key' => 'userid', 'label' => 'User ID');;
			$schema['labels'][] = array('key' => 'siteid', 'label' => 'Site ID');;
			$schema['labels'][] = array('key' => 'email', 'label' => 'Email');;
			$schema['labels'][] = array('key' => 'dateRegistered', 'label' => 'Date Reg');;
			$schema['labels'][] = array('key' => 'age', 'label' => 'Age');;
			$schema['labels'][] = array('key' => 'gender', 'label' => 'Gender');;
			$schema['labels'][] = array('key' => 'state', 'label' => 'State');;
			$schema['labels'][] = array('key' => 'country', 'label' => 'Country');;
			$schema['labels'][] = array('key' => 'interestLevel', 'label' => 'Interest Level');;
			$schema['labels'][] = array('key' => 'voteCount', 'label' => 'Vote Count');
			$schema['labels'][] = array('key' => 'postStoryCount', 'label' => 'Post Story Count');
			$schema['labels'][] = array('key' => 'postCommentCount', 'label' => 'Post Comment Count');
			$schema['labels'][] = array('key' => 'postBlogCount', 'label' => 'Post Blog Count');
			$schema['labels'][] = array('key' => 'readStoryCount', 'label' => 'Read Story Count');
			$schema['labels'][] = array('key' => 'completedChallengeCount', 'label' => 'Completed Challenge Count');
			$schema['labels'][] = array('key' => 'wonPrizeCount', 'label' => 'Won Prize Count');
			$schema['labels'][] = array('key' => 'chatStoryCount', 'label' => 'Chat Story Count');
			$schema['labels'][] = array('key' => 'inviteFriendsCount', 'label' => 'Invite Friends Count');
			$schema['labels'][] = array('key' => 'shareStoryCount', 'label' => 'Share Story Count');
			$schema['labels'][] = array('key' => 'cachedPointTotal', 'label' => 'Point Total');
			$schema['labels'][] = array('key' => 'tweetCount', 'label' => 'Tweet Count');
			$schema['labels'][] = array('key' => 'bookmarkToolAdded', 'label' => 'Bookmark Tool Added');
			$schema['labels'][] = array('key' => 'friendsSignUpCount', 'label' => 'Friend Sign Up Count');
			$schema['labels'][] = array('key' => 'visitsCount', 'label' => 'Total Visits Count');
			$schema['labels'][] = array('key' => 'daysOnSiteCount', 'label' => 'Total Days on Site');
			$schema['labels'][] = array('key' => 'avgPageViewVisit', 'label' => 'Avg Pages Viewed Per Visit');
			$schema['labels'][] = array('key' => 'avgSessionTime', 'label' => 'Avg Visit Duration (secs)');
			$schema['labels'][] = array('key' => 'medianSessionTime', 'label' => 'Median Visit Duration (secs)');
			$schema['labels'][] = array('key' => 'usageClass', 'label' => 'Usage Class');
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
			$where[] = 'U.isMember = 1';
		}
		if (isset($_REQUEST['teamEligibleOnly'])) {
			$where[] = "U.optInStudy = 1 AND U.eligibility = 'team'";
		}
		if (isset($_REQUEST['startDate'])) {
			//$where[] = "L.t > '".$_REQUEST['startDate']." 00:00:00'";
			$startLabel = date('l jS \of F Y', strtotime($params['startDate']));
		} else {
			//$where[] = "L.t > '".date("Y-m-d 00:00:00", time())."'";
			//$where[] = "L.t > '".date("2000-01-01 00:00:00", time())."'";
			$startLabel = date('l jS \of F Y', time());
		}
		if (isset($_REQUEST['endDate'])) {
			//$where[] = "L.t < '".$_REQUEST['endDate']." 23:59:59'";
			$endLabel = date('l jS \of F Y', strtotime($params['endDate']));
		} else {
			$endDate = false;
			$endLabel = 'Now';
		}
		if (isset($_REQUEST['siteid']) && $_REQUEST['siteid'] != 0) {
			$where[] = 'U.siteid = "'.$_REQUEST['siteid'].'"';
		}
		if ($view == 'csv') {
			$survey_join = ' LEFT JOIN SurveyMonkeys as S ON S.userid = U.userid AND S.siteid = U.siteid';
			$survey_select = ' S.*, ';
		} else {
			$survey_join = '';
			$survey_select = '';
		}
		if (count($where)) {
			$wherestr = ' AND '.join(' AND ', $where);
		} else {
			$wherestr = '';
		}
		if ($this->studyEndDate !== false && $_REQUEST['siteid'] != 0 && isset($this->studyEndDate['siteid-'.$_REQUEST['siteid']])) {
			$study_date = $this->studyEndDate['siteid-'.$_REQUEST['siteid']];
			$study_sql = " AND end_session < '$study_date' ";
		} else {
			$study_sql = '';
		}
		$usersql = "
			SELECT U.siteid as user_siteid, U.userid as user_userid, U.email as user_email,U.dateRegistered,U.age, U.gender, U.state, U.country, U.researchImportance as interestLevel, U.cachedPointTotal,
				U.voteCount,U.postStoryCount, U.postCommentCount, U.postBlogCount, U.readStoryCount, U.completedChallengeCount, U.wonPrizeCount,
				U.chatStoryCount, U.inviteFriendsCount, U.shareStoryCount, U.tweetCount, U.bookmarkToolAdded, U.friendsSignUpCount,
				$survey_select
				(SELECT count(1) FROM SessionLengths WHERE SessionLengths.userid = U.userid AND SessionLengths.siteid = U.siteid $study_sql) as visitsCount,
				(SELECT count(DISTINCT(date_format(start_session, '%y-%m-%d'))) from SessionLengths where SessionLengths.userid = U.userid AND SessionLengths.siteid = U.siteid $study_sql) as daysOnSiteCount,
				(SELECT avg(total_actions) FROM SessionLengths WHERE SessionLengths.userid = U.userid AND SessionLengths.siteid = U.siteid $study_sql) as avgPageViewVisit,
				(SELECT avg(session_length) FROM SessionLengths WHERE SessionLengths.userid = U.userid AND SessionLengths.siteid = U.siteid $study_sql) as avgSessionTime,
				(SELECT (min(0 + session_length) + max(0 + session_length)) / 2 FROM SessionLengths WHERE SessionLengths.userid = U.userid AND SessionLengths.siteid = U.siteid AND session_length != 0 $study_sql) as medianSessionTime
			FROM UserCollectives as U
			$survey_join
			WHERE U.userid != 0 AND ((U.rxConsentForm=1 AND U.age<18) OR U.age>17) $wherestr
		";
			//LIMIT 2000
		$results = $this->model->db->query($usersql);
		$users = array();
		while (($row = mysql_fetch_assoc($results)) !== false)
			$users[] = $row;
		$jsonarr = array('Results' => array('replyCode' => '201', 'replyText' => 'Data Follows', 'data' => array()));
		foreach ($users as $user) {
			//$baseurl = 'http://apps.facebook.com/hotdish/';
			//$title = sprintf("<a href=\"%s?p=read&cid=%s\">%s</a>", $baseurl, $user['itemid'], $user['title']);
			if ($view == 'pod') {
				$tmp = array(
					'userid' => util_link_for($user['user_userid'], 'members', 'view_member', $user['user_userid']),
					//'userid' => $user['userid'],
					'email' => $user['user_email'],
					'age' => $user['age']
				);
			} else if ($view == 'full') {
				$tmp = array(
					//'userid' => $user['userid'],
					'userid' => util_link_for($user['user_userid'], 'members', 'view_member', $user['user_userid']),
					'siteid' => $user['user_siteid'],
					'email' => $user['user_email'],
					'dateRegistered' => $user['dateRegistered'],
					'age' => $user['age'],
					'gender' => $user['gender'],
					'state' => $user['state'],
					'country' => $user['country'],
					'interestLevel' => $user['interestLevel'],
					'voteCount' => $user['voteCount'],
					'postStoryCount' => $user['postStoryCount'],
					'postCommentCount' => $user['postCommentCount'],
					'postBlogCount' => $user['postBlogCount'],
					'readStoryCount' => $user['readStoryCount'],
					'completedChallengeCount' => $user['completedChallengeCount'],
					'wonPrizeCount' => $user['wonPrizeCount'],
					'chatStoryCount' => $user['chatStoryCount'],
					'inviteFriendsCount' => $user['inviteFriendsCount'],
					'shareStoryCount' => $user['shareStoryCount'],
					'cachedPointTotal' => $user['cachedPointTotal'],
					'tweetCount' => $user['tweetCount'],
					'bookmarkToolAdded' => $user['bookmarkToolAdded'],
					'friendsSignUpCount' => $user['friendsSignUpCount'],
					'visitsCount' => $user['visitsCount'],
					'daysOnSiteCount' => $user['daysOnSiteCount'],
					'avgPageViewVisit' => $user['avgPageViewVisit'],
					'avgSessionTime' => round($user['avgSessionTime'], 2),
					'medianSessionTime' => round($user['medianSessionTime'], 2),
					'usageClass' => $this->getUsageClass($user['avgSessionTime'])
				);
		} else if ($view == 'csv') {
			$tmp = array(
				//'userid' => $user['userid'],
				'userid' => $user['user_userid'],
				'siteid' => $user['user_siteid'],
				'email' => $user['user_email'],
				'dateRegistered' => $user['dateRegistered'],
				'age' => $user['age'],
				'gender' => $user['gender'],
				'state' => $user['state'],
				'country' => $user['country'],
				'interestLevel' => $user['interestLevel'],
				'voteCount' => $user['voteCount'],
				'postStoryCount' => $user['postStoryCount'],
				'postCommentCount' => $user['postCommentCount'],
				'postBlogCount' => $user['postBlogCount'],
				'readStoryCount' => $user['readStoryCount'],
				'completedChallengeCount' => $user['completedChallengeCount'],
				'wonPrizeCount' => $user['wonPrizeCount'],
				'chatStoryCount' => $user['chatStoryCount'],
				'inviteFriendsCount' => $user['inviteFriendsCount'],
				'shareStoryCount' => $user['shareStoryCount'],
				'cachedPointTotal' => $user['cachedPointTotal'],
				'tweetCount' => $user['tweetCount'],
				'bookmarkToolAdded' => $user['bookmarkToolAdded'],
				'friendsSignUpCount' => $user['friendsSignUpCount'],
				'visitsCount' => $user['visitsCount'],
				'daysOnSiteCount' => $user['daysOnSiteCount'],
				'avgPageViewVisit' => $user['avgPageViewVisit'],
				'avgSessionTime' => round($user['avgSessionTime'], 2),
				'medianSessionTime' => round($user['medianSessionTime'], 2),
				'usageClass' => $this->getUsageClass($user['avgSessionTime'])
			);
					$tmp['q1a'] = $user['q1a'];
					$tmp['q1b'] = $user['q1b'];
					$tmp['q1c'] = $user['q1c'];
					$tmp['q1d'] = $user['q1d'];
					$tmp['q1e'] = $user['q1e'];
					$tmp['q1f'] = $user['q1f'];
					$tmp['q1g'] = $user['q1g'];
					$tmp['q2a'] = $user['q2a'];
					$tmp['q2b'] = $user['q2b'];
					$tmp['q2c'] = $user['q2c'];
					$tmp['q2d'] = $user['q2d'];
					$tmp['q2e'] = $user['q2e'];
					$tmp['q3a'] = $user['q3a'];
					$tmp['q3b'] = $user['q3b'];
					$tmp['q3c'] = $user['q3c'];
					$tmp['q3d'] = $user['q3d'];
					$tmp['q3e'] = $user['q3e'];
					$tmp['q3f'] = $user['q3f'];
					$tmp['q3g'] = $user['q3g'];
					$tmp['q3h'] = $user['q3h'];
					$tmp['q4a'] = $user['q4a'];
					$tmp['q4b'] = $user['q4b'];
					$tmp['q4c'] = $user['q4c'];
					$tmp['q4d'] = $user['q4d'];
					$tmp['q4e'] = $user['q4e'];
					$tmp['q4f'] = $user['q4f'];
					$tmp['q4g'] = $user['q4g'];
					$tmp['q5a'] = $user['q5a'];
					$tmp['q5b'] = $user['q5b'];
					$tmp['q5c'] = $user['q5c'];
					$tmp['q5d'] = $user['q5d'];
					$tmp['q5e'] = $user['q5e'];
					$tmp['q5f'] = $user['q5f'];
					$tmp['q5g'] = $user['q5g'];
					$tmp['q5h'] = $user['q5h'];
					$tmp['q5i'] = $user['q5i'];
					$tmp['q5j'] = $user['q5j'];
					$tmp['q5k'] = $user['q5k'];
					$tmp['q6'] = $user['q6'];
					$tmp['q7'] = $user['q7'];
					$tmp['q8a'] = $user['q8a'];
					$tmp['q8b'] = $user['q8b'];
					$tmp['q8c'] = $user['q8c'];
					$tmp['q8d'] = $user['q8d'];
					$tmp['q8e'] = $user['q8e'];
					$tmp['q8f'] = $user['q8f'];
					$tmp['q8g'] = $user['q8g'];
					$tmp['q8h'] = $user['q8h'];
					$tmp['q9a'] = $user['q9a'];
					$tmp['q9b'] = $user['q9b'];
					$tmp['q9c'] = $user['q9c'];
					$tmp['q9d'] = $user['q9d'];
					$tmp['q9e'] = $user['q9e'];
					$tmp['q9f'] = $user['q9f'];
					$tmp['q10a'] = $user['q10a'];
					$tmp['q10b'] = $user['q10b'];
					$tmp['q10c'] = $user['q10c'];
					$tmp['q10d'] = $user['q10d'];
					$tmp['q10e'] = $user['q10e'];
					$tmp['q10f'] = $user['q10f'];
					$tmp['q11a'] = $user['q11a'];
					$tmp['q11b'] = $user['q11b'];
					$tmp['q11c'] = $user['q11c'];
					$tmp['q11d'] = $user['q11d'];
					$tmp['q11e'] = $user['q11e'];
					$tmp['q11f'] = $user['q11f'];
					$tmp['q11g'] = $user['q11g'];
					$tmp['q11h'] = $user['q11h'];
					$tmp['q12a'] = $user['q12a'];
					$tmp['q12b'] = $user['q12b'];
					$tmp['q12c'] = $user['q12c'];
					$tmp['q12d'] = $user['q12d'];
					$tmp['q12e'] = $user['q12e'];
					$tmp['q12f'] = $user['q12f'];
					$tmp['q12g'] = $user['q12g'];
					$tmp['q12h'] = $user['q12h'];
					$tmp['q13a'] = $user['q13a'];
					$tmp['q13b'] = $user['q13b'];
					$tmp['q13c'] = $user['q13c'];
					$tmp['q13d'] = $user['q13d'];
					$tmp['q14a'] = $user['q14a'];
					$tmp['q14b'] = $user['q14b'];
					$tmp['q14c'] = $user['q14c'];
					$tmp['q14d'] = $user['q14d'];
					$tmp['q14e'] = $user['q14e'];
					$tmp['q14f'] = $user['q14f'];
					$tmp['q15'] = $user['q15'];
					$tmp['q16'] = $user['q16'];
					$tmp['q17a'] = $user['q17a'];
					$tmp['q17b'] = $user['q17b'];
					$tmp['q17c'] = $user['q17c'];
					$tmp['q17d'] = $user['q17d'];
					$tmp['q17e'] = $user['q17e'];
					$tmp['q18'] = $user['q18'];
					$tmp['q19'] = $user['q19'];
					$tmp['q20'] = $user['q20'];
					$tmp['q21'] = $user['q21'];
					$tmp['q22'] = $user['q22'];
					$tmp['q23'] = $user['q23'];
					$tmp['q24'] = $user['q24'];
					$tmp['q25'] = $user['q25'];
					$tmp['q26'] = $user['q26'];
					$tmp['q27'] = $user['q27'];
					$tmp['q28a'] = $user['q28a'];
					$tmp['q28b'] = $user['q28b'];
					$tmp['q28c'] = $user['q28c'];
					$tmp['q28d'] = $user['q28d'];
					$tmp['q28e'] = $user['q28e'];
					$tmp['q28f'] = $user['q28f'];
					$tmp['q29'] = $user['q29'];
					$tmp['q30'] = $user['q30'];
					$tmp['q31'] = $user['q31'];
					$tmp['q32'] = $user['q32'];
					$tmp['q33'] = $user['q33'];
					$tmp['q34'] = $user['q34'];
					$tmp['q35'] = $user['q35'];
					$tmp['q36'] = $user['q36'];
					$tmp['q37'] = $user['q37'];
					$tmp['q38a'] = $user['q38a'];
					$tmp['q38b'] = $user['q38b'];
					$tmp['q38c'] = $user['q38c'];
					$tmp['q38d'] = $user['q38d'];
					$tmp['q38e'] = $user['q38e'];
				}
			$jsonarr['Results']['data'][] = $tmp;
			//$jsonarr['Results']['SQL'] = stripslashes(preg_replace(array('/\n/', '/\t/'), array(' ', ' '), $usersql));
		}
		
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
		$jsonarr['Stats']['ActionName'] = 'User Report';
		$jsonarr['Stats']['ChartType'] = 'line';
		//$jsonarr['Stats']['SQL'] = $usersql;

		return json_encode($jsonarr);
	}

	function getUsageClass($avgtime) {
		if ($avgtime < 30)
			return 'Low';
		else if ($avgtime < 300)
			return 'Medium';
		else
			return 'High';
	}
			
}

?>
