<?php
	$static='<br /><h1>'.SITE_TITLE.' Frequently Asked Questions</h1><h5>Some of the most common questions we get are:</h5>'.
'<div class="bullet_list"><ul>' .
'<li><strong>How do I add the Bookmark tool to my browser?</strong> Go to the Post a Story tab and drag the orange "Post to Minnesota Daily" button up to the links bar in your browser.'. 
'<li><strong>How do I allow '.SITE_TITLE.' to send me occasional email?</strong> Go to "Settings" and click on "Would you like to receive email from us through facebook? (50 pts)" and wait for the magic to happen.'.
'<li><strong>How do I get SMS/text messages from '.SITE_TITLE.'?</strong> Click on "Settings" and then "Would you like to receive sms notifications from us through facebook? (50 pts)" and follow the instructions.'.	
          '</ul></div><!--end "bullet_list"-->';	
	
$category='faq';
$static = $dynTemp->useDBTemplate('FAQ',$static,'',false, $category);
	
?>