<?php

if(!in_array('failcount',DB::columnList('users'))) DB::query('ALTER TABLE `users` ADD `api_access` INT(1) NOT NULL DEFAULT \'1\' AFTER `password`, ADD `failcount` INT NOT NULL DEFAULT \'0\' AFTER `api_access`, ADD `lastfail` INT NOT NULL DEFAULT \'0\' AFTER `failcount`');

