<?php

//require('includer.php');
$basedir = __DIR__;
require_once($basedir . '/vendor/autoload.php');
require_once($basedir . '/support/language_tools.php');
require_once($basedir . '/support/smartyconfig.php');
require_once('support/urlBase.php');

if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
else $smarty->assign('urlPostFix', '.php');
$isUser = TRUE;
$smarty->assign('urlBase', $urlBase);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->display('welcome.tpl');
die();
