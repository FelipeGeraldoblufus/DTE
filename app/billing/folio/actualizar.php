<?php require_once '../../../config.php'; ?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Folios</title>
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
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/style.css">
    <!-- BEGIN STYLESHEET -->
  </head>
  <body class="menubar-hoverable menubar-first">
    <?php require_once '../../../inc/header.php'; ?>
    <?php require_once '../../../inc/menu.php'; ?>

    <!-- BEGIN BASE-->
    <div id="base">
      <!-- BEGIN CONTENT-->
        <div id="content">
          <section>
            <div class="section-header">
              <ol class="breadcrumb">
                <a href="<?php echo BASEURL ?>folio_firma.php"><li class="active">Folios y Firma</li></a>
                <li class="active">Actualizar Folios</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-11 col-sm-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">

                      <div class="col-sm-12">
                        <div class="form-group input-group-sm">
													<form action="update.php" method="post" enctype="multipart/form-data">
														<select name="tipoFolio" class="form-control">
													    <option  value="0" selected="selected">Seleccione tipo de Folio a subir</option>
													    <?php
													    $TipoFolio = new TipoFolio();
													    $tipofolio = $TipoFolio->listaTipoFolio();
													    foreach ($tipofolio as $key) {
													    ?>
												        <option value="<?php echo $key["tipo_numero"]; ?>"><?php echo $key["tipo_nombre"]; ?></option>
													    <?php
													    }
													    ?>
												  	</select><br>
														<input type="file" class="form-control" name="archivo" accept="text/xml"><br>
													
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="panel panel-default">
                        <div class="panel-body">
                          <button type="submit" class="btn btn-block ink-reaction btn-primary">CARGAR FOLIOS</button>
                          </form>
                        </div>
                      </div>
                    </div>                      
                  </div>
                </div>
                  </div>
                </div>
              </div>
              
            </div><!--end .section-body -->
        </section>
      </div>
      <!-- END CONTENT -->
    </div>
  </body>
  <script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
</html>