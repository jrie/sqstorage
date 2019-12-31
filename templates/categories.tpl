{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">


<hr/>
<ul class="categories list-group">
<li class="alert alert-info"><span class="list-span">{t}Kategorien{/t}</span>
<span class="list-span">{t}Anzahl{/t}</span><span class="list-span">{t}Aktionen{/t}</span></li>';
                {foreach $headCategories as $category}
                        <li class="list-group-item">
                            <a name="removeCategory" data-name="{$category.name}" href="categories.php?removeCategory={$category.id}" class="removalButton fas fa-times-circle btn"></a>
                            <a class="list-span" data-name="{$category.name}" href="inventory.php?category={$category.id}">{$category.name}</a><span class="list-span">{$category.amount} {t count=$category.amount plural="Gegenstände"}Gegenstand{/t}</span><a class="fas fa-edit editCategory" href="#" name="editCategory" data-name="{$category.name}" data-id="{$category.id}"></a></li>
                {/foreach}
</ul><hr/>

<ul class="categories list-group">
<li class="alert alert-info"><span class="list-span">{t}Unterkategorien{/t}</span>
<span class="list-span">{t}Anzahl{/t}</span><span class="list-span">{t}Aktionen{/t}</span><span class="list-span">{t}Oberkategorie{/t}</span></li>';

                {foreach $subCategories as $category}
                        <li class="list-group-item">
                        <a name="removeSubcategory" data-name="{$category.name}" href="categories.php?removeSubcategory={$category.id}" class="removalButton fas fa-times-circle btn"></a>
                        <a class="list-span" data-name="{$category.name}" href="inventory.php?subcategory={$category.id}">{$category.name}</a><span class="list-span">{$category.amount}  {t count=$category.amount plural="Gegenstände"}Gegenstand{/t}</span><a class="fas fa-edit editCategory" href="#" name="editSubcategory" data-name="{$category.name}" data-id="{$category.id}"></a>
                        <div class="dropdown list-span">
                            <select class="btn btn-secondary dropdown-toggle categoryDropdowns" type="button" data-originid="{$category.id}" tabindex="-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                                    <option value="-1" {if $category.headcategory == 0}selected="selected"{/if}>{t}Keine{/t}</option>
                                    {foreach $headCategories as $headCategory}
                                            <option value="{$headCategory.id}" {if $headCategory.id == $category.headcategory}selected="selected"{/if}>{$headCategory.name}</option>
                                    {/foreach}
                            </select>
                        </div>
                        </li>
                {/foreach}

</ul>


        </div>

{$target = "transfer.php"}

{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            let removalButtons = document.querySelectorAll('.removalButton')
            for (let button of removalButtons) {
                button.addEventListener('click', function (evt) {
                    let targetType = evt.target.name === 'removeCategory' ? '{/literal}{t}Kategorie wirklich entfernen?{/t}{literal}' : '{/literal}{t}Unterkategorie umbenennen?{/t}{literal}'
                    if (!window.confirm(targetType + ' "' + evt.target.dataset['name'] +'"')) {
                        evt.preventDefault()
                    }
                })
            }

            let editCategoryButtons = document.querySelectorAll('.editCategory')
            for (let button of editCategoryButtons) {
                button.addEventListener('click', function (evt) {
                    let targetType = evt.target.name === 'editCategory' ? '{/literal}{t}Kategorie umbenennen?{/t}{literal}' : '{/literal}{t}Unterkategorie umbenennen?{/t}{literal}'
                    let newName = window.prompt(targetType + ' "' + evt.target.dataset['name'] + '"', '')

                    if (newName !== null && newName.length !== 0) {
                        if (evt.target.name === 'editCategory') window.location.href = 'categories.php?headCategory=' + evt.target.dataset['id'] + '&to=' + encodeURIComponent(newName)
                        else window.location.href = 'categories.php?subCategory=' + evt.target.dataset['id'] + '&to=' + encodeURIComponent(newName)
                    }

                    return false
                })
            }

            let categoryDropdowns = document.querySelectorAll('.categoryDropdowns')
            for (let dropDown of categoryDropdowns) {
                dropDown.addEventListener('change', function (evt) {
                    let subcategoryId = evt.target.dataset['originid']

                    if (evt.target.value === -1) {
                        window.location.href = 'categories.php?resetSubcategoryId=' + subcategoryId
                        return
                    }

                    window.location.href = 'categories.php?setCategoryId=' + subcategoryId + '&to=' + encodeURIComponent(evt.target.value)
                })
            }
        </script>    
{/literal}
{include file="bodyend.tpl"}