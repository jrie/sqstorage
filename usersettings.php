<?php
require('login.php');
$error = "";
$success = "";

if ($useRegistration || !isset($user)) {
  if (!isset($user['username']) || !isset($user['usergroupid']) || (int)$user['usergroupid'] === 2) {
    $error = gettext('Zugriff verweigert!');
    include('accessdenied.php');
    die();
  }
}

require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

require_once('./support/dba.php');
if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
else $smarty->assign('urlPostFix', '.php');
if(isset($_POST['target'])){
  $mtarget = $_POST['target'];
}else{
  $mtarget = "";
}
$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'passwordchange') {
  if(USERS::SetUserPassword( $_POST['oldPassword'] , $_POST['newPassword1'] , $_POST['newPassword2'],$errors )   ){
    $success = gettext('Der Eintrag wurde erfolgreich aktualisiert.');
  }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'startpage') {
  SETTINGS::SettingsSet("startpage",$_SESSION['user']['username'],$_POST['startpagekey']);
}

$pages = [
  'entry' => gettext("Eintragen"),
  'inventory' => gettext('Inventar'),
  'transfer' => gettext('Transferieren'),
  'datafields' => gettext('Datenfelder'),
  'welcome' => gettext('Welcome!'),
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
