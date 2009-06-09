<?php
require_once(PATH_CORE.'/classes/db.class.php');
$db=new cloudDatabase();

function cleanDirty($string) {
	// Replace other special chars
	$specialCharacters = array(
	'#' => '',
	'$' => '',
	'%' => '',
	'&' => '',
	'@' => '',
	'.' => '',
	'€' => '',
	'+' => '',
	'=' => '',
	'§' => '',
	'\\' => '',
	'/' => '',
	);

	while (list($character, $replacement) = each($specialCharacters)) {
		$string = str_replace($character, '-' . $replacement . '-', $string);
	}

	$string = strtr($string,
	"ÀÁÂÃÄÅ� áâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
	"AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"
	);

	// Remove all remaining other unknown characters
	$string = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $string);
	$string = preg_replace('/^[\-]+/', '', $string);
	$string = preg_replace('/[\-]+$/', '', $string);
	$string = preg_replace('/[\-]{2,}/', ' ', $string);

	return $string;
}

if (isset($_GET['id']) && preg_match('/^story_([0-9]+)$/i', $_GET['id'], $matches))
	$id = $matches[1];
else
	$id = '';
if (isset($_GET['dropElId']))
	$dropElId = $_GET['dropElId'];
else
	$dropElId = '';
$type = preg_replace('/.*?-image-(mini|blurb)$/', '\1', $dropElId);

if ($id != '')
	$story = mysql_fetch_assoc($db->query("SELECT * FROM Content WHERE siteContentId = $id"));
else
	return false;

//$story['caption'] = cleanDirty($story['caption']);
$story['caption'] = htmlentities($story['caption'], ENT_QUOTES);

//echo '<img src="http://www.newscloud.com/images/scaleImage.php?id=34608&x=180&y=120&fixed=x&crop" />';

$story['title'] = htmlentities($story['title'], ENT_QUOTES);
?>
<?php echo "<?xml version=\"1.0\" ?>"; ?>
<story>
 <?php if ($type == 'mini'): ?>
 	<image><? echo htmlentities("<img src=\"http://www.newscloud.com/images/scaleImage.php?id={$story['imageid']}&x=40&y=30&fixed=x&crop\" />"); ?></image>
 <?php else: ?>
 	<image><? echo htmlentities("<img src=\"http://www.newscloud.com/images/scaleImage.php?id={$story['imageid']}&x=180&y=120&fixed=x&crop\" />"); ?></image>
 <?php endif; ?>
 <title><? echo htmlentities('<a href="'.$story['url'].'" target="_cts">'.$story['title'].'</a>', ENT_QUOTES); ?></title>
 <caption><? echo htmlentities((strlen($story['caption']) > 150) ? substr($story['caption'], 0, 150).' ...' : $story['caption'], ENT_QUOTES); ?></caption>
</story>
