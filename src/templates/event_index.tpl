{include "head.tpl"}
<ol class="breadcrumb">
    <li class="active">Events</li>
</ol>

<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="glyphicon glyphicon-plus"></span>
        Ajouter <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        {if $_user.user_role eq 'ADMINISTRATOR'}
            {foreach $sections as $sect}
                <li><a href="{mkurl action="section" page="mkevent" section=$sect->section_id}">{$sect->section_name}</a></li>
            {/foreach}
        {else}
            {foreach $_user.sections as $sect}
                <li {if $sect.us_type!="manager"}class="disabled"{/if}><a href="{mkurl action="section" page="mkevent" section=$sect.us_section}">{$sect.section_name}</a></li>
                {foreachelse}
                <li class="disabled"><a href="#">Aucune section</a></li>
                {/foreach}
            {/if}
    </ul>
</div>

<h1>Events</h1>
<im>Pour créer un event, passez par la page de votre section.</im>

<p>
<ul class="nav nav-pills">
    <li role="presentation"{if $smarty.request.page!="archive"} class="active"{/if}><a href="{mkurl action="event"}">A venir</a></li>
    <li role="presentation"{if $smarty.request.page=="archive"} class="active"{/if}><a href="{mkurl action="event" page="archive"}">Passé</a></li>
</ul>
</p>

{if $ptable.total == 0}
    <div class="alert alert-info">
        Il n'y a aucun événement dans cette catégorie.
    </div>
{else}
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Evenement</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Description</th>
                <th>Coef</th>
                <th style="min-width: 100px">Action</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$ptable.rows item="line"}
                <tr>
                    <td><a href="{mkurl action="event" page="view" event=$line.event_id}">{$line.event_name}</a></td>
                    <td>{$line.event_start}</td>
                    <td>{$line.event_end}</td>
                    <td>{$line.event_desc}</td>
                    <td>{$line.event_coef}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{mkurl action="event" page="delete" event=$line.event_id}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                            <a href="{mkurl action="event" page="edit" event=$line.event_id}" class="btn btn-warning"><i class="glyphicon glyphicon-pencil"></i></a>
                        </div>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}


{include "foot.tpl"}