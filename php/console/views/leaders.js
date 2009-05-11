
function selectLike(category, select)
{

	$A(document.getElementsByClassName(category)).each(
			function(e) 
			{
				e.checked=select;
				//eval ("document.moderate."+e.id+".checked = true;");    
			}); 

}


function markAll(mode) {
	if (mode == 1) 
	{
		$A(document.getElementsByClassName('ckmark')).each(
				function(e) 
				{
					eval ("document.moderate."+e.id+".checked = true;");    
				}); 
	} else {
		$A(document.getElementsByClassName('ckmark')).each(
				function(e) 
				{
					eval ("document.moderate."+e.id+".checked = false;");    
				}); 
	}
}


function checkAll(to_check, num)
{
	if (to_check == 1)
	{
		for (i = 0; i < num; i++)
			eval ("document.moderate.check_" + i + ".checked = true;");
	}
	else
	{
		for (i = 0; i < num; i++)
			eval ("document.moderate.check_" + i + ".checked = false;");
	}
}

