{include file="head.tpl" title="{t}Benutzereinstellungen{/t}"}
{include file="nav.tpl" target="usersettings.php" request=$REQUEST}

<div class="content">


    {if count($error)>0}
    <div class="alert alert-danger" role="alert">
      {foreach $error as $singleerror}
        <h6>{$singleerror}</h6>
      {/foreach}
    </div>
    {/if}

    {if strlen($success)>0}
    <div class="alert alert-info" role="alert">
        <p>{$success}</p>
    </div>
    {/if}


    <form accept-charset="utf-8" id="startpage" method="POST" action="">
        <input type="hidden" id="install" name="target" value="startpage" />
        <ul class="categories list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}Startseite{/t}</span>
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
    </form>
  <hr />


    <form accept-charset="utf-8" id="startpage" method="POST" action="">
        <input type="hidden" id="install" name="target" value="passwordchange" />
        <ul class="categories list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}sqStorage Passwortänderung{/t}</span>
            </li>
            <li class="list-group-item">
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">{t}Aktuelles Passwort{/t}</span>
                  </div>

                  <input type="password" name="oldPassword" maxlength="254" class="form-control" autocomplete="off" placeholder="{t}Aktuelles Passwort{/t}" aria-label="{t}Aktuelles Passwort{/t}" aria-describedby="basic-addon1">

                  <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon2">{t}Neues Passwort{/t}</span>
                  </div>

                  <input type="password" name="newPassword1" maxlength="254" class="form-control" autocomplete="off" placeholder="{t}Neues Passwort{/t}" aria-label="{t}Neues Passwort{/t}" aria-describedby="basic-addon2">

                  <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon3">{t}Passwort wiederholen{/t}</span>
                  </div>

                  <input type="password" name="newPassword2" maxlength="254" class="form-control" autocomplete="off" placeholder="{t}Passwort wiederholen{/t}" aria-label="{t}Passwort wiederholen{/t}" aria-describedby="basic-addon3">


              </div>


              <button type="submit" class="btn btn-primary float-right">{t}Neues Passwort speichern{/t}</button>
            </li>
        </ul>
    </form>
  <hr />


</div><!-- end of <div class="content"> -->



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
    let usergrpdropdown = document.querySelector('#usergroupDropdown')
    if (usergrpdropdown !== null) {
        usergrpdropdown.addEventListener('change', function(evt) {
            let usergroupdropdown = evt.target
            let usergroupname = document.querySelector('#usergroupname')
            let usergroupid = document.querySelector('#usergroupid')
            if (parseInt(usergroupdropdown.value) === -1) {
                usergroupname.value = ''
                return
            }
            usergroupname.value = usergroupdropdown.options[usergroupdropdown.selectedIndex].text
            usergroupid.value = usergroupdropdown.value
            usergroupdropdown.value = '-1'
        })
    }


    let userapidropdown = document.querySelector('#userapiDropdown')
    if (userapidropdown !== null) {
        userapidropdown.addEventListener('change', function(evt) {
            let userapidropdown = evt.target
            let usergroupname = document.querySelector('#userapi')
            let usergroupid = document.querySelector('#userapikey')
            if (parseInt(userapidropdown.value) === -1) {
                usergroupname.value = ''
                return
            }
            usergroupname.value = userapidropdown.options[userapidropdown.selectedIndex].text
            usergroupid.value = userapidropdown.value
            userapidropdown.value = '-1'
        })
    }

    let startpageselectdropdown = document.querySelector('#startpageDropdown')
    if (startpageselectdropdown !== null) {
        startpageselectdropdown.addEventListener('change', function(evt) {
            let startpageselectdropdown = evt.target
            let startpagename = document.querySelector('#startpagename')
            let startpage = document.querySelector('#startpagekey')
            if (parseInt(startpageselectdropdown.value) === -1) {
                startpagename.value = ''
                return
            }
            startpagename.value = startpageselectdropdown.options[startpageselectdropdown.selectedIndex].text
            startpage.value = startpageselectdropdown.value
            startpageselectdropdown.value = '-1'
        })
    }

</script>
{/literal}
{include file="bodyend.tpl"}
