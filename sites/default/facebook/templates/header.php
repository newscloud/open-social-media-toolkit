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
	$shareButton = $dynTemp->useDBTemplate('HeaderShareButton',
	'<fb:share-button class="meta"><meta name="title" content="Try the '.SITE_TITLE.' Facebook Application"/><meta name="description" content="A new community application in Facebook covering the '.SITE_TOPIC.' beat" /><link rel="image_src" href="'.URL_CALLBACK.'?p=cache&simg=bg_team.gif"/> <link rel="target_url" href="'.$shareLink.'"/></fb:share-button>',
	'', false, 'header');
/* example of header with background banner instead of h1 - see also #header definition in sites/facebook/styles/default.css
$header='<div id="header">'.
'<a href="?p=home" onclick="switchPage(\'home\');return false;"><img src="'.URL_CALLBACK.'?p=cache&img=spacer.gif" width="300" height="76" class="float_left" /></a>'.
'<div class="dh_links">'.
*/
	$header='<div id="header"><h1><a href="?p=home" onclick="switchPage(\'home\');return false;">'.SITE_TITLE.'</a></h1>'.'<div class="dh_links">'.
'<div style="float:left;padding:0px 5px 0px 0px;display:inline;">'.$shareButton.'<!-- end share button wrap --></div>'.
$signup.$pipe.'<a href="'.URL_CANVAS.'?p=invite" onclick="switchPage(\'invite\');return false;">Invite Friends</a>';
	if (defined('ENABLE_LINKS'))
		$header.=$pipe.'<a href="'.URL_CANVAS.'?p=links" onclick="switchPage(\'links\');return false;">'.SITE_TOPIC.' Resources</a>';
	$header.=$settings.$pipe.'<a href="'.URL_CANVAS.'?p=contact" onclick="switchPage(\'contact\');return false;">Contact us</a>'
.'</div><!--end "dh_links"-->';
	if (defined('TABS_SIMPLE') AND method_exists($this,'buildPageTabs')) $header.=$this->buildPageTabs($pageName,true,!isset($_POST['fb_sig_logged_out_facebook']));
	$header.='</div><!--end "header"-->';
if (isset($_POST['fb_sig_logged_out_facebook'])) $header= preg_replace('/on[cC]lick="[^"]+"/', '', $header); // remove script
if (defined('ADS_HOME_LARGE_BANNER')) {
	$header=str_replace("{ad}",'<fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=homeLargeBanner" frameborder="0" scrolling="no" style="width:768px;height:100px;padding:0px;margin:0px;"/>',$this->common['adWrapLargeBanner']).$header;
}			

?>

