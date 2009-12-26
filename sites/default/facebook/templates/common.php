<?php
// template definitions for common elements - headings, panel titles, and other short text blurbs

$category = 'common';
$common['FeaturedStoriesTitle']= $dynTemp->useDBTemplate('FeaturedStoriesTitle','Featured Stories','',false, $category);
$common['adWrapSidebar']= $dynTemp->useDBTemplate('adSidebarWrap','<div style="margin:0px;padding:0px;">{ad}</div>','Advertising Wrapper Sidebar',false, 'advertising'); 
$common['adWrapLargeBanner']= $dynTemp->useDBTemplate('adWrapLargeBanner','<div class="clearfix" style="padding:5px 0px 0px 0px;margin:0px 0px 10px 0px;background: url('.URL_CALLBACK.'?p=cache&img=p=cache&img=ads/728x90.gif) top left no-repeat;">{ad}</div>','Advertising Wrapper Large Banner',false, 'advertising'); 
$common['adWrapLargeRect']= $dynTemp->useDBTemplate('adWrapLargeRect','<div style="margin:0px;padding:0px;">{ad}</div>','Advertising Wrapper Large Rect',false, 'advertising'); 
$common['adWrapSmallBanner']= $dynTemp->useDBTemplate('adWrapSmallBanner','<div class="clearfix" style="padding:5px 0px 0px 0px;margin:0px 0px 10px 0px;background: url('.URL_CALLBACK.'?p=cache&img=p=cache&img=ads/468x60.gif) top left no-repeat;">{ad}</div>','Advertising Wrapper Small Banner',false, 'advertising'); 
$common['adWrapSkyscraper']= $dynTemp->useDBTemplate('adWrapSkyscraper','<div class="clearfix" style="padding:5px 0px 0px 0px;margin:0px 0px 10px 0px;background: url('.URL_CALLBACK.'?p=cache&img=p=cache&img=ads/160x600.gif) top left no-repeat;">{ad}</div>','Advertising Wrapper Skyscraper',false, 'advertising'); 

?>