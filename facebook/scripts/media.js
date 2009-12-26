function commonLike(module,id,targetDiv) {
	// record like of object
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showMediaAuthDlg();
	} else {
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
		ajax.post(ajaxUrl+"?p=ajax&m=commonLike&module="+module+"&id="+id+"&userid="+userid+"&sessionKey="+sessionKey);
	}
	return false;
}


function slideMediaPanel(pg) { // ,numPages
	var module='images';
//	var newX=500-(pg*500);  Animation(document.getElementById('thumbPanel')).to('left', newX+'px').go(); 
	var targetDiv=document.getElementById('imageStripPanel');
    setSmallLoading(targetDiv);
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = false;
	ajax.onerror = function() {
		// display pop up when session has expired, context is the vote span
		show_error_dialog(targetDiv);
	} 				
	ajax.ondone = function(data) {
	    targetDiv.setInnerFBML(data);
	}
	ajax.post(ajaxUrl+"?p=ajax&m=slideMediaPanel&module="+module+"&pg="+pg);	
} 

function refreshProfilePreview() {
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showMediaAuthDlg();
	} else {
		document.getElementById('uploadButtonItself').setClassName('btn_1'); // unhide
		document.getElementById('uploadButton').setClassName('hidden'); // hide panel
		var targetDiv=document.getElementById('previewImage');
		setSmallLoading(targetDiv);
		var imageIndex=document.getElementById('imageIndex').getValue();
		var previewImageFileName=document.getElementById('previewImageFileName');
		var location=document.getElementById('location').getValue();
		var alpha=document.getElementById('alpha').getValue();	
		var profileImageUrl=document.getElementById('profileImageUrl');
		var queryParams = { "profileImageUrl" :profileImageUrl.getValue()};		
		var ajax = new Ajax();
		ajax.responseType = Ajax.JSON;
		ajax.requireLogin = true; 
		ajax.onerror = function() {
			// display pop up when session has expired
			show_error_dialog(targetDiv);
		} 				
		ajax.ondone = function(data) {
			previewImageFileName.setValue(data.fileName);
		    targetDiv.setInnerXHTML(data.imgUrl);
		}
		ajax.post(ajaxUrl+"?p=ajax&m=mediaRefreshProfile&imageIndex="+imageIndex+"&location="+location+"&alpha="+alpha+"&userid="+userid+"&sessionKey="+sessionKey,queryParams);		
	}
}

function refreshProfileForm(imageIndex) {
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showMediaAuthDlg();
	} else {
		var targetDiv=document.getElementById('mediaFormOptions');
		setSmallLoading(targetDiv);
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.requireLogin = true;
		ajax.onerror = function() {
			// display pop up when session has expired
			show_error_dialog(targetDiv);
		} 				
		ajax.ondone = function(data) {
		    targetDiv.setInnerFBML(data);
		}
		ajax.post(ajaxUrl+"?p=ajax&m=mediaRefreshProfileForm&imageIndex="+imageIndex+"&userid="+userid+"&sessionKey="+sessionKey);		
	}
}

function changeProfileImage(imageIndex) {
	numProfileImages=document.getElementById('numProfileImages').getValue();
	for(i=0; i < numProfileImages; i++)
    {
        document.getElementById('proImage_'+i).setClassName('');
	}	
	// set chosen image
	document.getElementById('proImage_'+imageIndex).setClassName('selected');
	document.getElementById('imageIndex').setValue(imageIndex);	
	// update preview
	refreshProfilePreview();
	refreshProfileForm(imageIndex);
}

function uploadProfilePhoto() {
	var targetDiv=document.getElementById('uploadButton');
	targetDiv.setClassName(''); // unhide panel
	setSmallLoading(targetDiv);
	document.getElementById('uploadButtonItself').setClassName('hidden'); // hide button
	var tempName=document.getElementById('previewImageFileName');
	var ajax = new Ajax();
	var queryParams = { "tempName" :tempName.getValue()};		
	ajax.responseType = Ajax.FBML;
	ajax.requireLogin = true;
	ajax.onerror = function() {
		// display pop up when session has expired, context is the vote span
		show_error_dialog(targetDiv);
	} 				
	ajax.ondone = function(data) {
	    targetDiv.setInnerFBML(data);
	}
	ajax.post(ajaxUrl+"?p=ajax&m=mediaProfileUpload&userid="+userid+"&sessionKey="+sessionKey,queryParams);		
	
}

function showMediaAuthDlg() {
	dlg = new Dialog(Dialog.DIALOG_POP);
	var title='Please authorize this application';
	dlg.showMessage(title,mediaAuthMsg,'Close');	
}