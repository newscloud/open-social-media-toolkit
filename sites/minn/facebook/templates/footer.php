<?php
	$footer='<div id="footer" class="clearfix">'.
    '<div class="dh_links">'.
	'<a class="rss_link" target="_blank" href="'.URL_RSS.'" onclick="quickLog(\'extLink\',\'footer\',0,\''.URL_RSS.'\');" title="Follow our story updates via RSS">RSS</a>';
	if (USE_TWITTER)
		$footer.='<span class="pipe">|</span><a target="_blank" class="twitter_link" onclick="quickLog(\'extLink\',\'footer\',0,\'http://www.twitter.com/'.TWITTER_USER.'\');" href="http://www.twitter.com/'.TWITTER_USER.'" title="Follow our updates via Twitter">Twitter</a>';
	$footer.='<span class="pipe">|</span><a class="newscloud_link" href="http://www.newscloud.com" onclick="quickLog(\'extLink\',\'footer\',0,\'http://www.newscloud.com\');" title="Visit the NewsCloud Web site" target="_blank">NewsCloud.com</a><span class="pipe">'.
//	'|</span><a href="http://www.umn.edu" onclick="quickLog(\'extLink\',\'footer\',0,\'http://www.umn.edu\');" target="_blank">University of Minnesota</a>'.
    '<span class="pipe">|</span><a href="http://www.mndaily.com" onclick="quickLog(\'extLink\',\'footer\',0,\'http://www.mndaily.com\');" target="_blank">mndaily.com</a>'.
	'<span class="pipe">|</span><a href="?p=faq" onclick="switchPage(\'static\',\'faq\');return false;">FAQ</a><span class="pipe">|</span><a href="http://blog.newscloud.com/open-source.html" target="_blank">Developers</a><span class="pipe">|</span><a href="?p=tos" onclick="switchPage(\'static\',\'tos\');return false;">Terms of Use</a><span class="pipe">|</span><a href="?p=about" onclick="switchPage(\'static\',\'about\');return false;">About</a>';
	if ($isConsole)
		$footer .= '<span class="pipe">|</span><a href="http://minn.newsi.us/?p=console&e='.htmlentities($email).'&a='.htmlentities($actCode).'">Admin</a>';
	if ($isResearch)
		$footer .= '<span class="pipe">|</span><a href="http://research.newscloud.com/index.php?e='.htmlentities($email).'&a='.htmlentities($actCode).'">Research</a>';
    $footer.='</div><!--end "dh_links"--></div><!--end "footer"-->'.
    '<a class="powered" href="http://blog.newscloud.com/services.html" onclick="quickLog(\'extLink\',\'footer\',0,\'http://blog.newscloud.com/services.html\');" target="_blank" title="Learn about NewsCloud\'s technology">Powered by the<br /><span class="label">NewsCloud</span><br />social media toolkit</a><!--end "powered"-->';
?>