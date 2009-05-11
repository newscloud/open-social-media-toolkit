<?php
	// set widget to story
	echo "Removing WidgetId:".$id." from all stories<br />";
	if ($id>0) {
		require_once(PATH_CORE.'/classes/template.class.php');
		$templateObj=new template($db);
		$q=$db->query("SELECT siteContentId FROM Content WHERE widgetid=$id;");
		while ($data=$db->readQ($q)) {
			$db->query("UPDATE Content SET widgetid=0 WHERE widgetid=$id AND siteContentId=".$data->siteContentId);	
			$templateObj->resetCache('read',$data->siteContentId);		
		}		
	}		
?>