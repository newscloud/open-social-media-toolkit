<?php
// template definitions for common elements - headings, panel titles, and other short text blurbs


$common['FeaturedStoriesTitle']= 'Daily Featured Stories';
$common['adWrapSidebar']= $dynTemp->useDBTemplate('adSidebarWrap','<div style="font-size: 10px; line-height: 10px;"><a href="http://www.mndaily.com/mediakit?safe" target="_blank">Advertisement</a></div><div style="margin:0px 0px 5px 0px;"><fb:iframe src="'.URL_CALLBACK.'?p=cache&m=ad&locale=anySidebar" frameborder="0" scrolling="no" style="width:254px;height:120px;margin:0px 0px 5px -10px;padding:0px;overflow:hidden;"/></div>','Advertising Wrapper - Sidebar',false, 'advertising'); 

?>