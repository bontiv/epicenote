{include "head.tpl"}

{if $succes}
    <div class="alert success alert-success" role="alert">
      Inscription passé avec succès. Vous pouvez dès à présent vous connecter.
    </div>
{/if}
{if $error}
    <div class="alert alert-danger danger" role="alert"><strong>Erreur !</strong> {$error}</div>
{/if}


<h1>Inscription</h1>
<div class="alert alert-info">
  <p><strong>Attention !</strong> L'inscription sur ce site ne tient pas lieu
    d'inscription à l'association. Vous devez vous inscrire et cotiser en
    tant qu'adhérent pour bénéficier de tous les services de ce site.
  </p>
</div>

<div class="container col-lg-12">
  <form method="POST" action="{mkurl action="index" page="create"}">
    <div class="col-lg-3">
      <div class="input-group">
        <span class="input-group-addon">Pseudo</span>
        <input class="form-control" type="text" name="user_name" required="" {if isset($smarty.post.user_name)}value="{$smarty.post.user_name}"{/if}/>
      </div>
      <br/>
      <div class="input-group">
        <span class="input-group-addon">Nom</span>
        <input class="form-control" type="text" name="user_lastname" required="" {if isset($smarty.post.user_lastname)}value="{$smarty.post.user_lastname}"{/if} />
      </div>
      <br/>
      <div class="input-group">
        <span class="input-group-addon">Prénom</span>
        <input class="form-control" type="text" name="user_firstname" required="" {if isset($smarty.post.user_firstname)}value="{$smarty.post.user_firstname}"{/if} />
      </div>
      <br/>
      <div class="input-group">
        <span class="input-group-addon">Mot de passe</span>
        <input class="form-control" type="password" required="" name="user_pass" />
      </div>
      <br/>
      <div class="input-group">
        <span class="input-group-addon">Confirmation</span>
        <input class="form-control" type="password" name="confirmPassword" required="" placeholder="Confirmez le mot de passe" />
      </div>
      <br/>
    </div>

    <div class="col-lg-3">
      <div class="input-group">
        <span class="input-group-addon">Type</span>
        <select name="user_type" class="form-control">
          {foreach from=$types item="t"}
              <option value="{$t.ut_id}" {if isset($smarty.post.user_type) && $t.ut_id == $smarty.post.user_type}selected{/if}>{$t.ut_name}</option>
          {/foreach}
        </select>
      </div>
      <br/>
      <div class="input-group">
        <span class="input-group-addon">Login IONIS</span>
        <input class="form-control" type="text" name="user_login" {if isset($smarty.post.user_login)}value="{$smarty.post.user_login}"{/if} />
      </div>
      <br/>
      <div class="input-group">
        <span class="input-group-addon">Email</span>
        <input class="form-control" type="text" name="user_email" required="" {if isset($smarty.post.user_email)}value="{$smarty.post.user_email}"{/if} />
      </div>
      <br/>
      <div class="input-group">
        <span class="input-group-addon">Téléphone</span>
        <input class="form-control" type="text" name="user_phone" {if isset($smarty.post.user_phone)}value="{$smarty.post.user_phone}"{/if} />
      </div>
      <br/>
    </div>
    <div class="col-lg-4">

      <!-- Text input-->
      <div class="input-group{if isset($error_captcha) && $error_captcha} has-error{/if}">
        <label class="input-group-addon" for="captcha_code">Vérification Captcha</label>
        <a title="Nouvelle image" href="#" class="btn btn-default btn-sm" onclick="document.getElementById('captcha').src = '{mkurl action="index" page="securimage_show"}&' + Math.random();
                return false"><img id="captcha" src="{mkurl action="index" page="securimage_show"}" alt="CAPTCHA Image" /></a>
        <input id="captcha_code" name="captcha_code" placeholder="" class="form-control input-md" required="" type="text">
      </div>

      <br/><br/>
      <div>
        <input type="submit" name="Inscription" class="btn btn-success" />
      </div>
    </div>
  </form>
</div>
{include "foot.tpl"}