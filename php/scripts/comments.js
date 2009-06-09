function submitComment(siteContentId) {
	var comments=$('comments');
	// Build the AJAX object to request the dialog contents
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'readStory',cmd:'addComment',siteContentId:siteContentId,comments:comments.value},
	  onSuccess: function(transport) {
	  	refreshComments(siteContentId);
	  }
	});	
}

function refreshComments(siteContentId) {
	// Build the AJAX object to request the dialog contents
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'readStory',cmd:'refreshComments',siteContentId:siteContentId},
	  onSuccess: function(transport) {
		$('commentThread').innerHTML=transport.responseText; 
	  }
	});	
}


