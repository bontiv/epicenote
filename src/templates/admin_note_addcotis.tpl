{include "head.tpl"}

<ol class="breadcrumb">
  <li><a href="{mkurl action="admin_note" page="mandate"}">Mandats</a></li>
  <li><a href="{mkurl action="admin_note" page="cotis" mandate=$mandate->getKey()}">{$mandate->mandate_label}</a></li>
  <li class="active">Ajout</li>
</ol>
  
  <h1>
    Modes de cotisation<br/>
    <small>{$mandate->mandate_label}</small>
</h1>

<h2>Ajout</h2>

<form method="POST" class="form-horizontal" action="{mkurl action=admin_note page=addcotis mandate=$mandate->getKey()}">
    <fieldset>
        {$cotis->edit()}
    </fieldset>
    
    
  <div class="form-group">
    <label class="col-md-4 control-label" for="submit"></label>
    <div class="col-md-8">
        <button id="submit" name="submit" type="submit" class="btn btn-success">Valider</button>
    </div>
  </div>
</form>

{include "foot.tpl"}