<div class="yui-g">
<h1>Download settings</h1>

<?php
	foreach ($props as $k => $val) {
	    echo "Key: $k; Value: $val<br />";
		$ssObj->setState('fbApp_'.$k,$val);
	}	
	echo 'Completed';
?>
<div class="spacer"></div><br /><br />
</div>
