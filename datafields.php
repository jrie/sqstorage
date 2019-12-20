<?php
require('login.php');

include_once('customFieldsData.php');
$removedEntry = FALSE;
$removedData = FALSE;
$resetEntries = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('./support/meekrodb.2.3.class.php');
    require_once('./support/dba.php');

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
                if (DB::affectedRows() != 0) $resetEntries = DB::affectedRows() . ' ' . (DB::affectedRows() === 1 ? 'Datensatz' : 'Datensätze') . '  mit verknüpften Altdaten entfernt.';
            } else DB::update('customFields', array('label' => $dataName, 'default' => $dataDefault, 'dataType' => $dataType), 'id=%d', $existingId);
        }
    } else if ($_POST['doDelete'] === '1') {
        DB::delete('customFields', 'id=%d', $existingId);
        if (DB::affectedRows() === 1) {
            $removedEntry = TRUE;
            DB::delete('fieldData', 'fieldId=%d', $existingId);
            if (DB::affectedRows() !== 0) $removedData = 'Es wurden ' . DB::affectedRows() . ' ' . (DB::affectedRows() === 1 ? 'Eintrag' : 'Einträge') . '  verknüpfter Informationen gelöscht.';
            else $removedData = 'Es waren keine Einträge mit dem Datenfeld verknüpft.';
        }
    }
}

?>
<!DOCTYPE html>
<html>
<?php include_once('head.php'); ?>

<body>
    <?php include_once('nav.php'); ?>
    <?php if ($removedEntry) : ?>
        <div class="statusDisplay green">
            <p>Feld &quot;<?php echo $_POST['fieldName'] ?>&quot; entfernt. <?php if ($removedData !== FALSE) echo $removedData; ?></p>
        </div>
    <?php elseif ($resetEntries !== 0) : ?>
        <div class="statusDisplay green">
            <p>Feld &quot;<?php echo $_POST['fieldName'] ?>&quot; Typ geändert. <?php if ($resetEntries !== 0) echo $resetEntries; ?></p>
        </div>
    <?php endif; ?>
    <div class="content">
        <h5>Auswahl</h5>
        <form class="form-outline" name="fieldData" method="POST" action="datafields.php">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?php echo gettext('Datentyp') ?></span>
                </div>
                <div class="dropdown float-left">
                    <select name="dataType" autocomplete="off" required class="btn btn-primary dropdown-toggle switchdatatype" type="button" tabindex="-1" aria-haspopup="true" aria-expanded="false">'
                    <?php
                        echo '<option value="-1" selected="selected">' . gettext('Datentyp') . '</option>';
                        foreach ($fieldTypes as $type => $value) printf('<option value="%s">%s</option>', $type, $value);
                    ?>
                    </select>
                </div>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon2"><?php echo gettext('Beispielwerte') ?></span>
                </div>
                <?php echo '<input type="text" class="form-control example" autocomplete="off" readonly="readonly" placeholder="' . gettext('Datentyp Beispielwerte') . '" aria-label="' . gettext('Beispielwerte') . '" aria-describedby="basic-addon2">'; ?>
            </div>

            <hr>
            </hr>

            <h5><?php echo gettext('Details') ?></h5>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3"><?php echo gettext('Feldname') ?></span>
                </div>
                <?php echo '<input type="text" name="fieldName" maxlength="63" class="form-control" autocomplete="off" placeholder="' . gettext('Feldname') . '" aria-label="' . gettext('Feldname') . '" aria-describedby="basic-addon3">'; ?>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon4"><?php echo gettext('Standardwert') ?></span>
                </div>
                <?php echo '<input type="text" name="fieldDefault" maxlength="63" class="form-control" autocomplete="off" placeholder="' . gettext('Standardwert: Keine Eingabe gleichsam leer') . '" aria-label="' . gettext('Standardwert') . '" aria-describedby="basic-addon4">'; ?>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon6"><?php echo gettext('Auswahlwerte') ?></span>
                </div>
                <?php echo '<input type="text" maxlength="1023" name="fieldValues" requried="required" readonly="readonly" maxlength="1023" class="form-control fieldValues" autocomplete="off" placeholder="' . gettext('Auswahlwerte, durch Komma getrennt: \'\'Neu,Gebraucht,Refurbished\'\' und oder Dezimal und Gleitkommazahlen.') . '" aria-label="' . gettext('Neu,Gebraucht,Refurbished') . '" aria-describedby="basic-addon6">'; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo gettext('Eintragen / Aktualisieren'); ?></button>
            <button type="reset" class="btn btn-secondary"><?php echo gettext('Formular zurücksetzen'); ?></button>
            <button type="button" name="btnDelete" type="button" class="hidden btn btn-danger"><?php echo gettext('Feld löschen'); ?></button>
            <input type="hidden" readonly="readonly" name="existingId" value="-1" />
            <input type="hidden" readonly="readonly" name="doDelete" value="-1" />
        </form>

        <hr>
        </hr>

        <h5>Bestehende Datenfelder</h5>
        <ul class="existingFields">
        <?php
            $customFields = DB::query('SELECT * FROM customFields');
            foreach ($customFields as $field) printf('<li class="btn-secondary dataField" data-fieldid="%d" data-default="%s" data-type="%d" data-name="%s">%s</li>', $field['id'], $field['default'], $field['dataType'], $field['label'], $field['label']);
        ?>
        </ul>

        <?php include_once('footer.php'); ?>

        <script type="text/javascript">
            function setDataExample() {
                let targetValue = document.querySelector('.switchdatatype').value
                let dataExample = dataExamples[targetValue]
                document.querySelector('.example').value = dataExample
                let fieldValues = document.querySelector('.fieldValues')

                if (targetValue.indexOf('selection') == -1) fieldValues.setAttribute('readonly', 'readonly')
                else fieldValues.removeAttribute('readonly')
            }

            let dataExamples = <?php
                $joinedFields = array();
                foreach ($dataExamples as $key => $values) $joinedFields[] = '\'' . $key . '\': \'' . $values . '\'';
                echo '{' . implode(',', $joinedFields) . '}';?>

            function checkFields(evt) {
                if (document.querySelector('input[name="doDelete"]').value !== '-1') return

                let targetName = document.querySelector('input[name="fieldName"]').value.toString().trim()
                if (isExistingField(targetName)) {
                    if (currentSelection === null || (currentSelection !== null && currentSelection.dataset['name'] !== targetName)) {
                        alert('Ein Feld mit gleichem Namem "' + targetName + '" ist bereits vorhanden. Das alte Feld muß gelöscht oder umbenannt werden.')
                        evt.preventDefault()
                        return
                    }
                }

                let dataFields = evt.target.elements;
                let nonValidInputs = {
                    'dataType': ['-1', 'Datentyp', 1, 63],
                    'fieldName': ['', 'Feldname', 1, 63],
                    'fieldDefault': ['', 'Standardwert', 0, 63],
                    'fieldValues': ['', 'Auswahl', 1, 1023]
                }

                let validData = <?php
                    $joinedFields = array();
                    foreach ($fieldLimits as $key => $values) $joinedFields[] = '\'' . $key . '\': [' . implode(', ', $values) . ']';
                    echo '{' . implode(',', $joinedFields) . '}';?>

                let dataType = null;

                for (let field of dataFields) {
                    let dataValue = ''
                    if (field.nodeName === 'INPUT' && field.getAttribute('readonly') === null) dataValue = field.value.toString().trim()
                    else if (field.nodeName === 'SELECT') dataValue = (field.value).toString().trim()
                    else continue

                    if (nonValidInputs[field.name] !== undefined) {
                        if (dataValue === nonValidInputs[field.name][0] && dataValue !== '') {
                            alert('Feld "' + nonValidInputs[field.name][1] + '" hat keinen Wert.')
                            evt.preventDefault()
                            break;
                        } else if (dataValue.length < nonValidInputs[field.name][2] || dataValue.length > nonValidInputs[field.name][3]) {
                            alert('Feld "' + nonValidInputs[field.name][1] + '" muß zwischen ' + nonValidInputs[field.name][2].toString() + ' und ' + nonValidInputs[field.name][3].toString() + ' Zeichen lang sein.')
                            evt.preventDefault()
                            break;
                        }

                        if (field.name === 'dataType') dataType = field.value
                        else if (field.name === 'fieldDefault' && dataValue.length != 0 && dataType !== null) {
                            if (validData[dataType][0] !== '' && (field.value < validData[dataType][0] || field.value > validData[dataType][1])) {
                                alert('Feld "' + nonValidInputs[field.name][1] + '" darf nur zwischen ' + validData[dataType][0].toString() + ' und ' + validData[dataType][1].toString() + ' liegen')
                                evt.preventDefault()
                                break;
                            } else if (validData[dataType][0] === '' && (field.value.toString().trim().length < validData[dataType][1] || field.value.toString().trim().length > validData[dataType][2])) {
                                alert('Feld "' + nonValidInputs[field.name][1] + '" darf nur zwischen ' + validData[dataType][1].toString() + ' und ' + validData[dataType][2].toString() + ' Zeichen lang sein. Aktuelle Zeichen ' + field.value.length)
                                evt.preventDefault()
                                break;
                            }
                        }
                    }
                }
            }

            let fieldTypes = <?php
                $joinedFields = array();
                foreach ($fieldTypesPos as $key => $values) $joinedFields[] = $values . ': \'' . $key . '\'';
                echo '{'. implode(',', $joinedFields) . '}';?>

            let currentSelection = null

            function toggleDeleteButton(state) {
                let target = document.querySelector('form[name="fieldData"] button[name="btnDelete"]')
                if (state) target.classList.remove('hidden');
                else {
                    target.classList.add('hidden');
                    document.querySelector('form[name="fieldData"] input[name="doDelete"]').value = "-1"
                }
            }

            document.querySelector('form[name="fieldData"] button[type="reset"]').addEventListener('click', function() {
                toggleDeleteButton(false)

                if (currentSelection !== null) {
                    currentSelection.classList.add('btn-secondary')
                    currentSelection.classList.remove('btn-primary')
                    currentSelection = null
                    document.querySelector('input[name="existingId"]').value = "-1"
                    document.querySelector('input[name="doDelete"]').value = "-1"
                    document.querySelector('.fieldValues').setAttribute('readonly', 'readonly')
                }
            })

            document.querySelector('button[name="btnDelete"]').addEventListener('click', function() {
                document.querySelector('input[name="doDelete"]').value = "1"
                if (window.confirm('Eintrag "' + currentSelection.dataset['name'] + '" wirklich löschen?')) {
                    document.querySelector('form[name="fieldData"] button[type="submit"]').click()
                }
            })

            function fillData(evt) {
                if (currentSelection !== null) {
                    currentSelection.classList.add('btn-secondary')
                    currentSelection.classList.remove('btn-primary')
                }

                currentSelection = evt.target;
                evt.target.classList.add('btn-primary')
                evt.target.classList.remove('btn-secondary')
                toggleDeleteButton(true)

                document.querySelector('input[name="existingId"]').value = parseInt(evt.target.dataset['fieldid'], 10)

                let target = document.querySelector('select[name="dataType"]')
                target.value = fieldTypes[evt.target.dataset['type']]
                document.querySelector('input[name="dataType"]').value = target.options[target.selectedIndex].innerText
                document.querySelector('input[name="fieldDefault"]').value = evt.target.dataset['default']
                document.querySelector('input[name="fieldName"]').value = evt.target.dataset['name']

                setDataExample()
            }

            function isExistingField(targetName) {
                if (document.querySelector('ul.existingFields li[data-name="' + targetName + '"') !== null) return true
                return false
            }

            for (let item of document.querySelectorAll('ul.existingFields li')) item.addEventListener('click', fillData);
            document.querySelector('form[name="fieldData"').addEventListener('submit', checkFields)
            document.querySelector('.switchdatatype').addEventListener('change', setDataExample)
        </script>
</body>

</html>