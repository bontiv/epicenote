{include "head.tpl"}
<h2>Edition utilisateur</h2>
<form action="{mkurl action="user" page="edit" user=$user->getKey()}" method="post" class="form-horizontal">
    <fieldset>
        {$user->edit($fieldset)}

        <div class="form-group">
            <div class="col-md-offset-4 col-md-8">
                <input class="btn btn-primary" type="submit" value="Sauvegarder">
                <a href="{mkurl action="user" page="view" user=$user->getKey()}" class="btn btn-default">Annuler</a>
            </div>
        </div>
    </fieldset>
</form>
{include "foot.tpl"}