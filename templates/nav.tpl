<body>


<nav class="navbar navbar-light bg-light">
    <a href="index.php"><img class="logo" src="./img/sqstorage.png" alt="Logo" /></a>
    <ul class="nav">
        <li class="nav-item"><a href="index.php" class="nav-link">{t}Eintragen{/t}</a></li>
        <li class="nav-item"><a href="inventory.php" class="nav-link">{t}Inventar{/t}</a></li>
        <li class="nav-item"><a href="categories.php" class="nav-link">{t}Kategorien{/t}</a></li>
        <li class="nav-item"><a href="transfer.php" class="nav-link">{t}Transferieren{/t}</a></li>
        {if isset($SESSION.user.usergroupid)}
            {if $SESSION.user.usergroupid == 1}
                <li class="nav-item"><a href="settings.php" class="nav-link">{t}Einstellungen{/t}</a></li>
            {/if}

        {/if}       
        <li class="nav-item"><a href="datafields.php" class="nav-link">{t}Datenfelder{/t}</a></li>
    </ul>

    <form class="form-inline searchArea" method="GET" action="inventory.php">
        <input class="form-control mr-sm-2" name="searchValue" type="search" placeholder="{t}Suche{/t}" aria-label="{t}Suche{/t}">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">{t}Suchen{/t}</button>
    </form>


        {foreach $langsAvailable as $lang}
                <a href="index.php?lang={$lang}" alt="Language {$langsLabels.$lang}"><small>{$langsLabels.$lang}</small></a><br />
        {/foreach}
<!--        <select class="form-control mr-sm-2" name="lang">
        
            { foreach $langsAvailable as $lang }
            <option class="nav-item" value="{ $lang }" { if $langCurrent == $lang } selected="selected"{ /if }>{ $langsLabels[{ $lang }] }</option>
            { /foreach }
        
        </select>
-->


    <ul class="nav">
        <li class="nav-item"><a href="index.php?logout" class="nav-link"><i class="fas fa-sign-out-alt" title="{t}Abmelde{/t}"></i></a></li>
    </ul>
</nav>