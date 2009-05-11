function refreshPage(pageNumber) {
	var storyList=$('storyList');
	//setLoading(storyList);
	// var userid=$('userid');
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'newswire',currentPage:pageNumber,cmd:'fetchPage'},
	  onSuccess: function(transport) {
	      // update newswire
	      storyList.innerHTML=transport.responseText;
	  }
	});	
}

function publishStory(cmd,userid,itemid) {
	var cmdBar=$('cmdBar_'+itemid);
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'newswire',cmd:'publishStory',itemid:itemid},
	  onSuccess: function(transport) {
	      // update newswire
	      cmdBar.innerHTML=transport.responseText;
	  }
	});	
}