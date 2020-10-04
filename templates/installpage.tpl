{include file="head.tpl" title="{t}Installation{/t}"}
{include file="nav.tpl" target="index.php" request=$REQUEST}

        <center><h2>{t}sqStorage Installation / Aktualiserung{/t}</h2></center>

        {if count($error) > 0}

            <div class="alert alert-danger" role="alert">
                {foreach $error as $err}
                <h6>{$err}</h6>
                {/foreach}
            </div>


        {/if}

        {if count($successes)>0}
            <div class="alert alert-success" role="success">
                {foreach $successes as $suc}
                <h6>{$suc}</h6>
                {/foreach}
            </div>
        {/if}



          <div class="content">
            {if $success}
            <div class="statusDisplay green" role="alert">
                <p>"{if isset($POST.label)}{$POST.label}{/if}" {t}zur Datenbank hinzugefügt.{/t}</p>
            </div>
            {/if}



            <div id="errorForm" class="alert alert-danger hidden" role="alert">{t}Nicht gespeichert, es befinden sich Fehler in der Formular-Eingabe.{/t}</div>

            <form class="inputForm" accept-charset="utf-8" method="POST" action="install.php">

            {if $dbform}

                <input type="hidden" name="dbset" value="1">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{t}Datenbank-Benutzer{/t}</span>
                    </div>
                        <input type="text" name="dbuser" maxlength="255" class="form-control" required="required" placeholder="tlvUser" aria-label="{t}Datenbank-Benutzer{/t}" aria-describedby="basic-addon1" value="{if isset($POST.dbuser)}{$POST.dbuser}{else}tlvUser{/if}">
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{t}Datenbank-Passwort{/t}</span>
                    </div>
                        <input type="text" name="dbpass" maxlength="1024" class="form-control" required="required" placeholder="tlvUser" aria-label="{t}Datenbank-Passwort{/t}" aria-describedby="basic-addon1" value="{if isset($POST.dbpass)}{$POST.dbpass}{else}tlvUser{/if}">
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{t}Datenbank-Name{/t}</span>
                    </div>
                        <input type="text" name="dbname" maxlength="255" class="form-control" required="required" placeholder="tlv" aria-label="{t}Datenbank-Name{/t}" aria-describedby="basic-addon1" value="{if isset($POST.dbname)}{$POST.dbname}{else}tlv{/if}">
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{t}Datenbank-Server{/t}</span>
                    </div>
                        <input type="text" name="dbhost" maxlength="255" class="form-control" required="required" placeholder="localhost" aria-label="{t}Datenbank-Server{/t}" aria-describedby="basic-addon1" value="{if isset($POST.dbhost)}{$POST.dbhost}{else}localhost{/if}">
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">{t}Datenbank-Port{/t}</span>
                    </div>
                        <input type="text" name="dbport" maxlength="5" class="form-control" required="required" placeholder="3306" aria-label="{t}Datenbank-Port{/t}" aria-describedby="basic-addon1" value="{if isset($POST.dbport)}{$POST.dbport}{else}3306{/if}">
                </div>

                <input type="submit" class="btn form-control btn-success" value="{t}Verbinungsdaten eintragen{/t}">
            {else}
                {if count($MigMessages) < 1}
                <input type="hidden" name="dbwork" value="1">
                <input type="submit" value="{t}Datenbank installieren / aktualisieren{/t}" class="btn form-control btn-success">
                {else}

                        <div class="alert alert-success" role="success">
                            {foreach $MigMessages as $mig}
                            <h6>{$mig}</h6>
                            {/foreach}
                        </div>

                {/if}
            {/if}


            </form>
        </div>





{include file="footer.tpl"}
{literal}

{/literal}
{include file="bodyend.tpl"}
