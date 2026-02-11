<?php
require_once '../../config.php';
$_SESSION['navMenu']  = 'products';
$Producto = new Producto();
$Stock = new Stock();
$listaProducto = $Producto->listaProducto();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>DTE Chile | Productos</title>
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
  <?php require_once '../../inc/header.php'; ?>
  <?php require_once '../../inc/menu.php'; ?>

  <!-- BEGIN BASE-->
  <div id="base">
    <!-- BEGIN CONTENT-->
    <div id="content">
      <section>

        <div class="section-header">
          <ol class="breadcrumb">
            <li class="active">Productos</li>
          </ol>
        </div>

        <div class="section-body">
          <div class="col-lg-offset-1 col-md-10 col-sm-12">

            <div class="card">
              <div class="card-body">
                <?php if (isset($listaProducto) && !empty($listaProducto)): ?>
                <table id="dttProductos" class="table table-hover">
                  <thead>
                    <tr>
                      <th>Codigo</th>
                      <th>Nombre</th>
                      <th>Neto</th>
                      <th>Stock</th>
                      <th class="text-right">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($listaProducto as $producto): ?>
                      <tr>
                        <td><?php echo $producto['codigo'] ?></td>
                        <td><?php echo $producto['nombre'] ?></td>
                        <td>$ <?php echo number_format($producto['precio'],'0', ',','.'); ?></td>
                        <td><?php $stockProducto = $Stock->stockProducto($producto['codigo']); ?>
                        <?php if (!empty($stockProducto)): ?>
                          <?php echo $stockProducto['stock'] ?> <a class="btn btn-icon-toggle" href="<?php echo BASEURL ?>app/stock/index.php?id=<?php echo $producto['codigo'] ?>" data-toggle="tooltip" data-placement="top" data-original-title="Agregar o Modificar Stock"><i class="fa fa-archive"></i> </a> 
                          <?php else: ?>
                            Sin Stock <a class="btn btn-icon-toggle" href="<?php echo BASEURL ?>app/stock/?id=<?php echo $producto['codigo'] ?>" data-toggle="tooltip" data-placement="top" data-original-title="Agregar o Modificar Stock"><i class="fa fa-archive"></i> </a> 
                          <?php endif ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-icon-toggle update" href="javascript:void(0);" id="<?php echo $producto['codigo'] ?>" data-toggle="tooltip" data-placement="top" data-original-title="Editar"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-icon-toggle delete" href="javascript:void(0);" id="<?php echo $producto['codigo'] ?>" data-toggle="tooltip" data-placement="top" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></a>  
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
          <form autocomplete="off" class="sendForm">
            <div class="form-group">
              <label for="">Codigo del Producto</label>
              <input type="text" name="codigo" placeholder="" class="form-control">
            </div>
            <div class="form-group">
              <label id="">Nombre del Producto</label>
              <input type="text" name="nombre" placeholder="" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Precio del Producto (SIN IVA)</label>
              <input type="number" name="precio" placeholder="" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Unidad de medida del Producto</label>
              <input type="text" name="unimed" placeholder="" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Descripcion del Producto</label>
              <textarea name="descripcion" class="form-control" placeholder="" maxlength="500"></textarea>
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
          <form autocomplete="off" class="sendForm">
            <div class="form-group">
              <label for="">Codigo del Producto</label>
              <input type="text" name="codigo_edit" placeholder="" class="form-control" disabled="">
            </div>
            <div class="form-group">
              <label id="">Nombre del Producto</label>
              <input type="text" name="nombre_edit" placeholder="" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Precio del Producto (SIN IVA)</label>
              <input type="number" name="precio_edit" placeholder="" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Unidad de medida del Producto</label>
              <input type="text" name="unimed_edit" placeholder="" class="form-control">
            </div>
            <div class="form-group">
              <label for="">Descripcion del Producto</label>
              <textarea name="descripcion_edit" class="form-control" placeholder="" maxlength="500"></textarea>
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
    $(document).ready(function() {
      $('#dttProductos').DataTable({
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
          $('[name="codigo_edit"]').val(data.codigo);
          $('[name="nombre_edit"]').val(data.nombre);
          $('[name="precio_edit"]').val(data.precio);
          $('[name="unimed_edit"]').val(data.unimed);
          $('[name="descripcion_edit"]').val(data.descripcion);
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