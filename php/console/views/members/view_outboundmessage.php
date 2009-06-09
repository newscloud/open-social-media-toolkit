<div class="yui-g">
<div class="outboundmessage">
<h2>Member Details: <? echo $outboundmessage['name']; ?></h2>

<p id="outboundmessage[msgType]">Message Type: <?php echo htmlentities($outboundmessage['msgType']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[status]">Status: <?php echo htmlentities($outboundmessage['status']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[userGroup]">User Group: <?php echo htmlentities($outboundmessage['userGroup']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[userid]">User ID: <?php echo link_for($outboundmessage['userid'], 'members', 'view_outboundmessage', $outboundmessage['userid']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[subject]">Subject: <?php echo htmlentities($outboundmessage['subject']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[shortLink]">Short Link: <?php echo htmlentities($outboundmessage['shortLink']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[buttonLinkText]">Button Link Text: <?php echo htmlentities($outboundmessage['buttonLinkText']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[closingLinkText]">Closing Link Text: <?php echo htmlentities($outboundmessage['closingLinkText']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[userIntro]">User Intro: <?php echo htmlentities($outboundmessage['userIntro']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[msgBody]">Message Body: <?php echo htmlentities($outboundmessage['msgBody']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[t]">Date Created: <?php echo htmlentities($outboundmessage['t']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[numUsersReceived]">Number of Users Notified: <?php echo htmlentities($outboundmessage['numUsersReceived']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[numUsersExpected]">Number of Users Expected: <?php echo htmlentities($outboundmessage['numUsersExpected']); ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new outboundmessage', 'ctrl' => 'members', 'action' => 'new_outboundmessage'),
	array('title' => 'Edit this outboundmessage', 'ctrl' => 'members', 'action' => 'modify_outboundmessage', 'id' => $outboundmessage['id']),
	array('title' => 'Delete this outboundmessage', 'ctrl' => 'members', 'action' => 'destroy_outboundmessage', 'id' => $outboundmessage['id']),
    array('title' => 'Send this outboundmessage', 'ctrl' => 'members', 'action' => 'send_outboundmessage', 'id' => $outboundmessage['id'], 'onclick' => "if(!confirm('Are you sure you want to send this message?')) return false"),
    array('title' => 'Send Preview', 'ctrl' => 'members', 'action' => 'send_outboundmessage', 'id' => $outboundmessage['id'], 'onclick' => "if(!confirm('Are you sure you want to send (preview) this message?')) return false", 'extra_params' => array('preview' => 'true')),
	array('title' => 'Back to outboundmessages', 'ctrl' => 'members', 'action' => 'outboundmessages'),
);

if (($links = build_link_list($link_list))) {
	echo $links;
}

?>
</div>
