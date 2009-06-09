<?php
	$pipe='<span class="pipe">|</span>';
	$settings='';
	$shareLink=URL_CANVAS;
	if (!$this->session->isAppAuthorized OR !$this->session->isMember) {
		$signup=$pipe.'<a href="'.URL_CANVAS.'?p=signup'.(isset($_GET['referid'])?'&referid='.$_GET['referid']:'').'" '.(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').'>Sign up</a>';
	} else {		
		$signup='';
		$settings=$pipe.'<a href="'.URL_CANVAS.'?p=account&o=settings" onclick="switchPage(\'account\',\'settings\');return false;">Settings</a>';
		if ($this->session>isLoaded) $shareLink.='?referid='.$this->session->userid;
	}
	// $this->facebook->get_add_url()
	$header='<div id="header">'.
	'<a href="?p=home" onclick="switchPage(\'home\');return false;"><img src="'.URL_CALLBACK.'?p=cache&img=spacer.gif" width="300" height="76" class="float_left" /></a>'.
	'<div class="dh_links">'.
'<div style="float:left;padding:0px 5px 0px 0px;display:inline;"><fb:share-button class="meta"><meta name="title" content="Try the '.SITE_TITLE.' Facebook Application"/><meta name="description" content="Arm yourself with knowledge to save the planet and complete challenges to take real-world environmental action, and reap the rewards with sweet, low-impact prizes" /><link rel="image_src" href="'.URL_CALLBACK.'?p=cache&simg=bg_team.gif"/> <link rel="target_url" href="'.$shareLink.'"/></fb:share-button><!-- end share button wrap --></div>'.
$signup.$pipe.'<a href="'.URL_CANVAS.'?p=invite" onclick="switchPage(\'invite\');return false;">Invite Friends</a>'.
$pipe.'<a href="'.URL_CANVAS.'?p=links" onclick="switchPage(\'links\');return false;">'.SITE_TOPIC.' Resources</a>'.$settings.
$pipe.'<a href="'.URL_CANVAS.'?p=contact" onclick="switchPage(\'contact\');return false;">Contact us</a>'
.'</div><!--end "dh_links"-->'
.'</div><!--end "header"-->';
if (isset($_POST['fb_sig_logged_out_facebook'])) $header= preg_replace('/on[cC]lick="[^"]+"/', '', $header); // remove script
?>