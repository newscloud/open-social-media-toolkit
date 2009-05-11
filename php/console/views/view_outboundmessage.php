<div class="yui-g">
<div class="outboundmessage">
<h2>Member Details: <? echo $outboundmessage['name']; ?></h2>

<p id="outboundmessage[msgType]">Message Type: <?php echo htmlentities($outboundmessage['msgType']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[status]">Status: <?php echo htmlentities($outboundmessage['status']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[userGroup]">User Group: <?php echo htmlentities($outboundmessage['userGroup']); ?></p>
<div class="spacer"></div>

<p id="outboundmessage[userid]">User ID: <a href="index.php?p=console&group=members&action=view_outboundmessage&id=<?php echo htmlentities($outboundmessage['userid']); ?>"><?php echo htmlentities($outboundmessage['userid']); ?></a></p>
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
<ul>
	<li><a href="index.php?p=console&group=members&action=new_outboundmessage">Create a new outboundmessage</a></li>
	<li><a href="index.php?p=console&group=members&action=modify_outboundmessage&id=<? echo $outboundmessage['id']; ?>">Edit this outboundmessage</a></li>
	<li><a href="index.php?p=console&group=members&action=destroy_outboundmessage&id=<? echo $outboundmessage['id']; ?>">Delete this outboundmessage</a></li>
    <li><a href="index.php?p=console&group=members&action=send_outboundmessage&id=<?php echo $outboundmessage['id'] ?>" onclick="if(!confirm('Are you sure you want to send this message?')) return false">Send this outboundmessage</a></li>
    <li><a href="index.php?p=console&group=members&action=send_outboundmessage&id=<?php echo $outboundmessage['id'] ?>&preview=true" onclick="if(!confirm('Are you sure you want to send (preview) this message?')) return false">Send Preview</a></li>
	<li><a href="index.php?p=console&group=members&action=outboundmessages">Back to outboundmessages</a></li>
</ul>
</div>
