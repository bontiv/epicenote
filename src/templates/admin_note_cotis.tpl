{include "head.tpl"}

<ol class="breadcrumb">
    <li><a href="{mkurl action="admin_note" page="mandate"}">Mandats</a></li>
    <li class="active">Cotisations {$mandate->mandate_label}</li>
</ol>

<h1>
    Modes de cotisation<br/>
    <small>{$mandate->mandate_label}</small>
</h1>

<p>
    <a href='{mkurl action=admin_note page=addcotis mandate=$mandate->getKey()}' class="btn btn-primary">
        <i class="glyphicon glyphicon-plus"></i>
        Ajout
    </a>
</p>

{if isset($cotis)}
    <table class="table">
        <thead>
            <tr>
                <th>Nom cotisation</th>
                <th>Nombre</th>
                <th>Tarif</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            {foreach $cotis as $cot}
                <tr>
                    <td>{$cot->subscription_label}</td>
                    <td>{$cot->reverse('user_mandate')->count()}</td>
                    <td>{$cot->subscription_price} €</td>
                    <td>
                        <a href="{mkurl action=admin_note page=delcotis cotis=$cot->getKey()}" class="btn btn-danger btn-sm">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                    </td>
                </tr>

            {/foreach}
        </tbody>
    </table>
{else}
    <div class="alert alert-info">
        <p>Il n'y a aucune cotisation sur ce mandat !</p>
    </div>
{/if}
{include "foot.tpl"}