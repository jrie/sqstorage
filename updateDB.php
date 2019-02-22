<?php
include_once('head.php');

$items = DB::query('SELECT id, label, subcategories FROM items');

if ($items != null) {
    foreach($items as $item) {
        $subIds = trim($item['subcategories'], ',');
        $newSubIds = ',' . $subIds . ',';
        DB::update('items', array('subcategories' => $newSubIds), 'id=%d', $item['id']);
        printf('%s - updated...<br/>', $item['label']);
    }
}

echo '<br/><br/>Items update done.';
die();