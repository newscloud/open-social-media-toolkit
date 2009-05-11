<?php	
		
	//one liners				
	$one_line_story_templates = array(); 
	$one_line_story_templates[] = '{*actor*} posted the {*pubType*}, {*title*}, on {*target*}\'s wall with the <a href=\"{*url*}\">Climate Team</a> application.'; 
	$one_line_story_templates[] = '{*actor*} posted a story on {*target*}\'s wall with the <a href=\"{*url*}\">Climate Team</a> application.';
	$one_line_story_templates[] = '{*actor*} posted a story with the <a href=\"{*url*}\">Climate Team</a> application.';
	$one_line_story_templates[] = '{*actor*} posted a story with the Climate Team application.';  
	
	//short stories
	$short_story_templates = array(); 
	$short_story_templates[] = array(	'template_title' => '{*actor*} posted the story, {*title*}, on {*target*}\'s wall with the <a href=\"{*url*}\">Climate Team</a> application.',
										'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
									); 
	$short_story_templates[] = array(	'template_title' => '{*actor*} posted a story on {*target*}\'s wall with the <a href=\"{*url*}\">Climate Team</a> application.',
										'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
									); 
	$short_story_templates[] = array(	'template_title' => '{*actor*} posted a story with the <a href=\"{*url*}\">Climate Team</a> application.',
										'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
									); 
									
									
	//full story
	$full_story_templates = array(); 
	$full_story_templates[] = array(	'template_title' => '{*actor*} posted the story, {*title*}, on {*target*}\'s wall with the <a href=\"{*url*}\">Climate Team</a> application.',
										'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
									); 
	$full_story_templates[] = array(	'template_title' => '{*actor*} posted a story on {*target*}\'s wall with the <a href=\"{*url*}\">Climate Team</a> application.',
										'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
									); 
	$full_story_templates[] = array(	'template_title' => '{*actor*} posted a story with the <a href=\"{*url*}\">Climate Team</a> application.',
										'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
									); 
	
	$code=$fbApp->fbLib->api_client->feed_registerTemplateBundle($one_line_story_templates,$short_story_templates,$full_story_templates);
?>
