{include file="head.tpl" title="{t}Aktualisierungs-Manager{/t}"}
{include file="nav.tpl" target="updater.php" request=$REQUEST}

<div class="content">


{if $install_allowed}

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
    </form>


    <form accept-charset="utf-8" id="updater" method="POST" action="">
        <input type="hidden" id="install" name="target" value="updater" />
        <ul class="categories list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}Update-Quelle{/t}</span>
                <small>
                  <br /><span><b>{t}Verfügbare Update-Quellen{/t}</b></span>
                  <br /><span>{t}Release{/t} - {t}Getestete Veröffentlichung{/t}</span>
                  <br /><span>{t}Betatest{/t} - {t}Noch nich veröffentlichte Funktionen, aber möglicherweise auch mit Fehlern{/t}</span>
                  <br /><span>{t}Entwicklung{/t} - {t}Aktueller Stand der Entwicklung - mit Vorsicht zu geniesen - Fehler eingeschlossen{/t}</span>
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
                                    {$sel = "selected='selectd'"}
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
    </form>
{else}
        <div class="alert alert-danger" role="alert">
          <h6>{t}Die Installation ist momentan nicht erlaubt - bitte zuerst in den Einstellungen aktivieren{/t}</h6>
        </div>
{/if}


</div>



{$target = "transfer.php"}

{include file="footer.tpl"}
{literal}
<script type="text/javascript">

</script>
{/literal}
{include file="bodyend.tpl"}
