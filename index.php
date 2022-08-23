<?php

if (!file_exists('./support/dba.php')) {
  header("Location: install.php");
  die();
}

require('login.php');

$success = false;
require_once('customFieldsData.php');
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);


require_once('./includer.php');
if (!CheckDBCredentials(DB::$host, DB::$user, DB::$password, DB::$dbName, DB::$port)) {
  header("Location: install.php");
  die();
}

$tbls = DB::tableList();
if (count($tbls) == 0) {
  header("Location: install.php");
  die();
}

if ($useRegistration) {
  if (!isset($user)) {
    header('Location: '. $urlBase . '/inventory' . $urlPostFix);
    die();
  }else{
    $target = SettingsGetSingle("startpage",$user['username'],"entry");
    header('Location: '. $urlBase . '/' . $target . $urlPostFix);
  }
} else {
    $target = SettingsGetSingle("startpage","defaultuser","entry");
    header('Location: '. $urlBase . '/' . $target . $urlPostFix);
}

if ($usePrettyURLs) {
  $smarty->assign('urlPostFix', '');
} else {
  $smarty->assign('urlPostFix', '.php');
}

if (isset($_POST)) $smarty->assign('POST', $_POST);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->display('index.tpl');

die();
