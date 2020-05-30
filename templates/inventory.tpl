{include file="head.tpl" title="{t}Inventar{/t}"}
{include file="nav.tpl" target="inventory.php" request=$REQUEST}

{$hasdata=false}
{$selectid=0}

{if $parse.mode == "default"}
        <div class="content">
            <form id="inventoryForm" method="POST" action="{$urlBase}/inventory">
            {foreach $myitem as $itemstore}
            {$hasdata=true}
            {if $parse.showemptystorages || $itemstore.itemcount > 0 }
            <hr>
                <div class="storage-area">
                <button class="btn smallButton" name="removeStorage" data-name="{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}" value="{$itemstore.storage.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                <h4 class="text-dark">
                    <a href="{$urlBase}/inventory?storageid={$itemstore.storage.id}">{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}</a>&nbsp;
                    <span class="small">({$itemstore.positionen} {if $itemstore.positionen == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}, {$itemstore.itemcount} {if $itemstore.itemcount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if})</span>
                </h4>
                <ul class="list-group">


                        <li class="alert alert-info"><span class="list-span">{t}Gruppe{/t}</span><span class="list-span">{t}Bezeichnung{/t}</span><span class="list-span">{t}Anzahl{/t}</span><span class="list-span">{t}Bemerkung{/t}</span><span class="list-span">{t}Unterkategorien{/t}</span><span class="list-span">{t}Hinzugefügt{/t}</span><span class="list-span">{t}Aktionen{/t}</span>
                        </li>
                {if isset($itemstore.items)}
                    {foreach $itemstore.items as $item}


                        {assign var="subCats" value=","|explode:$item.subcategories}
                        {$subCategories=array()}
                        {foreach $subCats as $subCat}
                            {if isset($subcategories.$subCat)}
                            {$subCategories[] ="<a href='{$urlBase}/inventory?subcategory={$subcategories.$subCat.id}'>{$subcategories.$subCat.name}</a>"}
                            {/if}
                        {/foreach}

                        {assign var="implodedSubCats" value=", "|implode:$subCategories}
                        {assign var="dateexploded" value=" "|explode:$item.date}
                        {assign var="catid" value=$item.headcategory}
                        {assign var="category" value=$categories.$catid}
                        <li class="list-group-item">
                            <button class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                            <a href="{$urlBase}/inventory?category={$item.headcategory}" class="list-span">{$category.name}</a>
                            <span class="list-span"><a class="list-span" href="{$urlBase}/index?editItem={$item.id}">{$item.label}</a> {if $item.hasImages}<i title="{t}Gegenstand hat Bilder{/t}" class="fas fa-images"></i>{/if}</span>
                            <span class="list-span">{$item.amount}</span>
                            <span class="list-span">{$item.comment}</span>

                            <span class="list-span">{$implodedSubCats}</span>
                            <span class="list-span">{$dateexploded.0}</span>
                            <a class="list-span" href="{$urlBase}/index?editItem={$item.id}"><i class="fas fa-edit"></i></a>

                            <div class="dropdown float-right">
                                <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage" data-itemamount="{$item.amount}" data-value="0"  data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <option selected="selected" value="-1">{t}Zuweisen{/t}</option>

                                {foreach $storages as $storage}
                                    {if $storage.id == $itemstore.storage.id}
                                        {continue}
                                    {/if}
                                    <option value="{$storage.id}">{$storage.label}</option>
                                {/foreach}
                                </select>
                            </div>
                        </li>

                    {/foreach}
                {else}
                    <li class="list-group-item"><span>{t}Keine Gegenstände gefunden{/t}</span></li>
                {/if}
                </ul></div>
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
            <form id="inventoryForm" method="POST" action="{$urlBase}/inventory">
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


                        <li class="alert alert-info"><span class="list-span">{t}Gruppe{/t}</span><span class="list-span">{t}Bezeichnung{/t}</span><span class="list-span">{t}Anzahl{/t}</span><span class="list-span">{t}Bemerkung{/t}</span><span class="list-span">{t}Unterkategorien{/t}</span><span class="list-span">{t}Hinzugefügt{/t}</span><span class="list-span">{t}Aktionen{/t}</span>
                        </li>
                {if isset($itemstore.items)}
                    {foreach $itemstore.items as $item}


                        {assign var="subCats" value=","|explode:$item.subcategories}
                        {$subCategories=array()}
                        {foreach $subCats as $subCat}
                            {if isset($subcategories.$subCat)}
                            {$subCategories[] ="<a href='{$urlBase}/inventory?subcategory={$subcategories.$subCat.id}'>{$subcategories.$subCat.name}</a>"}
                            {/if}
                        {/foreach}

                        {assign var="implodedSubCats" value=", "|implode:$subCategories}
                        {assign var="dateexploded" value=" "|explode:$item.date}
                        {assign var="catid" value=$item.headcategory}
                        {assign var="category" value=$categories.$catid}
                        <li class="list-group-item">
                            <button class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                            <a href="{$urlBase}/inventory?category={$item.headcategory}" class="list-span">{$category.name}</a>
                            <span class="list-span">{$item.label}</span>
                            <span class="list-span">{$item.amount}</span>
                            <span class="list-span">{$item.comment}</span>

                            <span class="list-span">{$implodedSubCats}</span>
                            <span class="list-span">{$dateexploded.0}</span>
                            <a class="list-span" href="{$urlBase}/index?editItem={$item.id}"><i class="fas fa-edit"></i></a>

                            <div class="dropdown float-right">
                                <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage" data-value="0"  data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <option selected="selected" value="-1">{t}Zuweisen{/t}</option>

                                {foreach $storages as $storage}
                                    <option value="{$storage.id}">{$storage.label}</option>
                                {/foreach}
                                </select>
                            </div>
                        </li>

                    {/foreach}
                {else}
                    <li class="list-group-item"><span>{t}Keine Gegenstände gefunden{/t}</span></li>
                {/if}
                </ul></div>
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
            <form id="inventoryForm" method="POST" action="{$urlBase}/inventory">
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


                        <li class="alert alert-info"><span class="list-span">{t}Gruppe{/t}</span><span class="list-span">{t}Bezeichnung{/t}</span><span class="list-span">{t}Anzahl{/t}</span><span class="list-span">{t}Bemerkung{/t}</span><span class="list-span">{t}Unterkategorien{/t}</span><span class="list-span">{t}Hinzugefügt{/t}</span><span class="list-span">{t}Aktionen{/t}</span>
                        </li>
                {if isset($itemstore.items)}
                    {foreach $itemstore.items as $item}


                        {assign var="subCats" value=","|explode:$item.subcategories}
                        {$subCategories=array()}
                        {foreach $subCats as $subCat}
                            {if isset($subcategories.$subCat)}
                            {$subCategories[] ="<a href='{$urlBase}/inventory?subcategory={$subcategories.$subCat.id}'>{$subcategories.$subCat.name}</a>"}
                            {/if}
                        {/foreach}

                        {assign var="implodedSubCats" value=", "|implode:$subCategories}
                        {assign var="dateexploded" value=" "|explode:$item.date}
                        {assign var="catid" value=$item.headcategory}
                        {assign var="category" value=$categories.$catid}
                        <li class="list-group-item">
                            <button class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                            <a href="{$urlBase}/inventory?category={$item.headcategory}" class="list-span">{$category.name}</a>
                            <span class="list-span">{$item.label}</span>
                            <span class="list-span">{$item.amount}</span>
                            <span class="list-span">{$item.comment}</span>

                            <span class="list-span">{$implodedSubCats}</span>
                            <span class="list-span">{$dateexploded.0}</span>
                            <a class="list-span" href="{$urlBase}/index?editItem={$item.id}"><i class="fas fa-edit"></i></a>

                            <div class="dropdown float-right">
                                <select autocomplete="off" id="item_{$item.id}" class="btn btn-primary dropdown-toggle switchStorage" data-value="0"  data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <option selected="selected" value="-1">{t}Zuweisen{/t}</option>

                                {foreach $storages as $storage}
                                    <option value="{$storage.id}">{$storage.label}</option>
                                {/foreach}
                                </select>
                            </div>
                        </li>

                    {/foreach}
                {else}
                    <li class="list-group-item"><span>{t}Keine Gegenstände gefunden{/t}</span></li>
                {/if}
                </ul></div>
            {else}
            <!--<h1>Keine Teile verdammt</h1>-->
            {/if}

            {/foreach}
            </form>

        </div>
{/if}

{if isset($dump)}<pre>{$dump}</pre>{/if}
{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            function NumSelect(maxAmout) {
              var Amount = prompt("{/literal}{t}Von diesem Artikel sind mehrere Stück am Lagerplatz. Wieviele sollen zum neuen Lagerort transferiert werden?{/t}{literal}", maxAmout);
              return Amount
            }


            function MoveItem(itemid,storageid){
                alert('{/literal}{$urlBase}{literal}/inventory?storageid=' + itemid + '&itemid=' + storageid);
            }

            let switches = document.querySelectorAll('.btn.switchStorage')

            for (let item of switches) {
                item.addEventListener('change', function(evt) {
                    let amountTrans = ""
                    if (evt.target.value === '-1') return
                      if(evt.target.dataset['itemamount'] > 1){
                          let ToTranser = NumSelect(evt.target.dataset['itemamount'])
                          if (ToTranser >= 0 && ToTranser <= evt.target.dataset['itemamount']){
                            amountTrans = "&amount=" + ToTranser
                          }
                      }

                    window.location.href = '{/literal}{$urlBase}{literal}/inventory?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id'] + amountTrans;
                    //alert('{$urlBase}/inventory?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id'] + amountTrans)
                })
            }

            let removalButtons = document.querySelectorAll('.smallButton')
            for (let button of removalButtons) {
                button.addEventListener('click', function (evt) {
                    let target = evt.target.parentNode
                    let targetType = target.name === 'removeStorage' ? '{/literal}{t}Lagerplatz wirklich entfernen?{/t}{literal}' : '{/literal}{t}Position wirklich entfernen?{/t}{literal}'
                    if (!window.confirm(targetType + ' "' + target.dataset['name'] + '"')) {
                        evt.preventDefault()
                    }
                })
            }
        </script>
{/literal}
{include file="bodyend.tpl"}
