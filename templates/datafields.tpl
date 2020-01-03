{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}



    {if $removedEntry}
        <div class="statusDisplay green">
            <p>Feld &quot;{$POST.fieldName}&quot; entfernt. {if $removedData !== FALSE}{$removedData}{/if}</p>
        </div>
    {elseif $resetEntries !== 0}
        <div class="statusDisplay green">
            <p>Feld &quot;{$POST.fieldName}&quot; Typ geändert. {if $resetEntries !== 0}{$resetEntries}{/if}</p>
        </div>
    {/if}
    
    <div class="content">
        <h5>Auswahl</h5>
        <form class="form-outline" name="fieldData" method="POST" action="datafields.php">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">{t}Datentyp{/t}</span>
                </div>
                <div class="dropdown float-left">
                    <select name="dataType" autocomplete="off" required class="btn btn-primary dropdown-toggle switchdatatype" type="button" tabindex="-1" aria-haspopup="true" aria-expanded="false">
                    
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
                <input type="text" name="fieldName" maxlength="63" class="form-control" autocomplete="off" placeholder="{t}Feldname{/t}" aria-label="{t}Feldname{/t}" aria-describedby="basic-addon3">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon4">{t}Standardwert{/t}</span>
                </div>
                <input type="text" name="fieldDefault" maxlength="63" class="form-control" autocomplete="off" placeholder="{t}Standardwert: Keine Eingabe gleichsam leer{/t}" aria-label="{t}Standardwert{/t}" aria-describedby="basic-addon4">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon6">{t}Auswahlwerte{/t}</span>
                </div>
                <input type="text" maxlength="1023" name="fieldValues" requried="required" readonly="readonly" maxlength="1023" class="form-control fieldValues" autocomplete="off" placeholder="{t}Auswahlwerte, durch Komma getrennt: \'\'Neu,Gebraucht,Refurbished\'\' und oder Dezimal und Gleitkommazahlen.{/t}" aria-label="{t}Neu,Gebraucht,Refurbished{/t}" aria-describedby="basic-addon6">
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
                <li class="btn-secondary dataField" data-fieldid="{$field.id}" data-default="{$field.default}" data-type="{$field.dataType} " data-name="{$field.label}">{$field.label}</li>
            {/foreach}
        </ul>
    </div>




{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            function setDataExample() {
                let targetValue = document.querySelector('.switchdatatype').value
                alert(targetValue)
                let dataExample = dataExamples[targetValue]
                alert(dataExample)
                document.querySelector('.example').value = dataExample
                let fieldValues = document.querySelector('.fieldValues')

                if (targetValue.indexOf('selection') == -1) fieldValues.setAttribute('readonly', 'readonly')
                else fieldValues.removeAttribute('readonly')
            }

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
                    'dataType': ['-1', 'Datentyp', 1, 63],
                    'fieldName': ['', 'Feldname', 1, 63],
                    'fieldDefault': ['', 'Standardwert', 0, 63],
                    'fieldValues': ['', 'Auswahl', 1, 1023]
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
                            alert('{/literal}{t}Feld{/t}{literal} "' + nonValidInputs[field.name][1] + '" {/literal}{t}muß zwischen{/t}{literal} ' + nonValidInputs[field.name][2].toString() + ' {/literal}{t}und{/t}{literal} ' + nonValidInputs[field.name][3].toString() + ' {/literal}{t}Zeichen lang sein.{/t}{literal}')
                            evt.preventDefault()
                            break;
                        }

                        if (field.name === 'dataType') dataType = field.value
                        else if (field.name === 'fieldDefault' && dataValue.length != 0 && dataType !== null) {
                            if (validData[dataType][0] !== '' && (field.value < validData[dataType][0] || field.value > validData[dataType][1])) {
                                alert('{/literal}{t}Feld{/t}{literal} "' + nonValidInputs[field.name][1] + '" {/literal}{t}darf nur zwischen{/t}{literal} ' + validData[dataType][0].toString() + ' {/literal}{t}und{/t}{literal} ' + validData[dataType][1].toString() + ' {/literal}{t}liegen{/t}{literal}')
                                evt.preventDefault()
                                break;
                            } else if (validData[dataType][0] === '' && (field.value.toString().trim().length < validData[dataType][1] || field.value.toString().trim().length > validData[dataType][2])) {
                                alert('{/literal}{t}Feld{/t}{literal} "' + nonValidInputs[field.name][1] + '" {/literal}{t}darf nur zwischen{/t}{literal} ' + validData[dataType][1].toString() + ' {/literal}{t}und{/t}{literal} ' + validData[dataType][2].toString() + ' {/literal}{t}Zeichen lang sein. Aktuelle Zeichen{/t}{literal} ' + field.value.length)
                                evt.preventDefault()
                                break;
                            }
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
                //target.value = fieldTypes[evt.target.dataset['type']]
                target.value = fieldTypes[evt.target.dataset['type']]
                alert(target.value)
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
{/literal}
{include file="bodyend.tpl"}