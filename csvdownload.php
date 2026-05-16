<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once 'login.php';
    include_once './support/dba.php';


    if ($useRegistration) {
        if (!isset($user) || !isset($user['usergroupid']) || (int)$user['usergroupid'] === 2) {
            $error = gettext('Zugriff verweigert!');
            include 'accessdenied.php';
            die();
        }
    }

    $validTables = array('itemsHeadcategory','itemsSubcategory' , 'headCategories', 'subCategories', 'inventory');
    if (isset($_POST['table'])) {
        $targetTable = trim($_POST['table']);
        $targetTableId = isset($_POST['tableid']) ? filter_var($_POST['tableid'], FILTER_SANITIZE_NUMBER_INT) : -1;
        if (!empty($targetTable) && in_array($targetTable, $validTables)) {
            $data = null;
            switch ($targetTable) {
                case 'itemsHeadcategory':
                    if ($targetTableId != -1) {
                        $data = array(
                            array(gettext('Bezeichnung'), gettext('Anzahl'), gettext('Seriennummer'), gettext('Bemerkung'), gettext('Datum'))
                        );
    
                        $items = DB::query('SELECT `label`, `amount`, `serialnumber`, `comment`, `date` FROM `items` WHERE `headcategory`=%d ORDER BY label ASC', $targetTableId);
                        foreach ($items as $item) {
                            $data[] = array_values($item);
                        };
                    }
                    break;
                case 'itemsSubcategory':
                    if ($targetTableId != -1) {
                        $data = array(
                            array(gettext('Bezeichnung'), gettext('Anzahl'), gettext('Seriennummer'), gettext('Bemerkung'), gettext('Datum'))
                        );
    
                        $items = DB::query('SELECT `label`, `amount`, `serialnumber`, `comment`, `date` FROM `items` WHERE `subcategories` LIKE %ss ORDER BY label ASC', ',' . $targetTableId . ',');
                        foreach ($items as $item) {
                            $data[] = array_values($item);
                        };
                    }
                    break;
                case 'headCategories':
                    $data = array(
                        array(gettext('Name'), gettext('Anzahl'), gettext('Positionen'))
                    );

                    $headCategories = DB::query('SELECT `id`, `name`, `amount` FROM `headCategories` ORDER BY name ASC');
                    foreach ($headCategories as $key => $category) {
                        $positions = DB::queryFirstField('SELECT COUNT(*) FROM `items` WHERE `headcategory`=%d', $category['id']);
                        $headCategories[$key][gettext('Name')] = $headCategories[$key]['name'];
                        $headCategories[$key][gettext('Anzahl')] = $headCategories[$key]['amount'];
                        $headCategories[$key][gettext('Positionen')] = $positions;
                        unset($headCategories[$key]['name']);
                        unset($headCategories[$key]['amount']);
                        unset($headCategories[$key]['id']);
                        $data[] = array_values($headCategories[$key]);
                    };
                    break;
                case 'subCategories':
                    $subCategories = DB::query('SELECT `id`, `name`, `amount`, `headcategory` FROM `subCategories` ORDER BY name ASC');
                    $data = array(
                        array(gettext('Name'), gettext('Anzahl'), gettext('Positionen'), gettext('Oberkategorie'))
                    );
                    foreach ($subCategories as $key => $category) {
                        $subCategories[$key]['headcategory'] = DB::queryFirstField('SELECT `name` FROM `headCategories` WHERE `id`=%d', $category['headcategory']);
                        $positions = DB::queryFirstField('SELECT COUNT(*) FROM `items` WHERE `subcategories` LIKE %ss', ',' . $category['id'] . ',');
                        $subCategories[$key][gettext('Name')] = $subCategories[$key]['name'];
                        $subCategories[$key][gettext('Anzahl')] = $subCategories[$key]['amount'];
                        $subCategories[$key][gettext('Positionen')] = $positions;
                        $subCategories[$key][gettext('Oberkategorie')] = $subCategories[$key]['headcategory'];
                        unset($subCategories[$key]['name']);
                        unset($subCategories[$key]['amount']);
                        unset($subCategories[$key]['headcategory']);
                        unset($subCategories[$key]['positions']);
                        unset($subCategories[$key]['id']);
                        $data[] = array_values($subCategories[$key]);
                    }
                    break;
                case 'inventory':
                    $store = DB::queryFirstRow('SELECT * FROM storages WHERE `id`=%d', $targetTableId);
                    unset($store['id']);

                    $storeContent = array();
                    $storeContent['storage'] = $store;
                    $storeContent['positionen'] = 0;
                    $storeContent['itemcount'] = 0;

                    $data = array(
                        array(gettext('Kategorien'), gettext('Bezeichnung'), gettext('Anzahl'), gettext('Bemerkung'), gettext('Unterkategorien'), gettext('Hinzugefügt'))
                    );

                    $items = DB::query('SELECT * FROM items WHERE storageid=%d ORDER BY label ASC', $targetTableId);
                    for ($x = 0; $x < count($items); $x++) {
                        $item = $items[$x];
                        $headCategory = DB::queryFirstRow('SELECT `name` FROM `headCategories` WHERE `id`=%s', $item['headcategory']);
                        $item['headcategory'] = $headCategory['name'];

                        $subCats = explode(',', $item['subcategories']);
                        $subNames = array();
                        foreach ($subCats as $subCat) {
                            $subCategory = DB::queryFirstRow('SELECT `name` FROM `subCategories` WHERE `id`=%d', $subCat);
                            if ($subCategory) {
                                $subNames[]  = $subCategory['name'];
                            }
                        }

                        unset($item['id']);
                        unset($item['serialnumber']);
                        unset($item['storageid']);
                        unset($item['subcategories']);

                        $storeContent['positionen']++;
                        $storeContent['itemcount'] += $item['amount'];

                        $item[gettext('Kategorien')] = $item['headcategory'];
                        unset($item['headcategory']);

                        $item[gettext('Bezeichnung')] = $item['label'];
                        unset($item['label']);

                        $item[gettext('Anzahl')] = $item['amount'];
                        unset($item['amount']);

                        $item[gettext('Bemerkung')] = $item['comment'];
                        unset($item['comment']);

                        $item[gettext('Unterkategorien')] = implode(', ', $subNames);

                        $item[gettext('Hinzugefügt')] = $item['date'];
                        unset($item['date']);

                        $data[] = array_values($item);
                    }
                    break;
                default:
                    break;
                }

                if ($data !== null) {
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment;filename=csvdata.csv');
                    $output = fopen('php://output', 'w');
                    foreach ($data as $row) {
                        fputcsv($output, $row, ',', '"', '\\');
                    };

                    fclose($output);
                }

                exit();
        }
    }

    echo "ERROR";
    exit();
}
