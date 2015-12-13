{include "head.tpl"}
{include "event_head.tpl"}

{acl action="event" page="ews_add"}
{if count($ews) ne 0}
    <form class="form-inline" method="POST" action="{mkurl action="event" page="ews_add" event=$event.event_id}">
      <div class="row">
        <div class="col-lg-6">
          <div class="input-group">
            <span class="input-group-addon">Publier</span>
            <select class="form-control" name="ews_id">
              {foreach $ews as $site_id => $site_name}
                  <option value="{$site_id}">{$site_name}</option>
              {/foreach}
            </select>
            <span class="input-group-btn">
              <button class="btn btn-primary" type="submit"><div class="glyphicon glyphicon-plus-sign"></div></button>
            </span>
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
      </div><!-- /.row -->
    </form>
{/if}
{/acl}

<table class="table table-striped">
  <thead>
    <tr>
      <th>Site WEB</th>
      <th>Nombre d'inscrits</th>
      <th>Statut</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    {if isset($ee)}
        {foreach $ee as $ews}
            <tr>
              <td>{$ews.ews_name}</td>
              <td>{$ews.t_count}</td>
              <td>
                {if $ews.t_state eq 'AUTO'}
                    <span class="text-success">Auto</span>
                {elseif $ews.t_state eq 'CLOSE'}
                    <span class="text-warning">Fermé</span>
                {else}
                    {$ews.t_state}
                {/if}
              </td>
              <td>
                <div class="btn-group btn-group-xs">
                  {if $ews.t_state eq 'AUTO'}
                      <a href="{mkurl action="event" page="ews_lock" lock='CLOSE' event=$event.event_id ews=$ews.ee_id}" class="btn btn-warning">
                        <div class="glyphicon glyphicon-lock"></div>
                      </a>
                  {else}
                      <a href="{mkurl action="event" page="ews_lock" lock='AUTO' event=$event.event_id ews=$ews.ee_id}" class="btn btn-default" title="Fermeture auto">
                        <div class="glyphicon glyphicon-flash"></div>
                      </a>
                  {/if}
                  <a href="{mkurl action="event" page="ews_print" event=$event.event_id ews=$ews.ee_id}" class="btn btn-primary" target="print">
                    <div class="glyphicon glyphicon-print"></div>
                  </a>
                  <a href="{mkurl action="event" page="ews_del" event=$event.event_id ews=$ews.ee_id}" class="btn btn-danger">
                    <div class="glyphicon glyphicon-trash"></div>
                  </a>
                </div>
              </td>
            </tr>
        {/foreach}
    {else}
        <tr>
          <td colspan="3"><div class="text-muted">Aucune publication de l'événement.</div></td>
        </tr>
    {/if}
  </tbody>
</table>

{include "foot.tpl"}
