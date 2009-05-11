<div class="yui-g">
<h1>Completed Challenge: <a href="index.php?p=console&group=street_team&action=view_challenge&id=<? echo $completed_challenge['challengeid']; ?>"><? echo $completed_challenge['challengeid']; ?></a></h1><br /><br />

<div class="completed_challenge">

<p>User ID: <a href="index.php?p=console&group=members&action=view_member&id=<?php echo $completed_challenge['userid']; ?>"><?php echo $completed_challenge['userid']; ?></a></p>
<p>Challenge ID: <a href="index.php?p=console&group=street_team&action=view_challenge&id=<? echo $completed_challenge['challengeid']; ?>"><? echo $completed_challenge['challengeid']; ?></a></p>
<p>Submitted Date: <? echo $completed_challenge['dateSubmitted']; ?></p>
<p>Awarded Date: <? echo $completed_challenge['dateAwarded']; ?></p>
<p>Evidence: <? echo $completed_challenge['evidence']; ?></p>
<p>Comments: <? echo $completed_challenge['comments']; ?></p>
<p>Status: <? echo $completed_challenge['status']; ?></p>
<p>Points Awarded: <? echo $completed_challenge['pointsAwarded']; ?></p>
</div>

<br /><br />
<p>
<a href="index.php?p=console&group=street_team&action=modify_completed_challenge&id=<? echo $completed_challenge['id']; ?>">Edit</a> -- 
<a href="index.php?p=console&group=street_team&action=destroy_completed_challenge&id=<?php echo $completed_challenge['id'] ?>" onclick="if(!confirm('Are you sure you want to remove this item?')) return false">Remove</a> --
<a href="index.php?p=console&group=street_team&action=completed_challenges">Return to Completed Challenges</a>
</p>

</div>
<hr>
<div class="yui-g">


<h2>Review challenge submission:</h2><br />
<div>

<?php  
// hack: put review form on same page as view form
//require_once (PATH_CORE .'/classes/challenges.class.php');
//require_once (PATH_SITE .'/../constants.php'); // HAAACK

// TODO: factor these out in a common constants file

/*
define("URL_BASE","http://host.newscloud.com/sites/climate/facebook");	

define('URL_UPLOADS', URL_BASE.'/uploads');
define('URL_THUMBNAILS', URL_UPLOADS.'/images');
define('URL_SUBMITTED_IMAGES', URL_UPLOADS.'/submissions');
*/
/*
require_once (PATH_FACEBOOK .'/classes/actionFeed.class.php');

// build a report with at least the photos and videos
//require_once (PATH_CORE .'./classes/db.class.php');
//$db = new cloudDatabase();

// major hacks for now, until a custom report function can be built
$action = new stdClass;
$action->action= 'completedChallenge';
$action->itemid = $completed_challenge['id'];
$userid = $completed_challenge['userid'];
$action->userid1 = $userid;
// could fill in fbId here if we werent lazy too
$fbId = 0; 

$actionFeed = new actionFeed();
$report = $actionFeed->fetchChallengeCompletedFeedItem( $action , $fbId, true); 
echo $report;
*/
///////////////////////////
// NEW 


require_once (PATH_CORE .'/classes/user.class.php');
$ut = new UserInfoTable();
$memberids = $ut->getFbIdsForUsers(array($completed_challenge['userid']));
$memberid = $memberids[0];
echo '<a href="'.URL_CANVAS.'?p=profile&memberid='.$memberid.'&viewSubmitted">
	Click here to see profile with challenge submit records</a>';
//echo 'this is where the report goes'; 


////////////////////////////////////////////////////////////////
// figure out how many points would normally be credited


require_once (PATH_CORE . '/classes/challenges.class.php');
$ct = new ChallengeTable();
//$this->db->setDebug(true);
$completedTable = new ChallengeCompletedTable();
$completed = $completedTable->getRowObject();
if ($completed->load($completed_challenge['id']))
{
	$challenge = $ct->getRowObject();
	if ($challenge->load($completed->challengeid))	
		$points = $challenge->pointValue;
		
	else
	{
		$points = '?';
		echo 'oops, couldnt load challenge ' . $completed->challengeid;
	}
}
	
echo '<hr>';
echo 'Challenge title: '.$challenge->title .'<br>';
echo 'Challenge description: '.$challenge->description .'<br>';
//echo 'Challenge title: '.$challenge->title .'<br>';
echo '<hr>';

?>



</div>
<form method="post" action="index.php?p=console&group=street_team&action=approve_completed_challenge">
<input type="hidden" name="completed_challenge[id]" value="<?php echo $completed_challenge['id']; ?>" />
<br />
Points to be awarded: <input type="text" name="pointsAwarded" value="<?php echo $points ?>"/>
<div class="spacer"></div>


<br />
Comments (This is what appears in the feed and blog!)<br />
<textarea name="completed_challenge[comments]" style="width:100%" cols="50" rows="10"><?php echo htmlentities($completed_challenge['comments']); ?></textarea>


<div class="spacer"></div>
<br />
<input type="submit" name="approve" value="Approve" />
<input type="submit" name="reject" value="Reject" />

</form>
</div>
