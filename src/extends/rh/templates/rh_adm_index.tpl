{extends "event_head.tpl"}

{block "evt-bread"}
    <li class="active">Gestion RH</li>
    {/block}

{block "evt-content"}
    {if isset($conf)}
        <ul class="nav nav-pills">
            <li role="presentation"{if $smarty.get.page == "index"} class="active"{/if}><a href="{mkurl action="rh_adm" page="index" event=$conf->raw_re_event}">Accueil</a></li>
            <li role="presentation"{if $smarty.get.page == "cdt"} class="active"{/if}><a href="{mkurl action="rh_adm" page="cdt" event=$conf->raw_re_event}">Candidats</a></li>
            <li role="presentation"{if $smarty.get.page == "section"} class="active"{/if}><a href="{mkurl action="rh_adm" page="section" event=$conf->raw_re_event}">Sections</a></li>
            <li role="presentation"{if $smarty.get.page == "form"} class="active"{/if}><a href="{mkurl action="rh_adm" page="form" event=$conf->raw_re_event}">Formulaire</a></li>
        </ul>

        {block rhcontent}
        <p>Le recrutement est activé</p>{$conf->getKey()}
        {/block}
    {else}
        Les candidatures ne sont pas activés.

        <a href="{mkurl action=rh_adm page=activate event=$event.event_id}" class="btn btn-primary">Activer</a>
    {/if}
{/block}