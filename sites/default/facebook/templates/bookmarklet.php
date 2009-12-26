<?php

$firefoxCode="javascript:if(window.getSelection)txt=window.getSelection();else if(document.getSelection)txt=document.getSelection();else if(document.selection)txt=document.selection.createRange().text;var%20d=document,f='".URL_CANVAS."?p=postStory',l=d.location,e=encodeURIComponent,p='&src=bm&v=4&i=1231274279&u='+e(l.href)+'&t='+e(d.title)+'&c='+e(txt);1;try{if%20(!/^(.*\.)?facebook\.[^.]*$/.test(l.host))throw(0);share_internal_bookmarklet(p)}catch(z)%20{a=function()%20{if%20(!window.open(f+p,'sharer','toolbar=1,status=1,location=1,scrollbars=1,resizable=1,width=800,height=700'))l.href=f+p};if%20(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else{a()}}void(0)";
$code.='<div id="share_button_dialog">' .
	'<a title="Drag this link to your Bookmarks Bar. Click to learn more." class="share_button_browser_link" onclick="alert(\'Drag this button to your Bookmarks Bar.\'); return false;" href="'.$firefoxCode.'">' .
	' <div><div><div><div>Post to '.SITE_TITLE.'</div></div></div></div></div></a><p>Drag the button above to your Bookmarks Bar to quickly share content with your friends.</p></p></div>';								

$category = 'bookmarklet';
$code = $dynTemp->useDBTemplate('BookmarkletPanel',
'<html><head><style>/* Bookmarklet Panel */
body {
	direction:ltr;
	font-family:'.FONTS_MAIN.';
	font-size:11px;
	text-align:left;
	direction:ltr;
	unicode-bidi:embed;
	margin:0px;
	padding:0px;
	}
#bookmarkletPanel {
	padding: 10px 10px 3px 10px;
	margin: 0 0 5px 0;
	background: url('.URL_BASE.'/index.php?p=grad&color1='.CLR_LINKS.'&color2='.CLR_KEY3.'&size=200) repeat-x top #'.CLR_KEY3.';
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	}

#bookmarkletPanel p {
	font-size:11px;
	text-align:left;
	margin: 0 0 7px 0;
	padding: 0px;
	color: #fff;
	}

#bookmarkletPanel h2 {
	font-family:'.FONTS_MAIN.';
	font-size: 14px;
	font-weight: bold;
	margin: 0 0 7px 0;
	padding: 0;
	color: #fff;
	}

#bookmarkletPanel .btn_1 {
	display: inline;
	padding: 5px 12px 6px 12px;
	color: #fff;
	font-weight: bold;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	background: url('.URL_BASE.'/index.php?p=grad&color1=9daccc&color2='.CLR_LINKS.'&size=14) repeat-x top #'.CLR_LINKS.';
	font-size:13px;
	text-decoration:none;
	}

#bookmarkletDemo {
	width: 225px;
	height: 26px;
	padding: 70px 0 0 0;
	margin: 15px 0 10px 0;
	background: url('.URL_BASE.'/index.php?p=cache&simg=bookmarklet-demo.png) top left no-repeat;
	}

</style></head><body><div id="bookmarkletPanel" class="clearfix">
	<h2>There&rsquo;s an easier way to submit stories!</h2>
	<p>Drag the &ldquo;Post to the '.SITE_TITLE_SHORT.'&rdquo; button below to your browser links bar, as shown&hellip;&nbsp;</p>
    <div id="bookmarkletDemo"><a class="btn_1" onclick="alert(\'Drag this button to your Bookmarks Bar.\'); return false;" href="'.$firefoxCode.'">Post to '.SITE_TITLE.'</a></div>
	<p>Once there, just click it from a story on any website or blog to post it to '.SITE_TITLE.'!</p>
</div><!--end "bookmarkletPanel"--></body></html>', 'This is the bookmarklet', false, $category);
?>

