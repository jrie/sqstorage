{include file="head.tpl" title="{t}Welcome!{/t}"}
{include file="nav.tpl" target="welcome.php" request=$REQUEST}
<style type="text/css">
    .introShortcuts {
        list-style: decimal;
        line-height: 1.7em;
    }

    a.headLink {
        font-weight: bold;
        font-size: 1.5em;
    }

    .content {
        margin: 3em 8em;
    }

    .content>article {
        margin-top: 4em;
        margin-bottom: 0px;
        max-width: 100ch;
    }

    .content>article p {
        line-height: 1.4;
        font-size: 1.2em;
    }

    .content>article>p>a {
        text-decoration: underline;
        font-weight: bold;
    }

    .content>hr {
        margin: 0em;
        margin-bottom: 3em;
    }

    .credits {
        list-style: none;
        line-height: 2em;
        padding: 0px;
    }

    .credits {
        font-size: 1em;
        margin-bottom: 5em;
        text-align: left;
    }

    .credits .name {
        font-weight: bold;
        margin-right: 1ch;
    }

    .credits a {
        font-style: italic;
    }
</style>
{if file_exists("./welcome_lang/{$langShortCode}.html")}
{include file="welcome_lang/{$langShortCode}.html"}
{else}
{include file="welcome_lang/en.html"}
{/if}

{include file="footer.tpl"}
{literal}
<script type="text/javascript">
</script>
{/literal}
{include file="bodyend.tpl"}