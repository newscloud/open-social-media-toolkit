<?php
	/* FACEBOOK Module Templates for Home Page*/
	$this->addTemplateDynamic($dynTemp, 'whyPost','<p>Why should you post? By sharing stories that interest you, you\'ll add diversity to the news on the site and encourage others to participate as well.</p>','','postStory');
	$this->addTemplateStatic($dynTemp, 'blogDraftList','<div><ul>{items}</ul></div>','','postStory');
	$this->addTemplateStatic($dynTemp, 'blogDraftItem', '<li><strong><a href="'.URL_CANVAS.'?p=postStory&o=blog&editid={blogid}">{title}</a></strong></li>','','postStory');	
?>
