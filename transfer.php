<?php

require('login.php');

if ($useRegistration) {
  if (!isset($user) || !isset($user['usergroupid']) || (int)$user['usergroupid'] === 2) {
    $error = gettext('Zugriff verweigert!');
    include('accessdenied.php');
    die();
  }
}

require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

require_once('./support/dba.php');
if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
else $smarty->assign('urlPostFix', '.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['getId']) && !empty($_GET['getId'])) {
    //require_once('./support/meekrodb.2.3.class.php');
    require_once('./vendor/autoload.php');
    require_once('./support/dba.php');

    $storageId = (int)$_GET['getId'];
    $items = DB::query('SELECT id, label, amount FROM items WHERE storageid=%d', $storageId);
    echo json_encode($items);
    die();
  } else if (isset($_GET['transferTarget']) && !empty($_GET['transferTarget']) && isset($_GET['transferIds']) && !empty($_GET['transferIds']) && isset($_GET['transferAmounts']) && !empty($_GET['transferAmounts'])) {
    //require_once('./support/meekrodb.2.3.class.php');
    require_once('./vendor/autoload.php');
    require_once('./support/dba.php');

    $targetStorageId = (int)trim($_GET['transferTarget'], '"');
    $transferIds = explode(',', trim($_GET['transferIds'], '"'));
    $transferAmounts = explode(',', trim($_GET['transferAmounts'], '"'));

    foreach ($transferIds as $index => $itemId) {
      $item = DB::queryFirstRow('SELECT * FROM items WHERE id=%d LIMIT 1', $itemId);
      if ($item === null) continue;
      $srcStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d LIMIT 1', $item['storageid']);
      $destStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d LIMIT 1', $targetStorageId);
      $existingDest = DB::queryFirstRow('SELECT id, amount FROM items WHERE label=%s AND storageid=%d LIMIT 1', $item['label'], $destStorage['id']);
      $images = DB::query('SELECT * FROM images WHERE itemId=%d', $itemId);

      $leftAmount = (int)$item['amount'] - (int)$transferAmounts[$index];
      if ($leftAmount != 0) {
        DB::update('items', array('amount' => $leftAmount), "id=%d", $itemId);
      }

      if ($existingDest === null) {
        $insertarray = array();
        
        foreach ($item as $key => $value) {
          if ($key !== 'id') {
            $insertarray[$key] = $value;
          }
        }

        $insertarray['storageid'] = (int)$destStorage['id'];
        $insertarray['amount'] = (int)$transferAmounts[$index];
        DB::insert("items", $insertarray);
        
        $insertedId = DB::insertId();
        foreach ($images as $image) {
          unset($image['id']);
          $image['itemId'] = $insertedId;
          DB::insert('images', $image);
        }
      } else {
        DB::update('items', array('amount' => $existingDest['amount'] + (int)$transferAmounts[$index]), 'id=%d', $existingDest['id']);
        DB::update('images', array('itemId' => $existingDest['id']), 'itemId=%d', $itemId);

        // NOTE: Add extra check to add/rewrite field data to target merge item or to delete field data instead?
        //DB::update('fieldData', array('itemId' => $existingDest['id']), "id=%d", $itemId);
        DB::delete('fieldData', 'id=%d', $itemId);
      }

      if ($leftAmount === 0) {
        DB::delete('items', 'id=%d', $item['id']);
      }

      DB::update('storages', array('amount' => (int)$srcStorage['amount'] - (int)$transferAmounts[$index]), 'id=%d', $srcStorage['id']);
      DB::update('storages', array('amount' => (int)$destStorage['amount'] + (int)$transferAmounts[$index]), 'id=%d', $destStorage['id']);
    }
    echo 'transferred';
    die();
  }
}

$storages = DB::query('SELECT id,label FROM storages');
$smarty->assign('storages', $storages);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);

$smarty->display('transfer.tpl');
