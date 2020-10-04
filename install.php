<?php

$usePrettyURLs = false;
require('includer.php');

$success = false;
$error = array();
$info = array();
$successes = array();
$MigMessages = array();
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

$dba_exists = false;




if(file_exists('./support/dba.php')){
  $dba_exists = true;
}else{
  if(!@copy('./support/dba-example.php','./support/dba.php')){
        $error[] = gettext("Die Datei support/dba.php ist nicht vorhanden und konnte auch nicht erstellt werden.");
        $error[] = gettext("Setze die entsprechende Berechtigung so, dass Dein Webserver diese erstellen und bearbeiten kann oder erstelle die Datei manuell und gewähre darauf Schreibrechte für den Webserver");
  }
}

if(file_exists('./support/dba.php')){
  if(!is_writable('./support/dba.php')){
        $error[] =gettext("Der Webserver kann die Datei support/dba.php nicht ändern");
        $error[] =gettext("Bitte erlaube dem Webserver Schreibzugriff auf die Datei");
  }else{
    include_once('./support/dba.php');
    $dbform = false;
    if(!CheckDBCredentials(DB::$host, DB::$user, DB::$password, DB::$dbName,DB::$port)){
      $dbform = true;
    }
  }
}


if(isset($_POST['dbset'])){
  if (CheckDBCredentials($_POST['dbhost'],$_POST['dbuser'],$_POST['dbpass'],$_POST['dbname'],$_POST['dbport'])){


    $path_to_file = './support/dba.php';
    $file_contents = file_get_contents($path_to_file);
    $file_contents = str_replace("DB::\$user = 'tlvUser'", "DB::\$user = '". $_POST['dbuser'] ."'",$file_contents);
    $file_contents = str_replace("DB::\$password = 'tlvUser'", "DB::\$password = '". $_POST['dbpass'] ."'",$file_contents);
    $file_contents = str_replace("DB::\$dbName = 'tlv'", "DB::\$dbName = '". $_POST['dbname'] ."'",$file_contents);
    $file_contents = str_replace("DB::\$host = 'localhost'", "DB::\$host = '". $_POST['dbhost'] ."'",$file_contents);
    $file_contents = str_replace("DB::\$port = '3306'", "DB::\$port = '". $_POST['dbport'] ."'",$file_contents);
    file_put_contents($path_to_file,$file_contents);
    DB::$user = $_POST['dbuser'];
    DB::$password = $_POST['dbpass'];
    DB::$dbName = $_POST['dbname'];
    DB::$host = $_POST['dbhost'];
    DB::$port = $_POST['dbport'];


    $dbform = false;
  }else{
    $error[] = gettext("Datenbank-Verbindung nicht möglich. Bitte kontrolliere die Zugangsdaten");
  }
}

if (CheckDBCredentials(DB::$host, DB::$user, DB::$password, DB::$dbName,DB::$port)){
  $successes[] = gettext("Datenbank-Verbindung hergestellt");

  if(isset($_POST['dbwork'])){
    include_once('./support/database_migration/db_migration.php');
    DBMigration();
  }




}








if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
else $smarty->assign('urlPostFix', '.php');

$success = false;




if (isset($_POST)) $smarty->assign('POST', $_POST);
$smarty->assign('MigMessages', $MigMessages);
$smarty->assign('dbform',$dbform);
$smarty->assign('error',$error);
$smarty->assign('success',$success);
$smarty->assign('successes',$successes);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->display('installpage.tpl');













exit;
