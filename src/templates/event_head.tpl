{include "head.tpl"}
<h1>Fiche d'événement : {$event.event_name}</h1>

<ol class="breadcrumb">
    <li><a href="{mkurl action="event"}">Events</a></li>
        {if $smarty.get.page != "view"}
        <li><a href="{mkurl action="event" page="view" event=$event.event_id}">{$event.event_name}</a></li>
            {block "evt-bread"}
            <li class="active">
                {if $smarty.get.page == "sections"}
                    Sections
                {elseif $smarty.get.page == "bocal_list"}
                    Bocal
                {elseif $smarty.get.page == "ews_list"}
                    Sites
                {else}
                    Configuration
                {/if}
            </li>
        {/block}
    {else}
        <li class="active">{$event.event_name}</li>
        {/if}
</ol>


<div class="container">
    <div class="row">
        <div class="col-md-2">
            <!-- Nav tabs -->
            <ul class="nav nav-pills nav-stacked" role="tablist">
                <li class="{if $smarty.get.page == "view"}active{/if}"><a href="{mkurl action="event" page="view" event=$event.event_id}">Informations</a></li>
                <li class="{if $smarty.get.page == "sections"}active{/if}"><a href="{mkurl action="event" page="sections" event=$event.event_id}">Sections</a></li>
                <li class="disabled"><a>Participants</a></li>
                <li class="disabled"><a>Fiche event</a></li>
                <li class="{if $smarty.get.page == "bocal_list"}active{/if}"><a href="{mkurl action="event" page="bocal_list" event=$event.event_id}">Tickets Bocal</a></li>
                <li class="{if $smarty.get.page == "ews_list"}active{/if}"><a href="{mkurl action="event" page="ews_list" event=$event.event_id}">Préinscription</a></li>
                {mkmenu menu="event" event=$event.event_id}
            </ul>            
        </div>
        <div class="col-md-10">
            {block "evt-content"}
                <h2>Description</h2>
                <p>
                    <strong>Event :</strong> {$event.event_name}<br/>
                    <strong>Description :</strong> {$event.event_desc}<br/>
                    <strong>Début :</strong> {$event.event_start}<br/>
                    <strong>Fin :</strong> {$event.event_end}<br/>
                    <strong>Verrouillage des inscriptions :</strong> {$event.event_lock}<br/>
                    <strong>Première étape de notation :</strong> {$event.event_note1}<br/>
                    <strong>Deuxième étape de notation :</strong> {$event.event_note2}<br/>
                    <strong>Créateur de l'événement :</strong> <a href="{mkurl action="user" page="view" user=$event.user_id}">{$event.user_name|escape}</a><br/>
                    <strong>Evénement de la section :</strong> <a href="{mkurl action="section" page="details" section=$event.section_id}">{$event.section_name}</a><br/>
                    <strong>Coef de l'évent :</strong> {$event.event_coef}<br/>
                </p>

                <div class="btn-group">
                    <a class="btn btn-danger" href="{mkurl action="event" page="delete" event=$event.event_id}">Supprimer</a>
                    <a class="btn btn-warning" href="{mkurl action="event" page="edit" event=$event.event_id}">Modifier</a>
                </div>
            {/block}                    
        </div>
    </div>
</div>
{include "foot.tpl"}