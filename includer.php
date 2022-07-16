<?php


$basedir = __DIR__;


require_once($basedir . '/vendor/autoload.php');
if(file_exists('/support/dba.php')) {
  require_once($basedir . '/support/dba.php');
}
require_once($basedir . '/support/tools.php');
require_once($basedir . '/support/tools_users.php');

require_once($basedir . '/support/language_tools.php');
require_once($basedir . '/support/smartyconfig.php');

