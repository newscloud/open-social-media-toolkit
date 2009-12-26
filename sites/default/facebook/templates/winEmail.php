<?php
			
$cat = 'winEmail';
	$this->addTemplateDynamic($dynTemp, 'subject','You have won a Weekly prize: {prizeTitle}!','',$cat); 
	$this->addTemplateDynamic($dynTemp, 'body', 
		"Congratulations, {firstName}!\n\n". 
		"As one of the top scorers on the ".SITE_TITLE." ".SITE_TEAM_TITLE." last week, you have won a prize: {prizeTitle}! ". 
		"To claim your reward, just use this link: {claimURL}.\n\n". 
		"If you have any questions, feel free to reply to this email.\n\n". SITE_TITLE. " Online",'',$cat); 
	$this->addTemplateDynamic($dynTemp, 'from',"support@yoursite.com",'',$cat);
	$this->addTemplateDynamic($dynTemp, 'bcc',"support@yoursite.com",'',$cat);
	
	$this->addTemplateDynamic($dynTemp, 'notificationBody', 
		"Congratulations, {firstName}! As one of the top scorers on the ".SITE_TITLE." ".SITE_TEAM_TITLE.
		" last week, you have won a prize: {prizeTitle}! ". 
		"Click <a href='{claimURL}'>here</a> to claim your reward!"
	,'',$cat);
				
?>