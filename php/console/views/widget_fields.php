Title<br />
<input type="text" name="widget[title]" value="<?php echo htmlentities($widget['title']); ?>" />
<div class="spacer"></div>

HTML, FBML or iFrame SRC<br />
<textarea name="widget[html]" cols="100" rows="15"><?php echo htmlentities(($widget['html'])); ?></textarea>
<div class="spacer"></div>

Wrap<br />
Should embed a token called {widget} which will be replaced at display time with HTML, FBML or iFrame above. Allows you to wrap an object with local FBML or HTML.
<textarea name="widget[wrap]" cols="100" rows="5"><?php 
if ($widget['wrap']=='') {
	echo htmlentities('<div style="text-align:center;margin:0px 0px 10px 0px;">{widget}</div>');
} else {
	echo htmlentities($widget['wrap']); 
	} 
?></textarea>
<div class="spacer"></div>

Width (optional)<br />
<input type="text" name="widget[width]" value="<?php echo htmlentities($widget['width']); ?>" />
<div class="spacer"></div>

Height (optional)<br />
<input type="text" name="widget[height]" value="<?php echo htmlentities($widget['height']); ?>" />
<div class="spacer"></div>

<br />Style (optional)<br />
Note: Your style may get over-written by the wrap styles.<br />
<input type="text" name="widget[style]" value="<?php echo htmlentities($widget['style']); ?>" />
<div class="spacer"></div>

Use Smartsize? (optional)<br />
<input type="radio" name="widget[smartsize]" value="0" <? if ($widget['smartsize'] == 0) echo 'checked'; ?> />No
<input type="radio" name="widget[smartsize]" value="1" <? if ($widget['smartsize'] == 1) echo 'checked'; ?> />Yes
<div class="spacer"></div>

Advertisement?<br />
<input type="radio" name="widget[isAd]" value="0" <? if ($widget['isAd'] == 0) echo 'checked'; ?> />No
<input type="radio" name="widget[isAd]" value="1" <? if ($widget['isAd'] == 1) echo 'checked'; ?> />Yes
<div class="spacer"></div>
<br />
<strong>Type</strong><br />
Which should I use? <br/>
Remote iFrame - use this when you are supplying a URL SRC pointing to a remote web page to place in an iFrame. For this paste just the URL into the HTML field above.<br />
FBML or HTML - use this when you are supplying Facebook Markup Language (FBML) or HTML to display directly on the Facebook Application Canvas Page<br />
Embedded Object or JavaScript- use this if you are placing an embed object, javascript or some other widget that the Facebook platform will not display directly e.g. &lt;object ...&gt; or &lt;embed ...&gt;<br />
<br/> 
<select name="widget[type]">
<option value="src"<? if ($widget['type'] == 'src') echo ' selected'; ?>>Remote iFrame src</option>
<option value="fbml"<? if ($widget['type'] == 'fbml') echo ' selected'; ?>>FBML or HTML</option>
<option value="script"<? if ($widget['type'] == 'script') echo ' selected'; ?>>Embedded object or JavaScript</option>
</select>
<div class="spacer"></div>

