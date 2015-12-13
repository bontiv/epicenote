{include "head.tpl"}

<h1>Gestion des sites web autonomes</h1>

<p>Hey ta vu mon site ?</p>

<a href="{mkurl action="ews" page="adm_add"}" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Ajout</a>

<table class="table table-striped">
  <thead>
    <tr>
      <th>Section</th>
      <th>Nom</th>
      <th>URL</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    {if isset($sites)}
        {foreach $sites as $site}
            <tr>
              <td><a href="{mkurl action="section" page="details" section=$site->ews_section->section_id}">{$site->ews_section->section_name|escape}</a></td>
              <td>{$site->ews_name|escape}</td>
              <td>{$site->ews_url|escape}</td>
              <td><a href="{mkurl action="ews" page="adm_del" id=$site->ews_id}" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a></td>
            </tr>
        {/foreach}
    {else}
        <tr>
          <td colspan="4"><div class="text-muted">Rien à afficher...</div></td>
        </tr>
    {/if}
  </tbody>
</table>

{include "foot.tpl"}
