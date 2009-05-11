<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <title><?php echo ($title != '') ? $title : 'NewsCloud Management Console'; ?></title>
   <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
   <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.6.0/build/reset-fonts-grids/reset-fonts-grids.css&2.6.0/build/base/base-min.css"> 
   <!--<link rel="stylesheet" href="http://yui.yahooapis.com/2.5.1/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">-->
   <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/menu/assets/skins/sam/menu.css"> 
   <link rel="stylesheet" type="text/css" href="yui_menu.css"> 
   <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.6.0/build/button/assets/skins/sam/button.css" />
   <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/yahoo-dom-event/yahoo-dom-event.js"></script>
   <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/animation/animation.js"></script>

   <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/container/container_core.js"></script>
   <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/menu/menu.js"></script>
   <?php if (true): ?>
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/json/json-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/element/element-beta-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/button/button-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/connection/connection-min.js"></script>

<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.6.0/build/charts/charts-experimental-min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js"></script>
	<?php endif; ?>   
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
</head>
<?php if ($templateBuilder): ?>
	<body onload="loadTemplate('select_templates');" class="yui-skin-sam">
<?php else: ?>
	<body class="yui-skin-sam">
<?php endif; ?>
<div id="doc4" class="yui-t5">
   <div id="hd">

     <div style="clear:both;"></div>
     <div class="yui-gf" style="padding-bottom: 10px;">
     <div class="yui-u first"></div>
     <div class="yui-ge">
     <div class="yui-u first"></div>
     <div class="yui-u"></div>
     </div>
     </div>
     <div style="clear:both;"></div>

		<div class="clear"></div>
     <div id="management" class="yuimenubar yuimenubarnav" style="margin-left: 0px; width: 100%;">
       <div class="bd">

         <ul class="first-of-type" style="padding-left: 50px;">
           <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="index.php?p=console&group=stories">Editorial</a>
             <div id="stories" class="yuimenu">
               <div class="bd">                    
                 <ul>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=stories&action=featured">Featured Stories</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=stories&action=comments">Review Comments</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=stories&action=story_posts">Review Story Posts</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=stories&action=video_posts">Review Videos</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=stories&action=widgets">Widgets</a></li>
                 </ul>
               </div>
             </div>                    
           </li>
           <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="index.php?p=console&group=members">Community</a>
             <div id="members" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=members&action=members">Manage Members</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=members&action=member_emails">Manage Email Messages</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=members&action=outboundmessages">Manage Outbound Messages</a></li>
                 </ul>                    
               </div>
             </div>                                        
           </li>


           <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="index.php?p=console&group=street_team">Action Team</a>
             <div id="street_team" class="yuimenu">
               <div class="bd">                    
                 <ul>
                   <!-- <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=street_team&action=feature_panel">Features</a></li> -->
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=street_team&action=challenges">Challenges</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=street_team&action=completed_challenges">Completed Challenges</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=street_team&action=prizes">Prizes</a></li>
                   <!-- <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=street_team&action=winners">Winners</a></li> -->
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=street_team&action=orders">Orders</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=street_team&action=leaders">Leaders</a></li>
                 </ul>                    
               </div>
             </div>                                        
           </li>

           <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="index.php?p=console&group=admin">Administrative</a>
             <div id="admin" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=admin&action=cronJobs">Scheduled Tasks</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=admin&action=cloud_properties">Cloud Properties</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=admin&action=feed_list">Feed List</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=admin&action=database">Database</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=admin&action=flushfeeds">Flush Newswire</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=admin&action=sitestatus">Toggle site status</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=admin&action=editTemplates">Edit Site Templates</a></li>
                 </ul>                    
               </div>
             </div>                                        
           </li>
           <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="index.php?p=console&group=facebook">Facebook</a>
             <div id="facebook" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://www.facebook.com/apps/application.php?id=<?php global $init; echo $init['fbAppId'] ?>" target="_blank">About Page</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=facebook&action=registerFeedTemplates">Register Feed Templates</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=facebook&action=syncAllocations">Sync Allocations</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=facebook&action=downloadSettings">Download settings</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=facebook&action=initProfileBox">Initialize Profile Box</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=facebook&action=deleteFeedTemplates">Delete Feed Templates</a></li>                   
                 </ul>                    
               </div>
             </div>                                        
           </li>
           <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="index.php?p=console&group=statistics">Statistics</a>
             <div id="statistics" class="yuimenu">
               <div class="bd">                                        
                 <ul>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="index.php?p=console&group=statistics&action=statistics">View Statistics</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="https://www.google.com/analytics/reporting/?id=<?php global $init; echo $init['analyticsId'] ?>" target="_blank">Google Analytics</a></li>
                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://www.new.facebook.com/business/insights/app.php?id=<?php global $init;  echo $init['fbAppId'] ?>" target="_blank">Facebook Insight</a></li>
                 </ul>                    
               </div>
             </div>                                        
           </li>
         </ul>            
       </div>
     </div>

     <br />
   </div>
   <div id="bd">
   	<div id="yui-main">
      <div id="flash_notice" class="yui-g">
        <div style="color: green"><h1><?php if (isset($flash) && isset($flash['notice']) && $flash['notice'] != '') echo $flash['notice']; ?></h1></div>
      </div>
      <div id="error_notice" class="yui-g">
        <div style="color: red"><h1><?php if (isset($flash) && isset($flash['error']) && $flash['error'] != '') echo $flash['error']; ?></h1></div>
      </div>

<!-- MAIN CONTENT GOES HERE. THIS IS INCLUDED IN THE MAIN CONSOLE.PHP FILE -->
