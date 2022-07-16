<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once('includer.php');
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

require_once('./support/dba.php');

if (!$usePrettyURLs) {
  $urlPostFix = '.php';
} else {
  $urlPostFix = '';
}

$smarty->assign('urlPostFix', $urlPostFix);

if (isset($useRegistration) && !$useRegistration) {
  $smarty->assign('useRegistration', FALSE);
  $smarty->assign('isGuest', FALSE);
} else {
  $smarty->assign('useRegistration', TRUE);
  $smarty->assign('isGuest', FALSE);
}

if (isset($useRegistration) && !$useRegistration) {
  $activate = "";
  if (isset($_GET['activate'])) $activate = $_GET['activate'];
  $smarty->assign('activate', $activate);
  if (isset($_POST)) $smarty->assign('POST', $_POST);
  if (isset($error)) $smarty->assign('error', $error);
  } else {
  if (isset($_GET['logout'])) {
    unset($_SESSION['authenticated']);
    unset($_SESSION['user']);
    header('Location: ' . $urlBase . '/index' . $urlPostFix);
    exit;
  }

  if (!empty($_SESSION['authenticated'])) {
    $user = DB::queryFirstRow('SELECT u.id, u.username, u.password, g.usergroupid FROM users u LEFT JOIN users_groups g ON(g.userid=u.id) WHERE u.id=%i LIMIT 1', $_SESSION['user']['id']);
    if ($user !== NULL) {
      $_SESSION['authenticated'] = true;
      $_SESSION['user'] = ['username' => $user['username'], 'id' => $user['id'], 'usergroupid' => $user['usergroupid']];

      if ((int)$user['usergroupid'] === 2) {
        $smarty->assign('isGuest', TRUE);
      } else {
        $smarty->assign('isGuest', FALSE);
      }
    } else {
      header('Location: '. $urlBase . '/index' . $urlPostFix . '?logout');
      exit;
    }

    return;
  }

  $showRecover = isset($_GET['recover']);
  $createFirstAdmin = false;
  $showActivation = false;
  $hasUsers = DB::queryFirstRow('SELECT id FROM users LIMIT 1');
  $user = NULL;
  if ($hasUsers === NULL) {
    $showActivation = true;
    $createFirstAdmin = true;
  }

  if ($createFirstAdmin || (isset($_REQUEST['activate']) && !empty($_REQUEST['activate']))) {
    DB::delete('users_tokens', 'valid_until < NOW()');

    if (!$createFirstAdmin) {
      $userId = substr($_REQUEST['activate'], 0, -32);
      $activationToken = substr($_REQUEST['activate'], -32);
      $users = DB::query('SELECT u.username, u.password, t.token, t.id as tokenid FROM users u LEFT JOIN users_tokens t ON(t.userid=u.id) WHERE t.userid=%i', $userId);
      foreach ($users as $_user) {
        $verify = password_verify($activationToken, $_user['token']);
        if ($verify) {
          $user = $_user;
          break;
        }
      }
    }

    if ($createFirstAdmin || !is_null($user)) {
      if (isset($_POST['password'])) {
        $errors = [];

        if (strlen($_POST['password']) < 8) {
          $errors[] = gettext('Passwort zu kurz, mindestens 8 Zeichen!');
        }

        if (!preg_match("#[0-9]+#", $_POST['password'])) {
          $errors[] = gettext('Passwort muß eine Zahl enthalten!');
        }

        if (!preg_match("#[a-z]+#", $_POST['password'])) {
          $errors[] = gettext('Passwort muß einen Kleinbuchstaben enthalten!');
        }

        if (!preg_match("#[A-Z]+#", $_POST['password'])) {
          $errors[] = gettext('Passwort muß einen Großbuchstaben enthalten!');
        }

        if ($_POST['password'] != $_POST['password_repeat']) {
          $errors[] = gettext('Die Passwörter stimmen nicht überein!');
        }

        if (empty($_POST['username'])) {
          $errors[] = gettext('Benutzername ist erforderlich.');
        } else if (isset($_POST['username']) && !preg_match('/[^a-zA-Z0-9_\-\.]/', $_POST['username']) == 0) {
          $errors[] = gettext('Benutzername enthält nicht zugelassene Zeichen.');
        }

        if ($createFirstAdmin && !filter_var($_POST['mailaddress'], FILTER_VALIDATE_EMAIL)) {
          $errors[] = gettext("E-Mail-Adresse ungültig!");
        }

        if (count($errors) == 0) {
          $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
          DB::$error_handler = false;
          DB::$throw_exception_on_error = true;
          try {
            if ($createFirstAdmin) {
              $result = DB::insert('users', array('username' => trim($_POST['username']), 'mailaddress' => $_POST['mailaddress'], 'password' => $hashedPassword));
              $userId = DB::insertId();
              $result = DB::insert('users_groups', array('userid' => $userId, 'usergroupid' => 1));
            } else {
              $result = DB::update('users', array('username' => trim($_POST['username']), 'password' => $hashedPassword), 'id=%i', $userId);
            }
            if ($result && DB::affectedRows() == 1) {
              if (!empty($user['tokenid'])) {
                DB::delete('users_tokens', 'id=%i', $user['tokenid']);
              }
              $_SESSION['authenticated'] = true;
              $_SESSION['user'] = ['id' => $userId];
              header('Location: '. $urlBase . '/index' . $urlPostFix);
              exit;
            }
          } catch (Exception $e) {
            $message = $e->getMessage();
            if (strpos($message, 'Duplicate entry') !== false) {
              $error = 'Der Benutzername ist bereits vergeben.';
            } else {
              $error = $e->getMessage();
            }
          }
          DB::$error_handler = true;
          DB::$throw_exception_on_error = false;
        } else {
          $error = implode('<br />', $errors);
        }
      }
      $showActivation = true;
    } else {
      $error = gettext('Der Aktivierungslink ist nicht mehr gültig.');
    }

  } else if (isset($_POST['password']) && !empty($_POST['password'])) {
    $user = DB::queryFirstRow('SELECT u.id, u.username, u.password, g.usergroupid FROM users u LEFT JOIN users_groups g ON(g.userid=u.id) WHERE u.username=%s LIMIT 1', $_POST['username']);
//    if ($user && password_verify($_POST['password'], $user['password'])) {
    if(Login($_POST['username'],$_POST['password'],$error)){
      header('Location: '. $urlBase . '/index'. $urlPostFix);
      exit;
    } else {
      if($error == "")$error = gettext('Zugangsdaten ungültig');  //if error is not set yet, invalid login, otherwise too many login failures
    }
  } else if ($showRecover && ((isset($_POST['username']) && !empty($_POST['username'])) || isset($_POST['mailaddress']) && !empty($_POST['mailaddress']))) {
    if (empty($_POST['mailaddress'])) {
      $user = DB::queryFirstRow('SELECT id, username, mailaddress FROM users WHERE username=%s LIMIT 1', $_POST['username']);
    } else if (empty($_POST['username'])) {
      $user = DB::queryFirstRow('SELECT id, username, mailaddress FROM users WHERE mailaddress=%s LIMIT 1', $_POST['mailaddress']);
    } else {
      $user = DB::queryFirstRow('SELECT id, username, mailaddress FROM users WHERE username=%s AND mailaddress=%s LIMIT 1', $_POST['username'], $_POST['mailaddress']);
    }

    if ($user !== NULL) {
      $token = bin2hex(openssl_random_pseudo_bytes(16));
      $hashedToken = password_hash($token, PASSWORD_DEFAULT);
      $mailSettings = json_decode(DB::queryFirstField('SELECT jsondoc FROM settings WHERE namespace="mail"'));
      if (!empty($mailSettings)) {
        DB::insert('users_tokens', array('userid' => $user['id'], 'token' => $hashedToken, 'valid_until' => DB::sqlEval('NOW() + INTERVAL 24 HOUR')));
      }

      if ($mailSettings->enabled && filter_var($mailSettings->senderAddress, FILTER_VALIDATE_EMAIL)) {
        $header[] = 'MIME-Version: 1.0';
        $header[] = 'Content-type: text/html; charset=utf-8';
        $header[] = 'From: ' . $mailSettings->senderAddress;
        mail($user['mailaddress'], gettext('sqStorage Passwortänderung'), sprintf(gettext("Um das Passwort für sqStorage zu ändern bitte den folgenden Link aufrufen: <a href=\"%s\">%s</a>"), $urlBase . '/login?activate=' . $user['id'] . $token, $urlBase . '/login?activate=' . $user['id'] . $token), implode("\r\n", $header));
        $error = gettext('Falls ein Benutzerkonto gefunden wird, erhalten Sie nun eine Mail mit einem Link zum Zurücksetzen des Passworts.');
      } else {
        $error = gettext('Momentan können keine E-Mails versendet werden, bitte später noch einmal versuchen, oder einen Administrator kontaktieren.');
      }
    } else {
      $error = gettext('Falls ein Benutzerkonto gefunden wird, erhalten Sie nun eine Mail mit einem Link zum Zurücksetzen des Passworts.');
    }
  }

  $activate = "";
  if (isset($_GET['activate'])) $activate = $_GET['activate'];
  $smarty->assign('activate', $activate);

  if (isset($_POST)) $smarty->assign('POST', $_POST);

  $smarty->assign('showRecover', $showRecover);
  $smarty->assign('createFirstAdmin', $createFirstAdmin);
  $smarty->assign('showActivation', $showActivation);
  if (isset($error)) $smarty->assign('error', $error);

  $smarty->display('login.tpl');
  exit;
}


// ?? @$user['username']
