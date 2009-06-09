function recordVote(siteContentId) {
	// var storyList=$('storyList');
	// setLoading(storyList);
	// var userid=$('userid');
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'common',cmd:'recordVote',siteContentId:siteContentId},
	  onSuccess: function(transport) {
		$A(document.getElementsByClassName('vl_'+siteContentId)).each(function(e) {
		    e.innerHTML=transport.responseText;
		}); 
	  }
	});		
}