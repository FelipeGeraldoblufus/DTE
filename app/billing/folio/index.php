<?php
require_once '../../../config.php';
$_SESSION['navMenu']  = 'billing';
$Folio = new Folio();
$TipoFolio = new TipoFolio();
$listado = $Folio->listaFolio();
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Empresa</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/style.css">
    <style>
      .bg-danger-soft {
        background-color: rgba(236, 38, 8, 0.4) !important;
      }
    </style>
  </head>
  <body class="menubar-hoverable header-fixed full-content">
    <?php require_once '../../../inc/header.php'; ?>
    <?php require_once '../../../inc/menu.php'; ?>

    <div id="base">
      <div id="content">
        <section>
          <div class="section-header">
            <div class="row">
              <div class="col-md-6">
                <ol class="breadcrumb">
                  <li><a href="<?php echo BASEURL.'app/billing/'?>">Facturación</a></li>
                  <li class="active">Folios</li>
                </ol>
              </div>
              <div class="col-md-6 text-right">
                <div class="btn-group">
                  <button type="button" class="btn ink-reaction btn-default-light dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href="nuevo.php"><i class="fa fa-plus icon-style-danger"></i> Subir</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="section-body">
            <div class="row">
              <?php 
              if (isset($listado) && !empty($listado)) {
                foreach ($listado as $lista) {
              ?>              
              <div class="col-md-4">
                <div class="card <?php echo ($lista['folio_actual'] > $lista['hasta']) ? 'bg-danger-soft' : ''; ?>">
                  <div class="card-head">
                    <header>
                      <?php
                        $tipoLista = $TipoFolio->idTipoFolio($lista['tipo_folio']);
                        echo $tipoLista['tipo_nombre'];
                      ?>
                    </header>
                    <?php if ($lista['folio_actual'] > $lista['hasta']) { ?>
                      <div class="tools">
                      <a href="eliminar.php?id=<?php echo $lista['tipo_folio']; ?>" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" title="Eliminar">
                          <i class="fa fa-times"></i>
                        </a>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="card-body">
                    <table class="table table-hover no-margin table-condensed">
                      <tbody>
                        <tr>
                          <td>Desde</td>
                          <td><?php echo $lista['desde'] ?></td>
                        </tr>
                        <tr>
                          <td>Hasta</td>
                          <td><?php echo $lista['hasta'] ?></td>
                        </tr>
                        <tr>
                          <td>Actual</td>
                          <td><?php echo $lista['folio_actual']?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php
                  }
                } else {
                  echo 'No se registran datos.';
                }
              ?>
            </div>
          </div>
        </section>
      </div>
    </div>

    <script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
    <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
    <script src="<?php echo BASEURL ?>asset/js/application.js"></script>
    <script src="<?php echo BASEURL ?>asset/js/navigation.js"></script>
  </body>
</html>