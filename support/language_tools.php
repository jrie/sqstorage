<?php
define('LANGUAGEDIR', $basedir . '/languages/locale/');

$langsLabels = array(
  'en_GB' => 'English',
  'de_DE' => 'Deutsch'
);
$langsAvailable = array_keys($langsLabels);
$defaultLanguage = 'de_DE';

if (!isset($_SESSION)) {
  session_start();
  $_SESSION['lang'] = $defaultLanguage;
}

if (isset($_REQUEST['lang'])) {
  if (in_array($_REQUEST['lang'], $langsAvailable)) {
    $_SESSION['lang'] = $_REQUEST['lang'];
  }
}

$langCurrent = $_SESSION['lang'];
session_write_close();
session_start();
initLang($_SESSION['lang']);


function initLang($locale)
{
  $locales_root = LANGUAGEDIR; // locales directory
  $domain = "messages"; // the domain you’re using, this is the .PO/.MO file name without the extension

  // path to the .MO file that we should monitor
  $filename = "$locales_root".$locale."/LC_MESSAGES/$domain.mo";
  $mtime = filemtime($filename); // check its modification time
  $filename_new = "$locales_root".$locale."/LC_MESSAGES/{$domain}_{$mtime}.mo";
  if (!file_exists($filename_new)) { // check if we have created it before
    copy($filename, $filename_new);
  }
  $domain_new = "{$domain}_{$mtime}";
  if(!file_exists($filename_new)) $domain_new = "{$domain}";
  $suff =  ".UTF-8";
  if (isset($_SERVER['WINDIR'])) {
    if (strlen($_SERVER['WINDIR']) > 1) {
      $suff = "";
      switch ($locale) {
        case "de_DE":
          $locale = "deu";
          break;

        case "en_GB":
          $locale = "eng";
          break;
      }
    }
  }

  \Locale::setDefault(str_replace('_', '-', $locale));
  putenv('LC_ALL=' . $locale . $suff);
  putenv('LANG=' . $locale . $suff);
  setlocale(LC_ALL, "");
  setlocale(LC_ALL, $locale . $suff);
  setlocale(LC_CTYPE, $locale . $suff);
  bindtextdomain($domain_new, LANGUAGEDIR);
  textdomain($domain_new);
}
