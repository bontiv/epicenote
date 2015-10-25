{include "head.tpl"}

<ol class="breadcrumb">
  <li><a href="{mkurl action="section" page="index"}">Sections</a></li>
  <li><a href="{mkurl action="section" page="details" section=$section->section_id}">{$section->section_name|escape}</a></li>
  <li><a href="{mkurl action="section" page="mls" section=$section->section_id}">Groupes de diffusion</a></li>
  <li class="active">Envoi email</li>
</ol>

{include "section_head.tpl"}
<h3>Envoi d'email</h3>

<form action="" method="POST">

  <fieldset>

    <div class="form-group">
      <div class="input-group">
        <div class="input-group-addon">Sujet</div>
        <input class="form-control" name="subject" id="subject" {if isset($smarty.post.subject)}value="{$smarty.post.subject|escape}"{/if} />
      </div>
    </div>

    <div class="form-group">
      <div class="input-group">
        <div class="input-group-addon">Expéditeur</div>
        <input class="form-control" name="sender" id="sender" disabled="disabled" value="{$mail->email|escape}" />
      </div>
    </div>

    <div class="form-group">
      <div class="input-group">
        <div class="input-group-addon">Destinataire(s)</div>
        <input class="form-control" name="recipients" id="recipients" {if isset($smarty.post.recipients)}value="{$smarty.post.recipients|escape}"{/if} />
      </div>
      <span class="help-block">
        <p>Vous pouvez mettre plusieurs destinataires séparés par virgules.</p>
      </span>
    </div>

    <div class="form-group">
      <label for="ebody">Message:</label>
      <textarea name="ebody" id="ebody">{if isset($smarty.post.ebody)}{$smarty.post.ebody|escape}{/if}</textarea>
    </div>

    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.replace('ebody');
    </script>

    <div class="form-group">
      <input class="btn btn-success" name="submit" type="submit" id="submit" value="Envoyer" />
    </div>

  </fieldset>

</form>

{include "foot.tpl"}
