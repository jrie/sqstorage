{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">
            {$done=false}
            {if isset($error)}{if strlen($error)>1}
            {$done=true}
            <div class="alert alert-danger" role="alert">
                <h6>{$error}</h6>
            </div>
            {/if}{/if}

            {if !$done}
            <div class="alert alert-danger" role="alert">
                <h6>
                    {t}Zugriff verweigert!{/t}
                </h6>
            </div>
            {/if}


        </div>

{include file="footer.tpl"}

{include file="bodyend.tpl"}