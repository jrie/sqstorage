{include file="head.tpl" title="{t}Einstellungen{/t}"}
{include file="nav.tpl" target="settings.php" request=$REQUEST}

<div class="content">
    {if $updatecheck}
      {if $uptodate}
        <div class="alert alert-success" role="alert">
          <h6>{t}sqStorage ist aktuell{/t}</h6>
        </div>
      {else}
        <div class="alert alert-danger" role="alert">
          <h6>{t}Es steht eine Aktualisierung zu Verfügung{/t}</h6>
          <a href="updater.php" title="{t}Aktualisierung{/t}">{t}Aktualisierung{/t}</a>
        </div>
      {/if}
    {else}
      <form method="POST" id="updatecheckform">
      <input type="hidden" id="install" name="target" value="updatecheck" />
      <button type="submit" class="btn btn-primary float-right">{t}Auf Updates prüfen{/t}</button>
      </form>
      <div class="clearfix"></div>
    {/if}


    {if $isEdit || $isAdd}
    {if strlen($error)>0}
    <div class="alert alert-danger" role="alert">
        <h6>{$error}</h6>
    </div>
    {/if}

    {if strlen($success)>0}
    <div class="alert alert-info" role="alert">
        <p>{$POST.username} {t}zur Datenbank hinzugefügt{/t}</p>
    </div>
    {/if}

    {if $isEdit || $error}
    <div class="alert alert-danger" role="alert">
        <h6>{t}Benutzer zur Bearbeitung:{/t} &quot;{$user.username}&quot;</h6>
    </div>
    {/if}

    <form accept-charset="utf-8" id="userform" method="POST" action="#">

        {if $isEdit} <input type="hidden" value="{$user.id}" name="userUpdateId" />{/if}

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">{t}Benutzername{/t}</span>
            </div>

            {if !$isEdit && !$error}
            <input type="text" name="username" maxlength="20" class="form-control" required="required" placeholder="{t}Benutzername{/t}" aria-label="{t}Benutzername{/t}" aria-describedby="basic-addon1">
            {else}
            <input type="text" name="username" maxlength="20" class="form-control" required="required" placeholder="{t}Benutzername{/t}" aria-label="{t}Benutzername{/t}" aria-describedby="basic-addon1" value="{$user.username}">
            {/if}
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon7">{t}E-Mail{/t}</span>
            </div>

            {if !$isEdit && !$error}
            <input type="email" name="mailaddress" maxlength="254" class="form-control" autocomplete="off" placeholder="{t}E-Mail{/t}" aria-label="{t}E-Mail{/t}" aria-describedby="basic-addon7">
            {else}
            <input type="email" name="mailaddress" maxlength="254" class="form-control" autocomplete="off" placeholder="{t}E-Mail{/t}" aria-label="{t}E-Mail{/t}" aria-describedby="basic-addon7" value="{$user.mailaddress}">
            {/if}
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="dropdown">
                    <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="usergroupDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <option value="-1" selected="selected">{t}Benutzergruppe{/t}</option>
                        {$currentUsergroup=NULL}
                        {foreach $usergroups as $usergroup}
                        {if ($isEdit || $error) && $user.usergroupid == $usergroup.id}
                        {$currentUsergroup=$usergroup}
                        {/if}
                        <option value="{$usergroup.id}">{t}{$usergroup.name}{/t}</option>
                        {/foreach}
                    </select>
                </div>
            </div>

            {if (!$isEdit && !$error) || $currentUsergroup == null}
            <input type="text" class="form-control" id="usergroupname" name="usergroupname" readonly="readonly" required="required" autocomplete="off" placeholder="{t}Benutzergruppe{/t}">
            <input type="hidden" value="" id="usergroupid" name="usergroupid" />
            {else}
            <input type="text" class="form-control" id="usergroupname" name="usergroupname" readonly="readonly" required="required" autocomplete="off" placeholder="{t}Benutzergruppe{/t} " value="{$user.usergroupname}">
            <input type="hidden" value="{$user.usergroupid}" id="usergroupid" name="usergroupid" />
            {/if}
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="dropdown">
                    <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="userapiDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <option value="-1" selected="selected">{t}API Zugriff{/t}</option>
                        <option value="0">{t}verbieten{/t}</option>
                        <option value="1">{t}erlauben{/t}</option>
                    </select>
                </div>
            </div>
            {if (!$isEdit && !$error) || $currentUsergroup == null}
            <input type="text" class="form-control" id="userapi" name="userapi" readonly="readonly" required="required" autocomplete="off" placeholder="{t}API Zugriff{/t}">
            <input type="hidden" value="" id="userapikey" name="userapikey" />
            {else}
            <input type="text" class="form-control" id="userapi" name="userapi" readonly="readonly" required="required" autocomplete="off" placeholder="{t}API Zugriff{/t} " value="{if $user.api_access == 1}{t}erlauben{/t}{else}{t}verbieten{/t}{/if}">
            <input type="hidden" value="{$user.api_access}" id="userapikey" name="userapikey" />
            {/if}
        </div>

        <div style="float: right;">
            {if $isEdit}
            <button type="submit" class="btn btn-danger">{t}Überschreiben{/t}</button>
            {else}
            <button type="submit" class="btn btn-primary">{t}Eintragen{/t}</button>
            {/if}
        </div>
    </form>
    {else}
    {if strlen($error)>0}
    <div class="alert alert-danger" role="alert">
        <h6>{$error}</h6>
    </div>
    {/if}

    {if $update_available}
    <div class="alert alert-danger" role="alert">
        <h6></i><a href='install.php'><i class="fa fa-sync"></i>{t}Bitte die Datenbank aktualisieren{/t}<i class="fa fa-sync"></i></a></h6>
    </div>
    {/if}

    {if $useRegistration}
    {if !isset($users.0.api_access)}<a href='install.php'>{t}Bitte die Datenbank aktualisieren{/t}</a>{else}<a class="btn btn-primary addUser" href="{$urlBase}/settings{$urlPostFix}?addUser">{t}Neuer Benutzer{/t}</a>{/if}
    <hr />
    <ul class="categories list-group">
        <li class="alert alert-info"><span class="list-span">{t}Benutzername{/t}</span><span class="list-span">{t}E-Mail{/t}</span><span class="list-span">{t}Gruppe{/t}</span><span class="list-span">{t}API Zugriff{/t}</span><span class="list-span">{t}Aktionen{/t}</span></li>
        {foreach $users as $user}
        <li class="list-group-item"><a title="{t}Benutzer löschen{/t}" name="removeUser" data-name="{$user.username}" data-id="{$user.usergroupid}" href="{$urlBase}/settings{$urlPostFix}?removeUser={$user.id}" class="removalButton fas fa-times-circle btn"></a><span class="list-span">{$user.username}</span><span class="list-span">{$user.mailaddress}</span><span class="list-span">{$user.usergroupname}</span><span class="list-span">{if !isset($user.api_access)}{t}Bitte die Datenbank aktualisieren{/t}{else}{if $user.api_access == 1}<i class="fas fa-circle-check"></i>{else}<i class="fas fa-ban"></i>{/if}{/if}</span><a class="fas fa-edit editUser" href="#" name="editUser" data-name="{$user.username}" data-id="{$user.id}"></a></li>
        {/foreach}
    </ul>
    <hr />

    <form accept-charset="utf-8" id="mailform" method="POST" action="">
        <input type="hidden" id="mail" name="target" value="mail" />
        <ul class="categories list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}E-Mailserver-Einstellungen{/t}</span>
            </li>
            <li class="list-group-item">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon7">{t}Absender{/t}</span>
                    </div>
                    <input type="email" name="senderAddress" maxlength="254" class="form-control" autocomplete="off" placeholder="email@example.com" aria-label="Absender" aria-describedby="basic-addon7" value="{$mailSettings.senderAddress}">
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon7">{t}E-Mailversand{/t}</span>
                    </div>
                    <div class="form-check form-check-inline ml-3">
                        <input class="form-check-input" type="radio" name="mail_enabled" id="mail_enabled_off" value="false" {if !$mailSettings.enabled}checked="checked" {/if}>
                        <label class="form-check-label" for="mail_enabled_off">{t}deaktivieren{/t}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mail_enabled" id="mail_enabled_on" value="true" {if $mailSettings.enabled}checked="checked" {/if}>
                        <label class="form-check-label" for="mail_enabled_on">{t}aktivieren{/t}</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right">{t}Einstellungen speichern{/t}</button>
            </li>
        </ul>
        <div class="clearfix"></div>
    </form>
    <hr />
    {/if}
    <form accept-charset="utf-8" id="updateForm" method="POST" action="">
        <input type="hidden" id="install" name="target" value="install" />
        <ul class="categories list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}Installation und Aktualisierung{/t}</span>
            </li>
            <li class="list-group-item">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon7">{t}Installation{/t}</span>
                    </div>
                    <div class="form-check form-check-inline ml-3">
                        <input class="form-check-input" type="radio" name="allow_install" id="install_off" value="deny" {if $install_allowed}checked="checked" {/if}>
                        <label class="form-check-label" for="install_off">{t}verbieten{/t}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="allow_install" id="install_on" value="allow" {if !$install_allowed}checked="checked" {/if}>
                        <label class="form-check-label" for="install_on">{t}erlauben{/t}</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right">{t}Einstellungen speichern{/t}</button>
            </li>
        </ul>
        <div class="clearfix"></div>
    </form>
    <hr />
    <form accept-charset="utf-8" id="startpage" method="POST" action="">
        <input type="hidden" id="install" name="target" value="startpage" />
        <ul class="categories list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}Standard-Startseite{/t}</span>
            </li>
            <li class="list-group-item">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="startpageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <option value="-1" selected="selected">{t}Startseite{/t}</option>
                                {foreach $pages as $pagename => $pagelabel}
                                {if $pagename == $defaultStartPage}
                                    {$sel = "selected='selectd'"}
                                {else}
                                    {$sel = ""}
                                {/if}
                                <option value="{$pagename}" {$sel} >{t}{$pagelabel}{/t}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <input type="text" class="form-control" id="startpagename" name="startpagename" readonly="readonly" required="required" autocomplete="off" placeholder="{t}Startseite{/t} " value="{$pages.$defaultStartPage}">
                    <input type="hidden" value="{$defaultStartPage}" id="startpagekey" name="startpagekey" />
                </div>
                <button type="submit" class="btn btn-primary float-right">{t}Einstellungen speichern{/t}</button>
            </li>
        </ul>
        <div class="clearfix"></div>
    </form>


    <form accept-charset="utf-8" id="updater" method="POST" action="">
        <input type="hidden" id="install" name="target" value="updater" />
        <ul class="categories list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}Update-Quelle{/t}</span>
                <small>
                  <br><span><b>{t}Verfügbare Update-Quellen{/t}</b></span>
                  <br><span>{t}Release{/t} - {t}Getestete Veröffentlichung{/t}</span>
                  <br><span>{t}Betatest{/t} - {t}Noch nich veröffentlichte Funktionen, aber möglicherweise auch mit Fehlern{/t}</span>
                  <br><span>{t}Entwicklung{/t} - {t}Aktueller Stand der Entwicklung - mit Vorsicht zu geniesen - Fehler eingeschlossen{/t}</span>
                </small>
            </li>
            <li class="list-group-item">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="dropdown">
                            <select class="btn dropdown-toggle" tabindex="-1" autocomplete="off" type="button" id="branchDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <option value="-1" selected="selected">{t}Update-Quelle{/t}</option>
                                {foreach $settingdata.updater.branches  as $branch => $branchlabel}
                                {if $branch == $settingdata.updater.githubbranch}
                                    {$sel = "selected='selected'"}
                                    {$outputlabel = $branchlabel}
                                {else}
                                    {$sel = ""}
                                {/if}
                                <option class="branchselect" value="{$branch}" {$sel} >{t}{$branchlabel}{/t}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <input type="text" class="form-control" id="branchlabel" name="branchlabel" readonly="readonly" required="required" autocomplete="off" placeholder="{t}Version{/t} " value="{$outputlabel}">
                    <input type="hidden" value="{$settingdata.updater.githubbranch}" id="branch" name="branch" />
                </div>
                <button id="updaterunlockbutton" class="btn btn-primary float-left" onclick="unlockupdater();return false;">{t}Updatequellen-Auswahl aktivieren{/t}</button>
                <button type="submit" id="updaterbutton" class="btn btn-primary float-right" disabled>{t}Einstellungen speichern{/t}</button>
            </li>
        </ul>
        <div class="clearfix"></div>
    </form>



    {/if}
</div>



{$target = "transfer.php"}

{include file="footer.tpl"}
{literal}
<script type="text/javascript">
    let removalButtons = document.querySelectorAll('.removalButton')
    let countAdmins = 0;
    for (let button of removalButtons) {
        countAdmins = countAdmins + (button.getAttribute('data-id') == 1 ? 1 : 0)
        button.addEventListener('click', function(evt) {
            let isLastAdmin = countAdmins == 1 && evt.target.getAttribute('data-id') == 1;
            let targetType = evt.target.name === 'removeUser' && !isLastAdmin ? '{/literal}{t}Benutzer wirklich entfernen?{/t}{literal}' : '{/literal}{t}Der letzte Administrator kann nicht gelöscht werden!{/t}{literal}'
            if (!isLastAdmin) {
                if (!window.confirm(targetType + ' "' + evt.target.dataset['name'] + '"')) {
                    evt.preventDefault()
                }
            } else {
                window.alert(targetType)
                evt.preventDefault()
            }
        })
    }

    let editUserButtons = document.querySelectorAll('.editUser')
    for (let button of editUserButtons) {
        button.addEventListener('click', function(evt) {
            if (evt.target.name === 'editUser') window.location.href = '{/literal}{$urlBase}{literal}/settings{/literal}{$urlPostFix}{literal}?editUser=' + evt.target.dataset['id']

            return false
        })
    }

    if (document.querySelector('#usergroupDropdown') !== null) {
        document.querySelector('#usergroupDropdown').addEventListener('change', function(evt) {
            let usergroupname = document.querySelector('#usergroupname')
            let usergroupid = document.querySelector('#usergroupid')
            if (parseInt(evt.target.value) === -1) {
                usergroupname.value = ''
                return
            }
            usergroupname.value = evt.target.options[evt.target.selectedIndex].text
            usergroupid.value = evt.target.value
            evt.target.value = '-1'
        })
    }


    if (document.querySelector('#userapiDropdown') !== null) {
        document.querySelector('#userapiDropdown').addEventListener('change', function(evt) {
            let usergroupname = document.querySelector('#userapi')
            let usergroupid = document.querySelector('#userapikey')
            if (parseInt(evt.target.value) === -1) {
                usergroupname.value = ''
                return
            }
            usergroupname.value = evt.target.options[evt.target.selectedIndex].text
            usergroupid.value = evt.target.value
            evt.target.value = '-1'
        })
    }

    if (document.querySelector('#startpageDropdown') !== null) {
        document.querySelector('#startpageDropdown').addEventListener('change', function(evt) {
            let startpage = document.querySelector('#startpagekey')
            if (parseInt(evt.target.value) === -1) {
                document.querySelector('#startpagename').value = ''
                return
            }
            document.querySelector('#startpagename').value = evt.target.options[evt.target.selectedIndex].text
            startpage.value = evt.target.value
            evt.target.value = '-1'
        })
    }

    if (document.querySelector('#branchDropdown') !== null) {
        document.querySelector('#branchDropdown').addEventListener('change', function(evt) {
            if (parseInt(evt.target.value) === -1) {
                branch.value = ''
                return
            }

            document.querySelector('#branchlabel').value = evt.target.options[evt.target.selectedIndex].text
            document.querySelector('#branch').value = evt.target.value
            evt.target.value = '-1'
        })
    }

    function unlockupdater() {
        document.querySelector('#updaterbutton').disabled = !document.querySelector('#updaterbutton').disabled;
        if (document.querySelector('#updaterbutton').disabled) {
            document.querySelector('#updaterunlockbutton').innerHTML = {/literal}'{t}Updatequellen-Auswahl aktivieren{/t}'{literal}
        } else {
            document.querySelector('#updaterunlockbutton').innerHTML = {/literal}'{t}Updatequellen-Auswahl deaktivieren{/t}'{literal}
        }
    }

</script>
{/literal}
{include file="bodyend.tpl"}
