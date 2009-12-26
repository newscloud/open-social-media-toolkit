<?php
	/* FACEBOOK Module Templates for ST Rewards Page*/

	$cat = 'rewards';
	//$this->addTemplateDynamic($dynTemp, 'rewardList','<div id="rewardList">{items}</div>'); // ul needed? 
	$this->addTemplateDynamic($dynTemp, 'rewardOverview',
	'Take part in '.SITE_TITLE.' and be eligible for rewards <a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can earn points for those actions, 
	both on Facebook and for taking part in events. Redeem your points here for cool prizes. We will be posting new rewards periodically, as well as actions to earn these prizes.','',$cat);
	
	$this->addTemplateStatic($dynTemp, 'rewardList','{items}','',$cat);  

	
	$this->addTemplateStatic($dynTemp, 'rewardItem',
			'<div class="gridBlock">
            <div class="panel_block">    
                <div class="thumb"><a href="?p=rewards&id={id}" onclick="setTeamTab(\'rewards\',{id}); return false;">
                	<img src="' . //URL_THUMBNAILS.'/{thumbnail}" width="50" />
							URL_CALLBACK.'?p=cache&f={thumbnail}&x=90&y=90&m=scaleImg&path=uploads&fixed=y' .'" />                
                </a></div>
                <div class="storyBlockWrap">
                  <p class="pointValue {pointClass}">{pointCost} <span class="pts">pts</span></p>
                        <div class="storyBlockMeta">
                            <h3>{initialStock} available</h3>
                  </div><!--end "storyBlockMeta"-->
                </div><!--end "storyBlockWrap"-->
                
				<p class="storyHead"><a href="?p=rewards&id={id}" onclick="setTeamTab(\'rewards\',{id}); return false;">{title}</a></p>
				<p class="storyCaption">{description} <a href="?p=rewards&id={id}" onclick="setTeamTab(\'rewards\',{id}); return false;" class="more_link">&hellip;&nbsp;more</a></p>
            </div><!--end "panel_block"-->
        </div><!--end "gridBlock"-->','',$cat);



$this->addTemplateStatic($dynTemp, 'rewardPanelList','<ul>{items}</ul>','',$cat); // ul needed? 
	
	$this->addTemplateStatic($dynTemp, 'rewardPanelItem','
	<li>
	<div class="thumb clearfix">'.
		//{linkedThumbnail} '.
		'<a href="?p=rewards&id={id}" onclick="setTeamTab(\'rewards\',{id}); return false;">'.
			'<img src="' . 
	
				URL_CALLBACK.'?p=cache&f={thumbnail}&x=50&y=50&m=scaleImg&path=uploads&fixed' .'" />'
	//URL_THUMBNAILS.'/{thumbnail}" width="50" />'.
		.'</a>'.

		'</div>	
	<div class="storyBlockWrap clearfix">
	    <p class="storyHead">'.
	    

		'<a href="?p=rewards&id={id}" onclick="setTeamTab(\'rewards\',{id}); return false;">{title}</a>'.

		    
	    '</p>
        <p class="pointValue {pointClass}">costs {pointCost} <span class="pts">pts</span></p>
             </div></li>               
	','',$cat);
		
		
$this->addTemplateDynamic($dynTemp, 'leaderRewardWeeklyOverview',
	'<p>'.SITE_TEAM_TITLE.' team members participating in the research study may be eligible for rewards in the future. </p>','',$cat);


$this->addTemplateDynamic($dynTemp, 'leaderRewardAlltimeOverview',
	'<p>'.SITE_TEAM_TITLE.' team members participating in the research study may be eligible for rewards in the future. </p>','',$cat);

$this->addTemplateDynamic($dynTemp, 'GrandPrize','<div class="storyBlockWrap"></div><!--end "storyBlockWrap"-->','',$cat);

						/*'<div class="thumb">
							<a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;"><img src="'.URL_CALLBACK.'?p=cache&f=prize_47__expedition.jpg&x=120&y=90&m=scaleImg&path=uploads&fixed=x" /></a>
							</div><!--end "thumb"-->
							<div class="storyBlockWrap">
								<span class="pointValue">Grand Prize</span><p class="storyHead"><a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;">INTRO</a></p>
								<p>Description <a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;" class="more_link">&hellip;&nbsp;more</a></p>
					            
								</div><!--end "storyBlockWrap"-->','',$cat);*/

$this->addTemplateDynamic($dynTemp, 'BackText', 'Back to action rewards','',$cat);
$this->addTemplateDynamic($dynTemp, 'RewardTitle', 'Action Rewards','',$cat);
$this->addTemplateDynamic($dynTemp, 'EligibilityBox', 
	'<div  id="actionLegend"><p ><strong>We\'re sorry!</strong> Unfortunately, you are not eligible to win rewards. 
	We hope that you still enjoy '.SITE_TITLE.' and encourage you to stay active in the fight against climate change.&nbsp;
	<a href="?p=rules" onclick="setTeamTab(\'rules\'); return false;" class="more_link">&hellip;&nbsp;more</a>
	</p></div><!-- end eligibility box -->','',$cat);
 	
$this->addTemplateDynamic($dynTemp, 'ContestWeeks', 9,'',$cat);
$this->addTemplateDynamic($dynTemp, 'ContestStartDate', '2009/3/01 00:10:00','',$cat);
 			
?>