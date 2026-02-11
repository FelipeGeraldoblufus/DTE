<?php 
require_once '../../../../config.php';
$_SESSION['navMenu']  = 'sales';
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $rut = $_GET['id'];
  $Cliente = new Cliente();
  $CuentaCliente = new CuentaCliente();
  $Banco = new Banco();
  $TipoCuenta = new TipoCuenta();

  $getCliente = $Cliente->getCliente($rut);
  $listaCuenta = $CuentaCliente->listaCuenta($rut);
  $listaBanco = $Banco->listaBanco();
  $listaTipoCuenta = $TipoCuenta->listaTipoCuenta();  
} else {
  header('Location: ../index.php');
}

?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<title>DTE Chile | Cuentas Clientes</title>
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
		<?php require_once '../../../../inc/header.php'; ?>
		<?php require_once '../../../../inc/menu.php'; ?>

		<!-- BEGIN BASE-->
  	<div id="base">
      <!-- BEGIN CONTENT-->
        <div id="content">
          <section>
            <div class="section-header">
              <ol class="breadcrumb">
              	<li><a href="<?php echo BASEURL ?>app/sales/">Ventas</a></li>
                <li class="active"><a href="<?php echo BASEURL ?>app/sales/client/">Ventas</a></li>
                <li class="active">Cuentas Clientes</li>
              </ol>
            </div>
						<div class="section-body">
              <div class="col-lg-offset-1 col-md-10 col-sm-12">

                <div class="card">
                  <div class="card-body">
                  	<?php if (isset($listaCuenta) && !empty($listaCuenta)): ?>
                  	<table id="dttCuentas" class="table table-hover">
				              <thead>
				                <tr>
                          <th>Nombre</th>
				                  <th>Tipo</th>
				                  <th>Banco</th>
				                  <th>Numero</th>
				                  <th></th>
				                </tr>
				              </thead>
				              <tbody>
				                <?php foreach ($listaCuenta as $cuenta): ?>
				                  <tr>
                            <td>
                              <?php if (!empty($cuenta['nombre'])): ?>
                                <?php echo $cuenta['nombre'] ?>
                              <?php else: ?>
                                Sin Nombre
                              <?php endif ?>
                            </td>
				                    <td>
                              <?php if (!empty($cuenta['tipo_cuenta_id'])): ?>
                                <?php echo $TipoCuenta->tipoCuenta($cuenta['tipo_cuenta_id']) ?>
                              <?php else: ?>
                                Cuenta desconocida
                              <?php endif ?>
                            </td>
				                    <td>
                              <?php if (!empty($cuenta['banco_id'])): ?>
                                <?php echo $Banco->nombreBanco($cuenta['banco_id']) ?>
                              <?php else: ?>
                                Sin Banco
                              <?php endif ?>
                            </td>
                            <td>
                              <?php if (!empty($cuenta['numero'])): ?>
                                <?php echo $cuenta['numero'] ?>
                              <?php else: ?>
                                Sin Numero
                              <?php endif ?>    
                              </td>
				                    <td class="text-right">
                              <a class="btn btn-icon-toggle update" href="javascript:void(0)" id="<?php echo $cuenta['id'] ?>" data-toggle="tooltip" data-placement="top" data-original-title="Editar"><i class="fa fa-pencil"></i></a>
                              <a class="btn btn-icon-toggle delete" href="javascript:void(0)" id="<?php echo $cuenta['id'] ?>" data-toggle="tooltip" data-placement="top" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>  
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
								<a class="btn ink-reaction btn-floating-action btn-lg btn-default-dark create" data-toggle="tooltip" data-placement="left" data-original-title="Crear nuevo">
									<i class="fa fa-plus"></i>
								</a>
							</div>
						</div>
        </section>
      </div>

    </div>
	</body>

  <!-- FORMULARIO DE CREACION -->
  <div class="modal fade" id="newModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="formModalLabel">Crear nuevo registro</h4>
        </div>
        <div class="modal-body">
          <em class="text-caption">Debes llenar al menos 1 campo*</em>
            <form class="sendForm" autocomplete="off">
              <div class="form-group floating-label">
                <label for="tipo">Tipo de Cuenta</label>
                <select id="tipo" name="tipo" class="form-control">
                  <option value="">&nbsp;</option>
                  <?php foreach ($listaTipoCuenta as $tipo): ?>
                  <option value="<?php echo $tipo['id'] ?>"><?php echo $tipo['tipo'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>

              <div class="form-group floating-label">
                <label for="nombre">Nombre de la Cuenta</label>
                <input type="text" class="form-control" id="nombre" name="nombre">
              </div>
              <div class="form-group floating-label">
                <label for="numero">Numero de la Cuenta</label>
                <input type="number" class="form-control" id="numero" name="numero">
              </div>

              <div class="form-group floating-label">
                <label for="banco">Banco</label>
                <select id="banco" name="banco" class="form-control">
                  <option value="">&nbsp;</option>
                  <?php foreach ($listaBanco as $banco): ?>
                  <option value="<?php echo $banco['id'] ?>"><?php echo $banco['banco'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            <input type="hidden" name="rut" value="<?php echo $rut ?>">
          </div>
          <div class="modal-footer">
            <input type="hidden" name="data" value="create">
            <button type="button" class="btn btn-default-dark" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn ink-reaction btn-primary">Crear</button>
          </form>
        </div>

      </div>
    </div>
  </div>
  <!-- FORMULARIO DE CREACION -->

  <!-- FORMULARIO DE EDICION -->
  <div class="modal fade" id="updModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="formModalLabel">Editar registro</h4>
        </div>
        <div class="modal-body">
          <form class="sendForm" autocomplete="off">
            <div class="form-group floating-label">
              <label for="tipo">Tipo de Cuenta</label>
              <select id="tipo" name="tipo_edit" class="form-control">
                <option value="">&nbsp;</option>
                <?php foreach ($listaTipoCuenta as $tipo): ?>
                  <option value="<?php echo $tipo['id'] ?>"><?php echo $tipo['tipo'] ?></option>
                <?php endforeach ?>
              </select>
            </div>

            <div class="form-group floating-label">
              <label for="nombre">Nombre de la Cuenta</label>
              <input type="text" class="form-control" id="nombre" name="nombre_edit">
            </div>
            <div class="form-group floating-label">
              <label for="numero">Numero de la Cuenta</label>
              <input type="number" class="form-control" id="numero" name="numero_edit">
            </div>

            <div class="form-group floating-label">
              <label for="banco">Banco</label>
              <select id="banco" name="banco_edit" class="form-control">
                <option value="">&nbsp;</option>
                <?php foreach ($listaBanco as $banco): ?>
                  <option value="<?php echo $banco['id'] ?>"><?php echo $banco['banco'] ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <input type="hidden" name="rut" value="<?php echo $rut ?>">
          </div>
          <div class="modal-footer">
            <input type="hidden" name="id" value="">
            <input type="hidden" name="data" value="update">
            <button type="button" class="btn btn-default-dark" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn ink-reaction btn-primary">Editar</button>
          </form>
        </div>

      </div>
    </div>
  </div>
  <!-- FORMULARIO DE EDICION -->

  <!-- FORMULARIO DE ELIMINACION -->
  <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="formModalLabel">Eliminar Registro</h4>
        </div>
        <div class="modal-body text-center">
          <h1>¿Desea eliminar el registro?</h1>
          <h2 id="valor" class="text-bold"></h2>
        </div>
        <div class="modal-footer">
          <form autocomplete="off" class="sendForm">
            <input type="hidden" name="id" value="">
            <input type="hidden" name="data" value="delete">
            <button type="button" class="btn btn-default-dark" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn ink-reaction btn-primary">ELIMINAR</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- FORMULARIO DE ELIMINACION -->

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
    });

    $(document).ready(function(){
      $('.sendForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
          url: "data.php",
          type: "POST",
          data: new FormData(this),
          contentType:false,
          processData:false,
          success: function(data)
          {
            location.reload();
          },
          error: function()
          {
            toastr.info('ERROR! no se han guardado los datos.', '');
          }
        });
      });
    });

    $(document).on('click', '.create', function(){
      $('#newModal').modal('show');
    });

    $(document).on('click', '.update', function(){
      var id = $(this).attr("id");
      $.ajax({
        url:"data.php?id="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('#updModal').modal('show');
          $('[name="id"]').val(id);
          $('[name="tipo_edit"]').val(data.tipo_cuenta_id);
          $('[name="nombre_edit"]').val(data.nombre);
          $('[name="numero_edit"]').val(data.numero);
          $('[name="banco_edit"]').val(data.banco_id);
        }
      })
    });

    $(document).on('click', '.delete', function(){
      var id = $(this).attr("id");
      $.ajax({
        url:"data.php?id="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('#delModal').modal('show');
          $('[name="id"]').val(id);
          $('#valor').html(data.nombre);
        }
      })
    });
  </script>
</html>