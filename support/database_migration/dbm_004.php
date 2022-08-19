<?php

$zeroCategory = DB::queryFirstRow('SELECT `name`, `amount` FROM `headCategories` WHERE `id`=0 LIMIT 1');
if ($zeroCategory === NULL) {
    DB::query('UPDATE `headCategories` SET `id`=0, `name`="XYZXYZXYZ", amount=0 LIMIT 1');
} else {
    DB::query('UPDATE `headCategories` SET `id`=0, `name`="XYZXYZXYZ", `amount`=0 WHERE id=0 LIMIT 1');
    DB::insert('headCategories', ['name' => $zeroCategory['name'], 'amount' => $zeroCategory['amount']]);
    $zeroInsertId = DB::insertId();

    $zeroItems = DB::query("SELECT `amount` FROM `items` WHERE `headcategory` = 0");
    $affectedRows = DB::affectedRows();
    if ($affectedRows !== 0) {
        $itemAmounts = 0;
        foreach ($zeroItems as $item) {
            $itemAmounts += (int) $item['amount'];
        }

        DB::query('UPDATE `headCategories` SET `amount`=%d WHERE id=0 LIMIT 1', $itemAmounts);
    }
    
    DB::query("UPDATE `items` SET `headcategory`=%d WHERE `headcategory` = 0", $zeroInsertId);
    DB::query("UPDATE `subCategories` SET `headcategory`=%d WHERE `headcategory` = 0", $zeroInsertId);
}
