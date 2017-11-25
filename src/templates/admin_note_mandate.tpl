{include "head.tpl"}
{include "admin_note_head.tpl"}

<div class="tab-content">
    <div class="tab-pane active">
        <p>
            <a href="{mkurl action="admin_note" page="addmandate"}" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Ajouter</a>
        </p>

        {if isset($mandates)}
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th><abbr title="Date d'ouverture des inscriptions">Début</abbr></th>
                        <th><abbr title="Date de fermeture des inscriptions">Fin</abbr></th>
                        <th>Membres</th>
                        <th>Types de cotisation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$mandates item="m"}
                        <tr>
                            <td>{$m->mandate_label}</td>
                            <td>{$m->mandate_start}</td>
                            <td>{$m->mandate_end}</td>
                            <td>{$m->reverse("user_mandate")->count()}</td>
                            <td>
                                <a title="Liste des modes de cotisation" href="{mkurl action="admin_note" page="cotis" mandate=$m->mandate_id}">
                                    {$m->reverse("subscription")->count()}
                                </a>
                            </td>
                            <td>
                                {* // Hide delete action
                                <a href="{mkurl action="admin_note" page="delmandate" mandate=$m->mandate_id}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                                *}
                                <a href="{mkurl action="admin_note" page="modmandate" mandate=$m->mandate_id}" class="btn btn-warning"><i class="glyphicon glyphicon-pencil"></i></a>
                                <a title="Liste des modes de cotisation" href="{mkurl action="admin_note" page="cotis" mandate=$m->mandate_id}" class="btn btn-default"><i class="glyphicon glyphicon-list"></i></a>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <div class="alert alert-danger">
                <strong>Attention !</strong>
                <p>Aucun mandat n'a été défini.</p>
            </div>
        {/if}
    </div>
</div>

{include "foot.tpl"}
