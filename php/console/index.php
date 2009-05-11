<?php if ($_GET['logout']) require('global.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "XHTML1-s.dtd">
<html>
<head>
<title>Newscloud Management Interface -- Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--<link type="text/css" href="styles.css" rel="stylesheet" media="screen" />-->
<style>
body {background-color: #f6f4f5;}

</style>
</head>
<div class="containeradminfull">
<body>
<div class="containeradmin">
<!--content area BEGINS here-->
 <?php
 if($_GET['logout']) { ?>
	 <div style="color:red">Thank you for logging out.</div><br />
 <?php } ?>
<div class="equalboxtext">
	<table id="Table_01" width="150" height="50" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<form action="admin.php" method=POST>

			Username:

			  <input type="text" name="username"><br>

			Password:

			<input type="password" name="password">

			 <br>

			 <input type="submit" name="submit" value="Login">
			 <input type="reset" name="reset" value="Reset"><br /><br />
			 <?php
			 if($_GET['auth_failed']) { ?>
				 <div style="color:red">Incorrect username or password</div>
			 <?php } ?>
 </form>
		</tr>
</table>
Please click <a href="../index.php" border="0"> HERE </a> to return to the site.
</div>
<!--content area ENDS here-->
</div>
</body>
</div>
</html>