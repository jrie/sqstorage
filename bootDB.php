<?php
include_once('head.php');
DB::$usenull = false;

DB::query('CREATE TABLE IF NOT EXISTS `customFields` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, `label` varchar(64) NOT NULL, `dataType` int(10) UNSIGNED NOT NULL, `default` varchar (64) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `fieldData` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, `fieldId` bigint(20) UNSIGNED NOT NULL, `intNeg` int(11) DEFAULT NULL, `intPos` int(10) UNSIGNED DEFAULT NULL, `intNegPos` int(11) DEFAULT NULL, `floatNeg` float DEFAULT NULL, `floatPos` float UNSIGNED DEFAULT NULL, `string` varchar(512) DEFAULT NULL, `selection` varchar(1024) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `headCategories` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, `name` char(128) NOT NULL, `amount` int(10) UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (`id`), UNIQUE KEY `name` (`name`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `items` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,`label` varchar(64) NOT NULL,`comment` tinytext DEFAULT NULL,`date` timestamp NOT NULL DEFAULT current_timestamp(),`serialnumber` varchar(64) DEFAULT NULL,`amount` smallint(5) UNSIGNED NOT NULL DEFAULT 1,`headcategory` bigint(20) UNSIGNED NOT NULL,`subcategories` text DEFAULT NULL,`storageid` bigint(20) UNSIGNED DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `settings` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT , `namespace` varchar(64) NOT NULL, `jsondoc` longtext DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `namespace` (`namespace`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `storages` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,`label` varchar(64) NOT NULL,`amount` smallint(5) UNSIGNED NOT NULL DEFAULT 0,PRIMARY KEY (`id`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `subCategories` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,`name` char(128) NOT NULL,`amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0, `headcategory` bigint(20) UNSIGNED DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `subcategory` (`name`), UNIQUE KEY `name` (`name`) ) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `usergroups` (`id` bigint(20) UNSIGNED NOT NULL, `name` varchar(20) NOT NULL, `date` timestamp NOT NULL DEFAULT current_timestamp(), PRIMARY KEY (`id`), UNIQUE KEY `name` (`name`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `users` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,`username` varchar(20) NOT NULL,`mailaddress` varchar(254) NOT NULL,`password` varchar(255) DEFAULT NULL,`date` timestamp NOT NULL DEFAULT current_timestamp(),PRIMARY KEY (`id`),UNIQUE KEY `username` (`username`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `users_groups` (`userid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,`usergroupid` bigint(20) UNSIGNED NOT NULL,`date` timestamp NOT NULL DEFAULT current_timestamp(),PRIMARY KEY (`usergroupid`),UNIQUE KEY `userid` (`userid`)) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `users_tokens` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,`userid` bigint(20) NOT NULL,`token` varchar(255) NOT NULL,`valid_until` datetime DEFAULT NULL,PRIMARY KEY (`id`),KEY `userid` (`id`)) ENGINE=InnoDB;');

DB::$error_handler = false;
DB::query('INSERT INTO `settings` (`id`, `namespace`, `jsondoc`) VALUES (1, \'mail\', \'{}\')');
DB::query('INSERT INTO `usergroups` (`id`, `name`) VALUES (1, \'Administrator\'), (2, \'Gast\'), (3, \'Benutzer\');');
DB::$error_handler = true;

print('<strong>Database bootstrapped succesfully.</strong>');
die();
