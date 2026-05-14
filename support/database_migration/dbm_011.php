<?php
$customFieldRev = 2;
DB::query('ALTER TABLE `fielddata` ADD `datetime` DATETIME NOT NULL AFTER `qrcode`;');
DB::query('ALTER TABLE `fielddata` ADD `floatNegPos` DOUBLE NULL DEFAULT NULL AFTER `floatPos`;');
DB::query('ALTER TABLE `database_rev` ADD `customfieldrev` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `dbrev`;');

$customFieldRevDB = DB::queryFirstRow('SELECT `customfieldrev` FROM `database_rev`;');
if ($customFieldRev != $customFieldRevDB['customfieldrev']) {
    DB::query('UPDATE `customfields` SET `dataType` = `dataType` + 1 WHERE `customfields`.`dataType` > 4;');
    DB::query('UPDATE `database_rev` SET `customfieldrev` = 2;');
}
