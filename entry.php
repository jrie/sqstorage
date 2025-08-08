<?php

if (!file_exists('./support/dba.php')) {
  header("Location: install.php");
  die();
}

require('login.php');

require_once 'customFieldsData.php';
require_once 'support/urlBase.php';
$smarty->assign('urlBase', $urlBase);

require_once './includer.php';
if (!CheckDBCredentials($host, DB::$user, DB::$password, $dbName, null, null, $port)) {
  header("Location: install.php");
  die();
}

$tbls = DB::tableList();
if (count($tbls) == 0) {
  header("Location: install.php");
  die();
}

if ($useRegistration) {
  if (!isset($user) || !isset($user['usergroupid']) || (int)$user['usergroupid'] === 2) {
    header('Location: '. $urlBase . '/inventory' . $urlPostFix);
    die();
  }
}

if ($usePrettyURLs) {
  $smarty->assign('urlPostFix', '');
} else {
  $smarty->assign('urlPostFix', '.php');
}

$imageList = null;
$smarty->assign('updatedEntry', '-1');


if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['setcoverimage']) && isset($_GET['targetitem'])) {
  $out = DB::update('items', array('coverimage' => (int) $_GET['setcoverimage']), 'id=%d',  $_GET['targetitem']);

  if ($out === 1) {
    echo 'OK';
  } else  {
    echo 'FAIL';
  }

  die();
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getImageId'])) {
  $targetImage = DB::queryFirstRow('SELECT `imageData` FROM `images` WHERE `id`=%d', (int)$_GET['getImageId']);
  $targetData = [];
  if ($targetImage != null) {
    $targetData['status'] = 'OK';
    $targetData['data'] = $targetImage['imageData'];
    echo json_encode($targetData);
  }

  die();
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['removeImageId'])) {
  DB::query('DELETE FROM `images` WHERE id=%d LIMIT 1', (int)$_GET['removeImageId']);
  $targetData = [];

  if (DB::affectedRows() == 1) {
    $targetData['status'] = 'OK';
  } else {
    $targetData['status'] = 'FAIL';
  }

  echo json_encode($targetData);
  die();
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['checkin'])) {
  $itemId = $_GET['checkin'];
  DB::query('UPDATE `items` SET `checkedin` = !`checkedin` WHERE id=%d', $itemId);
  $targetData = [];

  if (DB::affectedRows() == 1) {
    $result = DB::queryFirstRow('SELECT `checkedin` FROM `items` WHERE id=%d', $itemId);
    if (isset($result) && isset($result['checkedin'])) {
      $checkedInStatus = $result['checkedin'];
    }
  } else {
    $checkedInStatus = 'fail';
  }

  $smarty->assign('checkedInStatus', $checkedInStatus);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES) && count($_FILES) != 0 && isset($_POST['editItem'])) {
  if (!isset($_FILES['images'])) die();
  if (!isset($_POST['editItem'])) die();

  $itemId = (int)$_POST['editItem'];

  $count = count($_FILES['images']['tmp_name']);

  // Check for zero length image size items due to "upload_max_size" php.ini errors
  // And display a message to inform the user about this
  for ($x = 0; $x < $count; ++$x) {
    if ($_FILES['images']['size'][$x] === 0) {
      $tmpName = $_FILES['images']['name'][$x];
      echo '<!DOCTYPE html>' . PHP_EOL . '<head>' . PHP_EOL . '<title>sqStorage - Image upload error</title>' . PHP_EOL . '<link rel="stylesheet" href="/css/bootstrap/bootstrap.css">' . PHP_EOL . '';
      echo '<link rel="stylesheet" href="/css/base.css">' . PHP_EOL . '<link rel="stylesheet" href="/fonts/fontawesome/css/solid.css"><link rel="stylesheet" href="/fonts/fontawesome/css/regular.css"><link rel="stylesheet" href="/fonts/fontawesome/css/fontawesome.css">' . PHP_EOL . '';
      echo '<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL . '';
      echo '</head>' . PHP_EOL . '<body>' . PHP_EOL . '<nav class="navbar navbar-light bg-light">' . PHP_EOL . '<a href="'.  $urlBase . '/index' . $urlPostFix . '"><img class="logo" src="/img/sqstorage.png" alt="sqStorage logo" /></a>' . PHP_EOL . '</nav>';
      echo '<div class="content">' . PHP_EOL . '<div class="alert alert-danger">' . PHP_EOL . '';
      echo '<h2>File image upload error due to size</h2><br><p>Error uploading image file: "<b>' . $tmpName . '</b>"<br><br>Visit and try to fix the PHP "upload_max_filesize" parameter, see for details: <a href="https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize">https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize</a><br><br><a href="' . $_SERVER['HTTP_REFERER'] . '">Click here to return to the previous page.</a></p>';
      echo '</div>' . PHP_EOL . '</div>' . PHP_EOL . '</body>' . PHP_EOL . '</html>';
      die();
    }
  }

  for ($x = 0; $x < $count; ++$x) {
    $tmpName = $_FILES['images']['tmp_name'][$x];

    $imageInfo = getimagesize($tmpName);
    $imgMime = $imageInfo['mime'];
    $imageData = imagecreatefromstring(file_get_contents(addslashes($tmpName)));
    $imageLarge = $imageData; // Keep the original upload size
    $imageThumbnail = imagescale($imageData, 200);

    if ($imgMime == 'image/png') {
      ob_start();
      imagepng($imageLarge);
    } else if ($imgMime == 'image/jpeg') {
      ob_start();
      imagejpeg($imageLarge);
    } else if ($imgMime == 'image/gif') {
      ob_start();
      imagegif($imageLarge);
    } else if ($imgMime == 'image/webp') {
      ob_start();
      imagewebp($imageLarge);
    } else if ($imgMime == 'image/bmp') {
      ob_start();
      imagebmp($imageLarge);
    } else {
      // Img type not supported
      // Show a proper message to user
      echo 'The selected image format is not supported.';
      die();
    }

    $imageData64 = base64_encode(ob_get_clean());

    if ($imgMime == 'image/png') {
      ob_start();
      imagepng($imageThumbnail);
    } else if ($imgMime == 'image/jpeg') {
      ob_start();
      imagejpeg($imageThumbnail);
    } else if ($imgMime == 'image/gif') {
      ob_start();
      imagegif($imageThumbnail);
    } else if ($imgMime == 'image/webp') {
      ob_start();
      imagewebp($imageThumbnail);
    } else if ($imgMime == 'image/bmp') {
      ob_start();
      imagebmp($imageThumbnail);
    } else {
      // Img type not supported
      // Show a proper message to user
      echo 'The selected image format is not supported.';
      die();
    }

    $imageThumbnailData64 = base64_encode(ob_get_clean());
    DB::query('INSERT INTO `images` VALUES(NULL, %d, %d, %d, %s, %s)', $itemId, $imageInfo[0], $imageInfo[1], $imageThumbnailData64, $imageData64);
  }

  header('Location: ' . $_SERVER['HTTP_REFERER']);
  die();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $amount = isset($_POST['amount']) && !empty($_POST['amount']) ? (int) $_POST['amount'] : 0;
  if ($amount < 0 || strlen( (string) $amount) > 19) {
    die();
  }

  $serialNumber = isset($_POST['serialnumber']) && !empty($_POST['serialnumber']) ? $_POST['serialnumber'] : NULL;
  $comment = isset($_POST['comment']) && !empty($_POST['comment']) ? $_POST['comment'] : NULL;
  $subcategories = isset($_POST['subcategories']) && !empty($_POST['subcategories']) ? explode(',', $_POST['subcategories']) : NULL;

  // Custom fields
  if (isset($_POST['itemUpdateId']) && !empty($_POST['itemUpdateId'])) {
    $existingItem = DB::queryFirstRow('SELECT * FROM items WHERE id=%d LIMIT 1', (int)$_POST['itemUpdateId']);

    $category = DB::queryFirstRow('SELECT id,amount FROM headCategories WHERE id=%d LIMIT 1', (int)$existingItem['headcategory']);
    if ($category !== NULL && $existingItem !== NULL) {
      DB::update('headCategories', array('amount' => (int)$category['amount'] - (int)$existingItem['amount']), 'id=%d', $category['id']);
    }

    $exitingSubCategories = explode(',', $existingItem['subcategories']);
    foreach ($exitingSubCategories as $subcategoryId) {
      $subCategory = DB::queryFirstRow('SELECT id, amount FROM subCategories WHERE id=%d', $subcategoryId);
      if ($subCategory !== null) {
        DB::update('subCategories', array('amount' => $subCategory['amount'] - (int)$existingItem['amount']), 'id=%d', $subCategory['id']);
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
    $item = DB::update('items', array('label' => $_POST['label'], 'comment' => $comment, 'serialnumber' => $serialNumber, 'amount' => $amount, 'headcategory' => $category['id'], 'subcategories' => (',' . implode(',', $subIds) . ','), 'storageid' => $storage['id']), 'id=%d', $existingItem['id']);
    $itemCreationId = $existingItem['id'];

    if ($itemCreationId) {
      $smarty->assign('updatedEntry', 'updated');
    }
  } else {
    $item = DB::insert('items', array('label' => $_POST['label'], 'comment' => $comment, 'serialnumber' => $serialNumber, 'amount' => $amount, 'headcategory' => $category['id'], 'subcategories' => (',' . implode(',', $subIds) . ','), 'storageid' => $storage['id']));
    $itemCreationId = DB::insertId();

    if ($itemCreationId) {
      $smarty->assign('updatedEntry', 'created');
    }

    if(isset($_FILES['images'])){
      $count = count($_FILES['images']['tmp_name']);
      if($count > 0){
        for ($x = 0; $x < $count; ++$x) {
          $tmpName = $_FILES['images']['tmp_name'][$x];
          if(strlen($tmpName)>0 ){
          $imageInfo = getimagesize($tmpName);
          $imgMime = $imageInfo['mime'];
          $imageData = imagecreatefromstring(file_get_contents(addslashes($tmpName)));
          $imageLarge = $imageData; // Keep the original upload size
          $imageThumbnail = imagescale($imageData, 200);

          if ($imgMime == 'image/png') {
            ob_start();
            imagepng($imageLarge);
          } else if ($imgMime == 'image/jpeg') {
            ob_start();
            imagejpeg($imageLarge);
          } else if ($imgMime == 'image/gif') {
            ob_start();
            imagegif($imageLarge);
          } else if ($imgMime == 'image/webp') {
            ob_start();
            imagewebp($imageLarge);
          } else if ($imgMime == 'image/bmp') {
            ob_start();
            imagebmp($imageLarge);
          } else {
            // Img type not supported
            // Show a proper message to user
            echo 'The selected image format is not supported.';
            die();
          }

          imagedestroy($imageLarge);
          $imageData64 = base64_encode(ob_get_contents());
          ob_end_clean();

          if ($imgMime == 'image/png') {
            ob_start();
            imagepng($imageThumbnail);
          } else if ($imgMime == 'image/jpeg') {
            ob_start();
            imagejpeg($imageThumbnail);
          } else if ($imgMime == 'image/gif') {
            ob_start();
            imagegif($imageThumbnail);
          } else if ($imgMime == 'image/webp') {
            ob_start();
            imagewebp($imageThumbnail);
          } else if ($imgMime == 'image/bmp') {
            ob_start();
            imagebmp($imageThumbnail);
          } else {
            // Img type not supported
            // Show a proper message to user
            echo 'The selected image format is not supported.';
            die();
          }
          imagedestroy($imageThumbnail);
          $imageThumbnailData64 = base64_encode(ob_get_contents());
          ob_end_clean();
          DB::query('INSERT INTO `images` VALUES(NULL, %d, %d, %d, %s, %s)', $itemCreationId, $imageInfo[0], $imageInfo[1], $imageThumbnailData64, $imageData64);
          }
        }
      }
    }
  }

  foreach (array_keys($_POST) as $key) {
    if (strncmp($key, 'cfd_', 4) === 0) {
      $fieldKey = (int) explode('_', $key, 2)[1];
      $value = $_POST[$key];
      $field = DB::queryFirstRow('SELECT `id`, `dataType`, `fieldValues`, `default` FROM `customFields` WHERE `id`=%d', $fieldKey);
      if ($field !== null) {
        $fieldType = null;
        foreach ($fieldTypesPos as $key => $index) {
          if ($index == (int) $field['dataType']) {
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
              $convertedValue = (int)$value;
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
        $existing = DB::queryFirstRow('SELECT `id` FROM `fieldData` WHERE `itemId`=%d AND `fieldId`=%d', (int)$itemCreationId, (int)$field['id']);
        if ($existing == null) DB::insert('fieldData', [$fieldType => $convertedValue, 'itemId' => (int)$itemCreationId, 'fieldId' => (int)$field['id']]);
        else DB::update('fieldData', [$fieldType => $convertedValue, 'itemId' => (int)$itemCreationId, 'fieldId' => (int)$field['id']], 'id=%d', $existing['id']);
      }
    }
  }
}

$imageList = null;
if ((isset($_GET['editItem']) && !empty($_GET['editItem'])) || (isset($_POST['editItem']) && !empty($_POST['editItem'])) || (isset($_GET['checkin']) && !empty($_GET['checkin']))) {
  if (isset($_GET['editItem'])) $itemId = (int)$_GET['editItem'];
  else if (isset($_POST['editItem'])) $itemId = (int)$_POST['editItem'];
  else if (isset($_GET['checkin'])) $itemId = (int)$_GET['checkin'];

  $item = DB::queryFirstRow('SELECT * FROM `items` WHERE `id`=%d', $itemId);
  if (isset($item)) {
    $customData = DB::query('SELECT * FROM `fieldData` WHERE `itemId`=%d', (int)$item['id']);
    $isEdit = true;

    $imageList = DB::query('SELECT `id`, `thumb`, `sizeX`, `sizeY` FROM `images` WHERE `itemId`=%d', $item['id']);
  }

} else {
  $customData = null;
  //if (isset($item)) $imageList = DB::query('SELECT `id`, `thumb`, `sizeX`, `sizeY` FROM `images` WHERE `itemId`=%d', (int)$item['id']);
}

$smarty->assign('imageList', $imageList);

if (!isset($item)) $item = array();
$storages = DB::query('SELECT `id`, `label` FROM storages');
$categories = DB::query('SELECT `id`, `name` FROM headCategories');

foreach ($categories as $key => $category) {
  if ((int) $category['id'] === 0) {
    unset($categories[$key]);
    break;
  }
}

$subcategories = DB::query('SELECT `id`, `name` FROM subCategories');
$customFields = DB::query('SELECT * FROM customFields');


$qrData = array();
$qrIdsCollection = array();
$qrTypesCollection = array();
foreach ($qrBaseFields as $qrBaseFields) {
  // echo 'Id: ' . $qrBaseFields['id'];
  // echo '<br>';
  // echo 'Type: ' . $qrBaseFields['type'];
  // echo '<br>';
  // echo 'Text: ' . $qrBaseFields['text'];
  // echo '<br><br>';
  array_push($qrIdsCollection, $qrBaseFields['id']);
  array_push($qrTypesCollection, $qrBaseFields['type']);
}

// echo '<br>Item:<br>';
// var_dump($item);

// echo 'ids <br>';
// var_dump($qrIdsCollection);
// echo '<br><br>';
// echo 'types <br>';
// var_dump($qrTypesCollection);
// echo '<br><br>';

if (isset($item) && !empty($item)) {
  foreach ($customFields as $key => $customField) {
    if ($customField['dataType'] !== '8') {
      continue;
    }

    // echo 'Id: ' . $customField['id'];
    // echo '<br>';
    // echo 'Label: ' . $customField['label'];
    // echo '<br>';
    // echo 'Type: ' . $customField['dataType'];
    // echo '<br>';
    // echo 'FieldValues: ' . $customField['fieldValues'];
    // echo '<br><br>';

    $itemIndex = array_search($customField['fieldValues'], $qrIdsCollection, false);
    if (!is_bool($itemIndex) && isset($itemId)) {
      $targetId = $qrIdsCollection[$itemIndex];
      $targetBase = $qrTypesCollection[$itemIndex];
      $searchColumn = null;
      $dataEntry = null;
      $dataEntryType = null;
      $prepend = null;

      if ($targetBase === 'base') {
        switch ($targetId) {
          case 0:
            $searchColumn = 'label';
            break;
          case 1:
            $searchColumn = 'serialnumber';
            break;
          case 3:
            $searchColumn = 'id';
            $prepend = $urlBase . '/entry' . $urlPostFix . '?checkin=';
            break;
          default:
            break;
        }

        if ($searchColumn) {
          $dataEntry = DB::queryFirstRow('SELECT %b as `data-value` FROM `items` WHERE `id`=%d;', $searchColumn, $itemId);
          // echo '<br>BASE<br>';
          // echo $searchColumn . '<br>';
          // var_dump($dataEntry);
          // echo '<br><br>';
        }
      } else if ($targetBase === 'extend') {
        switch ($targetId) {
          case 2:
            $searchColumn = 'storageid';
            $prepend = $urlBase . '/inventory' . $urlPostFix . '?category=';
            break;
          default:
            break;
        }

        if ($searchColumn) {
          $dataEntry = DB::queryFirstRow('SELECT `id` as `data-value` FROM `storages` WHERE `id`=%d;', $item['storageid']);
          // echo '<br>EXTEND<br>';
          // echo $searchColumn . '<br>';
          // var_dump($dataEntry);
          // echo '<br><br>';
        }
      }

      if ($dataEntry && isset($dataEntry['data-value'])) {
        if (isset($prepend)) {
          $dataEntry['data-value'] =  $prepend . $dataEntry['data-value'];
        }

        $qrData[$customField['id']] = $dataEntry;
        $customFields[$key]['qrValue'] = $dataEntry['data-value'];
      }
    }
  }
}


// $smarty->assign('qrData', $qrData);

if (isset($isEdit)) {
  $smarty->assign('isEdit', $isEdit);
  $smarty->assign('editCategory', $item['headcategory']);
} else {
  $smarty->assign('editCategory', -1);
  $smarty->assign('isEdit', false);
}

$smarty->assign('item', $item);
$smarty->assign('storages', $storages);
$smarty->assign('categories', $categories);
$smarty->assign('subcategories', $subcategories);

if (isset($customData)) {
  $smarty->assign('customData', $customData);
}

$smarty->assign('customFields', $customFields);
$smarty->assign('fieldTypesPos', $fieldTypesPos);
$smarty->assign('fieldLimits', $fieldLimits);
$smarty->assign('dataExamples', $dataExamples);

if (isset($_POST)) $smarty->assign('POST', $_POST);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->display('entry.tpl');
die();
