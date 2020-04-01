<?php require('login.php');
$SCRIPT_NAME = $_SERVER['REQUEST_URI'];
if (substr_count( $SCRIPT_NAME, '/') > 2) $urlBase = $SCRIPT_NAME;
else $urlBase = dirname($SCRIPT_NAME);

$alert = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (isset($_GET['setCategoryId']) && !empty($_GET['setCategoryId']) && isset($_GET['to']) && !empty($_GET['to'])) {
    $newCategory = DB::queryFirstRow('SELECT `id`, `amount` FROM `headCategories` WHERE `id`=%d', intval($_GET['to']));

    $subCategory = DB::queryFirstRow('SELECT `id`, `amount`, `headcategory` FROM `subCategories` WHERE id=%d', intval($_GET['setCategoryId']));
    $previousCategory = DB::queryFirstRow('SELECT `id`, `amount` FROM `headCategories` WHERE `id`=%d',  $subCategory['headcategory']);

    if ($previousCategory['id'] !== $newCategory['id']) {
      DB::update('headCategories', array('amount' => intval($previousCategory['amount']) - $subCategory['amount']), 'id=%d',  $previousCategory['id']);
      if ($newCategory !== NULL) {
        DB::update('headCategories', array('amount' => intval($newCategory['amount']) + $subCategory['amount']), 'id=%d',  $newCategory['id']);
      }

      DB::update('subCategories', array('headcategory' => $newCategory['id']), 'id=%d', intval($_GET['setCategoryId']));
    }

    header("location: $urlBase/categories");
    die();
  } else if (isset($_GET['resetSubcategoryId']) && !empty($_GET['resetSubcategoryId'])) {
    $subCategory = DB::queryFirstRow('SELECT `id`, `amount`, `headcategory` FROM `subCategories` WHERE id=%d', intval($_GET['resetSubcategoryId']));

    $previousCategory = DB::queryFirstRow('SELECT `id`, `amount` FROM `headCategories` WHERE `id`=%d',  $subCategory['headcategory']);
    if ($previousCategory !== NULL) {
      DB::update('headCategories', array('amount' => $previousCategory['amount'] - $subCategory['amount']), 'id=%d', $subCategory['headcategory']);
    }

    DB::update('subCategories', array('headcategory' => NULL), 'id=%d', $subCategory['id']);
    header("location: $urlBase/categories");
    die();
  } else if (isset($_GET['to']) && !empty($_GET['to']) && (isset($_GET['headCategory']) || isset($_GET['subCategory']))) {
    if (isset($_GET['headCategory']) && !empty($_GET['headCategory'])) {
      DB::update('headCategories', array('name' => $_GET['to']), 'id=%d', intval($_GET['headCategory']));
      if (DB::affectedRows() === 1) $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Kategorie umbenannt.') . '</p></div>';
    } else {
      DB::update('subCategories', array('name' => $_GET['to']), 'id=%d', intval($_GET['subCategory']));
      if (DB::affectedRows() === 1) $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Unterkategorie umbenannt.') . '</p></div>';
    }
  } else if (isset($_GET['removeCategory']) && !empty($_GET['removeCategory'])) {
    DB::delete('headCategories', "id=%d", intval($_GET['removeCategory']));
    if (DB::affectedRows() === 1) $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Kategorie entfernt.') . '</p></div>';
  } else if (isset($_GET['removeSubcategory']) && !empty($_GET['removeSubcategory'])) {
    DB::delete('subCategories', "id=%d", intval($_GET['removeSubcategory']));
    if (DB::affectedRows() === 1) $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Unterkategorie entfernt.') . '</p></div>';
  }
}

$headCategories = DB::query('SELECT `id`, `name`, `amount` FROM `headCategories` ORDER BY name ASC');
$subCategories = DB::query('SELECT `id`, `name`, `amount`, `headcategory` FROM `subCategories` ORDER BY name ASC');

$smarty->assign('alert', $alert);
$smarty->assign("headCategories", $headCategories);
$smarty->assign("subCategories", $subCategories);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->assign('_POST', $_POST);
$smarty->assign('_GET', $_GET);
$smarty->display('categories.tpl');
