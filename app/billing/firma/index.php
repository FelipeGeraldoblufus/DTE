<?php
require_once '../../../config.php';
$_SESSION['navMenu']  = 'billing';
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Empresa</title>
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
                <li><a href="<?php echo BASEURL.'app/billing/'?>">Facturación</a></li>
                <li class="active">Firma Electronica</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-11 col-sm-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                    	<div class="col-sm-12">
                        <div class="form-group input-group-sm">
													<form action="upload.php" method="post" enctype="multipart/form-data">
														<select name="rut_usuario" class="form-control">
													    <option  value="0" selected="selected">Seleccione Usuario Titular de la firma</option>
													    <?php
													    $Usuario = new Usuario();
													    $listaUsuario = $Usuario->listaUsuario();
													    foreach ($listaUsuario as $key) {
													    ?>
												        <option value="<?php echo $key["rut"]; ?>"><?php echo $key["nombre"].' '.$key["apellido"]; ?></option>
													    <?php
													    }
													    ?>
												  	</select><br>
														<input type="file" class="form-control" name="archivo" accept="application/x-pkcs12"><br>
														<input type="password" class="form-control" name="password" placeholder="Contraseña de la Firma"><br><br>
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
  <script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
</html>