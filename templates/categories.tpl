{include file="head.tpl" title="{t}Kategorien{/t}"}
{include file="nav.tpl" target="categories.php" request=$REQUEST}

<div class="content {if $isGuest}roleguest{/if}">
  {$alert}
  <hr />
  {if !$isGuest}
    {if !empty($error) || (isset($addedCategoryId) && $addedCategoryId === -1)}
      <div class="alert alert-danger" role="alert">
        <h6>{$error}</h6>
      </div>
    {else if isset($addedCategoryName)}
      <div class="alert alert-success" role="alert">
        <h6>{t}Kategorie angelegt:{/t}&nbsp;"{$addedCategoryName}"</h6>
      </div>
    {/if}
    <div class="categories">

      <form name="addcategory" method="POST" action="{$urlBase}/categories{$urlPostFix}">
        <ul class="list-group">
          <li class="alert alert-info">
            <span>{t}Neue Kategorie oder Unterkategorie anlegen{/t}</span>
          </li>
          <li class="list-group-item mb-2">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">{t}Bezeichnung{/t}</span>
              </div>
              <input type="text" name="categoryname" class="form-control" maxlength="128" required autocomplete="off" placeholder="{t}Kategorie/Unterkategorie Bezeichnung{/t}" aria-label="{t}Bezeichnung{/t}" aria-describedby="basic-addon1">
            </div>

            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon2">{t}Kategorie-Typ{/t}</span>
              </div>
              <div class="dropdown">
                <select name="categorytype" required autocomplete="off" required class="btn dropdown-toggle" type="button" tabindex="-1" aria-haspopup="true" aria-expanded="false">
                  <option value="0" selected="selected">{t}Kategorie{/t}</option>
                  <option value="1">{t}Unterkategorie{/t}</option>
                </select>
              </div>
            </div>

          </li>
        </ul>
        <button class="float-right btn-form-submit btn btn-primary" title="{t}Hinzufügen{/t}" type="submit">{t}Hinzufügen{/t}</button>
      </form>
    </div>
  {/if}
  <hr />

  <div class="categories">
    <ul class="list-group">
      <li class="alert alert-info">
        <span class="list-span header sortable" data-index="1" title="{t}Kategorien{/t}">{t}Kategorien{/t}</span>
        <span class="list-span header sortable" data-index="2" data-sort="number" title="{t}Positionen{/t}">{t}Positionen{/t}</span>
        <span class=" list-span header sortable" data-index="3" data-sort="number" title="{t}Anzahl{/t}">{t}Anzahl{/t}</span>
        {if !$isGuest}
          <span class="list-span" title="{t}Aktionen{/t}">{t}Aktionen{/t}</span>
        {/if}
      </li>
      {if empty($headCategories)}
        <li class="list-group-item">
          {t}Keine Hauptkategorie vorhanden{/t}
        </li>
      {/if}
      {foreach $headCategories as $category}
        <li class="list-group-item" data-id="{$category.id}">
          <a class="list-span" data-name="{$category.name}" title="{$category.name}" href="{$urlBase}/inventory{$urlPostFix}?category={$category.id}">{$category.name}</a>
          <span class="list-span">{$category.positions}
            {if $category.positions == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}</span>
          <span class="list-span">{$category.amount}
            {if $category.amount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if}</span>
          {if !$isGuest}
            <div class="list-span actions">
              <a name="removeCategory" tabindex="-1" data-name="{$category.name}" href="{$urlBase}/categories{$urlPostFix}?removeCategory={$category.id}" title="{t}Kategorie löschen{/t}" class="removalButton fas fa-times-circle btn"></a>
              <a title="{t}Kategorie umbenennen{/t}" class="fas fa-edit editCategory" href="#" name="editCategory" data-name="{$category.name}" data-id="{$category.id}"></a>
            </div>
          {/if}
        </li>
      {/foreach}
    </ul>

    {if !$isGuest}
      <button type="button" data-table="headCategories" data-name="categories" class="csvDownload btn btn-primary">{t}Als CSV herunterladen{/t}</button>
    {/if}
  </div>

  <hr />

  <div class="categories">
    <ul class="list-group">
      <li class="alert alert-info">
        <span class="list-span header sortable" data-index="1" title="{t}Unterkategorien{/t}">{t}Unterkategorien{/t}</span>
        <span class="list-span header sortable" data-index="2" data-sort="number" title="{t}Positionen{/t}">{t}Positionen{/t}</span>
        <span class="list-span header sortable" data-index="3" data-sort="number" title="{t}Anzahl{/t}">{t}Anzahl{/t}</span>
        {if !$isGuest}
          <span class="list-span" title="{t}Aktionen{/t}">{t}Aktionen{/t}</span>
        {/if}
        <span class="list-span" title="{t}Oberkategorie{/t}">{t}Oberkategorie{/t}</span>
      </li>
      {if empty($subCategories)}
        <li class="list-group-item">
          {t}Keine Unterkategorien vorhanden{/t}
        </li>
      {/if}
      {foreach $subCategories as $category}
        <li class="list-group-item" data-id="{$category.id}">
          <a class="list-span" data-name="{$category.name}" title="{$category.name}" href="{$urlBase}/inventory{$urlPostFix}?subcategory={$category.id}">{$category.name}</a>
          <span class="list-span">{$category.positions}
            {if $category.positions == 1}{t}Position{/t}{else}{t}Positionen{/t}{/if}</span>
          <span class="list-span">{$category.amount}
            {if $category.amount == 1}{t}Gegenstand{/t}{else}{t}Gegenstände{/t}{/if}</span>
          {if !$isGuest}
            <div class="subcategories list-span actions">
              <a name="removeSubcategory" tabindex="-1" data-name="{$category.name}" href="{$urlBase}/categories{$urlPostFix}?removeSubcategory={$category.id}" title="{t}Unterkategorie löschen{/t}" class="removalButton fas fa-times-circle btn"></a>
              <a title="{t}Unterkategorie umbenennen{/t}" class="fas fa-edit editCategory" href="#" name="editSubcategory" data-name="{$category.name}" data-id="{$category.id}"></a>
            </div>
          {/if}

          {if !$isGuest}
            <div class="dropdown list-span">
              <select class="btn dropdown-toggle categoryDropdowns" type="button" data-originid="{$category.id}" tabindex="-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">

                {if $category.headcategory != 0}
                  <option value="-1">{t}Keine Auswahl{/t}</option>
                {else}
                  <option value="-1" selected="selected">{t}Keine Auswahl{/t}</option>
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
                {t}Keine Auswahl{/t}
              {else}
                {foreach $headCategories as $headCategory}
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

    {if !$isGuest}
      <button type="button" data-table="subCategories" data-name="subcategories" class="csvDownload btn btn-primary">{t}Als CSV herunterladen{/t}</button>
    {/if}

  </div>
  {include file="footer.tpl"}
  {literal}
    <script type="text/javascript">
    {/literal}
    {if !$isGuest}
      {literal}
        function downloadCSV(tableName, tableId, downloadName) {
          var xhr = new XMLHttpRequest();
          xhr.open('POST', 'csvdownload.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

          xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
              var blob = new Blob([xhr.response], { type: 'text/csv' });
              var link = document.createElement('a');
              link.download = downloadName ? downloadName + '.csv' : tableName + '.csv';
              link.href = window.URL.createObjectURL(blob);
              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);
            }
          };
          if (tableId) {
            xhr.send('table=' + encodeURIComponent(tableName) + '&tableid=' + encodeURIComponent(tableId));
          } else {
            xhr.send('table=' + encodeURIComponent(tableName));
          }
        }

        let downloadButtons = document.querySelectorAll('.csvDownload');
        for (let button of downloadButtons) {
          button.addEventListener('click', function(evt) {
            downloadCSV(button.dataset['table'], button.dataset['tableid'], button.dataset['name']);
          })
        }
      {/literal}
    {/if}
    {literal}
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

      let activeSortIndex = -2
      let originalOrderIds = []
      let currentActive = null
      let previousActive = null

      function doSort(evt) {
        const sortByIndex = parseInt(evt.target.dataset['index']) - 1
        let listItems = evt.target.parentNode.parentNode.querySelectorAll('li.list-group-item')
        let sortItems = []

        currentActive = document.querySelector('span.header.orderup, span.header.orderdown')
        let doRestore = false
        if (currentActive !== null && currentActive !== previousActive) {
          doRestore = true
        }

        previousActive = currentActive


        if (activeSortIndex === -1 || doRestore) {
          let activeListItems = currentActive.parentNode.parentNode.querySelectorAll('li.list-group-item')
          for (const originalId of originalOrderIds) {
            for (const listItem of activeListItems) {
              if (listItem.dataset['id'] === originalId) {
                currentActive.parentNode.parentNode.appendChild(listItem)
                break
              }
            }
          }

          currentActive.classList.remove('orderup')
          currentActive.classList.remove('orderdown')
          if (!doRestore || activeSortIndex === -1) {
            previousActive = null
            originalOrderIds = []
            activeSortIndex = -2;
            return
          }
        }

        for (const listItem of listItems) {
          const itemRow = listItem.querySelectorAll('.list-span')
          if (activeSortIndex === -2) {
            originalOrderIds.push(listItem.dataset['id'])
          }
          sortItems.push(itemRow[sortByIndex].outerText.trim())
        }

        if (activeSortIndex !== sortByIndex) {
          if (evt.target.dataset['sort'] === undefined || evt.target.dataset['sort'] === 'date') {
            // Default sorting and "2022-12-31" sorting
            sortItems.sort(new Intl.Collator('{/literal}{$langShortCode}{literal}').compare)
          } else if (evt.target.dataset['sort'] === 'number') {
            // Number sorting
            sortItems.sort(new Intl.Collator('{/literal}{$langShortCode}{literal}', {
            'numeric': true
          }).compare)
      }
      activeSortIndex = sortByIndex
      evt.target.classList.add('orderup')
      }
      else {
        evt.target.classList.remove('orderup')
        evt.target.classList.add('orderdown')
        sortItems.reverse()
        activeSortIndex = -1
      }


      let listItemArray = Array.from(listItems)
      for (const sortValue of sortItems) {
        let currentIndex = 0
        for (const listItem of listItemArray) {
          const itemRow = listItem.querySelectorAll('.list-span')
          const sortText = itemRow[sortByIndex].outerText.trim()
          if (sortText === sortValue) {
            evt.target.parentNode.parentNode.appendChild(listItem)
            listItemArray.splice(currentIndex, 1)
            break
          } else {
            ++currentIndex
          }
        }
      }
      }


      let sortAbles = document.querySelectorAll('span.header.sortable')
      for (let sortAble of sortAbles) {
        sortAble.addEventListener('click', doSort)
        sortAble.classList.add('pointer')
      }
    </script>
  {/literal}
{include file="bodyend.tpl"}
