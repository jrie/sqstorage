{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}




{if $parse.mode == "default"}
        <div class="content">
            <form id="inventoryForm" method="POST" action="inventory.php">
            {foreach $myitem as $itemstore}
            {if $parse.showemptystorages || $itemstore.itemcount > 0 }    
            <hr>
                <div class="storage-area">
                <button class="btn smallButton" name="removeStorage" data-name="{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}" value="{$itemstore.storage.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                <h4 class="text-dark">
                    <a href="inventory.php?storageid={$itemstore.storage.id}">{if isset($itemstore.storage.label)}{$itemstore.storage.label}{else}{t}Unsortiert{/t}{/if}</a>&nbsp;
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
                            {$subCategories[] ="<a href='inventory.php?subcategory={$subcategories.$subCat.id}'>{$subcategories.$subCat.name}</a>"}
                            {/if}
                        {/foreach}

                        {assign var="implodedSubCats" value=", "|implode:$subCategories}
                        {assign var="dateexploded" value=" "|explode:$item.date}
                        {assign var="catid" value=$item.headcategory}
                        {assign var="category" value=$categories.$catid}
                        <li class="list-group-item">
                            <button class="btn smallButton" name="remove" data-name="{$item.label}" value="{$item.id}" type="submit"><i class="fas fa-times-circle"></i></button>
                            <a href="inventory.php?category={$item.headcategory}" class="list-span">{$category.name}</a>
                            <span class="list-span">{$item.label}</span>
                            <span class="list-span">{$item.amount}</span>
                            <span class="list-span">{$item.comment}</span>

                            <span class="list-span">{$implodedSubCats}</span> 
                            <span class="list-span">{$dateexploded.0}</span>
                            <a class="list-span" href="index.php?editItem={$item.id}"><i class="fas fa-edit"></i></a>

                            <div class="dropdown float-right">
                                <select autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" data-value="0"  data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                
                                <option selected="selected" value="-1">{t}Zuweisen{/t}</option>

                                {foreach $storages as $storage} 
                                    <option value="{$storage.id}">{$storage.label}</option>
                                {/foreach}
                                </select>
                            </div>
                        </li>
                    
                    {/foreach}
                    
                {/if}
                </ul></div>
            {else}
            <h1>Keine Teile verdammt</h1>
            {/if}
            
            {/foreach}
            </form>
{if isset($dump)}<pre>{$dump}</pre>{/if}
        </div>
{elseif $parse.mode == "category"}
<h1>Kategory parser</h1>
{elseif $parse.mode == "subcategory"}
<h1>subKategory parser</h1>
{else}
<h1>was denn sonst</h1>
{/if}


{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            let switches = document.querySelectorAll('.btn.switchStorage')
            for (let item of switches) {
                item.addEventListener('change', function(evt) {
                    if (evt.target.value === '-1') return
                    alert('inventory.php?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id']);
                    window.location.href = 'inventory.php?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id'];
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