<?php
	/* FACEBOOK Module Templates for Resource Page*/
	$this->addTemplate('resourceList','<h5>{listTitle}</h5><ul class="resourceList">{items}<!--end "resourceList"--></ul>');
	$this->addTemplate('resourceItemText','<li><a title="{title}" href="{url}" onclick="quickLog(\'extLink\',\'resource\',{id},\'{url}\');" title="{notes}" target="_blank">{title}</a></li>');
	$this->addTemplate('resourceItemImage','<li class="thumb"><a title="{title}" href="{url}" onclick="quickLog(\'extLink\',\'resource\',{id},\'{url}\');" title="{notes}" target="_blank">{resourceImage}{title}</a></li>');
	$this->addTemplate('resourceHalfStart','<div class="colFull_half">');
	$this->addTemplate('resourceHalfEnd','</div><!--end "colFull_half"-->');
	$this->addTemplate('resourcePageTitle','<h1>'.SITE_TOPIC.' Links</h1><p>Read more news from some of these websites.</p>');
?>
