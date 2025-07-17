<?php
$user="";
require('login.php');
$error = "";
$success = "";
$settingdata = array();
$updatecheck = false;
$uptodate = false;
$ghapi = array();

if ($useRegistration) {
  if (!isset($user) || !isset($user['usergroupid']) || (int)$user['usergroupid'] === 2) {
    $error = gettext('Zugriff verweigert!');
    include 'accessdenied.php';
    die();
  }
}

require_once 'support/urlBase.php';
$smarty->assign('urlBase', $urlBase);

require_once './support/dba.php';
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



$install_allowed = false;
if(file_exists($basedir . "/support/allow_install")) $install_allowed = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'mail') {
  try {
    $senderAddress = filter_var($_POST['senderAddress'], FILTER_VALIDATE_EMAIL);
    if (empty($senderAddress)) throw new Exception(gettext('Absender-Mailadresse ungültig.'));
    $mailEnabled = !empty($senderAddress) && $_POST['mail_enabled'] === 'true';
    DB::update('settings', ['jsondoc' => DB::sqleval(sprintf('JSON_SET(jsondoc, "$.senderAddress", "%s", "$.enabled", %s)', $senderAddress, $mailEnabled ? 'true' : 'false'))], 'namespace=%s', 'mail');
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'install'){
  if (isset($_POST['allow_install'])){
    if($_POST['allow_install']== "allow"){
        touch ($basedir . "/support/allow_install");
        $install_allowed = true;
    }else{
        unlink ($basedir . "/support/allow_install");
        $install_allowed = false;
    }
  }
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'updater'){
       if(isset($_POST['branch'])) {
        $branch = $_POST['branch'];
          if(in_array($branch,['main','dev','beta'])) SETTINGS::SettingsSet('updater','githubbranch',$branch);
        }
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'updatecheck'){
  require_once 'support/updater.php';
  $updatecheck = true;
  $updData = SETTINGS::SettingsGet('updater');
  $resetTime = 0;
  $remainingCalls = GetRemainingGithubAPICalls($resetTime);
  if($remainingCalls < 15){
    $msg = gettext('Github beschränkt leider die API-Nutzung, weshalb die Prüfung momentan nicht stattfinden kann.');
    $msg .=  "<br />" . gettext('Die nächste Überprüfung nach folgendem Zeitpunkt stattfinden:'). "<br />" . date('Y-m-d H:i:s',$resetTime);
    $error = $msg;
    $updatecheck = false;
  }else{
    $uptodate = AreFileUpToDate($updData['githubuser'],$updData['githubrepo'],$updData['githubbranch']);
  }


}elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'startpage'){
  SETTINGS::SettingsSet("startpage","defaultuser",$_POST['startpagekey']);
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

  try {
    $_POST['username'] =  trim($_POST['username']);
    if (!preg_match('/[^a-zA-Z0-9_\-\.]/', $_POST['username']) == 0) {
      throw new Exception(sprintf(gettext("Fehler: Benutzername \"%s\" enthält nicht zugelassene Zeichen."), $_POST['username']));
    }

    if (!filter_var($_POST['mailaddress'], FILTER_VALIDATE_EMAIL)) {
      throw new Exception(sprintf(gettext("Fehler: E-Mail-Adresse \"%s\" ungültig."), $_POST['mailaddress']));
    }

    DB::startTransaction();
    if (isset($_POST['userUpdateId']) && !empty($_POST['userUpdateId'])) {
      $countAdmins = DB::queryFirstField('SELECT count(*) FROM users_groups WHERE usergroupid=1 AND NOT userid = %i LIMIT 1', $_POST['userUpdateId']);
      if ($countAdmins == 0 && $_POST['usergroupid'] != 1) {
        throw new Exception(sprintf(gettext('Fehler: Dies ist der letzte Administrator, die Gruppe kann nicht auf "%s" geändert werden!'), $_POST['usergroupname']));
      }

      $user = DB::update('users', array('username' => trim($_POST['username']), 'mailaddress' => $_POST['mailaddress'], 'api_access' => $_POST['userapikey']), 'id=%i', $_POST['userUpdateId']);
      USERS::AssignUserToGroup($_POST['userUpdateId'],$_POST['usergroupid']);
    } else {
      $token = bin2hex(openssl_random_pseudo_bytes(16));
      $hashedToken = password_hash($token, PASSWORD_DEFAULT);
      $userId = DB::insert('users', array('username' => trim($_POST['username']), 'api_access' => $_POST['userapikey'], 'mailaddress' => $_POST['mailaddress']));
      $userId = DB::insertId();
      USERS::AssignUserToGroup($userId,$_POST['usergroupid']);
      DB::insert('users_tokens', array('userid' => $userId, 'token' => $hashedToken, 'valid_until' => DB::sqlEval('NOW( ) + INTERVAL 1 WEEK')));
      $mailSettings['enabled'] = SETTINGS::SettingsGetSingle('mail','enabled',false);
      $mailSettings['senderAddress'] = SETTINGS::SettingsGetSingle('mail','senderAddress',false);


      if ($mailSettings['enabled'] && filter_var($mailSettings['senderAddress'], FILTER_VALIDATE_EMAIL)) {
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        $header[] = 'From: ' . $mailSettings['senderAddress'];
        mail($_POST['mailaddress'], gettext('sqStorage Einladung'), sprintf(gettext("Sie haben eine Einladung für sqStorage erhalten: <a href=\"%s\">%s</a>"), dirname($_SERVER['HTTP_REFERER']) . '/login?activate=' . $userId . $token, dirname($_SERVER['HTTP_REFERER']) . '/login?activate=' . $userId . $token), implode("\r\n", $header));
      } else {
        DB::commit();
        throw new Exception(sprintf(gettext("Es können zur Zeit keine E-Mails vom System versendet werden.<br />Bitte diesen Einladungslink an den Benutzer weiterleiten:<br /><a href=\"%s\">%s</a>"), dirname($_SERVER['HTTP_REFERER']) . '/login?activate=' . $userId . $token, dirname($_SERVER['HTTP_REFERER']) .'/login?activate=' . $userId . $token));
      }
    }
    DB::commit();
    header('Location: ' . $urlBase . '/settings');
  } catch (Exception $e) {
    $error = $e->getMessage();
    if (strncmp($error, "Duplicate", 9) === 0) $error = sprintf(gettext('Der Benutzer "%s" existiert bereits.'), $user['username']);
    $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress, u.api_access, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $user['id']);
  }
}


$isEdit = false;
$isAdd = false;
$usergroups = DB::query('SELECT id, name FROM usergroups');

if ($_SERVER['REQUEST_METHOD'] == 'GET' || !empty($error) || ($_SERVER['REQUEST_METHOD'] == 'POST' && $mtarget  == 'mail')) {
  if (isset($_GET['editUser']) && !empty($_GET['editUser'])) {
    $isEdit = true;
  } else if (isset($_GET['addUser'])) {
    $isAdd = true;
  }

  if ($isEdit || $isAdd) {
    if (empty($error)) {
      if ($isEdit) {
        $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress, u.api_access, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $_GET['editUser']);
        if ($user == null) {
          header('Location: '. $urlBase . '/index');
          die();
        }
      } else {
        $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress,  u.api_access,  g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $user['id']);
      }
    } else if ($isAdd) {
      $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress,  u.api_access,  g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $userId);
    } else {
      $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress, u.api_access, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $_GET['editUser']);
      if ($user == null) {
        header('Location: '. $urlBase . '/index');
        die();
      }
    }
  } else {
    if (isset($_GET['removeUser']) && !empty($_GET['removeUser'])) {
      $adminAccounts = DB::query('SELECT userid FROM users_groups WHERE usergroupid=1 LIMIT 2');
      if (count($adminAccounts) === 1 && $adminAccounts[0]['userid'] == $_GET['removeUser']) {
        $error = gettext('Fehler: Der letzte Administrator kann nicht gelöscht werden!');
      } else {
        USERS::DeleteUser($_GET['removeUser']);
        header('Location: '. $urlBase . '/settings');
        die();
      }
    }
  }
}

$mailDB = DB::queryFirstField('SELECT jsondoc FROM settings WHERE namespace="mail" LIMIT 1');

if ($mailDB !== NULL) {
  $mailSet = json_decode($mailDB);
  $mailSettings['senderAddress'] = @$mailSet->senderAddress;
  $mailSettings['enabled'] = @$mailSet->enabled;
} else {
  $mailSettings['senderAddress'] = "";
  $mailSettings['enabled'] = false;
}

//if(!in_array('failcount',DB::columnList('users'))){
//  $users = DB::query('SELECT u.id, u.username, u.mailaddress, u.api_access, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) ORDER BY u.username ASC');
//} else {
//  $users = DB::query('SELECT u.id, u.username, u.mailaddress, u.api_access, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) ORDER BY u.username ASC');
//}
$users = DB::query('SELECT u.id, u.username, u.mailaddress, u.api_access, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) ORDER BY u.username ASC');

$dbUpdateAvailable = IsDBUpdateAvailable();
$pages = [
  'entry' => gettext("Eintragen"),
  'inventory' => gettext('Inventar'),
  'transfer' => gettext('Transferieren'),
  'datafields' => gettext('Datenfelder'),
  'welcome' => gettext('Welcome!'),
];

$settingdata['updater'] = SETTINGS::SettingsGet('updater');
if($settingdata['updater'] === null){
  SETTINGS::SettingsSet('updater','githubuser','jrie');
  SETTINGS::SettingsSet('updater','githubrepo','sqstorage');
  SETTINGS::SettingsSet('updater','githubbranch','main');
  $settingdata['updater'] = SETTINGS::SettingsGet('updater');
}
$settingdata['updater']['branches'] = ['main' => gettext("Release"),'beta' => gettext("Betatest"),'dev' => gettext("Entwicklung")];

$defaultStartPage = SETTINGS::SettingsGetSingle("startpage","defaultuser","entry");

$smarty->assign('updatecheck',$updatecheck);
$smarty->assign('uptodate',$uptodate);
$smarty->assign('settingdata',$settingdata);
$smarty->assign('pages',$pages);
$smarty->assign('defaultStartPage',$defaultStartPage);
$smarty->assign('update_available',$dbUpdateAvailable);
$smarty->assign('install_allowed',$install_allowed);
$smarty->assign('mailSettings', $mailSettings);
$smarty->assign('success', $success);
$smarty->assign('isEdit', $isEdit);
$smarty->assign('isAdd', $isAdd);
$smarty->assign('error', $error);
$smarty->assign('POST', $_POST);
$smarty->assign('user', $user);
$smarty->assign('users', $users);
$smarty->assign('usergroups', $usergroups);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);

$smarty->display('settings.tpl');
