<?php
	// set widget to locale
	echo "Assigning WidgetId:".$id." to locale: ".$locale."...<br/> ";
	$q=$db->query("SELECT * FROM FeaturedWidgets WHERE locale='$locale';");
	if ($db->countQ($q)>0) {
		$db->query("UPDATE FeaturedWidgets SET widgetid=$id WHERE locale='$locale'");
		// echo "UPDATE FeaturedWidgets SET widgetid=$id WHERE locale='$locale'";
	} else {
		$db->query("INSERT INTO FeaturedWidgets (widgetid,locale,position) VALUES ($id,'$locale',1);");
		//	echo "INSERT INTO FeaturedWidgets (widgetid,locale,position) VALUES ($id,'$locale',1);";
	}
		// clear out the cache for the story
	// require_once(PATH_CORE.'/classes/template.class.php');
	//$templateObj=new template($db);
	//$templateObj->resetCache('read',$siteContentId);	
?>