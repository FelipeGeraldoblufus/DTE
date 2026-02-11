<?php require_once '../../config.php';
$Cliente = new Cliente();
$listaCliente = $Cliente->listaCliente();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Cobranzas</title>
    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <!-- END META -->
    <!-- BEGIN STYLESHEET -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/DataTables/dataTables.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/style.css">
    <!-- BEGIN STYLESHEET -->
  </head>
  <body class="menubar-hoverable header-fixed full-content">
    <?php require_once '../../inc/header.php'; ?>
    <?php require_once '../../inc/menu.php'; ?>

    <!-- BEGIN BASE-->
    <div id="base">
      <!-- BEGIN CONTENT-->
        <div id="content">
          <section>
            <div class="section-header">
              <ol class="breadcrumb">
                <li><a href="<?php echo BASEURL ?>cobranzas_pagos.php">Cobranzas y Pagos</a></li>
                <li class="active">Historial de Cobranzas</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-10">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                          <h5 class="panel-title">Seleccion de Cliente</h5><br>
                      <div class="panel panel-default">
                        <div class="panel-body">
                          <table id="dttCliente" class="table table-hover">
                            <thead>
                              <tr>
                                <th>R.U.T.</th>
                                <th>Razón social</th>
                                <th>Direccion</th>
                                <th>Contacto</th>
                                <th class="text-right">Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if ($listaCliente): ?>
                              <?php foreach ($listaCliente as $cliente): ?>
                              <tr>
                                <td><?php echo $cliente['rut'] ?></td>
                                <td><?php echo $cliente['rznsoc'] ?></td>
                                <td><?php echo $cliente['direccion'].', '.$cliente['comuna'] ?></td>
                                <td><?php echo $cliente['contacto']; ?></td>
                                <td class="text-right">
                                  <a class="btn ink-reaction btn-primary" id="<?php echo $cliente['rut'] ?>" href="cliente.php">Ver cobros</a>
                                </td>
                              </tr>                          
                              <?php endforeach ?>
                              <?php else: ?>
                              <tr><td colspan="5">No hay datos en la tabla de clientes.</td></tr>  
                              <?php endif ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div> 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      <!-- END CONTENT -->
    </div>
  </body>
  <script src="<?php echo BASEURL ?>asset/js/jquery.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/bootstrap.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/DataTables/dataTables.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#dttCliente').DataTable({
        'language': {
          'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
        },
        "responsive": true,
        "ordering":  false,
        "serverSide": false, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
      });
    });
  </script>
</html>