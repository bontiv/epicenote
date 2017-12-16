{include "head.tpl"}
{include "section_head.tpl"}

<h2>Ajout d'une équipe</h2>
<form method="POST" action="{mkurl action="section" page="team_add" section=$section->getKey()}">
    <fieldset class="form-horizontal">
        {$team->edit()}

        <div class="form-group">
            <label class="col-md-4 control-label" for="edit"></label>
            <div class="col-md-8">
                <button id="edit" name="edit" class="btn btn-success">Valider</button>
            </div>
        </div>

    </fieldset>
</form>

{include "foot.tpl"}