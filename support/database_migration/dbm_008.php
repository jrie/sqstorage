<?php
DB::query("ALTER TABLE `storages` CHANGE `amount` `amount` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';");
