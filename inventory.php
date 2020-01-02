<?php require('login.php'); ?>
<!DOCTYPE html>
<html>
    <?php include_once('head.php'); ?>
    <body>
        <?php include_once('nav.php'); ?>

        <div class="content">
        <?php
        require_once('support/utility.php');

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['remove']) && !empty($_POST['remove'])) {
                    $item = DB::queryFirstRow('SELECT * FROM items WHERE id=%d', $_POST['remove']);
                    $storage = DB::queryFirstRow('SELECT amount FROM storages WHERE id=%d', $item['storageid']);

                    if (!empty($item['subcategories'])) {
                        foreach (explode(',', $item['subcategories']) as $subCategory) {
                            $subCategoryDB = DB::queryFirstRow('SELECT id, amount FROM subCategories WHERE id=%d', intVal($subCategory));

                            if ($subCategoryDB != NULL) {
                                DB::update('subCategories', array('amount' => intVal($subCategoryDB['amount']) - intVal($item['amount'])), 'id=%d', $subCategoryDB['id']);
                            }
                        }
                    }

                    $headCategory = DB::queryFirstRow('SELECT amount FROM headCategories WHERE id=%d', $item['headcategory']);
                    DB::update('storages', array('amount' => intVal($storage['amount']) - intVal($item['amount'])), 'id=%d', $item['storageid']);
                    DB::update('headCategories', array('amount' => intVal($headCategory['amount']) - intVal($item['amount'])), 'id=%d', $item['headcategory']);
                    DB::query('DELETE FROM items WHERE id=%d', $_POST['remove']);
                } else if (isset($_POST['removeStorage']) && !empty($_POST['removeStorage'])) {
                    DB::update('items', array('storageid' => 0), 'storageid=%d', $_POST['removeStorage']);
                    DB::query('DELETE FROM storages WHERE id=%d', $_POST['removeStorage']);
                }
            }
            ?>
            <form id="inventoryForm" method="POST" action="inventory.php">
            <?php
                $success = FALSE;
                if (isset($_GET['storageid']) && !empty($_GET['storageid']) && !isset($_GET['itemid'])) {
                    $storeId = intVal($_GET['storageid']);
                    $storages = DB::query('SELECT id, label, amount FROM storages ORDER BY label ASC');
                    $store = DB::queryFirstRow('SELECT id, label, amount FROM storages WHERE id=%d', $storeId);
                    $items = DB::query('SELECT * FROM items WHERE storageid=%d', $storeId);

                    addHeadColumns($store);

                    if ($items != null) foreach($items as $item) { addItem($item, $storages); }
                    else echo '<li class="list-group-item"><span>Keine Gegenstände gefunden.</span></li>';

                    echo '</ul></div>';
                } else if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
                    $categoryId = intVal($_GET['subcategory']);
                    $category = DB::queryFirstRow('SELECT id, name, amount from subCategories WHERE id=%d', $categoryId);
                    $items = DB::query('SELECT * FROM items WHERE subCategories LIKE %s', ('%,' . $categoryId . ',%'));

                    $itemCount = 0;
                    foreach ($items as $item) $itemCount += intVal($item['amount']);

                    printf('<div class="storage-area"><ul class="list-group"><h4>%s <small>(%d %s, %d %s)</small></h4>', $category['name'], DB::affectedRows(), DB::affectedRows() == 1 ? 'Position' : 'Positionen', $itemCount, $itemCount == 1 ? getText('Gegenstand') : gettext('Gegenstände'));
                    $storages = DB::query('SELECT id, label FROM storages ORDER BY label ASC');

                    echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Lagerplatz') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
                    if ($items != null) foreach($items as $item) { addItemStore($item, $storages); }
                    else echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';

                    echo '</ul></div>';
                } else if (isset($_GET['storageid']) && !empty($_GET['storageid']) && isset($_GET['itemid']) && !empty($_GET['itemid'])) {
                    $storeId = intVal($_GET['storageid']);
                    $itemId = intVal($_GET['itemid']);

                    $item = DB::queryFirstRow('SELECT id, amount, storageid FROM items WHERE id=%d', $itemId);
                    if ($item['storageid'] == $storeId) {
                        header("location: inventory.php");
                        die();
                    }

                    if ($storeId != NULL) {
                        $previousStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $item['storageid']);
                        DB::update('storages', array('amount' => intVal($previousStorage['amount']) - intVal($item['amount'])), 'id=%d', $previousStorage['id']);
                    }

                    $storage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $storeId);
                    DB::update('storages', array('amount' => intVal($storage['amount']) + intVal($item['amount'])), 'id=%d', $storage['id']);
                    DB::update('items', array('storageid' => $storage['id']), 'id=%d', $item['id']);
                    header("location: inventory.php");
                    die();
                } else if (isset($_GET['searchValue']) && !empty($_GET['searchValue'])) {
                    $searchValue = $_GET['searchValue'];

                    $storages = DB::query('SELECT id, label, amount FROM storages');
                    $headCategories = DB::query('SELECT id, name FROM headCategories');
                    $subCategories = DB::query('SELECT id, name FROM subCategories');

                    $foundData = FALSE;

                    $existingItemIds = array();
                    foreach ($storages as $store) {
                        $hasHeader = FALSE;
                        $hasItems = FALSE;

                        if ($headCategories != null) {
                            foreach ($headCategories as $headCategory) {
                                if (stripos($headCategory['name'], $searchValue) !== FALSE) $items = DB::query('SELECT * FROM items WHERE storageid=%d', $store['id']);
                                else $items = DB::query('SELECT * FROM items WHERE storageid=%d AND (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss)', $store['id'], $searchValue, $searchValue, $searchValue);

                                if ($items != null) {
                                    if (!$hasHeader) {
                                        addHeadColumnsPositions($store);
                                        $hasHeader = TRUE;
                                    }

                                    foreach($items as $item) if (!in_array($item['id'], $existingItemIds)) {
                                        addItem($item, $storages);
                                        $existingItemIds[] = $item['id'];
                                    }

                                    $hasItems = TRUE;
                                    $foundData = TRUE;
                                }
                            }
                        }

                        if ($subCategories != null) {
                            foreach ($subCategories as $subCategory) {
                                if (stripos($subCategory['name'], $searchValue) !== FALSE) $items = DB::query('SELECT * FROM items WHERE storageid=%d AND subcategories LIKE %s', $store['id'], ('%,' . $subCategory['id'] . ',%'));
                                else $items = DB::query('SELECT * FROM items WHERE storageid=%d AND subcategories LIKE %s AND (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss)', $store['id'], ('%,' . $subCategory['id'] . ',%'), $searchValue, $searchValue, $searchValue, ($searchValue . '%'));

                                if ($items != null) {
                                    if (!$hasHeader) {
                                        addHeadColumnsPositions($store);
                                        $hasHeader = TRUE;
                                    }

                                    foreach($items as $item) if (!in_array($item['id'], $existingItemIds)) {
                                        $existingItemIds[] = $item['id'];
                                        addItem($item, $storages);
                                    }

                                    $hasItems = TRUE;
                                    $foundData = TRUE;
                                }
                            }

                        }

                        if ($foundData) echo '</ul></div>';
                    }

                    if (!$foundData) {
                        echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                        echo '</ul></div>';
                    }

                } else if (isset($_GET['category']) && !empty($_GET['category'])) {
                    $categoryId = intVal($_GET['category']);
                    $category = DB::queryFirstRow('SELECT id, name, amount from headCategories WHERE id=%d', $categoryId);
                    $items = DB::query('SELECT * FROM items WHERE headcategory=%d', $categoryId);

                    $itemCount = 0;
                    foreach ($items as $item) $itemCount += intVal($item['amount']);

                    printf('<div class="storage-area"><ul class="list-group"><h4>%s <small>(%d %s, %d %s)</small></h4>', $category['name'], DB::affectedRows(), DB::affectedRows() == 1 ? getText('Position') : gettext('Positionen'), $itemCount, $itemCount == 1 ? getText('Gegenstand') : gettext('Gegenstände'));
                    $storages = DB::query('SELECT id, label FROM storages');

                    echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Lagerplatz') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';

                    if ($items != null) {
                        foreach($items as $item) { addItemStore($item, $storages); }
                    } else {
                        echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                    }

                    echo '</ul></div>';
                    echo '<hr/><h4>' . gettext('Unterkategorien') . '</h4>';

                    $subCategories = DB::query('SELECT * FROM subCategories WHERE headcategory=%d ORDER BY name ASC', $categoryId);
                    foreach ($subCategories as $subCategory) {
                        $items = DB::query('SELECT * FROM items WHERE subcategories LIKE %s', '%,' . $subCategory['id'] . ',%');

                        $itemCount = 0;
                        foreach ($items as $item) $itemCount += intVal($item['amount']);

                        printf('<div class="storage-area"><ul class="list-group"><h4>%s <small>(%d %s, %d %s)</small></h4>', $subCategory['name'], DB::affectedRows(), DB::affectedRows() == 1 ? getText('Position') : gettext('Positionen'), $itemCount, $itemCount == 1 ? getText('Gegenstand') : gettext('Gegenstände'));
                        $storages = DB::query('SELECT id, label FROM storages');

                        echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Lagerplatz') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';

                        if ($items != null) {
                            foreach($items as $item) { addItemStore($item, $storages); }
                        } else {
                            echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                        }

                        echo '</ul></div>';
                    }
                } else {
                    $loseItems = DB::query('SELECT * FROM items WHERE storageid=0');
                    $count = DB::affectedRows();
                    if ($loseItems != NULL) {
                        $storages = DB::query('SELECT id, label FROM storages ORDER BY label ASC');

                        printf('<div class="storage-area"><h4 class="text-dark">%s <span class="small">(%d %s)</span></h4><ul class="list-group">', gettext('Unsortiert'), $count, $count == 1 ? 'Position' : 'Positionen');

                        echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Hinzugefügt') . '</span></li>';

                        foreach ($loseItems as $item) addItem($item, $storages);
                        echo '</ul></div>';
                    }


                    $storages = DB::query('SELECT id, label, amount FROM storages ORDER BY label ASC');
                    if ($storages == NULL && $loseItems == NULL) {
                        echo '<div class="storage-area"><ul class="list-group"><li class="list-group-item"><span>Keine Gegenstände gefunden.</span></li>';
                    } else {
                        foreach ($storages as $store) {
                            $items = DB::query('SELECT * FROM items WHERE storageid=%d', $store['id']);
                            addHeadColumns($store);

                            if ($items != NULL) {
                                foreach($items as $item) { addItem($item, $storages); }
                            } else {
                                echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                            }
                            echo '</ul></div>';
                        }
                    }
                }
            ?>
            </form>
        </div>

        <?php include_once('footer.php'); ?>
        <script type="text/javascript">
            let switches = document.querySelectorAll('.btn.switchStorage')
            for (let item of switches) {
                item.addEventListener('change', function(evt) {
                    if (evt.target.value === '-1') return
                    window.location.href = 'inventory.php?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id'];
                })
            }

            let removalButtons = document.querySelectorAll('.smallButton')
            for (let button of removalButtons) {
                button.addEventListener('click', function (evt) {
                    let target = evt.target.parentNode
                    let targetType = target.name === 'removeStorage' ? '<?php echo gettext('Lagerplatz wirklich entfernen?') ?>' : '<?php echo gettext('Position wirklich entfernen?') ?>'
                    if (!window.confirm(targetType + ' "' + target.dataset['name'] + '"')) {
                        evt.preventDefault()
                    }
                })
            }
        </script>
    </body>
</html>
