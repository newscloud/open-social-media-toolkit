<div class="yui-g">
<h1>Member Email: <? echo $member_email['subject']; ?></h1><br /><br />

<div class="member_email">

<p>From: <? echo $member_email['email']; ?></p>
<p>Userid: <? echo $member_email['userid']; ?></p>
<p>Date: <? echo $member_email['date']; ?></p>
<p>Subject: <? echo $member_email['subject']; ?></p>
<p>Topic: <? echo $member_email['topic']; ?></p>
<p>Message: <? echo $member_email['message']; ?></p>
<p>Read?: <? echo ($email['is_read']) ? '<span class="read_email" style="color: green;">read</span>' : '<span class="unread_email" style="color: red;">unread</span>'; ?></p>
<p>Replied?: <? echo ($email['replied']) ? '<span class="replied_email" style="color: green;">replied</span>' : '<span class="unreplied_email" style="color: red;">unreplied</span>'; ?></p>
</div>

<br /><br />
<p>
<?php echo link_for('Return to Member Emails', 'members', 'member_emails'); ?>
</p>

</div>
