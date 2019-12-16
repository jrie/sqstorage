<?php
include_once('head.php');

// Tables
DB::query('CREATE TABLE IF NOT EXISTS `storages` (`id` BIGINT UNSIGNED NOT NULL, `label` VARCHAR(64) NOT NULL, `amount` smallint UNSIGNED NOT NULL DEFAULT \'0\') ENGINE=InnoDB DEFAULT CHARSET=utf8;');
DB::query('CREATE TABLE IF NOT EXISTS `headCategories` (`id` BIGINT UNSIGNED NOT NULL, `name` CHAR(128) NOT NULL, `amount` int(10) UNSIGNED NOT NULL DEFAULT \'0\') ENGINE=InnoDB DEFAULT CHARSET=utf8;');
DB::query('CREATE TABLE IF NOT EXISTS `subCategories` (`id` BIGINT UNSIGNED NOT NULL, `name` CHAR(128) NOT NULL, `amount` BIGINT UNSIGNED NOT NULL DEFAULT \'0\', `headcategory` BIGINT UNSIGNED DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
DB::query('CREATE TABLE IF NOT EXISTS `items` (`id` BIGINT UNSIGNED NOT NULL, `label` VARCHAR(64) NOT NULL, `comment` tinytext, `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `serialnumber` VARCHAR(64) DEFAULT NULL, `amount` SMALLINT UNSIGNED NOT NULL DEFAULT \'1\', `headcategory` BIGINT UNSIGNED NOT NULL, `subcategories` text, `storageid` BIGINT UNSIGNED NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

DB::query('CREATE TABLE IF NOT EXISTS `customFields` ( `id` BIGINT UNSIGNED NOT NULL, `label` VARCHAR(64) NOT NULL, `dataType` INT UNSIGNED NOT NULL, `default` VARCHAR(64) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
DB::query('CREATE TABLE IF NOT EXISTS `fieldData` ( `id` BIGINT UNSIGNED NOT NULL , `fieldId` BIGINT UNSIGNED NOT NULL , `intNeg` INT NULL DEFAULT NULL , `intPos` INT UNSIGNED NULL DEFAULT NULL , `intNegPos` INT NULL DEFAULT NULL , `floatNeg` FLOAT NULL DEFAULT NULL , `floatPos` FLOAT UNSIGNED NULL DEFAULT NULL , `string` VARCHAR(512) NULL DEFAULT NULL , `selection` VARCHAR(1024) NULL DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB DEFAULT CHARSET=utf8;');

DB::query('CREATE TABLE IF NOT EXISTS `settings` (`id` BIGINT UNSIGNED NOT NULL, `namespace` VARCHAR(64) NOT NULL, `jsondoc` json DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
DB::query('CREATE TABLE IF NOT EXISTS `usergroups` (`id` BIGINT UNSIGNED NOT NULL, `name` VARCHAR(20) NOT NULL, `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);');
DB::query('CREATE TABLE IF NOT EXISTS `users` (`id` BIGINT UNSIGNED NOT NULL, `username` VARCHAR(20) NOT NULL, `mailaddress` VARCHAR(254) NOT NULL, `password` VARCHAR(255) DEFAULT NULL, `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;');
DB::query('CREATE TABLE IF NOT EXISTS `users_groups` (`userid` BIGINT UNSIGNED NOT NULL, `usergroupid` BIGINT UNSIGNED NOT NULL, `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);');
DB::query('CREATE TABLE IF NOT EXISTS `users_tokens` (`id` BIGINT UNSIGNED NOT NULL, `userid` BIGINT NOT NULL, `token` VARCHAR(255) NOT NULL, `valid_until` datetime DEFAULT NULL);');

DB::query('INSERT INTO `settings` (`id`, `namespace`, `jsondoc`) VALUES (1, \'mail\', \'{}\')');
DB::query('INSERT INTO `usergroups` (`id`, `name`) VALUES (1, \'Administrator\'), (2, \'Gast\'), (3, \'Benutzer\');');

// Indexes
/*
DB::query('ALTER TABLE `headCategories` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);');
DB::query('ALTER TABLE `items` ADD PRIMARY KEY (`id`);');
DB::query('ALTER TABLE `settings` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `namespace` (`namespace`);');
DB::query('ALTER TABLE `storages` ADD PRIMARY KEY (`id`);');
DB::query('ALTER TABLE `subCategories` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `subcategory` (`name`);');
DB::query('ALTER TABLE `usergroups` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);');
DB::query('ALTER TABLE `users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`),ADD KEY `mailaddress` (`mailaddress`);');
DB::query('ALTER TABLE `users_groups` ADD PRIMARY KEY (`userid`,`usergroupid`), ADD UNIQUE KEY `userid` (`userid`), ADD KEY `fk_group` (`usergroupid`);');
*/


DB::$usenull = false;

$items = DB::query('SELECT `id`, `label`, `subcategories` FROM `items`');

if ($items != null) {
    foreach($items as $item) {
        $subIds = trim($item['subcategories'], ',');
        if (empty($subIds)) continue;
        $newSubIds = ',' . $subIds . ',';
        DB::update('items', array('subcategories' => $newSubIds), 'id=%d', $item['id']);
        printf('%s - updated...<br/>', $item['label']);
    }
}

echo '<br/><br/>Items update done.';
die();