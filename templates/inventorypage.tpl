{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">

{$sid = -99}
{$dataid = 0}
{foreach $storecontent as $items}
    {$positionen = 0}{$artikel = 0}
    {foreach $items as $quickitem}
        {$positionen = $positionen + 1}
        {$artikel = $artikel + $quickitem.amount}
    {/foreach}
    {foreach $items as $item name='itemloop'}
        {$dataid = $dataid + 1}
        {if $smarty.foreach.itemloop.first}
            <div class="storage-area">
            <button class="btn smallButton" name="removeStorage" data-name="{$storagearray[{$item.storageid}]['label']}" value="{$item.storageid}" type="submit">
            <i class="fas fa-times-circle"></i>
            </button>
            <h4 class="text-dark">
            <a href="{$target}?storageid={$item.storageid}">{$storagearray[{$item.storageid}]['label']}</a>&nbsp;<span class="small">({$positionen} {t count=$positionen plural="Positionen"}Position{/t} , {$artikel} {t count=$artikel 1=$artikel plural="Gegenstände"}Gegenstand{/t})</span></h4>
            <ul class="list-group">
            <li class="alert alert-info"><span class="list-span">{t}Gruppe{/t}</span><span class="list-span">{t}Bezeichnung{/t}</span><span class="list-span">{t}Anzahl{/t}</span><span class="list-span">{t}Bemerkung{/t}</span><span class="list-span">{t}Unterkategorien{/t}</span><span class="list-span">{t}Hinzugefügt{/t}</span><span class="list-span">{t}Aktionen{/t}</span></li>
        {/if}
        <li class="list-group-item">
            <button class="btn smallButton" name="remove" data-name="{$item.label}" value="1" type="submit"><i class="fas fa-times-circle"></i></button>
            <a href="{$target}?category={$item.headcategory}" class="list-span">{$headCatArr[{$item.headcategory}]['name']}</a>
            <span class="list-span">{$item.label}</span>
            <span class="list-span">{$item.amount}</span>
            <span class="list-span">{$item.comment}</span>
            <span class="list-span">
            
                {$subcategoriesDB = explode(',', trim($item['subcategories'], ','))}                            
                {if count($subcategoriesDB)>0}
                {foreach $subcategoriesDB as $sub}
                    {if strlen($sub)>0}
                    {$scat = $subCatArr[{$sub}]}
                    <a href="{$target}?subcategory={$scat.id}">{$scat.name}</a>
                    {/if}
                {/foreach}
                {/if}
            
            </span>



            <span class="list-span">{$item.date}</span>
            <a class="list-span" href="index.php?editItem={$item.id}"><i class="fas fa-edit"></i></a>
            <div class="dropdown float-right">

                <select autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" value="0" type="button" tabindex="-1" data-id="{$item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <option selected="selected" value="-1">{t}Zuweisen{/t}</option>
                    {foreach $storages as $storage}
                        <option value="{$storage.id}">{$storage.label}</option>
                    {/foreach}
                </select>

            </div>
        </li>
        {if $smarty.foreach.itemloop.last}
        </ul>
        </div>
        {/if}
    {/foreach}

{/foreach}

</div>



{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            let switches = document.querySelectorAll('.btn.switchStorage')
            for (let item of switches) {
                item.addEventListener('change', function(evt) {
                    if (evt.target.value === '-1') return
                    window.location.href = '{/literal}{$target}{literal}?storageid=' + evt.target.value + '&itemid=' + evt.target.dataset['id'];
                })
            }

            let removalButtons = document.querySelectorAll('.smallButton')
            for (let button of removalButtons) {
                button.addEventListener('click', function (evt) {
                    let targetType = evt.target.name === 'removeStorage' ? '{/literal}{t}Lagerplatz wirklich entfernen?{/t}{literal}' : '{/literal}{t}Position wirklich entfernen?{/t}{literal}'
                    if (!window.confirm(targetType + ' "' + evt.target.dataset['name'] + '"')) {
                        evt.preventDefault()
                    }
                })
            }
        </script>
    
{/literal}
{include file="bodyend.tpl"}