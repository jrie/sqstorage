<?php

$smarty = new Smarty();

$smarty->setTemplateDir($basedir . '/templates/');
$smarty->setCompileDir($basedir . '/smartyfolders/templates_c/');
$smarty->setConfigDir($basedir . '/smartyfolders/configs/');
$smarty->setCacheDir($basedir . '/smartyfolders/cache/');

if (isset($langsAvailable)) {
  $smarty->assign('langsAvailable', $langsAvailable, true);
  $smarty->assign('langsLabels', $langsLabels);
  $smarty->assign('langCurrent', $langCurrent);
  $smarty->assign('langShortCode', explode('_', $langCurrent, 2)[0]);
} else {
  $smarty->assign('langShortCode', 'de');
}

if (session_status() == PHP_SESSION_ACTIVE) {
  $smarty->assign('SESSION', $_SESSION);
}

//** un-comment the following line to show the debug console
if(file_exists('support/smarty_debug')){
  $smarty->debugging = true;
}else{
  $smarty->debugging = false;
}
$smarty->force_compile = false;
//$smarty->display('index.tpl');
