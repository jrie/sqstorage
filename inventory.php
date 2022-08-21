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
        $subCategoryDB = DB::queryFirstRow('SELECT `id`, `amount` FROM `subCategories` WHERE `id`=%d', (int)$subCategory);

        if ($subCategoryDB != null) {
          DB::update('subCategories', array('amount' => (int)$subCategoryDB['amount'] - (int)$item['amount']), 'id=%d', $subCategoryDB['id']);
        }
      }
    }

    $headCategory = DB::queryFirstRow('SELECT `amount` FROM headCategories WHERE id=%d', $item['headcategory']);
    DB::update('storages', array('amount' => (int)$storage['amount'] - (int)$item['amount']), 'id=%d', $item['storageid']);
    DB::update('headCategories', array('amount' => (int)$headCategory['amount'] - (int)$item['amount']), 'id=%d', $item['headcategory']);
    DB::query('DELETE FROM items WHERE id=%d', $_POST['remove']);
  } else if (isset($_POST['removeStorage']) && !empty($_POST['removeStorage'])) {
    DB::update('items', array('storageid' => 0), 'storageid=%d', $_POST['removeStorage']);
    DB::query('DELETE FROM `storages` WHERE id=%d', $_POST['removeStorage']);
  } else if (isset($_POST['listing-itemId'])) {
    $targetItem = DB::queryFirstRow('SELECT id, amount, headcategory, subcategories, storageid FROM items WHERE id=%d LIMIT 1', (int)$_POST['listing-itemId']);
    if (!isset($targetItem['id'])) {
      die();
    }

    $targetId = (int) $targetItem['id'];
    $targetAmountBefore = (int) $targetItem['amount'];
    $headCategory = DB::queryFirstRow('SELECT id, amount FROM headCategories WHERE id=%d', (int) $targetItem['headcategory']);
    $subIds = array();
    $subCategoriesData = array();

    if (isset($targetItem['subcategories']) && !empty($targetItem['subcategories'])) {
      $subIds = explode(',', $targetItem['subcategories']);
      foreach ($subIds as $subId) {
        $subCategory = DB::queryFirstRow('SELECT amount FROM subCategories WHERE id=%d', (int) $subId);
        if ($subCategory !== NULL) {
          $subCategoriesData[$subId] = (int) $subCategory['amount'];
        }
      }
    }

    $itemKeys = ['listing-label', 'listing-amount', 'listing-comment'];
    $dbUpdateArray = array();

    foreach($_POST as $key => $value) {
      if (in_array($key, $itemKeys)) {
        $itemKey = explode('listing-', $key, 2);
        if (isset($itemKey[1])) {
          $decodedValue = urldecode(trim($value));
          if ($itemKey[1] === 'amount') {
            // Check if the amount value consists only of digits
            // or trigger a error in Javascript
            if (!ctype_digit($decodedValue)) {
              echo 'AMOUNT_TYPE';
              die();
            }
            $targetAmountAfter = (int) $value;
          } else if ($itemKey[1] === 'label') {
            if (empty(trim($value))) {
              continue;
            }
          }
          $dbUpdateArray[$itemKey[1]] = $decodedValue;
        }
      }
    }

    if (!empty($dbUpdateArray)) {
      DB::update('items', $dbUpdateArray, 'id=%d', $targetId);

      if ($targetAmountAfter !== $targetAmountBefore) {
        $diffAmount = $targetAmountBefore - $targetAmountAfter;

        DB::update('storages', array('amount' => (int)$storage['amount'] - $diffAmount), 'id=%d', $storage['id']);
        DB::update('headCategories', array('amount' => (int)$headcategory['amount'] - $diffAmount), 'id=%d', $headcategory['id']);

        foreach($subCategoriesData as $id => $amount) {
          DB::update('subCategories', array('amount' => $amount - $diffAmount), 'id=%d', $id);
        }
      }

      echo 'OK';
      die();
    } else {
      echo 'FAIL';
      die();
    }
  }
}

//----- P0 - OK
$success = false;
$itesWithImages = DB::queryFirstColumn("SELECT  DISTINCT  itemId FROM images");

//----- P1 + OK
if (isset($_GET['storageid']) && !empty($_GET['storageid']) && !isset($_GET['itemid'])) {
  $storeId = (int)$_GET['storageid'];
  $storages = DB::query('SELECT id, label, amount FROM storages ORDER BY label ASC');
  $store = DB::queryFirstRow('SELECT id, label, amount FROM storages WHERE id=%d', $storeId);
  $items = DB::query('SELECT * FROM items WHERE storageid=%d', $storeId);
  $myitem[$storeId]['storage'] = $store;
  $myitem[$storeId]['positionen'] = 0;
  $myitem[$storeId]['itemcount'] = 0;
  for ($x = 0; $x < count($items); $x++) {


    if (in_array($items[$x]['id'], $itesWithImages  )) {
      $items[$x]['hasImages'] = true;
      $items[$x]['thumb'] = "";
    }

    $myitem[$storeId]['items'][] = $items[$x];
    $myitem[$storeId]['positionen']++;
    $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
  }

  //----- P1 - OK
  //----- P2 + OK       // SUBCATEGORY
} else if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
  $parse['mode'] = "subcategory";
  $parse['showemptystorages'] = true;
  $categoryId = (int)$_GET['subcategory'];
  $category = DB::queryFirstRow('SELECT id, name, amount from subCategories WHERE id=%d', $categoryId);
  $items = DB::query('SELECT * FROM items WHERE subCategories LIKE %s', ('%,' . $categoryId . ',%'));
  $storeId = 0;

  $store[$storeId]['id'] = $storeId;
  $store[$storeId]['label'] = gettext('Unterkategorie') . ": " . $category['name'];
  $myitem[$storeId]['storage'] = $store[$storeId];
  $myitem[$storeId]['positionen'] = 0;
  $myitem[$storeId]['itemcount'] = 0;

  for ($x = 0; $x < count($items); $x++) {
    $item = $items[$x];
    $store[$storeId]['id'] = $storeId;
    $store[$storeId]['label'] = gettext('Unterkategorie') . ": " . $category['name'];

    $myitem[$storeId]['storage'] = $store[$storeId];
    if (!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen'] = 0;
    if (!isset($myitem[$storeId]['itemcount'])) $myitem[$storeId]['itemcount'] = 0;

    if (in_array($items[$x]['id'], $itesWithImages  )) {
      $items[$x]['hasImages'] = true;
      $items[$x]['thumb'] = "";
    }

    $myitem[$storeId]['items'][] = $items[$x];
    $myitem[$storeId]['positionen']++;
    $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
  }


  //----- P2 - OK
  //----- P3 + OK
} else if (isset($_GET['storageid']) && !empty($_GET['storageid']) && isset($_GET['itemid']) && !empty($_GET['itemid'])) {

  $storeId = (int)$_GET['storageid'];
  $itemId = (int)$_GET['itemid'];

  $item = DB::queryFirstRow('SELECT * FROM items WHERE id=%d LIMIT 1', $itemId);
  $setamount = $item['amount'];
  if (isset($_GET['amount']) && (int)$_GET['amount']) $setamount = (int)$_GET['amount'];
  if ($item['storageid'] == $storeId) {
    header("location: {$urlBase}/inventory{$urlPostFix}");
    die();
  }

  if ($setamount == $item['amount']) {
    if ($storeId != null) {
      $previousStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $item['storageid']);
      DB::update('storages', array('amount' => (int)$previousStorage['amount'] - (int)$item['amount']), 'id=%d', $previousStorage['id']);
    }

    $storage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $storeId);
    DB::update('storages', array('amount' => (int)$storage['amount'] + (int)$item['amount']), 'id=%d', $storage['id']);
    DB::update('items', array('storageid' => $storage['id']), 'id=%d', $item['id']);
    header("location: " . $urlBase. "/inventory");
    die();
  } else {
    if ($storeId != null) {
      $previousStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $item['storageid']);
      DB::update('storages', array('amount' => (int)$previousStorage['amount'] - (int)$setamount), 'id=%d', $previousStorage['id']);
    }
    $storage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $storeId);
    DB::update('storages', array('amount' => (int)$storage['amount'] + (int)$setamount), 'id=%d', $storage['id']);

    $insertarray = array();
    foreach ($item as $key => $value) if ($key != 'id') $insertarray[$key] = $value;
    $insertarray['storageid'] = $storage['id'];
    $insertarray['amount'] = $setamount;
    DB::insert("items", $insertarray);

    DB::update('items', array('amount' => (int)$item['amount'] - $setamount), 'id=%d', $item['id']);

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
  //$sql .= " (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss) "; // Previous Syntax

  // Custom fields search
  $sql .= " (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss ";
  $customFields = DB::query('SELECT id, dataType FROM customFields WHERE dataType = 5 OR label LIKE %ss OR fieldValues LIKE %ss', $searchValue, $searchValue);
  $customItemIds = [];

  for ($x = 0; $x < count($customFields); ++$x) {
    if ((int) $customFields[$x]['dataType'] === 0) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND intNeg = %i", $customFields[$x]['id'], $searchValue);
    } else if ((int) $customFields[$x]['dataType'] === 1) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND intPos = %i", $customFields[$x]['id'], $searchValue);
    } else if ((int) $customFields[$x]['dataType'] === 2) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND intNegPos = %i", $customFields[$x]['id'], $searchValue);
    } else if ((int) $customFields[$x]['dataType'] === 3) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND floatNeg = %d", $customFields[$x]['id'], $searchValue);
    } else if ((int) $customFields[$x]['dataType'] === 4) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND floatPos = %d", $customFields[$x]['id'], $searchValue);
    } else if ((int) $customFields[$x]['dataType'] === 5) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND string LIKE %ss", $customFields[$x]['id'], $searchValue);
    } else if ((int) $customFields[$x]['dataType'] === 6) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND selection LIKE %ss", $customFields[$x]['id'], $searchValue);
    } else if ((int) $customFields[$x]['dataType'] === 7) {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d AND mselection LIKE %ss", $customFields[$x]['id'], $searchValue);
    } else {
      $fieldData = DB::query("SELECT itemId FROM fieldData WHERE fieldId=%d", $customFields[$x]['id']);
    }

    foreach ($fieldData as $keyItem) {
      $customItemIds[] = $keyItem['itemId'];
    }
  }

  if (count($customItemIds) > 0) {
    // SQL with custom item fields
    $sql .= " OR id IN %li)";

    if (count($headCategories) > 0) {
      $items = DB::query($sql, $headCategories, $searchValue, $searchValue, $searchValue, $customItemIds);
    } else {
      $items = DB::query($sql, $searchValue, $searchValue, $searchValue, $customItemIds);
    }

  } else {
    // Regular search
    $sql .= ")";
    if (count($headCategories) > 0) {
      $items = DB::query($sql, $headCategories, $searchValue, $searchValue, $searchValue);
    } else {
      $items = DB::query($sql, $searchValue, $searchValue, $searchValue);
    }
  }

  $storages = DB::query('SELECT * FROM storages ORDER BY label ASC');
  for ($y = 0; $y < count($storages); $y++) {
    $store[$storages[$y]['id']] = $storages[$y];
  }




  for ($x = 0; $x < count($items); $x++) {
    $item = $items[$x];
    if (in_array($items[$x]['id'], $itesWithImages  )) {
      $items[$x]['hasImages'] = true;
      $items[$x]['thumb'] = "";
    }


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
  $parse['showemptystorages'] = true;
  $categoryId = (int)$_GET['category'];
  $category = DB::queryFirstRow('SELECT id, name, amount from headCategories WHERE id=%d LIMIT 1', $categoryId);
  $items = DB::query('SELECT * FROM items WHERE headcategory=%d', $categoryId);
  $storeId = 0;

  $store[$storeId]['id'] = $storeId;
  $store[$storeId]['label'] = gettext('Kategorie') . ": " . $category['name'];

  $myitem[$storeId]['storage'] = $store[$storeId];
  $myitem[$storeId]['positionen'] = 0;
  $myitem[$storeId]['itemcount'] = 0;


  for ($x = 0; $x < count($items); $x++) {
    $item = $items[$x];
    $store[$storeId]['id'] = 0;
    $store[$storeId]['label'] = gettext('Kategorie') . ": " . $category['name'];

    $myitem[$storeId]['storage'] = $store[$storeId];
    if (!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen'] = 0;
    if (!isset($myitem[$storeId]['itemcount'])) $myitem[$storeId]['itemcount'] = 0;

    if (in_array($items[$x]['id'], $itesWithImages  )) {
      $items[$x]['hasImages'] = true;
      $items[$x]['thumb'] = "";
    }

    $myitem[$storeId]['items'][] = $items[$x];
    $myitem[$storeId]['positionen']++;
    $myitem[$storeId]['itemcount'] += $items[$x]['amount'];
  }


  $availsubcats = DB::query('SELECT * FROM subCategories WHERE headcategory=%d ORDER BY name ASC', $categoryId);
  foreach ($availsubcats as $subCategory) {
    $storeId++;
    $myitem[$storeId]['label'] = gettext('Unterkategorie') . ": " . $subCategory['name'];
    $myitem[$storeId]['positionen'] = 0;
    $myitem[$storeId]['itemcount'] = 0;
    $store[$storeId]['id'] = $storeId;

    $items = DB::query('SELECT * FROM items WHERE subcategories LIKE %ss', ',' . $subCategory['id'] . ',');
    $itesWithImages = DB::queryFirstColumn("SELECT  DISTINCT  itemId FROM images");
    for ($x = 0; $x < count($items); $x++) {
      $item = $items[$x];

    if (in_array($items[$x]['id'], $itesWithImages  )) {
      $items[$x]['hasImages'] = true;
      $items[$x]['thumb'] = "";
    }

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
    if (in_array($loseItems[$x]['id'], $itesWithImages  )) {
      $loseItems[$x]['hasImages'] = true;
      $loseItems[$x]['thumb'] = "";
    }

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
      if (in_array($items[$x]['id'], $itesWithImages  )) {
        $items[$x]['hasImages'] = true;
        $items[$x]['thumb'] = "";
      }

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

$categories[0]['name'] = gettext("Unkategorisiert");
$categories[0]['id'] = 0;
if(!isset($categories[0]['amount'])) $categories[0]['amount'] = 0;

$subcategories = array();
$subarray = DB::query('SELECT * FROM subCategories');
for ($x = 0; $x < count($subarray); $x++) {
  $tmp = $subarray[$x];
  $subcategories[$tmp['id']] = $tmp;
}
if (!isset($items)) $items = array();

/**
 * CustomFields....
 */
//GetCustomFieldsConfiguration() -->>  retval[ CategoryID oder All][customFieldsID][id/label/dataType/defau...]

$cfconf = GetCustomFieldsConfiguration();
$cfraw = DB::query('SELECT * FROM customFields');
$cfdata = GetItemBasedCFD($cfraw);


foreach($myitem as $itemL1 => $itemD1){
      for($x = 0; $x < count($itemD1['items']);$x++){
            $tocheck = array();
            $tocheck[] = 'all';
            $tocheck[] = $itemD1['items'][$x]['headcategory'];
            //$itemdatafields = GetDataFields($tocheck,$cfconf,$cfdata);
            $myitem[$itemL1]['items'][$x]['customFields'] = GetDataFields($tocheck,$cfconf,$cfdata,$itemD1['items'][$x]['id']);

      }
}



 /**
  * ....customFields
  */


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
