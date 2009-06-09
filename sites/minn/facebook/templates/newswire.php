<?php
	/* FACEBOOK Module Templates for Newswire Page aka Choose Stories*/
 	$rawItem = '<li>';
	$rawItem .= '<div class="storyBlockWrap">';
	$rawItem .= '<p class="storyHead"><a href="{url}" target="_cts" onclick="log(\'readWire\',{id});" >{title}</a></p>';
	$rawItem .= '<p class="storyCaption">{caption}<a class="more_link" href="{url}" onclick="log(\'readWire\',{id});" target="_cts">&hellip;&nbsp;more</a></p>';
	$rawItem .= '<div class="storyBlockMeta">';
	$rawItem .= '<p>Via {source}, {timeSince} ago</p>';
	$rawItem .= '<span id="pj_{id}" class="btn_left"><a href="#" onclick="addRawToJournal({id});return false;">Publish to '.SITE_TITLE.'</a></span>'.
'<span class="btn_right"><a href="{url}" onclick="log(\'readWire\',{id});" target="_cts">Read story</a></span>';
	$rawItem .= '</div><!-- end storyBlockMeta -->';
	$rawItem .= '</div><!-- end storyBlockWrap -->';
	$rawItem .= '</li>';
	$this->addTemplate('rawList','<div class="list_stories"><ul>{items}</ul></div>');
	$this->addTemplate('rawItem', $rawItem);
	$this->addTemplate('wideTip','<div id="wideTipPanel" class="panel_1">
			<div class="panelBar clearfix">
				<h2>Join the conversation</h2>
				<div class="bar_link"><a href="?p=postStory" onclick="switchPage(\'postStory\');return false;">Post a story</a><span class="pipe">|</span><a href="#" onclick="hideTip(\'postStory\',this);return false;">Hide tip</a></div>
		</div><!--end "panelBar"-->
		<div class="wideTipLeft">
            <p>The news in Minnesota Daily is driven by the editors at mndaily.com and especially by YOU, the community. When you find any news of note, post it here, and help spread the flow of information.</p>
            <p class="bump10"><a class="btn_1" href="?p=postStory" onclick="switchPage(\'postStory\');return false;">Post a Story</a></p>
          </div><!--end "wideTipLeft"-->
          <div class="wideTipRight">
            <p class="bold">Earn points from your activity:</p>
			<div class="pointsTable">
                  <table cellspacing="0">
                    <tbody>
                      <tr>
                        <td><a href="?p=postStory" onclick="switchPage(\'postStory\');return false;">Post a story</a></td>
                        <td class="pointValue">Earn 10 <span class="pts">pts</span></td>
                      </tr>
                      <tr>
                        <td>Share a story</td>
                        <td class="pointValue">Earn 25 <span class="pts">pts</span></td>
                      </tr>
                      <tr>
                        <td><a href="?p=invite" onclick="switchPage(\'invite\');return false;">Invite more friends</a></td>
                        <td class="pointValue">Earn 25 <span class="pts">pts</span></td>
                      </tr>
                      <tr>
                        <td><a href="?p=postStory" onclick="switchPage(\'postStory\');return false;">Add bookmark tool</a></td>
                        <td class="pointValue">Earn 25 <span class="pts">pts</span></td>
                      </tr>
                    </tbody>
                  </table>
			</div><!--end "pointsTable"-->
		</div><!--end "wideTipLeft"-->
	</div><!--end "wideTipPanel"-->');

?>