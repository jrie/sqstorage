<?php
require('login.php');

include_once('customFieldsData.php');
$removedEntry = FALSE;
$removedData = FALSE;
$resetEntries = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['existingId']) || !isset($_POST['fieldName']) || !isset($_POST['dataType']) || !isset($_POST['fieldDefault']) || !isset($_POST['fieldValues']) || !isset($_POST['doDelete'])) {
    die();
  }

  $existingId = intval($_POST['existingId']);

  if ($_POST['doDelete'] === '-1') {
    $dataName = trim($_POST['fieldName']);
    $dataType = $fieldTypesPos[$_POST['dataType']];
    $dataDefault = trim($_POST['fieldDefault']);
    if ($dataDefault === '') $dataDefault = NULL;

    $existingField = NULL;
    if ($existingId === -1) {
      $existingField = DB::queryFirstRow('SELECT NULL FROM `customFields` WHERE `label`=%s', $dataName);
      if ($existingField == NULL) DB::insert('customFields', array('label' => $dataName, 'default' => $dataDefault, 'dataType' => $dataType));
    } else {
      $existingField = DB::queryFirstRow('SELECT `label`, `dataType` FROM `customFields` WHERE `label`=%s', $dataName);
      if ($existingField['dataType'] !== $dataType) {
        DB::update('customFields', array('label' => $dataName, 'default' => $dataDefault, 'dataType' => $dataType), 'id=%d', $existingId);
        DB::delete('fieldData', 'fieldId=%d', $existingId);
        if (DB::affectedRows() != 0) $resetEntries = DB::affectedRows() . ' ' . (DB::affectedRows() === 1 ? gettext('Datensatz') : gettext('Datensätze')) . '  ' . gettext('mit verknüpften Altdaten entfernt.');
      } else DB::update('customFields', array('label' => $dataName, 'default' => $dataDefault, 'dataType' => $dataType), 'id=%d', $existingId);
    }
  } else if ($_POST['doDelete'] === '1') {
    DB::delete('customFields', 'id=%d', $existingId);
    if (DB::affectedRows() === 1) {
      $removedEntry = TRUE;
      DB::delete('fieldData', 'fieldId=%d', $existingId);
      if (DB::affectedRows() !== 0) $removedData = gettext('Es wurden') . ' ' . DB::affectedRows() . ' ' . (DB::affectedRows() === 1 ? gettext('Eintrag') : gettext('Einträge')) . '  ' . gettext('verknüpfter Informationen gelöscht.');
      else $removedData = gettext('Es waren keine Einträge mit dem Datenfeld verknüpft.');
    }
  }
}

$customFields = DB::query('SELECT * FROM customFields');
$smarty->assign('customFields', $customFields);

$smarty->assign('fieldTypesPos', $fieldTypesPos);
$smarty->assign('dataExamples', $dataExamples);
$smarty->assign('fieldLimits', $fieldLimits);
$smarty->assign('fieldTypes', $fieldTypes);
$smarty->assign('resetEntries', $resetEntries);
$smarty->assign('removedEntry', $removedEntry);
$smarty->assign('SESSION', $_SESSION);
$smarty->assign('POST', $_POST);

$smarty->display('datafields.tpl');
