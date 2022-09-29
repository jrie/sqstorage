<?php

/**
 *  Miscellaneous functions
 *  CheckDBCredentials($host,$user,$password,$name,$port,$silent=false) -> checks database credentials for validity
 *  GetNonEmptyArrayValues($ArrayGet) -> cleans empty elements from array  ['',12,'','ab',''] -> [12,'ab']
 *  IsDBUpdateAvailable() -> Checks if a db-migration is available which isn't installed yet. Returns true/false
 */

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


function GetNonEmptyArrayValues($ArrayGet){
  $out = array();
  for($x = 0; $x < count($ArrayGet);$x++){
      if($ArrayGet[$x] != "") $out[] = $ArrayGet[$x];
  }
  return $out;
}


function IsDBUpdateAvailable(){
  global $basedir;
    foreach (glob("$basedir"."/support/database_migration/dbm_*.php") as $filename) {
      $fn = basename($filename,".php");
      list($dump,$rev) = explode("_",$fn);
      $availablerev = $rev * 1;
    }
    $dbvers = DB::queryFirstField('SELECT MAX(dbrev) FROM database_rev');
    if($availablerev > $dbvers){
      return true;
    }
    return false;
}

/**
 * End Miscellaneous functions
 */


