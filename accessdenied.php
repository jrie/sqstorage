<?php
header("HTTP/1.1 401 Unauthorized");
require_once('includer.php');
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

if (isset($error)) $smart->assign('error', $error);

$smarty->assign('SESSION', $_SESSION);

$smarty->display('accessdenied.tpl');
