<?php
	/* FACEBOOK Module Templates for Home Page*/
	$this->addTemplate('whyPost','<p>Why should you post? Participating in the community rocks! You\'ll feel good about yourself and rid your body of aches and pains.</p>');
	$this->addTemplateStatic($dynTemp, 'blogDraftList','<div><ul>{items}</ul></div>','','postStory');
	$this->addTemplateStatic($dynTemp, 'blogDraftItem', '<li><strong><a href="'.URL_CANVAS.'?p=postStory&o=blog&editid={blogid}">{title}</a></strong></li>','','postStory');	
?>
