<?php
    $requireAdmin = true;
    require('login.php');

    // require_once('./support/meekrodb.2.3.class.php');
    require_once('./vendor/autoload.php');
    require_once('./support/dba.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['target']=='mail') {
        try{
            $senderAddress = filter_var($_POST['senderAddress'], FILTER_VALIDATE_EMAIL);
            if(empty($senderAddress)) throw new Exception(gettext('Absender-Mailadresse ungültig.'));
            $mailEnabled = !empty($senderAddress) && $_POST['mail_enabled']==='true';
            DB::update('settings', ['jsondoc'=>DB::sqleval(sprintf('JSON_SET(jsondoc, "$.senderAddress", "%s", "$.enabled", %s)', $senderAddress, $mailEnabled ? 'true' : 'false'))], 'namespace=%s', 'mail');
        }
        catch(Exception $e) {
            $error = $e->getMessage();
        }
        DB::$error_handler = 'meekrodb_error_handler';
        DB::$throw_exception_on_error = false;
    }
    elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                    if(isset($_POST['userUpdateId']) && !empty($_POST['userUpdateId'])) {
                        $countAdmins = DB::queryFirstField('SELECT count(*) FROM users_groups WHERE usergroupid=1 AND NOT userid = %i LIMIT 1', $_POST['userUpdateId']);
                        if ($countAdmins==0 && $_POST['usergroupid']!=1) {
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
                            mail($_POST['mailaddress'], gettext('sqStorage Einladung'), sprintf(gettext("Sie haben eine Einladung für sqStorage erhalten: <a href=\"%s\">%s</a>\r\n"), dirname($_SERVER['HTTP_REFERER']) . '/login.php?activate=' . $userId . $token, dirname($_SERVER['HTTP_REFERER']). '/login.php?activate=' . $userId . $token), implode("\r\n", $header));
                        } else {
                            DB::commit();
                            throw new Exception(sprintf(gettext("Es können zur Zeit keine Mails vom System versendet werden.<br />Bitte diesen Einladungslink an den Benutzer weiterleiten:<br /><a href=\"%s\">%s</a>\r\n"), dirname($_SERVER['HTTP_REFERER']) . '/login.php?activate=' . $userId . $token, dirname($_SERVER['HTTP_REFERER']). '/login.php?activate=' . $userId . $token));
                        }
                    }
                DB::commit();
                header('Location: settings.php');
            }
            catch(Exception $e) {
                $error = $e->getMessage();
                $user = $_POST;
                $user['id'] = $_POST['userUpdateId'];
            }
            DB::$error_handler = 'meekrodb_error_handler';
            DB::$throw_exception_on_error = false;
    }
?>
<!DOCTYPE html>
<html>
    <?php include_once('head.php'); ?>
    <body>
        <?php include_once('nav.php'); ?>

        <div class="content">
            <?php
                if ($_SERVER['REQUEST_METHOD'] == 'GET' || !empty($error) || ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['target']=='mail')) {
                    if (isset($_GET['editUser']) && !empty($_GET['editUser'])) {
                        $isEdit = true;
                    } else if (isset($_GET['addUser'])) {
                        $isAdd = true;
                    }

                    if ($isEdit || $isAdd) {

                        if(empty($error)){
                            $user = DB::queryFirstRow('SELECT u.id, u.username, u.mailaddress, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) WHERE u.id = %i LIMIT 1', $_GET['editUser']);
                        }
                        ?>

                        <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <h6><?php echo $error; ?></h6>
                        </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                        <div class="alert alert-info" role="alert">
                            <p><?php echo $_POST['username'] . ' ' . gettext('zur Datenbank hinzugefügt.') ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($isEdit || $error): ?>
                        <div class="alert alert-danger" role="alert">
                            <h6><?php echo gettext('Eintrag zur Bearbeitung:') ?> &quot;<?php echo $user['username'] ?>&quot;</h6>
                        </div>
                        <?php endif; ?>

                        <form accept-charset="utf-8" id="userform" method="POST" action="#">
                            <?php
                                if ($isEdit) printf('<input type="hidden" value="%d" name="userUpdateId" />', $user['id']);
                            ?>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><?php echo gettext('Benutzername') ?></span>
                                </div>

                                <?php
                                    if (!$isEdit && !$error) echo '<input type="text" name="username" maxlength="20" class="form-control" required="required" placeholder="'. gettext('Benutzername') . '" aria-label="' . gettext('Benutzername') . '" aria-describedby="basic-addon1">';
                                    else printf('<input type="text" name="username" maxlength="20" class="form-control" required="required" placeholder="'. gettext('Benutzername') . '" aria-label="' . gettext('Benutzername') . '" aria-describedby="basic-addon1" value="%s">', $user['username']);
                                ?>
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon7"><?php echo gettext('E-Mail') ?></span>
                                </div>
                                <?php
                                    if (!$isEdit && !$error) echo '<input type="email" name="mailaddress" maxlength="254" class="form-control" autocomplete="off" placeholder="' . gettext('E-Mail') . '" aria-label="' . gettext('E-Mail') . '" aria-describedby="basic-addon7">';
                                    else printf('<input type="email" name="mailaddress" maxlength="254" class="form-control" autocomplete="off" placeholder="' . gettext('E-Mail') . '" aria-label="E-Mail" aria-describedby="basic-addon7" value="%s">', $user['mailaddress']);
                                ?>

                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <div class="dropdown">
                                        <select class="btn btn-secondary dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="usergroupDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php
                                                echo '<option value="-1" selected="selected">' . gettext('Benutzergruppe') . '</option>';
                                                $usergroups = DB::query('SELECT id, name FROM usergroups');

                                                $currentUsergroup = NULL;
                                                foreach ($usergroups as $usergroup) {
                                                    if (($isEdit || $error) && $user['usergroupid'] == $usergroup['id']) {
                                                        $currentUsergroup = $usergroup;
                                                    }
                                                    printf('<option value="%s">%s</option>', $usergroup['id'], $usergroup['name']);
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                    if ((!$isEdit && !$error)  || $currentUsergroup == NULL) {
                                        echo '<input type="text" class="form-control" id="usergroupname" name="usergroupname" readonly="readonly"  required="required" autocomplete="off" placeholder="' . gettext('Benutzergruppe') . '">';
                                        echo '<input type="hidden" value="" id="usergroupid" name="usergroupid" />';
                                    } else {
                                        printf('<input type="text" class="form-control" id="usergroupname" name="usergroupname" readonly="readonly" required="required" autocomplete="off" placeholder="%s" value="%s">', gettext('Benutzergruppe'), $user['usergroupname']);
                                        printf('<input type="hidden" value="%d" id="usergroupid" name="usergroupid" />', $user['usergroupid']);
                                    }
                                ?>
                            </div>

                            <div style="float: right;">
                            <?php if ($isEdit): ?>
                                <button type="submit" class="btn btn-danger"><?php echo gettext('Überschreiben') ?></button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary"><?php echo gettext('Eintragen') ?></button>
                            <?php endif; ?>

                            </div>
                        </form>

                    <?php
                    }
                    else{
                        if(isset($_GET['removeUser']) && !empty($_GET['removeUser'])) {
                            $countAdmins = DB::query('SELECT count(*) as cnt, userid FROM users_groups WHERE usergroupid=1 LIMIT 1');
                            if ($countAdmins['cnt']==1 && $countAdmins['userid']==$_GET['removeUSER']) {
                                $error = 'Fehler: Der letzte Administrator kann nicht gelöscht werden!';
                            }
                            else{
                                $user = DB::delete('users', 'id=%d', $_GET['removeUser']);
                                header('Location: settings.php');
                            }
                        }

                        if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <h6><?php echo $error; ?></h6>
                        </div>
                        <?php endif;

                        $users = DB::query('SELECT u.id, u.username, u.mailaddress, g.name as usergroupname, g.id as usergroupid FROM users u LEFT JOIN users_groups ugs ON(ugs.userid = u.id) LEFT JOIN usergroups g ON(g.id = ugs.usergroupid) ORDER BY u.username ASC');
                        echo '<a class="btn btn-primary addUser" href="settings.php?addUser">Neuer Benutzer</a>';
                        echo '<hr/><ul class="categories list-group"><li class="alert alert-info"><span class="list-span">' .  gettext('Benutzername') . '</span><span class="list-span">' .  gettext('E-Mail') . '</span><span class="list-span">' .  gettext('Gruppe') . '</span><span class="list-span">' .  gettext('Aktionen') . '</span></li>';
                        foreach ($users as $user) {
                            printf('<li class="list-group-item"><a name="removeUser" data-name="%s" data-id="%d" href="settings.php?removeUser=%d" class="removalButton fas fa-times-circle btn"></a><span class="list-span">%s</span><span class="list-span">%s</span><span class="list-span">%s</span><a class="fas fa-edit editUser" href="#" name="editUser" data-name="%s" data-id="%d"></a></li>', $user['username'], $user['usergroupid'], $user['id'], $user['username'], $user['mailaddress'], $user['usergroupname'], $user['username'], $user['id']);
                        }
                        echo '</ul><hr/>';

                        $html = <<<'HTML'
                        <form accept-charset="utf-8" id="mailform" method="POST" action="">
                            <input type="hidden" id="mail" name="target" value="mail" />
                            <ul class="categories list-group">
                                <li class="alert alert-info">
                                    <span class="list-span">Mailserver-Einstellungen</span>
                                </li>
                                <li class="list-group-item">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon7">E-Mail Absenderadresse</span>
                                        </div>
                                        <input type="email" name="senderAddress" maxlength="254" class="form-control" autocomplete="off" placeholder="email@example.com" aria-label="Absender" aria-describedby="basic-addon7" value="%s">
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon7">Mailversand</span>
                                        </div>
                                        <div class="form-check form-check-inline ml-3">
                                            <input class="form-check-input" type="radio" name="mail_enabled" id="mail_enabled_off" value="false" %s>
                                            <label class="form-check-label" for="mail_enabled_off">deaktivieren</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mail_enabled" id="mail_enabled_on" value="true" %s>
                                            <label class="form-check-label" for="mail_enabled_on">aktivieren</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary float-right">Einstellungen speichern</button>
                                </li>
                            </ul>
                        </form>
HTML;
                        $mailSettings = json_decode(DB::queryFirstField('SELECT jsondoc FROM settings WHERE namespace="mail" LIMIT 1'));
                        echo sprintf($html, $mailSettings->senderAddress, $mailSettings->enabled ?: 'checked="checked"', $mailSettings->enabled ? 'checked="checked"' : '');
                    }
                }
            ?>
        </div>

        <?php include_once('footer.php'); ?>

        <script type="text/javascript">

            let removalButtons = document.querySelectorAll('.removalButton')
            let countAdmins = 0;
            for (let button of removalButtons) {
                countAdmins = countAdmins + (button.getAttribute('data-id') == 1 ? 1 : 0)
                button.addEventListener('click', function (evt) {
                    let isLastAdmin = countAdmins == 1 && evt.target.getAttribute('data-id')==1;
                    let targetType = evt.target.name === 'removeUser' && !isLastAdmin ? '<?php echo gettext('Benutzer wirklich entfernen?') ?>' : '<?php echo gettext('Der letzte Administrator kann nicht gelöscht werden!') ?>'
                    if (!isLastAdmin) {
                        if (!window.confirm(targetType + ' "' + evt.target.dataset['name'] +'"')) {
                            evt.preventDefault()
                        }
                    } else {
                        window.alert(targetType)
                        evt.preventDefault()
                    }
                })
            }

            let editUserButtons = document.querySelectorAll('.editUser')
            for (let button of editUserButtons) {
                button.addEventListener('click', function (evt) {
                    if (evt.target.name === 'editUser') window.location.href = 'settings.php?editUser=' + evt.target.dataset['id']

                    return false
                })
            }

            document.querySelector('#usergroupDropdown').addEventListener('change', function(evt) {
                let usergroupdropdown = evt.target
                let usergroupname = document.querySelector('#usergroupname')
                let usergroupid = document.querySelector('#usergroupid')
                if (parseInt(usergroupdropdown.value) === -1) {
                    usergroupname.value = ''
                    return
                }
                usergroupname.value = usergroupdropdown.options[usergroupdropdown.selectedIndex].text
                usergroupid.value = usergroupdropdown.value
                usergroupdropdown.value = '-1'
            })
        </script>
    </body>
</html>