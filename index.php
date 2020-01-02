<?php require('login.php'); ?>
<!DOCTYPE html>
<html>
<?php include_once('head.php'); ?>

<body>
    <?php include_once('nav.php'); ?>

    <?php
    $success = FALSE;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $amount = isset($_POST['amount']) && !empty($_POST['amount']) ? $_POST['amount'] : 1;
        $serialNumber = isset($_POST['serialnumber']) && !empty($_POST['serialnumber']) ? $_POST['serialnumber'] : NULL;
        $comment = isset($_POST['comment']) && !empty($_POST['comment']) ? $_POST['comment'] : NULL;
        $subcategories = isset($_POST['subcategories']) && !empty($_POST['subcategories']) ? explode(',', $_POST['subcategories']) : NULL;

        if (isset($_POST['itemUpdateId']) && !empty($_POST['itemUpdateId'])) {
            $existingItem = DB::queryFirstRow('SELECT * FROM items WHERE id=%d', intVal($_POST['itemUpdateId']));

            $category = DB::queryFirstRow('SELECT id,amount FROM headCategories WHERE id=%d', intVal($existingItem['headcategory']));
            DB::update('headCategories', array('amount' => $category['amount'] - intVal($existingItem['amount'])), 'id=%d', $category['id']);

            $exitingSubCategories = explode(',', $existingItem['subcategories']);
            foreach ($exitingSubCategories as $subcategoryId) {
                $subCategory = DB::queryFirstRow('SELECT id, amount FROM subCategories WHERE id=%d', $subcategoryId);
                if ($subCategory !== NULL) {
                    DB::update('subCategories', array('amount' => $subCategory['amount'] - intVal($existingItem['amount'])), 'id=%d', $subCategory['id']);
                }
            }

            $storage = DB::queryFirstRow('SELECT id,label,amount FROM storages WHERE id=%d', $existingItem['storageid']);
            if ($storage != NULL) {
                DB::update('storages', array('amount' => $storage['amount'] - $existingItem['amount']), 'id=%d', $storage['id']);
            }
        }

        $subIds = array();
        if ($subcategories !== NULL) {
            foreach ($subcategories as $subcategory) {
                $subCategory = DB::queryFirstRow('SELECT id, amount FROM subCategories WHERE name=%s', $subcategory);
                if ($subCategory !== NULL) {
                    $subIds[] = $subCategory['id'];
                    DB::update('subCategories', array('amount' => $subCategory['amount'] + $amount), 'id=%d', $subCategory['id']);
                } else {
                    DB::insert('subCategories', array('name' => $subcategory, 'amount' => $amount));
                    $subIds[] = DB::insertId();
                }
            }
        }

        $storage = DB::queryFirstRow('SELECT id,label,amount FROM storages WHERE label=%s', $_POST['storage']);

        if ($storage == NULL) {
            DB::insert('storages', array('label' => $_POST['storage'], 'amount' => $amount));
            $storage['id'] = DB::insertId();
        } else DB::update('storages', array('amount' => $storage['amount'] + $amount), 'id=%d', $storage['id']);

        $category = DB::queryFirstRow('SELECT id,amount FROM headCategories WHERE name=%s', $_POST['category']);
        if ($category == NULL) {
            DB::insert('headCategories', array('name' => $_POST['category'], 'amount' => $amount));
            $category['id'] = DB::insertId();
        } else DB::update('headCategories', array('amount' => $category['amount'] + $amount), 'id=%d', $category['id']);

        if (isset($_POST['itemUpdateId']) && !empty($_POST['itemUpdateId'])) {
            $item = DB::update('items', array('label' => $_POST['label'], 'comment' => $comment, 'serialnumber' => $serialNumber, 'amount' => $amount, 'headcategory' => $category['id'], 'subcategories' => (',' . implode($subIds, ',') . ','), 'storageid' => $storage['id']), 'id=%d', $existingItem['id']);
        } else {
            $item = DB::insert('items', array('label' => $_POST['label'], 'comment' => $comment, 'serialnumber' => $serialNumber, 'amount' => $amount, 'headcategory' => $category['id'], 'subcategories' => (',' . implode($subIds, ',') . ','), 'storageid' => $storage['id']));
        }

        $success = TRUE;
    }

    $isEdit = FALSE;
    if (isset($_GET['editItem']) && !empty($_GET['editItem'])) {
        $item = DB::queryFirstRow('SELECT * from items WHERE id=%d', intVal($_GET['editItem']));
        $isEdit = TRUE;
    }
    ?>

    <div class="content">
        <?php if ($success) : ?>
            <div class="alert alert-info" role="alert">
                <p><?php echo $_POST['label'] . ' ' . gettext('zur Datenbank hinzugefügt.') ?></p>
            </div>
        <?php endif; ?>

        <?php if ($isEdit) : ?>
            <div class="alert alert-danger" role="alert">
                <h6><?php echo gettext('Eintrag zur Bearbeitung:') ?> &quot;<?php echo $item['label'] ?>&quot;</h6>
            </div>
        <?php endif; ?>

        <form accept-charset="utf-8" method="POST" action="index.php">
            <?php
            if ($isEdit) printf('<input type="hidden" value="%d" name="itemUpdateId" />', $item['id']);
            ?>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?php echo gettext('Bezeichnung') ?></span>
                </div>

                <?php
                if (!$isEdit) echo '<input type="text" name="label" maxlength="64" class="form-control" required="required" placeholder="' . gettext('Bezeichnung oder Name') . '" aria-label="' . gettext('Bezeichnung') . '" aria-describedby="basic-addon1">';
                else printf('<input type="text" name="label" maxlength="64" class="form-control" required="required" placeholder="' . gettext('Bezeichnung oder Name') . '")aria-label="' . gettext('Bezeichnung') . '" aria-describedby="basic-addon1" value="%s">', $item['label']);
                ?>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="dropdown">
                        <select class="btn btn-secondary dropdown-toggle" type="button" tabindex="-1" id="storageDropdown" data-toggle="dropdown" data-nosettitle="true" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                            <?php
                            if ($isEdit && $item['storageid'] != 0) echo '<option value="-1">' . gettext('Lagerplatz') . '</option>';
                            else echo '<option value="-1" selected="selected">' . gettext('Lagerplatz') . '</option>';

                            $storages = DB::query('SELECT id, label FROM storages');
                            $currentStorage = NULL;

                            foreach ($storages as $storage) {
                                if ($isEdit && $storage['id'] == $item['storageid']) {
                                    $currentStorage = $storage;
                                    printf('<option value="%s" selected="selected">%s</option>', $storage['label'], $storage['label']);
                                } else {
                                    printf('<option value="%s">%s</option>', $storage['label'], $storage['label']);
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <?php
                if ($isEdit && $item['storageid'] != 0) printf('<input type="text" name="storage" id="storage" maxlength="32" class="form-control" placeholder="' . gettext('Lagerplatz') . '" required="required" autocomplete="off" value="%s">', $currentStorage['label']);
                else echo '<input type="text" name="storage" id="storage" maxlength="32" class="form-control" placeholder="' . gettext('Lagerplatz') . '" required="required" autocomplete="off">';
                ?>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon7"><?php echo gettext('Bemerkung') ?></span>
                </div>
                <?php
                if (isset($item['comment']) && !empty($item['comment']) != NULL) printf('<input type="text" name="comment" maxlength="255" class="form-control" autocomplete="off" placeholder="' . gettext('Bemerkung') . '" aria-label="Bemerkung" aria-describedby="basic-addon7" value="%s">', $item['comment']);
                else echo '<input type="text" name="comment" maxlength="255" class="form-control" autocomplete="off" placeholder="' . gettext('Bemerkung') . '" aria-label="' . gettext('Bemerkung') . '" aria-describedby="basic-addon7">';
                ?>

            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="dropdown">
                        <select class="btn btn-secondary dropdown-toggle" tabindex="-1" autocomplete="off" data-nosettitle="true" type="button" id="categoryDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                            if ($isEdit) {
                                echo '<option value="-1">' . gettext('Kategorie') . '</option>';
                            } else {
                                echo '<option value="-1" selected="selected">' . gettext('Kategorie') . '</option>';
                            }
                            $categories = DB::query('SELECT id, name FROM headCategories');

                            $currentCategory = NULL;
                            foreach ($categories as $category) {
                                if ($isEdit && $category['id'] == $item['headcategory']) {
                                    $currentCategory = $category;
                                    printf('<option value="%s" selected="selected">%s</option>', $category['name'], $category['name']);
                                } else {
                                    printf('<option value="%s">%s</option>', $category['name'], $category['name']);
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                if (!$isEdit || $currentCategory == NULL) {
                    echo '<input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="' . gettext('Netzwerk/Hardware') . '">';
                } else {
                    printf('<input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="%s" value="%s">', gettext('Netzwerk/Hardware'), $currentCategory['name']);
                }
                ?>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="dropdown">
                        <select class="btn btn-secondary dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="subcategoryDropdown" multiple="multiple" size="3" data-nosettitle="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php

                            $subCat = array();
                            if ($isEdit && !empty($item['subcategories'])) {
                                echo '<option value="-1">' . gettext('Unterkategorie') . '</option>';
                                $subCat = explode(',', $item['subcategories']);
                            } else echo '<option value="-1" selected="selected">' . gettext('Unterkategorie') . '</option>';

                            $subCategories = array();
                            $categories = DB::query('SELECT id, name FROM subCategories');
                            foreach ($categories as $category) {
                                if ($isEdit && in_array($category['id'], $subCat)) {
                                    $subCategories[] = $category['name'];
                                    printf('<option selected="selected" value="%s">%s</option>', $category['name'], $category['name']);
                                } else {
                                    printf('<option value="%s">%s</option>', $category['name'], $category['name']);
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                if (!$isEdit || empty($subCategories)) echo '<input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="' . gettext('Router,wlan,fritzBox') . '" aria-label="' . gettext('Unterkategorie') . '" autocomplete="off">';
                else printf('<input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="' . gettext('Router,wlan,fritzBox') . '" aria-label="' . gettext('Unterkategorie') . '" autocomplete="off" value="%s">', implode($subCategories, ','));
                ?>

            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon4"><?php echo gettext('Anzahl') ?></span>
                </div>
                <?php
                if (!$isEdit) echo '<input type="text" autocomplete="off" name="amount" class="form-control" placeholder="1" aria-label="' . gettext('Anzahl') . '" aria-describedby="basic-addon4">';
                else printf('<input type="text" autocomplete="off" name="amount" class="form-control" placeholder="1" aria-label="' . gettext('Anzahl') . '" aria-describedby="basic-addon4" value="%s">', $item['amount']);
                ?>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon6"><?php echo gettext('Seriennummer') ?></span>
                </div>
                <?php
                if (!$isEdit) echo '<input type="text" name="serialnumber" class="form-control" placeholder="Seriennummer/Artikelnummer" aria-label="' . gettext('Seriennummer') . '" aria-describedby="basic-addon6">';
                else printf('<input type="text" name="serialnumber" class="form-control" placeholder="%s" aria-label="Seriennummer" aria-describedby="basic-addon6" value="%s">', gettext('Seriennummer/Artikelnummer'), $item['serialnumber']);
                ?>

            </div>

            <div style="float: right;">
                <?php if ($isEdit) : ?>
                    <button type="submit" class="btn btn-danger"><?php echo gettext('Überschreiben') ?></button>
                <?php else : ?>
                    <button type="submit" class="btn btn-primary"><?php echo gettext('Eintragen') ?></button>
                <?php endif; ?>

            </div>
        </form>
    </div>

    <?php include_once('footer.php'); ?>
    <script type="text/javascript">
        document.querySelector('#storageDropdown').addEventListener('change', function(evt) {
            if (evt.target.value === '-1') {
                document.querySelector('#storage').value = ''
                return
            }
            document.querySelector('#storage').value = evt.target.value;
        })

        document.querySelector('#subcategoryDropdown').addEventListener('change', function(evt) {
            if (evt.target.value === '-1') {
                document.querySelector('#subcategory').value = ''
                return
            } else {
                let selections = []
                document.querySelector('#subcategory').value = '';
                for (let selection of this.selectedOptions) {
                    selections.push(selection.value);
                }
                document.querySelector('#subcategory').value = selections.join(',');
            }

        })



        document.querySelector('#categoryDropdown').addEventListener('change', function(evt) {
            if (evt.target.value === '-1') {
                document.querySelector('#category').value = ''
                return
            }
            document.querySelector('#category').value = evt.target.value;
        })
    </script>
</body>

</html>