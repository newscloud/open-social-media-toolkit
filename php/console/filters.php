<?php

function getGroups($podData = false) {
	$groups = array(
		'memberFilters' => array(
			'label' => 'Member Filters',
			'useFieldSet' => true
		),
		'dateFilters' => array(
			'label' => 'Date Range',
			'useFieldSet' => true
		)
	);

	return $groups;
}

function getFilters($podData = false) {
	if (!$podData) {
		$dateField = 't';
	} else {
		$dateField = $podData->dateField;
	}

	$filters = array(
		'siteFilters' => array(
			'name' => 'siteSelect',
			'type' => 'drop_down',
			'label' => 'Select Site',
			'options' => getSites(),
			'default_option' => '0',
			'group' => 'memberFilters',
			'authorization' => array('all')
		),
		'memberFilters' => array(
			'name' => 'membersOptions',
			'type' => 'radio',
			'label' => 'Member Types',
			'options' => array(
				'all' => array(
					'label' => 'All Users',
					'default' => true,
					'sql'	=> ''
				),
				'membersOnly' => array(
					'label' => 'All Members',
					'default' => false,
					'sql' => 'User.isMember = 1'
				),
				'teamEligibleOnly' => array(
					'label' => 'Inside Study',
					'default' => false,
					'sql' => "User.optInStudy = 1 AND User.eligiblity = 'team'"
				)
			),
			'default_option' => 'all',
			'group' => 'memberFilters',
			'authorization' => array('all')
		),
		'dateFilters' => array(
			'name' => 'dateSelect',
			'type' => 'yui_date_filters',
			'label' => 'Date Filters',
			'options' => array(
				'all' => array(
					'label' => 'All Time',
					// ::TODO:: dynamic date field selection
					'sql' => "Log.$dateField > '1984-05-22 07:21:12'"
				),
				'day' => array(
					'label' => 'Daily',
					'sql' => "Log.$dateField > '1984-05-22 07:21:12'"
				)
			),
			'default_option' => 'all',
			'group' => 'dateFilters',
			'authorization' => array('all')
		)
	);

	return $filters;
}

function getSites() {
	$db = new DB();
	$db->selectdb('research');
	$sites = array();
	$sites[] = array('label' => 'All Sites', 'sql' => '');
	$sites_res = $db->query("SELECT * FROM Sites");
	while (($row = mysql_fetch_assoc($sites_res)) !== false)
		$sites[$row['id']] = array('label' => $row['name'], 'sql' => 'siteid = '.$row['id']);
	
	return $sites;
}
	
?>
