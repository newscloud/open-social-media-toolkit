<?php
	/* FACEBOOK Module Templates for Resource Page*/
	$this->addTemplate('resourceList','<h5>{listTitle}</h5><ul class="resourceList">{items}<!--end "resourceList"--></ul>');
	$this->addTemplate('resourceItemText','<li><a title="{title}" href="{url}" onclick="quickLog(\'extLink\',\'resource\',{id},\'{url}\');" title="{notes}" target="_blank">{title}</a></li>');
	$this->addTemplate('resourceItemImage','<li class="thumb"><a title="{title}" href="{url}" onclick="quickLog(\'extLink\',\'resource\',{id},\'{url}\');" title="{notes}" target="_blank">{resourceImage}{title}</a></li>');
	$this->addTemplate('resourceHalfStart','<div class="colFull_half">');
	$this->addTemplate('resourceHalfEnd','</div><!--end "colFull_half"-->');
	$this->addTemplate('resourcePageTitle','<h1>'.SITE_TOPIC.' Links</h1><p>Learn more about climate change from some of these websites.</p>');
	$this->addTemplate('banner1','<div class="adWideBanner"><a href="?p=cbd" onclick="switchPage(\'static\',\'cbd\');return false;"><img src="'.URL_CALLBACK.'?p=cache&img=links_rareearthtones.jpg" alt="rare earthtones center for biological diversity" /></a></div>');
	$this->addTemplate('banner2','<div class="adWideBanner"><a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47);return false;"><img src="'.URL_CALLBACK.'?p=cache&img=links_quark.jpg" alt="quark expeditions - change your life" /></a></div>');
?>
