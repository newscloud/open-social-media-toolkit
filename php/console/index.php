<?php

// auth

$password = 'xxx';

session_start();

if($_REQUEST['page'] == 'logout') unset($_SESSION['authed']);

if($_REQUEST['password'] == 'xxx') {
  $_SESSION['authed'] = true;
}

if($_SESSION['authed']) {

require('../connect_to_db.php');

function url_for($ctrl, $action, $id=0) {
  return "?ctrl=$ctrl&page=$action&id=$id";
}

function create_from_params($table, $params) {
  $fields = array();
  $values = array();
  foreach($params as $key => $val) {
    array_push($fields, $key);
    array_push($values, "'" . mysql_real_escape_string(stripslashes($val)) . "'");
  }
  $fields = implode($fields, ',');
  $values = implode($values, ',');
  
  mysql_query("INSERT INTO $table ($fields) VALUES ($values)");
}

function update_from_params($table, $params, $id) {
  $assignments = array();
  foreach($params as $key => $val) {
    array_push($assignments, $key . '=' . "'" . mysql_real_escape_string(stripslashes($val)) . "'");
  }
  $assignments = implode($assignments, ',');
  
  mysql_query("UPDATE $table SET $assignments WHERE id=$id");
}

$controller = ($_REQUEST['ctrl'] ? $_REQUEST['ctrl'] : 'product_categories');
$action = ($_REQUEST['page'] ? $_REQUEST['page'] : 'index');
$id = $_REQUEST['id'];

$skip_render = false;

require("controllers/$controller.php");

if($skip_render == false) {
  require('header.php');
  require("views/$controller/$action.php");
  require('footer.php');
}


} else {
  echo '<html><head><body>';
  
      ?>
      
      
      
      <form action="index.php" method="post">

        Please enter your password:<br />
        <input type="password" name="password" />
        <br /><br />
        <input type="submit" value="Log in" />
      </form>
      </body></html>
      
      <?php



  
}
?>