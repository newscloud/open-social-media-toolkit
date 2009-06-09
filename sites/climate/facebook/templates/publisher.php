<?php
	/* FACEBOOK Module Templates for Wall Publisher and Email Attachments */
	// to do: may want url to feed through a newscloud page that allows us to track that the user did this
	//aded temp inline styles for layout before CSS

	
	//stories
	$this->addTemplate('storyList','<ul id="newswireList">{items}</ul>');
	$this->addTemplate('storyItem','<li class="panel_block">
									  	<div class="btn_radio">
									  		<input type="radio" value="{siteContentId}" name="selId"/>
									  	</div>
									  	<div class="thumb">{storyImage}</div>
							            <div class="storyBlockWrap">
							            	<p class="storyHead"><a href="{storyLink}" target="_blank">{title}</a></p>
							                <p class="storyCaption">{caption}&nbsp;&nbsp;&hellip;&nbsp;<a href="{storyLink}" target="_blank">more</a></p>                
							            </div><!--end "storyBlockWrap"-->');	
            
	$this->addTemplate('postedList','{items}');
	$this->addTemplate('postedItem','<p style="clear:both;"><b>{title}</b><br />{caption}</p>');	
	
	//rewards
	$this->addTemplate('pubRewardsList','<ul id="newswireList">{items}</ul>');
	$this->addTemplate('pubRewardsItem','<li class="panel_block">
										  	<div class="btn_radio">
										  		<input type="radio" value="{id}" name="selId"/>
										  	</div>
										  	<div class="thumb"><img src="'.URL_THUMBNAILS.'/{thumbnail}" border="0"></div>
								            <div class="storyBlockWrap">
								            	<p class="storyHead"><a href="'.URL_CANVAS.'/?p=rewards&id={id}" target="_blank">{title}</a><br /><span class="pointValue">Costs {pointCost} <span class="pts">pts</span></span></p>
								                <p class="storyCaption">{description}</p>                
								            </div><!--end "storyBlockWrap"-->');
								            
	$this->addTemplate('postedRewardsList','{items}');
	$this->addTemplate('postedRewardsItem','<p style="clear:both"><b>{title}</b><br>{description}<br>costs {pointCost} points');	
	
	//challenges
	$this->addTemplate('pubChallengesList','<ul id="newswireList">{items}</ul>');
	$this->addTemplate('pubChallengesItem','<li class="panel_block">
										  	<div class="btn_radio">
										  		<input type="radio" value="{id}" name="selId"/>
										  	</div>
										  	<div class="thumb"><img src="'.URL_THUMBNAILS.'/{thumbnail}" border="0"></div>
								            <div class="storyBlockWrap">
								            	<p class="storyHead"><a href="'.URL_CANVAS.'/?p=challenges&id={id}" target="_blank">{title}</a><br /><span class="pointValue">Earn {pointValue} <span class="pts">pts</span></span></p>
								                <p class="storyCaption">{description}</p>                
								            </div><!--end "storyBlockWrap"-->');
		
	$this->addTemplate('postedChallengesList','{items}');
	$this->addTemplate('postedChallengesItem','<p style="clear:both"><b>{title}</b><br>{description}<br>{monthstart} {daystart} to {monthend} {dayend} for {pointValue} points');	
	
	
?>