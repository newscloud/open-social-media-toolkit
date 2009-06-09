<?php

$site_roles = array(
	'admin' => array(
		'name' => 'admin',
		'ctrl_auth' => array(
			'stories.*',
			'members.*',
			'street_team.*',
			'dashpods.*',
			'admin.*',
			'facebook.*',
			'settings.*',
		),
		'model_auth' => array(
			'*.*'
		),
		'filter_auth' => array(
			'*.*'
		)
	),
	'researcher' => array(
		'name' => 'researcher',
		'ctrl_auth' => array(
			'stories.*',
			'members.*',
			'street_team.*',
			'dashpods.*'
		),
		'model_auth' => array(
			'*.*'
		),
		'filter_auth' => array(
			'*.*'
		)
	),
	'sponsor' => array(
		'name' => 'sponsor',
		'ctrl_auth' => array(
			'stories.*',
			'members.*',
			'street_team.*',
			'dashpods.*'
		),
		'model_auth' => array(
			'*.*'
		),
		'filter_auth' => array(
			'*.*'
		)
	),
	'moderator' => array(
		'name' => 'moderator',
		'ctrl_auth' => array(
			'stories.index',
			'stories.featured',
			'stories.story_posts',
			'stories.comments',
			'stories.video_posts',
			'stories.widgets',
			'stories.edittemplates',
			'members.index',
			'members.member_emails',
			'members.outboundmessages',
			'street_team.*'
		),
		'model_auth' => array(
			'*.*'
		),
		'filter_auth' => array(
			'*.*'
		)
	),
	'default' => array(
		'name' => 'default',
		'ctrl_auth' => array(
			'stories.index'
		),
		'model_auth' => array(
			'*.*'
		),
		'filter_auth' => array(
			'*.*'
		)		
	)
);

function check_auth($ctrl = false, $action = false) {
	global $params;
	$role = $_SESSION['role'];
	if (!$ctrl) {
		if (!isset($params['ctrl']) || $params['ctrl'] == '')
			return false;
		else
			$ctrl = $params['ctrl'];
	}
	if (!$action) {
		if (!isset($params['action']) || $params['action'] == '')
			$action = 'index';
		else
			$action = $params['action'];
	}

	/* Default catch for base action main/index all users have access to prevent redirect loops */
	if ($ctrl === 'main' && $action === 'index')
		return valid_ctrl_action($ctrl, $action);


	if(($auth = get_auth_for($role))) {
		if (!valid_ctrl_action($ctrl, $action)) {
			//set_flash(array('error' => 'Invalid action or you do not have permission to access this action.. ('.$ctrl.'--'.$action.')'));
			//redirect();
			return false;
		}

		if (in_array('*.*', $auth['ctrl_auth'])) {
			return true;
		} else if (in_array("$ctrl.*", $auth['ctrl_auth'])) {
			return true;
		} else if (in_array("$ctrl.$action", $auth['ctrl_auth'])) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function get_auth_for($role = false) {
	global $site_roles;
	if (!$role) return false;

	if (!isset($site_roles[$role])) return false;

	return $site_roles[$role];
}

/* Checks for valid controller file.
   This is also used to load each controller as all requests hit this
 */
function valid_ctrl_action($ctrl, $action) {
	if (!$ctrl || !$action) return false;


	if (!file_exists(PATH_CONSOLE.'/controllers/'.strtolower($ctrl).'_controller.php')) {
		set_flash(array('error' => 'Unknown controller or you do not have permission to access this controller.'));
		//redirect();
		return false;
	} else {
		require_once('controllers/'.strtolower($ctrl).'_controller.php');
	}

	return in_array($action, array_diff(get_class_methods(ucfirst($ctrl).'Controller'), get_class_methods('AppController')));
}

?>
