function publishWire(itemid) {
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'common',cmd:'publishWire',itemid:itemid},
	  onSuccess: function(transport) {
	      // update newswire
			$A(document.getElementsByClassName('pw_'+itemid)).each(function(e) {
			    e.innerHTML=transport.responseText;
			}); 
	  }
	});	
}