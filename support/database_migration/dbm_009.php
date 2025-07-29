<?php
DB::query("ALTER TABLE `items` ADD `coverimage` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `storageid`;");
