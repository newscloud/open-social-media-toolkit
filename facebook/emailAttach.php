<?php
	/* Manage email attachments */
	$init=parse_ini_file($_SERVER['DOCUMENT_ROOT'].'../genomics.ini');
	$api_key = $init['fbAPIKey'];
	$secret  = $init['fbSecretKey'];
	// the facebook client library
	include_once '../facebook/facebook.php';		
	$fbLib = new Facebook($api_key, $secret);
	require_once "../classes/fbApp.class.php";
	$fbApp=new fbApp($fbLib);

	$code='';
	if (!array_key_exists('message_sent', $_POST) || $_POST['message_sent'] < 1)
  {
  	//if attaching, display the gene selector
  	if (isset($_POST['giftid']))
		$giftid=$_POST['giftid']; 
	else
		$giftid=0;
	$code.=$fbApp->buildAttchmentSelector($giftid);  
  }

  //display attachment for live and preview
  if (array_key_exists('giftid', $_POST)) {
  		$giftid=$_POST['giftid'];
	    $code.=$fbApp->buildAttachment($giftid);
	    $code.='<div style="float:left;font-weight:bold;"><fb:if-user-has-added-app>'.$fbApp->makeFancyTitle('<a href="'.$fbApp->home.'?page=send">Send a Gene Now!</a>','100%').'<fb:else><p><a href="'.$fbApp->home.'?page=postAdd">'.$fbApp->makeFancyTitle('Add the Genomics application to your profile','100%').'</a></p></fb:else> </fb:if-user-has-added-app></div>';
	}
  
  echo $code;
?>
