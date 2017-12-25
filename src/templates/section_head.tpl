<h1>Sections</h1>
<h2>Section {$section->section_name}</h2>
<p>Section crée par {if $section->section_owner!=false}{$section->section_owner->user_name|escape}{else}<i class="disabled">annonyme</i>{/if}. C'est une {if $section->section_type=="primary"}section principale{else}sous section{/if}.</p>

{if $section->section_ml}
    <p>Cette section possède une ML. Vous pouvez contacter les responsables à
        l'addresse : <a href="mailto:{$section->section_ml}">{$section->section_ml}</a>.
    </p>
{/if}

<div class="btn-group">
    {if isset($_user.sections[$section->section_id])}
        <a href="{mkurl action="section" page="goout" section=$section->section_id}" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Quitter</a>
    {else}
        <a href="{mkurl action="section" page="goin" section=$section->section_id}" class="btn btn-info"><i class="glyphicon glyphicon-heart"></i> Adhérer</a>
    {/if}
    <a href="{mkurl action="section" page="mkevent" section=$section->section_id}" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Créer event</a>
    <a href="{mkurl action="section" page="addpoints" section=$section->section_id}" class="btn btn-default"><span class="glyphicon glyphicon-gift"></span> Donner des points</a>
    <a href="{mkurl action="section" page="trombi" section=$section->section_id}" class="btn btn-default"><span class="glyphicon glyphicon-camera"></span> Trombinoscope</a>
    {if $section->raw_section_type == "primary"}
        <a href="{mkurl action="section" page="team_add" section=$section->section_id}" class="btn btn-default"><span class="glyphicon glyphicon-briefcase"></span> Nouvelle équipe</a>
    {/if}
</div>

{* Les onglets *}
<ul class="nav nav-pills" style="margin-top: 20px;">
    <li{if $smarty.get.page=='details'} class="active"{/if}>
        <a href="{mkurl action=section page=details section=$section->section_id}">Accueil</a>
    </li>
    <li{if $smarty.get.page=='activities'} class="active"{/if}>
        <a href="{mkurl action=section page=activities section=$section->section_id}">Activités</a>
    </li>
    <li{if $smarty.get.page=='events'} class="active"{/if}>
        <a href="{mkurl action=section page=events section=$section->section_id}">Evénements</a>
    </li>
    <li{if $smarty.get.page=='mls' or $smarty.get.page=='send'} class="active"{/if}>
        <a href="{mkurl action=section page=mls section=$section->section_id}">Grps. diffusion</a>
    </li>
    {if $section->raw_section_type == "primary"}
        <li{if $smarty.get.page=='teams'} class="active"{/if}>
            <a href="{mkurl action=section page=teams section=$section->section_id}">Équipes</a>
        </li>
    {else}
        <li{if $smarty.get.page=='acl'} class="active"{/if}>
            <a href="{mkurl action=section page=acl section=$section->section_id}">Droits</a>
        </li>
    {/if}
</ul>
