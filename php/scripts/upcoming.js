function refreshPage(pageNumber) {
	var storyList=$('storyList');
	//setLoading(storyList);
	// var userid=$('userid');
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'upcoming',currentPage:pageNumber,cmd:'fetchPage'},
	  onSuccess: function(transport) {
	      storyList.innerHTML=transport.responseText;
	  }
	});	
}

function addToJournal(cmd,userid,contentid) {
	var cmdBar=$('cmdBar_'+contentid);
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'upcoming',cmd:'addToJournal',contentid:contentid},
	  onSuccess: function(transport) {
	      cmdBar.innerHTML=transport.responseText;
	  }
	});	
}