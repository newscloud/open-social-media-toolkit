<?php
require_once(PATH_CORE.'/classes/db.class.php');
$db=new cloudDatabase();
if (isset($_GET['story-1']) && preg_match('/^story_([0-9]+)$/', $_GET['story-1'], $id))
	$story_1_id = $id[1];
else
	$story_1_id = 0;
if (isset($_GET['story-2']) && preg_match('/^story_([0-9]+)$/', $_GET['story-2'], $id))
	$story_2_id = $id[1];
else
	$story_2_id = 0;
if (isset($_GET['story-3']) && preg_match('/^story_([0-9]+)$/', $_GET['story-3'], $id))
	$story_3_id = $id[1];
else
	$story_3_id = 0;
if (isset($_GET['story-4']) && preg_match('/^story_([0-9]+)$/', $_GET['story-4'], $id))
	$story_4_id = $id[1];
else
	$story_4_id = 0;
if (isset($_GET['story-5']) && preg_match('/^story_([0-9]+)$/', $_GET['story-5'], $id))
	$story_5_id = $id[1];
else
	$story_5_id = 0;
if (isset($_GET['story-6']) && preg_match('/^story_([0-9]+)$/', $_GET['story-6'], $id))
	$story_6_id = $id[1];
else
	$story_6_id = 0;

if (isset($_GET['template']) && preg_match('/^template_[0-9]+$/', $_GET['template']))
	$template = $_GET['template'];
else
	return false;

$db->query("UPDATE Content set isFeatured = 0 WHERE isFeatured = 1");
$sql = sprintf("REPLACE INTO FeaturedTemplate SET id = 1, template = '%s', story_1_id = %s, story_2_id = %s, story_3_id = %s, story_4_id = %s, story_5_id = %s, story_6_id = %s", $template, $story_1_id, $story_2_id, $story_3_id, $story_4_id, $story_5_id, $story_6_id);

$db->query($sql);
$db->query("UPDATE Content set isFeatured = 1 WHERE siteContentId IN ($story_1_id, $story_2_id, $story_3_id, $story_4_id, $story_5_id, $story_6_id)");
// clear out the cache of the home top stories
require_once(PATH_CORE.'/classes/template.class.php');
$templateObj=new template($db);
$templateObj->resetCache('home_feature');
//echo "<h1>SQL: $sql</h1>";

?>
