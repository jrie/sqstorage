<?php

/**
 *  Miscellaneous functions
 *  CheckDBCredentials($host,$user,$password,$name,$port,$silent=false) -> checks database credentials for validity
 *  GetNonEmptyArrayValues($ArrayGet) -> cleans empty elements from array  ['',12,'','ab',''] -> [12,'ab']
 *  IsDBUpdateAvailable() -> Checks if a db-migration is available which isn't installed yet. Returns true/false
 */

function CheckDBCredentials($host,$user,$password,$name,$port, $dbRootUser, $dbRootUserPassword, $silent=false){
  global $error;

  $tmp = error_reporting();
  error_reporting(0);
  try {
    $mysqli_connection = new MySQLi($host, $user, $password, $name, $port);
  } catch (mysqli_sql_exception $e) {
    if (!is_null($dbRootUser) && !is_null($dbRootUserPassword)) {
      $errorCode = $e->getCode();
      $query = "CREATE DATABASE IF NOT EXISTS " . $name;
      try {
        $mysqli_connection = new MySQLi($host, $dbRootUser, $dbRootUserPassword, "", $port);
      } catch (mysqli_sql_exception $e) {
        $error[] = gettext("Hauptnutzer konnte sich nicht anmelden um sqStorage Nutzer und Datenbank anzulegen");
        return false;
      }

      if (mysqli_query($mysqli_connection, $query)) {
        $queryCreateUser = "CREATE USER '$user'@'$host' IDENTIFIED BY '$password';";
        $queryGrant = "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER ON `$name`.* TO '$user'@'$host';";
        $queryFlush = "FLUSH PRIVILEGES;";
        try {
          mysqli_query($mysqli_connection, $queryCreateUser);
          mysqli_query($mysqli_connection, $queryGrant);
          mysqli_query($mysqli_connection, $queryFlush);
        } catch (mysqli_sql_exception $e2) {
          $errorCode2 = $e2->getCode();
          if ($errorCode2 === 1045) {
            $error[] = gettext("Datenbank Hauptnutzer Zugriff ungültig, konnte sqStorage Benutzer und Rechte nicht setzen");
          }

          return false;
        }

        return true;
      }
    }
  }

  error_reporting($tmp);

  if ($mysqli_connection->connect_error) {
      if(!$silent)$error[] = gettext("Zugang wurde verweigert. Bitte überprüfe die Zugangsdaten");
      return false;
  }

  return true;
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


