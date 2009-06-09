<?php
			
	$this->addTemplate('subject','You have won a Minnesota Daily Weekly prize: {prizeTitle}!'); 
	$this->addTemplate('body', 
		"Congratulations, {firstName}!\n\n". 
		"As one of the top scorers on the ".SITE_TITLE." ".SITE_TEAM_TITLE." last week, you have won a prize: {prizeTitle}! ". 
		"To claim your reward, just use this link: {claimURL}.\n\n". 
		"If you have any questions, feel free to reply to this email.\n\n". SITE_TITLE. " Online" ); 
	$this->addTemplate('from',"online@mndaily.com"); 
	$this->addTemplate('bcc',"online@mndaily.com"); 
	
	$this->addTemplate('notificationBody', 
		"Congratulations, {firstName}! As one of the top scorers on the ".SITE_TITLE." ".SITE_TEAM_TITLE.
		" last week, you have won a prize: {prizeTitle}! ". 
		"Click <a href='{claimURL}'>here</a> to claim your reward!"
	); 
				
?>