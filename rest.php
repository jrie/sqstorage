<?php


require_once('./vendor/autoload.php');
require_once('./support/dba.php');


$res = DB::tableList();
echo "<pre>". print_r($_SERVER,true) . "</pre>";







