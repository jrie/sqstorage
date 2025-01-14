<?php

/**
 * Determine the webserver user
 * Default: www-data
 */
$wsun = "www-data";
if (function_exists("posix_getpwuid")) {
    $poss =  posix_getpwuid(posix_geteuid());
    $wsun = $poss['name'];
} else {
    $wsun = get_current_user();
}



$basedir = __DIR__;
/**
 * Check for write permissions for smartyfolders and language/locales
 * Fallback to single file instead of template if no write access and exits install.php
 */
require_once 'support/install_permissions.php';

/**
 * Folder permissions OK, let's proceed
 */

$usePrettyURLs = false;
require_once 'includer.php';
$success = false;
$error = array();
$info = array();
$successes = array();
$MigMessages = array();
require_once 'support/urlBase.php';
$smarty->assign('urlBase', $urlBase);
$dba_exists = false;
$dbform = false;
$nodba = true;

/**
 *  Check dba.php existence , copy if required
 */

/**
 * Check user supplied credentials create db if required
 */
if (isset($_POST['dbset'])) {

    if (CheckDBCredentials($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'], $_POST['dbport'], true)) {
        $path_to_file = './support/dba.php';
        $file_contents = file_get_contents($path_to_file);
        $file_contents = str_replace("$dbName = 'tlv'", "$dbName = '" . $_POST['dbname'] . "'", $file_contents);
        $file_contents = str_replace("$host = 'localhost'", "$host = '" . $_POST['dbhost'] . "'", $file_contents);
        $file_contents = str_replace("$port = '3306'", "$port = '" . $_POST['dbport'] . "'", $file_contents);
        $file_contents = str_replace("DB::\$user = 'tlvUser'", "DB::\$user = '" . $_POST['dbuser'] . "'", $file_contents);
        $file_contents = str_replace("DB::\$password = 'tlvUser'", "DB::\$password = '" . $_POST['dbpass'] . "'", $file_contents);
        $ispretty = "false;";
        if (isset($_POST['prettyurl'])) {
            $ispretty = "true;";
        }
        $file_contents = str_replace("\$usePrettyURLs = true;", "\$usePrettyURLs = " . $ispretty, $file_contents);
        if (isset($_POST['userctl'])) {
            $file_contents = str_replace("\$useRegistration = false;", "\$useRegistration = true;", $file_contents);
        }

        DB::$user = 'tlvUser';
        DB::$password = 'tlvUser';

        // https://meekro.com/docs/logging.html
        DB::$logfile = null;

        DB::$user = $_POST['dbuser'];
        DB::$password = $_POST['dbpass'];
        $dbName = $_POST['dbname'];
        $host = $_POST['dbhost'];
        $port = $_POST['dbport'];
        $file_contents .= '\n' . 'DB::$dsn = mysql:host=' . $host . ';port=' . $port . ';charset=utf8;dbname=' . $dbName;
        file_put_contents($path_to_file, $file_contents);

        $dbform = false;
    } else {
        $error[] = gettext("Datenbank-Verbindung nicht möglich. Bitte kontrolliere die Zugangsdaten");
        $dbform = true;
    }
} else {


    /**
     * Check if dba.php is writeable
     */
    if (file_exists('./support/dba.php')) {
        $dba_exists = true;
        if (!is_writable('./support/dba.php')) {
            $error[] = gettext("Der Webserver kann die Datei support/dba.php nicht ändern");
            $error[] = gettext("Bitte erlaube dem Webserver Schreibzugriff auf die Datei");
            $error[] = "<b>" . gettext("Unter Linux könnten folgende Befehle weiterhelfen") . "</b><br>sudo chown $wsun ./support/dba.php<br>sudo chgrp $wsun ./support/dba.php";
        } else {
            $nodba = false;
            include_once './support/dba.php';
            $dbform = false;
            if (!CheckDBCredentials($host, DB::$user, DB::$password, $dbName, $port, true)) {
                $dbform = true;
            }
        }
    } else {
        if (!@copy('./support/dba-example.php', './support/dba.php')) {
            $error[] = gettext("Die Datei support/dba.php ist nicht vorhanden und konnte auch nicht erstellt werden.");
            $error[] = gettext("Setze die entsprechende Berechtigung so, dass Dein Webserver diese erstellen und bearbeiten kann") . "<br>" . gettext("Unter Linux könnten folgende Befehle weiterhelfen");
            $error[] = "<b>" . gettext("Unter Linux könnten folgende Befehle weiterhelfen") . "</b><br>sudo chown -R $wsun ./support/<br>sudo chgrp -R $wsun ./support/";
            $nodba = true;
        } else {
            $dbform = true;
        }
    }
}

/**
 * Do migration
 */
if (!$nodba) {
    if (CheckDBCredentials($host, DB::$user, DB::$password, $dbName, $port, true)) {
        $successes[] = gettext("Datenbank-Verbindung hergestellt");
        if (isset($_POST['dbwork'])) {
            include_once './support/database_migration/db_migration.php';
            DBMigration();
        }
    }
}


if ($usePrettyURLs) {
    $smarty->assign('urlPostFix', '');
} else {
    $smarty->assign('urlPostFix', '.php');
}

$success = false;
if (isset($_POST)) {
    $smarty->assign('POST', $_POST);
}
$smarty->assign('MigMessages', $MigMessages);
$smarty->assign('dbform', $dbform);
$smarty->assign('error', $error);
$smarty->assign('success', $success);
$smarty->assign('successes', $successes);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->display('installpage.tpl');




/**
 * custom function since is_writeable not reliable on Windows
 */
function IsDirWriteable($folder) {
    $tfile = $folder . "touchfile.txt";
    @touch($tfile);
    $tmp = file_exists($tfile);
    @unlink($tfile);
    return $tmp;
}

die();
