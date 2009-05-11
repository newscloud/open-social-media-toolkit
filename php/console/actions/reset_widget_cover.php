<?php
	// set widget to story
	echo "Removing featured widget from cover page<br />";
	$db->query("DELETE FROM FeaturedWidgets WHERE locale='homeFeature'");
	// clear out the cache for the cover widget area
	/*
	require_once(PATH_CORE.'/classes/template.class.php');
	$templateObj=new template($db);
	$templateObj->resetCache('read',$siteContentId);
	*/	
?>