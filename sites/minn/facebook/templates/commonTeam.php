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

$commonTeam['JoinTheTeam']= 'Join the '.SITE_TEAM_TITLE;

$commonTeam['TeamIntro'] ='<h1>The U of M is big, but it doesn\'t have to be.<a href="#" onclick="switchPage(\'team\');return false;" class="more_link">&hellip;&nbsp;Learn more</a></h1>
        <p>Join the '.SITE_TEAM_TITLE.' to to keep up with University news and connect with others in the community </p>';

$commonTeam['RewardsPanelTitle'] ='Action Rewards';
$commonTeam['WeeklyTeamLeadersPanelTitle'] = 'Weekly Team Leaders';
$commonTeam['AllTimeTeamLeadersPanelTitle'] = 'All Time Team Leaders';


$commonTeam['ProfileBoxIntro'] ='		
					<div class="box_intro" style="background: url('.URL_CALLBACK.'?p=cache&simg=bg_profilebox.gif) top right no-repeat #d8dfea;">
			          <h1><a href="{canvasLink}">We can do more, and have more fun doing it!</a></h1>
			          <p>Help spread the word, <br />drive the discussion on campus issues, and compete for prizes along the way.</p>
					</div>
					<!--end "box_intro"-->';

$commonTeam['ProfileBoxIntroJoinButton'] = '<p class="box_intro"><a href="{canvasLink}" class="btn_1" style="background: url('.URL_CALLBACK.'?p=cache&img=btn_1.png) top repeat-x #c14001;">Dive in to Minnesota Daily</a></p>';		

// pageTeam

$commonTeam['IntroThumb'] = 
	   '<div class="thumb">'.
       '<br />'.'<img src="'.URL_BASE.'/index.php?p=cache&simg=teamPhoto.jpg" alt="Daily Staff" width="320" height="240" style="border: 1px solid black;"/>'.
       	//'<fb:swf swfbgcolor="ffffff" imgstyle="border:none;" swfsrc="" imgsrc="" width="320" height="240" />'.
       '<div id="hideTip"><a href="#" onclick="hideTip(\'teamIntro\',this);return false;">Hide intro</a><!-- end hideTip --></div></div><!--end "thumb"-->';


$commonTeam['NewSignupIntro'] =				
				'<h1>Welcome to the team!</h1>'
				.'<div class="bullet_list"><ul>'.
		    '<li>As a new member, we recommend you ....</li>'.
                // REDEEM: '<li>Earn points for online and offline challenges and actions</li>'.
                '<li>Earn points for online and offline campus actions</li>'.
				'<li><a href="?p=invite" onclick="switchPage(\'invite\');return false;">Invite more friends</a></li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->';

$commonTeam['MemberIntro'] =					
      '<h1>Help spread the word about campus issues-win cool stuff!</h1>'.
		'<div class="bullet_list">'.
		  '<ul>'.
			    // REDEEM: '<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible Daily action team members</a> can earn cool,<br /> low-impact rewards</li>'.
			    '<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible Daily action team members</a> can compete for<br />cool, low-impact rewards</li>'.
                '<li>Earn points for online and offline campus actions</li>'.
                '<li>Help spread the word by keeping friends in the loop and getting active in the University community</li>'.
                '<li>Contribute to a <a href="#" onclick="showDynamicDialog(\'research\',\'About Our Research Study\',\'aboutResearch\');return false;">research study</a> about social media</li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->'.
		  '<p class="bump10"><a href="#" onclick="switchPage(\'invite\');return false;" class="btn_1">Invite more friends</a></p>';			

			// not yet a member - get them to sign up
$commonTeam['NonMemberIntro'] =					

      '<h1>Join the Minnesota Daily Action Team!</h1>'.      
		'<div class="bullet_list">'.
		  '<ul>'.
		    	//'<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can earn rewards</li>'.
		    	'<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can compete for great<br /> rewards</li>'.
				'<li>Earn points for online and offline actions</li>'.
                '<li>Help spread the word by keeping friends in the loop, and getting active in the University community</li>'.
                '<li>Contribute to a <a href="#" onclick="showDynamicDialog(\'research\',\'About Our Research Study\',\'aboutResearch\');return false;">research study</a> about social media</li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->'.
		 '<p class="bump10"><a href="?p=signup'.
			(isset($_GET['referid'])?'&referid='.$_GET['referid']:'').'" class="btn_1" '.
				(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').'>Join the '.SITE_TEAM_TITLE.'</a></p>';
				
				
$commonTeam['TeamFriendsPanelTitle'] = 'Friends on the '.SITE_TEAM_TITLE.'';
$commonTeam['TeamFeedPanelTitle'] = SITE_TEAM_TITLE . ' Feed';				


// pageProfile

$commonTeam['BioPanelTitle'] = 'My Bio';
//$commonTeam['PendingChallengesPanelTitle'] = 'Pending Challenges';
$commonTeam['ProfileFeedPanelTitle'] = 'My Daily Action Feed';
$commonTeam['PendingChallengesPanelTitle'] = 'Pending Challenges';
$commonteam['ChallengesSubmittedFeedPanelTitle']= 'Challenges Submitted Feed';
	




?>