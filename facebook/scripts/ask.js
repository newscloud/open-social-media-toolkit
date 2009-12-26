function askShare() {
	document.getElementById('askShare').toggleClassName('hidden');
}

function askShareSubmit(id) {
	lookupSession();
	targetDiv = document.getElementById('askShare');
    form = document.getElementById('ask_share_form');
    formdata = form.serialize();
    setSmallLoading(targetDiv);
    ajax = new Ajax();
    ajax.responseType = Ajax.FBML;
    ajax.requireLogin = true;
    ajax.error= function(data) {
        targetDiv.setInnerFBML(data);
    }
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=askShareSubmit&id="+id+"&userid="+userid+"&sessionKey="+sessionKey,formdata);
}

function askSetCategory(newTagId) {
	// set category to other tag and refresh
	var tagid=document.getElementById('tagid');
	tagid.setValue(newTagId);
	document.getElementById('askViewCategoryAll').setClassName('feedFilterButton');
	document.getElementById('askViewCategoryTopic').setClassName('feedFilterButton selected');	
	askRefreshBrowseQuestions();
}

function askResetCategory() {
	// set category to all and refresh
	var tagid=document.getElementById('tagid');
	tagid.setValue(0);
	document.getElementById('askViewCategoryAll').setClassName('feedFilterButton selected');
	if (document.getElementById('askViewCategoryTopic'))
		document.getElementById('askViewCategoryTopic').setClassName('feedFilterButton');
	askRefreshBrowseQuestions();
}

function askSetView(newFilter) {
	//var pagingFunction=document.getElementById('pagingFunction');
	switch (newFilter) {
		default:
			document.getElementById('askViewNoAnswers').setClassName('selected');
			document.getElementById('askViewRecent').setClassName();
			document.getElementById('askViewGreatest').setClassName();
			document.getElementById('askViewPopular').setClassName();
			document.getElementById('askViewFriends').setClassName();
		break;
		case 'recent':
			document.getElementById('askViewNoAnswers').setClassName();
			document.getElementById('askViewRecent').setClassName('selected');
			document.getElementById('askViewGreatest').setClassName();
			document.getElementById('askViewPopular').setClassName();
			document.getElementById('askViewFriends').setClassName();
		break;
		case 'popular':
			document.getElementById('askViewNoAnswers').setClassName();
			document.getElementById('askViewRecent').setClassName();
			document.getElementById('askViewGreatest').setClassName();
			document.getElementById('askViewPopular').setClassName('selected');
			document.getElementById('askViewFriends').setClassName();
		break;
		case 'greatest':
			document.getElementById('askViewNoAnswers').setClassName();
			document.getElementById('askViewRecent').setClassName();
			document.getElementById('askViewGreatest').setClassName('selected');
			document.getElementById('askViewPopular').setClassName();
			document.getElementById('askViewFriends').setClassName();
		break;
		case 'friends':
		document.getElementById('askViewNoAnswers').setClassName();
		document.getElementById('askViewRecent').setClassName();
		document.getElementById('askViewGreatest').setClassName();
		document.getElementById('askViewPopular').setClassName();
		document.getElementById('askViewFriends').setClassName('selected');
		break;
	}		
	var filter=document.getElementById('filter');
	filter.setValue(newFilter);
	askRefreshBrowseQuestions();
	return false;	
}

function askRefreshBrowseQuestions() {
	// get values in filter
	lookupSession();
	var filter=document.getElementById('filter');
	var tagid=document.getElementById('tagid');
	var targetDiv=document.getElementById('questionList');
	setLoading(targetDiv);
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;	
	ajax.requireLogin = false;	
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=askFetchBrowseQuestions&tagid="+tagid.getValue()+"&view="+filter.getValue()+"&userid="+userid+"&sessionKey="+sessionKey);
	return false;
}

function toggleAnswerComments(id) {
	var targetDiv=document.getElementById('answer_'+id+'_comments');
	targetDiv.toggleClassName('hidden');
}

function askRecordLike(mode,id) {
	// record like of a question
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showDialog('nonMember');
	} else {
		if (mode=='question')
			targetDiv=document.getElementById('ll_'+id);
		else
			targetDiv=document.getElementById('la_'+id);
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
		ajax.post(ajaxUrl+"?p=ajax&m=askRecordLike&mode="+mode+"&id="+id+"&userid="+userid+"&sessionKey="+sessionKey);
	}
	return false;
}

function askResetAnswerForm() {
	// hides and resets answer form after post anwer
	// reset the form content
	answerDetails=document.getElementById('answerDetails');
	answerDetails.setValue('');
	// hide the form
	answerForm=document.getElementById('answerForm');
	answerForm.setClassName('hidden');
}

function askPostAnswer(id) {
	lookupSession();
	var answerDetails=document.getElementById('answerDetails');
	var queryParams = { "answerDetails" :answerDetails.getValue()};
	// Build the AJAX object to request the dialog contents
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.post(ajaxUrl+"?p=ajax&m=askPostAnswer&id="+id+"&sessionKey="+sessionKey+"&userid="+userid,queryParams);	
	ajax.ondone = function(data) {
		askRefreshAnswers(id);
		document.getElementById('dialog_content').setInnerFBML(data); 
	};
	dlg = new Dialog(Dialog.DIALOG_POP); 
	dlg.showMessage('Processing your answer',dialogText ,'Close');
	dlg.onconfirm = function() {
		dlg.hide();
	}
	return false;
}

function askRefreshAnswers(id) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.ondone = function(data2) { 
		document.getElementById('answerList').setInnerFBML(data2); 
		askResetAnswerForm();
	};
	ajax.post(ajaxUrl+"?p=ajax&m=askRefreshAnswers&id="+id+"&sessionKey="+sessionKey+"&userid="+userid);
}

function showAnswerForm() {
	answerForm=document.getElementById('answerForm');
	answerForm.toggleClassName('hidden');
}

function showFullQuestionForm() {
	// full question form
	document.getElementById('fullQuestionForm').setClassName('');
}

function hideAskRelated() {
	// where suggested questions appear
	document.getElementById('askRelated').setClassName('hidden');
}

function showAskRelated() {
	// where suggested questions appear
	document.getElementById('askRelated').setClassName('');
}

function askAhead(obj) {
  this.obj = obj;
  showFullQuestionForm();

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
askAhead.prototype.onfocus = function(event) {
	showAskRelated();	
  this.focused = true;
}

// ...and hide it when they leave the text field
askAhead.prototype.onblur = function() {
	hideAskRelated();
}

// Every keypress updates the suggestions
askAhead.prototype.onkeyup = function(event) {
  switch (event.keyCode) {
    case 27: // escape
      hideAskRelated();
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
askAhead.prototype.onkeydown = function(event) {
  switch (event.keyCode) {
    case 9: // tab
    case 13: // enter
		hideAskRelated();
      break;

    case 38: // up
    case 40: // down
      break;
  }
}

// Override these events so they don't actually do anything
askAhead.prototype.onkeypress = function(event) {
  switch (event.keyCode) {
    case 13: // return
    case 38: // up
    case 40: // down
      break;
  }
}

// This is called every keypress to update the suggestions
askAhead.prototype.update_results = function() {
	// check # of words, call for new list via ajax
	panel=document.getElementById('askRelated');
	var val=this.obj.getValue().toLowerCase(); 
	var ajax = new Ajax();
	var queryParams = { "qStr" : val };
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = false;
    ajax.ondone = function(data) {panel.setInnerFBML(data);panel.setClassName(''); };
	ajax.post(ajaxUrl+"?p=ajax&m=askRelated",queryParams);
}
