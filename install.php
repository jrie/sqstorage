<?php

/**
 * Determine the webserver user
 * Default: www-data
 */
$wsun="www-data";
if(function_exists("posix_getpwuid")){
$poss[] =  posix_getpwuid(posix_geteuid());
$wsun=$poss['name'];
}else{
  $wsun = get_current_user();
}



$basedir = __DIR__ ;
/**
 * Check for write permissions for smartyfolders and language/locales
 * Fallback to single file instead of template if no write access and exits install.php
 */
include_once("support/install_permissions.php");

/**
 * Folder permissions OK, let's proceed
 */

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
$dbform = false;
$nodba = true;

/**
 *  Check dba.php existence , copy if required
 */

if(file_exists('./support/dba.php')){
  $dba_exists = true;
}else{
  if(!@copy('./support/dba-example.php','./support/dba.php')){
        $error[] = gettext("Die Datei support/dba.php ist nicht vorhanden und konnte auch nicht erstellt werden.");
        $error[] = gettext("Setze die entsprechende Berechtigung so, dass Dein Webserver diese erstellen und bearbeiten kann oder erstelle die Datei manuell und gewähre darauf Schreibrechte für den Webserver"). "<br>" . gettext("Unter Linux könnten folgende Befehle weiterhelfen");
        $error[] = "<b>Option 1:</b><br>sudo chown -R $wsun ./support/<br>sudo chgrp -R $wsun ./support/";
        $error[] = "<b>Option 2:</b><br>sudo cp ./support/dba-example.php ./support/dba.php<br>sudo chown $wsun ./support/dba.php<br>sudo chgrp $wsun ./support/dba.php";

    }
}

/**
 * Check if dba.php is writeable
 */
if(file_exists('./support/dba.php')){
  if(!is_writable('./support/dba.php')){
        $error[] =gettext("Der Webserver kann die Datei support/dba.php nicht ändern");
        $error[] =gettext("Bitte erlaube dem Webserver Schreibzugriff auf die Datei");
        $error[] = "<b>" . gettext("Unter Linux könnten folgende Befehle weiterhelfen") . "</b><br>sudo chown $wsun ./support/dba.php<br>sudo chgrp $wsun ./support/dba.php";
  }else{
    $nodba = false;
    include_once('./support/dba.php');
    $dbform = false;
    if(!CheckDBCredentials(DB::$host, DB::$user, DB::$password, DB::$dbName,DB::$port)){
      $dbform = true;
    }
  }
}

/**
 * Check user supplied credentials create db if required
 */
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

/**
 * Do migration
 */
if(!$nodba){
  if (CheckDBCredentials(DB::$host, DB::$user, DB::$password, DB::$dbName,DB::$port)){
    $successes[] = gettext("Datenbank-Verbindung hergestellt");
    if(isset($_POST['dbwork'])){
      include_once('./support/database_migration/db_migration.php');
      DBMigration();
    }
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




/**
 * custom function since is_writeable not reliable on Windows
 */
function IsDirWriteable($folder){
  $tfile = $folder . "touchfile.txt";
  @touch($tfile);
  $tmp = file_exists($tfile);
  @unlink($tfile);
  return $tmp;
}







exit;
