{include file="head.tpl" title="{t}Inventar{/t}"}
{include file="nav.tpl" target="inventory.php" request=$REQUEST}

{$hasdata=false}
{$selectid=0}

{if $parse.mode == "default"}
<div class="content {if $isGuest}roleguest{/if}">
    <form id="inventoryForm" method="POST" action="{$urlBase}/inventory{$urlPostFix}">
        {foreach $myitem as $itemstore}
        {$hasdata=true}
        {if $parse.showemptystorages || $itemstore.itemcount > 0 }
        <hr>
        <div class="storage-area">
            {if !$isGuest}
            <button title="{t}Lagerplatz löschen{/t}" class="btn smallButton" name="removeStorage" data-name="{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}" value="{$itemstore.storage.id}" type="submit"><i class="fas fa-times-circle"></i></button>
            {/if}
            <h4 class="text-dark">
                <a href="{$urlBase}/inventory{$urlPostFix}?storageid={$itemstore.storage.id}">{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}</a>&nbsp;
                {if !$isGuest}{if isset($itemstore.storage.id)}<a title="{t}Schnelle Bearbeitung{/t}" onclick="changeSingleValue('storages','label',{$itemstore.storage.id},true);" href="javascript:void(0)"><i class="fas fa-edit fa-xs"></i></a>{/if}{/if}
                <span class="small">({$itemstore.positionen} {if $itemstore.positionen == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}, {$itemstore.itemcount} {if $itemstore.itemcount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if})</span>
            </h4>

            <ul class="list-group">
                <li class="alert alert-info">
                    <span class="list-span">{t}Kategorien{/t}</span>
                    <span class="list-span">{t}Bezeichnung{/t}</span>
                    <span class="list-span">{t}Anzahl{/t}</span>
                    <span class="list-span">{t}Bemerkung{/t}</span>
                    <span class="list-span">{t}Unterkategorien{/t}</span>
                    <span class="list-span">{t}Hinzugefügt{/t}</span>
                    {if !$isGuest}
                    <span class="list-span">{t}Aktionen{/t}</span>
                    <span class="list-span">{t}Zuweisen{/t}</span>
                    {else}
                    <span class="list-span"></span>
                    <span class="list-span"></span>
                    {/if}
                </li>

                {if isset($itemstore.items)}
                {foreach $itemstore.items as $item}

                {assign var="subCats" value=","|explode:$item.subcategories}
                {$subCategories=array()}
                {foreach $subCats as $subCat}
                {if isset($subcategories.$subCat)}
                {$subCategories[] ="<a href='{$urlBase}/inventory{$urlPostFix}?subcategory={$subcategories.$subCat.id}'>{$subcategories.$subCat.name}</a>"}
                {/if}
                {/foreach}

                {assign var="implodedSubCats" value=", "|implode:$subCategories}
                {assign var="dateexploded" value=" "|explode:$item.date}
                {assign var="catid" value=$item.headcategory}
                {assign var="category" value=$categories.$catid}
                <li class="list-group-item" data-id="{$item.id}">
                    {if !$isGuest}
                    <button title="{t}Position löschen{/t}" class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                    {else}
                    <div class="list-span"></div>
                    {/if}

                    <a href="{$urlBase}/inventory{$urlPostFix}?category={$item.headcategory}" class="list-span">{$category.name}</a>
                    <div class="list-span"><span class="listing-hasimages">{if isset($item.hasImages) && $item.hasImages}<i title="{t}Gegenstand hat Bilder{/t}" class="picture fas fa-images"></i><img class="item-picture" data-id="{$item.id}" src="data:image/png;base64,{$item.thumb}">{/if}
                            <a class="listing-label quick-edit" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}">{$item.label}</a></span></div>
                    <div class="list-span"><span class="listing-amount quick-edit">{$item.amount}</span></div>
                    <div class="list-span"><span class="listing-comment quick-edit">{$item.comment}</span></div>

                    <div class="list-span"><span class="listing-subcategories">{$implodedSubCats}</span></div>
                    <div class="list-span"><span class="listing-dateadded">{$dateexploded.0}</span></div>
                    {if !$isGuest}
                    <a tabindex="-1" href="#" class="save-inline-edit inactive" title="{t}Schnelle Bearbeitung speichern{/t}" data-id="{$item.id}"><i class="fas fa-floppy-disk"></i></a>
                    <a tabindex="-1" href="#" class="open-inline-edit" title="{t}Schnelle Bearbeitung{/t}" data-id="{$item.id}"><i class="fas fa-eraser"></i></a>
                    <a title="{t}Ausführliche Bearbeitung{/t}" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}"><i class="fas fa-edit"></i></a>
                    {/if}

                    {if !$isGuest}
                    <div class="dropdown float-right">
                        <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage listing-switchstorage" data-itemamount="{$item.amount}" data-value="0" data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {$hasStorage = false}
                            {foreach $storages as $storage}
                            {if ($storage.id == $item.storageid)}
                            {$hasStorage = true}
                            <option selected="selected" value="-1">{$storage.label}</option>
                            {break}
                            {/if}
                            {/foreach}

                            {if !$hasStorage}
                            <option selected="selected" value="-1">{t}Zuweisen{/t}</option>
                            {/if}

                            {foreach $storages as $storage}
                            {if ($storage.id != $item.storageid)}
                            <option value="{$storage.id}">{$storage.label}</option>
                            {/if}
                            {/foreach}
                        </select>
                    </div>
                    {/if}
                </li>

                {/foreach}
                {else}
                <li class="list-group-item"><span>{t}Keine Gegenstände gefunden{/t}</span></li>
                {/if}
            </ul>
        </div>
        {else}
        <!--<h1>Keine Teile verdammt</h1>-->
        {/if}

        {/foreach}
        {if !$hasdata}<li class="list-group-item"><span>{t}Keine Gegenstände gefunden{/t}</span></li>{/if}
    </form>

</div>
<!-------------------------------------------------------------------------------------------------------------->
{elseif $parse.mode == "category"}
<!-------------------------------------------------------------------------------------------------------------->
<div class="content {if $isGuest}roleguest{/if}">
    <form id="inventoryForm" method="POST" action="{$urlBase}/inventory{$urlPostFix}">
        {foreach $myitem as $itemstore}
        {if $parse.showemptystorages || $itemstore.itemcount > 0 }
        <hr>
        <div class="storage-area">
            <h4 class="text-dark">
                {if isset($itemstore.label)}
                {if isset($itemstore.label)}{$itemstore.label}{else}{t}Unsortiert{/t}{/if}&nbsp;
                {else}
                {if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}&nbsp;
                {/if}

                <span class="small">({$itemstore.positionen} {if $itemstore.positionen == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}, {$itemstore.itemcount} {if $itemstore.itemcount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if})</span>
            </h4>
            <ul class="list-group">
                <li class="alert alert-info">
                    <span class="list-span">{t}Kategorien{/t}</span>
                    <span class="list-span">{t}Bezeichnung{/t}</span>
                    <span class="list-span">{t}Anzahl{/t}</span>
                    <span class="list-span">{t}Bemerkung{/t}</span>
                    <span class="list-span">{t}Unterkategorien{/t}</span>
                    <span class="list-span">{t}Hinzugefügt{/t}</span>
                    {if !$isGuest}
                    <span class="list-span">{t}Aktionen{/t}</span>
                    <span class="list-span">{t}Zuweisen{/t}</span>
                    {/if}
                </li>
                {if isset($itemstore.items)}
                {foreach $itemstore.items as $item}


                {assign var="subCats" value=","|explode:$item.subcategories}
                {$subCategories=array()}
                {foreach $subCats as $subCat}
                {if isset($subcategories.$subCat)}
                {$subCategories[] ="<a href='{$urlBase}/inventory{$urlPostFix}?subcategory={$subcategories.$subCat.id}'>{$subcategories.$subCat.name}</a>"}
                {/if}
                {/foreach}

                {assign var="implodedSubCats" value=", "|implode:$subCategories}
                {assign var="dateexploded" value=" "|explode:$item.date}
                {assign var="catid" value=$item.headcategory}
                {assign var="category" value=$categories.$catid}
                <li class="list-group-item" data-id="{$item.id}">
                    {if !$isGuest}
                    <button class="btn smallButton" title="{t}Position löschen{/t}" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                    {else}
                    <div class="list-span"></div>
                    {/if}

                    <a href="{$urlBase}/inventory{$urlPostFix}?category={$item.headcategory}" class="list-span">{$category.name}</a>
                    <div class="list-span"><span class="listing-hasimages">{if isset($item.hasImages) && $item.hasImages}<i title="{t}Gegenstand hat Bilder{/t}" class="picture fas fa-images"></i><img class="item-picture" data-id="{$item.id}" src="data:image/png;base64,{$item.thumb}">{/if}
                            <a class="listing-label quick-edit" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}">{$item.label}</a></span></div>
                    <div class="list-span"><span class="listing-amount quick-edit">{$item.amount}</span></div>
                    <div class="list-span"><span class="listing-comment quick-edit">{$item.comment}</span></div>

                    <div class="list-span"><span class="listing-subcategories">{$implodedSubCats}</span></div>
                    <div class="list-span"><span class="listing-dateadded">{$dateexploded.0}</span></div>

                    {if !$isGuest}
                    <a tabindex="-1" href="#" class="save-inline-edit inactive" title="{t}Schnelle Bearbeitung speichern{/t}" data-id="{$item.id}"><i class="fas fa-floppy-disk"></i></a>
                    <a tabindex="-1" href="#" class="open-inline-edit" title="{t}Schnelle Bearbeitung{/t}" data-id="{$item.id}"><i class="fas fa-eraser"></i></a>
                    <a title="{t}Ausführliche Bearbeitung{/t}" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}"><i class="fas fa-edit"></i></a>
                    {/if}

                    {if !$isGuest}
                    <div class="dropdown float-right">
                        <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage listing-switchstorage" data-itemamount="{$item.amount}" data-value="0" data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {$hasStorage = false}
                            {foreach $storages as $storage}
                            {if ($storage.id == $item.storageid)}
                            {$hasStorage = true}
                            <option selected="selected" value="-1">{$storage.label}</option>
                            {break}
                            {/if}
                            {/foreach}

                            {if !$hasStorage}
                            <option selected="selected" value="-1">{t}Zuweisen{/t}</option>
                            {/if}

                            {foreach $storages as $storage}
                            {if ($storage.id != $item.storageid)}
                            <option value="{$storage.id}">{$storage.label}</option>
                            {/if}
                            {/foreach}
                        </select>
                    </div>
                    {/if}
                </li>

                {/foreach}
                {else}
                <li class="list-group-item"><span>{t}Keine Gegenstände gefunden{/t}</span></li>
                {/if}
            </ul>
        </div>
        {/if}
        {/foreach}
    </form>
</div>
<!-------------------------------------------------------------------------------------------------------------->
{elseif $parse.mode == "subcategory"}
<!-------------------------------------------------------------------------------------------------------------->
<div class="content roleguest">
    <form id="inventoryForm" method="POST" action="{$urlBase}/inventory{$urlPostFix}">
        {foreach $myitem as $itemstore}
        {if $parse.showemptystorages || $itemstore.itemcount > 0 }
        <hr>
        <div class="storage-area">
            {if !$isGuest}
            <button title="{t}Unterkategorie löschen{/t}" class="btn smallButton" name="removeStorage" data-name="{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}" value="{$itemstore.storage.id}" type="submit"><i class="fas fa-times-circle"></i></button>
            {/if}

            <h4 class="text-dark">
                {if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}&nbsp;
                <span class="small">({$itemstore.positionen} {if $itemstore.positionen == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}, {$itemstore.itemcount} {if $itemstore.itemcount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if})</span>
            </h4>
            <ul class="list-group">
                <li class="alert alert-info">
                    <span class="list-span">{t}Kategorien{/t}</span>
                    <span class="list-span">{t}Bezeichnung{/t}</span>
                    <span class="list-span">{t}Anzahl{/t}</span>
                    <span class="list-span">{t}Bemerkung{/t}</span>
                    <span class="list-span">{t}Unterkategorien{/t}</span>
                    <span class="list-span">{t}Hinzugefügt{/t}</span>
                    {if !$isGuest}
                    <span class="list-span">{t}Aktionen{/t}</span>
                    <span class="list-span">{t}Zuweisen{/t}</span>
                    {/if}
                </li>
                {if isset($itemstore.items)}
                {foreach $itemstore.items as $item}


                {assign var="subCats" value=","|explode:$item.subcategories}
                {$subCategories=array()}
                {foreach $subCats as $subCat}
                {if isset($subcategories.$subCat)}
                {$subCategories[] ="<a href='{$urlBase}/inventory{$urlPostFix}?subcategory={$subcategories.$subCat.id}'>{$subcategories.$subCat.name}</a>"}
                {/if}
                {/foreach}

                {assign var="implodedSubCats" value=", "|implode:$subCategories}
                {assign var="dateexploded" value=" "|explode:$item.date}
                {assign var="catid" value=$item.headcategory}
                {assign var="category" value=$categories.$catid}
                <li class="list-group-item" data-id="{$item.id}">
                    {if !$isGuest}
                    <button title="{t}Position löschen{/t}" class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                    {else}
                    <div class="list-span"></div>
                    {/if}

                    <a href="{$urlBase}/inventory{$urlPostFix}?category={$item.headcategory}" class="list-span">{$category.name}</a>
                    <div class="list-span"><span class="listing-hasimages">{if isset($item.hasImages) && $item.hasImages}<i title="{t}Gegenstand hat Bilder{/t}" class="picture fas fa-images"></i><img class="item-picture" data-id="{$item.id}" src="data:image/png;base64,{$item.thumb}">{/if}
                            <a class="listing-label quick-edit" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}">{$item.label}</a></span></div>
                    <div class="list-span"><span class="listing-amount quick-edit">{$item.amount}</span></div>
                    <div class="list-span"><span class="listing-comment quick-edit">{$item.comment}</span></div>

                    <div class="list-span"><span class="listing-subcategories">{$implodedSubCats}</span></div>
                    <div class="list-span"><span class="listing-dateadded">{$dateexploded.0}</span></div>
                    {if !$isGuest}
                    <a tabindex="-1" href="#" class="save-inline-edit inactive" title="{t}Schnelle Bearbeitung speichern{/t}" data-id="{$item.id}"><i class="fas fa-floppy-disk"></i></a>
                    <a tabindex="-1" href="#" class="open-inline-edit" title="{t}Schnelle Bearbeitung{/t}" data-id="{$item.id}"><i class="fas fa-eraser"></i></a>
                    <a title="{t}Ausführliche Bearbeitung{/t}" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}"><i class="fas fa-edit"></i></a>
                    {/if}

                    {if !$isGuest}
                    <div class="dropdown float-right">
                        <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage listing-switchstorage" data-itemamount="{$item.amount}" data-value="0" data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {$hasStorage = false}
                            {foreach $storages as $storage}
                            {if ($storage.id == $item.storageid)}
                            {$hasStorage = true}
                            <option selected="selected" value="-1">{$storage.label}</option>
                            {break}
                            {/if}
                            {/foreach}

                            {if !$hasStorage}
                            <option selected="selected" value="-1">{t}Zuweisen{/t}</option>
                            {/if}

                            {foreach $storages as $storage}
                            {if ($storage.id != $item.storageid)}
                            <option value="{$storage.id}">{$storage.label}</option>
                            {/if}
                            {/foreach}
                        </select>
                    </div>
                    {/if}
                </li>

                {/foreach}
                {else}
                <li class="list-group-item"><span>{t}Keine Gegenstände gefunden{/t}</span></li>
                {/if}
            </ul>
        </div>
        {else}
        <!--<h1>Keine Teile verdammt</h1>-->
        {/if}
        {/foreach}
        {/if}
    </form>
</div>

{if isset($dump)}
<pre>{$dump}</pre>{/if}
{include file="footer.tpl"}
{literal}
<script type="text/javascript">
    function NumSelect(maxAmount) {
        while (true) {
            let transferAmount = parseInt(prompt("{/literal}{t}Von diesem Artikel sind mehrere Stück am Lagerplatz. Wie viele sollen zum neuen Lagerort transferiert werden?{/t}{literal}", maxAmount))
            if (isNaN(transferAmount)) return -1

            if (transferAmount > maxAmount) {
                alert("{/literal}{t}Von diesem Artikel sind nicht genug Einheiten vorhanden.{/t}{literal}")
            } else if (transferAmount < 0) {
                alert("{/literal}{t}Anzahl kann nicht negativ sein.{/t}{literal}")
            } else {
                return transferAmount
            }
        }
    }

    function MoveItem(itemid, storageid) {
        alert('{/literal}{$urlBase}{literal}/inventory{/literal}{$urlPostFix}{literal}?storageid=' + itemid + '&itemid=' + storageid);
    }

    let imgListings = document.querySelectorAll('.listing-hasimages')
    for (let listing of imgListings) {
        listing.addEventListener('click', function(evt) {
            if (evt.target.nodeName === 'INPUT') {
                evt.preventDefault()
                return
            } else if (evt.target.nodeName === 'A') {
                return
            }

            evt.target.parentNode.querySelector('img.item-picture').classList.toggle('active')
        })
    }

    let switches = document.querySelectorAll('.btn.switchStorage')
    for (let item of switches) {
        item.addEventListener('change', function(evt) {
            if (evt.target.value === '-1') return

            let amountTrans = 1
            if (parseInt(evt.target.dataset['itemamount']) > 1) {
                let toTransfer = NumSelect(evt.target.dataset['itemamount'])
                if (toTransfer <= 0) return
                amountTrans = toTransfer
            }

            window.location.href = '{/literal}{$urlBase}{literal}/inventory{/literal}{$urlPostFix}{literal}?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id'] + '&amount=' + amountTrans.toString();
        })
    }

    let removalButtons = document.querySelectorAll('.smallButton')
    for (let button of removalButtons) {
        button.addEventListener('click', function(evt) {
            let target = evt.target
            if (target.dataset['name'] === undefined) target = target.parentNode
            let targetType = target.name === 'removeStorage' ? '{/literal}{t}Lagerplatz wirklich entfernen?{/t}{literal}' : '{/literal}{t}Position wirklich entfernen?{/t}{literal}'

            if (!window.confirm(targetType + ' "' + target.dataset['name'] + '"')) evt.preventDefault()
        })
    }

    let inlineEdits = document.querySelectorAll('.open-inline-edit')

    for (let editButton of inlineEdits) {
        editButton.addEventListener('click', function(evt) {
            evt.preventDefault()

            if (evt.target.parentNode.nodeName === 'LI') {
                return
            }

            evt.target.parentNode.classList.toggle('active')

            const targetId = parseInt(evt.target.parentNode.dataset['id'])
            let inlineEditSaver = evt.target.parentNode.parentNode.querySelector('.save-inline-edit[data-id="' + targetId + '"]')
            if (inlineEditSaver !== null) {
                inlineEditSaver.classList.add('inactive')
            }

            if (evt.target.parentNode.classList.contains('active')) {
                const imgDisplay = evt.target.parentNode.parentNode.querySelector('.listing-hasimages > i')
                if (imgDisplay !== null) {
                    imgDisplay.classList.add('hidden')
                }

                let targetRowEdits = evt.target.parentNode.parentNode.querySelectorAll('li[data-id="' + targetId + '"] .quick-edit')
                for (const field of targetRowEdits) {
                    let input = document.createElement('input')
                    input.value = field.textContent
                    input.className = 'quick-edit'
                    input.dataset['id'] = targetId
                    input.type = 'text'

                    input.addEventListener('keydown', function(evt) {
                        if (evt.key === 'Enter') {
                            evt.preventDefault();
                        }
                    })

                    for (const className of field.classList.values()) {
                        if (className.startsWith('listing-')) {
                            if (className.endsWith('label')) {
                                input.maxLength = '64'
                                input.minLength = '1'
                                input.placeholder = '{/literal}{t}Bezeichnung{/t}{literal}'
                                input.required = 'required'
                            } else if (className.endsWith('comment')) {
                                input.maxLength = '255'
                            } else if (className.endsWith('amount')) {
                                input.type = 'number'
                                input.maxLength = '19'
                                input.minLength = '1'
                                input.min = '1'
                                input.required = 'required'
                            }

                            input.dataset['dataTarget'] = className
                            break
                        }
                    }

                    const originalContent = encodeURI(input.value)

                    function updateDirtyState(evt) {
                        const inputValue = encodeURI(evt.target.value.trim())
                        let dataTarget = evt.target.parentNode.parentNode.parentNode.querySelector('.save-inline-edit[data-id="' + targetId + '"]')

                        if (inputValue === originalContent) {
                            evt.target.classList.remove('edit-dirty')

                            let dirtyField = document.querySelector('.edit-dirty[data-id="' + targetId + '"]')
                            if (dirtyField === null) {
                                dataTarget.classList.add('inactive')
                            }

                            delete evt.target.dataset['dirtyValue']
                        } else {
                            evt.target.dataset['dirtyValue'] = inputValue
                            evt.target.classList.add('edit-dirty')
                            dataTarget.classList.remove('inactive')
                        }

                        if (evt.key !== undefined && evt.key === 'Enter') {
                            inlineEditSaver.dispatchEvent(new Event('click'))
                        }
                    }

                    input.addEventListener('keyup', updateDirtyState)
                    input.addEventListener('click', updateDirtyState)

                    field.classList.add('hidden')
                    field.classList.add('hide-quick')
                    field.parentNode.insertBefore(input, field)
                }
            } else {
                let targetRowEdits = evt.target.parentNode.parentNode.querySelectorAll('li[data-id="' + targetId + '"] input.quick-edit')
                evt.target.parentNode.parentNode.querySelector('.listing-hasimages').children[0].classList.toggle('hidden')
                for (let field of targetRowEdits) {
                    field.parentNode.removeChild(field)
                }

                let originalColumns = evt.target.parentNode.parentNode.querySelectorAll('li[data-id="' + targetId + '"] .hide-quick.quick-edit')
                for (let field of originalColumns) {
                    field.classList.remove('hide-quick')
                    field.classList.remove('hidden')
                }
            }
        })
    }

    let inlineSaves = document.querySelectorAll('.save-inline-edit')
    for (let saveButton of inlineSaves) {
        saveButton.addEventListener('click', function(evt) {
            evt.preventDefault()

            const targetId = evt.target.parentNode.dataset['id']
            const dirtyFields = document.querySelectorAll('input.quick-edit.edit-dirty[data-id="' + targetId + '"]')
            const itemButton = evt.target

            if (dirtyFields.length === 0) {
                return
            } else {
                let formData = new FormData();
                let xmlRequest = new XMLHttpRequest()
                xmlRequest.open('POST', '{/literal}{$urlBase}{literal}/inventory{/literal}{$urlPostFix}{literal}')
                formData.append('listing-itemId', targetId)
                for (const field of dirtyFields) {
                    if (field.value.trim().length < 1) {
                        if (field.dataset['dataTarget'] === 'listing-label') {
                            alert('{/literal}{t}Die Bezeichnung kann nicht leer sein.{/t}{literal}')
                            return
                        }
                    }
                    const fieldValue = encodeURI(field.value.trim())
                    formData.append(field.dataset['dataTarget'], fieldValue)
                }

                xmlRequest.addEventListener('error', function(evt) {
                    console.log(evt)
                    alert('{/literal}{t}Es trat ein Fehler bei dem Speichern der Inhalte auf. Die Browser Entwickler-Konsole enthält Details.{/t}{literal}')
                })

                xmlRequest.addEventListener('loadend', function(evt) {
                    if (evt.target.responseText === 'OK') {
                        let targetRowEdits = itemButton.parentNode.parentNode.querySelectorAll('li[data-id="' + targetId + '"] input.quick-edit')
                        let originalColumns = itemButton.parentNode.parentNode.querySelectorAll('li[data-id="' + targetId + '"] .hide-quick.quick-edit')
                        itemButton.parentNode.parentNode.querySelector('.listing-hasimages').children[0].classList.toggle('hidden')

                        for (let x = 0; x < targetRowEdits.length; ++x) {
                            originalColumns[x].textContent = decodeURI(targetRowEdits[x].value)
                            originalColumns[x].classList.remove('hide-quick')
                            originalColumns[x].classList.remove('hidden')
                            targetRowEdits[x].parentNode.removeChild(targetRowEdits[x])
                        }


                        itemButton.classList.remove('active')
                        itemButton.classList.add('inactive')
                        itemButton.parentNode.classList.add('inactive')
                        itemButton.parentNode.parentNode.querySelector('.open-inline-edit.active[data-id="' + targetId + '"]').classList.remove('active')
                        alert('{/literal}{t}Der Eintrag wurde erfolgreich aktualisiert.{/t}{literal}')
                    } else if (evt.target.responseText === 'AMOUNT_TYPE') {
                        alert('{/literal}{t}Die Anzahl darf nur gesamte Einheiten umfassen.{/t}{literal}')
                    } else {
                        console.log(evt)
                        alert('{/literal}{t}Bei dem Versuch zu speichern trat ein Fehler auf. Die Browser Entwickler-Konsole enthält Details.{/t}{literal}')
                    }
                })

                xmlRequest.send(formData)
            }

        })
    }

    window.addEventListener('beforeunload', function(evt) {
        let dirtyField = document.querySelector('.edit-dirty')
        if (dirtyField !== null) {
            evt.preventDefault()
        }
    })
</script>
{/literal}
{include file="bodyend.tpl"}
