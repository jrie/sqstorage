<?php require('login.php'); ?>
<?php
include_once('head.php');

DB::query('CREATE TABLE `tlv`.`customFields` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `label` VARCHAR(64) NOT NULL , `type` TINYINT UNSIGNED NOT NULL , `defaultValue` VARCHAR(512) NULL DEFAULT NULL , `subvalues` VARCHAR(512) NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci');


DB::$usenull = false;

$items = DB::query('SELECT id, label, subcategories FROM items');

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