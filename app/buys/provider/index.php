<?php
require_once '../../../config.php';
$_SESSION['navMenu']  = 'buys';
$Proveedor = new Proveedor();
$listaProveedor = $Proveedor->listaProveedor();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>DTE Chile | Proveedores</title>
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
            <li><a href="<?php echo BASEURL ?>app/buys/">Compras</a></li>
            <li class="active">Proveedores</li>
          </ol>
        </div>

        <div class="section-body">
          <div class="col-lg-offset-1 col-md-10 col-sm-12">

            <div class="card">
              <div class="card-body">
                <?php if (isset($listaProveedor) && !empty($listaProveedor)): ?>
                <table id="dttProveedor" class="table table-hover">
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
                    <?php foreach ($listaProveedor as $cliente): ?>
                      <tr>
                        <td><?php echo $cliente['rut'] ?></td>
                        <td><?php echo $cliente['rznsoc'] ?></td>
                        <td><?php echo $cliente['direccion'].', '.$cliente['comuna'] ?></td>
                        <td><?php echo $cliente['contacto']; ?></td>
                        <td class="text-right">
                          <a class="btn btn-icon-toggle" href="accounts/index.php?id=<?php echo $cliente['rut'] ?>" id="" data-toggle="tooltip" data-placement="top" data-original-title="Cuentas"><i class="fa fa-university"></i></a>
                          <a class="btn btn-icon-toggle" href="dues/index.php?id=<?php echo $cliente['rut'] ?>" id="" data-toggle="tooltip" data-placement="top" data-original-title="Deudas"><i class="fa fa-usd"></i></a>
                          
                          <a id="<?php echo $cliente['rut'] ?>" href="javascript:void(0)" class="btn btn-icon-toggle update" data-toggle="tooltip" data-placement="top" data-original-title="Editar"><i class="fa fa-pencil"></i></a>
                          <a id="<?php echo $cliente['rut'] ?>" href="javascript:void(0)" class="btn btn-icon-toggle delete" data-toggle="tooltip" data-placement="top" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>
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

  <!-- FORMULARIO DE CREACION -->
  <div class="modal fade" id="newModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="formModalLabel">Crear nuevo registro</h4>
        </div>
        <div class="modal-body">
          <form class="sendForm" autocomplete="off">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="rut">R.U.T.</label>
                  <input type="text" id="rut" name="rut" class="form-control" maxlength="12" onpaste="javascript:return false;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="rznsoc">Razon Social</label>
                  <input type="text" name="rznsoc" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="numid">Numero identificador del receptor extranjero</label>
                  <input type="text" name="numid" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="nacionalidad">Nacionalidad del receptor extranjero</label>
                  <input type="text" name="nacionalidad" class="form-control">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="">Giro del receptor</label>
              <input type="text" name="giro" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Contacto receptor</label>
              <input type="text" name="contacto" class="form-control">              
            </div>
            <div class="form-group">
              <label for="">Correo contacto</label>
              <input type="text" name="correo" class="form-control">
            </div>

            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Direccion</label>
                  <input type="text" name="direccion" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Comuna</label>
                  <input type="text" name="comuna" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Ciudad</label>
                  <input type="text" name="ciudad" class="form-control">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Direccion Postal</label>
                  <input type="text" name="direccionpostal" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Comuna Postal</label>
                  <input type="text" name="comunapostal" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Ciudad Postal</label>
                  <input type="text" name="ciudadpostal" class="form-control">
                </div>
              </div>
            </div>

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
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="rut_edit">R.U.T.</label>
                  <input type="text" id="rut_edit" name="rut_edit" class="form-control" maxlength="12" onpaste="javascript:return false;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="rznsoc">Razon Social</label>
                  <input type="text" name="rznsoc_edit" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="numid">Numero identificador del receptor extranjero</label>
                  <input type="text" name="numid_edit" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="nacionalidad">Nacionalidad del receptor extranjero</label>
                  <input type="text" name="nacionalidad_edit" class="form-control">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="">Giro del receptor</label>
              <input type="text" name="giro_edit" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Contacto receptor</label>
              <input type="text" name="contacto_edit" class="form-control">              
            </div>
            <div class="form-group">
              <label for="">Correo contacto</label>
              <input type="text" name="correo_edit" class="form-control">
            </div>

            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Direccion</label>
                  <input type="text" name="direccion_edit" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Comuna</label>
                  <input type="text" name="comuna_edit" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Ciudad</label>
                  <input type="text" name="ciudad_edit" class="form-control">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Direccion Postal</label>
                  <input type="text" name="direccionpostal_edit" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Comuna Postal</label>
                  <input type="text" name="comunapostal_edit" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="">Ciudad Postal</label>
                  <input type="text" name="ciudadpostal_edit" class="form-control">
                </div>
              </div>
            </div>

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
    $('#rut').Rut({ on_error: function(){ toastr.info('ERROR! R.U.T. incorrecto.', ''); $('#rut').val(''); $("#rut").focus();}, format_on: 'keyup'});

    $('#rut_edit').Rut({ on_error: function(){ toastr.info('ERROR! R.U.T. incorrecto.', ''); $('#rut_edit').val(''); $("#rut_edit").focus();}, format_on: 'keyup'});

    $(document).ready(function() {
      $('#dttProveedor').DataTable({
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
          $('[name="rut_edit"]').val(data.rut);
          $('[name="rznsoc_edit"]').val(data.rznsoc);
          $('[name="numid_edit"]').val(data.numid);
          $('[name="nacionalidad_edit"]').val(data.nacionalidad);
          $('[name="giro_edit"]').val(data.giro);
          $('[name="contacto_edit"]').val(data.contacto);
          $('[name="correo_edit"]').val(data.correo);
          $('[name="direccion_edit"]').val(data.direccion);
          $('[name="comuna_edit"]').val(data.comuna);
          $('[name="ciudad_edit"]').val(data.ciudad);
          $('[name="direccionpostal_edit"]').val(data.direccionpostal);
          $('[name="comunapostal_edit"]').val(data.comunapostal);
          $('[name="ciudadpostal_edit"]').val(data.ciudadpostal);
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
          $('#valor').html(data.rznsoc);
        }
      })
    });
  </script>
</body>

</html>