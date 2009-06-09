<?php
	echo 'got into PHP profilebox directory';
	/* FACEBOOK Module Templates for Profile Page */
	$this->addTemplate('storyList','<div id="featuredStories"><h3>Featured Stories</h3><div class="bullet_list"><ul>{items}</ul></div></div>');
	$this->addTemplate('storyItem','<li class="storyTitle"><a href="{storyLink}">{storyImage}</a> <a href="{storyLink}">{title}</a></li>');
	
	$this->addTemplate('cProfileBoxList','<div id="featuredChallenges"><h3>Featured Challenges</h3><div class="bullet_list"><ul>{items}</ul></div></div>');
	$this->addTemplate('cProfileBoxItem','<li class="challengeTitle"><img src="' . URL_THUMBNAILS.'/{thumbnail}" width="50" height="50" border="1" />{title}</li>');
	
//	$this->addTemplate('challengePanelList','<ul>{items}</ul>'); // ul needed? 
//	$this->addTemplate('challengePanelItem','<li class="panel_block">
//                <div class="thumb"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;"><img src="' . URL_THUMBNAILS.'/{thumbnail}" width="50" /></a>
//                </div>
//                <div class="storyBlockWrap">
//					<p class="storyHead"><a href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;">{title}</a><br /><span class="pointValue">Earn {pointValue} <span class="pts">pts</span></span></p>
//                    <p>{description}</p>
//                    <p><a class="btn_2" href="?p=challenges&id={id}" onclick="setTeamTab(\'challenges\',{id}); return false;">Learn more</a><a class="btn_2" href="">I did this!</a><a class="btn_2" href="">Challenge your friends</a></p>
//                  </div><!--end "storyBlockWrap"-->
//            </li><hr />');

	
?>