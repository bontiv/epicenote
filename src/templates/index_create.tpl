{include "head.tpl"}

{if $succes}
    <div class="alert success alert-success" role="alert">
        Inscription passé avec succès. Vous pouvez dès à présent vous connecter.
    </div>
{/if}
{if $error}
    <div class="alert alert-danger danger" role="alert"><strong>Erreur !</strong> {$error}</div>
{/if}

{literal}
    <style type="text/css">
        .centered-form{
            margin-top: 60px;
        }

        .centered-form .panel{
            background: rgba(255, 255, 255, 0.8);
            box-shadow: rgba(0, 0, 0, 0.3) 20px 20px 20px;
        }
    </style>
{/literal}


<form method="POST" action="{mkurl action="index" page="create"}">
    <div class="container">
        <div class="alert alert-info">
            <p><strong>Attention !</strong> L'inscription sur ce site ne tient pas lieu
                d'inscription à l'association. Vous devez vous inscrire et cotiser en
                tant qu'adhérent pour bénéficier de tous les services de ce site.
            </p>
        </div>
        <div class="row centered-form">
            <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Inscription à l'intranet EPITANIME</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <input type="text" name="user_name" id="user_name" class="form-control input-sm" placeholder="Pseudo (login)" value="{$smarty.post.user_name|default:''}" required />
                        </div>

                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <input type="text" name="user_firstname" id="user_firstname" class="form-control input-sm" placeholder="Prénom" value="{$smarty.post.user_firstname|default:''}" />
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <input type="text" name="user_lastname" id="user_lastname" class="form-control input-sm" placeholder="Nom" value="{$smarty.post.user_lastname|default:''}" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="email" name="user_email" id="user_email" class="form-control input-sm" placeholder="Email" value="{$smarty.post.user_email|default:''}" required />
                        </div>

                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <input type="password" name="user_pass" id="user_pass" class="form-control input-sm" placeholder="Mot de passe">
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-control input-sm" placeholder="Confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="form-group{if isset($error_captcha) && $error_captcha} has-error{/if}">
                            <!-- Text input-->

                            <a title="Nouvelle image" href="#" class="btn btn-default btn-sm" onclick="document.getElementById('captcha').src = '{mkurl action="index" page="securimage_show"}&' + Math.random();
                                    return false"><img id="captcha" src="{mkurl action="index" page="securimage_show"}" alt="CAPTCHA Image" /></a>

                        </div>

                        <div class="form-group">
                            <input id="captcha_code" name="captcha_code" placeholder="Texte de l'image" class="form-control input-md" required="" type="text">
                        </div>

                        <input type="submit" value="Créeer le compte" class="btn btn-info btn-block">

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{include "foot.tpl"}