<?php
include_once("pgettext.php");
$domain = 'messages';
$directory = dirname(__FILE__);
putenv("LANGUAGE=");
$locale ="sv_SE.utf8";
setlocale( LC_MESSAGES, $locale);
bindtextdomain($domain, $directory);
textdomain($domain);
bind_textdomain_codeset($domain, 'UTF-8');

foreach(array(1,2) as $nbr) {
	printf(npgettext("body", "One heart\n", "%d hearts\n", $nbr), $nbr);
	printf(npgettext("place", "One heart\n", "%d hearts\n", $nbr), $nbr);
}
printf(pgettext("door", "Open\n"));
printf(pgettext("book", "Open\n"));
