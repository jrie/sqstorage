<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a href="index.php"><img class="logo" src="./img/sqstorage.png" /></a>
    <ul class="nav">
        <li class="nav-item"><a href="index.php" class="nav-link">{t}Eintragen{/t}</a></li>
        <li class="nav-item"><a href="inventory.php" class="nav-link">{t}Inventar{/t}</a></li>
        <li class="nav-item"><a href="categories.php" class="nav-link">{t}Kategorien{/t}</a></li>
        <li class="nav-item"><a href="transfer.php" class="nav-link">{t}Transferieren{/t}</a></li>
    </ul>

    <form class="form-inline my-2 " method="GET" action="inventory.php">
        <input class="form-control mr-sm-2" name="searchValue" type="search" placeholder="{t}Suche{/t}" aria-label="{t}Suche{/t}">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">{t}Suchen{/t}</button>
    </form>
    <form class="form-inline my-2 " method="GET">
    <select class="form-control mr-sm-2" name="lang">
        {foreach $langsAvailable as $lang}
        <option class="nav-item" value="{$lang}" {if $langCurrent == $lang} selected="selected"{/if}>{$langsLabels[{$lang}]}</option>
        {/foreach}
    </select>
    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">{t}Sprache wählen{/t}</button>
    </form>
</nav>