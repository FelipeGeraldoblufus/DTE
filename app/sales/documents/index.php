<?php 
require_once '../../../config.php';
$_SESSION['navMenu']  = 'sales';
$Empresa = new Empresa();
$Cliente = new Cliente();
$IngresoDocumento =  new IngresoDocumento();
$datosEmpresa = $Empresa->listaEmpresa();
$listaDocumento = $IngresoDocumento->listaDocumento('Venta');
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Ingresar Compras</title>
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
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/toastr.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/DataTables/dataTables.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/style.css">
    <!-- BEGIN STYLESHEET -->
  </head>
  <body class="menubar-hoverable header-fixed full-content">
    <?php require_once '../../../inc/header.php'; ?>
    <?php require_once '../../../inc/menu.php'; ?>

    <!-- BEGIN BASE-->
    <div id="base">
      <!-- BEGIN CONTENT-->
        <div id="content">
          <section>
            <div class="section-header">
              <ol class="breadcrumb">
                <li><a href="<?php echo BASEURL ?>app/sales/">Ventas</a></li>
                <li class="active">Documentos</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-10 col-sm-12">

                <div class="card">
                  <div class="card-body">
                    <?php if (isset($listaDocumento) && !empty($listaDocumento)): ?>
                    <table id="dttCuentas" class="table table-hover">
                      <thead>
                        <tr>
                          <th>Cliente</th>
                          <th>Documento</th>
                          <th>Emision</th>
                          <th>Total</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($listaDocumento as $documento): ?>
                          <?php $rznsocCliente = $Cliente->rznsocCliente($documento['cliente_rut']); ?>
                          <tr>
                            <td><?php echo '<strong>'.$documento['cliente_rut'].'</strong> '.$rznsocCliente; ?></td>
                            <td>
                              <?php if ($documento['dte'] == 30): ?>
                                Factura <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 32): ?>
                                Factura no Afecta <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 38): ?>
                                Boleta exenta <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 40): ?>
                                Liq. Factura <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 45): ?>
                                Factura Compra <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 50): ?>
                                Guia despacho <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 55): ?>
                                Nota Débito <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 60): ?>
                                Nota Crédito <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 33): ?>
                                Factura elec. <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 34): ?>
                                Factura no Afecta elec. <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 39): ?>
                                Boleta elec. <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 46): ?>
                                Factura Compra elec. <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 56): ?>
                                Nota Débito elec. <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 61): ?>
                                Nota Crédito elec. <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                              <?php if ($documento['dte'] == 52): ?>
                                Guia despacho elec. <strong>N° <?php echo $documento['folio'] ?></strong>
                              <?php endif ?>
                            </td>
                            <td><?php echo $documento['emision'] ?></td>
                            <td>$<?php echo number_format($documento['total'],'0', ',','.') ?></td>
                            <td class="text-right">
                              <a class="btn btn-icon-toggle delete" href="javascript:void(0)" id="<?php echo $documento['id'] ?>" data-toggle="tooltip" data-placement="top" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>  
                            </td>
                          </tr>
                        <?php endforeach ?>
                      </tbody>
                    </table>                      
                    <?php else: ?>
                      <div class="col-lg-12 text-center">
                        <h1><span class="text-xxxl text-light">Ups <i class="fa fa-terminal text-primary"></i></span></h1>
                        <h2 class="text-light">No se han encontrado registros.</h2>
                      </div>
                    <?php endif ?>
                  </div>
                </div>

              </div>
            </div>
            <div class="section-action style-primary">
              <div class="section-action-row">
                Pulsa el boton para crear un nuevo registro.
              </div>
              <div class="section-floating-action-row">
                <a class="btn ink-reaction btn-floating-action btn-lg btn-default-dark create" href="ingreso/index.php" title="Crear nuevo">
                  <i class="fa fa-plus"></i>
                </a>
              </div>
            </div>
        </section>
      </div>

    </div>
  </body>


  <script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/toastr.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/DataTables/dataTables.min.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/jquery.Rut.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/numerosYletras.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#dttCuentas').DataTable({
        'language': {
          'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
        },
        "responsive": true,
        "ordering":  false,
        "serverSide": false, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
      });
      <?php if (isset($_SESSION["offlog"]) && !empty($_SESSION["offlog"])): ?>
        toastr.info('<?php echo $_SESSION["offlog"] ?>', '');
        <?php unset($_SESSION["offlog"]) ?>
      <?php endif ?>
    });
  </script>
</html>