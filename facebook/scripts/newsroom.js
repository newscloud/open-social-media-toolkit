// set the ajax server node for site 
var ajaxUrl=document.getElementById('ajaxNode').getValue(); // URL_CALLBACK from constants.php;
var userid;
var fbId;
var sessionKey;
var sessionExpires;
var authLevel;
var hasSimpleAccess;
var dlg;

function switchPage(name,option,arg3,hack_id) { // hack_id added so setTeamTab calls can pass an id to their targets. 
	// should really have made setTeamTab and switchPage take the same standard set of arguments.
	lookupSession();
	setLoading(document.getElementById('pageContent')); // must happen after lookupSession - it obliterates userid
	// array doesn't work in FBJS
	document.getElementById('tabHome').setClassName('');
	if (document.getElementById('tabWall'))
	{ 
	 document.getElementById('tabWall').setClassName('');
	}
	if (document.getElementById('tabCards'))
	{ 
	 document.getElementById('tabCards').setClassName('');
	}
	if (document.getElementById('tabAsk')) // may need in readStory - if news stories appear in this feature
	{ 
	 document.getElementById('tabAsk').setClassName('');
	}
	if (document.getElementById('tabIdeas')) // may need in readStory - if news stories appear
	{ 
	 document.getElementById('tabIdeas').setClassName('');
	}
	if (document.getElementById('tabPredict')) // may need in readStory - if news stories appear
	{ 
	 document.getElementById('tabPredict').setClassName('');
	}
	if (document.getElementById('tabStuff')) // may need in readStory - if news stories appear
	{ 
	 document.getElementById('tabStuff').setClassName('');
	}
	document.getElementById('tabStories').setClassName('');
	if (document.getElementById('tabPostStory'))
		document.getElementById('tabPostStory').setClassName('');
	document.getElementById('tabProfile').setClassName('');
	document.getElementById('tabTeam').setClassName('');
	
	if (typeof option!='undefined') 
		opt=option;
	else
		opt='';
	if (typeof arg3!='undefined') 
		arg3=arg3;
	else
		arg3='';
	if (typeof hack_id!='undefined') 
		hack_id=hack_id;
	else
		hack_id='';
	
	switch (name) {
		case 'home':
			document.getElementById('tabHome').setClassName('selected');
		break;
		case 'stories':
		case 'read':
			document.getElementById('tabStories').setClassName('selected');
		break;
		case 'postStory':
			if (document.getElementById('tabPostStory'))
				document.getElementById('tabPostStory').setClassName('selected');
		break;
		case 'profile':
			document.getElementById('tabProfile').setClassName('selected');			
			break;
		case 'team':
			document.getElementById('tabTeam').setClassName('selected');
		break;		
		case 'wall':
			document.getElementById('tabWall').setClassName('selected');
		break;
		case 'cards':
			document.getElementById('tabCards').setClassName('selected');
		break;
		case 'ask':
			document.getElementById('tabAsk').setClassName('selected');
		break;
		case 'ideas':
			document.getElementById('tabIdeas').setClassName('selected');
		break;
		case 'stuff':
			document.getElementById('tabStuff').setClassName('selected');
		break;
		case 'predict':
			document.getElementById('tabPredict').setClassName('selected');
		break;
	}
	var ajax = new Ajax();
	if(name in {'home':'','read':'', 'stories':'','team':'','rules':'', 'rewards':'','challenges':'','leaders':'','static':'','links':'','wall':'','stuff':'','ideas':'','ask':'','media':'','micro':'','predict':''}) {
		// leave it open
		ajax.requireLogin = false;
	} else {
		// requires app to be added
		if (fb_sig_logged_out_facebook==0)
			ajax.requireLogin = true;
		// send to sign up page unless they are a member
		if (authLevel!='member' && hasSimpleAccess!=true) { 
			name='signup';
			option='ajax';
		}
	}
	ajax.responseType = Ajax.FBML;
	ajax.onerror = function() {
		// display pop up when session has expired
		showDialog('nonMember');
	} 	
	ajax.ondone = function(data) {
		document.getElementById('pageName').setValue(name); 
		document.getElementById('pageContent').setInnerFBML(data);
	};
	ajax.post(ajaxUrl+"?p=ajax&m=switchPage" +
			"&name="+name +"&option="+opt +"&arg3="+arg3+"&id="+hack_id+
			"&userid="+userid+"&sessionKey="+sessionKey);
	return false; 	
}

function readStory(siteContentId) {
	document.getElementById('tabHome').setClassName('');
	document.getElementById('tabStories').setClassName('selected');
	document.getElementById('tabProfile').setClassName('');
	document.getElementById('tabTeam').setClassName('');	
	if (document.getElementById('tabAsk'))
	{ 
	 document.getElementById('tabAsk').setClassName('');
	}	
	if (document.getElementById('tabIdeas'))
	{ 
	 document.getElementById('tabIdeas').setClassName('');
	}	
	if (document.getElementById('tabStuff'))
	{ 
	 document.getElementById('tabStuff').setClassName('');
	}	
	setLoading(document.getElementById('pageContent'));
	lookupSession();
	log('readStory', siteContentId);
	var ajax = new Ajax();
	ajax.requireLogin = false;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { 
		document.getElementById('pageContent').setInnerFBML(data);
	};
	document.getElementById('pageName').setValue('read');
	ajax.post(ajaxUrl+"?p=ajax&m=switchPage&" +
			"&name=read"+"&option=comments&cid="+siteContentId+
			"&userid="+userid+"&sessionKey="+sessionKey);
	return false; 
}

function setLoading(el) {
	// sets loading graphic to el element
	el.setInnerFBML(loading);	
}

function setSmallLoading(el) {
	// sets loading graphic to el element
	el.setInnerFBML(smallLoading);
}

function lookupSession() {
	// look up current Facebook user session from POST variables and validates session time	
	fb_sig_logged_out_facebook=document.getElementById('fb_sig_logged_out_facebook').getValue();
	userid=document.getElementById('userid').getValue();
	fbId=document.getElementById('fbId').getValue();
	sessionKey=document.getElementById('sessionKey').getValue();
	sessionExpires=document.getElementById('sessionExpires').getValue();
	authLevel=document.getElementById('authLevel').getValue();	
	hasSimpleAccess=document.getElementById('hasSimpleAccess').getValue();	
}

function setTeamTab(newTab, idparam) {
	lookupSession();
	var targetDiv=document.getElementById('teamWrap');
	if (null != targetDiv) {
		document.getElementById('subtabteam').setClassName('');
		if (document.getElementById('subtabrewards')) document.getElementById('subtabrewards').setClassName('');
		if (document.getElementById('subtabchallenges')) document.getElementById('subtabchallenges').setClassName('');
		if (document.getElementById('subtabwall')) document.getElementById('subtabwall').setClassName('');
		document.getElementById('subtabrules').setClassName('');
		document.getElementById('subtableaders').setClassName('');
		if (document.getElementById('subtab'+newTab)) // djm: allows no subtab to be highlighted for team pages that dont have the same label 
		{	document.getElementById('subtab'+newTab).setClassName('selected'); }
		setLoading(targetDiv);
		if (typeof idparam!='undefined') 
			id=idparam;
		else
			id='';
		var ajax = new Ajax();
		ajax.requireLogin = false;	
		ajax.responseType = Ajax.FBML;	
		ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
		ajax.post(ajaxUrl+"?p=ajax&m=switchTeamTab&tab="+newTab+"&userid="+userid+"&sessionKey="+sessionKey+"&id="+id);
		return false;
	} else {	
		switchPage(newTab, 0,0,idparam);
		return false;
	}	
}

function setNewswireTab(newTab) {
	var newswireWrap=document.getElementById('newswireWrap');
	tabAllStories=document.getElementById('tabAllStories');
	tabRawFeeds=document.getElementById('tabRawFeeds');	
	if (newTab=='raw') {
		tabRawFeeds.setClassName('selected');
		tabAllStories.setClassName('');
	} else {
		tabRawFeeds.setClassName('');
		tabAllStories.setClassName('selected');
	}
	setLoading(newswireWrap);
	lookupSession();
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { newswireWrap.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchNewswireWrap&tab="+newTab+"&userid="+userid+"&sessionKey="+sessionKey);
	return false;	
}

function setNewswireFilter(newFilter) {
	var pagingFunction=document.getElementById('pagingFunction');
	switch (newFilter) {
		default:
			document.getElementById('storyFilterSponsor').setClassName();
			document.getElementById('storyFilterFriends').setClassName();
			document.getElementById('storyFilterAll').setClassName('selected');
		break;
		case 'friends':
			document.getElementById('storyFilterSponsor').setClassName();
			document.getElementById('storyFilterFriends').setClassName('selected');
			document.getElementById('storyFilterAll').setClassName();
		break;
		case 'sponsor':
			document.getElementById('storyFilterSponsor').setClassName('selected');
			document.getElementById('storyFilterFriends').setClassName();
			document.getElementById('storyFilterAll').setClassName();
		break;
	}		
	var filter=document.getElementById('filter');
	filter.setValue(newFilter);
	// add selected class
	refreshNewswire();
	return false;	
}

function refreshNewswire() {
	// get values in filter
	var option=document.getElementById('option');
	var filter=document.getElementById('filter');
	var targetDiv=document.getElementById('storyList');
	setLoading(targetDiv);
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchNewswire&filter="+filter.getValue()+"&o="+option.getValue()+"&userid="+userid+"&sessionKey="+sessionKey);
	return false;
}

function refreshPage(pageNumber) { 
	// to do: sort needs to be passed for prizelists...
	var storyList=document.getElementById('storyList');
	setLoading(storyList);
	var pagingFunction=document.getElementById('pagingFunction');
	var option=document.getElementById('option');
	var filter=document.getElementById('filter');
	var category=document.getElementById('category');
	var sort=document.getElementById('sort');
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.ondone = function(data) { storyList.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m="+pagingFunction.getValue() +
			(option 	? ("&o="+option.getValue()) 		: "") +
			(filter 	? ("&filter="+filter.getValue()) 		: "") +
			(category 	? ("&category="+category.getValue()) 	: "") +
			(sort 		? ("&sort="+sort.getValue()) 			: "") +
			"&currentPage="+pageNumber+"&userid="+userid+"&sessionKey="+sessionKey); 
	return false;
}

function setChallengeSort(newSort) {
	//var sort=document.getElementById('sort');
	//sort.setValue(newSort);

	var sort=document.getElementById('sort');
	sort.setValue(newSort);
	document.getElementById('pointValueSort').setClassName('');
	document.getElementById('titleSort').setClassName('');
	document.getElementById('dateStartSort').setClassName('');
	document.getElementById('isFeaturedSort').setClassName('');
	//document.getElementById('rewardsFeedFilter').setClassName('feedFilterButton');
	document.getElementById(newSort+'Sort').setClassName('selected');

	
	//refreshChallenges();
	refreshPage(1);
}

/*
function refreshChallenges() {
	// get values in filter
	//var filter=document.getElementById('filter');
	//var category=document.getElementById('category');
	var sort=document.getElementById('sort');
	
	var ajaxChallenges=document.getElementById('challengeList');
	setLoading(ajaxChallenges);
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { ajaxChallenges.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchChallenges&" +
			"&sort="+sort.getValue() 
			//"&filter="+filter.getValue()
			); // +"&category="+category.getValue()+"&sort="+sort.getValue()
}*/

function setRewardSort(newSort) {
	//var sort=document.getElementById('sort');
	//sort.setValue(newSort);

	var sort=document.getElementById('sort');
	sort.setValue(newSort);
	//REDEEM: document.getElementById('pointCostSort').setClassName('');
	document.getElementById('titleSort').setClassName('');
	document.getElementById('currentStockSort').setClassName('');
	//document.getElementById('rewardsFeedFilter').setClassName('feedFilterButton');
	document.getElementById(newSort+'Sort').setClassName('selected');

	
	refreshRewards();	
}

function setRewardFilter(newFilter) 
{
	//var sort=document.getElementById('sort');
	//sort.setValue(newSort);

	var filter=document.getElementById('filter');
	filter.setValue(newFilter);
	//REDEEM: document.getElementById('redeemableFilter').setClassName('');
	document.getElementById('weeklyFilter').setClassName('');
	document.getElementById('grandFilter').setClassName('');
	//document.getElementById('rewardsFeedFilter').setClassName('feedFilterButton');
	document.getElementById(newFilter+'Filter').setClassName('selected');

	
	refreshRewards();	
}


function refreshRewards() {
	// get values in filter
	var filter=document.getElementById('filter');
	//var category=document.getElementById('category');
	var sort=document.getElementById('sort');
	
	var ajaxRewards=document.getElementById('rewardGrid');
	setLoading(ajaxRewards);
	lookupSession();
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { ajaxRewards.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchRewards&" +
			"&sort="+sort.getValue()+ 
			"&filter="+filter.getValue()+
			"&userid="+userid+"&sessionKey="+sessionKey
			); // +"&category="+category.getValue()+"&sort="+sort.getValue()
}

function setFeedFilter(newFilter)
{
	var filter=document.getElementById('filter');
	filter.setValue(newFilter);
	document.getElementById('allFeedFilter').setClassName('feedFilterButton');
	document.getElementById('storiesFeedFilter').setClassName('feedFilterButton');
	document.getElementById('commentsFeedFilter').setClassName('feedFilterButton');
	document.getElementById('challengesFeedFilter').setClassName('feedFilterButton');
	document.getElementById('rewardsFeedFilter').setClassName('feedFilterButton');
	if (elem=document.getElementById('scorelogFeedFilter')) elem.setClassName('feedFilterButton');
	document.getElementById(newFilter+'FeedFilter').setClassName('selected');
	refreshFeed(1);
}

function setLeaderView(newFilter) {
	document.getElementById('alltimeLeaderView').setClassName('feedFilterButton');
	document.getElementById('weeklyLeaderView').setClassName('feedFilterButton');
	document.getElementById(newFilter+'LeaderView').setClassName('selected');
	document.getElementById('leaderView').setValue(newFilter);
	setLeaderRewardHeadView(newFilter);
	refreshLeaders(newFilter,document.getElementById('leaderFilter').getValue(),1);
	
}

function setLeaderRewardHeadView(newView)
{
	
	document.getElementById('alltimeLeaderRewardHead').setClassName('hidden');
	document.getElementById('weeklyLeaderRewardHead').setClassName('hidden');
	document.getElementById(newView+'LeaderRewardHead').setClassName('');
}

function setLeaderFilter(newFilter) {
	document.getElementById('noneLeaderFilter').setClassName('feedFilterButton');
	document.getElementById('insideLeaderFilter').setClassName('feedFilterButton');
	//document.getElementById('outsideLeaderFilter').setClassName('feedFilterButton');
	document.getElementById(newFilter+'LeaderFilter').setClassName('selected');
	document.getElementById('leaderFilter').setValue(newFilter);
	refreshLeaders(document.getElementById('leaderView').getValue(),newFilter,1);	
}

function refreshLeaders(view,filter,pageNumber) {
	// get values in filter
	var targetDiv=document.getElementById('leaderList');
	setSmallLoading(targetDiv);
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchLeaders" +
			"&view="+view+"&filter="+filter+
			"&currentPage="+pageNumber
			); 
	return false;
}



function refreshFeed(pageNumber) {
	// get values in filter
	var filter=document.getElementById('filter');
	var filter_userid=document.getElementById('filter_userid');
	var filter_challengeid=document.getElementById('filter_challengeid');
	//var category=document.getElementById('category');
	//var sort=document.getElementById('sort');
	var targetDiv=document.getElementById('feedList');
	setSmallLoading(targetDiv);
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchFeed" +
			//"&sort="+sort.getValue() 
			"&filter="+filter.getValue()+
			"&filter_userid="+filter_userid.getValue()+			
			"&filter_challengeid="+filter_challengeid.getValue()+			
			"&currentPage="+pageNumber
			); // +"&category="+category.getValue()+"&sort="+sort.getValue()
	return false;
}

function editBio() 
{
	lookupSession();
	var ajaxBio=document.getElementById('profileBio');
	setSmallLoading(ajaxBio);
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { ajaxBio.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchBioEditor" //+ 
			//"&fbId="+fbId
			+"&userid="+userid+"&sessionKey="+sessionKey
			); 
}

function saveBio() 
{
	lookupSession();
	var bioText = document.getElementById('bioText');
	var queryParams = { "bioText" :bioText.getValue() };

	var ajaxBio=document.getElementById('profileBio');
	setSmallLoading(ajaxBio);
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { ajaxBio.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchBioAreaAndSaveBio"// + 
			//"&fbId="+fbId+
			//"&bioText="+bio.getValue()
			+"&userid="+userid+"&sessionKey="+sessionKey
			,queryParams
			); 
}

function addRawToJournal(itemid) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.ondone = function(transport) {
        document.getElementById('pj_'+itemid).setInnerFBML(transport);	
	}
	ajax.post(ajaxUrl+"?p=ajax&m=addRawToJournal&itemid="+itemid+"&userid="+userid+"&sessionKey="+sessionKey);	 
}

function recordVote(siteContentId) {
	lookupSession();
	if (authLevel=='member' || (authLevel=='nonMember' && hasSimpleAccess)) { // either member or authorized app
		targetDiv=document.getElementById('vl_'+siteContentId);
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.requireLogin = true;
		ajax.onerror = function() {
			// display pop up when session has expired, context is the vote span
			show_error_dialog(targetDiv);
		} 				
		ajax.ondone = function(transport) {
	        targetDiv.setInnerFBML(transport);
		}	
		ajax.post(ajaxUrl+"?p=ajax&m=common&cmd=recordVote&siteContentId="+siteContentId+
				"&userid="+userid+"&sessionKey="+sessionKey);	
	} else {
		if (hasSimpleAccess)
			showDialog('noAuth'); // auth is required
		else
			showDialog('nonMember'); // sign up is required
	}
	return false;
}

function refreshTeamFriendsList(state)
{
	lookupSession();
	var ajaxTeamFriendsList=document.getElementById('ajaxTeamFriendsList');
	document.getElementById('friendsSeeAll').toggleClassName('hidden');
	document.getElementById('friendsSeeFewer').toggleClassName('hidden');
	//var friends=document.getElementById('ajaxTeamUserFriends');

	setSmallLoading(ajaxTeamFriendsList);
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { ajaxTeamFriendsList.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchTeamFriendsList&" +
			"&state="+state +
			"&userid="+userid+"&sessionKey="+sessionKey);
//			"&ajaxTeamUserFriends="+friends.getValue() +
			
}

/*
function accountAgeChanged(changeOptInInfo)
{
	var ageControl=document.getElementById('accountAge');
	
	// refresh age/eligibility-specific text
	
	var ageSpecDiv = document.getElementById('accountAgeSpecificOptInText');	
	var age = parseInt(ageControl.getValue()); 
	
	
	if (changeOptInInfo) {
		// opt in is available, so change headers
		if ((age >= 16 && age <= 25) || age==0) // special case so the inegilibility text doesnt show until a real age is selected
		{	
			ageSpecDiv.setInnerFBML(youthText); // these set up in document with fb:js-string		
		}
		else
		{	
			ageSpecDiv.setInnerFBML(generalText);
		}
	} else {
		ageSpecDiv.setClassName('hidden');
	}
}*/

function log(action,itemid) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { return true;}	
	ajax.post(ajaxUrl+"?p=ajax&m=log" +
			"&action="+action +
			"&itemid="+itemid +
			"&userid="+userid+"&sessionKey="+sessionKey
			); 	
}

function quickLog(log,action,itemid,str) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { return true;}	
	ajax.post(ajaxUrl+"?p=ajax&m=quickLog&" +
			"&log="+log +
			"&action="+action +
			"&itemid="+itemid +
			"&str="+str+
			"&userid="+userid+"&sessionKey="+sessionKey
			); 	
}

function shareStory(context,itemid) {
	lookupSession();
	if (authLevel=='member' || (authLevel=='nonMember' && hasSimpleAccess)) { // either member or authorized app
		var pageName=document.getElementById('pageName');
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.requireLogin = true;
		ajax.onerror = function() {
			// display pop up when session has expired
			showDialog('noSession');
		} 		
	    ajax.ondone = function(data) {
			dlg = new Dialog(Dialog.DIALOG_POP);
			dlg.showChoice('Share this story with your friends', dialogText , 'Send', 'Cancel'); 	
			dlg.oncancel = function() {
				dlg.hide();
			}
			dlg.onconfirm = function() {		
				shareStorySubmit(dlg,'dialog_form','formWrap');
				return false;
			}
		    document.getElementById('dialog_content').setInnerFBML(data); 
	    };
		ajax.post(ajaxUrl+"?p=ajax&m=shareStory&itemid="+itemid+"&returnPage="+pageName.getValue()+"&userid="+userid+"&sessionKey="+sessionKey);
	} else {
		if (hasSimpleAccess)
			showDialog('noAuth'); // auth is required
		else
			showDialog('nonMember'); // sign up is required
	}		
	return false;
} 	

function shareStorySubmit(oldDialog,formname, rewriteid) {
	 lookupSession();
	 targetDiv = document.getElementById(rewriteid);
    form = document.getElementById(formname);
    formdata = form.serialize();
    setSmallLoading(document.getElementById('dialog_content'));
    ajax = new Ajax();
    ajax.responseType = Ajax.FBML;
    ajax.requireLogin = true;
    ajax.error= function(data) {
        targetDiv.setInnerFBML(data);
    }
    ajax.ondone = function(data) {
		oldDialog.showMessage('Share this story with your friends', dialogText , 'Close'); 	
		oldDialog.onconfirm = function() {		
			oldDialog.hide();
		}        
   	    document.getElementById('dialog_content').setInnerFBML(data); 
	 }
	ajax.post(ajaxUrl+"?p=ajax&m=shareStorySubmit&" +
			"&userid="+userid+"&sessionKey="+sessionKey,formdata
			); 
	return false;
}

function postComment(contentid) {
	lookupSession();	
	var urlnode ='videoURL';
	var url = '';
	if (document.getElementById(urlnode))
	{	url = document.getElementById(urlnode).getValue(); }
	var commentMsg=document.getElementById('commentMsg');
	var queryParams = { "commentMsg" :commentMsg.getValue(), "videoURL": url};
	// Build the AJAX object to request the dialog contents
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.post(ajaxUrl+"?p=ajax&m=postComment&cid="+contentid+"&sessionKey="+sessionKey+"&userid="+userid,queryParams);	
	ajax.ondone = function(data) { 
		refreshComments(contentid);
		document.getElementById('dialog_content').setInnerFBML(data); 
	};
	dlg = new Dialog(Dialog.DIALOG_POP); 
	dlg.showMessage('Posting your comment',dialogText ,'Close');
	dlg.onconfirm = function() {
		dlg.hide();
	}
	return false;
}

function refreshComments(contentid) {
	lookupSession();
	// Build the AJAX object to request the dialog contents
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.ondone = function(data2) { 
		document.getElementById('commentList').setInnerFBML(data2); 
	};
	ajax.post(ajaxUrl+"?p=ajax&m=refreshComments&cid="+contentid+"&sessionKey="+sessionKey+"&userid="+userid);
}

function updateProfileTabName(viewerFbId, targetFbId)
{
	// hack: set profile tab name based on viewing user and target user

	if (viewerFbId==targetFbId)
		document.getElementById('tabProfile').setTextValue('My Profile');
	else
		document.getElementById('tabProfile').setTextValue('Profile');

}

function showChallengeSubmitDialog(challengeid) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
    ajax.ondone = function(data) {document.getElementById('dialog_content').setInnerFBML(data); };
	ajax.post(ajaxUrl+"?p=ajax&m=showChallengeSubmitDialog&challengeid="+challengeid+"&userid="+userid+"&sessionKey="+sessionKey);
	dlg = new Dialog(Dialog.DIALOG_POP); 
	dlg.showChoice('Show us what you\'ve done', dialogText , 'Submit', 'Cancel'); 	
	dlg.oncancel = function() {
		dlg.hide();
	}
	dlg.onconfirm = function() {
		document.getElementById('dialog_form').submit();
		dlg.hide();
		return false; // matters?
	}
	return false;
} 

function requestVerify() {
	lookupSession();
	if (authLevel!='member' && hasSimpleAccess!=true) { 
		showDialog('nonMember');
	} else {	
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.requireLogin = true;
		ajax.onerror = function() {
			// display pop up when session has expired, context is the vote span
			showDialog('noSession');
		} 				
		ajax.ondone = function(transport) {
			showDialog('success','Verification Request Sent','Please check your email (or your spam folder) and click on the enclosed link to verify your email address.');
		}	
		ajax.post(ajaxUrl+"?p=ajax&m=requestVerify"+
				"&userid="+userid+"&sessionKey="+sessionKey);
	}
	return false;
}

function hideTip(tip,panel) {
	lookupSession();
	if (tip=='teamIntro')
		panel=document.getElementById('teamPanel');
	else
		panel=document.getElementById('wideTipPanel');
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
    ajax.ondone = function(data) {panel.setInnerFBML(data); };
	ajax.post(ajaxUrl+"?p=ajax&m=hideTip&tip="+tip+"&userid="+userid+"&sessionKey="+sessionKey);
	return false;
} 

function hideDialog() {	
	dlg.hide();
}

function show_error_dialog(el) { 
	dialog = new Dialog(Dialog.DIALOG_CONTEXTUAL).setContext(el).showMessage('Please refresh your session', 'Your application session may have expired. Please refresh this page to reactivate your session.'); 
}

function showDynamicDialog(templateFile,title,dialogName) {
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = false;
    ajax.ondone = function(data) {document.getElementById('dialog_content').setInnerFBML(data); };
	ajax.post(ajaxUrl+"?p=ajax&m=fetchDynamicDialog&t="+templateFile+"&d="+dialogName);
	dlg = new Dialog(Dialog.DIALOG_POP); 
	dlg.showMessage(title, dialogText , 'Close'); 	
	dlg.onconfirm = function() {
		dlg.hide();
	}
}

function showDialog(mode,title,msg) {
	switch (mode) {
		case 'noSession':
			title='Please refresh your session';
			msg=sessionMsg;
		break;
		case 'nonMember':
			title='Sign Up is Required';
			msg=signupMsg;
		break;
		case 'noAuth':
			title='Authorization is Required';
			msg=signupMsg; // same message 
		break;
		default: // pass thru title and msg
		break;
	}
	dlg = new Dialog(Dialog.DIALOG_POP);
	dlg.showMessage(title,msg,'Close');
}

/////////////////////////
// new template editing stuff

function editTemplate(nodeId, shortName, /*helpString,*/ refreshPage)
{
	
	lookupSession();
	
	var queryParams = { "shortName" :shortName/*, "helpString": helpString*/ };

	dlg = new Dialog(Dialog.DIALOG_POP); 
	dlg.showChoice("Edit template "+shortName, dialogText , 'Save', 'Cancel'); 	

	
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = false;
    ajax.ondone = function(data) {document.getElementById('dialog_content').setInnerFBML(data); };
	ajax.post(ajaxUrl+"?p=ajax&m=fetchTemplateEditorDialog"
			+"&userid="+userid+"&sessionKey="+sessionKey
			,queryParams
			); 

	var ajaxTemplate=document.getElementById(nodeId);
	//setSmallLoading(ajaxTemplate); // creates too many problems... 
	
	dlg.onconfirm = function() 
	{
		var newCode = document.getElementById('templateEditorCode').getValue();
		
		var queryParams = { "shortName" :shortName, "code" : newCode };

		
		var ajax = new Ajax();
		ajax.requireLogin = true;
		ajax.responseType = Ajax.FBML;	
		ajax.ondone = function(data) 
		{ 
			ajaxTemplate.setInnerFBML(data);
			if (refreshPage) 
				location.reload(true); 
		}
		ajax.post(ajaxUrl+"?p=ajax&m=saveTemplate" 
				+"&userid="+userid+"&sessionKey="+sessionKey
				,queryParams
				); 
		
		dlg.hide();
		
		return false; // matters?
	}
	
	
}


function clearTemplate(nodeId, shortName, repopulate)
{
	
	lookupSession();
	
	var ajaxTemplate=document.getElementById(nodeId);
	//setSmallLoading(ajaxTemplate); // creates too many problems... 
	
			
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) 
	{ 
		ajaxTemplate.setInnerFBML(data);
	}
	
	if (repopulate) method = 'repopulateTemplate';
	else method = 'clearTemplate';
	
	ajax.post(ajaxUrl+"?p=ajax&m="+method+
			+"&userid="+userid+"&sessionKey="+sessionKey
			+"&shortName="+shortName
			);
	
}

function showVideoPreview() {
	document.getElementById('videoPreview').setClassName(''); // removes hidden class		
}

function videoURLChanged()
{
	var url;
	var urlnode ='videoURL';
	var videoPreviewNode = 'videoPreview';
	document.getElementById('videoPreview').setClassName(''); // removes hidden class	
	if (document.getElementById(urlnode)) { 
		url = document.getElementById(urlnode).getValue();
	} else {
		// named videoEmbed on post story form
		url = document.getElementById('videoEmbed').getValue();
	}
	
	var queryParams = { "videoURL" : url };

	var ajaxPreview=document.getElementById(videoPreviewNode);
	setSmallLoading(ajaxPreview);
	var ajax = new Ajax();
	ajax.requireLogin = true;
	ajax.responseType = Ajax.FBML;	
	ajax.ondone = function(data) { ajaxPreview.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=fetchVideoPreview",queryParams); 
}

function banStoryPoster(cid) {
	// call ajax to ban poster of story cid
	var targetDiv=document.getElementById('banStoryPoster');
	lookupSession();
    ajax = new Ajax();
    ajax.responseType = Ajax.FBML;
    ajax.requireLogin = true;
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=banStoryPoster&cid="+cid+"&userid="+userid+"&sessionKey="+sessionKey); 
}