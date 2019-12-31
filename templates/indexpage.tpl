{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">
            {if $success}
            <div class="alert alert-info" role="alert">
                <p>{$post.label} {t}zur Datenbank hinzugefügt.{/t}</p>
            </div>
            {/if}

            {if $isEdit }
            <div class="alert alert-danger" role="alert">
                <h6>{t}Eintrag zur Bearbeitung:{/t}&quot;{$item.label}&quot;</h6>
            </div>
            {/if}

            <form accept-charset="utf-8" method="POST" action="index.php">
                {if $isEdit}<input type="hidden" value="{$item.id}" name="itemUpdateId" />{/if}
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{t}Bezeichnung{/t}</span>
                    </div>
                    <input type="text" name="label" maxlength="64" class="form-control" required="required" placeholder="{t}Bezeichnung oder Name{/t}")aria-label="{t}Bezeichnung{/t}" aria-describedby="basic-addon1" {if $isEdit}value="{$item.label}"{/if}>                    
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn btn-secondary dropdown-toggle" type="button" tabindex="-1" id="storageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                                {if  $isEdit and $item.storageid != 0}
                                    <option value="-1">{t}Lagerplatz{/t}</option>
                                {else}
                                    <option value="-1" selected="selected">{t}Lagerplatz{/t}</option>
                                {/if}

                                {foreach $storages as $storage}
                                    {if $isEdit and $storage.id == $item.storageid}
                                        {$currentStorage = $storage}
                                        <option value="{$storage.label}" selected="selected">{$storage.label}</option>
                                    {else}
                                        <option value="{$storage.label}" >{$storage.label}</option>
                                    {/if}

                                {/foreach}
                            </select>
                        </div>
                    </div>

                <input type="text" name="storage" id="storage" maxlength="32" class="form-control" placeholder="{t}Lagerplatz{/t}" required="required" autocomplete="off" {if  $isEdit and $item.storageid != 0}value="{$currentStorage.label}"{/if}>

                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon7">{t}Bemerkung{/t}</span>
                    </div>


                    <input type="text" name="comment" maxlength="255" class="form-control" autocomplete="off" placeholder="{t}Bemerkung{/t}" aria-label="Bemerkung" aria-describedby="basic-addon7" {if isset($item.comment)}value="{$item.comment}"{/if}>

                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn btn-secondary dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="categoryDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {if $isEdit}
                                        <option value="-1">{t}Kategorie{/t}</option>
                                    {else}
                                        <option value="-1" selected="selected">{t}Kategorie{/t}</option>

                                    {/if}
                                    {foreach $categories as $category}
                                        {if $isEdit and $category.id == $item.headcategory}
                                            {$currentCategory = $category}
                                            <option value="{$category.name}" selected="selected">{$category.name}</option>
                                        {else}
                                            <option value="{$category.name}" >{$category.name}</option>
                                        {/if}

                                    {/foreach}


                            </select>
                        </div>
                    </div>
                        {if !$isEdit or $currentCategory == NULL}
                            <input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="{t}Netzwerk/Hardware{/t}">
                        {else}
                            <input type="text" class="form-control" id="category" name="category" required="required" autocomplete="off" placeholder="{t}Netzwerk/Hardware{/t}" value="{$currentCategory.name}">

                        {/if}
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn btn-secondary dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="subcategoryDropdown" multiple="multiple" size="3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {if $isEdit and !empty($item.subcategories)}
                                        {$subCat = explode(',',$item.subcategories)}
                                        <option value="-1">{t}Unterkategorie{/t}</option>
                                    {else}
                                        <option value="-1" selected="selected">{t}Unterkategorie{/t}</option>
                                    {/if}
                                    {$subCategories = array()}
                                    {foreach $categories as $category}
                                        {if $isEdit and in_array($category.id,$subCat)}
                                            {$subCategories[] = $category.name}
                                            <option selected="selected" value="{$category.name}">{$category.name}</option>
                                        {else}
                                            <option value="{$category.name}">{$category.name}</option>
                                        {/if}
                                    {/foreach}  
                            </select>
                        </div>
                    </div>
                    {$subCategories_imploded = implode($subCategories,',')}
                    {if !$isEdit or empty($subCategories_imploded)}
                        <input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="{t}Router,wlan,fritzBox{/t}" aria-label="{t}Unterkategorie{/t}" autocomplete="off">
                    {else}
                        <input type="text" class="form-control" id="subcategory" name="subcategories" placeholder="{t}Router,wlan,fritzBox{/t}" aria-label="{t}Unterkategorie{/t}" autocomplete="off" value="{$subCategories_imploded}">
                    {/if}
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon4">{t}Anzahl{/t}</span>
                    </div>
                        <input type="text" autocomplete="off" name="amount" class="form-control" placeholder="1" aria-label="{t}Anzahl{/t}" aria-describedby="basic-addon4" {if $isEdit}value="{$item.amount}"{/if}>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon6">{t}Seriennummer{/t}</span>
                    </div>
                        <input type="text" name="serialnumber" class="form-control" placeholder="{t}Seriennummer/Artikelnummer{/t}" aria-label="{t}Seriennummer{/t}" aria-describedby="basic-addon6" {if $isEdit}value="{$item.serialnumber}"{/if}>
                </div>

                <div style="float: right;">
                    <button type="submit" class="btn btn-danger">{if $isEdit}{t}Überschreiben{/t}{else}{t}Eintragen{/t}{/if}</button>
                </div>
            </form>
        </div>


{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            document.querySelector('#storageDropdown').addEventListener('change', function(evt) {
                if (parseInt(evt.target.value) === -1) {
                    document.querySelector('#storage').value = ''
                    return
                }
                document.querySelector('#storage').value = evt.target.value;
            })

            document.querySelector('#subcategoryDropdown').addEventListener('change', function(evt) {
                if (parseInt(evt.target.value) === -1) {
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
                if (parseInt(evt.target.value) === -1) {
                    document.querySelector('#category').value = ''
                    return
                }
                document.querySelector('#category').value = evt.target.value;
            })
        </script>
    
{/literal}
{include file="bodyend.tpl"}