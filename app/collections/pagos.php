<?php require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Cobranzas</title>
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
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/DataTables/TableTools.css">
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
                <li><a href="<?php echo BASEURL ?>cobranzas_pagos.php">Cobranzas y Pagos</a></li>
                <li><a href="<?php echo BASEURL ?>app/cobranzas/">Panel de Cobranzas</a></li>
                <li class="active">Pagos</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-10">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                        <h5 class="panel-title">Seccion de Pagos</h5><br>
                      <div class="panel panel-default">
                        <div class="panel-body">
                          <div class="col-xs-4">
                            <address>
                              <strong>Nombre del CLiente S.A.</strong><br>
                              <strong>RUT: 11.111.111-1</strong><br>
                              AV. Direccion #123<br>
                              Santiago, Chile<br>
                            </address>
                          </div>
                          <div class="col-xs-4">
                            <address>
                              Contacto: Nombre de Contacto<br>
                              Email: contacto@ejemplo.cl<br>
                              <abbr title="Telefono">Telefono:</abbr> +56 9 1234 5678
                            </address>
                          </div>
                          <div class="col-xs-4">
                            <div class="well">
                              <div class="clearfix">
                                <div class="pull-left"> FACTURA ELECTRONICA : </div>
                                <div class="pull-right"> 6541 </div>
                              </div>
                              <div class="clearfix">
                                <div class="pull-left"> FECHA DE EMISION : </div>
                                <div class="pull-right"> 10/02/19 </div>
                              </div>
                              <div class="clearfix">
                                <div class="pull-left"> FECHA DE VENCIMIENTO : </div>
                                <div class="pull-right"> 11/02/19 </div>
                              </div>
                            </div>
                          </div>

                          <div id="item">
                            <div class="row">
                              <div class="col-sm-4">
                                <div class="input-group input-group-sm">
                                  <label class="input-group-addon">Forma de Pago</label>
                                  <select class="form-control" name="FmaPago">
                                    <option value="0">Forma de pago</option>
                                    <option value="1" selected>Efectivo</option>
                                    <option value="2">Transferencia</option>
                                    <option value="3">Cheque</option>
                                    <option value="4">Valevista</option>
                                  </select>                          
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <label class="checkbox-inline checkbox-styled">
                                  <input type="checkbox" value="option1" checked="true"><span>Pago Parcial</span>
                                </label>
                                <label class="checkbox-inline checkbox-styled">
                                  <input type="checkbox" value="option2"><span>Pago Total</span>
                                </label>
                              </div>
                              <div class="col-sm-4">
                                <div class="form-group input-group-sm">
                                  <input type="number" max="999999999999" maxlength="12" class="form-control" name="QtyItem" placeholder="Monto a Pagar" required/>
                                </div>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-sm-12">
                                <div class="form-group input-group-sm">
                                  <textarea name="DscItem" class="form-control" placeholder="Comentarios" rows="3" /></textarea>
                                </div>
                              </div>                
                            </div>
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="input-group input-group-sm">
                                  <label class="input-group-addon">Destino del pago</label>
                                  <select class="form-control" name="FmaPago">
                                    <option value="0" selected>Destino del Pago (Cuentas de la empresa)</option>
                                    <option value="1">Caja Chica</option>
                                    <option value="2">Cuenta Banco 1</option>
                                    <option value="3">Cuenta Banco 2</option>
                                    <option value="4">Cuenta Banco 3</option>
                                  </select>                          
                                </div>
                              </div>
                            </div>
                          </div>

                          <br>
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="form-group input-group-sm text-center">
                                <button type="button" class="btn ink-reaction btn-primary" id="agregar_item">Agregar Detalle Pago</button>
                                <button type="button" class="btn ink-reaction btn-warning hidden" id="quitar_item">Remover Detalle Pago</button>
                              </div>
                            </div>
                          </div>
                          <br>

                          <div class="row">
                            <div class="col-sm-12">
                              <button class="btn btn-block ink-reaction btn-default-dark">Generar Pago</button>
                            </div>
                          </div>
                          
                        </div>
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
  <script src="<?php echo BASEURL ?>asset/js/jquery.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/bootstrap.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/DataTables/dataTables.min.js" type="text/javascript"></script>
  <div id="script"></div>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#dttCliente').DataTable({
        'language': {
          'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
        },
        "responsive": true,
        "ordering":  false,
        "serverSide": false, //Feature control DataTables' server-side processing mode.
        "order": [, //Initial no order.
      });
    });
  </script>
    <script type="text/javascript">
  $(document).ready(function(){
    var count_item=0;
    $('#agregar_item').click(function(){
    $('#quitar_item').removeClass('hidden');
    ++count_item;
    $('#item').append(  '<div id="nueva_item' +count_item+ '"><br>' +
                        '  <div class="row"> ' +
                        '      <div class="col-sm-4"> ' +
                        '        <div class="input-group input-group-sm"> ' +
                        '          <label class="input-group-addon">Forma de Pago</label> ' +
                        '          <select class="form-control" name="FmaPago"> ' +
                        '            <option value="0">Forma de pago</option> ' +
                        '            <option value="1" selected>Efectivo</option> ' +
                        '            <option value="2">Transferencia</option> ' +
                        '            <option value="3">Cheque</option> ' +
                        '            <option value="4">Valevista</option> ' +
                        '          </select> ' +
                        '        </div> ' +
                        '      </div> ' +
                        '      <div class="col-sm-4"> ' +
                        '        <label class="checkbox-inline checkbox-styled"> ' +
                        '          <input type="checkbox" value="option1" checked="true"><span>Pago Parcial</span> ' +
                        '        </label> ' +
                        '        <label class="checkbox-inline checkbox-styled"> ' +
                        '          <input type="checkbox" value="option2"><span>Pago Total</span> ' +
                        '        </label> ' +
                        '      </div> ' +
                        '      <div class="col-sm-4"> ' +
                        '        <div class="form-group input-group-sm"> ' +
                        '          <input type="number" max="999999999999" maxlength="12" class="form-control" name="QtyItem" placeholder="Monto a Pagar" required/> ' +
                        '        </div> ' +
                        '      </div> ' +
                        '    </div> ' +
                        '    <div class="row"> ' +
                        '      <div class="col-sm-12"> ' +
                        '        <div class="form-group input-group-sm"> ' +
                        '          <textarea name="DscItem" class="form-control" placeholder="Comentarios" rows="3" /></textarea> ' +
                        '        </div> ' +
                        '      </div>                            </div>   ' +
                        '    <div class="row"> ' +
                        '      <div class="col-sm-12"> ' +
                        '        <div class="input-group input-group-sm"> ' +
                        '          <label class="input-group-addon">Destino del pago</label> ' +
                        '          <select class="form-control" name="FmaPago"> ' +
                        '            <option value="0" selected>Destino del Pago (Cuentas de la empresa)</option> ' +
                        '            <option value="1">Caja Chica</option> ' +
                        '            <option value="2">Cuenta Banco 1</option> ' +
                        '            <option value="3">Cuenta Banco 2</option> ' +
                        '            <option value="4">Cuenta Banco 3</option> ' +
                        '          </select>                           ' +
                        '        </div> ' +
                        '      </div> ' +
                        '    </div>  ' +
                        '</div> ');

    $('#script').append(  '<script type="text/javascript">' +
                          '\n$(document).ready(function(){' +
                          '\n$("#codProducto'+ count_item +'").typeahead({' +
                          '\n  source: function(query, result){' +
                          '\n    $.ajax({' +
                          '\n      url:"data.php",' +
                          '\n      method:"POST",' +
                          '\n      dataType:"json",' +
                          '\n      data:{codProducto:query},' +
                          '\n      success:function(data){ result($.map(data, function(item){ return item; }))}' +
                          '\n    })' +
                          '\n  }' +
                          '\n});' +
                          '\n$("#codProducto'+ count_item +'").change(function(){' +
                          '\n  $.ajax({' +
                          '\n    url:"data.php",' +
                          '\n    type:"POST",' +
                          '\n    dataType:"json",' +
                          '\n    data:{ datoProducto:$("#codProducto'+ count_item +'").val()},' +
                          '\n  }).done(function(respuesta){' +
                          '\n    $("[name=\'item['+ count_item +'][NmbItem]\']").val(respuesta.nombre);' +
                          '\n    $("[name=\'item['+ count_item +'][PrcItem]\']").val(respuesta.precio);' +
                          '\n    $("[name=\'item['+ count_item +'][DscItem]\']").val(respuesta.descripcion);' +
                          '\n    $("[name=\'item['+ count_item +'][UnmdItem]\']").val(respuesta.unimed);' +
                          '\n  });' +
                          '\n});' +
                          '\n});' +
                          '\n <\/script>');
  });

  $('#quitar_item').click(function() {
    $('#nueva_item'+count_item).remove();

    count_item--;
    
    if (count_item==0) {
       $('#quitar_item').addClass('hidden');
    }
  });
  });
  </script>
</html>