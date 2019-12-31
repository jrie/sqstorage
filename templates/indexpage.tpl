{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">
            {if $success}
            <div class="alert alert-info" role="alert">
                <p>{if isset($POST.label)}{$POST.label}{/if} {t}zur Datenbank hinzugefügt.{/t}</p>
            </div>
            {/if}

            {if $isEdit}
            <div class="alert alert-danger" role="alert">
                <h6>{t}Eintrag zur Bearbeitung:{/t} &quot;{$item.label}&quot;</h6>
            </div>
            {/if}

            <form accept-charset="utf-8" method="POST" action="index.php">
                
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
                            <select class="btn btn-secondary dropdown-toggle" type="button" tabindex="-1" id="storageDropdown" data-toggle="dropdown" data-nosettitle="true" aria-haspopup="true" aria-expanded="false" autocomplete="off">

                                {if $isEdit}
                                    {if $item.storageid != 0}
                                        <option value="-1">{t}Lagerplatz{/t}</option>
                                    {else}
                                        <option value="-1" selected="selected">{t}Lagerplatz{/t}</option>
                                    {/if}
                                {/if}
                                    {foreach $storages as $storage}
                                        {if $isEdit}
                                            {if $storage.id == $item.storageid}
                                                {$currentStorage = $storage}
                                                <option value="{$storage.label}" selected="selected">{$storage.label}</option>
                                            {else}
                                                <option value="{$storage.label}">{$storage.label}</option>
                                            {/if}
                                        {/if}
                                    {{/foreach}}

                            </select>
                        </div>
                    </div>

                                {if $isEdit}
                                    {if $item.storageid != 0}
                                        <input type="text" name="storage" id="storage" maxlength="32" class="form-control" placeholder="{t}Lagerplatz{/t}" required="required" autocomplete="off" value="{$currentStorage.label}">
                                    {else}
                                        <input type="text" name="storage" id="storage" maxlength="32" class="form-control" placeholder="{t}Lagerplatz{/t}" required="required" autocomplete="off">
                                    {/if}
                                {/if}
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon7">{t}Bemerkung{/t}</span>
                    </div>
                    <?php
 ---                       if (isset($item['comment']) && !empty($item['comment']) != NULL) printf('<input type="text" name="comment" maxlength="255" class="form-control" autocomplete="off" placeholder="{t}Bemerkung{/t}" aria-label="{t}Bemerkung{/t}" aria-describedby="basic-addon7" value="%s">', $item['comment']);
  ---                      else echo '<input type="text" name="comment" maxlength="255" class="form-control" autocomplete="off" placeholder="{t}Bemerkung{/t}" aria-label="{t}Bemerkung{/t}" aria-describedby="basic-addon7">';
                    ?>

                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn btn-secondary dropdown-toggle" tabindex="-1" autocomplete="off" data-nosettitle="true" type="button" id="categoryDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {if $isEdit}
                                        <option value="-1">{t}Kategorie{/t}</option>
                                    {else}
                                        <option value="-1" selected="selected">{t}Kategorie{/t}</option>
                                    {/if}
                                    {foreach $categories as $category}
                                        {if $isEdit}
                                            {if $category.id == $item.headcategory}
                                            $currentCategory = $category;
                                                <option value="{$category.name}" selected="selected">{$category.name}</option>
                                            {else}
                                                <option value="{$category.name}">{$category.name}</option>'
                                            {/if}

                                        {/if}    
                                    {/foreach}
                             </select>
                        </div>
                    </div>
                        {if !$isEdit || $currentCategory == NULL}
                            <input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="{t}Netzwerk/Hardware{/t}">
                        {else}
                            <input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="{t}Netzwerk/Hardware{/t}" value="{t}{$currentCategory.name}{/t}">
                        {/if}
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn btn-secondary dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="subcategoryDropdown" multiple="multiple" size="3" data-nosettitle="true"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php

                                    $subCat = array();
                                    if ($isEdit && !empty($item['subcategories'])) {
                                        echo '<option value="-1">{t}Unterkategorie{/t}</option>';
                                        $subCat = explode(',', $item['subcategories']);
                                    } else echo '<option value="-1" selected="selected">{t}Unterkategorie{/t}</option>';

                                    $subCategories = array();
                                    $categories = DB::query('SELECT id, name FROM subCategories');
                                    foreach ($categories as $category) {
                                        if ($isEdit && in_array($category['id'], $subCat)) {
                                            $subCategories[] = $category['name'];
                                            printf('<option selected="selected" value="%s">%s</option>', $category['name'], $category['name']);
                                        } else {
                                            printf('<option value="%s">%s</option>', $category['name'], $category['name']);
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
                        if (!$isEdit || empty($subCategories)) echo '<input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="{t}Router,wlan,fritzBox{/t}" aria-label="{t}Unterkategorie{t/}" autocomplete="off">';
                        else printf('<input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="{t}Router,wlan,fritzBox{/t}" aria-label="{t}Unterkategorie{/t}" autocomplete="off" value="%s">', implode($subCategories, ','));
                    ?>

                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon4">{t}Anzahl{/t}</span>
                    </div>
                    <?php
                        if (!$isEdit) echo '<input type="text" autocomplete="off" name="amount" class="form-control" placeholder="1" aria-label="{t}Anzahl{/t}" aria-describedby="basic-addon4">';
                        else printf('<input type="text" autocomplete="off" name="amount" class="form-control" placeholder="1" aria-label="{t}Anzahl{/t}" aria-describedby="basic-addon4" value="%s">', $item['amount']);
                    ?>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon6">{t}Seriennummer{/t}</span>
                    </div>
                    <?php
                        if (!$isEdit) echo '<input type="text" name="serialnumber" class="form-control" placeholder="{t}Seriennummer/Artikelnummer{/t}" aria-label="{t}Seriennummer{/t}" aria-describedby="basic-addon6">';
                        else printf('<input type="text" name="serialnumber" class="form-control" placeholder="{t}Seriennummer/Artikelnummer{/t}" aria-label="Seriennummer" aria-describedby="basic-addon6" value="%s">',  $item['serialnumber']);
                    ?>

                </div>

                <div style="float: right;">
                <?php if ($isEdit): ?>
                    <button type="submit" class="btn btn-danger">{t}Überschreiben{/t}</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">{t}Eintragen{/t}</button>
                <?php endif; ?>

                </div>
            </form>
        </div>



{include file="footer.tpl"}
{literal}
 
        <script type="text/javascript">
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
                    for (let selection of this.selectedOptions) {
                        selections.push(selection.value);
                    }
                    document.querySelector('#subcategory').value =  selections.join(',');
                }

            })



            document.querySelector('#categoryDropdown').addEventListener('change', function(evt) {
                if (evt.target.value === '-1') {
                    document.querySelector('#category').value = ''
                    return
                }
                document.querySelector('#category').value = evt.target.value;
            })
        </script>
    
{/literal}
{include file="bodyend.tpl"}