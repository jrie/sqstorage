{include file="head.tpl" title="{t}Eintragen{/t}"}
{include file="nav.tpl" target="index.php" request=$REQUEST}
        {$dataFieldsByKey=null}
        {foreach $fieldTypesPos as $key => $value}
        {$dataFieldsByKey[$value]=$key}
        {/foreach}

        <div class="content">
            {if $success}
            <div class="statusDisplay green" role="alert">
                <p>"{if isset($POST.label)}{$POST.label}{/if}" {t}zur Datenbank hinzugefügt.{/t}</p>
            </div>
            {/if}

            {if $isEdit}
            <div class="alert alert-danger" role="alert">
                <h6>{t}Eintrag zur Bearbeitung:{/t} &quot;{$item.label}&quot;</h6>
            </div>
            {/if}

            <div id="errorForm" class="alert alert-danger hidden" role="alert">{t}Nicht gespeichert, es befinden sich Fehler in der Formular-Eingabe.{/t}</div>

            <form class="inputForm" accept-charset="utf-8" method="POST" action="index.php">

                {if $isEdit}<input type="hidden" value="{$item.id}" name="itemUpdateId" />{/if}

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{t}Bezeichnung{/t}</span>
                    </div>
                {if !$isEdit}
                    <input type="text" name="label" maxlength="64" class="form-control" required="required" placeholder="{t}Bezeichnung oder Name{/t}" aria-label="{t}Bezeichnung{/t}" aria-describedby="basic-addon1">
                {else}
                    <input type="text" name="label" maxlength="64" class="form-control" required="required" placeholder="{t}Bezeichnung oder Name{/t}")aria-label="{t}Bezeichnung{/t}" aria-describedby="basic-addon1" value="{$item.label}">
                {/if}

                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn dropdown-toggle" type="button" tabindex="-1" id="storageDropdown" data-toggle="dropdown" data-nosettitle="true" aria-haspopup="true" aria-expanded="false" autocomplete="off">

                            {if $isEdit && $item.storageid != 0}
                                    <option value="-1">{t}Lagerplatz{/t}</option>
                                {else}
                                    <option value="-1" selected="selected">{t}Lagerplatz{/t}</option>
                            {/if}
                            {foreach $storages as $storage}
                                {if $isEdit && $storage.id == $item.storageid}
                                    {$currentStorage = $storage}
                                    <option value="{$storage.label}" selected="selected">{$storage.label}</option>
                                {else}
                                    <option value="{$storage.label}">{$storage.label}</option>
                                {/if}
                            {{/foreach}}

                            </select>
                        </div>
                    </div>

                {if $isEdit && $item.storageid != 0}
                    <input type="text" name="storage" id="storage" maxlength="64" class="form-control" placeholder="{t}Lagerplatz{/t}" required="required" autocomplete="off" value="{$currentStorage.label}">
                {else}
                    <input type="text" name="storage" id="storage" maxlength="64" class="form-control" placeholder="{t}Lagerplatz{/t}" required="required" autocomplete="off">
                {/if}
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon7">{t}Bemerkung{/t}</span>
                    </div>

                    {if isset($item.comment) && !empty($item.comment)}
                        <input type="text" name="comment" maxlength="255" class="form-control" autocomplete="off" placeholder="{t}Bemerkung{/t}" aria-label="{t}Bemerkung{/t}" aria-describedby="basic-addon7" value="{$item.comment}">
                    {else}
                        <input type="text" name="comment" maxlength="255" class="form-control" autocomplete="off" placeholder="{t}Bemerkung{/t}" aria-label="{t}Bemerkung{/t}" aria-describedby="basic-addon7">
                    {/if}

                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" data-nosettitle="true" type="button" id="categoryDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {if $isEdit}
                                <option value="-1">{t}Kategorie{/t}</option>
                            {else}
                                <option value="-1" selected="selected">{t}Kategorie{/t}</option>
                            {/if}/
                            {foreach $categories as $category}
                            {if $isEdit && $category.id == $item.headcategory}
                                {$currentCategory = $category}
                                <option value="{$category.name}" data-catid="{$category.id}" selected="selected">{$category.name}</option>
                            {else}
                                <option value="{$category.name}" data-catid="{$category.id}">{$category.name}</option>'
                            {/if}
                            {/foreach}
                             </select>
                        </div>
                    </div>
                    {if !$isEdit || $currentCategory == null}
                        <input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="{t}Netzwerk/Hardware{/t}">
                    {else}
                        <input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="{t}Netzwerk/Hardware{/t}" value="{t}{$currentCategory.name}{/t}">
                    {/if}
                </div>


                {foreach $customFields as $field}
                    <div class="customFieldWrapper hidden">
                    {if $field.visibleIn !== ';-1;'}
                        <span class="customFieldTitle">{$field.label}</span>
                        <div class="input-group mb-3 customFields" data-catvisible="{$field.visibleIn}">
                            {$existingData = null}
                            {foreach $customData as $customField}
                                {if $customField['fieldId'] === $field.id}
                                    {$selectType = $dataFieldsByKey[$field.dataType]}
                                    {$existingData = explode(';', $customField[$selectType])}
                                    {break}
                                {/if}
                            {/foreach}

                            {$defaultData = explode(';', $field.default)}
                            {$readonly = ''}
                            {if $field.dataType === '6' || $field.dataType === '7'}
                                {$readonly = ' readonly="readonly"'}
                            <div class="dropdown">
                            {if $field.dataType === '6'}
                                <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="cf{$field.id}" data-default="{$field.default}" data-nosettitle="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {else}
                                <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="cf{$field.id}" data-default="{$field.default}" multiple="multiple" data-nosettitle="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {/if}
                            {$hasData = false}
                            {$options = []}
                            {foreach explode(';', rtrim($field.fieldValues, ';')) as $value}
                                {if $existingData !== null && in_array($value, $existingData)}
                                    {$options[]= "<option value=\"{$value}\" selected=\"selected\">{$value}</option>"}
                                    {$hasData = true}
                                {else if empty($existingData) && in_array($value, $defaultData)}
                                    {$options[]= "<option value=\"{$value}\" selected=\"selected\">{$value}</option>"}
                                    {$hasData = true}
                                {else}
                                    {$options[]= "<option value=\"{$value}\" >{$value}</option>"}
                                {/if}
                            {/foreach}
                            {if $hasData === false && empty($field.default)}
                                <option value="-1" selected="selected" data-default="{$field.default}">{$field.label}</option>
                            {else}
                                <option value="-1" data-default="{$field.default}">{$field.label}</option>
                            {/if}
                            {implode('', $options)}
                            </select>
                                </div>
                            {else}
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon-{$field.id}">{$field.label}</span>
                                </div>
                            {/if}

                            {if !$isEdit}
                                {if empty($field.default)}
                                    <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}">
                                {else}
                                    <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}" value="{$field.default}">
                                {/if}
                            {else if !empty($existingData)}
                                <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}"  value="{implode(';',$existingData)}">
                            {else}
                                <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}" value="{$field.default}">
                            {/if}
                        </div>
                    {/if}
                    </div>
                {/foreach}

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="subcategoryDropdown" multiple="multiple" size="3" data-nosettitle="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {$subCat = array()}
                                    {$subCategories = array()}
                                    {if $isEdit && !empty($item.subcategories)}
                                        <option value="-1">{t}Unterkategorie{/t}</option>
                                        {assign var="subCat" value=","|explode:$item.subcategories}
                                    {else}
                                        <option value="-1" selected="selected">{t}Unterkategorie{/t}</option>
                                    {/if}

                                    {foreach $subcategories as $category}
                                    {if $isEdit && in_array($category.id, $subCat)}
                                        {$subCategories[] = $category.name};
                                        <option selected="selected" value="{$category.name}">{$category.name}</option>
                                    {else}
                                        <option value="{$category.name}">{$category.name}</option>
                                    {/if}
                                    {/foreach}
                            </select>
                        </div>
                    </div>
                    {if !$isEdit || empty($subCategories)}
                        <input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="{t}Router,wlan,fritzBox{/t}" aria-label="{t}Unterkategorie{/t}" autocomplete="off">
                    {else}
                        <input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="{t}Router,wlan,fritzBox{/t}" aria-label="{t}Unterkategorie{/t}" autocomplete="off" value="{','|implode:$subCategories}">
                    {/if}
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon4">{t}Anzahl{/t}</span>
                    </div>
                    {if !$isEdit}
                        <input type="number" maxlength="19" min="1" autocomplete="off" name="amount" required="required" class="form-control" placeholder="1" aria-label="{t}Anzahl{/t}" aria-describedby="basic-addon4">
                    {else}
                        <input type="number" maxlength="19" min="1" autocomplete="off" name="amount" required="required" class="form-control" placeholder="1" aria-label="{t}Anzahl{/t}" aria-describedby="basic-addon4" value="{$item.amount}">
                    {/if}
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon6">{t}Seriennummer{/t}</span>
                    </div>
                    {if !$isEdit}
                        <input type="text" maxlenght="64" name="serialnumber" class="form-control" placeholder="{t}Seriennummer/Artikelnummer{/t}" aria-label="{t}Seriennummer{/t}" aria-describedby="basic-addon6">
                    {else}
                        <input type="text" maxlength="64" name="serialnumber" class="form-control" placeholder="{t}Seriennummer/Artikelnummer{/t}" aria-label="Seriennummer" aria-describedby="basic-addon6" value="{$item.serialnumber}">
                    {/if}
                </div>

                <div class="customFieldWrapper">
                {foreach $customFields as $field}
                    {if $field.defaultVisible === '0' && $field.visibleIn === ';-1;'}
                        <span class="customFieldTitle">{$field.label}</span>
                        <div class="input-group mb-3 customFields">
                            {$existingData = null}
                            {foreach $customData as $customField}
                                {if $customField['fieldId'] === $field.id}
                                    {$selectType = $dataFieldsByKey[$field.dataType]}
                                    {$existingData = explode(';', $customField[$selectType])}
                                    {break}
                                {/if}
                            {/foreach}


                            {$defaultData = explode(';', $field.default)}
                            {$readonly = ''}
                            {if $field.dataType === '6' || $field.dataType === '7'}
                                {$readonly = ' readonly="readonly"'}

                            <div class="dropdown">
                            {if $field.dataType === '6'}
                                <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="cf{$field.id}" data-default="{$field.default}" data-nosettitle="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {else}
                                <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="cf{$field.id}" data-default="{$field.default}" multiple="multiple" data-nosettitle="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {/if}

                            {$hasData = false}
                            {$options = []}
                            {foreach explode(';', rtrim($field.fieldValues, ';')) as $value}
                                {if $existingData !== null && in_array($value, $existingData)}
                                    {$options[]= "<option value=\"{$value}\" selected=\"selected\">{$value}</option>"}
                                    {$hasData = true}
                                {else if empty($existingData) && in_array($value, $defaultData)}
                                    {$options[]= "<option value=\"{$value}\" selected=\"selected\">{$value}</option>"}
                                    {$hasData = true}
                                {else}
                                    {$options[]= "<option value=\"{$value}\" >{$value}</option>"}
                                {/if}
                            {/foreach}
                            {if $hasData === false && empty($field.default)}
                                <option value="-1" selected="selected" data-default="{$field.default}">{$field.label}</option>
                            {else}
                                <option value="-1" data-default="{$field.default}">{$field.label}</option>
                            {/if}
                            {implode('', $options)}

                            </select>
                        </div>
                        {/if}
                        {if !$isEdit || (!empty($field.default) && $existingData === null)}
                            <input type="text" data-type="{$field.dataType}"  name="cfd_{$field.id}" {$readonly} class="form-control" readonly="readonly" placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}" value="{$field.default}">
                        {else if $existingData !== null}
                            <input type="text" data-type="{$field.dataType}"  name="cfd_{$field.id}" {$readonly} class="form-control" readonly="readonly" placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}" value="{implode(';', $existingData)}">
                        {else}
                            <input type="text" data-type="{$field.dataType}"  name="cfd_{$field.id}" {$readonly} class="form-control" readonly="readonly" placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}" value="{$field.label}">
                        {/if}
                        </div>
                    {/if}
                {/foreach}
                </div>

                <div class="customFieldWrapper">
                {foreach $customFields as $field}
                    {if $field.defaultVisible !== '0' && $field.visibleIn === ';-1;'}
                        <span class="customFieldTitle">{$field.label}</span>
                        {$existingData = null}
                        {foreach $customData as $customField}
                            {if $customField['fieldId'] === $field.id}
                                {$selectType = $dataFieldsByKey[$field.dataType]}
                                {$existingData = $customField[$selectType]}
                                {break}
                            {/if}
                        {/foreach}

                        <div class="input-group mb-3 customFields" data-catvisible="{$field.visibleIn}">
                            {$readonly = ''}
                            {if $field.dataType === '6' || $field.dataType === '7'}
                                {$readonly = ' readonly="readonly"'}
                                <div class="dropdown">
                                    <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="cf{$field.id}" data-default="{$field.default}" data-nosettitle="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                    <option value="-1" data-default="{$field.default}">{$field.label}</option>
                                    {foreach explode(';', rtrim($field.fieldValues, ';')) as $value}
                                        {if $value === $field.default}
                                            <option value="{$value}">{$value}</option>'
                                        {else if $value === $existingData}
                                            <option value="{$value}" selected="selected">{$value}</option>'
                                        {else}
                                            <option value="{$value}">{$value}</option>'
                                        {/if}
                                    {/foreach}
                                    </select>
                                </div>
                            {else}
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon-{$field.id}">{$field.label}</span>
                                </div>
                            {/if}

                            {if !$isEdit}
                                {if empty($field.default)}
                                    <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}">
                                {else}
                                    <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}" value="{$field.default}">
                                {/if}
                            {else if !empty($existingData)}
                                <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}"  value="{$existingData}">
                            {else}
                                <input type="text" data-type="{$field.dataType}" name="cfd_{$field.id}" class="form-control" {$readonly} placeholder="{$field.default}" aria-label="{$field.label}" aria-describedby="basic-addon-{$field.id}" value="{$field.default}">
                            {/if}
                        </div>
                    {/if}
                {/foreach}
                </div>

                <div style="float: right;">
                {if $isEdit}
                    <button type="submit" class="btn btn-danger">{t}Überschreiben{/t}</button>
                {else}
                    <button type="submit" class="btn btn-primary">{t}Eintragen{/t}</button>
                {/if}
                </div>

            </form>

            {if $isEdit}
                <h2 class="clearfix">{t}Bilder des Gegenstandes{/t}</h2>
                <form method="POST" accept-charset="utf-8" action="index.php" enctype="multipart/form-data">
                    <input name="images[]" required="required" type="file" multiple="multiple" accept="image/png, image/jpeg, image/jpg, image/webp, image/gif, image/bmp" placeholder="{t}Bild Upload{/t}"/>
                    <input type="hidden" value="{$item.id}" name="editItem" />
                    <input type="submit" class="submit" value="{t}Bilder hochladen{/t}"/>
                </form>
                {if $imageList != null}
                <div class="imageOverlay"><img class="overlayedImaged" /></div>
                <div class="uploadedImages">
                    {foreach $imageList as $image}
                    <div class="imgDiv">
                        <a class="imageLink" data-imageid="{$image['id']}" href="#"><img src="data:image;base64,{$image['thumb']}"/></a>
                        <a class="removeImageOverlay" title="{t}Bild entfernen{/t}" data-imageid="{$image['id']}" href="#"><i class="fas fa-times-circle"></i></a>
                    </div>
                    {/foreach}
                </div>
                {/if}
            {/if}

        </div>

{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            function removeImage(evt) {
                evt.preventDefault()
                if (window.confirm('{/literal}{t}Bild wirklich entfernen?{/t}{literal}')) {
                    let imgContainer = evt.target.parentNode.parentNode
                    let imgRemovalRequest = new XMLHttpRequest()

                    function handleDeleteRequest(evt) {
                        if (evt.target.readyState === 4) {
                            if (evt.target.status === 200) {
                                let responseJson = JSON.parse(evt.target.responseText)
                                if (responseJson['status'] === 'OK') imgContainer.parentNode.removeChild(imgContainer)
                            }
                        }
                    }

                    imgRemovalRequest.addEventListener('readystatechange', handleDeleteRequest)
                    if (evt.target.parentNode.dataset['imageid'] === undefined) imageId = evt.target.dataset['imageid']
                    else imageId = evt.target.parentNode.dataset['imageid']

                    imgRemovalRequest.open("GET", "index.php?removeImageId=" + imageId);
                    imgRemovalRequest.send()
                }
            }

            function openImageLink(evt) {
                evt.preventDefault()

                let imgSrc = evt.target.parentNode
                let imgLoader = new XMLHttpRequest()
                let imgOverlay = document.querySelector('.imageOverlay')

                function handleImgResize(evt) {
                    imgOverlay.style.left = Math.floor((1 - (imgOverlay.clientWidth / window.innerWidth)) * 50) + '%'
                    imgOverlay.style.top = Math.floor((1 - (imgOverlay.clientHeight / window.innerHeight)) * 50) + '%'
                }

                function hidePreview(evt) {
                    imgOverlay.classList.remove('active')
                    imgOverlay.removeEventListener('click', hidePreview)
                    window.removeEventListener('resize', handleImgResize)            
                }

                function handleRequest(evt) {
                    if (evt.target.readyState === 4) {
                        if (evt.target.status === 200) {
                            let responseJson = JSON.parse(evt.target.responseText)
                            if (responseJson['status'] === 'OK') {
                                imgOverlay.children[0].addEventListener('loadend', handleImgResize)
                                window.addEventListener('resize', handleImgResize)
                                imgOverlay.children[0].src = 'data:image;base64,' + responseJson['data']
                                imgOverlay.classList.add('active')
                                imgOverlay.addEventListener('click', hidePreview)
                            }
                        }
                    }
                }

                imgLoader.addEventListener('readystatechange', handleRequest)
                imgLoader.open("GET", "index.php?getImageId=" + evt.target.parentNode.dataset['imageid']);
                imgLoader.send();
            }

            let imageLinks = document.querySelectorAll('.imageLink')
            for (let image of imageLinks) image.addEventListener('click', openImageLink)

            let imageRemoveLinks = document.querySelectorAll('.removeImageOverlay')
            for (let link of imageRemoveLinks) link.addEventListener('click', removeImage)

            function removeInvisibleFields(evt) {
                {/literal}
                {foreach $fieldLimits as $key => $values}
                {assign var="implodedValues" value=", "|implode:$values}
                {assign var="tmpstring" value="'{$key}':[{$implodedValues}]"}
                {$joinedFields[]=$tmpstring}
                {/foreach}
                {assign var="implodedFields" value=","|implode:$joinedFields}

                {$joinedFields=null}
                {foreach $fieldTypesPos as $key => $value}
                {$joinedFields[]="'{$value}':'$key'"}
                {/foreach}
                {assign var="implodedDataFields" value=","|implode:$joinedFields}

                {$joinedFields=null}
                {foreach $dataExamples as $key => $value}
                {$joinedFields[]="'{$key}':'$value'"}
                {/foreach}
                {assign var="implodedExamples" value=","|implode:$joinedFields}
                {literal}

                let validData = {{/literal}{$implodedFields}{literal}}
                let dataTypes = {{/literal}{$implodedDataFields}{literal}}
                let dataExamples = {{/literal}{$implodedExamples}{literal}}

                let testInputs = document.querySelectorAll('.customFieldWrapper input.form-control')
                document.querySelector('#errorForm').classList.add('hidden')

                for (let input of testInputs) {
                    let dataType = dataTypes[input.dataset['type']]
                    if (dataType === 'mselection' || dataType === 'selection' || dataType === 'string') {
                        if (input.value.length < validData[dataType][1] || input.value.length > validData[dataType][2]) {
                            evt.preventDefault()
                            input.setAttribute('title', '{/literal}{t}Fehler{/t}{literal}: ' + dataExamples[dataType])
                            input.classList.add('errorString')
                            document.querySelector('#errorForm').classList.remove('hidden')
                        } else {
                            input.classList.remove('errorString')
                            input.removeAttribute('title')
                        }
                    } else if (dataType.startsWith('int')) {
                        if (parseInt(input.value) < validData[dataType][0] || parseInt(input.value) > validData[dataType][1]) {
                            evt.preventDefault()
                            input.setAttribute('title', '{/literal}{t}Fehler{/t}{literal}: ' + dataExamples[dataType])
                            input.classList.add('errorInt')
                            document.querySelector('#errorForm').classList.remove('hidden')
                        } else {
                            input.classList.remove('errorInt')
                            input.removeAttribute('title')
                        }
                    } else if (dataType.startsWith('float')) {
                        if (parseFloat(input.value) < validData[dataType][0] || parseFloat(input.value) > validData[dataType][1]) {
                            evt.preventDefault()
                            input.setAttribute('title', '{/literal}{t}Fehler{/t}{literal}: ' + dataExamples[dataType])
                            input.classList.add('errorFloat')
                            document.querySelector('#errorForm').classList.remove('hidden')
                        } else {
                            input.classList.remove('errorFloat')
                            input.removeAttribute('title')
                        }
                    }
                }

                let removalInputs = document.querySelectorAll('.customFieldWrapper.hidden input')
                for (let removalItem of removalInputs) removalItem.removeAttribute('name')
            }

            document.querySelector('form.inputForm').addEventListener('submit', removeInvisibleFields)
            document.querySelector('#storageDropdown').addEventListener('change', function(evt) {
                if (evt.target.value === '-1') {
                    document.querySelector('#storage').value = ''
                    return
                }
                document.querySelector('#storage').value = evt.target.value;
            })

            document.querySelector('#subcategoryDropdown').addEventListener('change', function(evt) {
                if (evt.target.value === '-1') {
                    document.querySelector('#subcategory').value = ''
                    return
                } else {
                    let selections = []
                    document.querySelector('#subcategory').value = '';
                    for (let selection of this.selectedOptions) selections.push(selection.value);
                    document.querySelector('#subcategory').value =  selections.join(',');
                }

            })

            document.querySelector('#categoryDropdown').addEventListener('change', function(evt) {
                if (evt.target.value === '-1') {
                    document.querySelector('#category').value = ''
                    let categoryTargetId = -1
                    let dataDivs = document.querySelectorAll('div[data-catvisible]')
                    for (let div of dataDivs) {
                        if (div.dataset['catvisible'] === ';-1;') div.parentNode.classList.remove('hidden')
                        else div.parentNode.classList.add('hidden')
                    }
                    return
                }
                let dataDivs = document.querySelectorAll('div[data-catvisible]')
                let categoryTargetId = evt.target.selectedOptions[0].dataset['catid']

                for (let div of dataDivs) {
                    if (div.dataset['catvisible'] === ';-1;') div.parentNode.classList.remove('hidden')
                    else if (div.dataset['catvisible'].indexOf(';' + categoryTargetId + ';') === -1) div.parentNode.classList.add('hidden')
                    else div.parentNode.classList.remove('hidden')
                }
                document.querySelector('#category').value = evt.target.value;

            })

            let customFields = document.querySelectorAll('.customFields')
            let targetCategory = ";{/literal}{$editCategory}{literal};"

            for (let field of customFields) {
                if (field.dataset['catvisible'] !== undefined) {
                    if (field.dataset['catvisible'].indexOf(targetCategory) !== -1) field.parentNode.classList.remove('hidden')
                    else if (field.dataset['catvisible'] === ';-1;') field.parentNode.classList.remove('hidden')
                    else field.parentNode.classList.add('hidden')
                } else {
                    field.parentNode.classList.remove('hidden')
                }

                let isDropdown = field.children[0].classList.contains('dropdown')
                if (isDropdown) {
                    field.children[0].children[0].addEventListener('change', function(evt) {
                        if (evt.target.getAttribute('multiple') === null) {
                            let targetId = 'cfd_' + evt.target.id.replace('cf', '')
                            if (evt.target.value === '-1') document.querySelector('input[name="'+ targetId + '"]').value = ''
                            else document.querySelector('input[name="'+ targetId + '"]').value = evt.target.value;
                        } else {
                            let targetId = 'cfd_' + evt.target.id.replace('cf', '')

                            let optionsSelected = []
                            for (let option of evt.target.selectedOptions) optionsSelected.push(option.value)

                            if (optionsSelected[0] !== '-1') document.querySelector('input[name="'+ targetId + '"]').value = optionsSelected.join(';')
                            else document.querySelector('input[name="'+ targetId + '"]').value = ''
                        }
                    })
                }
            }
        </script>
{/literal}
{include file="bodyend.tpl"}