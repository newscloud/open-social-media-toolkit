function checkAvailable() { 
   resultDiv=$('isAvailableResult');
   memberInput=$('memberName');

	resultDiv.innerHTML='Searching... Please wait...';
	resultDiv.show();
	var ajax=new Ajax.Request(ajaxPath, {
	  method: 'get',
	  parameters: {page:'auth',cmd:'checkMemberName',memberName:memberInput.value},
	  onSuccess: function(transport) {		  
		   resultDiv.innerHTML=transport.responseText;
		},
	  onFailure : function(resp) {
		   resultDiv.show();
		   resultDiv.innerHTML='Having trouble reaching our server. Please try again later.';
		} 
	});		
}

function registerUser() {
	if (validateForm()) {
	   memberName=$('memberName');
	   password=$('pwd');
	   email=$('email');
	   formDiv=$('wholeForm');
		new Ajax.Request("ajaxHandler.php", {
		 onSuccess : function(resp) {
		   formDiv.innerHTML=resp.responseText;
		 },
		 onFailure : function(resp) {
		   formDiv.innerHTML=resp.responseText;
		 },
		 parameters : "method=registerUser&email="+email.value+"&memberName="+memberName.value+"&password="+password.value
		});
		return true;
	} else
		return false;
}

function validateForm() {
	// make sure password 1 and 2 match
   email=$('email');
   pass1=$('pwd');
   pass2=$('confirmPwd');
   if (email.value=='') {
   	alert ('Please enter an email address.');
   	return false;   
   } else if (pass1.value=='') {
   	alert ('Your password is blank! Please try again.');
   	return false;
   } else if (pass1.value!=pass2.value) {
   	alert ('Your passwords do not match! Please try again.');
   	return false;
   } else
   	return true;
}