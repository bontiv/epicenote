{include "head.tpl"}

<ol class="breadcrumb">
  <li><a href="{mkurl action="section"}">Sections</a></li>
  <li class="active">{$section->section_name}</li>
</ol>

{include "section_head.tpl"}

<h3>Membres</h3>
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Pseudo</th>
      <th>Type</th>
      <th>Login</th>
      <th>Email</th>
      <th>Téléphone</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$managers item="line"}
        <tr>
          <td><a href="{mkurl action="user" page="view" user=$line.user_id}">{$line.user_name|escape}</a></td>
          <td><span class="label label-primary">Manager</span></td>
          <td>{$line.user_login|escape}</td>
          <td><a href="mailto:{$line.user_email|escape:'url'}">{$line.user_email|escape}</a></td>
          <td><a href="tel:{$line.user_phone|escape:'url'}">{$line.user_phone|escape}</a></td>
          <td><a href="{mkurl action="section" page="accept" user=$line.user_id section=$section->section_id}" class="btn btn-warning" title="Rétrograder membre de la section"><span class="glyphicon-thumbs-down glyphicon"></span></td>
        </tr>
    {/foreach}
    {foreach from=$users item="line"}
        <tr>
          <td>{$line.user_name|escape}</td>
          <td><span class="label label-success">Staff</span></td>
          <td>{$line.user_login|escape}</td>
          <td><a href="mailto:{$line.user_email|escape:'url'}">{$line.user_email|escape}</a></td>
          <td><a href="tel:{$line.user_phone|escape:'url'}">{$line.user_phone|escape}</a></td>
          <td>
            <div class="btn-group">
              <a href="{mkurl action="section" page="reject" user=$line.user_id section=$section->section_id}" class="btn btn-danger" title="Retirer l'utilisateur de la section"><span class="glyphicon-remove glyphicon"></span></a>
              <a href="{mkurl action="section" page="manager" user=$line.user_id section=$section->section_id}" class="btn btn-warning" title="Promouvoir responsable de la section"><span class="glyphicon-thumbs-up glyphicon"></span></a>
            </div>
          </td>
        </tr>
    {/foreach}
    {foreach from=$guests item="line"}
        <tr>
          <td>{$line.user_name|escape}</td>
          <td><span class="label label-default">En attente</span></td>
          <td>{$line.user_login|escape}</td>
          <td><a href="mailto:{$line.user_email|escape:'url'}">{$line.user_email|escape}</a></td>
          <td><a href="tel:{$line.user_phone|escape:'url'}">{$line.user_phone|escape}</a></td>
          <td>
            <div class="btn-group">
              <a href="{mkurl action="section" page="reject" user=$line.user_id section=$section->section_id}" class="btn btn-danger" title="Refuser la candidature"><span class="glyphicon-remove glyphicon"></span></a>
              <a href="{mkurl action="section" page="accept" user=$line.user_id section=$section->section_id}" class="btn btn-primary" title="Accepter la candidature"><span class="glyphicon-plus glyphicon"></span></a>
            </div>
          </td>
        </tr>
    {/foreach}
  </tbody>
</table>

<form class="form-inline" method="POST" action="{mkurl action="section" page="staff_add" section=$section->section_id}">
  <div class="row">
    <div class="col-lg-6">
      <div class="input-group">
        <span class="input-group-addon">Ajout</span>
        <input type="text" class="form-control" name="login" id="staffAdd" placeholder="Pseudo">
        <span class="input-group-btn">
          <button class="btn btn-primary" type="submit" title="Ajouter cet utilisateur en manager"><div class="glyphicon glyphicon-plus-sign"></div></button>
        </span>
      </div><!-- /input-group -->
    </div><!-- /.col-lg-6 -->
  </div><!-- /.row -->
</form>

<script type="text/javascript">
  var srcUrl = "{mkurl action="event" page="staff_add" section=$section->section_id format="json"}";
  {literal}
  $(function () {

    var split = function (val) {
      return val.split(/,\s*/);
    }
    var extractLast = function (term) {
      return split(term).pop();
    }

    //Use tabulations
    $('#staffAdd').bind("keydown", function (event) {
      if (event.keyCode === $.ui.keyCode.TAB &&
              $(this).autocomplete("instance").menu.active) {
        event.preventDefault();
      }
    });

    //Auto complete
    $('#staffAdd').autocomplete({
      source: function (request, response) {
        var answer = new Array();
        $.getJSON(srcUrl, {
          term: extractLast(request.term)
        }, function (data) {
          var _len = data.length;
          for (var _i = 0; _i < _len; _i++) {
            var line = data[_i];
            answer.push({
              value: line.user_name,
              label: line.user_firstname
                      + " " + line.user_lastname
                      + " (" + line.user_name + ")"
            });
          }
          response(answer);
        });
      },
      search: function () {
        // custom minLength
        var term = extractLast(this.value);
        if (term.length < 2) {
          return false;
        }
      },
      focus: function () {
        // prevent value inserted on focus
        return false;
      },
      select: function (event, ui) {
        var terms = split(this.value);
        // remove the current input
        terms.pop();
        // add the selected item
        terms.push(ui.item.value);
        // add placeholder to get the comma-and-space at the end
        terms.push("");
        //this.value = terms.join(", ");
        this.value = ui.item.value;
        return false;
      }
    });
  });
  {/literal}
</script>

{include "foot.tpl"}