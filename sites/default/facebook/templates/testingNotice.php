<?php
	$static='<h1>'.SITE_TITLE.' is in Testing Mode</h1>';	
	$static.='<p>Our Daily Action Team promotion has not yet started. Any points you accumulate are fake and will be reset at the start of the official promotion. No prizes are available yet </p>';
	$static.='<p>Please report bugs through <a href="?p=contact" onclick="switchPage(\'contact\');return false;"">our Contact page</a>. If you cannot sign in, send them to <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a></p>';
	$static.='<br /><br />';
	
	$category='testing';
    $static = $dynTemp->useDBTemplate('TestingNotice',$static,'',false, $category);
	
?>