<?php
	/* FACEBOOK Module Templates for ST Rewards Page*/

	
	//$this->addTemplate('rewardList','<div id="rewardList">{items}</div>'); // ul needed? 
	$this->addTemplate('rewardOverview',
	'Take part in Minnesota Daily promotions and be eligible for rewards <a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can earn points for those actions, 
	both on Facebook and for taking part in events on campus. Redeem your points here for cool prizes. We will be posting new rewards periodically, as well as actions to earn these prizes.');
	
	$this->addTemplate('rewardList','{items}');  

	
	$this->addTemplate('rewardItem',
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
        </div><!--end "gridBlock"-->');



$this->addTemplate('rewardPanelList','<ul>{items}</ul>'); // ul needed? 
	
	$this->addTemplate('rewardPanelItem','
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
	');
		
		
$this->addTemplate('leaderRewardWeeklyOverview',
	'<p>Daily Action team members participating in the research study are eligible for rewards. <a href="?p=faq" onclick="switchPage(\'static\',\'faq\');return false;">How do you know if you\'re eligible?</a></p><p>We will give away prizes periodically to the eligible action team members who earn the greatest point total during each specified period.</p>');


$this->addTemplate('leaderRewardAlltimeOverview',
	'<p>Action team members aged 16 to 25 years old living in the 50 United States are eligible for rewards. <a href="?p=faq" onclick="switchPage(\'static\',\'faq\');return false;">How do you verify your eligibility?</a></p><p>The action team member who accrues the greatest number of points through May 3, 2009 will win the grand prize. The nine members who accrue the next greatest numbers of points will receive <a href="?p=rewards" onclick="setTeamTab(\'rewards\'); return false;">runners-up prizes</a>.</p>');

$this->addTemplate('GrandPrize',''

						/*'<div class="thumb">
							<a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;"><img src="'.URL_CALLBACK.'?p=cache&f=prize_47__expedition.jpg&x=120&y=90&m=scaleImg&path=uploads&fixed=x" /></a>
							</div><!--end "thumb"-->
							<div class="storyBlockWrap">
								<span class="pointValue">Grand Prize</span><p class="storyHead"><a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;">Become a Polar Ambassador!</a></p>
								<p>Experience the Arctic like you never imagined possible on a <strong>Spitsbergen Explorer trip for two to the Arctic with Quark Expeditions</strong>. Your 11-day adventure begins August 4, 2009 with the aim of circumnavigating the coastline of the largest island in Norway\'s Svelbard archipelago. <a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;" class="more_link">&hellip;&nbsp;more</a></p>
					            
								</div><!--end "storyBlockWrap"-->'*/);

$this->addTemplate('BackText', 'Back to action rewards');
$this->addTemplate('RewardTitle', 'Action Rewards');
$this->addTemplate('EligibilityBox', 
	'<div  id="actionLegend"><p ><strong>We\'re sorry!</strong> Unfortunately, you are not eligible to win rewards. 
	We hope that you still enjoy '.SITE_TITLE.' and encourage you to stay active in the fight against climate change.&nbsp;
	<a href="?p=rules" onclick="setTeamTab(\'rules\'); return false;" class="more_link">&hellip;&nbsp;more</a>
	</p></div><!-- end eligibility box -->');
 	
$this->addTemplate('ContestWeeks', 9);
$this->addTemplate('ContestStartDate', '2009/3/01 00:10:00');
 			
?>