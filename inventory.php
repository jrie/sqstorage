<!DOCTYPE html>
<html>
    <?php include_once('head.php'); ?>
    <body>
        <?php include_once('nav.php'); ?>

        <div class="content">
        <?php
            function addItemStore($item, $storages) {
                $category = DB::queryFirstRow('SELECT name,amount FROM headCategories WHERE id=%d ORDER BY name ASC', $item['headcategory']);

                $storage = DB::queryFirstRow('SELECT id,label FROM storages WHERE id=%d', $item['storageid']);

                $subcategoriesDB = explode(',', $item['subcategories']);
                $subCategories= array();
                foreach ($subcategoriesDB as $sub) {
                    $subCategory = dB::queryFirstRow('SELECT id, name FROM subCategories WHERE id=%d', intVal($sub));
                    if (DB::affectedRows() == 1) $subCategories[] = sprintf('<a href="inventory.php?subcategory=%d">%s</a>', $subCategory['id'], $subCategory['name']);
                }

                printf('<li class="list-group-item"><button class="btn smallButton" name="remove" data-name="%s" value="%d" type="submit"><i class="fa fas fa-times-circle"></i></button><a href="inventory.php?category=%d" class="list-span">%s</a><span class="list-span">%s</span><span class="list-span">%d</span><span class="list-span">%s</span><a class="list-span" href="inventory.php?storageid=%d">%s</a><span class="list-span">%s</span><a class="list-span" href="index.php?editItem=%d"><i class="fa fas fa-edit"></i></a>', $item['label'], $item['id'], $item['headcategory'], $category['name'], $item['label'], $item['amount'], $item['comment'], $storage['id'], $storage['label'], implode(', ', $subCategories), $item['id']);

                printf('<div class="dropdown float-right"><select autocomplete="false" class="btn btn-primary dropdown-toggle switchStorage" value="0"  type="button" tabindex="-1" data-id="%d" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">', $item['id']);
                echo '<option selected="selected" value="-1">Zuweisen</option>';

                foreach ($storages as $storage) {
                    printf('<option value="%s">%s</option>', $storage['id'], $storage['label']);
                }
                echo '</select></li>';
            }

            function addItem($item, $storages) {
                $category = DB::queryFirstRow('SELECT name,amount FROM headCategories WHERE id=%d ORDER BY name ASC', $item['headcategory']);

                $subcategoriesDB = explode(',', $item['subcategories']);
                $subCategories= array();
                foreach ($subcategoriesDB as $sub) {
                    $subCategory = dB::queryFirstRow('SELECT id, name FROM subCategories WHERE id=%d', intVal($sub));
                    if (DB::affectedRows() == 1) $subCategories[] = sprintf('<a href="inventory.php?subcategory=%d">%s</a>', $subCategory['id'], $subCategory['name']);
                }

                printf('<li class="list-group-item"><button class="btn smallButton" name="remove" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><a href="inventory.php?category=%d" class="list-span">%s</a><span class="list-span">%s</span><span class="list-span">%d</span><span class="list-span">%s</span><span class="list-span">%s</span> <span class="list-span">%s</span><a class="list-span" href="index.php?editItem=%d"><i class="fas fa-edit"></i></a>', $item['label'], $item['id'], $item['headcategory'], $category['name'], $item['label'], $item['amount'], $item['comment'], implode(', ', $subCategories), explode(' ', $item['date'])[0], $item['id']);

                printf('<div class="dropdown float-right"><select autocomplete="false" class="btn btn-primary dropdown-toggle switchStorage" value="0"  type="button" tabindex="-1" data-id="%d" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">', $item['id']);
                echo '<option selected="selected" value="-1">' . gettext('Zuweisen') . '</option>';

                foreach ($storages as $storage) {
                    printf('<option value="%s">%s</option>', $storage['id'], $storage['label']);
                }
                echo '</select></li>';
            }

            function addHeadColumns($store) {
                printf('<hr></hr><div class="storage-area"><button class="btn smallButton" name="removeStorage" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><h4 class="text-dark"><a href="inventory.php?storageid=%d">%s</a>&nbsp;<span class="small">(%d %s)</span></h4><ul class="list-group">', $store['label'], $store['id'], $store['id'], $store['label'], $store['amount'], $store['amount'] == 1 ? getText('Gegenstand') : gettext('Gegenstände'));
                echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Hinzugefügt') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
            }

            function addHeadColumnsPositions($store) {
                printf('<hr></hr><div class="storage-area"><button class="btn smallButton" name="removeStorage" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><h4 class="text-dark"><a href="inventory.php?storageid=%d">%s</a>&nbsp;<span class="small">(%d %s)</span></h4><ul class="list-group">', $store['label'], $store['id'], $store['id'], $store['label'], DB::affectedRows(), DB::affectedRows() == 1 ? 'Position': 'Positionen');
                echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Hinzugefügt') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
            }




            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['remove']) && !empty($_POST['remove'])) {
                    $item = DB::queryFirstRow('SELECT * FROM items WHERE id=%d', $_POST['remove']);
                    $storage = DB::queryFirstRow('SELECT amount FROM storages WHERE id=%d', $item['storageid']);

                    if (!empty($item['subcategories'])) {
                        foreach (explode(',', $item['subcategories']) as $subCategory) {
                            $subCategoryDB = DB::queryFirstRow('SELECT id, amount FROM subCategories WHERE id=%d', intVal($subCategory));

                            if ($subCategoryDB != NULL) {
                                DB::update('subCategories', array('amount' => intVal($subCategoryDB['amount'])- intVal($item['amount'])), 'id=%d', $subCategoryDB['id']);
                            }
                        }
                    }

                    $headCategory = DB::queryFirstRow('SELECT amount FROM headCategories WHERE id=%d ORDER BY name ASC', $item['headcategory']);
                    DB::update('storages', array('amount' => intVal($storage['amount']) - intVal($item['amount'])), 'id=%d', $item['storageid']);
                    DB::update('headCategories', array('amount' => intVal($headCategory['amount']) - intVal($item['amount'])), 'id=%d', $item['headcategory']);
                    DB::query('DELETE FROM items WHERE id=%d', $_POST['remove']);
                } else if (isset($_POST['removeStorage']) && !empty($_POST['removeStorage'])) {
                    DB::update('items', array('storageid' => NULL), 'storageid=%d', $_POST['removeStorage']);
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

                    if ($items != null) {
                        foreach($items as $item) { addItem($item, $storages); }
                    } else {
                        echo '<li class="list-group-item"><span>Keine Gegenstände gefunden.</span></li>';
                    }

                    echo '</ul></div>';
                } else if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
                    $categoryId = intVal($_GET['subcategory']);
                    $category = DB::queryFirstRow('SELECT id, name, amount from subCategories WHERE id=%d', $categoryId);
                    $items = DB::query('SELECT * FROM items WHERE subcategories LIKE %ss', $categoryId);

                    printf('<div class="storage-area"><ul class="list-group"><h4>%s <small>(%d %s)</small></h4>', $category['name'], DB::affectedRows(), DB::affectedRows() == 1 ? 'Position' : 'Positionen');
                    $storages = DB::query('SELECT id, label FROM storages ORDER BY label ASC');

                    echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Lagerplatz') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
                    if ($items != null) {
                        foreach($items as $item) { addItemStore($item, $storages); }
                    } else {
                        echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                    }

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

                    $storages = DB::query('SELECT id, label, amount FROM storages ORDER BY label ASC', $searchValue);
                    $headCategories = DB::query('SELECT id FROM headCategories WHERE name LIKE %ss ORDER BY name ASC', $searchValue);
                    $subCategories = DB::query('SELECT id FROM subCategories WHERE name LIKE %ss', $searchValue);

                    foreach ($storages as $store) {
                        $hasHeader = FALSE;

                        if ($headCategories != null) {
                            foreach ($headCategories as $headCategory) {
                                $items = DB::query('SELECT * FROM items WHERE storageid=%d AND (headCategory=%d OR label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss OR subcategories LIKE %ss)', $store['id'], $headCategory['id'], $searchValue, $searchValue, $searchValue, $searchValue);
                                if (!$hasHeader) {
                                    addHeadColumnsPositions($store);
                                    $hasHeader = TRUE;
                                }

                                if ($items != null) foreach($items as $item) addItem($item, $storages);
                                else echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                            }
                        } else {
                            foreach ($headCategories as $headCategory) {
                                $items = DB::query('SELECT * FROM items WHERE storageid=%d AND (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss OR subcategories LIKE %ss)', $store['id'], $searchValue, $searchValue, $searchValue, $searchValue);

                                if ($items != null) {
                                    foreach($items as $item) { addItem($item, $storages); }
                                } else {
                                    echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                                }
                            }
                        }

                        if ($subCategories != null) {
                            foreach ($subCategories as $subCategory) {
                                $items = DB::query('SELECT * FROM items WHERE storageid=%d AND (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss OR subcategories LIKE %ss)', $store['id'], $subCategory['id'], $searchValue, $searchValue, $searchValue, $searchValue);
                                if (!$hasHeader) {
                                    addHeadColumnsPositions($store);
                                    $hasHeader = TRUE;
                                }

                                if ($items != null) foreach($items as $item) addItem($item, $storages);
                                else echo '<li class="list-group-item"><span class="list-span">' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                            }
                        }



                        echo '</ul></div>';
                    }
                } else if (isset($_GET['category']) && !empty($_GET['category'])) {
                    $categoryId = $_GET['category'];
                    $category = DB::queryFirstRow('SELECT id, name, amount from headCategories WHERE id=%d', $categoryId);
                    $items = DB::query('SELECT * FROM items WHERE headcategory=%d', $categoryId);

                    printf('<div class="storage-area"><ul class="list-group"><h4>%s <small>(%d Positionen)</small></h4>', $category['name'], DB::affectedRows());
                    $storages = DB::query('SELECT id, label FROM storages ORDER BY label ASC');

                    echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Lagerplatz') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';

                    if ($items != null) {
                        foreach($items as $item) { addItemStore($item, $storages); }
                    } else {
                        echo '<li class="list-group-item"><span>' . gettext('Keine Gegenstände gefunden.') . '</span></li>';
                    }

                    echo '</ul></div>';
                } else {
                    $loseItems = DB::query('SELECT * FROM items WHERE storageid=0');
                    if ($loseItems != NULL) {
                        $storages = DB::query('SELECT id, label FROM storages ORDER BY label ASC');

                        printf('<div class="storage-area"><h4 class="text-dark">Unsortiert <span class="small">(%d %s)</span></h4><ul class="list-group">', DB::affectedRows(), DB::affectedRows() == 1 ? 'Position' : 'Positionen');

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
                    let targetType = evt.target.name === 'removeStorage' ? '<?php echo gettext('Lagerplatz wirklich entfernen?') ?>' : '<?php echo gettext('Position wirklich entfernen?') ?>'
                    if (!window.confirm(targetType + ' "' + evt.target.dataset['name'] + '"')) {
                        evt.preventDefault()
                    }
                })
            }
        </script>
    </body>
</html>