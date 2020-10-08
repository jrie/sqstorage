<?php







function CheckDBCredentials($host,$user,$password,$name,$port,$silent=false){
  global $error;

  $tmp = error_reporting();
  error_reporting(0);
  $mysqli_connection = new MySQLi($host,$user,$password,"",$port);
  error_reporting($tmp);
  if ($mysqli_connection->connect_error) {
      if(!$silent)$error[] = gettext("Zugang wurde verweigert. Bitte überprüfe die Zugangsdaten");
      return false;
  }
  else {
    $query = "CREATE DATABASE IF NOT EXISTS " . $name ;
    if(mysqli_query($mysqli_connection, $query)){
      return true;
    } else {
      $error[] = gettext("Die gewählte Datenbank existiert nicht und konnte nicht erstellt werden");
      return false;
    }
  }
}
