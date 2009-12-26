<?php
	/* FACEBOOK Module Templates for Featured Stories Sections*/
	//$templateItem1 = '<div id="featuredStory1"'
	//$this->addTemplate('featuredStories','<div id="featuredStories">{asdf}{items}</div>');
	//$this->addTemplate('templateItem1', '<div id="featuredStory1">{story_1_image}<div class="story-content">{story_1_title}{story_1_caption}</div></div>');
	function buildTemplate1(&$stories) {
		$s &= $stories;
		list($s_1) = $stories;
		$s_1_image_url = getImageLink($s_1, 'large');
		$s_1_caption = getCaption($s_1);

		$code = '<div class="featured_story clearfix">';
		$code .= '<div id=story_1_image" class="thumb">'.$s_1_image_url.'</div>';
		$code .= '<div class="storyBlockWrap"><h1 id="story_1_title">'.getStoryLink($s_1).'</h1>';
		$code .= '<p id="story_1_caption">'.$s_1_caption.'</p>';
		$code .= '</div><!-- end storyBlockWrap --></div><!-- end featured_story -->';

		return $code;
	}

	function buildTemplate2(&$stories) {
		$s &= $stories;
		list($s_1, $s_2, $s_3) = $stories;
		$s_1_image_url = getImageLink($s_1, 'large');
		$s_1_caption = getCaption($s_1);

		$code = '<div class="clearfix featured_story" >';
		$code .= '<div id=story_1_image" class="thumb">'.$s_1_image_url.'</div>';
		$code .= '<div class="storyBlockWrap"><h1 id="story_1_title">'.getStoryLink($s_1).'</h1>';
		$code .= '<p id="story_1_caption">'.$s_1_caption.'</p>';
		$code .= '<div class="related_story">';
		$code .= '<div class="bullet_list"><ul>';
		$code .= '<li id="story_2_title">'.getStoryLink($s_2).'</li>';
		$code .= '<li id="story_3_title">'.getStoryLink($s_3).'</li>';
		$code .= '</div></div>';
		$code .= '</div><!-- end storyBlockWrap --></div><!-- end featured_story -->';

		return $code;
	}

	function buildTemplate3(&$stories) {
		$s &= $stories;
		list($s_1, $s_4) = $stories;
		$s_1_image_url = getImageLink($s_1, 'large');
		$s_1_caption = getCaption($s_1);
		$s_4_image_url = getImageLink($s_4, 'large');
		$s_4_caption = getCaption($s_4);


		$code = '<div class="featured_story clearfix">';
		$code .= '<div id=story_1_image" class="thumb">'.$s_1_image_url.'</div>';
		$code .= '<div class="storyBlockWrap"><h1 id="story_1_title">'.getStoryLink($s_1).'</h1>';
		$code .= '<p id="story_1_caption">'.$s_1_caption.'</p>';
		$code .= '</div><!-- end storyBlockWrap --></div><!-- end featured_story -->';
		$code .= '<div class="featured_story clearfix">';
		$code .= '<div id=story_4_image" class="thumb">'.$s_4_image_url.'</div>';
		$code .= '<div class="storyBlockWrap"><h1 id="story_4_title">'.getStoryLink($s_4).'</h1>';
		$code .= '<p id="story_4_caption">'.$s_4_caption.'</p>';
		$code .= '</div><!-- end storyBlockWrap --></div><!-- end featured_story -->';
		return $code;
	}

	function buildTemplate4(&$stories) {
		$s &= $stories;
		list($s_1, $s_4) = $stories;
		$s_1_image_url = getImageLink($s_1, 'large');
		$s_1_caption = getCaption($s_1);
		$s_4_image_url = getImageLink($s_4, 'large');
		$s_4_caption = getCaption($s_4);

		$code = '<div class="clearfix featured_story_half">';
		$code .= '<div class="thumb">'.$s_1_image_url.'</div>';
		$code .= '<h1 id="story_1_title">'.getStoryLink($s_1).'</h1>';
		$code .= '<p id="story_1_caption">'.$s_1_caption.'</p>';
		$code .= '</div>';

		$code .= '<div class="clearfix featured_story_half">';
		$code .= '<div class="thumb">'.$s_4_image_url.'</div>';
		$code .= '<h1 id="story_4_title">'.getStoryLink($s_4).'</h1>';
		$code .= '<p id="story_4_caption">'.$s_4_caption.'</p>';
		$code .= '</div>';

		return $code;
	}

	function buildTemplate5(&$stories) {
		$s &= $stories;
		list($s_1, $s_2, $s_3, $s_4, $s_5, $s_6) = $stories;
		$s_1_image_url = getImageLink($s_1, 'large');
		$s_1_caption = getCaption($s_1);
		$s_2_image_url = getImageLink($s_2, 'small');
		$s_2_caption = getCaption($s_2);
		$s_3_image_url = getImageLink($s_3, 'small');
		$s_3_caption = getCaption($s_3);
		$s_4_image_url = getImageLink($s_4, 'large');
		$s_4_caption = getCaption($s_4);
		$s_5_image_url = getImageLink($s_5, 'small');
		$s_5_caption = getCaption($s_5);
		$s_6_image_url = getImageLink($s_6, 'small');
		$s_6_caption = getCaption($s_6);
		//$code = '<div id="featured_story" class="clearfix">';
		$code = '<div class="clearfix featured_story">';
		$code .= '<div id=story_1_image" class="thumb">'.$s_1_image_url.'</div>';
		$code .= '<div class="storyBlockWrap"><h1 id="story_1_title">'.getStoryLink($s_1).'</h1>';
		$code .= '<p id="story_1_caption">'.$s_1_caption.'</p>';
		$code .= '<div class="related_story">';
		$code .= '<div id="story_2_image" class="slot">'.$s_2_image_url.'</div>';
		$code .= '<p id="story_2_title">'.getStoryLink($s_2).'</p>';
		$code .= '</div>';
		$code .= '<div class="related_story">';
		$code .= '<div id="story_3_image" class="slot">'.$s_3_image_url.'</div>';
		$code .= '<p id="story_3_title">'.getStoryLink($s_3).'</p>';
		$code .= '</div>';
		$code .= '</div><!-- end storyBlockWrap --></div><!-- end featured_story -->';

		//$code .= '<div id="featured_story" class="clearfix">';
		$code .= '<div class="clearfix featured_story">';
		$code .= '<div id=story_4_image" class="thumb">'.$s_4_image_url.'</div>';
		$code .= '<div class="storyBlockWrap"><h1 id="story_4_title">'.getStoryLink($s_4).'</h1>';
		$code .= '<p id="story_4_caption">'.$s_4_caption.'</p>';
		$code .= '<div class="related_story">';
		$code .= '<div id="story_5_image" class="slot">'.$s_5_image_url.'</div>';
		$code .= '<p id="story_5_title">'.getStoryLink($s_5).'</p>';
		$code .= '</div>';
		$code .= '<div class="related_story">';
		$code .= '<div id="story_6_image" class="slot">'.$s_6_image_url.'</div>';
		$code .= '<p id="story_6_title">'.getStoryLink($s_6).'</p>';
		$code .= '</div>';
		$code .= '</div><!-- end storyBlockWrap --></div><!-- end featured_story -->';




		/*
		$code = '<div id="featured-story">';
		$code .= '<div id="story-1-image" style="margin: 0 10px 0 0; float: left">'.$s_1_image_url.'</div>';
		$code .= '<div class="story-content"><p id="story-1-title">'.getStoryLink($s_1).'</p>';
		$code .= '<p id="story-1-caption">'.$s_1_caption.'</p>';
		$code .= '<div id="story-2-image" class="slot" style="width: 40px; height: 30px; float: left; margin: 0 10px 0 0;">'.$s_2_image_url.'</div>';
		$code .= '<p id="story-2-title">'.getStoryLink($s_2).'</p>';
		$code .= '<div id="story-3-image" class="slot" style="width: 40px; height: 30px; float: left; margin: 0 10px 0 0;">'.$s_3_image_url.'</div>';
		$code .= '<p id="story-3-title">'.getStoryLink($s_3).'</p>';
		$code .= '</div>';
		$code .= '<div class="spacer" style="clear: both;">';
		$code .= '</div>';
		$code .= '<div id="story-4-image" style="margin: 0 40px 0 0; float: left">'.$s_4_image_url.'</div>';
		$code .= '<div class="story-content"><p id="story-4-title">'.getStoryLink($s_4).'</p>';
		$code .= '<p id="story-4-caption">'.$s_4_caption.'</p>';
		$code .= '<div id="story-5-image" class="slot" style="width: 40px; height: 30px; float: left; margin: 0 10px 0 0;">'.$s_5_image_url.'</div>';
		$code .= '<p id="story-5-title">'.getStoryLink($s_5).'</p>';
		$code .= '<div id="story-6-image" class="slot" style="width: 40px; height: 30px; float: left; margin: 0 10px 0 0;">'.$s_6_image_url.'</div>';
		$code .= '<p id="story-6-title">'.getStoryLink($s_6).'</p>';
		$code .= '</div>';
		$code .= '</div>';
		*/

		return $code;
	}
	
	function buildTemplateForProfileBox(&$stories) {
		$s &= $stories;
		list($s_1, $s_2) = $stories;

		$code = '<div id="featured_story" class="clearfix">';
		$code .= '<div class="bullet_list"><ul>';
		$code .= '<li id="story_1_title">'.getStoryLink($s_1).'</li>';
		$code .= '<li id="story_2_title">'.getStoryLink($s_2).'</li>';
		$code .= '</ul></div></div><!-- end featured_story -->';

		return $code;
	}
	
		function getImageLink($story = false, $size = 'large') {
			if (!$story)
				return false;
			$imageid = $story['imageid'];
			$imageUrl = $story['imageUrl'];
			$story_url = $story['url'];
			$cid = $story['siteContentId'];
			$url = "href=\"?p=read&cid=$cid\" onclick=\"return readStory($cid);\"";
			//$largeImageWidth = "180";
			//$largeImageHeight = "135";
			$largeImageWidth = "640";
			$largeImageHeight = "480";
			$smallImageWidth = "40";
			$smallImageHeight = "30";
	/*
			if ($imageid == 0 || !($size == 'large' || $size == 'small'))
				return false;
			else
				return sprintf("<a %s><img src=\"{URL_BASE}/index.php?p=scaleImg&id=%s&x=%s&y=%s&fixed=x&crop\" /></a>", $url, $imageid, ${$size.'ImageWidth'}, ${$size.'ImageHeight'});
	*/
			if ($imageUrl<>'')
				return sprintf("<a %s><img src=\"%s\" /></a>", $url,$imageUrl);
			else
				return false;

		}


	function getStoryLink($story) {
		$cid = $story['siteContentId'];
		$title = $story['title'];

		return "<a href=\"?p=read&cid=$cid\" onclick=\"return readStory($cid);\">$title</a>";
	}

	function getCaption($story) {
		$caption = $story['caption'];
		$cid = $story['siteContentId'];
		require_once(PATH_CORE.'/classes/template.class.php');
		$templateObj = new template();
		$tmp = $templateObj->cleanString($caption, LENGTH_CAPTION);
		$tmp .= ' <a class="more_link" href="?p=read&o=comments&cid='.$cid.'" onclick="return readStory('.$cid.');">&hellip;&nbsp;more</a>';
		//$tmp = substr($caption, 0, 150);
		return $tmp;
	}

?>
