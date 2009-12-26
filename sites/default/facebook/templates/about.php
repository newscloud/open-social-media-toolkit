<?php
$category ='about';
$static.=$dynTemp->useDBTemplate('AboutText', 

'<h1>About '.SITE_TITLE.'</h1><p>'.SITE_TITLE.' brings the power of community to news creating a sharp, addictive news application for Facebook users all around town.</p><p>'.SITE_TITLE.' is powered in part by technology funded by the <a href="http://www.knightfoundation.org" target="_blank">John S. and James L. Knight Foundation</a>.</p>

<h2>About NewsCloud</h2>
<p>
Launched in 2005, <a onclick="quickLog(\'extLink\',\'about\',0,\'http://blog.newscloud.com\');" href="http://blog.newscloud.com" target="_blank">NewsCloud</a> is an open source solutions provider for Facebook. Visit <a onclick="quickLog(\'extLink\',\'about\',0,\'http://opensource.newscloud.com\');" href="http://opensource.newscloud.com" target="_blank">www.newscloud.com/research</a> for more information.</p>

<h2>About the John S. and James L. Knight Foundation</h2>
<p><a onclick="quickLog(\'extLink\',\'about\',0,\'http://www.knightfoundation.org\');" href="http://www.knightfoundation.org" target="_blank"><img border="0" src="'.URL_CALLBACK.'?p=cache&img=kf_logo_color_250px.jpg" style="float:right;" alt="Knight Foundation Logo" /></a>
The <a onclick="quickLog(\'extLink\',\'about\',0,\'http://www.knightfoundation.org\');" href="http://www.knightfoundation.org" target="_blank">John S. and James L. Knight Foundation</a> invests in journalism excellence worldwide and in the vitality of U.S. communities where the Knight brothers owned newspapers. Since 1950, the foundation has granted more than $400 million to advance quality journalism and freedom of expression. Knight Foundation focuses on projects with the potential to create transformational change.<br clear="all"/></p>'
	, '', false, $category);
	
	$static.=$dynTemp->useDBTemplate('SiteCredits','<h2>Site Credits</h2>'.
'<p>Design by <a onclick="quickLog(\'extLink\',\'about\',0,\'http://youreyelevel.com/smt/\');" href="http://youreyelevel.com/smt/">EyeLevel</a></p><p>Open source libraries used: Platform - <a href="http://www.debian.org/">Linux Debian</a>, <a href="http://www.apache.org/">Apache</a>, <a href="http://www.php.net/">PHP</a>, <a href="http://www.mysql.com/">MySQL</a>, RSS Parsing - <a href="http://simplepie.org">Simple Pie</a>, RSS Creator - Derived from <a href="http://www.feedcreator.org/">Feed Creator</a>, Web services parsing example uses the modified <a target="_cts" href="http://www.engageinteractive.com/mambo/index.php?option=content&task=view&id=3628&Itemid=10159">Saxy Parser</a> from <a href="http://www.phpflickr.com/" target="_cts">PHPFlickr</a>, <a href="http://javascript.crockford.com/jsmin2.php.txt" target="_cts">JSMinLib for PHP</a></p>'
	.'<br /><br />','',false,$category);
	
?>