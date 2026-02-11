<?php
require_once '../../../config.php';
$_SESSION['navMenu']  = 'company';

$Usuario = new Usuario();
$Permiso = new Permiso();
$Empresa = new Empresa();
$listaUsuario = $Usuario->listaUsuario();
$listaPermiso = $Permiso->listaPermiso();
$rutRazsoc = $Empresa->rutRazsoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>DTE Chile | Usuarios Sistema</title>
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
            <li><a href="<?php echo BASEURL ?>usuarios.php">Usuarios</a></li>
            <li class="active">Panel de Usuarios</li>
          </ol>
        </div>

        <div class="section-body">
          <div class="col-lg-offset-1 col-md-10 col-sm-12">

            <div class="card">
              <div class="card-body">
                <?php if (isset($listaUsuario) && !empty($listaUsuario)): ?>
                <table id="dttUsuarios" class="table table-hover">
                  <thead>
                    <tr>
                      <th>Foto</th>
                      <th>Nombre</th>
                      <th>Email</th>
                      <th>Cargo</th>
                      <th class="text-right">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($listaUsuario as $usuario): ?>
                      <tr>
                        <td><img class="img-circle size-1" src="<?php echo BASEURL.$usuario['foto'] ?>" alt="" /></td>
                        <td><?php echo $usuario['nombre'].' '.$usuario['apellido'] ?></td>
                        <td><?php echo $usuario['email'] ?></td>
                        <td><?php echo $Permiso->perfilPermiso($usuario['permiso_id']) ?></td>
                        <td class="text-right">
                          <a id="<?php echo $usuario['rut'] ?>" class="btn btn-icon-toggle password" data-toggle="tooltip" data-placement="top" data-original-title="Cambiar Contraseña"><i class="fa fa-asterisk"></i></a>
                          <a id="<?php echo $usuario['rut'] ?>" class="btn btn-icon-toggle update" data-toggle="tooltip" data-placement="top" data-original-title="Editar"><i class="fa fa-pencil"></i></a>
                          <a id="<?php echo $usuario['rut'] ?>" class="btn btn-icon-toggle delete" data-toggle="tooltip" data-placement="top" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>
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
          <form enctype="multipart/form-data" autocomplete="off" class="sendForm">
            <div class="form-group">
              <label for="">R.U.T.</label>
              <input type="text" name="rut" id="rut" class="form-control" maxlength="12" onpaste="javascript:return false;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Nombre</label>
                  <input type="text" name="nombre" placeholder="" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Apellidos</label>
                  <input type="text" name="apellido" placeholder="" class="form-control">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Contraseña</label>
                  <input type="password" name="password" placeholder="" class="form-control">
                </div> 
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Repetir Contraseña</label>
                  <input type="password" name="password2" placeholder="" class="form-control">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Direccion</label>
                  <input type="text" name="direccion" placeholder="" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Comuna</label>
                  <input type="text" name="comuna" placeholder="" class="form-control">
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Telefono</label>
                  <input type="text" name="telefono" placeholder="" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Email</label>
                  <input type="email" name="email" placeholder="" class="form-control">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="">Permisos</label>
              <select name="permiso" class="form-control">
                <?php foreach ($listaPermiso as $permiso): ?>
                  <option value="<?php echo $permiso["id"]; ?>"><?php echo $permiso["permiso"]; ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label for="">Empresa </label>
              <select name="empresa" class="form-control">
                <?php foreach ($rutRazsoc as $datos): ?>
                  <option value="<?php echo $datos["rut"]; ?>"><?php echo $datos["rznsoc"] ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label for="">Imagen de Perfil</label>
              <input type="file" name="foto[]" accept="image/jpeg,image/jpg" class="form-control">
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
          <form enctype="multipart/form-data" autocomplete="off" class="sendForm">
            <div class="form-group">
              <label for="">R.U.T.</label>
              <input type="text" name="rut_edit" id="rut_edit" class="form-control" maxlength="12" onpaste="javascript:return false;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" disabled="disabled">
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Nombre</label>
                  <input type="text" name="nombre_edit" placeholder="" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Apellidos</label>
                  <input type="text" name="apellido_edit" placeholder="" class="form-control">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Direccion</label>
                  <input type="text" name="direccion_edit" placeholder="" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Comuna</label>
                  <input type="text" name="comuna_edit" placeholder="" class="form-control">
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Telefono</label>
                  <input type="text" name="telefono_edit" placeholder="" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Email</label>
                  <input type="email" name="email_edit" placeholder="" class="form-control">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="">Permisos</label>
              <select name="permiso_edit" class="form-control">
                <?php foreach ($listaPermiso as $permiso): ?>
                  <option value="<?php echo $permiso["id"]; ?>"><?php echo $permiso["permiso"]; ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label for="">Cambiar foto Perfil</label>
              <input type="file" name="foto[]" accept="image/jpeg" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="id" value="">
            <input type="hidden" name="imagen_actual" value="">
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

  <!-- FORMULARIO DE ELIMINACION -->
  <div class="modal fade" id="passModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="formModalLabel">Cambiar Contraseña</h4>
        </div>
        <div class="modal-body text-center">
          <form autocomplete="off" class="sendForm">
            <h3>¿Desea cambiar la contraseña a: <span class="text-bold" id="nombre"></span>?</h3>
            <br>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Contraseña</label>
                  <input type="password" name="password" placeholder="" class="form-control">
                </div> 
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="">Repetir Contraseña</label>
                  <input type="password" name="password2" placeholder="" class="form-control">
                </div>
              </div>
            </div>
            <br><br>
        <div class="modal-footer">
            <input type="hidden" name="id" value="">
            <input type="hidden" name="data" value="changePassword">
            <button type="button" class="btn btn-default-dark" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn ink-reaction btn-primary">Guardar</button>
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
      $('#dttUsuarios').DataTable({
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

    $(document).on('click', '.password', function(){
      var id = $(this).attr("id");
      $.ajax({
        url:"data.php?id="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('#passModal').modal('show');
          $('[name="id"]').val(id);
          $('#nombre').html(data.nombre+' '+data.apellido);
        }
      })
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
          $('[name="rut_edit"]').val(data.rut);
          $('[name="nombre_edit"]').val(data.nombre);
          $('[name="apellido_edit"]').val(data.apellido);
          $('[name="direccion_edit"]').val(data.direccion);
          $('[name="comuna_edit"]').val(data.comuna);
          $('[name="telefono_edit"]').val(data.telefono);
          $('[name="email_edit"]').val(data.email);
          $('[name="permiso_edit"]').val(data.permiso_id);
          $('[name="imagen_actual"]').val(data.foto);
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
</body>

</html>