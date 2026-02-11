<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>DTE Chile | Login</title>
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
	  <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/style.css">
		<!-- BEGIN STYLESHEET -->
	</head>
	<body style="">
		<!-- BEGIN BASE-->
		<div id="base">
			<!-- BEGIN OFFCANVAS LEFT -->
			<div class="offcanvas"></div>
			<!-- END OFFCANVAS LEFT -->

			<!-- BEGIN CONTENT-->
			<div id="content">
				<!-- BEGIN BLANK SECTION -->
				<section class="section-account">
					<div class="card contain-sm style-transparent">
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12">
								<br/><br/><br/>
								<form class="form floating-label" action="login.php" accept-charset="utf-8" method="post">
									<div class="form-group">
										<input type="text" class="form-control" id="empresa" name="empresa" maxlength="12" oninput="formatRut(this)" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
										<label for="empresa">R.U.T. Empresa</label>
									</div>
									<div class="form-group">
										<input type="text" class="form-control" id="usuario" name="usuario" maxlength="12" oninput="formatRut(this)" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
										<label for="usuario">R.U.T. Usuario</label>
									</div>
									<div class="form-group">
										<input type="password" class="form-control" id="password" name="password" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
										<label for="password">Password</label>
									</div>
									<br/>
									<div class="row">
										<div class="col-xs-12 text-center">
											<button class="btn btn-primary btn-raised btn-block" type="submit">Ingresar</button>
										</div><!--end .col -->
									</div><!--end .row -->
								</form>
								</div><!--end .col -->
							</div>
						</div><!--end .card -->
					</div>
				</section>
				<!-- BEGIN BLANK SECTION -->
			</div>
			<!-- END CONTENT -->
		<!-- BEGIN OFFCANVAS RIGHT -->
		<div class="offcanvas"></div>
		<!-- END OFFCANVAS RIGHT -->
		</div>
		<!-- END BASE -->
	<script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/toastr.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/jquery.Rut.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/numerosYletras.js" type="text/javascript"></script>
  <script type="text/javascript">
	$(document).ready(function () {
		$('#empresa').Rut({ on_error: function(){ toastr.info('ERROR! R.U.T. Empresa incorrecto.', ''); $('#empresa').val(''); $("#empresa").focus();}, format_on: 'keyup'});

		$('#usuario').Rut({ on_error: function(){ toastr.info('ERROR! R.U.T. Usuario incorrecto.', ''); $('#usuario').val(''); $("#usuario").focus();}, format_on: 'keyup'});
	});
</script>
	</body>
</html>