{include "head.tpl"}

<ol class="breadcrumb">
    <li><a href="{mkurl action="section"}">Sections</a></li>
    <li><a href="{mkurl action="section" page="details" section=$section->section_id}">{$section->section_name|escape}</a></li>
    <li class="active">Groupes de diffusion</li>
</ol>

{include "section_head.tpl"}

<h3>Équipes de {$section->section_name|escape}</h3>

{if isset($teams)}

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Nombre de membres</th>
                <th>Créateur</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$teams item="line"}
                <tr>
                    <td><a href="{mkurl action="section" page="details" section=$line.section_id}">{$line.section_name}</a>
                        {if $line.inType=="guest"}<span class="label label-default">En attente</span>{/if}
                        {if $line.inType=="rejected"}<span class="label label-danger">Rejeté</span>{/if}
                        {if $line.inType=="user"}<span class="label label-success">Staff</span>{/if}
                        {if $line.inType=="manager"}<span class="label label-primary">Manager</span>{/if}
                    </td>
                    <td>{if $line.section_ml}<a href="mailto:{$line.section_ml}">{$line.section_ml}</a>{/if}</td>
                    <td><span class="text-muted">N/A</span></td>
                    <td>{$line.user_name|escape}</td>
                    <td>
                        <div class="btn-group">
                            {acl action="section" page="trombi"}<a href="{mkurl action="section" page="trombi" section=$line.section_id}" title="Trombinoscope" class="btn btn-default"><i class="glyphicon glyphicon-camera"></i></a>{/acl}
                            {acl level="ADMINISTRATOR"}<a href="{mkurl action="section" page="delete" section=$line.section_id}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>{/acl}
                            {acl level="ADMINISTRATOR"}<a href="{mkurl action="section" page="edit" section=$line.section_id}" class="btn btn-warning"><i class="glyphicon glyphicon-pencil"></i></a>{/acl}
                                {if $line.inType}
                                <a href="{mkurl action="section" page="goout" section=$line.section_id}" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                                {else}
                                <a href="{mkurl action="section" page="goin" section=$line.section_id}" class="btn btn-info"><i class="glyphicon glyphicon-heart"></i></a>
                                {/if}
                        </div>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <p>Aucune section définie.</p>
{/if}

{include "foot.tpl"}
