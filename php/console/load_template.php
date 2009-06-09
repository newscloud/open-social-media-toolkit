<?php
//sleep(1);
if (isset($_GET['template']))
	$template = $_GET['template'];
else
	$template = '';

switch ($template) {
	case (preg_match('/template_[0-9]+/', $template) ? $template : !$template):
		echo '<div id="template-container">';
		echo '<p>Select a <a href="#" onclick="return loadTemplate(\'select_templates\');">different template.</a></p>';
		require("views/templates/$template.php");
		echo '<br /><button type="button" onclick="saveTemplate(\''.$template.'\');">Save Template</button>';
		echo '<br /><br />';
		echo '</div>';
	break;
	case 'select_templates':
		require('views/templates/select_templates.php');
	break;
	default:
		echo '<h2 style="color: red;">No template by that name found.</h2>';
	break;
}

?>

