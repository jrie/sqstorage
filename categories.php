<?php
require_once 'login.php';
require_once 'support/urlBase.php';
$smarty->assign('urlBase', $urlBase);

require_once './support/dba.php';
if ($usePrettyURLs) {
    $smarty->assign('urlPostFix', '');
} else {
    $smarty->assign('urlPostFix', '.php');
}


$alert = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['setCategoryId']) && !empty($_GET['setCategoryId']) && isset($_GET['to']) && !empty($_GET['to'])) {
        $newCategory = DB::queryFirstRow('SELECT `id`, `amount` FROM `headCategories` WHERE `id`=%d', (int)$_GET['to']);

        $subCategory = DB::queryFirstRow('SELECT `id`, `amount`, `headcategory` FROM `subCategories` WHERE id=%d', (int)$_GET['setCategoryId']);
        $previousCategory = DB::queryFirstRow('SELECT `id`, `amount` FROM `headCategories` WHERE `id`=%d',  $subCategory['headcategory']);

        if ($previousCategory['id'] !== $newCategory['id']) {
            DB::update('headCategories', array('amount' => (int)$previousCategory['amount'] - (int)$subCategory['amount']), 'id=%d',  $previousCategory['id']);
            if ($newCategory !== null) {
                DB::update('headCategories', array('amount' => (int)$newCategory['amount'] + (int)$subCategory['amount']), 'id=%d',  $newCategory['id']);
            }

            DB::update('subCategories', array('headcategory' => $newCategory['id']), 'id=%d', (int)$_GET['setCategoryId']);
        }

        if ($usePrettyURLs) {
            header("location: $urlBase/categories");
        } else {
            header("location: $urlBase/categories.php");
        }
        die();
    } else if (isset($_GET['resetSubcategoryId']) && !empty($_GET['resetSubcategoryId'])) {
        $subCategory = DB::queryFirstRow('SELECT `id`, `amount`, `headcategory` FROM `subCategories` WHERE id=%d', (int)$_GET['resetSubcategoryId']);

        $previousCategory = DB::queryFirstRow('SELECT `id`, `amount` FROM `headCategories` WHERE `id`=%d',  $subCategory['headcategory']);
        if ($previousCategory !== null) {
            DB::update('headCategories', array('amount' => $previousCategory['amount'] - $subCategory['amount']), 'id=%d', $subCategory['headcategory']);
        }

        DB::update('subCategories', array('headcategory' => null), 'id=%d', $subCategory['id']);

        if ($usePrettyURLs) {
            header("location: $urlBase/categories");
        } else {
            header("location: $urlBase/categories.php");
        }

        die();
    } else if (isset($_GET['to']) && !empty($_GET['to']) && (isset($_GET['headCategory']) || isset($_GET['subCategory']))) {
        if (isset($_GET['headCategory']) && !empty($_GET['headCategory'])) {
            DB::update('headCategories', array('name' => $_GET['to']), 'id=%d', (int)$_GET['headCategory']);
            if (DB::affectedRows() === 1) {
                $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Kategorie umbenannt.') . '</p></div>';
            }
        } else {
            DB::update('subCategories', array('name' => $_GET['to']), 'id=%d', (int)$_GET['subCategory']);
            if (DB::affectedRows() === 1) {
                $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Unterkategorie umbenannt.') . '</p></div>';
            }
        }
    } else if (isset($_GET['removeCategory']) && !empty($_GET['removeCategory'])) {
        DB::delete('headCategories', "id=%d", (int)$_GET['removeCategory']);
        if (DB::affectedRows() === 1) {
            $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Kategorie entfernt.') . '</p></div>';
        }
        DB::query('UPDATE items SET headcategory = 0 WHERE headcategory = %i', $_GET['removeCategory']);
        DB::query('UPDATE subCategories SET headcategory = 0 WHERE headcategory = %i', $_GET['removeCategory']);
    } else if (isset($_GET['removeSubcategory']) && !empty($_GET['removeSubcategory'])) {
        $subCategory = DB::queryFirstRow('SELECT `id`, `amount`, `headcategory` FROM `subCategories` WHERE `id`=%d', (int)$_GET['removeSubcategory']);
        $previousCategory = DB::queryFirstRow('SELECT `id`, `amount` FROM `headCategories` WHERE `id`=%d',  $subCategory['headcategory']);
        if ($subCategory !== null) {
            if ($previousCategory !== null) {
                DB::update('headCategories', array('amount' => (int)$previousCategory['amount'] - $subCategory['amount']), 'id=%d',  $previousCategory['id']);
            }
            DB::delete('subCategories', "id=%d", (int)$_GET['removeSubcategory']);
            if (DB::affectedRows() === 1) {
                $alert = '<div class="alert alert-info" role="alert"><p>' . gettext('Unterkategorie entfernt.') . '</p></div>';
            }

            DB::query('UPDATE items SET subcategories=REPLACE(subcategories,",' . (int)$_GET['removeSubcategory']  . ',","," ) ');
        }
    }
}

$headCategories = DB::query('SELECT `id`, `name`, `amount` FROM `headCategories` ORDER BY name ASC');
foreach ($headCategories as $key => $category) {
    if ((int) $category['id'] === 0) {
        unset($headCategories[$key]);
        break;
    }
}

foreach ($headCategories as $key => $category) {
    $positions = DB::queryFirstField('SELECT COUNT(*) FROM `items` WHERE `headcategory`=%d', $category['id']);
    $headCategories[$key]['positions'] = $positions;
}

$subCategories = DB::query('SELECT `id`, `name`, `amount`, `headcategory` FROM `subCategories` ORDER BY name ASC');
foreach ($subCategories as $key => $category) {
    $positions = DB::queryFirstField('SELECT COUNT(*) FROM `items` WHERE `subcategories` LIKE %ss', ',' . $category['id'] . ',');
    $subCategories[$key]['positions'] = $positions;
}

$smarty->assign('alert', $alert);
$smarty->assign("headCategories", $headCategories);
$smarty->assign("subCategories", $subCategories);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->assign('_POST', $_POST);
$smarty->assign('_GET', $_GET);
$smarty->display('categories.tpl');
