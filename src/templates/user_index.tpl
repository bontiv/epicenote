{include "head.tpl"}

{* BLOCK : Modal ajout membre *}
<div class="modal fade" id="addMember" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Passer en membre</h4>
            </div>
            <div class="modal-body">
                Vous allez passer <span id="member_firstname"></span> membre de l'association sur
                le mandat :

                Passez par le bouton de validation. J'ai la flème pour cet écran.

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Valider</button>
            </div>
        </div>
    </div>
</div>
{* END BLOCK : Modal ajout membre *}

{* BLOCK : Modal PDF export *}
<form class="form-horizontal" method="POST" action="{mkurl action="user" page="pdfex"}">
    <div class="modal fade" id="ExportPDF" tabindex="-1" role="dialog" aria-labelledby="ExportPDF" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Export PDF des membres</h4>


                </div>
                <div class="modal-body">
                    <fieldset>

                        <!-- Select Basic -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic">Mandat</label>
                            <div class="col-md-4">
                                <select id="mandate" name="mandate" class="form-control">
                                    {foreach $mandates as $mandate}
                                        <option value="{$mandate->mandate_id}">{$mandate->mandate_label}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Valider</button>
                </div>
            </div>
        </div>
    </div>
</form>
{* END BLOCK : Modal PDF export *}

<script type="text/javascript">
    {literal}
        var clipboard = '';

        function addAddress(email) {
            clipboard = clipboard + email + ",\n";
        }

        function sendToClipboard()
        {
            $('#googleCopyText').val(clipboard);
            $('#googleCopy').modal('show');
            $('#googleCopyText').get(0).select();
            return false;
        }

        function updateTextArea() {
            $('#googleCopyText').val(clipboard);
            $('#googleCopyText').get(0).focus();
            $('#googleCopyText').get(0).select();
        }
    {/literal}
</script>

<div class="">
    <h1>Administration</h1>
    <h3>Gestion des utilisateurs</h3>

    {* START BLOCK recherche *}
    <form class="form-inline" method="POST" action="{mkurl action="user" page="index"}">
        <fieldset>
            <div class="form-group">
                <div class="col-md-8">
                    <input id="search" name="search" placeholder="Recherche" class="form-control input-md"  type="search">
                </div>
            </div>
        </fieldset>
    </form>
    {* END BLOCK *}


    <a class="btn btn-link" href="{mkurl action="user" page="add"}" role="button" data-toggle="modal"><i class="glyphicon glyphicon-plus"></i> Ajouter</a>
    <a class="btn btn-link" href="{mkurl action="user" page="check"}" role="button" data-toggle="modal"><i class="glyphicon glyphicon-check" title="Valider des cotisations"></i> Valider</a>
    <a class="btn btn-link" href="#" role="button" data-toggle="modal" data-target="#ExportPDF"><i class="glyphicon glyphicon-share" title="Exporter en PDF"></i> Export PDF</a>
    <a class="btn btn-link" href="{mkurl action="user" page="sync2"}" role="button" data-toggle="modal"><i class="glyphicon glyphicon-export"></i> GoogleSync</a>
    <a class="btn btn-link" href="{mkurl action="user" page="remove_old"}" role="button" data-toggle="modal"><i class="glyphicon glyphicon-trash"></i> Retrait anciens</a>
    <ul class="pager">
        {if $ptable.showPrev}
            <li><a href="{$ptable.prev}"><i class="glyphicon glyphicon-arrow-left"></i> Précédent</a></li>
            {/if}
            {if $ptable.showNext}
            <li><a href="{$ptable.next}">Suivant <i class="glyphicon glyphicon-arrow-right"></i></a></li>
                {/if}
    </ul>


    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Pseudo</th>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Téléphone</th>
                <th>email</th>
                <th>Login</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$ptable.rows item="line"}
                <tr>
                    <td>
                        <a href="{mkurl action="user" page="view" user=$line.user_id}">{$line.user_name|escape}</a>
                        {if $line.user_role=='USER'}<span class="label label-default">Membre</span>{/if}
                        {if $line.user_role=='ADMINISTRATOR'}<span class="label label-primary">Admin</span>{/if}
                    </td>
                    <td>{$line.user_lastname|escape}</td>
                    <td>{$line.user_firstname|escape}</td>
                    <td><a href="tel:{$line.user_phone|escape:'url'}">{$line.user_phone|escape}</a></td>
                    <td>
                        <a class="email-field" href="mailto:{$line.user_email|escape:'url'}">{$line.user_email|escape}</a>
                        <script type="text/javascript">
                            addAddress('{$line.user_email|escape:'javascript'}');
                        </script>
                    </td>
                    <td><a href="https://intra.epitech.eu/user/{$line.user_login|escape:'url'}/">{$line.user_login|escape}</a></td>
                    <td>
                        <div class="btn-group">
                            <a href="{mkurl action="user" page="delete" user=$line.user_id}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                            <a href="{mkurl action="user" page="edit" user=$line.user_id}" class="btn btn-warning"><i class="glyphicon glyphicon-pencil"></i></a>
                            {if $line.user_role=='GUEST'}<a href="#" class="btn btn-info" data-toggle="modal" data-target="#addMember"><i class="glyphicon glyphicon-heart"></i></a>{/if}
                        </div>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

<ul class="pager">
    {if $ptable.showPrev}
        <li><a href="{$ptable.prev}"><i class="glyphicon glyphicon-arrow-left"></i> Précédent</a></li>
        {/if}
        {if $ptable.showNext}
        <li><a href="{$ptable.next}">Suivant <i class="glyphicon glyphicon-arrow-right"></i></a></li>
            {/if}
</ul>

{include "foot.tpl"}