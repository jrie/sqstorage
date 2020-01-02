<?php
session_start();

require_once('./vendor/autoload.php');
require_once('./support/dba.php');

if (isset($_GET['logout'])) {
    unset($_SESSION['authenticated']);
    unset($_SESSION['user']);
    header('Location: index.php');
    exit;
}

if (!empty($_SESSION['authenticated'])) {
    $user = DB::queryFirstRow('SELECT u.id, u.username, u.password, g.usergroupid FROM users u LEFT JOIN users_groups g ON(g.userid=u.id) WHERE u.id=%i LIMIT 1', $_SESSION['user']['id']);
    if ($user) {
        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = ['username' => $user['username'], 'id' => $user['id'], 'usergroupid' => $user['usergroupid']];
    } else {
        header('Location: index.php?logout');
    }

    if ($requireAdmin && $user['usergroupid'] != 1) {
        $error = gettext('Zugriff verweigert!');
        include('accessdenied.php');
        exit;
    }

    return;
}

$showRecover = isset($_GET['recover']);

DB::query('SELECT id FROM users LIMIT 1');
if (DB::count() == 0) {
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
    if ($createFirstAdmin || $user) {
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
                        header('Location: index.php');
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
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = ['id' => $user['id']];
        header('Location: index.php');
    } else {
        $error = gettext('Zugangsdaten ungültig');
    }
} else if ($showRecover && ((isset($_POST['username']) && !empty($_POST['username'])) || isset($_POST['mailaddress']) && !empty($_POST['mailaddress']))) {
    if (empty($_POST['mailaddress'])) {
        $user = DB::query('SELECT id, username, mailaddress FROM users WHERE username=%s', $_POST['username']);
    } else if (empty($_POST['username'])) {
        $user = DB::query('SELECT id, username, mailaddress FROM users WHERE mailaddress=%s', $_POST['mailaddress']);
    } else {
        $user = DB::query('SELECT id, username, mailaddress FROM users WHERE username=%s AND mailaddress=%s', $_POST['username'], $_POST['mailaddress']);
    }
    $countUsers = DB::count();
    if ($countUsers > 1) {
        $error = gettext('Bitte Benutzername und E-Mail-Adresse angeben.');
    } else if ($countUsers == 1) {
        $user = $user[0];
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        DB::insert('users_tokens', array('userid' => $user['id'], 'token' => $hashedToken, 'valid_until' => DB::sqlEval('NOW( ) + INTERVAL 24 HOUR')));
        $mailSettings = json_decode(DB::queryFirstField('SELECT jsondoc FROM settings WHERE namespace="mail" LIMIT 1'));

        if ($mailSettings->enabled && filter_var($mailSettings->senderAddress, FILTER_VALIDATE_EMAIL)) {
            $header[] = 'MIME-Version: 1.0';
            $header[] = 'Content-type: text/html; charset=utf-8';
            $header[] = 'From: ' . $mailSettings->senderAddress;
            mail($user['mailaddress'], gettext('sqStorage Passwortänderung'), sprintf(gettext("Um das Passwort für sqStorage zu ändern bitte den folgenden Link aufrufen: <a href=\"%s\">%s</a>\r\n"), dirname($_SERVER['HTTP_REFERER']) . '/login.php?activate=' . $user['id'] . $token, dirname($_SERVER['HTTP_REFERER']) . '/login.php?activate=' . $user['id'] . $token), implode("\r\n", $header));
            $error = gettext('Falls ein Benutzerkonto gefunden wird, erhalten Sie nun eine Mail mit einem Link zum Zurücksetzen des Passworts.');
        } else {
            $error = gettext('Momentan können keine E-Mails versendet werden, bitte später noch einmal versuchen, oder einen Administrator kontaktieren.');
        }
    } else {
        $error = gettext('Falls ein Benutzerkonto gefunden wird, erhalten Sie nun eine Mail mit einem Link zum Zurücksetzen des Passworts.');
    }
}
?>
<!DOCTYPE html>
<html>
<?php include_once('head.php'); ?>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="index.php"><img class="logo" src="./img/sqstorage.png" /></a>
    </nav>

    <div class="content">
        <div class="login-box">
            <div class="card">
                <div class="card-body login-card-body">

                    <?php if ($error) { ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php } ?>

                    <p class="login-box-msg"><?php echo ($createFirstAdmin ? gettext('Neue Admin-Zugangsdaten eingeben') : ($showActivation ? gettext('Neue Zugangsdaten eingeben') : gettext('Zugangsdaten eingeben'))); ?></p>

                    <form action="login.php<?php echo $showActivation ? '?activate=' . $_GET['activate'] : '' ?><?php echo $showRecover ? '?recover' : '' ?>" method="post">
                        <div class="input-group mb-3">
                            <input type="text" id="username" name="username" class="form-control" placeholder="<?php echo gettext('Benutzername'); ?>" value="<?php echo ($showActivation || $showRecover) ? ($_POST['username'] ?? $user['username']) : ''; ?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <?php if ($showRecover || $createFirstAdmin) { ?>
                            <div class="input-group mb-3">
                                <input type="email" id="mailaddress" name="mailaddress" class="form-control" placeholder="<?php echo gettext('E-Mail'); ?>" value="<?php echo $_POST['mailaddress']; ?>">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (!$showRecover) { ?>
                            <div class="input-group mb-3">
                                <input type="password" id="password" name="password" class="form-control" placeholder="<?php echo gettext('Passwort'); ?>" value="<?php echo $showActivation ? $_POST['password'] : '' ?>">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($showActivation) { ?>
                                <div class="input-group mb-3">
                                    <input type="password" id="password_repeat" name="password_repeat" class="form-control" placeholder="<?php echo gettext('Passwort wiederholen'); ?>" value="<?php echo $showActivation ? $_POST['password_repeat'] : '' ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-7">
                                    <div class="form-group form-check">
                                        <input type="checkbox" id="remember" class="form-check-input">
                                        <label for="remember" class="form-check-label">
                                            <?php echo gettext('Angemeldet bleiben?'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo ($showActivation ? gettext('Speichern') : gettext('Anmelden')); ?></button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-7">
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo gettext('Anfordern'); ?></button>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                    <?php if (!$showActivation && !$showRecover) { ?>
                        <p class="mb-1">
                            <a href="login.php?recover"><?php echo gettext('Zugangsdaten vergessen?'); ?></a>
                        </p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('footer.php'); ?>
    <script type="text/javascript">
    </script>
</body>

</html>
<?php exit; ?>