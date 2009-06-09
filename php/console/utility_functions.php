<?php
// Utility functions
// Set the Title variable for the page, override this in action function
// to change the title for the given page. This is used in header.php

function gen_actions($action_list) {
	// ::TODO:: build function to generate role based action list
	return true;
}

function gen_links($link_list) {
	// ::TODO:: build function to generate role based link list
	return true;
}

function url_for($ctrl, $action = false, $id=0) {
	global $BASE_URL;
	if ($action == false) $action = 'index';
	$ctrl = strtolower($ctrl);

	if (!check_auth($ctrl, $action)) return false;

  return $BASE_URL."&ctrl=$ctrl&action=$action&id=$id";
}

function util_link_for($title = null, $ctrl = null, $action = null, $id = null, $onclick = null, $extra_params = null) {
	if ($title === null)
		return false;
	if ($ctrl === null)
		$ctrl = 'main';
	if ($action === null)
		$action = 'index';
	if ($id === null)
		$id = 0;

	$onclick = ($onclick !== null) ? "onclick=\"$onclick\"" : '';


	$url = url_for($ctrl, $action, $id);
	if (!$url) return false;

	if ($extra_params !== null && is_array($extra_params) && count($extra_params))
		foreach ($extra_params as $param => $value)
			$url .= "&$param=$value";

	return "<a href=\"$url\" $onclick>$title</a>";
}

function create_from_params($table, $params) {
  $fields = array();
  $values = array();
  foreach($params as $key => $val) {
    array_push($fields, $key);
    array_push($values, "'" . mysql_real_escape_string(stripslashes($val)) . "'");
  }
  $fields = implode($fields, ',');
  $values = implode($values, ',');
  
  mysql_query("INSERT INTO $table ($fields) VALUES ($values)");
}

function update_from_params($table, $params, $id) {
  $assignments = array();
  foreach($params as $key => $val) {
    array_push($assignments, $key . '=' . "'" . mysql_real_escape_string(stripslashes($val)) . "'");
  }
  $assignments = implode($assignments, ',');
  
  mysql_query("UPDATE $table SET $assignments WHERE id=$id");
}

function render($view_file = false) {
	if (!$view_file) return false;

	if (!file_exists($view_file)) return false;

	disp_header();
	require($view_file);
	disp_footer();
}

function redirect($url = false) {
	global $BASE_URL;
	if (!$url) $url = "$BASE_URL&ctrl=main&action=index";
	header('Location: '.$url);
	$skip_render = true;
	exit;
}

function disp_header($title = "Newscloud Management Console", $action = 'index') {
	global $init;
	global $controller_name;
	global $action_name;
	global $curr_site_id;
	require_once (PATH_PHP.'/classes/page.class.php');
	$page=new XHTMLpage();
	$page->pkgScripts(CACHE_PREFIX.'nrConsole',array(PATH_PHP_SCRIPTS.'/template.js'));
	//$template_src = '<script src="http://hotdish.newsi.us/?p=cache&type=js&cf=template.js&v=1.002"></script>';
	//$page->addScript(PATH_PHP_SCRIPTS.'/template.js');

	// hack: djm - dont know correct way to incorporate this
	//$page->addScript('http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js');
			
	$templateBuilder = false;
	if ($action == 'template_builder')
		$templateBuilder = true;

	$flash = get_flash();
	include_once('header.php');
}

function disp_footer($action = '') {
	global $controller_name;
	$templateBuilder = false;
	if ($action == 'template_builder')
		$templateBuilder = true;

	require_once('footer.php');
}

// Load Dashboard
// This will load up the various info for the dashboard
function load_dashboard($db) {
	$data = array();
	$data['new_contact_emails'] = $db->load_all("SELECT * FROM ContactEmails WHERE is_read = 0 ORDER BY date DESC");

	return $data;
}

/*
// HAAAXXX
define("URL_BASE","http://host.newscloud.com/sites/climate/facebook");

// TODO: problem: management console needs these as well
define('URL_UPLOADS', URL_BASE.'/uploads');
define('URL_THUMBNAILS', URL_UPLOADS.'/images');
define('URL_SUBMITTED_IMAGES', URL_UPLOADS.'/submissions');

define('PATH_UPLOAD_IMAGES', PATH_SITE.'/../facebook/uploads/images/'); // TODO: FIX THIS HACK	
*/




function handle_image_upload($fieldname, $prefix)
{			
	
	//echo '<pre> '. print_r($_POST, true) . '</pre>';
	//echo '<pre> '. print_r($_FILES, true) . '</pre>';
	if (is_uploaded_file($_FILES[$fieldname]['tmp_name']))
	{
		//echo 'uploaded temp: ' .$_FILES[$fieldname]['tmp_name'];
	   
		$uploaddir = PATH_UPLOAD_IMAGES;
		$filename="{$prefix}_" . basename($_FILES[$fieldname]['name']);
	    $uploadfile = $uploaddir . $filename;
	    move_uploaded_file($_FILES[$fieldname]['tmp_name'], $uploadfile);
	    copy($uploadfile, $uploaddir. 'thumbnail_'. $filename); // TODO: implement resizing for bandwidth savings
	    
	    return $filename;
	} else

	return false;    
}

function load_pod($podData = false) {
	if (isset($podData['data']['podfile']) && $podData['data']['podfile'] != '' && isset($podData['data']['name']) && file_exists(PATH_CONSOLE.'/pods/'.$podData['data']['podfile'])) {
		require(PATH_CONSOLE.'/pods/'.$podData['data']['podfile']);
		$name = $podData['data']['name'];
		$podh = new $name($podData);
	} else {
		require_once(PATH_CONSOLE.'/pods/BasePod.php');
		$podh = new BasePod($podData);
	}

	return $podh;
}

function build_filters_from_params($model, $params) {
	$filters = array(
		'membersOnly'	=> array(
			'filter' => 'User.isMember = 1'
		),
		'insideStudy' => array(
			'filter' => "User.optInStudy = 1 AND User.eligibility = 'team'"
		),
		'startDate'	=> array(
			'filter' => "{$model->tablename}.{$model->dateField} > '{$params['startDate']}'"
		),
		'endDate'		=> array(
			'filter' => "{$model->tablename}.{$model->dateField} < '{$params['endDate']}'"
		)
	);

	$selectedFilters = array();

	foreach ($filters as $name => $filter)
		if (isset($params[$name]))
			$selectedFilters[] = $filter['filter'];

	return $selectedFilters;
}

?>
