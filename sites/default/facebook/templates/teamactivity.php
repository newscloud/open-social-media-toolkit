<?php
	$this->addTemplate('challengeList','<ol id="challengeList">{items}</ol>'); 
	$this->addTemplate('challengeItem',
				'<p>'.
				//<img src="' . URL_THUMBNAILS.'/{thumbnail}" width="50" />' .
				'{profile-pic} {profile-name} {status} for '.
			//	'<fb:profile-pic uid="{fbId}" size="thumb"/>' .
			//	'<fb:name ifcantsee="Anonymous" uid="{fbId}" capitalize="true"/> {status} for '.
				'"{title}"<br>'.
				//'{dateStart} to {dateEnd} <br>'.
				'on {monthstart} {daystart} '. // to {monthend} {dayend}'.
			
				'for {pointValue} points  <br>'.
				'- <a href="?p=challenges&id={challengeid}">Learn more...</a>.' .
				'</p>'); 
	
//	$this->addTemplate('profileChallengeList','<ul id="challengeList">{items}</ul>'); 
	$this->addTemplate('profileChallengeList',
 		'<table cellspacing="0">
                    <tbody>
                      <tr>
                        <th>Challenge Submitted</th>
                        <th>Points</th>
                        <th>Date</th>
                      </tr>
                      {items}
                     
                    </tbody>
                  </table>');	
	$this->addTemplate('profileChallengeItem',
				'<tr>'.
				'<td class="bold"><a href="?p=challenges&id={challengeid}" 
						onclick="setTeamTab(\'challenges\',{challengeid});return false;">{title}</a></td>'.
				'<td class="pointValue">{pointValue}</td>'.	
				'<td>{date}</td>'.
			
				'</tr>'); 

	
		
	
	$this->addTemplate('leaderList','<ul>{items}</ul>');	
	$this->addTemplate('leaderItem',
			'<li>'.
		
			'<div class="panel_block">'
				. '<div class="thumb">
						'	.template::buildLinkedProfilePic('{fbId}', 'size="square"')
					.'</div>'
			
		
					.'<div class="storyBlockWrap">'
					
						.'<h2>'.
		template::buildLocalProfileLink('{name}', '{fbId}').'</h2>'
		
						//.'<h1><fb:name ifcantsee="Anonymous" uid="'.$userinfo->fbId.'" useyou="false" capitalize="true" firstnameonly="false" linked="false"/></h1>'
						."<h3>{location}</h3>"
						.'<span class="pointValue">{pointTotal} <span class="pts">points</span>'	
								
					.'</div>' //<div class="storyBlockWrap">'
				
				.'</div><!__end "panel_block"__>'
			.'</li>'
		
		
		
	); 

	$this->addTemplate('userLevels', $GLOBALS['userLevels']);
	
?>