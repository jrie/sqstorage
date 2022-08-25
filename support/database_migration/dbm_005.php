<?php

DB::query('ALTER TABLE `users_groups` DROP PRIMARY KEY;');
DB::query('ALTER TABLE `users_groups` CHANGE `userid` `userid` BIGINT(20) UNSIGNED NOT NULL;');
DB::query('ALTER TABLE `users_groups` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);');
