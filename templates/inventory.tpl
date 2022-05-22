{include file="head.tpl" title="{t}Inventar{/t}"}
{include file="nav.tpl" target="inventory.php" request=$REQUEST}

{$hasdata=false}
{$selectid=0}

{if $parse.mode == "default"}
<div class="content">
    <form id="inventoryForm" method="POST" action="{$urlBase}/inventory{$urlPostFix}">
        {foreach $myitem as $itemstore}
        {$hasdata=true}
        {if $parse.showemptystorages || $itemstore.itemcount > 0 }
        <hr>
        <div class="storage-area">
            <button class="btn smallButton" name="removeStorage" data-name="{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}" value="{$itemstore.storage.id}" type="submit"><i class="fas fa-times-circle"></i></button>
            <h4 class="text-dark">
                <a href="{$urlBase}/inventory{$urlPostFix}?storageid={$itemstore.storage.id}">{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}</a>&nbsp;
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
                    <span class="list-span">{t}Aktionen{/t}</span>
                    <span class="list-span">{t}Zuweisen{/t}</span>
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
                <li class="list-group-item">
                    <button class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                    <a href="{$urlBase}/inventory{$urlPostFix}?category={$item.headcategory}" class="list-span">{$category.name}</a>
                    <span class="list-span listing-hasimages">{if isset($item.hasImages) && $item.hasImages}<i title="{t}Gegenstand hat Bilder{/t}" class="picture fas fa-images"></i>{/if}<a href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}">{$item.label}</a></span>
                    <span class="list-span listing-amount">{$item.amount}</span>
                    <span class="list-span listing-comment">{$item.comment}</span>

                    <span class="list-span listing-subcategories">{$implodedSubCats}</span>
                    <span class="list-span listing-dateadded">{$dateexploded.0}</span>
                    <a class="list-span listing-edititem" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}"><i class="fas fa-edit"></i></a>

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
<div class="content">
    <form id="inventoryForm" method="POST" action="{$urlBase}/inventory{$urlPostFix}">
        {foreach $myitem as $itemstore}
        {if $parse.showemptystorages || $itemstore.itemcount > 0 }
        <hr>
        <div class="storage-area">
            <button class="btn smallButton" name="removeStorage" data-name="{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}" value="{$itemstore.storage.id}" type="submit"><i class="fas fa-times-circle"></i></button>
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
                    <span class="list-span">{t}Aktionen{/t}</span>
                    <span class="list-span">{t}Zuweisen{/t}</span>
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
                <li class="list-group-item">
                    <button class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                    <a href="{$urlBase}/inventory{$urlPostFix}?category={$item.headcategory}" class="list-span">{$category.name}</a>
                    <span class="list-span listing-hasimages">{if isset($item.hasImages) && $item.hasImages}<i title="{t}Gegenstand hat Bilder{/t}" class="picture fas fa-images"></i>{/if}<a href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}">{$item.label}</a></span>
                    <span class="list-span listing-amount">{$item.amount}</span>
                    <span class="list-span listing-comment">{$item.comment}</span>

                    <span class="list-span listing-subcategories">{$implodedSubCats}</span>
                    <span class="list-span listing-dateadded">{$dateexploded.0}</span>
                    <a class="list-span listing-edititem" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}"><i class="fas fa-edit"></i></a>

                    <div class="dropdown float-right">
                        <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage listing-switchstorage" data-value="0" data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

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
    </form>

</div>
<!-------------------------------------------------------------------------------------------------------------->
{elseif $parse.mode == "subcategory"}
<!-------------------------------------------------------------------------------------------------------------->
<div class="content">
    <form id="inventoryForm" method="POST" action="{$urlBase}/inventory{$urlPostFix}">
        {foreach $myitem as $itemstore}
        {if $parse.showemptystorages || $itemstore.itemcount > 0 }
        <hr>
        <div class="storage-area">
            <button class="btn smallButton" name="removeStorage" data-name="{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}" value="{$itemstore.storage.id}" type="submit"><i class="fas fa-times-circle"></i></button>
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
                    <span class="list-span">{t}Aktionen{/t}</span>
                    <span class="list-span">{t}Zuweisen{/t}</span>
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
                <li class="list-group-item">
                    <button class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                    <a href="{$urlBase}/inventory{$urlPostFix}?category={$item.headcategory}" class="list-span">{$category.name}</a>
                    <span class="list-span listing-hasimages">{if isset($item.hasImages) && $item.hasImages}<i title="{t}Gegenstand hat Bilder{/t}" class="picture fas fa-images"></i>{/if}<a href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}">{$item.label}</a></span>
                    <span class="list-span listing-amount">{$item.amount}</span>
                    <span class="list-span listing-comment">{$item.comment}</span>

                    <span class="list-span listing-subcategories">{$implodedSubCats}</span>
                    <span class="list-span listing-dateadded">{$dateexploded.0}</span>
                    <a class="list-span listing-edititem" href="{$urlBase}/index{$urlPostFix}?editItem={$item.id}"><i class="fas fa-edit"></i></a>

                    <div class="dropdown float-right">
                        <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage listing-switchstorage" data-value="0" data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

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
    </form>

</div>
{/if}

{if isset($dump)}
<pre>{$dump}</pre>{/if}
{include file="footer.tpl"}
{literal}
<script type="text/javascript">
    function NumSelect(maxAmount) {
        while (true) {
            let transferAmount = parseInt(prompt("{/literal}{t}Von diesem Artikel sind mehrere Stück am Lagerplatz. Wie viele sollen zum neuen Lagerort transferiert werden?{/t}{literal}", maxAmount))
            if (isNaN(transferAmount)) return -1
            if (transferAmount > maxAmount) alert("{/literal}{t}Von diesem Artikel sind nicht genug Einheiten vorhanden.{/t}{literal}")
            else if (transferAmount < 0) alert("{/literal}{t}Anzahl kann nicht negativ sein.{/t}{literal}")
            else return transferAmount
        }
    }

    function MoveItem(itemid, storageid) {
        alert('{/literal}{$urlBase}{literal}/inventory?storageid=' + itemid + '&itemid=' + storageid);
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

            window.location.href = '{/literal}{$urlBase}{literal}/inventory?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id'] + '&amount=' + amountTrans.toString();
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
</script>
{/literal}
{include file="bodyend.tpl"}