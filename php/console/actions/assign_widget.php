<?php
	// set widget to story
	echo "Assigning WidgetId:".$id." to story id: ".$siteContentId."...<br/> <a href=\"".URL_CANVAS."?p=read&cid=".$siteContentId."\" target=\"_blank\">Preview the story</a>";
	$db->query("UPDATE Content SET widgetid=$id WHERE siteContentId=$siteContentId");
	// clear out the cache for the story
	require_once(PATH_CORE.'/classes/template.class.php');
	$templateObj=new template($db);
	$templateObj->resetCache('read',$siteContentId);	
?>