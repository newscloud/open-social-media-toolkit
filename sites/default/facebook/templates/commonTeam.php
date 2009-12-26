<?php


/*
 * 
// fill in template with blank stuff, marked so we can tell easily what needs to be added


include(PATH_SITE. '../../../climate/facebook/templates/commonTeam.php');

foreach ($commonTeam as $key => $text)
{
	
	$commonTeam[$key] = '*minn*'.$text; //$text;
}
*/



// action team
$category = 'commonTeam';
$commonTeam['JoinTheTeam']= $dynTemp->useDBTemplate('JoinTheTeam','Join the '.SITE_TEAM_TITLE,'This goes on the Join button',false, $category); 

$commonTeam['TeamIntro'] =$dynTemp->useDBTemplate('TeamIntro','<h1>'.SITE_TITLE.' is a Facebook community for '.SITE_TOPIC.'<a href="#" onclick="switchPage(\'team\');return false;" class="more_link">&hellip;&nbsp;Learn more</a></h1><p>Join the '.SITE_TEAM_TITLE.' to to keep up with local news and connect with others in the community </p>','',false, $category);

$commonTeam['RewardsPanelTitle'] =$dynTemp->useDBTemplate('RewardsPanelTitel','Rewards','',false, $category);
$commonTeam['WeeklyTeamLeadersPanelTitle'] = $dynTemp->useDBTemplate('WeeklyTeamLeadersPanelTitle','Weekly Team Leaders','',false, $category);
$commonTeam['AllTimeTeamLeadersPanelTitle'] = $dynTemp->useDBTemplate('AllTimeTeamLeadersPanelTitle','All Time Team Leaders','',false, $category);


$commonTeam['ProfileBoxIntro'] =$dynTemp->useDBTemplate('ProfileBoxIntro','		
					<div class="box_intro" style="background: url('.URL_CALLBACK.'?p=cache&simg=bg_profilebox.gif) top right no-repeat #d8dfea;">
			          <h1><a href="{canvasLink}">Join the '.SITE_TITLE.' community</a></h1>
			          <p>Help spread the word, <br />drive the discussion on '.SITE_TOPIC.' issues, and earn points.</p>
					</div>
					<!--end "box_intro"-->','',false, $category);

$commonTeam['ProfileBoxIntroJoinButton'] = $dynTemp->useDBTemplate('ProfileBoxIntroJoinButton','<p class="box_intro"><a href="{canvasLink}" class="btn_1" style="background: url('.URL_CALLBACK.'?p=cache&simg=btn_1.png) top repeat-x #c14001;">Dive in to '.SITE_TITLE.'</a></p>','',false, $category);		

// pageTeam

$commonTeam['IntroThumb'] = $dynTemp->useDBTemplate('IntroThumb',
	   '<div class="thumb">'.
       '<br />'.'<img src="'.URL_BASE.'/index.php?p=cache&simg=teamPhoto.jpg" alt="Chapel of St. Ignatius" width="320" height="247" style="border: 1px solid black;"/>'.
       	//'<fb:swf swfbgcolor="ffffff" imgstyle="border:none;" swfsrc="" imgsrc="" width="320" height="240" />'.
       '<div id="hideTip"><a href="#" onclick="hideTip(\'teamIntro\',this);return false;">Hide intro</a><!-- end hideTip --></div></div><!--end "thumb"-->','',false, $category);


$commonTeam['NewSignupIntro'] = $dynTemp->useDBTemplate('NewSignupIntro',				
				'<h1>Welcome to the team!</h1>'
				.'<div class="bullet_list"><ul>'.
		    '<li>As a new member, we recommend you ....</li>'.
                // REDEEM: '<li>Earn points for online and offline challenges and actions</li>'.
                '<li>Earn points for online and offline campus actions</li>'.
				'<li><a href="?p=invite" onclick="switchPage(\'invite\');return false;">Invite more friends</a></li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->','',false, $category);

$commonTeam['MemberIntro'] = $dynTemp->useDBTemplate('MemberIntro',					
      '<h1>Help spread the word about '.SITE_TOPIC.' issues!</h1>'.
		'<div class="bullet_list">'.
		  '<ul>'.
			    // REDEEM: '<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible Daily action team members</a> can earn cool,<br /> low-impact rewards</li>'.
			    //'<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible Daily action team members</a> can compete for<br />cool, low-impact rewards</li>'.
                '<li>Earn points for online participation, hit the leader board</li>'.
                '<li>Help spread the word by keeping friends in the loop and getting active in the '.SITE_TOPIC.' community</li>'.
                '<li>Contribute to a <a href="#" onclick="showDynamicDialog(\'research\',\'About Our Research Study\',\'aboutResearch\');return false;">research study</a> about social media</li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->'.
		  '<p class="bump10"><a href="#" onclick="switchPage(\'invite\');return false;" class="btn_1">Invite more friends</a></p>','',false, $category);			

			// not yet a member - get them to sign up
$commonTeam['NonMemberIntro'] =					
	$dynTemp->useDBTemplate('NonMemberIntro',
      '<h1>Join the '.SITE_TITLE.' Community!</h1>'.      
		'<div class="bullet_list">'.
		  '<ul>'.
		    	//'<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can earn rewards</li>'.
		    	//'<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can compete for great<br /> rewards</li>'.
                '<li>Earn points for online participation, hit the leader board</li>'.
                '<li>Help spread the word by keeping friends in the loop and getting active in the '.SITE_TOPIC.' community</li>'.
                //'<li>Contribute to a <a href="#" onclick="showDynamicDialog(\'research\',\'About Our Research Study\',\'aboutResearch\');return false;">research study</a> about social media</li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->','Non-members see this Intro',false,'commonTeam').
		 '<p class="bump10"><a href="?p=signup'.
			(isset($_GET['referid'])?'&referid='.$_GET['referid']:'').'" class="btn_1" '.
				(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').'>'.$commonTeam['JoinTheTeam'].'</a></p>';
				
				
$commonTeam['TeamFriendsPanelTitle'] = $dynTemp->useDBTemplate('TeamFriendsPanelTitle','Friends on the '.SITE_TEAM_TITLE.'','',false, $category);
$commonTeam['TeamFeedPanelTitle'] = $dynTemp->useDBTemplate('TeamFeedPanelTitle', SITE_TEAM_TITLE . ' Feed','',false, $category);				


// pageProfile

$commonTeam['BioPanelTitle'] = $dynTemp->useDBTemplate('BioPanelTitle','My Bio','',false, $category);
//$commonTeam['PendingChallengesPanelTitle'] = 'Pending Challenges';
$commonTeam['ProfileFeedPanelTitle'] = $dynTemp->useDBTemplate('ProfileFeedPanelTitle', 'My Daily Action Feed','',false, $category);
$commonTeam['PendingChallengesPanelTitle'] = $dynTemp->useDBTemplate('PendingChallengesPanelTitle', 'Pending Challenges','',false, $category);
$commonteam['ChallengesSubmittedFeedPanelTitle']= $dynTemp->useDBTemplate('ChallengesSubmittedFeedPanelTitle','Challenges Submitted Feed','',false, $category);
	




?>