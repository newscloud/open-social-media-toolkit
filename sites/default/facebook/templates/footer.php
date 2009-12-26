<?php
	$footerBegin='<div id="footer" class="clearfix">'.
    '<div class="dh_links">'.
	'<a class="rss_link" target="_blank" href="'.URL_RSS.'" onclick="quickLog(\'extLink\',\'footer\',0,\''.URL_RSS.'\');" title="Follow our story updates via RSS">RSS</a>';
	if (USE_TWITTER)
		$twitterFooter.='<span class="pipe">|</span><a target="_blank" class="twitter_link" onclick="quickLog(\'extLink\',\'footer\',0,\'http://www.twitter.com/'.TWITTER_USER.'\');" href="http://www.twitter.com/'.TWITTER_USER.'" title="Follow our updates via Twitter">Twitter</a>';
	$footerMiddle.=
//	'<span class="pipe">|</span><a class="newscloud_link" href="http://www.newscloud.com" onclick="quickLog(\'extLink\',\'footer\',0,\'http://www.newscloud.com\');" title="Visit the NewsCloud Web site" target="_blank">NewsCloud.com</a>'.
// place link to your web site here   '<span class="pipe">|</span><a href="http://www.chitowndailynews.com" onclick="quickLog(\'extLink\',\'footer\',0,\'http://www.chitowndailynews.com\');" target="_blank">chitowndailynews.com</a>'.
	'<span class="pipe">|</span><a href="?p=faq" onclick="switchPage(\'static\',\'faq\');return false;">FAQ</a>'.
	'<span class="pipe">|</span><a href="http://opensource.newscloud.com" target="_blank">Developers</a>'.
	'<span class="pipe">|</span><a href="?p=tos" onclick="switchPage(\'static\',\'tos\');return false;">Terms of Use</a>'.
	'<span class="pipe">|</span><a href="?p=about" onclick="switchPage(\'static\',\'about\');return false;">About</a>';
	
	if ($isConsole)
		$consoleFooter .= '<span class="pipe">|</span><a href="'.URL_CONSOLE.'&e='.htmlentities($email).'&a='.htmlentities($actCode).'">Admin</a>';
	/*
if ($isResearch)
		$consoleFooter .= '<span class="pipe">|</span><a href="http://research.newscloud.com/index.php?e='.htmlentities($email).'&a='.htmlentities($actCode).'">Research</a>';	 * 
	 */

    $footerLower.='</div><!--end "dh_links"--></div><!--end "footer"-->'.
    '<a class="powered" href="http://opensource.newscloud.com" onclick="quickLog(\'extLink\',\'footer\',0,\'http://opensource.newscloud.com\');" target="_blank" title="Learn about NewsCloud\'s technology">Powered by the<br /><span class="label">NewsCloud</span><br />social media toolkit</a><!--end "powered"-->';
    
    $category='footer';
    
    //$footerBegin = $dynTemp->useDBTemplate('footerBegin',$footerBegin,false, $category); 
    //$twitterFooter = $dynTemp->useDBTemplate('twitterFooter',$twitterFooter,false, $category); 
    $footerMiddle = $dynTemp->useDBTemplate('footerMiddle',$footerMiddle,'',false, $category); 
    //$consoleFooter = $dynTemp->useDBTemplate('consoleFooter',$consoleFooter,false, $category);     
    //$footerLower = $dynTemp->useDBTemplate('footerLower',$footerLower,false, $category); 
        
    
    
    
    $footer = $footerBegin.$twitterFooter.$footerMiddle.$consoleFooter.$footerLower;
    
    
    
?>