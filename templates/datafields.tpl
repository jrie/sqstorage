{include file="head.tpl" title="{t}Datenfelder{/t}"}
{include file="nav.tpl" target="datafields.php" request=$REQUEST}}




    <div id="errorForm" class="alert alert-danger hidden" role="alert">{t}Nicht gespeichert, es befinden sich Fehler in der Formular-Eingabe.{/t}</div>

    <div class="content">
        {if $removedField}
            <div class="statusDisplay green">
                <p>{t}Feld entfernt:{/t} {$POST.fieldName} {if $removedData !== false}{$removedData}{/if}</p>
            </div>
        {elseif $resetEntries !== 0}
            <div class="statusDisplay green">
                <p>{t}Feld Typ geändert:{/t} {$POST.fieldName} {if $resetEntries !== 0}{$resetEntries}{/if}</p>
            </div>
        {else if $addedField !== ''}
            <div class="statusDisplay green">
                <p>{t}Zur Datenbank hinzugefügt:{/t} {$POST.fieldName}</p>
            </div>
        {/if}
        <h5>{t}Auswahl{/t}</h5>
        <form class="form-outline" name="fieldData" method="POST" action="datafields.php">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">{t}Datentyp{/t}</span>
                </div>
                <div class="dropdown float-left">
                    <select name="dataType" autocomplete="off" required class="btn dropdown-toggle switchdatatype" type="button" tabindex="-1" aria-haspopup="true" aria-expanded="false">

                        <option value="-1" selected="selected">{t}Datentyp{/t}</option>
                        {foreach $fieldTypes as $type => $value}
                            <option value="{$type}">{$value}</option>
                        {/foreach}

                    </select>
                </div>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon2">{t}Beispielwerte{/t}</span>
                </div>
                <input type="text" class="form-control example" autocomplete="off" readonly="readonly" placeholder="{t}Datentyp Beispielwerte{/t}" aria-label="{t}Beispielwerte{/t}" aria-describedby="basic-addon2">
            </div>

            <hr>
            </hr>

            <h5>{t}Details{/t}</h5>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3">{t}Feldname{/t}</span>
                </div>
                <input type="text" name="fieldName" required maxlength="63" class="form-control" autocomplete="off" placeholder="{t}Feldname{/t}" aria-label="{t}Feldname{/t}" aria-describedby="basic-addon3">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon4">{t}Standardwert{/t}</span>
                </div>
                <input type="text" name="fieldDefault" data-check="fieldValues" maxlength="511" class="form-control" autocomplete="off" placeholder="{t}Standardwert: Keine Eingabe gleichsam leer{/t}" aria-label="{t}Standardwert{/t}" aria-describedby="basic-addon4">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon5">{t}Feld immer sichtbar?{/t}</span>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="defaultVisible" id="defaultVisible1" value="on" checked>
                    <label class="form-check-label" for="defaultVisible1">{t}Ja{/t}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="defaultVisible" id="defaultVisible2" value="off">
                    <label class="form-check-label" for="defaultVisible2">{t}Nein{/t}</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon6">{t}Sichtbarkeit{/t}</span>
                </div>
                <div class="dropdown float-left">
                    <select name="visibleInCategories" multiple="yes" autocomplete="off" required class="btn dropdown-toggle switchvisiblity" type="button" tabindex="-1" aria-haspopup="true" aria-expanded="false">

                        <option value="-1" selected="selected">{t}Überall sichtbar{/t}</option>
                        {foreach $headCategories as $headCategory}
                            <option value="{$headCategory['id']}">{$headCategory['name']}</option>
                        {/foreach}

                    </select>
                </div>
            </div>


            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon6">{t}Auswahlwerte{/t}</span>
                </div>
                <input type="text" name="fieldValues" requried="required" readonly="readonly" maxlength="1279" class="form-control fieldValues" autocomplete="off" placeholder="{t}Auswahlwerte, durch Semikolon getrennt: 'Neu;Gebraucht;Refurbished' und oder Dezimal und Gleitkommazahlen.{/t}" aria-label="{t}Neu;Gebraucht;Refurbished{/t}" aria-describedby="basic-addon6">
            </div>
            <button type="submit" class="btn btn-primary">{t}Eintragen / Aktualisieren{/t}</button>
            <button type="reset" class="btn btn-secondary">{t}Formular zurücksetzen{/t}</button>
            <button type="button" name="btnDelete" type="button" class="hidden btn btn-danger">{t}Feld löschen{/t}</button>
            <input type="hidden" readonly="readonly" name="existingId" value="-1" />
            <input type="hidden" readonly="readonly" name="doDelete" value="-1" />
        </form>

        <hr>
        </hr>

        <h5>{t}Bestehende Datenfelder{/t}</h5>
        <ul class="existingFields">
            {foreach $customFields as $field}
                <li class="btn-secondary dataField" data-fieldid="{$field.id}" data-values="{$field.fieldValues}" data-default="{$field.default}" data-defaultvisible="{$field.defaultVisible}" data-type="{$field.dataType}" data-name="{$field.label}" data-visiblein="{$field.visibleIn}">{$field.label}</li>
            {/foreach}
        </ul>
    </div>




{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            function setDataExample() {
                let targetValue = document.querySelector('.switchdatatype').value
                let dataExample = dataExamples[targetValue]

                document.querySelector('.example').value = dataExample
                let fieldValues = document.querySelector('.fieldValues')

                if (targetValue.indexOf('selection') == -1) {
                    fieldValues.setAttribute('readonly', 'readonly')
                    fieldValues.value = ''
                } else fieldValues.removeAttribute('readonly')

                resetFields()
            }

            function setVisiblity() {
                let selectedOptions = document.querySelector('.switchvisiblity').selectedOptions
                let optionCount = selectedOptions.length
                if (selectedOptions[0].value === '-1') document.querySelector('input[name="visibleInCategories_input"]').value = ''
                else document.querySelector('input[name="visibleInCategories_input"]').value = optionCount.toString() + ' ' + (optionCount === 1 ? "{/literal}{t}Kategorie{/t}{literal}" : "{/literal}{t}Kategorien{/t}{literal}")
            }

            function checkSubmitData(evt) {
                document.querySelector('#errorForm').classList.add('hidden')
                let inputs = document.querySelectorAll('form[name="fieldData"] > .input-group > input')
                for (let input of inputs) {
                    if (input.getAttribute('readonly') !== null || input.getAttribute('hidden') !== null || input.value.trim() === '') continue

                    if (input.dataset['check'] !== undefined) {
                        input.removeAttribute('title')

                        let checkTarget = document.querySelector('input[name="' + input.dataset['check'] +'"]')
                        if (checkTarget.getAttribute('readonly') !== null) continue
                        let inputEntries = input.value.split(';')
                        let dataEntries = checkTarget.value.split(';')

                        let count = {}
                        let isDuplicated = false
                        let lastError = ''
                        for (let entryInput of inputEntries) {
                            for (let entryInputCheck of inputEntries) {
                                if (entryInput == entryInputCheck) {
                                    count[entryInput] === undefined ? count[entryInput] = 1 : ++count[entryInput]
                                    if (count[entryInput] > 1) {
                                        isDuplicated = true
                                        lastError = entryInput
                                        break
                                    }
                                }
                            }

                            if (isDuplicated) break
                        }

                        if (isDuplicated) {
                            evt.preventDefault()
                            input.setAttribute('title', '"'+ lastError + '" : ' +'{/literal}{t}ist doppelt enthalten.{/t}{literal}')
                            input.classList.add('errorString')
                            document.querySelector('#errorForm').textContent = '"'+ lastError + '" : ' + '{/literal}{t}Standardwert doppelt.{/t}{literal}'
                            document.querySelector('#errorForm').classList.remove('hidden')
                            return false
                        } else input.classList.remove('errorString')

                        let hasFound = true
                        for (let entryInput of inputEntries) {
                            if (dataEntries.indexOf(entryInput) === -1) {
                                hasFound = false;
                                lastError = entryInput
                                break;
                            }
                        }

                        if (!hasFound) {
                            evt.preventDefault()
                            input.setAttribute('title',  '"'+ lastError + '" : ' + '{/literal}{t} nicht in Standardwerten enthalten.{/t}{literal}')
                            input.classList.add('errorString')
                            document.querySelector('#errorForm').textContent =  '"'+ lastError + '" : ' + '{/literal}{t}Standardwert nicht in Auswahlwerten enthalten.{/t}{literal}'
                            document.querySelector('#errorForm').classList.remove('hidden')
                        } else input.classList.remove('errorString')
                    }
                }
            }

            function resetFields() {
                let fields = document.querySelectorAll('input.form-control')
                for (let field of fields) {
                    field.classList.remove('errorString')
                    field.classList.remove('errorInt')
                    field.classList.remove('errorFloat')
                }

                document.querySelector('#errorForm').classList.add('hidden')
            }

            document.querySelector('form[name="fieldData"]').addEventListener('submit', checkSubmitData)
            document.querySelector('form[name="fieldData"]').addEventListener('reset', resetFields)

{/literal}
{$joinedFields = array()}
{foreach $dataExamples as $key => $values}
{assign var="tmpstring" value="'{$key}':'{$values}'"}
{$joinedFields[]=$tmpstring}
{/foreach}
{assign var="implodedFields" value=","|implode:$joinedFields}
{literal}

            let dataExamples = {{/literal}{$implodedFields}{literal}}

            function checkFields(evt) {
                if (document.querySelector('input[name="doDelete"]').value !== '-1') return

                let targetName = document.querySelector('input[name="fieldName"]').value.toString().trim()
                if (isExistingField(targetName)) {
                    if (currentSelection === null || (currentSelection !== null && currentSelection.dataset['name'] !== targetName)) {
                        alert('{/literal}{t}Ein Feld mit gleichem Namem{/t}{literal} "' + targetName + '" {/literal}{t}ist bereits vorhanden. Das alte Feld muß gelöscht oder umbenannt werden.{/t}{literal}')
                        evt.preventDefault()
                        return
                    }
                }

                let dataFields = evt.target.elements;
                let nonValidInputs = {
                    'dataType': ['-1', '{/literal}{t}Datentyp{/t}{literal}', 1, 63],
                    'fieldName': ['', '{/literal}{t}Feldname{/t}{literal}', 1, 63],
                    'fieldDefault': ['', '{/literal}{t}Standardwert{/t}{literal}', 0, 63],
                    'fieldValues': ['', '{/literal}{t}Auswahl{/t}{literal}', 1, 1023]
                }

{/literal}
{$joinedFields = array()}
{foreach $fieldLimits as $key => $values}
{assign var="implodedValues" value=", "|implode:$values}
{assign var="tmpstring" value="'{$key}': [{$implodedValues}]"}
{$joinedFields[]=$tmpstring}
{/foreach}
{assign var="implodedFields" value=","|implode:$joinedFields}
{literal}

                let validData = {{/literal}{$implodedFields}{literal}}

{/literal}
{$joinedFields = array()}
{foreach $fieldConverts as $key => $value}
{assign var="tmpstring" value="'{$key}': '{$value}'"}
{$joinedFields[]=$tmpstring}
{/foreach}
{assign var="fieldConverts" value=","|implode:$joinedFields}
{literal}

                let fieldConverts = {{/literal}{$fieldConverts}{literal}}

                let dataType = null;

                for (let field of dataFields) {
                    let dataValue = ''
                    if (field.nodeName === 'INPUT' && field.getAttribute('readonly') === null) dataValue = field.value.toString().trim()
                    else if (field.nodeName === 'SELECT') dataValue = (field.value).toString().trim()
                    else continue

                    if (fieldConverts[field.value] !== undefined) {
                        if (fieldConverts[field.value] === 'int' || fieldConverts[field.value] === 'float') {
                            let value = document.querySelector('input[name="fieldDefault"]').value
                            if (value.length === 0) continue
                            if (fieldConverts[field.value] === 'int') tmpValue = parseInt(value)
                            else if (fieldConverts[field.value] === 'float') tmpValue = parseFloat(value)

                            if (isNaN(tmpValue) || tmpValue === undefined || tmpValue < validData[dataValue][0] || tmpValue > validData[dataValue][1]) {
                                alert('{/literal}{t}Der Standardwert darf nur zwischen{/t}{literal} ' + validData[field.value][0].toString() + ' {/literal}{t}und{/t}{literal} ' + validData[field.value][1].toString() + ' {/literal}{t}liegen.{/t}{literal}')
                                evt.preventDefault()
                                document.querySelector('input[name="fieldDefault"]').focus()
                                break
                            }
                        }
                    }

                    if (nonValidInputs[field.name] !== undefined) {
                        if (dataValue === nonValidInputs[field.name][0] && dataValue !== '') {
                            alert('{/literal}{t}Feld{/t}{literal} "' + nonValidInputs[field.name][1] + '" {/literal}{t}hat keinen Wert.{/t}{literal}')
                            evt.preventDefault()
                            break;
                        } else if (dataValue.length < nonValidInputs[field.name][2] || dataValue.length > nonValidInputs[field.name][3]) {
                            alert('{/literal}{t}Feld{/t}{literal} "' + nonValidInputs[field.name][1] + '" {/literal}{t}muß zwischen{/t}{literal} ' + nonValidInputs[field.name][2].toString() + ' {/literal}{t}und{/t}{literal} ' + nonValidInputs[field.name][3].toString() + ' {/literal}{t}Zeichen lang sein.{/t}{literal}')
                            evt.preventDefault()
                            break;
                        }
                    }
                }
            }

{/literal}
{$joinedFields = array()}
{foreach $fieldTypesPos as $key => $values}
{assign var="tmpstring" value="{$values}:'{$key}'"}
{$joinedFields[]=$tmpstring}
{/foreach}
{assign var="implodedFields" value=","|implode:$joinedFields}
{literal}

            let fieldTypes = {{/literal}{$implodedFields}{literal}}
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
                if (window.confirm('{/literal}{t}Eintrag{/t}{literal} "' + currentSelection.dataset['name'] + '" {/literal}{t}wirklich löschen?{/t}{literal}')) {
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
                target.value = fieldTypes[parseInt(evt.target.dataset['type'])]
                document.querySelector('input[name="dataType_input"]').value = target.options[target.selectedIndex].innerText
                document.querySelector('input[name="fieldDefault"]').value = evt.target.dataset['default']
                document.querySelector('input[name="fieldName"]').value = evt.target.dataset['name']
                document.querySelector('input[name="fieldDefault"]').value = evt.target.dataset['default']
                document.querySelector('input[name="fieldValues"]').value = evt.target.dataset['values']

                let visibleSelections = evt.target.dataset['visiblein'].split(';')
                let checkOptions =  document.querySelector('.switchvisiblity').parentNode.children[0].children[2].children

                checkOptions[0].children[0].click()
                document.querySelector('input[name="visibleInCategories_input"]').parentNode.children[2].classList.add('hide')
                document.querySelector('input[name="visibleInCategories_input"]').parentNode.children[2].classList.remove('show')

                for (let value of visibleSelections) {
                    if (value === '') continue

                    for (let option of checkOptions) {
                        if (option.getAttribute('value') === value) {
                            if (option.children[0].firstChild.checked === true) continue
                            option.children[0].click()
                            document.querySelector('input[name="visibleInCategories_input"]').parentNode.children[2].classList.add('hide')
                            document.querySelector('input[name="visibleInCategories_input"]').parentNode.children[2].classList.remove('show')
                            break
                        }
                    }
                }

                document.querySelector('input#defaultVisible1').checked = false
                document.querySelector('input#defaultVisible2').checked = false

                if (evt.target.dataset['defaultvisible'] === '1') document.querySelector('input#defaultVisible1').checked = true;
                else document.querySelector('input#defaultVisible2').checked = true;

                setVisiblity()
                setDataExample()
            }

            function isExistingField(targetName) {
                if (document.querySelector('ul.existingFields li[data-name="' + targetName + '"') !== null) return true
                return false
            }

            for (let item of document.querySelectorAll('ul.existingFields li')) item.addEventListener('click', fillData);
            document.querySelector('form[name="fieldData"').addEventListener('submit', checkFields)
            document.querySelector('.switchdatatype').addEventListener('change', setDataExample)
            document.querySelector('.switchvisiblity').addEventListener('change', setVisiblity)
        </script>
{/literal}
{include file="bodyend.tpl"}