<?php 
require_once '../../config.php';
$_SESSION['navMenu']	=	'buys';
$Empresa = new Empresa();
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
