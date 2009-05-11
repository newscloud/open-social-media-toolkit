<?php
	/* registers module-specific templates for Core Template engine */
	// invoked from the core template class

	/* PHP Module Templates */
	$this->addTemplate('upcomingList','<h2>{listTitle}</h2><ul id="storyList">{items}</ul>');
	$this->addTemplate('upcomingListNoTitle','<ul id="storyList">{items}</ul>');
	$this->addTemplate('upcomingItem','<li class="title">{storyImage}<a href="'.URL_PREFIX.'?p=readStory&permalink={permalink}">{title}<span style="display:none;">{contentid}</span></a></li><li class="posted">Posted by {postedByName}, {time_since} ago</li><li class="caption">{caption}</li><li class="commands">{cmdVote} | {cmdComment} | {cmdAdd} | {cmdRead}</li>'); //  via {source}
	$this->addTemplate('sidebarList','<ul class="sidebarList"><h3>{listTitle}</h3>{items}</ul>');
	$this->addTemplate('sidebarItem','<li><h3><a href="'.URL_PREFIX.'?p=readStory&permalink={permalink}">{title}</a></h3><p class="commands">{cmdVote} | {cmdComment} <span style="display:none;">{contentid}</span></p></li>');
	$this->addTemplate('wireItem','<li><h3><a href="'.URL_PREFIX.'?p=process&action=readWire&itemid={id}" target="story">{title}</a></h3><p class="commands">| <span class="pw_{id}"><a href="#" onclick="publishWire({id});">Publish to your journal</a></span></p></li>');
	$this->addTemplate('readStoryContainer','<div id="readStory"><ul id="storyList">{items}</ul></div>');
	$this->addTemplate('readStoryContent','<li class="storyImage">{storyImage}</li><li class="title"><a href="'.URL_PREFIX.'?p=readStory&permalink={permalink}">{title}</a></li><li class="posted">Posted by {postedByName}, {time_since} ago</li><li class="caption">{caption}</li><li class="commands">{cmdVote} | {cmdAdd} | {cmdRead}</li>'); //  via {source}
	$this->addTemplate('commentList','<h2>Reader Comments</h2><ul>{items}</ul>');
	$this->addTemplate('commentItem','<li>{userImage}<p class="comments">{comments}</p><p class="posted">Posted by {postedByName}, {time_since} ago</p></li>');
	$this->addTemplate('newswireList','<ul id="storyList">{items}</ul>');
	$this->addTemplate('newswireItem','<li class="title"><a href="'.URL_PREFIX.'?p=process&action=readWire&itemid={id}">{title}</a></li><li class="posted">Posted {time_since} ago</li><li class="caption">{caption}</li><li class="commands">| <span class="pw_{id}"><a href="#" onclick="publishWire({id});">Publish to your journal</a></span> | <a href="'.URL_PREFIX.'?p=process&action=readWire&itemid={id}" target="_wire">Read story</a></li>'); // via {source},
	$this->addTemplate('resourceList','<ul class="resourceList"><h2>{listTitle}</h2>{items}</ul>');
	$this->addTemplate('resourceItems','<li>{resourceImage}<a href="{url}" title="{notes}">{title}</a></li>');
	$this->addTemplate('mobileList','{items}');
	$this->addTemplate('mobileItems','<p><a href="{url}">{title}</a> {caption}</p>');	
	
?>
