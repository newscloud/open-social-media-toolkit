<?php

$static='<br /><h1>'.SITE_TITLE.' Action Team Rules Summary</h1><h5>While not official, the following summary gives you a basic intro:</h5>';
    $static.='<p>Be sure to read the complete list of <a href="?p=tos" onclick="switchPage(\'static\',\'tos\');return false;">official rules for the '.SITE_TITLE.' Action Team</a> before you sign up.</p>';
    $static.='<div class="bullet_list">
          <ul>' .
          '<li>Participation is voluntary. If you don\'t like the rules, put down the mouse and walk away.</li>'.
          '<li>No purchase is necessary.</li>' .
          '<li>The Daily Action Team is scheduled to run from March 25, 2009 to May 12, 2009.</li>' .
          '<li>Like on Facebook, much of what you post, share, or say on Minnesota Daily application will appear on Minnesota Daily, so don\'t share something you don\'t want people to see.</li>' .
          '<li>Info from Minnesota Daily will be shared with Mndaily.com and NewsCloud. Stories and comments you post will appear on the NewsCloud.com website.</li>' .
          '<li>The Daily Action Team is part of a voluntary research study of 16- to 25-year-olds conducted by University of Minnesota researchers. Participation is optional, and before you decide whether to participate, please read the <a href="'.URL_CANVAS.'?p=consent" target="_blank" onclick="switchPage(\'static\',\'consent\');return false;">consent form</a>.</li>' .
          '<li>Earn points by participating in the Minnesota Daily site and completing challenges on- and offline. Always stay safe -- you\'re the only one responsible for your actions.</li>' .
          '<li>In order to be eligible for rewards, participants must be United States residents between the ages of 16 to 25 years old. Minors will be required to provide a consent form from a parent or guardian.</li>' .
          '<li>Redemption of some rewards may require that you (or your parents, if you\'re a minor) sign an additional consent form.</li>' .
          '<li>Periodically, we\'ll offer reward(s) to the eligible members with the highest point totals in their category.</li>' .
          '<li>None of the above is legally binding; you have to read the <a href="?p=tos" onclick="switchPage(\'static\',\'tos\');return false;">official rules</a> for that stuff.</li>' .
            '</ul></div><!--end "bullet_list"-->';
    $static.='<p>Check out our <a href="?p=faq" onclick="switchPage(\'static\',\'faq\');return false;">FAQ</a> for more information. If you don\'t see your question there, please <a href="?p=contact" onclick="switchPage(\'contact\');return false;">contact us</a>. </p><br />';
	require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
	$this->teamObj=new actionTeam($this->page);				
	$static.=$this->teamObj->fetchLegend('full');
?>