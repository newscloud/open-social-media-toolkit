<?php
	/* FACEBOOK Module Templates for Facebook news feeds */	
	
	////need these
	//signup,completedChallenge,redeemed,wonPrize,postStory FOR CANVAS ACTIONS
	//publish, publishSelf for Publisher
	
	$defaultWelcomeMessage='Welcome to '.SITE_TITLE.'!<br>Help spread the word, drive the discussion on hot topics, and earn points for participating along the way.<br><br>Go to the <a href=\"{*url*}\">{*appName*}</a> application.';
	$category='feeds';
	$defaultWelcomeMessage = $dynTemp->useDBTemplate('DefaultWelcomeMessage',$defaultWelcomeMessage,'',false, $category);
	
	$appLink='<a href="{*url*}">{*appName*}</a>';
	
	$feeds=array();
	
	
	
	//when publishing on friends wall
	$feeds['publish']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} shared the {*pubType*}, <a href="{*storyLink*}">{*headline*}</a>, with {*target*} with the '.$appLink.' application.', 
											'{*actor*} shared a {*pubType*} with {*target*} with the '.$appLink.' application.',
											'{*actor*} shared a {*pubType*} with the '.$appLink.' application.',
											'{*actor*} shared a {*pubType*} with the {*appName*} application.' 
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} shared the {*pubType*}, <a href="{*storyLink*}">{*headline*}</a>, with {*target*} with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
													  ), 
												array(	'template_title' => '{*actor*} shared a {*pubType*}  with {*target*} with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													  ), 
												array(	'template_title' => '{*actor*} used the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													 )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} shared the {*pubType*}, <a href="{*storyLink*}">{*headline*}</a>, with {*target*} with the '.$appLink.' application.',
												'template_body' => '<a href="{*storyLink*}"><img src="{*storyImage*}" border="0" align="left" style="margin-right:5px;"></a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
												)
							);
							
							
	//when publish to own wall					
	$feeds['publishSelf']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} posted the {*pubType*}, <a href="{*storyLink*}">{*headline*}</a> on <fb:pronoun uid="actor" possessive="true"/> wall, with the '.$appLink.' application.', 
											'{*actor*} posted a {*pubType*} on <fb:pronoun uid="actor" possessive="true"/> wall with the '.$appLink.' application.',
											'{*actor*} posted a {*pubType*} on <fb:pronoun uid="actor" possessive="true"/> wall with the {*appName*} application.' 
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} posted the {*pubType*}, <a href="{*storyLink*}">{*headline*}</a> on <fb:pronoun uid="actor" possessive="true"/> wall, with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br><br><a href="{*storyLink*}">Read More</a>'
													  ), 
												array(	'template_title' => '{*actor*} posted a {*pubType*} on <fb:pronoun uid="actor" possessive="true"/> wall with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													  ), 
												array(	'template_title' => '{*actor*} used the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													 )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} posted the {*pubType*}, <a href="{*storyLink*}">{*headline*}</a>, on <fb:pronoun uid="actor" possessive="true"/> wall with the '.$appLink.' application.',
												'template_body' => '<a href="{*storyLink*}"><img src="{*storyImage*}" border="0" align="left" style="margin-right:5px;"></a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
												)
							);

	//when posts a story			
	$feeds['postStory']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} posted the story, <a href="{*storyLink*}">{*headline*}</a>, with the '.$appLink.' application.', 
											'{*actor*} posted a story with the '.$appLink.' application.',
											'{*actor*} posted a story with the {*appName*} application.' 
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} posted the story, <a href="{*storyLink*}">{*headline*}</a>, with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													  ), 
												array(	'template_title' => '{*actor*} posted a story with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													  )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} submitted the article, <a href="{*storyLink*}">{*headline*}</a>, with the '.$appLink.' application.',
												'template_body' => '<a href="{*storyLink*}"><img src="{*storyImage*}" border="0" align="left">&nbsp;&nbsp;{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
												)
							);
	
	//when comments on a story			
	$feeds['comment']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} commented on the story, <a href="{*storyLink*}">{*headline*}</a>, with the '.$appLink.' application.', 
											'{*actor*} commented on a story with the '.$appLink.' application.',
											'{*actor*} commented on a story with the {*appName*} application.' 
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} commented on the story, <a href="{*storyLink*}">{*headline*}</a>, with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													  ), 
												array(	'template_title' => '{*actor*} commented on a story with the '.$appLink.' application.',
														'template_body' => '<a href="{*storyLink*}">{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
													  )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} commented on the article, <a href="{*storyLink*}">{*headline*}</a>, with the '.$appLink.' application.',
												'template_body' => '<a href="{*storyLink*}"><img src="{*storyImage*}" border="0" align="left">&nbsp;&nbsp;{*title*}</a><br>{*story*}<br /><a href="{*storyLink*}">Read More</a>'
												)
							);
	
	//when user joins the app				
	$feeds['signup']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} joined the '.SITE_TEAM_TITLE.' with the '.$appLink.' application.'
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} joined the '.SITE_TEAM_TITLE.' with the '.$appLink.' application.',
														'template_body' => $defaultWelcomeMessage
													  )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} joined the '.SITE_TEAM_TITLE.' with the '.$appLink.' application.',
												'template_body' => $defaultWelcomeMessage
												)
							);
						
							
	//when user invites friends to the app				
	/*
	 $feeds['invite']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} invited {*target*} to the '.$appLink.' application.', 
											'{*actor*} invited friends to the '.$appLink.' application.'
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} invited friends, {*target*} to the '.$appLink.' application.',
														'template_body' => $defaultWelcomeMessage
													  ),
												array (	'template_title' => '{*actor*} invited friends to the '.$appLink.' application.',
														'template_body' => $defaultWelcomeMessage
													  )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} invited friends, {*target*} to the '.$appLink.' application.',
												'template_body' => $defaultWelcomeMessage
												)
							);

	*/
	
	//when user completes a challenge			
	$feeds['completedChallenge']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} completed the challenge, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.', 
											'{*actor*} completed a challenge with the '.$appLink.' application.'
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} completed the challenge, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.',
														'template_body' => '{*story*}'
													  ),
												array (	'template_title' => '{*actor*} completed a challenge with the '.$appLink.' application.',
														'template_body' => '{*story*}'
													  )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} completed the challenge, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.',
												'template_body' => '{*story*}<br /><br />'.$defaultWelcomeMessage
												)
							);
							
							
	//when user wins a prize			
	$feeds['wonPrize']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} won the prize, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.', 
											'{*actor*} won a prize with the '.$appLink.' application.'
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} won the prize, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.',
														'template_body' => '{*story*}'
													  ),
												array (	'template_title' => '{*actor*} won a prize with the '.$appLink.' application.',
														'template_body' => '{*story*}'
													  )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} won a prize with the '.$appLink.' application.',
												'template_body' => '{*story*}'.'<br><br>'.$defaultWelcomeMessage
												)
							);
	
	//when user redeems a prize			
	$feeds['redeemed']=array(
							//one liners				
							'oneLine' => array( 
											'{*actor*} cashed in and redeemed, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.', 
											'{*actor*} cashed in and redeemed a prize with the '.$appLink.' application.'
											), 
							//short stories
							'shortStory' => array( 
												array (	'template_title' => '{*actor*} cashed in and redeemed, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.',
														'template_body' => '{*story*}'
													  ),
												array (	'template_title' => '{*actor*} cashed in and redeemed a prize with the '.$appLink.' application.',
														'template_body' => '{*story*}'
													  )
												), 						
							//full story
							'fullStory' => array(	
												'template_title' => '{*actor*} cashed in and redeemed, <a href="{*storyLink*}">{*title*}</a>, with the '.$appLink.' application.',
												'template_body' => '{*story*}'.'<br /><br />'.$defaultWelcomeMessage
												)
							);
?>
