<div class="yui-g">
<div class="widget">
<h2>Widget Details: <? echo $widget['title']; ?></h2>

<p id="widget[html]">HTML, FBML or Src:<br /> <?php echo htmlentities($widget['html']); ?></p>
<div class="spacer"></div>

<p id="widget[wrap]">Wrap:<br /> <?php echo htmlentities($widget['wrap']); ?></p>
<div class="spacer"></div>

<p id="widget[width]">Width: <a href="<?php echo htmlentities($widget['width']); ?>"><?php echo htmlentities($widget['width']); ?></a></p>
<div class="spacer"></div>

<p id="widget[height]">Height: <a href="<?php echo htmlentities($widget['height']); ?>"><?php echo htmlentities($widget['height']); ?></a></p>
<div class="spacer"></div>

<p id="widget[style]">Style: <a href="<?php echo htmlentities($widget['style']); ?>"><?php echo htmlentities($widget['style']); ?></a></p>
<div class="spacer"></div>

<p id="widget[type]">Type: <a href="<?php echo htmlentities($widget['type']); ?>"><?php echo htmlentities($widget['type']); ?></a></p>
<div class="spacer"></div>

<p id="widget[isAd]">Is Ad?: <?php echo ($widget['isAd']) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="widget[smartsize]">Is Smartsized?: <?php echo ($widget['smartsize']) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<ul>
	<li><a href="index.php?p=console&group=stories&action=new_widget">Create a new widget</a></li>
	<li><a href="index.php?p=console&group=stories&action=modify_widget&id=<? echo $widget['id']; ?>">Edit this widget</a></li>
	<li><a href="index.php?p=console&group=stories&action=destroy_widget&id=<? echo $widget['id']; ?>">Delete this widget</a></li>
   <li><a href="index.php?p=console&group=stories&action=widgets">Back to Widgets</a></li>
</ul>
</div>
