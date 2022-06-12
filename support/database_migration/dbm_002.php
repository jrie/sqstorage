<?php

DB::query('ALTER TABLE `items` CHANGE `amount` `amount` BIGINT UNSIGNED NOT NULL DEFAULT \'1\'' );
