<div class="yui-g">
<div class="widget">
<h2>Widget Details: <? echo $widget['title']; ?></h2>

<p id="widget[html]">HTML, FBML or Src:<br /> <?php echo htmlentities($widget['html']); ?></p>
<div class="spacer"></div>

<p id="widget[wrap]">Wrap:<br /> <?php echo htmlentities($widget['wrap']); ?></p>
<div class="spacer"></div>

<p id="widget[width]">Width: <?php echo htmlentities($widget['width']); ?></p>
<div class="spacer"></div>

<p id="widget[height]">Height: <?php echo htmlentities($widget['height']); ?></p>
<div class="spacer"></div>

<p id="widget[style]">Style: <?php echo htmlentities($widget['style']); ?></p>
<div class="spacer"></div>

<p id="widget[type]">Type: <?php echo htmlentities($widget['type']); ?></p>
<div class="spacer"></div>

<p id="widget[isAd]">Is Ad?: <?php echo ($widget['isAd']) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<p id="widget[smartsize]">Is Smartsized?: <?php echo ($widget['smartsize']) ? 'Yes' : 'No'; ?></p>
<div class="spacer"></div>

<br /><br />
<p>Actions:</p>
<?php
$link_list = array(
	array('title' => 'Create a new widget', 'ctrl' => 'stories', 'action' => 'new_widget'),
	array('title' => 'Edit this widget', 'ctrl' => 'stories', 'action' => 'modify_widget', 'id' => $widget['id']),
	array('title' => 'Delete this widget', 'ctrl' => 'stories', 'action' => 'destroy_widget', 'id' => $widget['id'], 'onclick' => "if(!confirm('Are you sure you want to remove this item?')) return false"),
   array('title' => 'Back to Widgets', 'ctrl' => 'stories', 'action' => 'widgets')
);
if (($links = build_link_list($link_list))) {
	echo $links;
}
?>
</div>
