<?php





function showNotificationForm($userids, $prizeid, $winEmail,$winNot) // uses unprocessed templates
{
	
	?>
	<hr>
<h2>Edit Mail template:</h2>
<form method="post" action="index.php?p=console&group=street_team&action=award_prize&mail">
<input type="hidden" name="userids" value="<?php echo implode(',',$userids); ?>"/>
<input type="hidden" name="prizeid" value="<?php echo $prizeid; ?>"/>

To:<br />
<input type="text" name="to" size="70" style="width: 70%;" value="<?php echo $winEmail->email; ?>"/>
<br />
From:<br />
<input type="text" name="from" size="70" style="width: 70%;" value="<?php echo $winEmail->from; ?>"/>
<br />
Bcc:<br />
<input type="text" name="bcc" size="70" style="width: 70%;" value="<?php echo $winEmail->bcc; ?>"/>
<br />

Subject:<br />
<input type="text" name="subject" size="70" style="width: 70%;" value="<?php echo $winEmail->subject; ?>"/>

<br />
Body:
<br />
<textarea name="body" rows="10" cols="80" style="width: 70%;"><?php echo $winEmail->body; ?></textarea>
<br />


<h2>Edit Facebook Notification Body:</h2>	
<!-- <input type="hidden" name="fbId" value="<?php echo $winNot->fbId; ?>" />-->
<br />
<textarea name="notificationBody" rows="10" cols="80" style="width: 70%;"><?php echo $winNot->body; ?></textarea>
<br />

<input type="submit" value="Mail & Notify User" />
<br /> 
</form>
	
	<?php 
	
}


function showUsersToBeNotified($userids)
{ 
	echo "<p>";
	echo "Users to be awarded & notified:<br>";
	require_once(PATH_CORE .'/classes/user.class.php');				
	$ut = new UserTable($db);
	$uit = new UserInfoTable($db);
	$user = $ut->getRowObject();
	
	foreach ($userids as $userid)
	{
		$user->load($userid);
		echo "$user->name <br>";
	}
	echo "</p>";
	
}


function logWinner($userid, $prizeid)
{
	require_once(PATH_CORE .'/classes/log.class.php');
		$log = new log();
		$db = $log->db;
		$log->update($log->serialize(0, $userid, 'wonPrize', $prizeid, 0)); 				
		
}

function awardUsers($userids, $prizeid)
{
	foreach ($userids as $userid)
	{
		logWinner($userid, $prizeid);
	}
	
}

// not doing this atm
function sendContactEmail($userid, $prizeid, $subject, $message)
{
	/////////////////////////////
	// send a contact email to us for reference even if we dont email the user right away
	require_once(PATH_CORE.'/classes/contactEmails.class.php');
	$cet = new ContactEmailTable($log->db);
	$contactObj = $cet->getRowObject();
	
	$contactObj->email = 'console@newscloud.com';
	$contactObj->subject = 'Prize Award Link -'.$userid.'-'.$prizeid;
	$contactObj->message = mysql_real_escape_string($message); 
	$contactObj->userid = $userid;
	$contactObj->date = date("Y-m-d H:i:s", time());
	$contactObj->topic = 'team';
	
	//echo '<pre>'.print_r($contactObj,true).'</pre>';
	//echo 'Insert result: '. $contactObj->insert();
		
	
	
}

function getTemplates(&$winEmail, &$winNot)
{
	require_once(PATH_CORE .'/classes/template.class.php');
	$templateObj = new template($db);
	$templateObj->registerTemplates('FACEBOOK','winEmail');
	
	$winEmail->subject = $templateObj->templates['subject'];	
	$winEmail->body = $templateObj->templates['body'];
	$winEmail->from = $templateObj->templates['from'];
	$winEmail->bcc = $templateObj->templates['bcc'];
	
	$winNot->body = $templateObj->templates['notificationBody'];
	
	
	
}


function notifyUser($userid, $prizeid, $winEmailTemplate, $winNotTemplate)
{
	$claimURL = URL_CANVAS."?p=redeem&id=$prizeid";
	$claimLink = "<a href='".$claimURL."'>".$claimURL."</a>";


	$message .= "Prize $prizeid awarded to user $userid. ";
	$message .= "User needs to use following link to claim their prize: ".$claimLink;

	//////////////////////////
	// set up data to notify user
	require_once(PATH_CORE .'/classes/user.class.php');				
	$ut = new UserTable($db);
	$uit = new UserInfoTable($db);
	$user = $ut->getRowObject();
	$user->load($userid);
	require_once(PATH_CORE .'/classes/prizes.class.php');
	$pt = new PrizeTable($db);
	$prize= $pt->getRowObject();
	$prize->load($prizeid);
	
	$nameWords = explode(" ", $user->name);
	
	require_once(PATH_CORE .'/classes/template.class.php');
	$templateObj = new template($db);
	
	$columns['prizeTitle']=$prize->title;
	$columns['claimURL']=$claimURL;
	$columns['firstName']=$nameWords[0];
		
	$callbacks = array();
	
	$winEmail=clone($winEmailTemplate); // i hate php 
	$winNot = clone($winNotTemplate);
	// construct an email for this specific user 
	$winEmail->email = $user->email;	
	$winEmail->subject = $templateObj->processRow($columns, $winEmailTemplate->subject,$callbacks);	
	$winEmail->body = $templateObj->processRow($columns,$winEmailTemplate->body,$callbacks);
	
	// construct notification info
	$winNot->body = $templateObj->processRow($columns, $winNotTemplate->body,$callbacks);
	$fbIds = $uit->getFbIdsForUsers(array($user->userid));
	$winNot->fbId = $fbIds[0];
	//$mailtoText = "mailto:$winEmail->email?subject=$winEmail->subject&body=$winEmail->body";
	
	// do send notifications
	echo "Mailing $winEmail->email...";
	sendEmail($winEmail);
	echo "Notifying $winNot->fbId...";
	sendNotification($winNot);
}

function sendEmail($mailObj)
{
	if (do_mail($mailObj) )
	{
		echo "Mail Sent successfully: $mailObj->email, $mailObj->subject<br>"; //, back to <a href="index.php?p=console&group=street_team&action=leaders">Leaders</a>.';
	} else
	{
		echo 'Failed to send mail! Back to <a href="index.php?p=console&group=street_team&action=leaders">Leaders</a>.'; 
		echo '<pre>'.print_r($mailObj,true).'</pre>';
	}
	
	// hack since Bcc isnt working
	$mailObjBcc = $mailObj; $mailObjBcc->email=$mailObj->bcc; // not copying on write!?!
		
	do_mail($mailObjBcc);
	echo "bcc: $mailObjBcc->email<br/>";
	// debug
	//$mailObjDebug = $mailObj; $mailObjDebug->email='dmacd10@gmail.com';		
	//do_mail($mailObjDebug);
	
		
	
}

function do_mail($mailObj)
{
	
	$to      = $mailObj->email;
	$subject = $mailObj->subject;
	$message = wordwrap($mailObj->body, 70);
	$headers = 'From: '.$mailObj->from . "\r\n" .
	    'Reply-To: '.$mailObj->from . "\r\n" .
	    //'Bcc: '.$mailObj->bcc . "\r\n" . // BCC not working, no idea why 
		'X-Mailer: PHP/' . phpversion();
	
	return mail($to, $subject, $message, $headers);
}

function sendNotification($notObj)
{
	/* initialize the SMT Facebook appliation class, NO Facebook library */
	require_once PATH_FACEBOOK."/classes/app.class.php";
	$app=new app(NULL,true);
	$facebook=&$app->loadFacebookLibrary();				
	$apiResult=$facebook->api_client->notifications_send($notObj->fbId,$notObj->body, 'app_to_user'); 	

	echo '<p>Facebook Notification returned '.'<pre>'.print_r($apiResult,true).'</pre></p>';
	
}

function notifyUsers($userids, $prizeid, $winEmail, $winNot) 
{
	
	foreach($userids as $userid)
	{
		
		
		notifyUser($userid, $prizeid, $winEmail, $winNot); 
		//echo $message;
		//sendContactEmail() // would go here if we were doing it that way
			
		
	}	
	
	
}




if (!isset($_GET['mail']))
{
	// userids and prizeid passed in from console 
	
	$userids = explode(',',$userids);
	
	showUsersToBeNotified($userids);
	$winEmail = new stdClass;
	$winNot = new stdClass;
	getTemplates(&$winEmail, &$winNot);
	showNotificationForm($userids, $prizeid, $winEmail, $winNot);
	

} else
{
	
	$userids = explode(',', $_POST['userids']);
	$prizeid = $_POST['prizeid'];
	
	$mailObj->email = $_POST['to'];
	$mailObj->from = $_POST['from'];
	$mailObj->subject = $_POST['subject'];
	$mailObj->body = $_POST['body'];
	$mailObj->bcc=$_POST['bcc'];
	
	$notObj->fbId = $_POST['fbId'];
	$notObj->body = $_POST['notificationBody'];
	
	echo "Awarding ".implode(',',$userids)."...<br/>";
	awardUsers($userids, $prizeid);

	echo "Notifying ".implode(',',$userids)."...<br/>";
	
	notifyUsers($userids, $prizeid, $mailObj, $notObj);
}






/*
//////////////////////////////////////////////
// old
if (!isset($_GET['mail']))
{
	require_once(PATH_CORE .'/classes/log.class.php');
	$log = new log();
	$db = $log->db;
	$log->update($log->serialize(0, $userid, 'wonPrize', $prizeid, 0)); 				

	$claimURL = URL_CANVAS."?p=redeem&id=$prizeid";
	$claimLink = "<a href='".$claimURL."'>".$claimURL."</a>";


	$message .= "Prize $prizeid awarded to user $userid. ";
	$message .= "User needs to use following link to claim their prize: ".$claimLink;
	
	echo $message;
	
	/////////////////////////////
	// send a contact email to us for reference even if we dont email the user right away
	require_once(PATH_CORE.'/classes/contactEmails.class.php');
	$cet = new ContactEmailTable($log->db);
	$contactObj = $cet->getRowObject();
	
	$contactObj->email = 'console@newscloud.com';
	$contactObj->subject = 'Prize Award Link -'.$userid.'-'.$prizeid;
	$contactObj->message = mysql_real_escape_string($message); 
	$contactObj->userid = $userid;
	$contactObj->date = date("Y-m-d H:i:s", time());
	$contactObj->topic = 'team';
	
	//echo '<pre>'.print_r($contactObj,true).'</pre>';
	//echo 'Insert result: '. $contactObj->insert();
	
	
	//////////////////////////
	// set up data to notify user
	require_once(PATH_CORE .'/classes/user.class.php');				
	$ut = new UserTable($db);
	$uit = new UserInfoTable($db);
	$user = $ut->getRowObject();
	$user->load($userid);
	require_once(PATH_CORE .'/classes/prizes.class.php');
	$pt = new PrizeTable($db);
	$prize= $pt->getRowObject();
	$prize->load($prizeid);
	
	$nameWords = explode(" ", $user->name);
	
	require_once(PATH_CORE .'/classes/template.class.php');
	$templateObj = new template($db);
	$templateObj->registerTemplates('FACEBOOK','winEmail');
	
	$columns['prizeTitle']=$prize->title;
	$columns['claimURL']=$claimURL;
	$columns['firstName']=$nameWords[0];
		
	$callbacks = array();
	// construct an email 
	$winEmail->email = $user->email;	
	$winEmail->subject = $templateObj->processRow($columns, $templateObj->templates['subject'],$callbacks);	
	$winEmail->body = $templateObj->processRow($columns,$templateObj->templates['body'],$callbacks);
	$winEmail->from = $templateObj->templates['from'];
	$winEmail->bcc = $templateObj->templates['bcc'];
	
	// construct notification info
	$winNot->body = $templateObj->processRow($columns, $templateObj->templates['notificationBody'],$callbacks);
	$fbIds = $uit->getFbIdsForUsers(array($user->userid));
	$winNot->fbId = $fbIds[0];
	//$mailtoText = "mailto:$winEmail->email?subject=$winEmail->subject&body=$winEmail->body";
	
	//$message .= 'Click <a href="'.$mailtoText.'">here</a> to send mail to '. $user->name;
	
	///////////////////////////////////////////////
	// mail form
	echo "<p>Use the form below to send an email to this user now.</p>";
?>	
<hr>
<h2>Mail User</h2>
<form method="post" action="index.php?p=console&group=street_team&action=award_prize&mail">
<input type="hidden" name="userid" value="<?php echo $userid; ?>"/>
<input type="hidden" name="prizeid" value="<?php echo $prizeid; ?>"/>

To:<br />
<input type="text" name="to" size="70" style="width: 70%;" value="<?php echo $winEmail->email; ?>"/>
<br />
From:<br />
<input type="text" name="from" size="70" style="width: 70%;" value="<?php echo $winEmail->from; ?>"/>
<br />
Bcc:<br />
<input type="text" name="bcc" size="70" style="width: 70%;" value="<?php echo $winEmail->bcc; ?>"/>
<br />

Subject:<br />
<input type="text" name="subject" size="70" style="width: 70%;" value="<?php echo $winEmail->subject; ?>"/>

<br />
Body:
<br />
<textarea name="body" rows="10" cols="80" style="width: 70%;"><?php echo $winEmail->body; ?></textarea>
<br />


Facebook Notification Body	
<input type="hidden" name="fbId" value="<?php echo $winNot->fbId; ?>" />
<br />
<textarea name="notificationBody" rows="10" cols="80" style="width: 70%;"><?php echo $winNot->body; ?></textarea>
<br />

<input type="submit" value="Mail & Notify User" />
<br /> 
</form>
	
	
	
<?php 
	

} else
{
	
	$mailObj->email = $_POST['to'];
	$mailObj->from = $_POST['from'];
	$mailObj->subject = $_POST['subject'];
	$mailObj->body = $_POST['body'];
	$mailObj->bcc=$_POST['bcc'];
	
	$notObj->fbId = $_POST['fbId'];
	$notObj->notificationBody = $_POST['notificationBody'];
	
	
	// hack: bcc isnt working, bcc manually
	
	if (do_mail($mailObj) )
	{
		echo 'Mail Sent successfully, back to <a href="index.php?p=console&group=street_team&action=leaders">Leaders</a>.';
	} else
	{
		echo 'Failed to send mail! Back to <a href="index.php?p=console&group=street_team&action=leaders">Leaders</a>.'; 
		echo '<pre>'.print_r($mailObj,true).'</pre>';
	}
	
	// hack since Bcc isnt working
	$mailObjBcc = $mailObj; $mailObjBcc->email=$mailObj->bcc; // not copying on write!?!	
	do_mail($mailObjBcc);
	
	// debug
	//$mailObjDebug = $mailObj; $mailObjDebug->email='dmacd10@gmail.com';		
	//do_mail($mailObjDebug);
	
	// initialize the SMT Facebook appliation class, NO Facebook library 
	require_once PATH_FACEBOOK."/classes/app.class.php";
	$app=new app(NULL,true);
	$facebook=&$app->loadFacebookLibrary();				
	$apiResult=$facebook->api_client->notifications_send($notObj->fbId,$notObj->notificationBody, 'app_to_user'); 	

	
	echo '<p>Facebook Notification returned '.'<pre>'.print_r($apiResult,true).'</pre></p>';
	
	
}


/*/





?>