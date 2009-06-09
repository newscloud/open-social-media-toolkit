Message Type<br />
<select name="outboundmessage[msgType]">
<option value="notification"<? if ($outboundmessage['msgType'] == 'notification') echo ' selected'; ?>>Facebook Notification</option>
<option value="announce"<? if ($outboundmessage['msgType'] == 'announce') echo ' selected'; ?>>Facebook Email (not implemented)</option>
</select>
<div class="spacer"></div>

Status<br />
<select name="outboundmessage[status]">
<option value="pending"<? if ($outboundmessage['status'] == 'pending') echo ' selected'; ?>>Pending</option>
<option value="sent"<? if ($outboundmessage['status'] == 'sent') echo ' selected'; ?>>Sent</option>
<option value="hold"<? if ($outboundmessage['status'] == 'hold') echo ' selected'; ?>>Hold</option>
<option value="incomplete"<? if ($outboundmessage['status'] == 'incomplete') echo ' selected'; ?>>Incomplete</option>
</select>
<div class="spacer"></div>

User Group<br />
<select name="outboundmessage[userGroup]">
<option value="all"<? if ($outboundmessage['userGroup'] == '') echo ' selected'; ?>>All visitors</option>
<option value="members"<? if ($outboundmessage['userGroup'] == 'User.isMember = 1') echo ' selected'; ?>>Registered Members</option>
<option value="nonmembers"<? if ($outboundmessage['userGroup'] == 'User.isMember = 0') echo ' selected'; ?>>Non Members: all</option>
<option value="teampotential"<? if ($outboundmessage['userGroup'] == "User.isMember = 0 AND UserInfo.age<=25 AND UserInfo.age>=16 AND User.eligibility<>'ineligible'") echo ' selected'; ?>>Non members: aged 16-25</option>
<option value="team"<? if ($outboundmessage['userGroup'] == "User.eligibility='team'") echo ' selected'; ?>>Inside study group</option>
<option value="general"<? if ($outboundmessage['userGroup'] == "User.eligibility='general'") echo ' selected'; ?>>Outside study group</option>
<option value="admin"<? if ($outboundmessage['userGroup'] == 'User.isAdmin = 1') echo ' selected'; ?>>Admin Preview</option>
</select>
<div class="spacer"></div>

User ID<br />
<input type="text" name="outboundmessage[userid]" value="<?php echo htmlentities((isset($outboundmessage['userid'])) ? $outboundmessage['userid'] : $_SESSION['userid']); ?>" />
<div class="spacer"></div>

Subject<br /> (not used for notifications)<br />
<input type="text" name="outboundmessage[subject]" value="<?php echo htmlentities($outboundmessage['subject']); ?>" />
<div class="spacer"></div>

Short Link<br /> (not used for notifications)<br />
<input type="text" name="outboundmessage[shortLink]" value="<?php echo htmlentities($outboundmessage['shortLink']); ?>" />
<div class="spacer"></div>

Button Link Text<br /> (not used for notifications)<br />
<input type="text" name="outboundmessage[buttonLinkText]" value="<?php echo htmlentities($outboundmessage['buttonLinkText']); ?>" />
<div class="spacer"></div>

Closing Link Text<br /> (not used for notifications)<br />
<input type="text" name="outboundmessage[closingLinkText]" value="<?php echo htmlentities($outboundmessage['closingLinkText']); ?>" />
<div class="spacer"></div>

User Intro<br /> (not used for notifications)<br />
<input type="text" name="outboundmessage[userIntro]" value="<?php echo htmlentities($outboundmessage['userIntro']); ?>" />
<div class="spacer"></div>

Message Body<br />                                                                        
<textarea name="outboundmessage[msgBody]" cols="50" rows="10"><?php echo htmlentities($outboundmessage['msgBody']); ?></textarea>
<div class="spacer"></div>
                                                                                         
Date Created<br />
<input type="text" name="outboundmessage[t]" value="<?php echo htmlentities($outboundmessage['t']); ?>" />
<div class="spacer"></div>
