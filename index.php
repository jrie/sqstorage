<?php require('login.php');

$success = false;
require_once('customFieldsData.php');
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);


require_once('./support/dba.php');
if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
else $smarty->assign('urlPostFix', '.php');

$imageList = null;

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getImageId'])) {
  $targetImage = DB::queryFirstRow('SELECT `imageData` FROM `images` WHERE `id`=%d', intval($_GET['getImageId']));
  $targetData = [];
  if ($targetImage != null) {
    $targetData['status'] = 'OK';
    $targetData['data'] = $targetImage['imageData'];
    echo json_encode($targetData);
  }

  die();
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['removeImageId'])) {
  DB::query('DELETE FROM `images` WHERE id=%d LIMIT 1', intval($_GET['removeImageId']));
  $targetData = [];

  if (DB::affectedRows() == 1) {
    $targetData['status'] = 'OK';
  } else {
    $targetData['status'] = 'FAIL';
  }

  echo json_encode($targetData);
  die();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES) && count($_FILES) != 0) {
  if (!isset($_FILES['images'])) die();
  if (!isset($_POST['editItem'])) die();

  $itemId = intval($_POST['editItem']);

  $count = count($_FILES['images']['tmp_name']);
  for ($x = 0; $x < $count; ++$x) {
    $tmpName = $_FILES['images']['tmp_name'][$x];

    $imageData = imagecreatefromstring(file_get_contents(addslashes($tmpName)));
    $imageLarge = imagescale($imageData, 1920);
    ob_start();
    imagejpeg($imageLarge);
    $imageData64 = base64_encode(ob_get_clean());

    $imageThumbnail = imagescale($imageData, 200);
    ob_start();
    imagejpeg($imageThumbnail);
    $imageThumbnailData64 = base64_encode(ob_get_clean());

    $imageInfo = getimagesize($tmpName);

    DB::query('INSERT INTO `images` VALUES(NULL, %d, %d, %d, %s, %s)', $itemId, $imageInfo[0], $imageInfo[1], $imageThumbnailData64, $imageData64);
  }

  header('Location: ' . $_SERVER['HTTP_REFERER']);
  die();

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $amount = isset($_POST['amount']) && !empty($_POST['amount']) ? $_POST['amount'] : 1;
  $serialNumber = isset($_POST['serialnumber']) && !empty($_POST['serialnumber']) ? $_POST['serialnumber'] : NULL;
  $comment = isset($_POST['comment']) && !empty($_POST['comment']) ? $_POST['comment'] : NULL;
  $subcategories = isset($_POST['subcategories']) && !empty($_POST['subcategories']) ? explode(',', $_POST['subcategories']) : NULL;

  // Custom fields
  if (isset($_POST['itemUpdateId']) && !empty($_POST['itemUpdateId'])) {
    $existingItem = DB::queryFirstRow('SELECT * FROM items WHERE id=%d', intVal($_POST['itemUpdateId']));

    $category = DB::queryFirstRow('SELECT id,amount FROM headCategories WHERE id=%d', intVal($existingItem['headcategory']));
    DB::update('headCategories', array('amount' => $category['amount'] - intVal($existingItem['amount'])), 'id=%d', $category['id']);

    $exitingSubCategories = explode(',', $existingItem['subcategories']);
    foreach ($exitingSubCategories as $subcategoryId) {
      $subCategory = DB::queryFirstRow('SELECT id, amount FROM subCategories WHERE id=%d', $subcategoryId);
      if ($subCategory !== null) {
        DB::update('subCategories', array('amount' => $subCategory['amount'] - intVal($existingItem['amount'])), 'id=%d', $subCategory['id']);
      }
    }

    $storage = DB::queryFirstRow('SELECT id,label,amount FROM storages WHERE id=%d', $existingItem['storageid']);
    if ($storage !== null) {
      if ($storage['amount'] - $existingItem['amount'] >= 0) {
        DB::update('storages', array('amount' => $storage['amount'] - $existingItem['amount']), 'id=%d', $storage['id']);
      }
    }
  }

  $subIds = array();
  if ($subcategories !== null) {
    foreach ($subcategories as $subcategory) {
      $subCategory = DB::queryFirstRow('SELECT id, amount FROM subCategories WHERE name=%s', $subcategory);
      if ($subCategory !== null) {
        $subIds[] = $subCategory['id'];
        DB::update('subCategories', array('amount' => $subCategory['amount'] + $amount), 'id=%d', $subCategory['id']);
      } else {
        DB::insert('subCategories', array('name' => $subcategory, 'amount' => $amount));
        $subIds[] = DB::insertId();
      }
    }
  }

  $storage = DB::queryFirstRow('SELECT id,label,amount FROM storages WHERE label=%s', $_POST['storage']);

  if ($storage == null) {
    DB::insert('storages', array('label' => $_POST['storage'], 'amount' => $amount));
    $storage['id'] = DB::insertId();
  } else DB::update('storages', array('amount' => $storage['amount'] + $amount), 'id=%d', $storage['id']);

  $category = DB::queryFirstRow('SELECT id,amount FROM headCategories WHERE name=%s', $_POST['category']);
  if ($category == null) {
    DB::insert('headCategories', array('name' => $_POST['category'], 'amount' => $amount));
    $category['id'] = DB::insertId();
  } else DB::update('headCategories', array('amount' => $category['amount'] + $amount), 'id=%d', $category['id']);

  $itemCreationId = null;
  if (isset($_POST['itemUpdateId']) && !empty($_POST['itemUpdateId'])) {
    $item = DB::update('items', array('label' => $_POST['label'], 'comment' => $comment, 'serialnumber' => $serialNumber, 'amount' => $amount, 'headcategory' => $category['id'], 'subcategories' => (',' . implode($subIds, ',') . ','), 'storageid' => $storage['id']), 'id=%d', $existingItem['id']);
    $itemCreationId = $existingItem['id'];
  } else {
    $item = DB::insert('items', array('label' => $_POST['label'], 'comment' => $comment, 'serialnumber' => $serialNumber, 'amount' => $amount, 'headcategory' => $category['id'], 'subcategories' => (',' . implode($subIds, ',') . ','), 'storageid' => $storage['id']));
    $itemCreationId = DB::insertId();
  }

  foreach(array_keys($_POST) as $key) {
    if (strncmp($key, 'cfd_', 4) === 0) {
      $fieldKey = intVal(explode('_', $key, 2)[1]);
      $value = $_POST[$key];
      $field = DB::queryFirstRow('SELECT `id`, `dataType`, `fieldValues`, `default` FROM `customFields` WHERE `id`=%d', $fieldKey);
      if ($field !== null) {
        $fieldType = null;
        foreach ($fieldTypesPos as $key => $index) {
          if ($index === intVal($field['dataType'])) {
            $fieldType = $key;
            break;
          }
        }

        if (empty($value)) {
          $convertedValue = $field['default'];
        } else {
          switch ($field['dataType']) {
            case 0:
            case 1:
            case 2:
              $convertedValue = intval($value);
            break;
            case 3:
            case 4:
              $convertedValue = doubleval($value);
            break;
            default:
              $convertedValue = $value;
            break;
          }
        }

        $existing = DB::queryFirstRow('SELECT `id` FROM `fieldData` WHERE `itemId`=%d AND `fieldId`=%d', intval($itemCreationId), intval($field['id']));
        if ($existing == null) DB::insert('fieldData', [$fieldType => $convertedValue, 'itemId' => intval($itemCreationId),'fieldId' => intval($field['id'])]);
        else DB::update('fieldData', [$fieldType => $convertedValue, 'itemId' => intval($itemCreationId),'fieldId' => intval($field['id'])], 'id=%d', $existing['id']);
      }
    }
  }

  $success = true;
}

$isEdit = false;
$imageList = null;
if ((isset($_GET['editItem']) && !empty($_GET['editItem'])) || (isset($_POST['editItem']) && !empty($_POST['editItem']))) {
  if (isset($_GET['editItem'])) $itemId = intval($_GET['editItem']);
  else if (isset($_POST['editItem'])) $itemId = intval($_POST['editItem']);

  $item = DB::queryFirstRow('SELECT * FROM `items` WHERE `id`=%d', $itemId);
  $customData = DB::query('SELECT * FROM `fieldData` WHERE `itemId`=%d', intval($item['id']));
  $isEdit = true;

  $imageList = DB::query('SELECT `id`, `thumb`, `sizeX`, `sizeY` FROM `images` WHERE `itemId`=%d', $item['id']);
} else {
  $customData = null;
  if (isset($item)) $imageList = DB::query('SELECT `id`, `thumb`, `sizeX`, `sizeY` FROM `images` WHERE `itemId`=%d', intval($item['id']));
}

$smarty->assign('imageList', $imageList);

if (!isset($item)) $item = array();
$storages = DB::query('SELECT `id`, `label` FROM storages');
$categories = DB::query('SELECT `id`, `name` FROM headCategories');
$subcategories = DB::query('SELECT `id`, `name` FROM subCategories');

$customFields = DB::query('SELECT * FROM customFields');

$smarty->assign('success', $success);
$smarty->assign('isEdit', $isEdit);
if ($isEdit) $smarty->assign('editCategory', $item['headcategory']);
else $smarty->assign('editCategory', -1);
$smarty->assign('item', $item);
$smarty->assign('storages', $storages);
$smarty->assign('categories', $categories);
$smarty->assign('subcategories', $subcategories);

$smarty->assign('customData', $customData);
$smarty->assign('customFields', $customFields);
$smarty->assign('fieldTypesPos', $fieldTypesPos);
$smarty->assign('fieldLimits', $fieldLimits);
$smarty->assign('dataExamples', $dataExamples);

if (isset($_POST)) $smarty->assign('POST', $_POST);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->display('indexpage.tpl');

exit;
