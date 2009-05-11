<?php
// action team

$commonTeam['JoinTheTeam']= 'Join the '.SITE_TEAM_TITLE;

$commonTeam['TeamIntro'] ='<h1>Climate change sucks, but you can do something about it<a href="#" onclick="switchPage(\'team\');return false;" class="more_link">&hellip;&nbsp;Learn more</a></h1>
        <p>Join the '.SITE_TEAM_TITLE.' to take online and real-world eco-action. 
        	You can earn points, win prizes, and together we can kick climate change in the ... shin.</p>';

$commonTeam['RewardsPanelTitle'] ='Action Rewards';
$commonTeam['WeeklyTeamLeadersPanelTitle'] = 'Weekly Team Leaders';
$commonTeam['AllTimeTeamLeadersPanelTitle'] = 'All Time Team Leaders';


$commonTeam['ProfileBoxIntro'] ='		
					<div class="box_intro" style="background: url('.URL_CALLBACK.'?p=cache&simg=bg_profilebox.gif) top right no-repeat #d8dfea;">
			          <h1><a href="{canvasLink}">We can do more, and have more fun doing it!</a></h1>
			          <p>Help spread the word, <br />drive the discussion on hot topics, and compete for earth-happy prizes along the way.</p>
					</div>
					<!--end "box_intro"-->';

$commonTeam['ProfileBoxIntroJoinButton'] = '<p class="box_intro"><a href="{canvasLink}" class="btn_1" style="background: url('.URL_CALLBACK.'?p=cache&img=btn_1.png) top repeat-x #c14001;">Dive in to Hot Dish</a></p>';		

// pageTeam

$commonTeam['IntroThumb'] = 
	   '<div class="thumb">'.
       '<br /><fb:swf swfbgcolor="ffffff" imgstyle="border:none;" swfsrc="http://www.youtube.com/v/U9K6pXym1O8" imgsrc="http://img.youtube.com/vi/U9K6pXym1O8/2.jpg" width="320" height="240" />'.
       '<div id="hideTip"><a href="#" onclick="hideTip(\'teamIntro\',this);return false;">Hide intro</a><!-- end hideTip --></div></div><!--end "thumb"-->';


$commonTeam['NewSignupIntro'] =				
				'<h1>Welcome to the team!</h1>'
				.'<div class="bullet_list"><ul>'.
		    '<li>As a new member, we recommend you ....</li>'.
                // REDEEM: '<li>Earn points for online and offline actions planet-saving actions</li>'.
                '<li>Earn points for online and offline planet-saving actions</li>'.
				'<li><a href="?p=invite" onclick="switchPage(\'invite\');return false;">Invite more friends</a></li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->';

$commonTeam['MemberIntro'] =					
      '<h1>Help spread the word about climate change-win cool stuff!</h1>'.
		'<div class="bullet_list">'.
		  '<ul>'.
			    // REDEEM: '<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible action team members</a> can earn cool,<br /> low-impact rewards, and compete for a trip to the Arctic</li>'.
			    '<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible action team members</a> can compete for<br />cool, low-impact rewards, and even win a trip to the Arctic</li>'.
                '<li>Earn points for online and offline planet-saving actions</li>'.
                '<li>Help spread the word by keeping friends in the loop and getting active in your community</li>'.
                '<li>Contribute to a <a href="#" onclick="showDynamicDialog(\'research\',\'About Our Research Study\',\'aboutResearch\');return false;">research study</a> about social media</li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->'.
		  '<p class="bump10"><a href="#" onclick="switchPage(\'invite\');return false;" class="btn_1">Invite more friends</a></p>';			

			// not yet a member - get them to sign up
$commonTeam['NonMemberIntro'] =					

      '<h1>Help spread the word about climate change, compete for great prizes!</h1>'.      
		'<div class="bullet_list">'.
		  '<ul>'.
		    	'<li><a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can compete for great<br /> earth-happy rewards, including a trip to the Arctic</li>'.
				'<li>Earn points for online and offline  planet-saving actions</li>'.
                '<li>Help spread the word by keeping friends in the loop, and getting active in the community</li>'.
                '<li>Contribute to a <a href="#" onclick="showDynamicDialog(\'research\',\'About Our Research Study\',\'aboutResearch\');return false;">research study</a> about social media</li>'.
                '<li>Download free endangered species ringtones from the Center for Biological Diversity</li>'.
		    '</ul>'.
		  '</div><!--end "bullet_list"-->'.
		 '<p class="bump10"><a href="?p=signup'.
			(isset($_GET['referid'])?'&referid='.$_GET['referid']:'').'" class="btn_1 btn_1_lg" '.
				(!isset($_POST['fb_sig_logged_out_facebook'])?'requirelogin="1"':'').'>Sign up for the '.SITE_TEAM_TITLE.'</a></p>';
				
				
$commonTeam['TeamFriendsPanelTitle'] = 'Friends on the '.SITE_TEAM_TITLE.'';
$commonTeam['TeamFeedPanelTitle'] = SITE_TEAM_TITLE . ' Feed';				


// pageProfile

$commonTeam['BioPanelTitle'] = 'My Bio';
//$commonTeam['PendingChallengesPanelTitle'] = 'Pending Challenges';
$commonTeam['ProfileFeedPanelTitle'] = 'My Action Feed';
$commonTeam['PendingChallengesPanelTitle'] = 'Pending Challenges';
$commonteam['ChallengesSubmittedFeedPanelTitle']= 'Challenges Submitted Feed';
	

?>