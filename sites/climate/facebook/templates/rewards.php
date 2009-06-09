<?php
	/* FACEBOOK Module Templates for ST Rewards Page*/

	//$this->addTemplate('rewardList','<div id="rewardList">{items}</div>'); // ul needed? 
	$this->addTemplate('rewardOverview',
	'Take credit for everything you do for the planet! <a href="?p=rules" onclick="setTeamTab(\'rules\');return false;">Eligible members</a> can earn points for those actions, 
	both on Facebook and in the real world. Redeem your points here for sweet, green-ish prizes. 
	The top climate activists on Hot Dish will be eligible to win a Grand Prize and Runners-Up Prizes. Visit our <a href="'.URL_CANVAS.'?p=rewards&o=summary">rewards summary</a> and <a href="'.URL_CANVAS.'?p=winners">winners page</a>.');
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
                            <h3>{initialStock} available <span class="{dateClass}">for week ending {shortDateEnd}</span></h3>
                  </div><!--end "storyBlockMeta"-->
                </div><!--end "storyBlockWrap"-->
                
				<p class="storyHead"><a href="?p=rewards&id={id}" onclick="setTeamTab(\'rewards\',{id}); return false;">{title}</a></p>
				<p class="storyCaption">{description} <a href="?p=rewards&id={id}" onclick="setTeamTab(\'rewards\',{id}); return false;" class="more_link">&hellip;&nbsp;more</a></p>
            </div><!--end "panel_block"-->
        </div><!--end "gridBlock"-->');

	$this->addTemplate('rewardItemTextWeekly',
			'<p style="clear:left;"><a href="?p=rewards&id={id}" >
                	<img style="float:left;margin:0px 5px 3px 0px;" src="' . //URL_THUMBNAILS.'/{thumbnail}" width="50" />
							URL_CALLBACK.'?p=cache&f={thumbnail}&x=60&y=60&m=scaleImg&path=uploads&fixed=x' .'" /> </a><span style="font-size:80%;">{initialStock} available for week ending {shortDateEnd}</span><br />                
                <strong><a href="?p=rewards&id={id}">{title}</a></strong><br />
				{description} <a href="?p=rewards&id={id}" >&hellip;&nbsp;more</a></p> 				            
        ');

	$this->addTemplate('rewardItemTextFinal',
			'<p style="clear:left;"><a href="?p=rewards&id={id}" >
                	<img style="float:left;margin:0px 5px 3px 0px;" src="' . //URL_THUMBNAILS.'/{thumbnail}" width="50" />
							URL_CALLBACK.'?p=cache&f={thumbnail}&x=60&y=60&m=scaleImg&path=uploads&fixed=x' .'" /> </a>                
                <strong><a href="?p=rewards&id={id}">{title}</a></strong><br />
				{description} <a href="?p=rewards&id={id}" >&hellip;&nbsp;more</a></p> 				            
        ');

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
	'<p>Action team members aged 16 to 25 years old living in the 50 United States are eligible for rewards. <a href="?p=faq" onclick="switchPage(\'static\',\'faq\');return false;">How do you verify your eligibility?</a></p><p>We will give away one (or sometimes more) weekly prizes to the eligible action team members who earn the greatest point total during each seven day period. Hot Dish weeks begin Sunday after midnight.</p>');

$this->addTemplate('leaderRewardAlltimeOverview',
	'<p>Action team members aged 16 to 25 years old living in the 50 United States are eligible for rewards. <a href="?p=faq" onclick="switchPage(\'static\',\'faq\');return false;">How do you verify your eligibility?</a></p><p>The action team member who accrues the greatest number of points through May 3, 2009 will win the grand prize. The nine members who accrue the next greatest numbers of points will receive <a href="?p=rewards" onclick="setTeamTab(\'rewards\'); return false;">runners-up prizes</a>.</p>');


$this->addTemplate('GrandPrize',
						'<div class="thumb">
							<a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;"><img src="'.URL_CALLBACK.'?p=cache&f=prize_47__expedition.jpg&x=120&y=90&m=scaleImg&path=uploads&fixed=x" /></a>
							</div><!--end "thumb"-->
							<div class="storyBlockWrap">
								<span class="pointValue">Grand Prize</span><p class="storyHead"><a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;">Become a Polar Ambassador!</a></p>
								<p>Experience the Arctic like you never imagined possible on a <strong>Spitsbergen Explorer trip for two to the Arctic with Quark Expeditions</strong>. Your 11-day adventure begins August 4, 2009 with the aim of circumnavigating the coastline of the largest island in Norway\'s Svelbard archipelago. <a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;" class="more_link">&hellip;&nbsp;more</a></p>
					            
								</div><!--end "storyBlockWrap"-->');

		/*'<div class="thumb">
							<a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;"><img src="'.URL_CALLBACK.'?p=cache&f=prize_47__expedition.jpg&x=120&y=90&m=scaleImg&path=uploads&fixed=x" /></a>
							</div><!--end "thumb"-->
							<div class="storyBlockWrap">
								<span class="pointValue">Grand Prize</span><p class="storyHead"><a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;">Become a Polar Ambassador!</a></p>
								<p>Experience the Arctic like you never imagined possible on a <strong>Spitsbergen Explorer trip for two to the Arctic with Quark Expeditions</strong>. Your 11-day adventure begins August 4, 2009 with the aim of circumnavigating the coastline of the largest island in Norway\'s Svelbard archipelago. <a href="?p=rewards&id=47" onclick="setTeamTab(\'rewards\',47); return false;" class="more_link">&hellip;&nbsp;more</a></p>
					            
								</div><!--end "storyBlockWrap"-->'.*/
$this->addTemplate('BackText', 'Back to action rewards');
$this->addTemplate('RewardTitle', 'Action Rewards');
$this->addTemplate('EligibilityBox', 
	'<div  id="actionLegend"><p ><strong>We\'re sorry!</strong> Unfortunately, you are not eligible to win rewards. 
	We hope that you still enjoy '.SITE_TITLE.' and encourage you to stay active in the fight against climate change.&nbsp;
	<a href="?p=rules" onclick="setTeamTab(\'rules\'); return false;" class="more_link">&hellip;&nbsp;more</a>
	</p></div><!-- end eligibility box -->');
 	
$this->addTemplate('ContestWeeks', 9);
$this->addTemplate('ContestStartDate', '2009/3/01 00:10:00');


// for winners page

$this->addTemplate('winnerList','<ul>{items}</ul>'); // ul needed? 
	
	$this->addTemplate('winnerItem','
	<li class="panel_block" style="display:block;">
	<div class="thumb">'.template::buildLinkedRewardPic('{prizeid}', '{thumbnail}', 30).
		                '</div>
		                <div class="storyBlockWrap">
		                <div class="feed_poster"><div class="avatar">'.template::buildLinkedProfilePic('{fbId}', 'size="square"  with="30" height="30"') .'</div>
		                    <h3><span class="bold">'. template::buildLinkedProfileName('{fbId}').
		                    	'</h3>
		                    </div>
		                    <p class="storyHead">'.template::buildRewardLink('{title}', '{prizeid}') .' </p>
		                    <p class="storyCaption"></p>                
		                </div><!__end "storyBlockWrap"__>'
	.'</li>'
	);

	/*
	$this->addTemplate('winnerItem',
	
	'<li class="panel_block">
		                <div class="storyBlockWrap">
		                     <h3><span class="bold">'. template::buildLinkedProfileName('{fbId}').'</h3>
		                   
		                </div><!__end "storyBlockWrap"__>'
	.'</li>'
	);*/
	


?>