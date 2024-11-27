<?php

DB::query('ALTER TABLE `headCategories` CHANGE `amount` `amount` BIGINT(20) UNSIGNED;');
DB::query('ALTER TABLE `items` CHANGE `amount` `amount` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;');
DB::query('ALTER TABLE `storages` CHANGE `amount` `amount` BIGINT(20) UNSIGNED;');
