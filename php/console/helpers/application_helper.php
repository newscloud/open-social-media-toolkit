<?php

/*************************************************************
 *  Application helper 
 *
 *  Place general utility functions in this file
 *  They will be directly accessible in any view file
 *
 *  Place functions in the ApplicationController file
 *    to have them globally accessible in all controllers
 *
 *  Specify the helpers variable in your controller with the
 *    desired helpers you want and they will be loaded from
 *    the file 'helpers/{$name}_helper.php
 *  IE: var $helpers = array('application', 'jquery', 'etc');
 *    This will load the appropriately named helper files
 ************************************************************/


/*
function example() {
	$out = <<<EOD
		<div id="example">
			<p>asdf</p>
		</div>
	EOD;

	echo $out;
}
*/

function link_for($title = null, $ctrl = null, $action = null, $id = null, $onclick = null, $extra_params = null) {
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

/*************************************************************
 * function: build_link_list
 *   returns a list of authorized links
 *
 * (Param $links): Accepts array of arrays of:
 *   'title' => $title,
 *   'ctrl'  => $ctrl,
 *   'action'=> $action,
 *   'id'    => $id
 ************************************************************/
function build_link_list($links = false) {
	if (!$links) return false;

	$list = array();
	foreach ($links as $link) {
		if (($atag = link_for($link['title'], $link['ctrl'], $link['action'], $link['id'], $link['onclick'], $link['extra_params'])))
			$list[] = $atag;
	}

	if (count($list)) {
		return '<p>'.join(' | ', $list).'</p>';
	} else {
		return false;
	}
}

?>
