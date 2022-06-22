<?php
include_once('head.php');
include_once('login.php');

if ($useRegistration) {
  if (!isset($user) || !isset($user['usergroupid']) || intval($user['usergroupid']) === 2) {
    $error = gettext('Zugriff verweigert!');
    include('accessdenied.php');
    die();
  }
}

DB::$usenull = false;

$items = DB::query('SELECT `id`, `label`, `subcategories` FROM `items`');

if ($items != null) {
  foreach ($items as $item) {
    $subIds = trim($item['subcategories'], ',');
    if (empty($subIds)) continue;
    $newSubIds = ',' . $subIds . ',';
    DB::update('items', array('subcategories' => $newSubIds), 'id=%d', $item['id']);
    printf('%s - updated...<br/>', $item['label']);
  }
}

echo '<br/><br/>Items update done.';
die();
