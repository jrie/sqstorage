<?php
require_once 'login.php';
require_once 'support/dba.php';
require_once 'support/urlBase.php';
$smarty->assign('urlBase', $urlBase);

$error = "";
$success = "";

if ($useRegistration) {
  if (isset($user) && isset($user['usergroupid']) && ((int)$user['usergroupid'] === 1 || (int)$user['usergroupid'] === 3)) {
  } else {
    $error = gettext('Zugriff verweigert!');
    include 'accessdenied.php';
    die();
  }
}

if ($usePrettyURLs) {
    $smarty->assign('urlPostFix', '');
} else {
    $smarty->assign('urlPostFix', '.php');
}

if(isset($_POST['target'])){
  $mtarget = $_POST['target'];
}else{
  $mtarget = "";
}

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'passwordchange') {
  if ($_SERVER['SERVER_NAME'] !== 'demo.sqstorage.net') {
    if(USERS::SetUserPassword( $_POST['oldPassword'] , $_POST['newPassword1'] , $_POST['newPassword2'],$errors)) {
      $success = gettext('Der Eintrag wurde erfolgreich aktualisiert.');
    }
  } else {
    $errors[] = 'You are in the demo environment, it is not possible to change the password here!';
  }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'startpage') {
  SETTINGS::SettingsSet("startpage",$_SESSION['user']['username'],$_POST['startpagekey']);
}

$pages = [
  'welcome' => gettext('Welcome!'),
  'entry' => gettext("Eintragen"),
  'inventory' => gettext('Inventar'),
  'transfer' => gettext('Transferieren'),
  'datafields' => gettext('Datenfelder'),
];

$defaultStartPage = SETTINGS::SettingsGetSingle("startpage", $user['username'], SETTINGS::SettingsGetSingle("startpage","defaultuser","welcome"));

$smarty->assign('pages',$pages);
$smarty->assign('defaultStartPage',$defaultStartPage);
$smarty->assign('success', $success);
$smarty->assign('error', $errors);
$smarty->assign('POST', $_POST);
$smarty->assign('user', $user);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);

$smarty->display('usersettings.tpl');
