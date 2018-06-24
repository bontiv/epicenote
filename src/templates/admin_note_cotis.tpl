{include "head.tpl"}

<ol class="breadcrumb">
    <li><a href="{mkurl action="admin_note" page="mandate"}">Mandats</a></li>
    <li class="active">Cotisations {$mandate->mandate_label}</li>
</ol>

<h1>
    Modes de cotisation<br/>
    <small>{$mandate->mandate_label}</small>
</h1>

{if $mandate->raw_mandate_state == 'DRAFT'}
    <div class="alert alert-warning">
        <strong>Attention !</strong>
        <p>Ce mandat n'est pas activé. Cliquez sur "Valider" afin d'activer ce mandat. Les membres pourront alors s'inscrire mais les mandats ne seont
            plus éditables.</p>
    </div>
{/if}

<p>
    {if $mandate->raw_mandate_state == 'DRAFT'}
        <a href='{mkurl action=admin_note page=addcotis mandate=$mandate->getKey()}' class="btn btn-primary">
            <i class="glyphicon glyphicon-plus"></i>
            Ajout
        </a>
        {if isset($cotis)}
            <a href='{mkurl action=admin_note page=addcotis mandate=$mandate->getKey()}' class="btn btn-success">
                <i class="glyphicon glyphicon-check"></i>
                Valider
            </a>
        {/if}
    {/if}
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
                        {if $mandate->raw_mandate_state == 'DRAFT'}
                            <a href="{mkurl action=admin_note page=delcotis cotis=$cot->getKey()}" class="btn btn-danger btn-sm">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        {/if}
                    </td>
                </tr>

            {/foreach}
        </tbody>
    </table>
{else}
    <div class="alert alert-info">
        <p>Il n'y a aucune cotisation sur ce mandat ! Vous ne pouvez pas valider le mandat sans cotisation.</p>
    </div>
{/if}
{include "foot.tpl"}