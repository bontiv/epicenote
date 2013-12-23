{include "head.tpl"}

<h1>Administration</h1>
<h3>Gestion des sections</h3>
<a class="btn btn-link" href="{mkurl action="section" page="add"}" role="button" data-toggle="modal"><i class="glyphicon glyphicon-plus"></i> Ajouter</a>
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Nom</th>
      <th>Type</th>
      <th>Créateur</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
{foreach from=$sections item="line"}
    <tr>
      <td><a href="{mkurl action="section" page="details" section=$line.section_id}">{$line.section_name}</a>
      {if $line.inType=="guest"}<span class="label label-default">En attente</span>{/if}
      {if $line.inType=="rejected"}<span class="label label-danger">Rejeté</span>{/if}
      {if $line.inType=="user"}<span class="label label-success">Staff</span>{/if}
      {if $line.inType=="manager"}<span class="label label-primary">Manager</span>{/if}
          </td>
      <td>{$line.section_type}</td>
      <td>{$line.user_name}</td>
      <td>
        <div class="btn-group">
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
    

{include "foot.tpl"}