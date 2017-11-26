<h1>Administration de la notation</h1>

<ul class="nav nav-tabs">
  <li role="presentation"{if $smarty.get.page=="index"} class="active"{/if}>
    <a href="{mkurl action="admin_note" page="index"}">A valider</a>
  </li>
  <li role="presentation"{if $smarty.get.page=="list"} class="active"{/if}>
    <a href="{mkurl action="admin_note" page="list"}">Notes</a>
  </li>
  <li role="presentation"{if $smarty.get.page=="mandate"} class="active"{/if}>
    <a href="{mkurl action="admin_note" page="mandate"}">Mandats</a>
  </li>
  <li role="presentation"{if $smarty.get.page=="periods"} class="active"{/if}>
    <a href="{mkurl action="admin_note" page="periods"}">Semestres</a>
  </li>
  <li role="presentation"{if $smarty.get.page=="bulletin"} class="active"{/if}>
    <a href="{mkurl action="admin_note" page="bulletin"}">Bulletins</a>
  </li>
  <li role="presentation"{if $smarty.get.page=="reclam"} class="active"{/if}>
    <a href="{mkurl action="admin_note" page="reclam"}">Réclamations</a>
  </li>
</ul>

<br/>