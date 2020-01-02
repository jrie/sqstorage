<?php
function addItemStore($item, $storages)
{
  $category = DB::queryFirstRow('SELECT name, amount FROM headCategories WHERE id=%d ORDER BY name ASC', $item['headcategory']);

  $storage = DB::queryFirstRow('SELECT id,label FROM storages WHERE id=%d', $item['storageid']);

  $subcategoriesDB = explode(',', trim($item['subcategories'], ','));
  $subCategories = array();
  foreach ($subcategoriesDB as $sub)
  {
    $subCategory = dB::queryFirstRow('SELECT id, name FROM subCategories WHERE id=%d', intVal($sub));
    $subCategories[] = sprintf('<a href="inventory.php?subcategory=%d">%s</a>', $subCategory['id'], $subCategory['name']);
  }

  printf('<li class="list-group-item"><button class="btn smallButton" name="remove" data-name="%s" value="%d" type="submit"><i class="fa fas fa-times-circle"></i></button><a href="inventory.php?category=%d" class="list-span">%s</a><span class="list-span">%s</span><span class="list-span">%d</span><span class="list-span">%s</span><a class="list-span" href="inventory.php?storageid=%d">%s</a><span class="list-span">%s</span><a class="list-span" href="index.php?editItem=%d"><i class="fa fas fa-edit"></i></a>', $item['label'], $item['id'], $item['headcategory'], $category['name'], $item['label'], $item['amount'], $item['comment'], $storage['id'], $storage['label'], implode(', ', $subCategories) , $item['id']);

  printf('<div class="dropdown float-right"><select autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" value="0" type="button" data-id="%d" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">', $item['id']);
  echo '<option selected="selected" value="-1">Zuweisen</option>';

  foreach ($storages as $storage)
  {
    printf('<option value="%s">%s</option>', $storage['id'], $storage['label']);
  }
  echo '</select></li>';
}

function addItem($item, $storages)
{
  $category = DB::queryFirstRow('SELECT name,amount FROM headCategories WHERE id=%d ORDER BY name ASC', $item['headcategory']);

  $subcategoriesDB = explode(',', $item['subcategories']);
  $subCategories = array();
  foreach ($subcategoriesDB as $sub)
  {
    $subCategory = dB::queryFirstRow('SELECT id, name FROM subCategories WHERE id=%d', intVal($sub));
    if (DB::affectedRows() == 1) $subCategories[] = sprintf('<a href="inventory.php?subcategory=%d">%s</a>', $subCategory['id'], $subCategory['name']);
  }

  printf('<li class="list-group-item"><button class="btn smallButton" name="remove" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><a href="inventory.php?category=%d" class="list-span">%s</a><span class="list-span">%s</span><span class="list-span">%d</span><span class="list-span">%s</span><span class="list-span">%s</span> <span class="list-span">%s</span><a class="list-span" href="index.php?editItem=%d"><i class="fas fa-edit"></i></a>', $item['label'], $item['id'], $item['headcategory'], $category['name'], $item['label'], $item['amount'], $item['comment'], implode(', ', $subCategories) , explode(' ', $item['date']) [0], $item['id']);

  printf('<div class="dropdown float-right"><select autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" data-value="0"  type="button" data-id="%d" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">', $item['id']);
  echo '<option selected="selected" value="-1">' . gettext('Zuweisen') . '</option>';

  foreach ($storages as $storage)
  {
    printf('<option value="%s">%s</option>', $storage['id'], $storage['label']);
  }
  echo '</select></li>';
}

function addHeadColumns($store)
{
  printf('<hr></hr><div class="storage-area"><button class="btn smallButton" name="removeStorage" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><h4 class="text-dark"><a href="inventory.php?storageid=%d">%s</a>&nbsp;<span class="small">(%d %s, %d %s)</span></h4><ul class="list-group">', $store['label'], $store['id'], $store['id'], $store['label'], DB::affectedRows() , DB::affectedRows() == 1 ? getText('Position') : gettext('Positionen') , $store['amount'], $store['amount'] == 1 ? getText('Gegenstand') : gettext('Gegenstände'));
  echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Hinzugefügt') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
}

function addHeadColumnsPositions($store)
{
  printf('<hr></hr><div class="storage-area"><button class="btn smallButton" name="removeStorage" data-name="%s" value="%d" type="submit"><i class="fas fa-times-circle"></i></button><h4 class="text-dark"><a href="inventory.php?storageid=%d">%s</a>&nbsp;<span class="small">(%d %s)</span></h4><ul class="list-group">', $store['label'], $store['id'], $store['id'], $store['label'], DB::affectedRows() , DB::affectedRows() == 1 ? 'Position' : 'Positionen');
  echo '<li class="alert alert-info"><span class="list-span">' . gettext('Gruppe') . '</span><span class="list-span">' . gettext('Bezeichnung') . '</span><span class="list-span">' . gettext('Anzahl') . '</span><span class="list-span">' . gettext('Bemerkung') . '</span><span class="list-span">' . gettext('Unterkategorien') . '</span><span class="list-span">' . gettext('Hinzugefügt') . '</span><span class="list-span">' . gettext('Aktionen') . '</span></li>';
}
