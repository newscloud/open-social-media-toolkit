<?php
	/* FACEBOOK Module Templates for ST Challenges Page and Panels*/

	$this->addTemplate('challengePanelList','<ul>{items}</ul>'); // ul needed? 
	$this->addTemplate('challengePanelItem','<li class="panel_block">
                <div class="thumb"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;"><img src="' . URL_THUMBNAILS.'/{thumbnail}" width="50" /></a>
                </div>
                <div class="storyBlockWrap">
					<p class="storyHead"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;">{title}</a><br /><span class="pointValue">Earn {pointValue} <span class="pts">pts</span></span></p>
                    <p>{description}</p>
                    <p>
                    '.
					template::buildChallengeLink("Learn more","{id}","btn_2").
					template::buildChallengeSubmitLink("I did this","{id}","btn_2 {submissionStyle}").
					/*template::buildChallengeLink("Challenge your friends","{id}","btn_2").*/ // TODO: point to actual challenge-your-friends dialog
	
					//	'<a class="btn_2" href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;">Learn more</a>
                    //	<a class="btn_2" href="">I did this!</a>
                    //	<a class="btn_2" href="">Challenge your friends</a>'.
                    	'</p>
                  </div><!--end "storyBlockWrap"-->
            </li>');
	$this->addTemplate('challengePanelItemShort','<li class="panel_block">
                <div class="thumb"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;"><img src="' . URL_THUMBNAILS.'/{thumbnail}" width="50" /></a>
                </div>
                <div class="storyBlockWrap">
					<p class="storyHead"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;">{title}</a><br /><span class="pointValue">Earn {pointValue} <span class="pts">pts</span></span></p>
                    <p>{description}</p>
                  </div><!--end "storyBlockWrap"-->
            </li>');
            
	$this->addTemplate('challengeTip','<div id="wideTipPanel" class="panel_1">
	<div class="bump10">
	'."Participate in events and promotions and get rewards for participating in the Daily Action Team.
  These online and offline challenges aim to get you more engaged in news and the University of Minnesota
  community. Sure campus is big, but you can make it small by becoming more active in the issues that
  impact you, your fellow students, faculty and the surroundng community. It's all about being in the know
  and why not get some rewards while you're at it. Get started by checking out some of the challenges below
  or submit one of your own.".
	'</div>
</div><!--end "wideTipPanel"-->');
	
	
	$this->addTemplate('challengeLimits',
	'<div  id="actionLegend"><p ><strong>Limits on challenge submission:</strong> '. 
	'To promote fairness and prevent mischief in the '.SITE_TITLE.' action team contest, many challenges '. 
	'have limitations on the number of times per day and the total number of times during the research study '. 
	'that you will receive points for completing them.</p></div><!-- end eligibility box -->'
	);
	
	$this->addTemplate('BackText', 'Back to action challenges');
	$this->addTemplate('ChallengesTitle', 'Action Challenges');
	
	$this->addTemplate('CompletionTimesClause', ' during the contest.');
	
?>