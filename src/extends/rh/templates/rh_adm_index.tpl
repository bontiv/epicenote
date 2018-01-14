{extends "event_head.tpl"}

{block "evt-bread"}
    <li class="active">Gestion RH</li>
    {/block}

{block "evt-content"}
    {$conf->getKey()}
    {if $conf->getKey()}
        <p>Le recrutement est activé</p>{$conf->getKey()}
    {else}
        Les candidatures ne sont pas activés.

        <a href="{mkurl action=rh_adm page=activate event=$event.event_id}" class="btn btn-primary">Activer</a>
    {/if}
{/block}