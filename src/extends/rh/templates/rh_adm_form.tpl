{extends "rh_adm_index.tpl"}

{block rhcontent}
    <form class="form-horizontal">
        <fieldset>
            
            <legend>Formulaire d'inscription</legend>
            
           <!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Text Input</label>  
  <div class="col-md-4">
  <input id="textinput" name="textinput" placeholder="placeholder" class="form-control input-md" type="text">
  <span class="help-block">help</span>  
  </div>
</div>
 
           <!-- Text input-->
<div class="form-group">
  <div class="col-md-offset-4 col-md-4">
      <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle" id="addElement" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expended="ẗrue">
              Ajouter <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="addElement">
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="shorttext" event=$conf->raw_re_event}">Text court</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="longtext" event=$conf->raw_re_event}">Text long</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="phone" event=$conf->raw_re_event}">Numéro de téléphone</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="address" event=$conf->raw_re_event}">Adresse</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="dropdown" event=$conf->raw_re_event}">Liste déroulante</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="checkbox" event=$conf->raw_re_event}">Choix multipes (cases à cocher)</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="radio" event=$conf->raw_re_event}">Choix unique (cases radio)</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="step" event=$conf->raw_re_event}">Nouvelle étape</a></li>
              <li><a href="{mkurl action="rh_adm" page="addelmt" type="section" event=$conf->raw_re_event}">Choix de section</a></li>
          </ul>
      </div>
  </div>
</div>
           
           
        </fieldset>
    </form>        
    
{/block}