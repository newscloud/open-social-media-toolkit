<?php
	/* FACEBOOK Module Templates for ST Challenges Page and Panels*/
$category = 'challenges';
	$this->addTemplateStatic($dynTemp, 'challengePanelList','<ul>{items}</ul>', '{items} replaced with list item elements',$category); // ul needed? 
	$this->addTemplateStatic($dynTemp, 'challengePanelItem','<li class="panel_block">
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
            </li>', 'Substitution {fields}: title, pointValue, id, description, submissionStyle '
            		.'(text set to "hidden" if not a submission challenge), daystart, dayend, monthstart, monthend, '
            		.'completions', $category );
            		
	$this->addTemplateStatic($dynTemp, 'challengePanelItemShort','<li class="panel_block">
                <div class="thumb"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;"><img src="' . URL_THUMBNAILS.'/{thumbnail}" width="50" /></a>
                </div>
                <div class="storyBlockWrap">
					<p class="storyHead"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;">{title}</a><br /><span class="pointValue">Earn {pointValue} <span class="pts">pts</span></span></p>
                    <p>{description}</p>
                  </div><!--end "storyBlockWrap"-->
            </li>', 'Substitution {fields}: title, pointValue, id, description, submissionStyle '
            		.'(text set to "hidden" if not a submission challenge), daystart, dayend, monthstart, monthend, '
            		.'completions', $category );
            
	$this->addTemplateDynamic($dynTemp, 'challengeTip','<div id="wideTipPanel" class="panel_1">
	<div class="bump10">
	'."Earn points for participating in ".SITE_TITLE." Community.
  These online challenges aim to get you more engaged in our ".SITE_TOPIC."
  community. Get more involved in the issues that you care about. It's all about being in the know
  and why not have fun while you're at it.".
	'</div>
</div><!--end "wideTipPanel"-->','',$category);
	
	
	$this->addTemplateDynamic($dynTemp, 'challengeLimits',
	'<div  id="actionLegend"><p ><strong>Limits on challenge submission:</strong> '. 
	'To promote fairness and prevent mischief in the '.SITE_TITLE.' community, many challenges '. 
	'have limitations on the number of times per day and the total number of times during the research study '. 
	'that you will receive points for completing them.</p></div><!-- end eligibility box -->'
	, '', $category);
	
	$this->addTemplateDynamic($dynTemp, 'BackText', 'Back to action challenges', '', $category);
	$this->addTemplateDynamic($dynTemp, 'ChallengesTitle', 'Action Challenges', '', $category);
	
	$this->addTemplateDynamic($dynTemp, 'CompletionTimesClause', ' during the contest.', '', $category);
	
?>