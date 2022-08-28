<body>
    <nav class="navbar navbar-light bg-light">
        {assign var="logoGfx" value='<picture class="logo" alt="sqStorage logo"><source srcset="img/sqstorage.webp" type="image/webp" width="205" height="56"><img src="img/sqstorage.png" width="205" height="56"></picture>'}
        {if isset($useRegistration) && !$useRegistration}
        <a href="{$urlBase}/index{$urlPostFix}">{$logoGfx}</a>
        {else if isset($SESSION.user) && (int)$SESSION.user.usergroupid === 2}
        <a href="{$urlBase}/inventory{$urlPostFix}">{$logoGfx}</a>
        {else if isset($SESSION.user) && (int)$SESSION.user.usergroupid !== 2}
        <a href="{$urlBase}/index{$urlPostFix}">{$logoGfx}</a>
        {/if}
        <ul class="nav">
            {$pages = ['index.php' => '', 'entry.php' => '', 'inventory.php' => '', 'categories.php' => '', 'transfer.php' => '', 'datafields.php' => '', 'settings.php' => '']}
            {$pages[$target] = 'activePage'}

            {if isset($useRegistration) && !$useRegistration}
            <li class="nav-item"><a href="{$urlBase}/entry{$urlPostFix}" class="nav-link {$pages['entry.php']}">{t}Eintragen{/t}</a></li>
            {else if isset($SESSION.user) && (int)$SESSION.user.usergroupid !== 2}
            <li class="nav-item"><a href="{$urlBase}/entry{$urlPostFix}" class="nav-link {$pages['entry.php']}">{t}Eintragen{/t}</a></li>
            {/if}

            <li class="nav-item"><a href="{$urlBase}/inventory{$urlPostFix}" class="nav-link {$pages['inventory.php']}">{t}Inventar{/t}</a></li>
            <li class="nav-item"><a href="{$urlBase}/categories{$urlPostFix}" class="nav-link {$pages['categories.php']}">{t}Kategorien{/t}</a></li>

            {if isset($useRegistration) && !$useRegistration}
            <li class="nav-item"><a href="{$urlBase}/transfer{$urlPostFix}" class="nav-link {$pages['transfer.php']}">{t}Transferieren{/t}</a></li>
            {else if isset($SESSION.user) && (int)$SESSION.user.usergroupid !== 2}
            <li class="nav-item"><a href="{$urlBase}/transfer{$urlPostFix}" class="nav-link {$pages['transfer.php']}">{t}Transferieren{/t}</a></li>
            {/if}

            {if isset($useRegistration) && !$useRegistration}
            <li class="nav-item"><a href="{$urlBase}/datafields{$urlPostFix}" class="nav-link {$pages['datafields.php']}">{t}Datenfelder{/t}</a></li>
            {else if isset($SESSION.user) && (int)$SESSION.user.usergroupid !== 2}
            <li class="nav-item"><a href="{$urlBase}/datafields{$urlPostFix}" class="nav-link {$pages['datafields.php']}">{t}Datenfelder{/t}</a></li>
            {/if}

            {if (isset($SESSION.user) && isset($SESSION.user.usergroupid) && (int)$SESSION.user.usergroupid === 1) || !$useRegistration}
            <li class="nav-item"><a href="{$urlBase}/settings{$urlPostFix}" class="nav-link {$pages['settings.php']}">{t}Einstellungen{/t}</a></li>
            {/if}
        </ul>

        <form class="form-inline searchArea" method="GET" action="{$urlBase}/inventory{$urlPostFix}">
            <input class="form-control mr-sm-2" name="searchValue" type="search" placeholder="{t}Suche{/t}" aria-label="{t}Suche{/t}">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">{t}Suchen{/t}</button>
        </form>


        <ul class="nav">
            {if isset($SESSION.user)}
            <li class="nav-item"><a href="{$urlBase}/usersettings" class="nav-link" title="{t}Benutzereinstellungen{/t}"><center><i class="fas fa-gears" title="{t}Benutzereinstellungen{/t}"></i><br /><small>{t}Benutzereinstellungen{/t}</small></center></a></li>
            <li class="nav-item"><a href="{$urlBase}/index{$urlPostFix}?logout" class="nav-link" title="{t}Abmelden{/t}"><center><i class="fas fa-sign-out-alt" title="{t}Abmelden{/t}"></i><br /><small>{t}Abmelden{/t}</small></center></a></li>
            {/if}
        </ul>

        <div class="dropdown">
            <select class="form-control mr-sm-2" name="lang">
                {foreach $langsAvailable as $lang}
                <option value="{$lang}" {if $langCurrent==$lang} selected="selected" {/if}>{$langsLabels.$lang}</option>
                {/foreach}
            </select>
            <script type="text/javascript">
                let langSelection = document.querySelector('select[name="lang"').addEventListener('change', function(evt) {
                    let langValue = evt.target.options[evt.target.selectedIndex].value
                    let srcUri = window.location.href.toString().replace(/.lang=.[^\&\/]*/, '')
                    if (srcUri.indexOf('?') === -1) window.location.href = srcUri + '?lang=' + langValue
                    else window.location.href = srcUri + '&lang=' + langValue
                })
            </script>
        </div>


    </nav>
    <p id="msgbox"></p>
