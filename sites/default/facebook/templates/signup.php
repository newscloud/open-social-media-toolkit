<?php
	/* FACEBOOK Module Templates for SignUp Page */
$cat = 'signup';
	$this->addTemplateDynamic($dynTemp, 'heading','<h1>Join the '.SITE_TITLE.' community on Facebook!</h1><h5>Stay informed about '.SITE_TOPIC.'!</h5>', '',$cat);
	$this->addTemplateDynamic($dynTemp, 'whyJoin', 
	'<div id="teamPanel"><div id="teamIcon">'.
	'<h1>Stay informed about '.SITE_TOPIC.' news and connect with others on Facebook! <a href="?p=team" class="more_link">&hellip;&nbsp;Learn more</a></h1>'
      .'<ul class="bullet_list">'
	//.'<li>Earn rewards and become more active in campus life</li>'
	.'<li>Earn points for participating, hit the leader board</li>'
	.'<li>Contribute to a research study</li>'
	.'</ul>'
	.'<p>We\'ll even start you off with a 200 point bonus just for joining!</p>'   
	.	'</div><!--end "teamIcon"--></div><!--end "teamPanel"-->','',$cat);
	
	$this->addTemplateDynamic($dynTemp, 'intro','<div id="introPanel"><p>Technology powering '.SITE_TITLE.' is funded in part by a <a href="http://www.newscloud.com/research" class="more_link" onclick="quickLog(\'extLink\',\'signup\',0,\'http://www.newscloud.com/research\');" target="_blank">not for profit research study sponsored by the Knight Foundation</a> to find new ways of engaging young people in news readership and community engagement.</p><!-- end of introPanel --></div>','',$cat);	
?>