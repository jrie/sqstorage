{include file="head.tpl" title="{t}Kategorien{/t}"}
{include file="nav.tpl" target="categories.php" request=$REQUEST}

<div class="content {if $isGuest}roleguest{/if}">
    {$alert}
    <hr />
    <ul class="categories list-group">
        <li class="alert alert-info">
            <span class="list-span" title="{t}Kategorien{/t}">{t}Kategorien{/t}</span>
            <span class=" list-span" title="{t}Anzahl{/t}">{t}Anzahl{/t}</span>
            <span class="list-span" title="{t}Positionen{/t}">{t}Positionen{/t}</span>
            {if !$isGuest}
            <span class="list-span" title="{t}Aktionen{/t}">{t}Aktionen{/t}</span>
            {/if}
        </li>
        {foreach $headCategories as $category}
        <li class="list-group-item">
            {if !$isGuest}
            <a name="removeCategory" tabindex="-1" data-name="{$category.name}" href="{$urlBase}/categories{$urlPostFix}?removeCategory={$category.id}" title="{t}Kategorie löschen{/t}" class="removalButton fas fa-times-circle btn"></a>
            {/if}
            <a class="list-span" data-name="{$category.name}" title="{$category.name}" href="{$urlBase}/inventory{$urlPostFix}?category={$category.id}">{$category.name}</a>
            <span class="list-span">{$category.amount} {if $category.amount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if}</span>
            <span class="list-span">{$category.positions} {if $category.positions == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}</span>
            {if !$isGuest}
            <a title="{t}Kategorie umbenennen{/t}" class="fas fa-edit editCategory" href="#" name="editCategory" data-name="{$category.name}" data-id="{$category.id}"></a>
            {/if}
        </li>
        {/foreach}
    </ul>
    <hr />

    <ul class="categories list-group">
        <li class="alert alert-info">
            <span class="list-span" title="{t}Unterkategorien{/t}">{t}Unterkategorien{/t}</span>
            <span class="list-span" title="{t}Anzahl{/t}">{t}Anzahl{/t}</span>
            <span class="list-span" title="{t}Positionen{/t}">{t}Positionen{/t}</span>
            {if !$isGuest}
            <span class="list-span" title="{t}Aktionen{/t}">{t}Aktionen{/t}</span>
            {/if}
            <span class="list-span" title="{t}Oberkategorie{/t}">{t}Oberkategorie{/t}</span>
        </li>
        {foreach $subCategories as $category}
        <li class="list-group-item">

            {if !$isGuest}
            <a name="removeSubcategory" tabindex="-1" data-name="{$category.name}" href="{$urlBase}/categories{$urlPostFix}?removeSubcategory={$category.id}" title="{t}Unterkategorie löschen{/t}" class="removalButton fas fa-times-circle btn"></a>
            {/if}
            <a class="list-span" data-name="{$category.name}" title="{$category.name}" href="{$urlBase}/inventory{$urlPostFix}?subcategory={$category.id}">{$category.name}</a>
            <span class="list-span">{$category.amount} {if $category.amount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if}</span>
            <span class="list-span">{$category.positions} {if $category.positions == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}</span>
            {if !$isGuest}
            <a title="{t}Unterkategorie umbenennen{/t}" class="fas fa-edit editCategory" href="#" name="editSubcategory" data-name="{$category.name}" data-id="{$category.id}"></a>
            {/if}

            {if !$isGuest}
            <div class="dropdown list-span">
                <select class="btn dropdown-toggle categoryDropdowns" type="button" data-originid="{$category.id}" tabindex="-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">

                    {if $category.headcategory != 0}
                    <option value="-1">{t}Keine{/t}</option>
                    {else}
                    <option value="-1" selected="selected">{t}Keine{/t}</option>
                    {/if}

                    {foreach $headCategories as $headCategory} {
                    {if $headCategory.id == $category.headcategory}
                    <option value="{$headCategory.id}" selected="selected">{$headCategory.name}</option>
                    {else}
                    <option value="{$headCategory.id}">{$headCategory.name}</option>
                    {/if}
                    {/foreach}
                </select>
            </div>
            {else}
            <div class="list-span">
                {if $category.headcategory === NULL}
                    {t}Keine{/t}
                {else}
                    {foreach $headCategories as $headCategory} {
                        {if $headCategory.id == $category.headcategory}
                            {$headCategory.name}
                        {break}
                        {/if}
                    {/foreach}
                {/if}
            </div>
            {/if}
        </li>

        {/foreach}
    </ul>
</div>
{include file="footer.tpl"}
{literal}
<script type="text/javascript">
    let removalButtons = document.querySelectorAll('.removalButton')
    for (let button of removalButtons) {
        button.addEventListener('click', function(evt) {
            let targetType = evt.target.name === 'removeCategory' ? '{/literal}{t}Kategorie wirklich entfernen?{/t}{literal}' : '{/literal}{t}Unterkategorie wirklich entfernen?{/t}{literal}'
            if (!window.confirm(targetType + ' "' + evt.target.dataset['name'] + '"')) {
                evt.preventDefault()
            }
        })
    }

    let editCategoryButtons = document.querySelectorAll('.editCategory')
    for (let button of editCategoryButtons) {
        button.addEventListener('click', function(evt) {
            let targetType = evt.target.name === 'editCategory' ? '{/literal}{t}Kategorie umbenennen?{/t}{literal}' : '{/literal}{t}Unterkategorie umbenennen?{/t}{literal}'
            let newName = window.prompt(targetType + ' "' + evt.target.dataset['name'] + '"', '')

            if (newName !== null && newName.length !== 0) {
                if (evt.target.name === 'editCategory') window.location.href = '{/literal}{$urlBase}{literal}/categories{/literal}{$urlPostFix}{literal}?headCategory=' + evt.target.dataset['id'] + '&to=' + encodeURIComponent(newName)
                else window.location.href = '{/literal}{$urlBase}{literal}/categories{/literal}{$urlPostFix}{literal}?subCategory=' + evt.target.dataset['id'] + '&to=' + encodeURIComponent(newName)
            }

            return false
        })
    }

    let categoryDropdowns = document.querySelectorAll('.categoryDropdowns')
    for (let dropDown of categoryDropdowns) {
        dropDown.addEventListener('change', function(evt) {
            let subcategoryId = evt.target.dataset['originid']
            if (evt.target.value === '-1') {
                window.location.href = '{/literal}{$urlBase}{literal}/categories{/literal}{$urlPostFix}{literal}?resetSubcategoryId=' + subcategoryId
                return
            }

            window.location.href = '{/literal}{$urlBase}{literal}/categories{/literal}{$urlPostFix}{literal}?setCategoryId=' + subcategoryId + '&to=' + evt.target.value
        })
    }
</script>
{/literal}
{include file="bodyend.tpl"}