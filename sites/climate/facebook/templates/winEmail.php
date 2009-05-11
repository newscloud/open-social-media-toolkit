<?php
	$this->addTemplate('subject','You have won a Hot Dish weekly prize: {prizeTitle}!'); 
	$this->addTemplate('body', 
	
		"Congratulations, {firstName}!\n\n". 
		"As one of the top scorers on the Hot Dish action team last week, you have won a prize: {prizeTitle}! ".
		"To claim your reward, just use this link: {claimURL}.\n\n".
		"If you have any questions, feel free to reply to this email.\n\n".
		"Holly Richmond,\nHot Dish Support"
				); 
	$this->addTemplate('from',"support@newscloud.com"); 
	$this->addTemplate('bcc',"hotdish.maillog@gmail.com"); 
	
	$this->addTemplate('notificationBody', 
			 
		"Congratulations, {firstName}! As one of the top scorers on the Hot Dish action team last week, you have won a prize: {prizeTitle}! ".
		"Click <a href='{claimURL}'>here</a> to claim your reward!"
				); 
?>