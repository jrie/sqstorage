<?php


$basedir = __DIR__;


require_once($basedir . '/vendor/autoload.php');
if(file_exists('/support/dba.php')) {
  require_once($basedir . '/support/dba.php');
}


require_once($basedir . '/support/language_tools.php');
require_once($basedir . '/support/smartyconfig.php');

function CheckDBCredentials($host,$user,$password,$name,$port){
  $tmp = error_reporting();
  error_reporting(0);
  $mysqli_connection = new MySQLi($host,$user,$password,$name,$port);
  error_reporting($tmp);
  if ($mysqli_connection->connect_error) {
      return false;
  }
  else {
    return true;
  }
}
