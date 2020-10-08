#!/usr/bin/bash

PE=""
if [ "$EUID" != "0" ]
then
PE="sudo "
fi


$PE chown -R www-data ./smartyfolders/
$PE chgrp -R www-data ./smartyfolders/
$PE chown -R www-data ./languages/locale/
$PE chgrp -R www-data ./languages/locale/
$PE chown -R www-data ./support/
$PE chgrp -R www-data ./support/
