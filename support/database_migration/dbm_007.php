<?php
DB::query('ALTER TABLE `fieldData` ADD `qrcode` VARCHAR(256) NULL DEFAULT NULL AFTER `mselection`;');

