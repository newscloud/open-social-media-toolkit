<?php
	$static='<h1 style="text-align:center;">'.SITE_TITLE.' - TERMS OF USE</h1><h5>Important: Please read these rules before joining the '.SITE_TITLE.' (the "'.SITE_TEAM_TITLE.'") sponsored by '.SITE_SPONSOR.' (the "Sponsor"). These rules constitute the "Official Rules" with respect to the '.SITE_TEAM_TITLE.'. By joining the '.SITE_TEAM_TITLE.', you agree to be bound by the Terms of Use, and represent that you satisfy all of the eligibility requirements set forth in these Terms of Use.</h5> 
	<p>Your use of the '.SITE_TITLE.' application and '.SITE_TEAM_TITLE.' is governed by the terms and conditions below. Use of the '.SITE_TEAM_TITLE.' constitutes your acceptance of these terms, which take effect when you sign up for the '.SITE_TEAM_TITLE.'. If you do not agree to these terms, please do not sign up for the '.SITE_TEAM_TITLE.'.

<p>No purchase or payment of any consideration is necessary to become a member of the '.SITE_TEAM_TITLE.'. 

<p>If you have any questions about these Official Rules or the '.SITE_TEAM_TITLE.', please send written questions to '.SUPPORT_EMAIL;

$category='tos';
$static = $dynTemp->useDBTemplate('ToS',$static,'',false, $category);
	
	
?>