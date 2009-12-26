function microCatChange() {
	// set category to other tag and refresh
	var tag=document.getElementById('microCat');
	microRefreshBrowse(1);
}

function microRefreshBrowse(page) {
	// get values in filter
	var tag=document.getElementById('microCat');
	var targetDiv=document.getElementById('postList');
	setLoading(targetDiv);
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;	
	ajax.requireLogin = false;	
	ajax.ondone = function(data) { targetDiv.setInnerFBML(data);}
	ajax.post(ajaxUrl+"?p=ajax&m=microFetchBrowse&tag="+tag.getValue()+"&page="+page);
	return false;
}