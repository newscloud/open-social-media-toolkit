function showHoodSelect() {
	// show selector
	document.getElementById('hoodSelect').toggleClassName('hidden');
}

function updateHood() {
	// hide selector
	document.getElementById('hoodSelect').setClassName('hidden');
	var targetDiv=document.getElementById('sideWire_Local');
	var hood=document.getElementById('newHood').getValue();
	setSmallLoading(targetDiv);
	lookupSession();
	if (hasSimpleAccess!=true) { 
		showMediaAuthDlg();
	} else {
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
		ajax.post(ajaxUrl+"?p=ajax&m=chooseHood&hood="+hood+"&userid="+userid+"&sessionKey="+sessionKey);		
	}
	return false;
}