<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <title><?php echo ($title != '') ? $title : ' NewsCloud Console'; ?></title>
   <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	 <style type="text/css">
	 /*margin and padding on body element
	   can introduce errors in determining
	   element position and are not recommended;
	   we turn them off as a foundation for YUI
	   CSS treatments. */
	 body {
margin:0;
padding:0;
	}
	 </style>

	 <!--<link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css"> -->
	 <link rel="stylesheet" href="http://yui.yahooapis.com/combo?2.7.0/build/reset-fonts-grids/reset-fonts-grids.css&2.7.0/build/base/base-min.css"> 
	 <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/paginator/assets/skins/sam/paginator.css"> 
	 <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/fonts/fonts-min.css" />
	 <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/container/assets/skins/sam/container.css" />
	 <link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/logger/assets/skins/sam/logger.css"> 
   <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/button/assets/skins/sam/button.css" />
	 <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/calendar/assets/skins/sam/calendar.css" />
   <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/menu/assets/skins/sam/menu.css"> 
   <link rel="stylesheet" type="text/css" href="yui_menu.css"> 

	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/element/element-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/datasource/datasource-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/json/json-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/connection/connection-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/animation/animation-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dragdrop/dragdrop-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/button/button-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/container/container-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/slider/slider-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/datatable/datatable-min.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/paginator/paginator-min.js"></script> 
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/charts/charts-debug.js"></script>
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/calendar/calendar-min.js"></script>
   	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/menu/menu.js"></script>
	 <!--<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/charts/charts-min.js"></script>-->
	 <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/logger/logger-min.js"></script> 
	 <?php if ($controller_name == 'dashpods'): ?>
	 	<script type="text/javascript">
			var pod_group = '<? echo $action_name; ?>';
			var CURR_SITE_ID = <?php echo $curr_site_id; ?>;
		</script>
		 <? if (isset($_REQUEST['new_js'])): ?>
			<script type="text/javascript" src="new_pods.js?<? echo time(); ?>"></script>
		 <?php else: ?>
			<script type="text/javascript" src="pods.js?<? echo time(); ?>"></script>
			<script type="text/javascript">
				<?php echo file_get_contents(PATH_CONSOLE.'/pods.js', true); ?>
			</script>
			<!--<script type="text/javascript" src="pods.js?<? //echo time(); ?>"></script>-->
		 <?php endif; ?>
	 <?php endif; ?>







<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js"></script>
	<?php if ($templateBuilder): ?>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/dragdrop/dragdrop-min.js"></script>
		<?php echo $page->_genScripts(); ?>
		<? //echo $template_src; ?>
		<!--<script src="template.js"></script>-->
	<?php endif; ?>

   <script type="text/javascript">

   /*
      Initialize and render the MenuBar when its elements are ready 
      to be scripted.
    */

   YAHOO.util.Event.onContentReady("management", function () {

       var ua = YAHOO.env.ua,
       oAnim;  // Animation instance


       /*
          "beforeshow" event handler for each submenu of the MenuBar
          instance, used to setup certain style properties before
          the menu is animated.
        */

       function onSubmenuBeforeShow(p_sType, p_sArgs) {

       var oBody,
       oElement,
       oShadow,
       oUL;


       if (this.parent) {

         oElement = this.element;

         /*
            Get a reference to the Menu's shadow element and 
            set its "height" property to "0px" to syncronize 
            it with the height of the Menu instance.
          */

         oShadow = oElement.lastChild;
         oShadow.style.height = "0px";


         /*
            Stop the Animation instance if it is currently 
            animating a Menu.
          */ 

         if (oAnim && oAnim.isAnimated()) {

           oAnim.stop();
           oAnim = null;

         }


         /*
            Set the body element's "overflow" property to 
            "hidden" to clip the display of its negatively 
            positioned <ul> element.
          */ 

         oBody = this.body;


         //  Check if the menu is a submenu of a submenu.

         if (this.parent && 
             !(this.parent instanceof YAHOO.widget.MenuBarItem)) {


           /*
              There is a bug in gecko-based browsers and Opera where 
              an element whose "position" property is set to 
              "absolute" and "overflow" property is set to 
              "hidden" will not render at the correct width when
              its offsetParent's "position" property is also 
              set to "absolute."  It is possible to work around 
              this bug by specifying a value for the width 
              property in addition to overflow.
            */

           if (ua.gecko || ua.opera) {

             oBody.style.width = oBody.clientWidth + "px";

           }


           /*
              Set a width on the submenu to prevent its 
              width from growing when the animation 
              is complete.
            */

           if (ua.ie == 7) {

             oElement.style.width = oElement.clientWidth + "px";

           }

         }


         oBody.style.overflow = "hidden";


         /*
            Set the <ul> element's "marginTop" property 
            to a negative value so that the Menu's height
            collapses.
          */ 

         oUL = oBody.getElementsByTagName("ul")[0];

         oUL.style.marginTop = ("-" + oUL.offsetHeight + "px");

       }

       }


       /*
          "tween" event handler for the Anim instance, used to 
          syncronize the size and position of the Menu instance's 
          shadow and iframe shim (if it exists) with its 
          changing height.
        */

       function onTween(p_sType, p_aArgs, p_oShadow) {

         if (this.cfg.getProperty("iframe")) {

           this.syncIframe();

         }

         if (p_oShadow) {

           p_oShadow.style.height = this.element.offsetHeight + "px";

         }

       }


       /*
          "complete" event handler for the Anim instance, used to 
          remove style properties that were animated so that the 
          Menu instance can be displayed at its final height.
        */

       function onAnimationComplete(p_sType, p_aArgs, p_oShadow) {

         var oBody = this.body,
             oUL = oBody.getElementsByTagName("ul")[0];

         if (p_oShadow) {

           p_oShadow.style.height = this.element.offsetHeight + "px";

         }


         oUL.style.marginTop = "";
         oBody.style.overflow = "";


         //  Check if the menu is a submenu of a submenu.

         if (this.parent && 
             !(this.parent instanceof YAHOO.widget.MenuBarItem)) {


           // Clear widths set by the "beforeshow" event handler

           if (ua.gecko || ua.opera) {

             oBody.style.width = "";

           }

           if (ua.ie == 7) {

             this.element.style.width = "";

           }

         }

       }


       /*
          "show" event handler for each submenu of the MenuBar 
          instance - used to kick off the animation of the 
          <ul> element.
        */

       function onSubmenuShow(p_sType, p_sArgs) {

         var oElement,
             oShadow,
             oUL;

         if (this.parent) {

           oElement = this.element;
           oShadow = oElement.lastChild;
           oUL = this.body.getElementsByTagName("ul")[0];


           /*
              Animate the <ul> element's "marginTop" style 
              property to a value of 0.
            */

           oAnim = new YAHOO.util.Anim(oUL, 
               { marginTop: { to: 0 } },
               .5, YAHOO.util.Easing.easeOut);


           oAnim.onStart.subscribe(function () {

               oShadow.style.height = "100%";

               });


           oAnim.animate();


           /*
              Subscribe to the Anim instance's "tween" event for 
              IE to syncronize the size and position of a 
              submenu's shadow and iframe shim (if it exists)  
              with its changing height.
            */

           if (YAHOO.env.ua.ie) {

             oShadow.style.height = oElement.offsetHeight + "px";


             /*
                Subscribe to the Anim instance's "tween"
                event, passing a reference Menu's shadow 
                element and making the scope of the event 
                listener the Menu instance.
              */

             oAnim.onTween.subscribe(onTween, oShadow, this);

           }


           /*
              Subscribe to the Anim instance's "complete" event,
              passing a reference Menu's shadow element and making 
              the scope of the event listener the Menu instance.
            */

           oAnim.onComplete.subscribe(onAnimationComplete, oShadow, this);

         }

       }


       /*
          Instantiate a MenuBar:  The first argument passed to the 
          constructor is the id of the element in the page 
          representing the MenuBar; the second is an object literal 
          of configuration properties.
        */

       var oMenuBar = new YAHOO.widget.MenuBar("management", { autosubmenudisplay: true, showdelay: 0, hidedelay: 750, lazyload: true });


       /*
          Subscribe to the "beforeShow" and "show" events for 
          each submenu of the MenuBar instance.
        */

       oMenuBar.subscribe("beforeShow", onSubmenuBeforeShow);
       oMenuBar.subscribe("show", onSubmenuShow);


       /*
          Call the "render" method with no arguments since the 
          markup for this MenuBar already exists in the page.
        */

       oMenuBar.render();          

   });

</script>
	<style type="text/css">
	/*.pod.yui-module { border:1px dotted black;padding:5px;margin:10px; display:none; }*/
	.pod.yui-module { padding:5px;margin:10px; display:none; }
	.pod.yui-module .hd { border:1px solid #9ff088;padding:5px; height: 19px; display: block; background-color: #9ff088;}
	.pod.yui-module .bd { border:1px solid #9ff088;padding:5px; }
	.pod.yui-module .ft { border:1px solid #9ff088;padding:5px; }
	.pod {min-width: 260px;}
	#full-view .hd { background-color: #9ff088; }
	.pod.yui-module .hd span {float: left;}
	.pod-menu {float: right;}
	.full-menu {float: right;}
	/*.yui-u {display: block; width: 32%; position: relative;}*/
	/* REQUIRED:: FIND WORK AROUND, OTHERWISE COLUMNS OVERRIDE */
	.yui-u {padding-top: 1px; min-height: 500px; width: 31% !important;}
	.pod .hd {cursor: move;}
	/* TODO: FIX WORDWRAP */
	ul.nav li {
		overflow: hidden;
		word-wrap: break-word;
		text-overflow: ellipsis;
	}
	#main-nav ul{ width: 122px; overflow: hidden;}
	.yui-panel-container .underlay { overflow: visisble; }
	.yui-panel .bd { overflow: auto; }
	.yui-module-container .underlay { overflow: visisble; }
	.yui-module .bd { overflow: auto; }
	/*#dashpods {position:relative;width:100%;}*/
	.asdf-hidden-button {
		display: block;
		height: 0px;
		left: -9999px;
		overflow: hidden;
		position: relative;
		top: -9999px;
		width: 0px;
	}
	.asdf-pod-full-button {
		background-image: url('images/unselected_full.jpg');
		height: 23px;
		width: 21px;
		z-index: 3;
		float: right;
		position: relative;
	}
	.filters-fieldset {
		border: 2px groove threedface;
		margin-left: 2px;
		margin-right: 2px;
		padding: 2px;
		width: 320px;
	}
	.chart-pod
	{
		width: 250px;;
		height: 150px;
	}

	.chart-full
	{
		width: 500px;
		height: 350px;
	}
	.chart_title
	{
		display: block;
		font-size: 1.2em;
		font-weight: bold;
		margin-bottom: 0.4em;
	}
    #dates {
        float:left;
        border: 1px solid #000;
        background-color: #ccc;
        padding:10px;
        margin:10px;
    }

    #dates p {
        clear:both;
    }

    #dates label {
        float:left;
        display:block;
        width:7em;
        font-weight:bold;
    }
	
</style>
</head>
<?php if ($templateBuilder): ?>
	<body onload="loadTemplate('select_templates');" class="yui-skin-sam">
<?php else: ?>
	<body class="yui-skin-sam">
<?php endif; ?>
<!--<div id="doc4" class="yui-t5">-->
<div id="doc3">
   <div id="hd">

	 <!--
     <div style="clear:both;"></div>
     <div class="yui-gf" style="padding-bottom: 10px;">
     <div class="yui-u first"></div>
     <div class="yui-ge">
     <div class="yui-u first"></div>
     <div class="yui-u"></div>
     </div>
     </div>
     <div style="clear:both;"></div>
	 -->

		<div class="clear"></div>
		<strong><?php echo SITE_TITLE; ?> Console</strong><br />
     <div id="management" class="yuimenubar yuimenubarnav" style="margin-left: 0px; width: 100%;">
       <div class="bd">

         <ul class="first-of-type" style="padding-left: 50px;">

           <?php if (($url = url_for('stories', false))): ?>
           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Editorial</a>
             <div id="stories" class="yuimenu">
               <div class="bd">                    
                 <ul>
                   <?php if (($url = url_for('stories', 'featured'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Featured Stories</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('stories', 'deliver_featured'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Deliver Features</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('stories', 'story_posts'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Review Stories</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('stories', 'comments'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Review Comments</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('stories', 'video_posts'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Review Videos</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('stories', 'widgets'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Widgets</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('stories', 'edittemplates'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Site Templates</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('stories', 'list_feed'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Feed List</a></li>
                   <?php endif; ?>
                 </ul>
               </div>
             </div>                    
           </li>
           <?php endif; ?>

           <?php if (($url = url_for('members', false))): ?>
           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Community</a>
             <div id="members" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <?php if (($url = url_for('members', 'members'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Manage Members</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('members', 'member_emails'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Manage Email Messages</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('members', 'outboundmessages'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Manage Outbound Messages</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('members', 'cards'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Manage Cards</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('members', 'forumtopics'))):   ?> 
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Manage Forum Topics</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('members', 'folders'))):   ?> 
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Manage Resource Folders</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('members', 'folderlinks'))):   ?> 
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Manage Resource Links</a></li>
                   <?php endif; ?>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://www.facebook.com/apps/application.php?id=<?php global $init; echo $init['fbAppId'] ?>" target="_blank">Facebook About Page</a></li>
                 </ul>                    
               </div>
             </div>                                        
           </li>
           <?php endif; ?>


           <?php if (($url = url_for('street_team', false))): ?>
           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Action Team</a>
             <div id="street_team" class="yuimenu">
               <div class="bd">                    
                 <ul>
                   <!-- <?php if (($url = url_for('street_team', 'feature_panel'))): ?>
 		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Features</a></li> -->
 <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'challenges'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Challenges</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'completed_challenges'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Completed Challenges</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'prizes'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Prizes</a></li>
                   <?php endif; ?>
                   <!-- <?php if (($url = url_for('street_team', 'winners'))): ?>
 		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Winners</a></li> -->
 <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'orders'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Orders</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'leaders'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Leaders</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'update_weekly_leaders'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Update Weekly Leaders</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'updateScores'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Update All Scores</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'prepareContest'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Prepare Contest Start</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'resetContestAdmins'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Reset Admins</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('street_team', 'cleanupOrphans'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Clean up orphan data</a></li>
                   <?php endif; ?>

                 </ul>                    
               </div>
             </div>                                        
           </li>
           <?php endif; ?>

		           <?php if (($url = url_for('dashpods', 'main'))): ?>
		           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Statistics</a>
		             <div id="dashpods-menu" class="yuimenu">
		               <div class="bd">                    
		                 <ul>
		                   <?php if (($url = url_for('dashpods', 'main'))): ?>
		                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Overview</a></li>
		                   <?php endif; ?>
		                   <?php if (($url = url_for('dashpods', 'members'))): ?>
		                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Members</a></li>
		                   <?php endif; ?>
		                   <?php if (($url = url_for('dashpods', 'stories'))): ?>
		                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Stories</a></li>
		                   <?php endif; ?>
		                   <?php if (($url = url_for('dashpods', 'stats'))): ?>
		                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Activities</a></li>
		                   <?php endif; ?>
		                   <?php if (($url = url_for('dashpods', 'geo'))): ?>
		                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Geography</a></li>
		                   <?php endif; ?>
		                   <?php if (($url = url_for('dashpods', 'challenges'))): ?>
		                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Challenges</a></li>
		                   <?php endif; ?>
		                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="https://www.google.com/analytics/reporting/?id=<?php global $init; echo $init['analyticsId'] ?>" target="_blank">Google Analytics</a></li>
		                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://www.new.facebook.com/business/insights/app.php?id=<?php global $init;  echo $init['fbAppId'] ?>" target="_blank">Facebook Insight</a></li>
		<!--                   <?php if (($url = url_for('dashpods', 'misc'))): ?>
		                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Misc Pods</a></li>
		                   <?php endif; ?> -->
		                 </ul>
		               </div>
		             </div>                    
		           </li>
		           <?php endif; ?>


           <?php if (($url = url_for('research', false))): ?>
           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Research</a>
             <div id="research" class="yuimenu">
               <div class="bd">                    
                 <ul>
                   <?php if (($url = url_for('research', 'export_users'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Export Users</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('research', 'export_stories'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Export Stories</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('research', 'export_challenges'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Export Challenges</a></li>
                   <?php endif; ?>
                 </ul>                    
               </div>
             </div>                                        
           </li>
           <?php endif; ?>
           <?php if (($url = url_for('admin', false))): ?>
           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Administrative</a>
             <div id="admin" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <?php if (($url = url_for('admin', 'cronjobs'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Scheduled Tasks</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('admin', 'cloud_properties'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Cloud Properties</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('admin', 'database'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Database</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('admin', 'flushfeeds'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Flush Newswire</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('admin', 'sitestatus'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Toggle site status</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('admin', 'insert_survey_monkey_data'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Load Survey Monkey CSV</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('admin', 'export_users'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Export Users</a></li>
                   <?php endif; ?>

                 </ul>                    
               </div>
             </div>                                        
           </li>
           <?php endif; ?>

           <?php if (($url = url_for('facebook', false))): ?>
           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Facebook</a>
             <div id="facebook" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <?php if (($url = url_for('facebook', 'registerfeedtemplates'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Register Feed Templates</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('facebook', 'syncallocations'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Sync Allocations</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('facebook', 'downloadsettings'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Download settings</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('facebook', 'initprofilebox'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Initialize Profile Box</a></li>
                   <?php endif; ?>
                   <?php if (($url = url_for('facebook', 'deletefeedtemplates'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">Delete Feed Templates</a></li>                   
                   <?php endif; ?>
                 </ul>                    
               </div>
             </div>                                        
           </li>
           <?php endif; ?>

<!--
	           <?php if (($url = url_for('statistics', false))): ?>
           		<li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="<?php echo $url; ?>">Statistics</a>
             <div id="statistics" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <?php if (($url = url_for('statistics', 'statistics'))): ?>
                   		<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?php echo $url; ?>">View Statistics</a></li>
                   <?php endif; ?>
                 </ul>                    
               </div>
             </div>                                        
           </li>
           <?php endif; ?>
-->

         </ul>            
       </div>
     </div>

     <br />
   </div>
   <!-- OPTIONALLY LOAD DASHPODS -->
   <?php if ($controller_name == 'dashpods'): ?>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tbody>
	<tr>
	<td id="col1" width="133px" height="100%" style="vertical-align: top;">
						<div class="col1_contents">
							<div id="main-nav">
								<p>Dash Pods</p>
							<ul id="dashpod-nav" class="nav">
							</ul>
						</div>
					</div>
	</td>
	<td id="col2" style="vertical-align: top;">
	<div id="dashpods">
   <?php endif; ?>
   <div id="bd">
   	<div id="yui-main">
      <div id="flash_notice" class="yui-g">
        <div style="color: green"><h1><?php if (isset($flash) && isset($flash['notice']) && $flash['notice'] != '') echo $flash['notice']; ?></h1></div>
      </div>
      <div id="error_notice" class="yui-g">
        <div style="color: red"><h1><?php if (isset($flash) && isset($flash['error']) && $flash['error'] != '') echo $flash['error']; ?></h1></div>
      </div>

	 	<?php if ($controller_name == 'dashpods'): ?>
	 		<div class="yui-b">
						<div class="yui-gb"> 
							<div id="pod-col-1" class="yui-u first"> 
					<!-- YOUR DATA GOES HERE --> 
							</div> 
							<div id="pod-col-2" class="yui-u"> 
					<!-- YOUR DATA GOES HERE --> 
							</div> 
							<div id="pod-col-3" class="yui-u"> 
					<!-- YOUR DATA GOES HERE --> 
							</div> 
						</div> <!-- end yui-gb -->
		<!-- ELSE load <div class="yui-g"> in view file
		<?php //else: ?>
		<?php endif; ?>
<!-- MAIN CONTENT GOES HERE. THIS IS INCLUDED IN THE MAIN CONSOLE.PHP FILE -->
