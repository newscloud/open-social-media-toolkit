function previewPublish(id) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.ondone = function(transport) {
        document.getElementById('prePub_'+id).setInnerFBML(transport);
		document.getElementById('prePub_'+id).toggleClassName('hidden');
	}
	ajax.post(ajaxUrl+"?p=ajax&m=previewPublish&id="+id+"&userid="+userid+"&sessionKey="+sessionKey);	 
}

function showCaption(id) {
	document.getElementById('caption_'+id).toggleClassName('hidden');	
}

function hidePreviewPublish(id) {
	document.getElementById('prePub_'+id).toggleClassName('hidden');	
}

function postPublish(id) {
	lookupSession();
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.ondone = function(transport) {
        document.getElementById('prePub_'+id).setInnerFBML(transport);
		document.getElementById('prePub_'+id).toggleClassName('hidden');
	}
	ajax.post(ajaxUrl+"?p=ajax&m=postPublish&id="+id+"&userid="+userid+"&sessionKey="+sessionKey);	 
	
}