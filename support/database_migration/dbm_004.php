<?php

$zeroCategory = DB::queryFirstRow('SELECT `name`, `amount` FROM `headCategories` WHERE `id`=0 LIMIT 1');
if ($zeroCategory === NULL) {
    DB::query("INSERT INTO `headCategories` (`id`, `name`, `amount`) VALUES (NULL, 'XYZXYZXYZ', '0') ");
    DB::query('Update `headCategories` set id=0 WHERE `name` LIKE "XYZXYZXYZ"');
} else {
    DB::query('UPDATE `headCategories` SET `id`=0, `name`="XYZXYZXYZ", amount=0 WHERE id=0 LIMIT 1');
    DB::insert('headCategories', ['name' => $zeroCategory['name'], 'amount' => $zeroCategory['amount']]);
    $zeroInsertId = DB::insertId();

    DB::query("UPDATE items SET `headcategory`=%d WHERE `headcategory` = 0", $zeroInsertId);
    DB::query("UPDATE `subCategories` SET `headcategory`=%d WHERE `headcategory` = 0", $zeroInsertId);
}
