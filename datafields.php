<?php
require('login.php');
require_once('support/urlBase.php');
$smarty->assign('urlBase', $urlBase);

require_once('./support/dba.php');
if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
else $smarty->assign('urlPostFix', '.php');

include_once('customFieldsData.php');
$removedField = false;
$removedData = false;
$resetEntries = 0;
$addedField = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['existingId']) || !isset($_POST['fieldName']) || !isset($_POST['dataType']) || !isset($_POST['fieldDefault']) || !isset($_POST['fieldValues']) || !isset($_POST['doDelete'])) {
    die();
  }

  $existingId = intval($_POST['existingId']);

  if ($_POST['doDelete'] === '-1') {
    $dataName = trim($_POST['fieldName']);
    $dataType = $fieldTypesPos[$_POST['dataType']];
    $dataDefault = trim($_POST['fieldDefault']);
    $defaultVisible = $_POST['defaultVisible'] === 'on' ? true : false;
    $visibleIn = ';' . implode(';', $_POST['visibleInCategories']) . ';';
    $dataValues = null;

    if ($_POST['dataType'] === 'selection' || $_POST['dataType'] === 'mselection') $dataValues = $_POST['fieldValues'] . ';';
    if ($dataDefault === '') $dataDefault = null;
    $existingField = null;
    if ($existingId === -1) {
      DB::insert('customFields', array('label' => $dataName, 'default' => $dataDefault, 'dataType' => $dataType, 'defaultVisible' => $defaultVisible, 'visibleIn' => $visibleIn, 'fieldValues' => $dataValues));
      $addedField = $dataName;
    } else {
      $existingField = DB::queryFirstRow('SELECT `id`, `label`, `dataType` FROM `customFields` WHERE `id`=%d', $existingId);
      if ($existingField['dataType'] !== $dataType) DB::update('customFields', array('label' => $dataName, 'default' => $dataDefault, 'dataType' => $dataType, 'defaultVisible' => $defaultVisible, 'visibleIn' => $visibleIn, 'fieldValues' => $dataValues), 'id=%d', $existingField['id']);
      else DB::update('customFields', array('label' => $dataName, 'default' => $dataDefault, 'dataType' => $dataType, 'defaultVisible' => $defaultVisible, 'visibleIn' => $visibleIn, 'fieldValues' => $dataValues), 'id=%d', $existingField['id']);
      if ($existingField !== null && intval($existingField['dataType']) !== intval($dataType)) {
        DB::delete('fieldData', 'fieldId=%d', $existingField['id']);
        if (DB::affectedRows() != 0) $resetEntries = DB::affectedRows() . ' ' . (DB::affectedRows() === 1 ? gettext('1 Datensatz mit verknüpften Altdaten entfernt.' ) : DB::affectedRows() . ' ' . gettext('Datensätze mit verknüpften Altdaten entfernt.'));
      }
    }
  } else if ($_POST['doDelete'] === '1') {
    DB::delete('customFields', 'id=%d', $existingId);
    if (DB::affectedRows() === 1) {
      $removedField = true;
      DB::delete('fieldData', 'fieldId=%d', $existingId);
      if (DB::affectedRows() !== 0) $removedData = DB::affectedRows() . ' ' . (DB::affectedRows() === 1 ? gettext('1 Eintrag wurde gelöscht.') : DB::affectedRows() . ' ' . gettext('Einträge wurden gelöscht.'));
      else $removedData = gettext('Es waren keine Einträge mit dem Datenfeld verknüpft.');
    }
  }
}

$customFields = DB::query('SELECT * FROM customFields');
$smarty->assign('customFields', $customFields);

$headCategories = DB::query('SELECT `id`, `name` FROM headCategories');
$smarty->assign('headCategories', $headCategories);

$smarty->assign('fieldTypesPos', $fieldTypesPos);
$smarty->assign('dataExamples', $dataExamples);
$smarty->assign('fieldLimits', $fieldLimits);
$smarty->assign('fieldTypes', $fieldTypes);
$smarty->assign('fieldConverts', $fieldConverts);
$smarty->assign('resetEntries', $resetEntries);
$smarty->assign('removedField', $removedField);
$smarty->assign('addedField', $addedField);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('REQUEST', $_SERVER['REQUEST_URI']);
$smarty->assign('POST', $_POST);

$smarty->display('datafields.tpl');
