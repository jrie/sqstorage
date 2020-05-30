<?php require('login.php'); ?>
<?php
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['getId']) && !empty($_GET['getId'])) {
    //require_once('./support/meekrodb.2.3.class.php');
    require_once('./vendor/autoload.php');
    require_once('./support/dba.php');

    $storageId = intVal($_GET['getId']);
    $items = DB::query('SELECT id, label, amount FROM items WHERE storageid=%d', $storageId);
    echo json_encode($items);
    die();
  } else if (isset($_GET['transferTarget']) && !empty($_GET['transferTarget']) && isset($_GET['transferIds']) && !empty($_GET['transferIds'])) {
    //require_once('./support/meekrodb.2.3.class.php');
    require_once('./vendor/autoload.php');
    require_once('./support/dba.php');

    $targetStorageId = intVal(trim($_GET['transferTarget'], '"'));
    $transferIds = explode(',', trim($_GET['transferIds'], '"'));

    foreach ($transferIds as $itemId) {
      $item = DB::queryFirstRow('SELECT storageid, amount FROM items WHERE id=%d', $itemId);
      if ($item === NULL) continue;

      $srcStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $item['storageid']);
      $destStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $targetStorageId);

      DB::update('storages', array('amount' => intVal($srcStorage['amount']) - intVal($item['amount'])), 'id=%d', $srcStorage['id']);
      DB::update('storages', array('amount' => intVal($destStorage['amount']) + intVal($item['amount'])), 'id=%d', $destStorage['id']);
      DB::update('items', array('storageid' => $targetStorageId), 'id=%d', $itemId);
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
