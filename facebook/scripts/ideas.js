function ideaShare() {
	document.getElementById('ideaShare').toggleClassName('hidden');
	// clear form
}

function ideaShareSubmit(id) {
	lookupSession();
	targetDiv = document.getElementById('ideaShare');
    form = document.getElementById('idea_share_form');
    formdata = form.serialize();
    setSmallLoading(targetDiv);
    ajax = new Ajax();
    ajax.responseType = Ajax.FBML;
    ajax.requireLogin = true;
    ajax.error= function(data) {
        targetDiv.setInnerFBML(data);
    }
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=ideaShareSubmit&id="+id+"&userid="+userid+"&sessionKey="+sessionKey,formdata);
}

function ideaSetCategory(newTagId) {
	// set category to other tag and refresh
	var tagid=document.getElementById('tagid');
	tagid.setValue(newTagId);
	document.getElementById('ideaViewCategoryAll').setClassName('feedFilterButton');
	document.getElementById('ideaViewCategoryTopic').setClassName('feedFilterButton selected');	
	ideaRefreshBrowse();
}

function ideaResetCategory() {
	// set category to all and refresh
	var tagid=document.getElementById('tagid');
	tagid.setValue(0);
	document.getElementById('ideaViewCategoryAll').setClassName('feedFilterButton selected');
	if (document.getElementById('ideaViewCategoryTopic'))
		document.getElementById('ideaViewCategoryTopic').setClassName('feedFilterButton');
	ideaRefreshBrowse();
}

function ideaSetView(newFilter) {
	//var pagingFunction=document.getElementById('pagingFunction');
	switch (newFilter) {
		default:
			document.getElementById('ideaViewNoComment').setClassName('selected');
			document.getElementById('ideaViewRecent').setClassName();
			document.getElementById('ideaViewGreatest').setClassName();
			document.getElementById('ideaViewPopular').setClassName();
			document.getElementById('ideaViewFriends').setClassName();
		break;
		case 'recent':
			document.getElementById('ideaViewNoComment').setClassName();
			document.getElementById('ideaViewRecent').setClassName('selected');
			document.getElementById('ideaViewGreatest').setClassName();
			document.getElementById('ideaViewPopular').setClassName();
			document.getElementById('ideaViewFriends').setClassName();
		break;
		case 'popular':
			document.getElementById('ideaViewNoComment').setClassName();
			document.getElementById('ideaViewRecent').setClassName();
			document.getElementById('ideaViewGreatest').setClassName();
			document.getElementById('ideaViewPopular').setClassName('selected');
			document.getElementById('ideaViewFriends').setClassName();
		break;
		case 'greatest':
			document.getElementById('ideaViewNoComment').setClassName();
			document.getElementById('ideaViewRecent').setClassName();
			document.getElementById('ideaViewGreatest').setClassName('selected');
			document.getElementById('ideaViewPopular').setClassName();
			document.getElementById('ideaViewFriends').setClassName();
		break;
		case 'friends':
		document.getElementById('ideaViewNoComment').setClassName();
		document.getElementById('ideaViewRecent').setClassName();
		document.getElementById('ideaViewGreatest').setClassName();
		document.getElementById('ideaViewPopular').setClassName();
		document.getElementById('ideaViewFriends').setClassName('selected');
		break;
	}		
	var filter=document.getElementById('filter');
	filter.setValue(newFilter);
	ideaRefreshBrowse();
	return false;	
}

function ideaRefreshBrowse() {
	lookupSession();	
	// get values in filter
	var filter=document.getElementById('filter');
	var tagid=document.getElementById('tagid');
	var targetDiv=document.getElementById('ideaList');
	setLoading(targetDiv);
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;	
	ajax.requireLogin = false;	
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=ideaFetchBrowse&tagid="+tagid.getValue()+"&view="+filter.getValue()+"&userid="+userid+"&sessionKey="+sessionKey);
	return false;
}

function toggleAnswerComments(id) {
	var targetDiv=document.getElementById('answer_'+id+'_comments');
	targetDiv.toggleClassName('hidden');
}

function ideaRecordLike(id) {
	// record like of a question
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showDialog('nonMember');
	} else {
		targetDiv=document.getElementById('li_'+id);
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.requireLogin = false;
		ajax.onerror = function() {
			// display pop up when session has expired, context is the vote span
			show_error_dialog(targetDiv);
		} 				
		ajax.ondone = function(transport) {
	        targetDiv.setInnerFBML(transport);
		}	
		ajax.post(ajaxUrl+"?p=ajax&m=ideaRecordLike&id="+id+"&userid="+userid+"&sessionKey="+sessionKey);
	}
	return false;
}

function ideaRefreshAnswers(id) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.ondone = function(data2) { 
		document.getElementById('answerList').setInnerFBML(data2); 
		ideaResetAnswerForm();
	};
	ajax.post(ajaxUrl+"?p=ajax&m=ideaRefreshAnswers&id="+id+"&sessionKey="+sessionKey+"&userid="+userid);
}


function showFullIdeaForm() {
	// full question form
	document.getElementById('fullIdeaForm').setClassName('');
}

function hideIdeaRelated() {
	// where suggested questions appear
	document.getElementById('ideaRelated').setClassName('hidden');
}

function showIdeaRelated() {
	// where suggested questions appear
	document.getElementById('ideaRelated').setClassName('');
}

function ideaAhead(obj) {
  this.obj = obj;
  showFullIdeaForm();

  // Setup the events we're listening to
  this.obj.purgeEventListeners('focus') // we want to get rid of the focus event added in the FBML above
          .addEventListener('focus', this.onfocus.bind(this))
          .addEventListener('keyup', this.onkeyup.bind(this))
          .addEventListener('keydown', this.onkeydown.bind(this))
          .addEventListener('keypress', this.onkeypress.bind(this));

  // Various flags
  this.focused = true;
}

// Show suggestions when the user focuses the text field
ideaAhead.prototype.onfocus = function(event) {
	showIdeaRelated();	
  this.focused = true;
}

// ...and hide it when they leave the text field
ideaAhead.prototype.onblur = function() {
	hideIdeaRelated();
}

// Every keypress updates the suggestions
ideaAhead.prototype.onkeyup = function(event) {
  switch (event.keyCode) {
    case 27: // escape
      hideIdeaRelated();
      break;
    case 0:
    case 13: // enter
    case 37: // left
    case 38: // up
    case 39: // right
    case 40: // down
  //    break;
    default:
      this.update_results();
      break;
  }
}

// We want interactive stuff to happen on keydown to make it feel snappy
ideaAhead.prototype.onkeydown = function(event) {
  switch (event.keyCode) {
    case 9: // tab
    case 13: // enter
		hideIdeaRelated();
      break;

    case 38: // up
    case 40: // down
      break;
  }
}

// Override these events so they don't actually do anything
ideaAhead.prototype.onkeypress = function(event) {
  switch (event.keyCode) {
    case 13: // return
    case 38: // up
    case 40: // down
      break;
  }
}

// This is called every keypress to update the suggestions
ideaAhead.prototype.update_results = function() {
	// check # of words, call for new list via ajax
	panel=document.getElementById('ideaRelated');
	var val=this.obj.getValue().toLowerCase(); 
	var ajax = new Ajax();
	var queryParams = { "qStr" : val };
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = false;
    ajax.ondone = function(data) {panel.setInnerFBML(data);panel.setClassName(''); };
	ajax.post(ajaxUrl+"?p=ajax&m=ideaRelated",queryParams);
}
