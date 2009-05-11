<?php

function clean($string) {
	if ($_GET['type'] == 'sessions') return $string;
	$return = $string;
	return preg_replace(array('/num/', '/([A-Z])/'), array('Number of', ' $1'), $string);
	if (preg_match('/(num)?([A-Z][^A-Z]+)([A-Z][^A-Z]+)?/', $string, $match)) {
		$return = '';
		if ($match[1] != '')
			$return .= 'Number of ';
		if ($match[2] != '')
			$return .= $match[2];
		if ($match[3] != '')
			$return .= " {$match[3]}";
	}
	return $return;
}

require_once(PATH_CORE.'/classes/db.class.php');
$db=new cloudDatabase();

$times = array('day', 'week', 'month', 'all');
$types = array('members', 'actions', 'sessions');
$timeslabels = array(
	'day' 	=> 'Daily ('.date('l jS \of F Y', time()).')',
	'week'	=> 'Week of ',
	'month' => 'Month of '.date('F Y', time()),
	'all'		=> 'All time'
);
$typeslabels = array(
	'members' 	=> 'Members Statistics',
	'actions' 	=> 'Site Actions Statistics',
	'sessions' 	=> 'Active Session Statistics',
);
if (isset($_GET['type']))
	$type = $_GET['type'];
//if (isset($_GET['time']))
	//$time = $_GET['time'];
$time = ($_GET['time'] ? $_GET['time'] : 'day');

if (!in_array($type, $types))
	$type = 'actions';
	//$type = 'members';
if (!in_array($time, $times))
	$time = 'day';

$t = date("Y-m-d 00:00:00", time());
$timeframe = '';
switch ($time) {
	case 'day':
		$day = date("d", time());
		$start = date("Y-m-d 00:00:00", time());
		$finish = preg_replace('/-[0-9]{2} /', ($day < 9 ? '-0' : '-') . ($day+1)." ", $start);
		$timeframe = "t > '$start' AND t < '$finish'";
	break;
	case 'week':
		$todayMidnight = date("U", strtotime(date("Y-m-d 00:00:00", time())));
		$start = date("Y-m-d 00:00:00", $todayMidnight - (6 * 24 * 60 * 60));
		$startLabel = date("l F jS", $todayMidnight - (6 * 24 * 60 * 60));
		$day = date("d", time());
		$finish = date("Y-m-d 00:00:00", time());
		$finishLabel = date("l F jS Y", time());
		$finish = preg_replace('/-[0-9]{2} /', ($day < 9 ? '-0' : '-') . ($day+1)." ", $finish);
		$timeframe = "t > '$start' AND t < '$finish'";
		$timeslabels[$time] .= $startLabel .' -- '. $finishLabel;
	break;
	case 'month':
		//$numDays = date('t', time());
		$day = date("d", time());
		$finish = date("Y-m-d 00:00:00", time());
		$finish = preg_replace('/-[0-9]{2} /', ($day < 9 ? '-0' : '-') . ($day+1)." ", $finish);
		$start = preg_replace('/-[0-9]{2} /', "-01 ", $finish);
		$timeframe = "t > '$start' AND t < '$finish'";
	break;
	case 'all':
	break;
}
$where = '';
if ($timeframe != '')
	$where = "WHERE $timeframe";
if ($type == 'actions')
	$sql = "SELECT * FROM Log $where";
else if ($type == 'sessions')
	$sql = "SELECT * FROM Log WHERE action = 'sessionsHour' AND t LIKE '".date("Y-m-d", time())."%'";
else
	$sql = "SELECT *, (SELECT count(1) FROM User WHERE isMember = 1) as numUsers, (SELECT count(1) FROM User WHERE isMember = 0) as numNonUsers, (SELECT count(1) FROM User WHERE eligibility = 'team') as numActionTeam FROM Log $where";
//echo "<h1>SQL: $sql</h1>";
$results = $db->query($sql);
$stats = array();
while ($row = mysql_fetch_assoc($results))
	$stats[] = $row;

//$actionTypes = array('vote','comment','readStory','readWire','invite','postStory','publishWire','publishStory','shareStory','referReader','postTwitter','signup','acceptedInvite','redeemed','wonPrize','completedChallenge','addedWidget','addedFeedHeadlines','friendSignup','addBookmarkTool','levelIncrease');
if ($type == 'actions') {
	$actionTypes = array(
		'vote'					=> 'numVotes',
		'comment'				=> 'numComments',
		'shareStory'		=> 'numSharedStories',
		'postStory'			=> 'numPostedStories',
		'postBlog'=> 'numPostedBlogs',
		'publishStory'	=> 'numPublishedStories',
		'readStory'		=> 'numReadStories',
		'chatStory'=> 'numChatStories',
		'completedChallenge'	=> 'numCompletedChallenges'
	);
	$counts = array(
		'numVotes'						=> 0,
		'numComments'					=> 0,
		'numSharedStories'		=> 0,
		'numPostedStories'		=> 0,
		'numPostedBlogs'		=> 0,
		'numPublishedStories'	=> 0,
		'numReadStories'	=> 0,
		'numChatStories'		=> 0,
		'numCompletedChallenges'	=> 0,
	);
} else if ($type == 'sessions') {
} else if ($type == 'members') {
	$actionTypes = array(
		'invite'		=> 'numInvites',
		'signup'		=> 'numSignups',
		'redeemed'	=> 'numPrizesRedeemed',
		'acceptedInvite'	=> 'numAcceptedInvites'
	);
	$counts = array( 
		'numInvites'				=> 0,
		'numSignups'				=> 0,
		'numPrizesRedeemed'	=> 0,
		'numAcceptedInvites'	=> 0,
		'numTotalMembers'		=> 0,
		'numTotalNonMembers'		=> 0,
		'numActionTeamMembers'		=> 0
	);
} else {
}

if ($type != 'sessions') {
	$totalActions = 0;
	foreach ($stats as $stat) {
		if ($totalActions == 0 && $type == 'members') {
			$counts['numTotalMembers'] = $stat['numUsers'];
			$counts['numTotalNonMembers'] = $stat['numNonUsers'];
			$counts['numActionTeamMembers'] = $stat['numActionTeam'];
		}
		if (array_key_exists($stat['action'], $actionTypes)) {
			$totalActions++;
			$counts[$actionTypes[$stat['action']]] += 1;
		}
	}
} else {
	$counts = array();
	foreach ($stats as $stat) {
		$logtime = preg_replace('/^[0-9]{4}-[0-9]{2}-[0-9]{2} ([0-9]{2}:[0-9]{2}):[0-9]{2}$/', '$1', $stat['t']);
		$counts[$logtime] = $stat['itemid'];
	}
}

//echo "<h1>Total Actions: $totalActions</h1><br /><br />";
//print_r($counts);

$actionName = $typeslabels[$type] .' :: '. $timeslabels[$time];
$json = '{ "Stats" : { "Totals" : [';
//$json = '{ "Results" : [';
foreach ($counts as $name => $count)
	$json .= sprintf('{ "name" : "%s", "count" : "%s", "label" : "%s"},', $name, $count, clean($name)." ($count)");
$json .= '],';
$json .= ' "ActionName" : "'.$actionName.'"';
$json .= '}}';
//$json .= '], "Total": '.$totalActions.'}}';

echo $json;

?>
