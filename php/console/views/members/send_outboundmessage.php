<?php if ($outboundmessage['msgType'] == 'announcement'): ?>
	<img width="760" height="78" class="float_left" src="http://host.newscloud.com/sites/climate/facebook/index.php?p=cache&simg=bg_banner.jpg"/>

	<h1 style="color: green;"><?php echo $outboundmessage['userIntro']; ?></h1>

	<p style="color: black;"><?php echo $outboundmessage['msgBody']; ?></p>
<?php elseif ($outboundmessage['msgType'] == 'notification'): ?>
	<?php echo $outboundmessage['msgBody']; ?>
<?php else: ?>
<?php endif; ?>
