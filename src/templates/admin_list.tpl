{include "head.tpl"}

<h1>Les administrateurs</h1>
{if isset($admins)}
    <table class="table table-striped">
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Pseudo</th>
            <th>Révocation</th>
        </tr>
        {foreach $admins as $adm}
            <tr>
                <td>{$adm->user_lastname|escape}</td>
                <td>{$adm->user_firstname|escape}</td>
                <td>{$adm->user_name|escape}</td>
                <td><a class="btn btn-sm btn-danger" href="{mkurl action="admin" page="remove" user=$adm->user_id}"><i class="glyphicon glyphicon-trash" Révoquer</a></td>
            </tr>
        {/foreach}
    </table>
{else}
    <div class="alert alert-danger">
        Pas d'administrateurs ? Oups ! C'est dangereux !
    </div>
{/if}

<p>Ajouter :</p>
<form method="POST" action="{mkurl action="admin" page="add"}" class="form-inline">
    <div class="form-group">
        <input type="text" name="addadmin" id="addadmin" placeholder="Pseudo" class="form-control" />
    </div>
    <div class="form-group">
        <button class="btn btn-primary" type="submit">Go</button>
    </div>
</form>
    <script type="text/javascript">
        let url = '{mkurl action="admin" page="autocomplete"}';
{literal}
        $('#addadmin').autocomplete({
            source: url,
            select: function(event, ui) {
                event.target.value = ui.item.value;
                event.target.form.submit();
            }
        });
{/literal}
    </script>

{include "foot.tpl"}
