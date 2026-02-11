<?php require_once '../../config.php';
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
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/DataTables/TableTools.css">
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
                <li><a href="<?php echo BASEURL ?>app/cobranzas/">Panel de Cobranzas</a></li>
                <li class="active">Cobranzas</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-10">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                        <h5 class="panel-title">Seleccion de Cobranzas</h5><br>
                      <div class="panel panel-default">
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-sm-12">
                              <table id="dttCliente" class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>Seleccion</th>
                                    <th>Documento</th>
                                    <th>F. Emision</th>
                                    <th>F. Vencimiento</th>
                                    <th>Monto total</th>
                                    <th>Monto Pagado</th>
                                    <th>Monto Pendiente</th>
                                  </tr>
                                </thead>
                                <tbody>

                                  <tr>
                                    <td class="text-right">
                                      <label class="checkbox-inline checkbox-styled">
                                        <input type="checkbox" value="option1">
                                      </label>
                                    </td>
                                    <td>Factura Electronica 5432</td>
                                    <td>01/01/2019</td>
                                    <td>01/02/2019</td>
                                    <td>$ 98.741</td>
                                    <td>$ 0</td>
                                    <td>$ 98.741</td>
                                  </tr>

                                  <tr>
                                    <td class="text-right">
                                      <label class="checkbox-inline checkbox-styled">
                                        <input type="checkbox" value="option1">
                                      </label>
                                    </td>
                                    <td>Factura Electronica 6589</td>
                                    <td>01/01/2019</td>
                                    <td>01/02/2019</td>
                                    <td>$ 198.741</td>
                                    <td>$ 100.000</td>
                                    <td>$ 98.741</td>
                                  </tr>

                                  <tr>
                                    <td class="text-right">
                                      <label class="checkbox-inline checkbox-styled">
                                        <input type="checkbox" value="option1">
                                      </label>
                                    </td>
                                    <td>Factura Electronica 7542</td>
                                    <td>01/01/2019</td>
                                    <td>01/02/2019</td>
                                    <td>$ 298.741</td>
                                    <td>$ 298.741</td>
                                    <td>$ 0</td>
                                  </tr>

                                </tbody>
                              </table>                              
                            </div>
                          </div>
                          <br>

                          <div class="row">
                            <div class="col-sm-12">
                              <a class="btn btn-block ink-reaction btn-primary" href="pagos.php">Continuar con el Pago</a>
                            </div>
                          </div>
                          
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