{include "head.tpl"}

<ol class="breadcrumb">
    <li><a href="{mkurl action="section"}">Sections</a></li>
    <li><a href="{mkurl action="section" page="details" section=$section->section_id}">{$section->section_name|escape}</a></li>
    <li class="active">Trombinoscope</li>
</ol>

<h1>Trombinoscope <small>Section {$section->section_name|escape}</small></h1>

<h2>Responsables</h2>
<div class="row">
    {foreach $managers as $manager}
        <div class="col-md-2">
            <div class="panel panel-primary">
                <div class="panel-body">
                    {if $manager->us_user->user_photo != ""}
                        {acl action="user" page="viewphoto"}
                        <img width="100%" src="{mkurl action="user" page="viewphoto" user=$manager->us_user->user_id}" />
                        {/acl}
                    {elseif $manager->us_user->user_login}
                        <img  width="100%" src="https://intra-bocal.epitech.eu/trombi/{$manager->us_user->user_login|escape:'url'}.jpg" />
                    {else}
                        <img width="100%" alt="Pas d'image" src="images/nobody.png" />
                    {/if}
                </div>
                <div class="panel-footer">
                    {$manager->us_user->user_name|escape}
                </div>
            </div>
        </div>
    {/foreach}
</div>

<h2>Staffs</h2>
<div class="row">
    {foreach $users as $staff}
        {if $staff->us_user->user_status != "DELETE"}
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        {if $staff->us_user->user_photo != ""}
                            {acl action="user" page="viewphoto"}
                            <img width="100%" src="{mkurl action="user" page="viewphoto" user=$staff->us_user->user_id}" />
                            {/acl}
                        {elseif $staff->us_user->user_login}
                            <img  width="100%" src="https://intra-bocal.epitech.eu/trombi/{$staff->us_user->user_login|escape:'url'}.jpg" />
                        {else}
                            <img width="100%" alt="Pas d'image" src="images/nobody.png" />
                        {/if}
                    </div>
                    <div class="panel-footer">
                        {$staff->us_user->user_name|escape}
                    </div>
                </div>
            </div>
        {/if}
    {/foreach}
</div>


{include "foot.tpl"}
