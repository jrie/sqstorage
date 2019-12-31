{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">
            <?php

                if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    if (isset($_GET['setCategoryId']) && !empty($_GET['setCategoryId']) && isset($_GET['to']) && !empty($_GET['to'])) {
                        DB::update('subCategories', array('headcategory' => intVal($_GET['to'])), 'id=%d', intVal($_GET['setCategoryId']));
                        header("location: categories.php");
                        die();
                    } else if (isset($_GET['resetSubcategoryId']) && !empty($_GET['resetSubcategoryId'])) {
                        DB::update('subCategories', array('headcategory' => NULL), 'id=%d', intVal($_GET['resetSubcategoryId']));
                        header("location: categories.php");
                        die();
                    } else if (isset($_GET['to']) && !empty($_GET['to']) && (isset($_GET['headCategory']) || isset($_GET['subCategory']))) {
                        if (isset($_GET['headCategory']) && !empty($_GET['headCategory'])) {
                            DB::update('headCategories', array('name' => $_GET['to']), 'id=%d', intVal($_GET['headCategory']));
                            if (DB::affectedRows() === 1) echo '<div class="alert alert-info" role="alert"><p>' . gettext('Kategorie umbenannt.') . '</p></div>';
                        } else {
                            DB::update('subCategories', array('name' => $_GET['to']), 'id=%d', intVal($_GET['subCategory']));
                            if (DB::affectedRows() === 1) echo '<div class="alert alert-info" role="alert"><p>' . gettext('Unterkategorie umbenannt.') . '</p></div>';
                        }
                    } else if (isset($_GET['removeCategory']) && !empty($_GET['removeCategory'])) {
                        DB::delete('headCategories', "id=%d", intVal($_GET['removeCategory']));
                        if (DB::affectedRows() === 1) echo '<div class="alert alert-info" role="alert"><p>' . gettext('Kategorie entfernt.') . '</p></div>';
                    } else if (isset($_GET['removeSubcategory']) && !empty($_GET['removeSubcategory'])) {
                        DB::delete('subCategories', "id=%d", intVal($_GET['removeSubcategory']));
                        if (DB::affectedRows() === 1) echo '<div class="alert alert-info" role="alert"><p>' . gettext('Unterkategorie entfernt.') . '</p></div>';
                    }

                    $headCategories = DB::query('SELECT id, name, amount FROM headCategories ORDER BY name ASC');
                    $subCategories = DB::query('SELECT id, name, amount, headcategory FROM subCategories ORDER BY name ASC');

                    echo '<hr/><ul class="categories list-group"><li class="alert alert-info"><span class="list-span">' .  gettext('Kategorien') . '</span><span class="list-span">' .  gettext('Anzahl') . '</span><span class="list-span">' .  gettext('Aktionen') . '</span></li>';
                    foreach ($headCategories as $category) {
                        printf('<li class="list-group-item"><a name="removeCategory" tabindex="-1" data-name="%s" href="categories.php?removeCategory=%d" class="removalButton fas fa-times-circle btn"></a><a class="list-span" data-name="%s" href="inventory.php?category=%d">%s</a><span class="list-span">%d %s</span><a class="fas fa-edit editCategory" href="#" name="editCategory" data-name="%s" data-id="%d"></a></li>', $category['name'], $category['id'], $category['name'], $category['id'], $category['name'], $category['amount'], $category['amount'] == 1 ? gettext('Gegenstand') : gettext('Gegenstände'), $category['name'], $category['id']);
                    }
                    echo '</ul><hr/>';

                    echo '<ul class="categories list-group"><li class="alert alert-info"><span class="list-span">' .  gettext('Unterkategorien') . '</span><span class="list-span">' .  gettext('Anzahl') . '</span><span class="list-span">' .  gettext('Aktionen') . '</span><span class="list-span">' . gettext('Oberkategorie') . '</span></li>';

                    foreach ($subCategories as $category) {
                        printf('<li class="list-group-item"><a name="removeSubcategory" tabindex="-1" data-name="%s" href="categories.php?removeSubcategory=%d" class="removalButton fas fa-times-circle btn"></a><a class="list-span" data-name="%s" href="inventory.php?subcategory=%d">%s</a><span class="list-span">%d %s</span><a class="fas fa-edit editCategory" href="#" name="editSubcategory" data-name="%s" data-id="%d"></a>', $category['name'], $category['id'], $category['name'], $category['id'], $category['name'], $category['amount'], $category['amount'] == 1 ? gettext('Gegenstand') : gettext('Gegenstände'), $category['name'], $category['id']);
                        ?>
                        <div class="dropdown list-span">
                            <select class="btn btn-secondary dropdown-toggle categoryDropdowns" type="button" data-originid="<?php echo $category['id'] ?>" tabindex="-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                                <?php
                                    if ($category['headcategory'] != 0) echo '<option value="-1">' . gettext('Keine') . '</option>';
                                    else echo '<option value="-1" selected="selected">' . gettext('Keine') . '</option>';
                                    foreach ($headCategories as $headCategory) {
                                        if ($headCategory['id'] == $category['headcategory']) {
                                            printf('<option value="%d" selected="selected">%s</option>', $headCategory['id'], $headCategory['name']);
                                        } else {
                                            printf('<option value="%d">%s</option>', $headCategory['id'], $headCategory['name']);
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                    <?php
                        echo '</li>';
                    }

                    echo '</ul>';
                }
            ?>
        </div>


{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            let removalButtons = document.querySelectorAll('.removalButton')
            for (let button of removalButtons) {
                button.addEventListener('click', function (evt) {
                    let targetType = evt.target.name === 'removeCategory' ? '<?php echo gettext('Kategorie wirklich entfernen?') ?>' : '<?php echo gettext('Unterkategorie wirklich entfernen?') ?>'
                    if (!window.confirm(targetType + ' "' + evt.target.dataset['name'] +'"')) {
                        evt.preventDefault()
                    }
                })
            }

            let editCategoryButtons = document.querySelectorAll('.editCategory')
            for (let button of editCategoryButtons) {
                button.addEventListener('click', function (evt) {
                    let targetType = evt.target.name === 'editCategory' ? '<?php echo gettext('Kategorie umbenennen?') ?>' : '<?php echo gettext('Unterkategorie umbenennen?') ?>'
                    let newName = window.prompt(targetType + ' "' + evt.target.dataset['name'] + '"', '')

                    if (newName !== null && newName.length !== 0) {
                        if (evt.target.name === 'editCategory') window.location.href = 'categories.php?headCategory=' + evt.target.dataset['id'] + '&to=' + encodeURIComponent(newName)
                        else window.location.href = 'categories.php?subCategory=' + evt.target.dataset['id'] + '&to=' + encodeURIComponent(newName)
                    }

                    return false
                })
            }

            let categoryDropdowns = document.querySelectorAll('.categoryDropdowns')
            for (let dropDown of categoryDropdowns) {
                dropDown.addEventListener('change', function (evt) {
                    let subcategoryId = evt.target.dataset['originid']
                    if (evt.target.value === '-1') {
                        window.location.href = 'categories.php?resetSubcategoryId=' + subcategoryId
                        return
                    }

                    window.location.href = 'categories.php?setCategoryId=' + subcategoryId + '&to=' + evt.target.value
                })
            }
        </script>   
{/literal}
{include file="bodyend.tpl"}