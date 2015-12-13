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
  <p>
    Nom de l'événement</span><br />
    <input type="text" name="event_name" class="form-control" />
  </p>
  <p>
    Description</span><br />
    <textarea id="event_desc" name="event_desc" class="form-control"></textarea>
  </p>
  <p>
    Date de début</span> <i>format YYYY-MM-DD hh:mm:ss</i><br />
    <input type="text" name="event_start" class="form-control" />
  </p>
  <p>
    Date de fin <i>format YYYY-MM-DD hh:mm:ss</i><br />
    <input type="text" name="event_end" class="form-control" />
  </p>
  <p><input type="submit" value="Créer" class="form-control btn btn-primary" /></p>
</form>

<script type="text/javascript" src="ckeditor/ckeditor.js">
</script>
<script type="text/javascript">
    CKEDITOR.replace('event_desc');
</script>

{include "foot.tpl"}