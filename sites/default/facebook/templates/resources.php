<?php
	/* FACEBOOK Module Templates for Resource Page*/
	$this->addTemplateStatic($dynTemp, 'resourceList','<h5>{listTitle}</h5><ul class="resourceList">{items}<!--end "resourceList"--></ul>', '','resources');
	$this->addTemplateStatic($dynTemp, 'resourceItemText','<li><a title="{title}" href="{url}" onclick="quickLog(\'extLink\',\'resource\',{id},\'{url}\');" title="{notes}" target="_blank">{title}</a></li>','','resources');
	$this->addTemplateStatic($dynTemp, 'resourceItemImage','<li class="thumb"><a title="{title}" href="{url}" onclick="quickLog(\'extLink\',\'resource\',{id},\'{url}\');" title="{notes}" target="_blank">{resourceImage}{title}</a></li>', '','resources');
	$this->addTemplateStatic($dynTemp, 'resourceHalfStart','<div class="colFull_half">','','resources');
	$this->addTemplateStatic($dynTemp, 'resourceHalfEnd','</div><!--end "colFull_half"-->','','resources');
	$this->addTemplateStatic($dynTemp, 'resourcePageTitle','<h1>'.SITE_TOPIC.' Links</h1><p>Read more news from some of these websites.</p>','','resources');
?>
