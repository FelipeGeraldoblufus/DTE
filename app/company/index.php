<?php 
require_once '../../config.php';
$_SESSION['navMenu']	=	'company';
$Empresa = new Empresa();
$ActEco = new ActEco();
$datos = $Empresa->listaEmpresa();
$ActividadEconomica = $ActEco->idActEco($datos['acteco']);
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
		<?php require_once '../../inc/header.php'; ?>
		<?php require_once '../../inc/menu.php'; ?>

		<!-- BEGIN BASE-->
  	<div id="base">
      <!-- BEGIN CONTENT-->
        <div id="content">
          <section>
            <div class="section-header">
              <ol class="breadcrumb">
                <li class="active">Empresa</li>
              </ol>
            </div>
						<div class="section-body">
              <div class="col-lg-offset-1 col-md-10 col-sm-12">
                <div class="card">
                  <div class="card-body">
                  	<div class="row">
                  		<div class="col-md-6">
                  			<h1><?php echo $datos['rznsoc'] ?></h1>
												<img src="<?php echo BASEURL.$datos['logo'] ?>" alt="<?php echo $datos['rznsoc'] ?>" /><br>
												<?php echo $datos['rut'] ?><br/>
												<?php echo $datos['giro']?><br/>
												<?php echo $ActividadEconomica['actividad_economica']?><br>
                  		</div>
                  		<div class="col-md-6">
                  			<h4>Informacion de contacto</h4>
												<span class="opacity-50">Telefonos</span><br/>
												<?php echo $datos['telefono']?><br>

												<span class="opacity-50">Email</span><br/>
												<a class="text-medium" href="mailto:<?php echo $datos['correo']?>"><?php echo $datos['correo']?></a><br>

												<span class="opacity-50">Direccion</span><br/>
												<?php echo $datos['direccion']?><br/>
												<?php echo $datos['comuna']?><br/>
												<?php echo $datos['ciudad']?>
                  		</div>
                  	</div>
                  	<div class="row">
                  		<div class="panel">
			                  <div class="panel-body">
			                    <a href="update.php" class="btn btn-block ink-reaction btn-primary">Actualizar Datos</a>
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
	<script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
	<script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
	<script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
	<script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
</html>
