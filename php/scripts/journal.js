function addToJournal(cmd,userid,siteContentId) {
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'common',cmd:'addToJournal',siteContentId:siteContentId},
	  onSuccess: function(transport) {
		$A(document.getElementsByClassName('aj_'+siteContentId)).each(function(e) {
		    e.innerHTML=transport.responseText;
		});
	  }
	});	
}