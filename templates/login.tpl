{include file="head.tpl" title="{t}Login und Registrierung{/t}"}
<nav class="navbar navbar-light bg-light">
    {assign var="logoGfx" value='<picture class="logo" alt="sqStorage logo">
        <source srcset=".//img/sqstorage.webp" type="image/webp" width="205" height="56"><img src=".//img/sqstorage.png" width="205" height="56">
    </picture>'}

    <a href="{$urlBase}/{$urlPostFix}">{$logoGfx}</a>

    <div class="dropdown">
        <select class="form-control mr-sm-2" name="lang">
            {foreach $langsAvailable as $lang}
                <option value="{$lang}" {if $langCurrent == $lang} selected="selected" {/if}>{$langsLabels.$lang}</option>
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

<div class="content">
    <div class="login-box">
	{if isset($error)}
           <div class="statusDisplay red">{$error}</div>
        {/if}
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">
                    {if $createFirstAdmin}
                        {t}Neue Admin-Zugangsdaten eingeben{/t}
                    {else}
                        {if $showActivation}
                            {t}Neue Zugangsdaten eingeben{/t}
                        {else}
                            {t}Zugangsdaten eingeben{/t}
                    {/if}
                </p>
                {/if}
                <form action="{$urlBase}/login{$urlPostFix}{if $showActivation}?activate={$activate}{/if}{if $showRecover}?recover{/if}" method="post">
                    <div class="input-group mb-3">
                        <input type="text" id="username" name="username" class="form-control" placeholder="{t}Benutzername{/t}" value="{if ($showActivation || $showRecover)}{if isset($user.username)}{$user.username}{/if}{else}{if isset($POST.username)}{$POST.username}{/if}{/if}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    {if ($showRecover || $createFirstAdmin)}
                        <div class="input-group mb-3">
                            <input type="email" id="mailaddress" name="mailaddress" class="form-control" placeholder="{t}E-Mail{/t}" value="{if isset($POST.mailaddress)}{$POST.mailaddress}{/if}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                    {/if}
                    {if (!$showRecover)}


                        <div class="input-group mb-3">
                            <input type="password" id="password" name="password" class="form-control" placeholder="{t}Passwort{/t}" value="{if $showActivation}{if isset($POST.password)}{$POST.password}{/if}{/if}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        {if $showActivation}
                            <div class="input-group mb-3">
                                <input type="password" id="password_repeat" name="password_repeat" class="form-control" placeholder="{t}Passwort wiederholen{/t}" value="{if $showActivation}{if isset($POST.password_repeat)}{$POST.password_repeat}{/if}{/if}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        <div class="row">
                            <div class="form-group form-check login">
                                    <div>
                                    <input type="checkbox" id="remember" class="form-check-input">
                                    <label for="remember" class="form-check-label">
                                        {t}Angemeldet bleiben?{/t}
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-flat">{if $showActivation}{t}Speichern{/t}{else}{t}Anmelden{/t}{/if}</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>


                    {else}
                    <div class="login">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{t}Anfordern{/t}</button>
                    </div>
                    {/if}

                </form>
                {if !$showActivation}
                    {if !$showRecover}
                        <p class="mb-1">
                            <a href="{$urlBase}/login{$urlPostFix}?recover">{t}Zugangsdaten vergessen{/t}</a>
                        </p>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</div>

{include file="footer.tpl"}

{include file="bodyend.tpl"}