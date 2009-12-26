function resetStuffAccess() { 
	var ifo=document.getElementById('isFriendsOnly');
	if (ifo.getChecked()) {
		// reset others
		tmp=document.getElementById('accessCity');
		if (tmp) tmp.setChecked(false);
		for (i=0;i<5;i++) {
			tmp=document.getElementById('aclGrp_'+i);
			if (tmp) tmp.setValue(0);
		}
		numNetworks=document.getElementById('numNetworks').getValue();
		for(i=0; i < numNetworks; i++)
        {	
            document.getElementById('aclNetCheck_'+i).setChecked(false);
		}
	}
}

function updateStuffAccess() {
	document.getElementById('isFriendsOnly').setChecked(false);	
}

function stuffSetStatus() {
	id=document.getElementById('stuffid').getValue();
	newStatus=document.getElementById('stuffStatus').getValue();	
		lookupSession();
		if (hasSimpleAccess!=true) { 
			showDialog('nonMember');
		} else {
			var ajax = new Ajax();
			ajax.responseType = Ajax.FBML;
			ajax.requireLogin = false;
			ajax.ondone = function(transport) {
		       // do nothing
			}	
			ajax.post(ajaxUrl+"?p=ajax&m=stuffSetStatus&id="+id+"&newStatus="+newStatus+"&userid="+userid+"&sessionKey="+sessionKey);
		}
	}

function stuffSetVisibility() {
	id=document.getElementById('stuffid').getValue();
	newVis=document.getElementById('stuffVisibility').getValue();	
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showDialog('nonMember');
	} else {
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.requireLogin = false;
		ajax.ondone = function(transport) {
	       // do nothing
		}	
		ajax.post(ajaxUrl+"?p=ajax&m=stuffSetVisibility&id="+id+"&newVis="+newVis+"&userid="+userid+"&sessionKey="+sessionKey);
	}
}

function stuffRecordLike(id) {
	// record like of a question
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showDialog('nonMember');
	} else {
		targetDiv=document.getElementById('ls_'+id);
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
		ajax.post(ajaxUrl+"?p=ajax&m=stuffRecordLike&id="+id+"&userid="+userid+"&sessionKey="+sessionKey);
	}
	return false;
}

function stuffSetFilter(group,newFilter) {
	//var pagingFunction=document.getElementById('pagingFunction');
	switch (group) {
		default:  // view
			switch (newFilter) {
				default:
					document.getElementById('stuffViewAll').setClassName('selected');
					document.getElementById('stuffViewFriends').setClassName();
				break;
				case 'friends':
					document.getElementById('stuffViewAll').setClassName();
					document.getElementById('stuffViewFriends').setClassName('selected');
				break;
			}		
			filter=document.getElementById('filterView');
			filter.setValue(newFilter);
		break;
		case 'type':
			switch (newFilter) {
				default:
					document.getElementById('stuffTypeShare').setClassName('selected');
					document.getElementById('stuffTypeFree').setClassName();
				break;
				case 'free':
					document.getElementById('stuffTypeShare').setClassName();
					document.getElementById('stuffTypeFree').setClassName('selected');
				break;
			}		
			filter=document.getElementById('filterType');
			filter.setValue(newFilter);
		break;
		case 'status':
			switch (newFilter) {
				default:
					document.getElementById('stuffStatusAvailable').setClassName('selected');
					document.getElementById('stuffStatusAll').setClassName();
				break;
				case 'all':
					document.getElementById('stuffStatusAvailable').setClassName();
					document.getElementById('stuffStatusAll').setClassName('selected');
				break;
			}		
			filter=document.getElementById('filterStatus');
			filter.setValue(newFilter);
		break;
	}
	stuffRefreshSearch();
}

function stuffSetCategory(newTagId) {
	// set category to other tag and refresh
	var tagid=document.getElementById('tagid');
	tagid.setValue(newTagId);
	document.getElementById('stuffCategoryAll').setClassName('feedFilterButton');
	document.getElementById('stuffCategoryTopic').setClassName('feedFilterButton selected');	
	stuffRefreshSearch();
}

function stuffResetCategory() {
	// set category to all and refresh
	var tagid=document.getElementById('tagid');
	tagid.setValue(0);
	document.getElementById('stuffCategoryAll').setClassName('feedFilterButton selected');
	if (document.getElementById('stuffCategoryTopic'))
		document.getElementById('stuffCategoryTopic').setClassName('feedFilterButton');
	stuffRefreshSearch();
}

function stuffRefreshSearch() {
	// get search str
	var keyword=document.getElementById('keyword').getValue().toLowerCase();
	if (keyword.length>0 && keyword.length<4) return false;
	lookupSession();	
	// get values in filter
	var view=document.getElementById('filterView');
	var status=document.getElementById('filterStatus');
	var filterType=document.getElementById('filterType');
	var tagid=document.getElementById('tagid');
	var targetDiv=document.getElementById('stuffList');
	setSmallLoading(targetDiv);
	var ajax = new Ajax();
	var queryParams = { "keyword" : keyword };	
	ajax.responseType = Ajax.FBML;	
	ajax.requireLogin = false;	
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=stuffRefreshSearch&tagid="+tagid.getValue()+"&view="+view.getValue()+"&status="+status.getValue()+"&type="+filterType.getValue()+"&userid="+userid+"&sessionKey="+sessionKey,queryParams);
	return false;
}

function stuffKeySearch(obj) {
  this.obj = obj;

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
stuffKeySearch.prototype.onfocus = function(event) {
  this.focused = true;
}

// ...and hide it when they leave the text field
stuffKeySearch.prototype.onblur = function() {
}

// Every keypress updates the suggestions
stuffKeySearch.prototype.onkeyup = function(event) {
  switch (event.keyCode) {
    case 27: // escape
      break;
    case 0:
    case 13: // enter
    case 37: // left
    case 38: // up
    case 39: // right
    case 40: // down
      break;
    default:
      this.update_results();
      break;
  }
}

// We want interactive stuff to happen on keydown to make it feel snappy
stuffKeySearch.prototype.onkeydown = function(event) {
  switch (event.keyCode) {
    case 9: // tab
    case 13: // enter
      break;

    case 38: // up
    case 40: // down
      break;
  }
}

// Override these events so they don't actually do anything
stuffKeySearch.prototype.onkeypress = function(event) {
  switch (event.keyCode) {
    case 13: // return
    case 38: // up
    case 40: // down
      break;
  }
}

// This is called every keypress to update the suggestions
stuffKeySearch.prototype.update_results = function() {
	// check # of words, call for new list via ajax
	stuffRefreshSearch();
}

function searchAws() {
	// hide other quicklist
	hideQuickList();
	// get current title
	var keyword=document.getElementById('item').getValue();
	var ajax = new Ajax();
	var queryParams = { "keyword" : keyword };
	var targetDiv=document.getElementById('quickListAws');
	setSmallLoading(targetDiv);
	targetDiv.setClassName('');
	ajax.responseType = Ajax.FBML;	
	ajax.requireLogin = false;	
	ajax.ondone = function(data) { 
		hideQuickList(); // in case it came back
		targetDiv.setInnerFBML(data);
	}
	ajax.post(ajaxUrl+"?p=ajax&m=searchAws",queryParams);
}

function hideQuickList() {
	// where suggested questions appear
	document.getElementById('quickList').setClassName('hidden');
}

function showQuickList() {
	// where suggested questions appear
	document.getElementById('quickList').setClassName('');
	// hide aws quick list
	document.getElementById('quickListAws').setClassName('hidden');
}

function hideAwsList() {
	document.getElementById('quickListAws').setClassName('hidden');
}

function itemAhead(obj) {
  this.obj = obj;

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
itemAhead.prototype.onfocus = function(event) {
	showQuickList();	
  this.focused = true;
}

// ...and hide it when they leave the text field
itemAhead.prototype.onblur = function() {
	hideQuickList();
}

// Every keypress updates the suggestions
itemAhead.prototype.onkeyup = function(event) {
  switch (event.keyCode) {
    case 27: // escape
	hideQuickList();
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
itemAhead.prototype.onkeydown = function(event) {
  switch (event.keyCode) {
    case 9: // tab
    case 13: // enter
	hideQuickList();
      break;

    case 38: // up
    case 40: // down
      break;
  }
}

// Override these events so they don't actually do anything
itemAhead.prototype.onkeypress = function(event) {
  switch (event.keyCode) {
    case 13: // return
    case 38: // up
    case 40: // down
      break;
  }
}

// This is called every keypress to update the suggestions
itemAhead.prototype.update_results = function() {
	// check # of words, call for new list via ajax
	panel=document.getElementById('quickList');
	var val=this.obj.getValue().toLowerCase(); 
	var ajax = new Ajax();
	var queryParams = { "qStr" : val };
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = false;
    ajax.ondone = function(data) {panel.setInnerFBML(data);panel.setClassName(''); };
	ajax.post(ajaxUrl+"?p=ajax&m=stuffRelated",queryParams);
}

function xferItemInfo(data) {
	document.getElementById('item').setValue(data.title);	
	document.getElementById('asin').setValue(data.asin);	
	document.getElementById('detailPageUrl').setValue(data.detailPageUrl);	
	document.getElementById('imageUrl').setValue(data.imageUrl);		
	if (data.imageUrl!='') 
		document.getElementById('itemPreview').setInnerFBML(data.fbml_imageUrl);
}

function copyAwsItem(asin) {
		var targetDiv=document.getElementById('quickListAws');
		setSmallLoading(targetDiv);
		var queryParams = { "asin" : asin };	
		var ajax = new Ajax();
		ajax.responseType = Ajax.JSON;	
		ajax.requireLogin = false;	
		ajax.ondone = function(data) {
			targetDiv.setClassName('hidden');
			if (data.ProductGroup!='')
				document.getElementById('chooseCategory').setValue(data.ProductGroup);
			xferItemInfo(data);
			}
		ajax.post(ajaxUrl+"?p=ajax&m=stuffCopyAwsItem",queryParams);	
}

function copyItemDetails(id) {
	var targetDiv=document.getElementById('quickList');
	setSmallLoading(targetDiv);
	document.getElementById('imageUrl').setValue('');	// reset image url
	//document.getElementById('itemPreview').setInnerFBML('');		
	var ajax = new Ajax();
	ajax.responseType = Ajax.JSON;	
	ajax.requireLogin = false;	
	ajax.ondone = function(data) {
		hideQuickList();
		document.getElementById('caption').setTextValue(data.caption);
		document.getElementById('chooseCategory').setValue(data.tagid);
		xferItemInfo(data);
	}
	ajax.post(ajaxUrl+"?p=ajax&m=stuffCopyItem&id="+id);
}
