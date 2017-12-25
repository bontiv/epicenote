{include "head.tpl"}
{include "section_head.tpl"}

<form class="form-horizontal" method="POST" action="{mkurl action=section page=acl section=$section->getKey()}">
    {foreach $acls as $grp => $acll}
        <fieldset>
            <legend>{$grp}</legend>
            {foreach $acll as $aid => $acl}
                <div class="form-group">
                    <label for="acl-{$aid}" class="control-label col-md-4">{$acl.acl_page}</label>
                    <div class="col-md-4">
                        <div class=" checkbox">
                            <input type="checkbox" name="acl-{$aid}" id="acl-{$aid}" {if $acl.acl_enable}checked{/if} />                    
                        </div>
                    </div>
                </div>
            {/foreach}
        </fieldset>
    {/foreach}
    <div class="form-group">
        <label class="col-md-4 control-label" for="singlebutton"></label>
        <div class="col-md-4">
            <button id="singlebutton" name="singlebutton" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
</form>

{include "foot.tpl"}