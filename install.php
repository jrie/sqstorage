<?php


$writeable_folder = array("./smartyfolders/","./languages/locale/");

$unwriteable = array();
foreach($writeable_folder as $wf){
  if (!IsDirWriteable($wf)) $unwriteable[] = $wf;
}
if(count($unwriteable)>0){
  echo '<html><head><title>sqStorage - Installation</title><link rel="stylesheet" href="./css/bootstrap/bootstrap.css"><link rel="stylesheet" href="./css/base.css"><link rel="stylesheet" href="./fonts/fontawesome/css/solid.css"><link rel="stylesheet" href="./fonts/fontawesome/css/regular.css"><link rel="stylesheet" href="./fonts/fontawesome/css/fontawesome.css"><meta charset="utf-8"></head><body>';
  echo '<nav class="navbar navbar-light bg-light"><a href="http://localhost:8888\/index"><img class="logo" src="./img/sqstorage.png" alt="sqStorage logo" /></a></nav><center>';
  echo "<h2>". gettext("Zugriff auf folgende Verzeichnisse fehlgeschlagen:") . "</h2>";
  foreach($unwriteable as $unw){
    echo "<h3>" . $unw . "</h3>";
  }
  echo "<h2>". gettext("Unter Linux könnten folgende Befehle weiterhelfen") . "</h2>";
  foreach($unwriteable as $unw){
  echo "<h3>sudo chown -R www-data $unw</h3>";
  echo "<h3>sudo chgrp -R www-data $unw</h3>";
  }
  echo "</center>";
  echo '<footer class="footer"><script type="text/javascript">eval(unescape("%64%6f%63%75%6d%65%6e%74%2e%77%72%69%74%65%28%27%3c%61%20%68%72%65%66%3d%22%6d%61%69%6c%74%6f%3a%6a%61%6e%40%64%77%72%6f%78%2e%6e%65%74%3f%73%75%62%6a%65%63%74%3d%73%71%73%74%6f%72%61%67%65%22%20%63%6c%61%73%73%3d%22%62%74%6e%20%62%74%6e%2d%69%6e%66%6f%22%20%74%61%62%69%6e%64%65%78%3d%22%2d%31%22%3e%4b%6f%6e%74%61%6b%74%3c%2f%61%3e%27%29%3b"))</script><a class="btn btn-info" tabIndex="-1" target="_blank" href="https://github.com/jrie/sqstorage">Github</a></footer></script></body></html>';
  exit();
}



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
        $error[] = gettext("Unter Linux könnten folgende Befehle weiterhelfen");
        $error[] = "chown -R www-data ./support/";
        $error[] = "chgrp -R www-data ./support/";
    }
}

if(file_exists('./support/dba.php')){
  if(!is_writable('./support/dba.php')){
        $error[] =gettext("Der Webserver kann die Datei support/dba.php nicht ändern");
        $error[] =gettext("Bitte erlaube dem Webserver Schreibzugriff auf die Datei");
        $error[] = gettext("Unter Linux könnten folgende Befehle weiterhelfen");
        $error[] = "chown www-data ./support/dba.php";
        $error[] = "chgrp www-data ./support/dba.php";
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





function IsDirWriteable($folder){
  $tfile = $folder . "touchfile.txt";
  @touch($tfile);
  $tmp = file_exists($tfile);
  @unlink($tfile);
  return $tmp;
  //custom function since is_writeable not reliable on Windows

}







exit;
