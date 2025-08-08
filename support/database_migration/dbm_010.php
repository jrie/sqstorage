<?php
DB::query('ALTER TABLE `items` ADD `checkedin` BOOLEAN NULL DEFAULT 0 AFTER `coverimage`;');