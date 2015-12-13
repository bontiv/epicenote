<html>
  <head>
    <!-- Css -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-select.css" rel="stylesheet">
    <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <!-- /Css -->
    <!-- Scripts -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="js/datetime-picker.min.js"></script>
    <!-- /Scripts -->

    <title>Liste participants :: {$ews->ee_ews->ews_name} :: {$event.event_name}</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf8" />
  </head>
  <body>
    <div class="container">
      <div class="hidden-print">
        <h1>Impression des participants</h1>
        <p>
          <a href="javascript:window.print()" class="btn btn-primary">
            <span class="glyphicon glyphicon-print"></span> Imprimer
          </a>
          <a href="javascript:window.close()" class="btn btn-default">
            <span class="glyphicon glyphicon-off"></span> Fermer
          </a>
        </p>

        <div class="alert alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <p>
            Vous devriez avoir les entêtes sur toutes les pages. Si ce n'est
            pas le cas, essayez un autre navigateur, votre navigateur ne prend
            pas correctement en compte les balises thead.
          </p>
        </div>
      </div>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>
              ID
              <a href="{mkurl action="event" page="ews_print" event=$event.event_id ews=$ews->ee_id order='tu_id'}" class="glyphicon glyphicon-sort-by-alphabet hidden-print" title="Tri"></a>
            </th>
            <th>
              Nom
              <a href="{mkurl action="event" page="ews_print" event=$event.event_id ews=$ews->ee_id order='tu_lastname'}" class="glyphicon glyphicon-sort-by-alphabet hidden-print" title="Tri"></a>
            </th>
            <th>Prénom</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          {foreach $users as $usr}
              <tr>
                <td>{$usr.tu_id}</td>
                <td>{$usr.tu_lastname}</td>
                <td>{$usr.tu_fistname}</td>
                <td>{$usr.tu_email}</td>
              </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </body>
</html>