<?php require('login.php');
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

require_once('./support/dba.php');
if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
else $smarty->assign('urlPostFix', '.php');

$parse['mode'] = "default";
$parse['showemptystorages'] = true;
$sqle = array();
$myitem = array();
//----- P0 + OK

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['remove']) && !empty($_POST['remove'])) {
    $item = DB::queryFirstRow('SELECT * FROM items WHERE id=%d', $_POST['remove']);
    $storage = DB::queryFirstRow('SELECT amount FROM storages WHERE id=%d', $item['storageid']);

    if (!empty($item['subcategories'])) {
      foreach (explode(',', $item['subcategories']) as $subCategory) {
        $subCategoryDB = DB::queryFirstRow('SELECT `id`, `amount` FROM `subCategories` WHERE `id`=%d', intval($subCategory));

        if ($subCategoryDB != null) DB::update('subCategories', array('amount' => intval($subCategoryDB['amount']) - intval($item['amount'])), 'id=%d', $subCategoryDB['id']);
      }
    }

    $headCategory = DB::queryFirstRow('SELECT `amount` FROM headCategories WHERE id=%d', $item['headcategory']);
    DB::update('storages', array('amount' => intval($storage['amount']) - intval($item['amount'])), 'id=%d', $item['storageid']);
    DB::update('headCategories', array('amount' => intval($headCategory['amount']) - intval($item['amount'])), 'id=%d', $item['headcategory']);
    DB::query('DELETE FROM items WHERE id=%d', $_POST['remove']);
  } else if (isset($_POST['removeStorage']) && !empty($_POST['removeStorage'])) {
    DB::update('items', array('storageid' => 0), 'storageid=%d', $_POST['removeStorage']);
    DB::query('DELETE FROM `storages` WHERE id=%d', $_POST['removeStorage']);
  }
}

//----- P0 - OK
$success = false;
//----- P1 + OK
if (isset($_GET['storageid']) && !empty($_GET['storageid']) && !isset($_GET['itemid'])) {
  $storeId = intval($_GET['storageid']);
  $storages = DB::query('SELECT id, label, amount FROM storages ORDER BY label ASC');
  $store = DB::queryFirstRow('SELECT id, label, amount FROM storages WHERE id=%d', $storeId);
  $items = DB::query('SELECT * FROM items WHERE storageid=%d', $storeId);


  $myitem[$storeId]['storage'] = $store;
  $myitem[$storeId]['positionen'] = 0;
  $myitem[$storeId]['itemcount'] = 0;
  for ($x = 0; $x < count($items); $x++) {
    $myitem[$storeId]['items'][] = $items[$x];
    $myitem[$storeId]['positionen']++;
    $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
  }

  //----- P1 - OK
  //----- P2 + OK       // SUBCATEGORY
} else if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
  $parse['mode'] = "subcategory";
  $parse['showemptystorages'] = false;
  $categoryId = intval($_GET['subcategory']);
  $category = DB::queryFirstRow('SELECT id, name, amount from subCategories WHERE id=%d', $categoryId);
  $items = DB::query('SELECT * FROM items WHERE subCategories LIKE %s', ('%,' . $categoryId . ',%'));

  for ($x = 0; $x < count($items); $x++) {
    $item = $items[$x];
    $storeId = 0;
    $store[$storeId]['id'] = 0;
    $store[$storeId]['label'] = $category['name'];

    $myitem[$storeId]['storage'] = $store[$storeId];
    if (!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen'] = 0;
    if (!isset($myitem[$storeId]['itemcount'])) $myitem[$storeId]['itemcount'] = 0;

    $hasImages = DB::query('SELECT `id` FROM `images` WHERE `itemId`=%d LIMIT 1', intval($items[$x]['id']));
    if (DB::affectedRows() == 1) $items[$x]['hasImages'] = true;

    $myitem[$storeId]['items'][] = $items[$x];
    $myitem[$storeId]['positionen']++;
    $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
  }


  //----- P2 - OK
  //----- P3 + OK
} else if (isset($_GET['storageid']) && !empty($_GET['storageid']) && isset($_GET['itemid']) && !empty($_GET['itemid'])) {

  $storeId = intval($_GET['storageid']);
  $itemId = intval($_GET['itemid']);

  $item = DB::queryFirstRow('SELECT * FROM items WHERE id=%d', $itemId);
  $setamount = $item['amount'];
  if (isset($_GET['amount']) && intval($_GET['amount'])) $setamount = intval($_GET['amount']);
  if ($item['storageid'] == $storeId) {
    header("location: {$urlBase}/inventory{$urlPostFix}");
    die();
  }

  if ($setamount == $item['amount']) {
    if ($storeId != null) {
      $previousStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $item['storageid']);
      DB::update('storages', array('amount' => intval($previousStorage['amount']) - intval($item['amount'])), 'id=%d', $previousStorage['id']);
    }

    $storage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $storeId);
    DB::update('storages', array('amount' => intval($storage['amount']) + intval($item['amount'])), 'id=%d', $storage['id']);
    DB::update('items', array('storageid' => $storage['id']), 'id=%d', $item['id']);
    header("location: " . $urlBase. "/inventory");
    die();
  } else {
    if ($storeId != null) {
      $previousStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $item['storageid']);
      DB::update('storages', array('amount' => intval($previousStorage['amount']) - intval($setamount)), 'id=%d', $previousStorage['id']);
    }
    $storage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $storeId);
    DB::update('storages', array('amount' => intval($storage['amount']) + intval($setamount)), 'id=%d', $storage['id']);

    $insertarray = array();
    foreach ($item as $key => $value) if ($key != 'id') $insertarray[$key] = $value;
    $insertarray['storageid'] = $storage['id'];
    $insertarray['amount'] = $setamount;
    DB::insert("items", $insertarray);

    DB::update('items', array('amount' => intval($item['amount'] - $setamount)), 'id=%d', $item['id']);

    header("location: " . $urlBase. "/inventory");
    die();
  }

  //----- P3 - OK
  //----- P4 + OK       // SEARCH
} else if (isset($_GET['searchValue']) && !empty($_GET['searchValue'])) {
  $parse['mode'] = "default";
  $parse['showemptystorages'] = true;
  $searchValue = $_GET['searchValue'];
  $storages = DB::query('SELECT id, label, amount FROM storages');
  $headCategories = DB::query('SELECT id FROM headCategories WHERE name LIKE %ss', $searchValue);
  $subCategories = DB::query('SELECT id FROM subCategories WHERE name LIKE %ss', $searchValue);
  $subCSQL = "";
  for ($x = 0; $x < count($subCategories); $x++) {
    $subCSQL .= " find_in_set('" . $subCategories[$x]['id'] . "',subcategories) <> 0 OR ";
  }
  $sql = "Select * from items WHERE ";
  if (count($headCategories) > 0)    $sql .= " headcategory IN %li OR ";
  $sql .= $subCSQL;
  $sql .= " (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss) ";


  if (count($headCategories) > 0) {
    $items = DB::query($sql, $headCategories, $searchValue, $searchValue, $searchValue);
  } else {
    $items = DB::query($sql, $searchValue, $searchValue, $searchValue);
  }

  $storages = DB::query('SELECT * FROM storages ORDER BY label ASC');
  for ($y = 0; $y < count($storages); $y++) {
    $store[$storages[$y]['id']] = $storages[$y];
  }


  for ($x = 0; $x < count($items); $x++) {
    $item = $items[$x];
    $storeId = $item['storageid'];
    $myitem[$storeId]['storage'] = $store[$storeId];
    if (!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen'] = 0;
    if (!isset($myitem[$storeId]['itemcount'])) $myitem[$storeId]['itemcount'] = 0;
    $myitem[$storeId]['items'][] = $items[$x];
    $myitem[$storeId]['positionen']++;
    $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
  }

  //----- P4 - OK
  //----- P5 + OK         // Category
} else if (isset($_GET['category']) && !empty($_GET['category'])) {

  $parse['mode'] = "category";
  $parse['showemptystorages'] = false;
  $categoryId = intval($_GET['category']);
  $category = DB::queryFirstRow('SELECT id, name, amount from headCategories WHERE id=%d', $categoryId);
  $items = DB::query('SELECT * FROM items WHERE headcategory=%d', $categoryId);

  for ($x = 0; $x < count($items); $x++) {
    $item = $items[$x];
    $storeId = 0;
    $store[$storeId]['id'] = 0;
    $store[$storeId]['label'] = gettext('Kategorie') . ": " . $category['name'];

    $myitem[$storeId]['storage'] = $store[$storeId];
    if (!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen'] = 0;
    if (!isset($myitem[$storeId]['itemcount'])) $myitem[$storeId]['itemcount'] = 0;
    $hasImages = DB::query('SELECT `id` FROM `images` WHERE `itemId`=%d LIMIT 1', intval($items[$x]['id']));
    if (DB::affectedRows() == 1) $items[$x]['hasImages'] = true;
    $myitem[$storeId]['items'][] = $items[$x];
    $myitem[$storeId]['positionen']++;
    $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
  }

  $availsubcats = DB::query('SELECT * FROM subCategories WHERE headcategory=%d ORDER BY name ASC', $categoryId);
  foreach ($availsubcats as $subCategory) {
    $storeId++;
    $store[$storeId]['label'] = gettext('Unterkategorie') . ": " . $subCategory['name'];
    $myitem[$storeId]['positionen'] = 0;
    $myitem[$storeId]['itemcount'] = 0;
    $items = DB::query('SELECT * FROM items WHERE subcategories LIKE %ss', ',' . $subCategory['id'] . ',');
    for ($x = 0; $x < count($items); $x++) {
      $item = $items[$x];

      $myitem[$storeId]['storage'] = $store[$storeId];
      $myitem[$storeId]['items'][] = $items[$x];
      $myitem[$storeId]['positionen']++;
      $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
    }
  }


  //----- P5 - OK
  //----- P6 + OK
} else {
  $parse['mode'] = "default";
  $parse['showemptystorages'] = true;
  //--
  $storagebyid = array();
  $myitem = array();
  $loseItems = DB::query('SELECT * FROM items WHERE storageid=0');
  if (count($loseItems) > 0) {
    $myitem[0]['storage']['id'] = "0";
    $myitem[0]['positionen'] = 0;
    $myitem[0]['itemcount'] = 0;
  }
  for ($x = 0; $x < count($loseItems); $x++) {
    $hasImages = DB::query('SELECT `id` FROM `images` WHERE `itemId`=%d LIMIT 1', intval($$loseItems[$x]['id']));
    if (DB::affectedRows() == 1) $$loseItems[$x]['hasImages'] = true;

    $myitem[0]['items'][] = $loseItems[$x];
    $myitem[0]['positionen']++;
    $myitem[0]['itemcount'] += $loseItems[$x]['amount'];
  }
  $storages = DB::query('SELECT * FROM storages ORDER BY label ASC');
  foreach ($storages as $store) {
    $storagebyid[$store['id']] = $store;
    $myitem[$store['id']]['storage'] = $store;
    $myitem[$store['id']]['positionen'] = 0;
    $myitem[$store['id']]['itemcount'] = 0;
    $items = DB::query('SELECT * FROM items WHERE storageid=%d', $store['id']);

    for ($x = 0; $x < count($items); $x++) {
      $hasImages = DB::query('SELECT `id` FROM `images` WHERE `itemId`=%d LIMIT 1', intval($items[$x]['id']));
      if (DB::affectedRows() == 1) $items[$x]['hasImages'] = true;

      $myitem[$store['id']]['items'][] = $items[$x];
      $myitem[$store['id']]['positionen']++;
      $myitem[$store['id']]['itemcount'] += $items[$x]['amount'];
    }
  }
}
//----- P6 - OK

$storagebyid = array();
$storages = DB::query('SELECT id, label FROM storages');
if (!isset($storagebyid)) {
  foreach ($storages as $store) {
    $storagebyid[$store['id']] = $store;
  }
}
$categories = array();
$categoryarray = DB::query('SELECT * FROM headCategories');
for ($x = 0; $x < count($categoryarray); $x++) {
  $tmp = $categoryarray[$x];
  $categories[$tmp['id']] = $tmp;
}

$subcategories = array();
$subarray = DB::query('SELECT * FROM subCategories');
for ($x = 0; $x < count($subarray); $x++) {
  $tmp = $subarray[$x];
  $subcategories[$tmp['id']] = $tmp;
}
if (!isset($items)) $items = array();

//$smarty->assign('dump',print_r(array($sql,$categories,$subcategories,$storages,$myitem,$items),true));
//$smarty->assign('dump',print_r(array($myitem,$items),true));

$smarty->assign('storages', $storages);
$smarty->assign('categories', $categories);
$smarty->assign('subcategories', $subcategories);

$smarty->assign('storagebyid', $storagebyid);
$smarty->assign('success', $success);
$smarty->assign('myitem', $myitem);
$smarty->assign('parse', $parse);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->assign('_POST', $_POST);
$smarty->assign('_GET', $_GET);

$smarty->display('inventory.tpl');


exit;
