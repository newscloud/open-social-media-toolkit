<?php

ini_set('display_errors', 1);
//error_reporting(E_ALL);
error_reporting(E_ERROR);

$BASE_URL = 'index.php?p=console';
$title = "Newscloud Management Console";
// Load session, database and global functions
require('global.php');
require('utility_functions.php');
require('roles.php');
require_once('controllers/app_controller.php');

if (!isset($_SESSION['curr_site_id'])) {
	// this will be set to 0 for researchers to see filters
	$curr_site_id = RESEARCH_SITE_ID;
} else {
	$curr_site_id = $_SESSION['curr_site_id'];
}

/*
 * POD DB
 * ::TODO::
 * change this..
 */
//require_once('../db.php');
require_once('/var/www/grist/db.php');
$poddb = new DB();
$controller_name = '';
//$poddb->selectdb('hotdish');
/*
require_once('filters.php');
$filters = getFilters();
$groups = getGroups();
*/



/* ::TODO:: CHANGE THIS TO USER BASED ROLES */
if (isset($_REQUEST['role']))
	$_SESSION['role'] = $_REQUEST['role'];
//else
	//$_SESSION['role'] = 'admin';

/* Load the controller action */

// Array containing messages
// Populate $flash['notice'] to display notices such as successfully saving a change
// Or populate $flash['error'] to display error messages
// Afterwards run set_flash($flash) to save the data
$flash = array();
$flash['notice'] = '';
$flash['error'] = '';
set_flash($flash);

// ::TODO:: Scrub $_REQUEST
if ($curr_site_id !== 0) {
	$_REQUEST['siteid'] = $curr_site_id;
	$_GET['siteid'] = $curr_site_id;
	$_POST['siteid'] = $curr_site_id;
}
$params = array();
if (isset($_REQUEST)) {
	$params = $_REQUEST;
}

if (!isset($params['id']) || !is_numeric($params['id']))
	$params['id'] = 0;
if (!isset($_REQUEST['ctrl']) || $_REQUEST['ctrl'] == '')
	$params['ctrl'] = 'main';
if (!isset($_REQUEST['action']) || $_REQUEST['action'] == '')
	$params['action'] = 'index';

$ctrl = $params['ctrl'];
$action = $params['action'];
$id = $params['id'];

if (check_auth($ctrl, $action) === false) {
	set_flash(array('error' => 'You are not authorized to visit that page.'));
	redirect();
}

// This will setup the appropriate db class for you group/admin
$db = init_db($ctrl, $action);

/* LOAD POD SETTINGS */
$podid = (isset($_REQUEST['pod']) ? $_REQUEST['pod'] : false);
$view = (isset($_REQUEST['view']) ? $_REQUEST['view'] : false);
$pod_action = (isset($_REQUEST['pod_action']) ? $_REQUEST['pod_action'] : 'index');
if ($podid && !is_numeric($podid) && preg_match('/^pod([0-9]+)$/', $podid, $match))
	$podid = $match[1];
if ($podid) {
	$podData = $poddb->get_pod($podid);
	$pod = load_pod($podData);
	if ($pod_action == 'load_item')
		echo $pod->{$pod_action}($id, $view);
	else if ($pod_action == 'load_all')
		echo $pod->{$pod_action}($view);
	else if ($pod_action == 'load_statistics')
		echo $pod->{$pod_action}($view, $params);
	else
		echo $pod->{$pod_action}($view);
	exit;
}


$ctrl_classname = ucfirst($ctrl).'Controller';
$controller = new $ctrl_classname;

$controller->{$action}();
exit;

?>
