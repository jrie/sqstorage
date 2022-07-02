<?php
$user="";
require('login.php');
$error = "";
$success = "";

if ($useRegistration) {
  if (!isset($user) || !isset($user['usergroupid']) || (int)$user['usergroupid'] === 2) {
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

$install_allowed = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['target'] == 'mail') {
  try {
    $senderAddress = filter_var($_POST['senderAddress'], FILTER_VALIDATE_EMAIL);
    if (empty($senderAddress)) throw new Exception(gettext('Absender-Mailadresse ungültig.'));
    $mailEnabled = !empty($senderAddress) && $_POST['mail_enabled'] === 'true';
    DB::update('settings', ['jsondoc' => DB::sqleval(sprintf('JSON_SET(jsondoc, "$.senderAddress", "%s", "$.enabled", %s)', $senderAddress, $mailEnabled ? 'true' : 'false'))], 'namespace=%s', 'mail');
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
  DB::$error_handler = 'meekrodb_error_handler';
  DB::$throw_exception_on_error = false;
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['target'] == 'install'){
  if(file_exists($basedir . "/support/allow_install")) $install_allowed = true;
  if (isset($_POST['allow_install'])){
    if($_POST['allow_install']== "allow"){
        touch ($basedir . "/support/allow_install");
    }else{
        unlink ($basedir . "/support/allow_install");
    }
  }
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
  DB::$error_handler = false;
  DB::$throw_exception_on_error = true;
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

      $user = DB::update('users', array('username' => trim($_POST['username']), 'mailaddress' => $_POST['mailaddress']), 'id=%i', $_POST['userUpdateId']);
      $usergroup = DB::insertUpdate('users_groups', array('userid' => $_POST['userUpdateId'], 'usergroupid' => $_POST['usergroupid']), array('usergroupid' => $_POST['usergroupid']));
    } else {
      $token = bin2hex(openssl_random_pseudo_bytes(16));
      $hashedToken = password_hash($token, PASSWORD_DEFAULT);
      $userId = DB::insert('users', array('username' => trim($_POST['username']), 'mailaddress' => $_POST['mailaddress']));
      $userId = DB::insertId();
      $usergroup = DB::insertUpdate('users_groups', array('userid' => $userId, 'usergroupid' => $_POST['usergroupid']), array('usergroupid' => $_POST['usergroupid']));
      DB::insert('users_tokens', array('userid' => $userId, 'token' => $hashedToken, 'valid_until' => DB::sqlEval('NOW( ) + INTERVAL 1 WEEK')));
      $mailSettings = json_decode(DB::queryFirstField('SELECT jsondoc FROM settings WHERE namespace="mail" LIMIT 1'));

      if ($mailSettings->enabled && filter_var($mailSettings->senderAddress, FILTER_VALIDATE_EMAIL)) {
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        $header[] = 'From: ' . $mailSettings->senderAddress;
        mail($_POST['mailaddress'], gettext('sqStorage Einladung'), sprintf(gettext("Sie haben eine Einladung für sqStorage erhalten: <a href=\"%s\">%s</a>"), dirname($_SERVER['HTTP_REFERER']) . '/login?activate=' . $userId . $token, dirname($_SERVER['HTTP_REFERER']) . '/login?activate=' . $userId . $token) . '\r\n', implode("\r\n", $header));
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
    $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $user['id']);
  }
  DB::$error_handler = 'meekrodb_error_handler';
  DB::$throw_exception_on_error = false;
}


$isEdit = false;
$isAdd = false;
$usergroups = DB::query('SELECT id, name FROM usergroups');

if ($_SERVER['REQUEST_METHOD'] == 'GET' || !empty($error) || ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['target'] == 'mail')) {
  if (isset($_GET['editUser']) && !empty($_GET['editUser'])) {
    $isEdit = true;
  } else if (isset($_GET['addUser'])) {
    $isAdd = true;
  }

  if ($isEdit || $isAdd) {
    if (empty($error)) {
      if ($isEdit) {
        $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $_GET['editUser']);
        if ($user == null) {
          header('Location: '. $urlBase . '/index');
          die();
        }
      } else if ($isAdd) {
        $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $_GET['addUser']);
      }
    }
  } else {
    if (isset($_GET['removeUser']) && !empty($_GET['removeUser'])) {
      $adminAccounts = DB::query('SELECT userid FROM users_groups WHERE usergroupid=1 LIMIT 2');
      if (count($adminAccounts) === 1 && $adminAccounts['userid'] == $_GET['removeUser']) {
        $error = gettext('Fehler: Der letzte Administrator kann nicht gelöscht werden!');
      } else {
        $user = DB::delete('users', 'id=%d', $_GET['removeUser']);
        header('Location: '. $urlBase . '/settings');
        exit;
      }
    }
  }
}

$mailDB = DB::queryFirstField('SELECT jsondoc FROM settings WHERE namespace="mail" LIMIT 1');

if ($mailDB !== NULL) {
  $mailSet = json_decode($mailDB);
  $mailSettings['senderAddress'] = $mailSet->senderAddress;
  $mailSettings['enabled'] = $mailSet->enabled;
} else {
  $mailSettings['senderAddress'] = "";
  $mailSettings['enabled'] = false;
}

$users = DB::query('SELECT u.id, u.username, u.mailaddress, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) ORDER BY u.username ASC');
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
