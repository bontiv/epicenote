{include "head.tpl"}

{if $error}
    <div class="alert alert-danger">
      Une erreur s'est produit. L'événement n'est pas crée.
    </div>
{/if}

{if $succes}
    <div class="alert alert-success">
      Evénement crée avec succès.
    </div>
{/if}

<h1>Gestion événementiel</h1>
<h2>Création d'événement</h2>

<form method="POST" action="{mkurl action="section" page="mkevent" section=$section}">
  <form class="form-horizontal">
    <fieldset>

      <!-- Form Name -->
      <legend>Description général</legend>

      <!-- Text input-->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="event_name">Nom de l'événement</label>
        <div class="col-md-4">
          <input id="event_name" name="event_name" placeholder="" class="form-control input-md" required="" type="text">
          <span class="help-block">Nom de l'événement. Ne mettez pas non plus une phrase complète.</span>
        </div>
      </div>

      <!-- Textarea -->
      <div class="form-group row">
        <label class="col-md-12 control-label" for="event_desc">Description de l'événement</label>
        <div class="col-md-12">
          <textarea class="form-control" id="event_desc" name="event_desc"></textarea>
        </div>
      </div>

    </fieldset>
    <fieldset>

      <!-- Form Name -->
      <legend>Début de l'événement</legend>

      <!-- Text input-->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="event_start">Date de début</label>
        <div class="col-md-4">
          <input id="event_start" name="event_start" placeholder="yyyy-mm-dd" class="form-control input-md" required="" type="text">

        </div>
      </div>

      <!-- Text input-->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="event_start_hours">Heures</label>
        <div class="col-md-4">
          <div class="input-group">
            <input id="event_start_hours" name="event_start_hours" placeholder="00" class="form-control form-hours" required="" type="text">
            <span class="input-group-addon">h</span>
          </div>
        </div>
      </div>

      <!-- Text input-->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="event_start_mins">Minutes</label>
        <div class="col-md-4">
          <div class="input-group">
            <input id="event_start_mins" name="event_start_mins" placeholder="00" class="form-control form-hours" required="" type="text">
            <span class="input-group-addon">mins</span>
          </div>
        </div>
      </div>

    </fieldset>
    <fieldset>

      <!-- Form Name -->
      <legend>Fin de l'événement</legend>

      <!-- Text input-->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="event_end">Date de fin</label>
        <div class="col-md-4">
          <input id="event_end" name="event_end" placeholder="yyyy-mm-dd" class="form-control input-md" required="" type="text">

        </div>
      </div>

      <!-- Text input-->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="event_end_hours">Heures</label>
        <div class="col-md-4">
          <div class="input-group">
            <input id="event_end_hours" name="event_end_hours" placeholder="00" class="form-control form-hours" required="" type="text">
            <span class="input-group-addon">h</span>
          </div>
        </div>
      </div>

      <!-- Text input-->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="event_end_mins">Minutes</label>
        <div class="col-md-4">
          <div class="input-group">
            <input id="event_end_mins" name="event_end_mins" placeholder="00" class="form-control form-hours" required="" type="text">
            <span class="input-group-addon">mins</span>
          </div>
        </div>
      </div>


      <!-- Button -->
      <div class="form-group row">
        <label class="col-md-4 control-label" for="go"></label>
        <div class="col-md-4">
          <button id="go" name="go" class="btn btn-primary">Valider</button>
        </div>
      </div>

    </fieldset>
  </form>
</form>

{literal}
    <script type="text/javascript" src="ckeditor/ckeditor.js">
    </script>
    <script type="text/javascript">
        CKEDITOR.replace('event_desc');

        $(function () {
            $("#event_start").datepicker({
                dateFormat: 'yy-mm-dd'
            });
            $("#event_end").datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });
    </script>
{/literal}

{include "foot.tpl"}