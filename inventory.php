<?php require('login.php');
//-- func +
            function addItemStore($item, $storages) {
                $category = DB::queryFirstRow('SELECT name, amount FROM headCategories WHERE id=%d ORDER BY name ASC', $item['headcategory']);

                $storage = DB::queryFirstRow('SELECT id,label FROM storages WHERE id=%d', $item['storageid']);

                $subcategoriesDB = explode(',', trim($item['subcategories'], ','));
                $subCategories= array();
                foreach ($subcategoriesDB as $sub) {
                    $subCategory = dB::queryFirstRow('SELECT id, name FROM subCategories WHERE id=%d', intVal($sub));
                    $subCategories[] = sprintf('<a href="inventory.php?subcategory=%d">%s</a>', $subCategory['id'], $subCategory['name']);
                }

                printf('<li class="list-group-item"><button class="btn smallButton" name="remove" data-name="%s" value="%d" type="submit"><i class="fa fas fa-times-circle"></i></button><a href="inventory.php?category=%d" class="list-span">%s</a><span class="list-span">%s</span><span class="list-span">%d</span><span class="list-span">%s</span><a class="list-span" href="inventory.php?storageid=%d">%s</a><span class="list-span">%s</span><a class="list-span" href="index.php?editItem=%d"><i class="fa fas fa-edit"></i></a>', $item['label'], $item['id'], $item['headcategory'], $category['name'], $item['label'], $item['amount'], $item['comment'], $storage['id'], $storage['label'], implode(', ', $subCategories), $item['id']);

                printf('<div class="dropdown float-right"><select autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" value="0" type="button" data-id="%d" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">', $item['id']);
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

                printf('<div class="dropdown float-right"><select autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" data-value="0"  type="button" data-id="%d" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">', $item['id']);
                echo '<option selected="selected" value="-1">' . gettext('Zuweisen') . '</option>';

                foreach ($storages as $storage) {
                    printf('<option value="%s">%s</option>', $storage['id'], $storage['label']);
                }
                echo '</select></li>';
            }

            function addHeadColumns($store) {
                printf('<hr></hr><div class="storage-area"><button class="btn smallButton" name="removeStorage" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><h4 class="text-dark"><a href="inventory.php?storageid=%d">%s</a>&nbsp;<span class="small">(%d %s, %d %s)</span></h4><ul class="list-group">', $store['label'], $store['id'], $store['id'], $store['label'], DB::affectedRows(), DB::affectedRows() == 1 ? getText('Position') : gettext('Positionen'), $store['amount'], $store['amount'] == 1 ? getText('Gegenstand') : gettext('Gegenstände'));
                echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Hinzugefügt') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
            }

            function addHeadColumnsPositions($store) {
                printf('<hr></hr><div class="storage-area"><button class="btn smallButton" name="removeStorage" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><h4 class="text-dark"><a href="inventory.php?storageid=%d">%s</a>&nbsp;<span class="small">(%d %s)</span></h4><ul class="list-group">', $store['label'], $store['id'], $store['id'], $store['label'], DB::affectedRows(), DB::affectedRows() == 1 ? 'Position': 'Positionen');
                echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Hinzugefügt') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
            }
//---- func -

$parse['mode'] = "default";
$parse['showemptystorages'] = true;
$sqle =array();
$myitem=array();
//----- P0 + OK
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

//----- P0 - OK
                $success = FALSE;
//----- P1 + OK
                if (isset($_GET['storageid']) && !empty($_GET['storageid']) && !isset($_GET['itemid'])) {
                    $storeId = intVal($_GET['storageid']);
                    $storages = DB::query('SELECT id, label, amount FROM storages ORDER BY label ASC');
                    $store = DB::queryFirstRow('SELECT id, label, amount FROM storages WHERE id=%d', $storeId);
                    $items = DB::query('SELECT * FROM items WHERE storageid=%d', $storeId);

                    
                    $myitem[$storeId]['storage'] = $store;
                    $myitem[$storeId]['positionen']=0;
                    $myitem[$storeId]['itemcount'] = 0;
                    for($x=0;$x<count($items);$x++){
                        $myitem[$storeId]['items'][]=$items[$x];
                        $myitem[$storeId]['positionen']++;
                        $myitem[$storeId]['itemcount'] += $items[$x]['amount'];                               
                    }

//----- P1 - OK
//----- P2 + OK       // SUBCATEGORY
                } else if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
                    $parse['mode'] = "subcategory";
                    $parse['showemptystorages'] = false;
                    $categoryId = intVal($_GET['subcategory']);
                    $category = DB::queryFirstRow('SELECT id, name, amount from subCategories WHERE id=%d', $categoryId);
                    $items = DB::query('SELECT * FROM items WHERE subCategories LIKE %s', ('%,' . $categoryId . ',%'));

                  
                    
                    for($x=0;$x<count($items);$x++){
                        $item = $items[$x];
                        $storeId = 0;
                        $store[$storeId]['id'] = 0;
                        $store[$storeId]['label'] = $category['name'];

                        $myitem[$storeId]['storage'] = $store[$storeId];
                        if(!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen']=0;
                        if(!isset($myitem[$storeId]['itemcount'] )) $myitem[$storeId]['itemcount'] = 0;
                            $myitem[$storeId]['items'][]=$items[$x];
                            $myitem[$storeId]['positionen']++;
                            $myitem[$storeId]['itemcount'] += $items[$x]['amount'];                               
                    }                    


//----- P2 - OK
//----- P3 + OK
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
//----- P3 - OK
//----- P4 + OK       // SEARCH
                } else if (isset($_GET['searchValue']) && !empty($_GET['searchValue'])) {
                    $parse['mode'] = "default";
                    $parse['showemptystorages'] = true;
                    $searchValue = $_GET['searchValue'];
                    $storages = DB::query('SELECT id, label, amount FROM storages');
                    $headCategories = DB::query('SELECT id FROM headCategories WHERE name LIKE %ss',$searchValue);
                    $subCategories = DB::query('SELECT id FROM subCategories WHERE name LIKE %ss',$searchValue);
                    $subCSQL="";
                    for($x=0;$x<count($subCategories);$x++){
                        $subCSQL .= " find_in_set('".$subCategories[$x]['id']."',subcategories) <> 0 OR ";
                    }
                    $sql = "Select * from items WHERE ";
                    if(count($headCategories)>0)    $sql .= " headcategory IN %li OR ";
                    $sql .= $subCSQL;
                    $sql .= " (label LIKE %ss OR comment LIKE %ss OR serialnumber LIKE %ss) ";


                    if (count($headCategories)>0){
                        $items = DB::query($sql, $headCategories, $searchValue,$searchValue,$searchValue );                        
                    }else{
                        $items = DB::query($sql, $searchValue,$searchValue,$searchValue );
                    }

                    $storages = DB::query('SELECT * FROM storages ORDER BY label ASC');
                    for($y=0;$y<count($storages);$y++){
                        $store[$storages[$y]['id']] = $storages[$y];
                    }
                    
                    
                    for($x=0;$x<count($items);$x++){
                        $item = $items[$x];
                        $storeId = $item['storageid'];
                        $myitem[$storeId]['storage'] = $store[$storeId];
                        if(!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen']=0;
                        if(!isset($myitem[$storeId]['itemcount'] )) $myitem[$storeId]['itemcount'] = 0;
                            $myitem[$storeId]['items'][]=$items[$x];
                            $myitem[$storeId]['positionen']++;
                            $myitem[$storeId]['itemcount'] += $items[$x]['amount'];                               
                    }

//----- P4 - OK
//----- P5 +          // Category
                } else if (isset($_GET['category']) && !empty($_GET['category'])) {

                    $parse['mode'] = "category";
                    $parse['showemptystorages'] = false; 
                    $categoryId = intVal($_GET['category']);
                    $category = DB::queryFirstRow('SELECT id, name, amount from headCategories WHERE id=%d', $categoryId);
                    $items = DB::query('SELECT * FROM items WHERE headcategory=%d', $categoryId);
                    
                    for($x=0;$x<count($items);$x++){
                        $item = $items[$x];
                        $storeId = 0;
                        $store[$storeId]['id'] = 0;
                        $store[$storeId]['label'] = $category['name'];

                        $myitem[$storeId]['storage'] = $store[$storeId];
                        if(!isset($myitem[$storeId]['positionen'])) $myitem[$storeId]['positionen']=0;
                        if(!isset($myitem[$storeId]['itemcount'] )) $myitem[$storeId]['itemcount'] = 0;
                            $myitem[$storeId]['items'][]=$items[$x];
                            $myitem[$storeId]['positionen']++;
                            $myitem[$storeId]['itemcount'] += $items[$x]['amount'];                               
                    } 

//----- P5 -
//----- P6 + OK
                } else {
                    $parse['mode'] = "default";
                    $parse['showemptystorages'] = true;
                    //--
                    $storagebyid = array();
                    $myitem=array();
                    $loseItems = DB::query('SELECT * FROM items WHERE storageid=0');
                    if(count($loseItems)> 0) {
                        $myitem[0]['storage']['id'] = "0";
                        $myitem[0]['positionen']=0;
                        $myitem[0]['itemcount'] = 0;
                    }
                    for($x=0;$x<count($loseItems);$x++){

                        $myitem[0]['items'][]=$loseItems[$x];
                        $myitem[0]['positionen']++;
                        $myitem[0]['itemcount'] += $loseItems[$x]['amount'];

                    }
                    $storages = DB::query('SELECT * FROM storages ORDER BY label ASC');
                    foreach ($storages as $store) {
                        $storagebyid[$store['id']] = $store;
                        $myitem[$store['id']]['storage'] = $store;
                        $myitem[$store['id']]['positionen']=0;
                        $myitem[$store['id']]['itemcount'] = 0;                        
                        $items = DB::query('SELECT * FROM items WHERE storageid=%d', $store['id']);
                            for($x=0;$x<count($items);$x++){
                                $myitem[$store['id']]['items'][]=$items[$x];
                                $myitem[$store['id']]['positionen']++;
                                $myitem[$store['id']]['itemcount'] += $items[$x]['amount'];                               
                            }
                    }




                }
//----- P6 - OK


$storagebyid=array();
$storages = DB::query('SELECT id, label FROM storages');
if(!isset($storagebyid)){
    foreach ($storages as $store) {
        $storagebyid[$store['id']] = $store;
    }    
}
$categories = array();
$categoryarray = DB::query('SELECT * FROM headCategories');
for($x=0;$x < count($categoryarray);$x++){
    $tmp = $categoryarray[$x];
    $categories[$tmp['id']] = $tmp;
}

$subcategories = array();
$subarray = DB::query('SELECT * FROM subCategories');
for($x=0;$x<count($subarray);$x++){
    $tmp = $subarray[$x];
    $subcategories[$tmp['id']] = $tmp;
}
if(!isset($items)) $items = array();

//$smarty->assign('dump',print_r(array($sql,$categories,$subcategories,$storages,$myitem,$items),true));
$smarty->assign('dump',print_r(array($myitem,$items),true));

$smarty->assign('storages',$storages);
$smarty->assign('categories',$categories);
$smarty->assign('subcategories',$subcategories);

$smarty->assign('storagebyid',$storagebyid);
$smarty->assign('success', $success);
$smarty->assign('myitem', $myitem);
$smarty->assign('parse',$parse);

/*
$smarty->assign('item', $item);
$smarty->assign('storages', $storages);
$smarty->assign('categories', $categories);
$smarty->assign('subcategories', $subcategories);
if(isset($_POST)) $smarty->assign('POST',$_POST);
*/

    
$smarty->display('inventory.tpl'); 


exit;

