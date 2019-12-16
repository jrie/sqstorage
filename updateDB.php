<?php require('login.php'); ?>
<?php
include_once('head.php');

DB::query('CREATE TABLE IF NOT EXISTS `customFields` ( `id` BIGINT(20) UNSIGNED NOT NULL, `label` VARCHAR(64) NOT NULL, `dataType` INT UNSIGNED NOT NULL, `default` VARCHAR(64) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
DB::query('CREATE TABLE IF NOT EXISTS `fieldData` ( `id` BIGINT UNSIGNED NOT NULL , `fieldId` BIGINT UNSIGNED NOT NULL , `intNeg` INT NULL DEFAULT NULL , `intPos` INT UNSIGNED NULL DEFAULT NULL , `intNegPos` INT NULL DEFAULT NULL , `floatNeg` FLOAT NULL DEFAULT NULL , `floatPos` FLOAT UNSIGNED NULL DEFAULT NULL , `string` VARCHAR(512) NULL DEFAULT NULL , `selection` VARCHAR(1024) NULL DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB DEFAULT CHARSET=utf8;');

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