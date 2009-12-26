<?php
require_once(PATH_CORE.'/classes/db.class.php');
$db=new cloudDatabase();
$results = $db->query("SELECT SQL_CALC_FOUND_ROWS Content.*,ContentImages.url as imageUrl,UserInfo.fbId FROM Content LEFT JOIN UserInfo ON Content.postedById = UserInfo.userid LEFT JOIN ContentImages ON (Content.siteContentId=ContentImages.siteContentId) ORDER BY Content.date DESC LIMIT 20");
$stories = array();
$story_ids = array();
while ($row = mysql_fetch_assoc($results))
	$stories[] = $row;
?>
<div class="stories" style="border: 1px solid black;">
<?php foreach ($stories as $story): ?>
	<?php $story_id = "story_".$story['siteContentId'];
		  $story_ids[] = "'$story_id'"; ?>
	<div class="story" id="<? echo $story_id; ?>" style="border: 1px solid black; margin: 10px; padding: 10px;">
		<?php if ($story['imageUrl'] <> ''):  ?>
			<img alt="story image" style="max-width:100px;max-height:100px;" src="<? echo $story['imageUrl']; ?>"/>
		<?php endif; // if ($story['imageid'] > 0):  {URL_BASE}/index.php?p=scaleImg&id=<? echo $story['imageid']; ..missing question and close tag. &x=60&y=60&fixed=x&crop ?>
		<? /*<span class="storyHead"><a href="<? echo $story['url']; ?>" target="_cts"><? echo $story['title']; ?></a></span><br /> */ ?>
		<span class="storyHead" style="font-size: 80%;"><? echo $story['title']; ?></span><br />
		<p style="font-size: 70%;">Posted by: <? echo $story['postedByName']; ?></p>
	</div>
<?php endforeach; ?>
</div>
<?php /*
<h1>Current Stories</h1>
<ul>
	<div class="story" id="story_1">
	<p style="clear:both;"><span class="storyHead"><a href="?p=read&o=comments&cid=924&record">Dutch take to skates as cold snap freezes canals</a><br /></span>
	<div class="profilePic"><a href="http://www.newscloud.com/journal/{submitBy}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {submitBy}" /></a></div><br /><span class="storyPosted">Posted by <a href="http://www.newscloud.com/journal/{submitBy}">{timeSince}</a>, ago</span><br />
	<span class="storyCaption">Al Gore ! It's getting colder not warmer. Scientists who look at more than one variable in the climate model have been predicting cooling. Is it time for the truth to be told ?<br /></span>
	</p></div>
	<div class="story" id="story_2">
<p style="clear:both;"><span class="storyHead"><a href="?p=read&o=comments&cid=923&record">Global warming deniers and the English language</a><br /></span>
	<div class="profilePic"><a href="http://www.newscloud.com/journal/{submitBy}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {submitBy}" /></a></div><br /><span class="storyPosted">Posted by <a href="http://www.newscloud.com/journal/{submitBy}">{timeSince}</a>, ago</span><br />
	<span class="storyCaption">By Joseph RommIn his famous essay, "Politics and the English Language," George Orwell wrote:  "The English language  ...  becomes ugly and inaccurate because our thoughts are foolish, but the slovenliness of our language makes it easier for us to ha...<br /></span>
	</p></div>
	<div class="story" id="story_3">
<p style="clear:both;"><span class="storyHead"><a href="?p=read&o=comments&cid=922&record">Global warming deniers and the English language</a><br /></span>
	<fb:profile_pic uid="688429164" linked="true" /><br /><span class="storyPosted">Posted by <a href="'.URL_CANVAS.'?p=profile&memberid=688429164"><fb:name ifcantsee="Anonymous" uid="688429164" capitalize="true" firstnameonly="false" linked="false" /></a>, {timeSince} ago</span><br />
	<span class="storyCaption">By Joseph RommIn his famous essay, "Politics and the English Language," George Orwell wrote:  "The English language  ...  becomes ugly and inaccurate because our thoughts are foolish, but the slovenliness of our language makes it easier for us to ha...<br /></span>
	</p></div>
	<div class="story" id="story_4">
<p style="clear:both;"><span class="storyHead"><a href="?p=read&o=comments&cid=921&record">And they're off ...</a><br /></span>
	<div class="profilePic"><a href="http://www.newscloud.com/journal/{submitBy}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {submitBy}" /></a></div><br /><span class="storyPosted">Posted by <a href="http://www.newscloud.com/journal/{submitBy}">{timeSince}</a>, ago</span><br />
	<span class="storyCaption">By Kate Sheppard    Senate Environment and Public Works Committee Chair Barbara Boxer (D_Calif.) said on Wednesday that she has been consulting with incoming Obama administration officials on a climate plan, though she didn't give a sense of when to...<br /></span>
	</p></div>
	<div class="story" id="story_5">
<p style="clear:both;"><span class="storyHead"><a href="?p=read&o=comments&cid=920&record">And they're off ...</a><br /></span>
	<div class="profilePic"><a href="http://www.newscloud.com/journal/{submitBy}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {submitBy}" /></a></div><br /><span class="storyPosted">Posted by <a href="http://www.newscloud.com/journal/{submitBy}">{timeSince}</a>, ago</span><br />
	<span class="storyCaption">By Kate Sheppard    Senate Environment and Public Works Committee Chair Barbara Boxer (D_Calif.) said on Wednesday that she has been consulting with incoming Obama administration officials on a climate plan, though she didn't give a sense of when to...<br /></span>
	</p></div>
	<div class="story" id="story_6">
<p style="clear:both;"><span class="storyHead"><a href="?p=read&o=comments&cid=919&record">Extreme Alaska cold grounds planes, disables cars</a><br /></span>
	<div class="profilePic"><a href="http://www.newscloud.com/journal/{submitBy}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {submitBy}" /></a></div><br /><span class="storyPosted">Posted by <a href="http://www.newscloud.com/journal/{submitBy}">{timeSince}</a>, ago</span><br />
	<span class="storyCaption">Global Warming, er FREEZING TEMPERATURES making like difficult in Alaska. &quot;Alaskans are accustomed to subzero temperatures but the prolonged conditions have folks wondering what's going on with winter less than a month old.&quot;<br /></span>
	</p></div>
	<div class="story" id="story_7">
<p style="clear:both;"><a href="/index.php?p=readStory&permalink=Researchers_make_car_parts_out_of_coconuts"><img src="{URL_BASE}/index.php?p=scaleImg&id=34608&x=120&y=120&fixed=x&crop" alt="story image" /></a><span class="storyHead"><a href="?p=read&o=comments&cid=918&record">Researchers make car parts out of coconuts</a><br /></span>
	<div class="profilePic"><a href="http://www.newscloud.com/journal/{submitBy}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {submitBy}" /></a></div><br /><span class="storyPosted">Posted by <a href="http://www.newscloud.com/journal/{submitBy}">{timeSince}</a>, ago</span><br />
	<span class="storyCaption">Researchers in Texas are making car parts out of coconuts.  A team at Baylor University has made trunk liners, floorboards and car_door interior covers using fibers from the outer husks of coconuts, replacing the synthetic polyester fibers typically...<br /></span>
	</p></div>
	<div class="story" id="story_8">
<p style="clear:both;"><span class="storyHead"><a href="?p=read&o=comments&cid=917&record">Inventing a New Kind of Family for a New Era</a><br /></span>
	<div class="profilePic"><a href="http://www.newscloud.com/journal/{submitBy}"><img src="http://www.newscloud.com/images/usericon.php?uid={ncId}" alt="Photo of {submitBy}" /></a></div><br /><span class="storyPosted">Posted by <a href="http://www.newscloud.com/journal/{submitBy}">{timeSince}</a>, ago</span><br />
	<span class="storyCaption">by Jay Walljasper During the holidays, people gather together with their families (parents, grandparents, siblings, aunts, uncles, cousins, close friends) for food and kinship. These gatherings, especially in the United States, can be a rare...<br /></span>
	</p></div>
</ul>
*/
?>
