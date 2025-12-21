{include file="head.tpl" title="{t}Welcome!{/t}"}
{include file="nav.tpl" target="welcome.php" request=$REQUEST}
<style type="text/css">
    .introShortcuts {
        list-style: decimal;
        line-height: 1.8rem;
        margin-bottom: 3rem;
    }

    a.headLink {
        font-weight: bold;
        font-size: 1.5rem;
        margin-bottom: 2rem;
    }

    .content {
        margin: 3rem 2rem;
    }

    .content>article {
        margin-top: 1.6rem;
        margin-bottom: 0px;
        max-width: 98ch;
    }

    .content>article > h6 {
        margin-top: 2.2rem;
        margin-bottom: 0.7rem;
    }

    .content>article p {
        line-height: 1.7rem;
        font-size: 1.1rem;
    }

    .content>article>p>a {
        text-decoration: underline;
        font-weight: bold;
        padding-top: 1rem;
    }

    .content>hr {
        margin: 0em;
        margin-top: 1.3rem;
        margin-bottom: 1rem;
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