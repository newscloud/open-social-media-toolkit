<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title>YUI Base Page</title>
   <!--<link rel="stylesheet" href="http://yui.yahooapis.com/2.5.1/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">-->
   <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.6.0/build/reset-fonts-grids/reset-fonts-grids.css&2.6.0/build/base/base-min.css"> 
<!--CSS file (default YUI Sam Skin) -->
<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.6.0/build/logger/assets/skins/sam/logger.css">

<!-- Dependencies -->
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>

<!-- OPTIONAL: Drag and Drop (not required if not enabling drag and drop) -->
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/dragdrop/dragdrop-min.js"></script>

<!-- Source file -->
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/logger/logger-min.js"></script>
<? /*
	<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
	<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/dragdrop/dragdrop-min.js"></script>
*/ ?>

	<!--  <script type="text/javascript">
		var URL_CANVAS = '<?php echo URL_CANVAS; ?>';
		var SITE_TITLE = '<?php echo SITE_TITLE; ?>';
	</script>-->
   <script src="template.js"></script>
</head>
<body onload="loadTemplate('select_templates');">
<div id="doc4" class="yui-t5">
   <div id="hd">
   	<h1>Featured Story Builder</h1>
   </div>
   <div id="bd">
	<div id="yui-main">
	</div>
	<div id="yui-nav" class="yui-b" style="border: 1px solid black;"><!-- YOUR NAVIGATION GOES HERE -->
	<h1>My nav</h1>
	</div>
	
	</div>
	<br />
   <div id="ft">Footer is here.</div>
   	<input type="hidden" id="URL_CANVAS" value="<?php echo URL_CANVAS;?>"/>
   	<input type="hidden" id="SITE_TITLE" value="<?php echo SITE_TITLE;?>"/> 
	<div id="logger-container"></div>
<script type="text/javascript">
var myContainer = document.getElementById('logger-container');
var myLogReader = new YAHOO.widget.LogReader(myContainer);
</script>
</div>
</body>
</html>
