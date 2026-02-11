<?php 
require_once '../../../../config.php';
$TipoDTE = 39;
$Empresa = new Empresa();
$Folio = new Folio();
$TipoFolio = new TipoFolio();
$Firma = new Firma();
$Cliente = new Cliente();
$Producto = new Producto();
$datos = $Empresa->listaEmpresa();
$datosTipoFolio = $TipoFolio->numeroTipoFolio($TipoDTE);
$datosFolio = $Folio->getFolio(39);
$listaClientes = $Cliente->listaCliente();
$listaProductos = $Producto->listaProducto();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Factura Electronica</title>
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
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/typeahead.css">
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
                <li><a href="<?php echo BASEURL ?>emision.php">Emitir documento</a></li>
                <li class="active">Boleta Electronica</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-11 col-sm-12">
                <div class="card">
                  <div class="card-body">

                    <form class="form-horizontal" action="procesar_boleta.php" method="post" autocomplete="off">
                    <div class="shadow">
                      <br/>
                      <br/>
                      <div class="row">
                        <input type="hidden" name="TipoDTE" value="39">

                        <div class="col-xs-4 col-sm-3 nopadding text-center">
                          <img src="<?php echo BASEURL.$datos['logo'] ?>" alt="<?php echo $datos['rznsoc'] ?>">
                          <input type="hidden" name="logo_empresa" value="<?php echo BASEURL.$datos['logo'] ?>">
                        </div>
                        <div class="col-xs-6 col-sm-3 col-sm-push-6 text-center">
                          <div class="well well-sm">
                            <strong>R.U.T.: <?php echo $datos['rut'] ?></strong><br/>
                            <strong><?php echo $datosTipoFolio['tipo_nombre'] ?></strong><br/>
                            <strong>N° folio <?php echo $datosFolio['folio_actual'] ?> </strong><br/>
                            <input type="hidden" name="Folio" value="<?php echo $datosFolio['folio_actual'] ?>">
                            <input type="hidden" name="rutaFolio" value="<?php echo 'client/folio/39/39.xml' ?>">
                            <!--
                            <input type="hidden" name="Folio" value="<?php echo $datosFolio['folio_actual'] ?>">
                            <input type="hidden" name="rutaFolio" value="<?php echo $datosFolio['ruta'] ?>">
                            -->
                          </div>
                        </div>
                      </div>
                    </div>

                <div class="panel panel-default">
                <div class="panel-heading">
                  <h5 class="panel-title">
                    DATOS EMISOR 
                  </h5>
                </div>
                <div class="panel-body">
                    <div class="row"> 
                      <div class="col-sm-12">
                        <div class="form-group input-group-sm">
                          <input type="hidden" name="RUTEmisor" value="<?php echo $datos['rut'] ?>">
                          <input type="text" class="form-control" name="RznSoc" placeholder="Razón Social" value="<?php echo $datos['rznsoc'] ?>"/>
                        </div>
                      </div>
                    </div>

                    <div class="row"> 
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="DirOrigen" placeholder="Dirección" value="<?php echo $datos['direccion'] ?>"/>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="CmnaOrigen" placeholder="Comuna" value="<?php echo $datos['comuna']?>"/>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="CiudadOrigen" placeholder="Ciudad" value="<?php echo $datos['ciudad']?>"/>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="Telefono" placeholder="Telefono" value="<?php echo $datos['telefono']?>"/>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="CorreoEmisor" placeholder="Email" value="<?php echo $datos['correo']?>"/>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="GiroEmis" placeholder="Giro" value="<?php echo $datos['giro']?>"/> 
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                            <select name="Acteco" class="form-control">
                              <?php
                              $objActeco = new ActEco();
                              $acteco = $objActeco->listaActEco();
                              foreach ($acteco as $key) {
                                if($datos['acteco'] == $key["codigo"]){
                                  ?>
                                  <option  value="<?php echo $key["codigo"]; ?>" selected="selected"><?php echo $key["actividad_economica"]; ?></option>
                                  <?php
                                }else{
                                  ?>
                                  <option value="<?php echo $key["codigo"]; ?>"><?php echo $key["actividad_economica"]; ?></option>
                                  <?php
                                }
                              }
                              ?>
                            </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <div class="row">
                      <div class="col-sm-8">
                        <h5 class="panel-title">
                          DATOS RECEPTOR
                        </h5>
                      </div>
                      <div class="col-sm-4">
                        <a class="btn ink-reaction btn-primary btn-xs pull-right" data-toggle="modal" data-target="#formModal">Buscar <i class="fa fa-search"></i></a>
                      </div>
                    </div>
                  </div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="RUTRecep" placeholder="R.U.T" id="RUTRecep" required/>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="RznSocRecep" placeholder="Razón Social" required/>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="DirRecep" placeholder="Dirección" required/>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="CmnaRecep" placeholder="Comuna" required/>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="CiudadRecep" placeholder="Ciudad" required/>
                        </div>
                      </div>                  
                    </div>
                
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="GiroRecep" placeholder="Giro" required/>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="Contacto" placeholder="Contacto" required/>
                        </div>
                      </div>
                    </div> 
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="form-group input-group-sm">
                          <input type="text" class="form-control" name="RUTSocilita" placeholder="R.U.T solicita (Opcional)" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                         <h5 class="panel-title">INFORMACIÓN</h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="input-group input-group-sm">
                                    <label class="input-group-addon">Fecha Emisión</label>
                                    <input type="date" name="FchEmis" class="form-control" value="<?php echo date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group input-group-sm">
                                    <label class="input-group-addon">Tipo de Venta</label>
                                    <select name="IndServicio" class="form-control" required>
                                        <option value="3" selected>3 - Boleta de Productos y Servicios</option>
                                        <option value="2">2 - Servicios Periódicos Domiciliarios</option>
                                        <option value="1">1 - Servicios Periódicos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <div class="row">
                      <div class="col-sm-8">
                        <h5 class="panel-title">
                          DETALLE
                        </h5>
                      </div>
                      <div class="col-sm-4">
                        <a class="btn ink-reaction btn-primary btn-xs pull-right" data-toggle="modal" data-target="#searchModal">Buscar <i class="fa fa-search"></i></a>
                      </div>
                    </div>
                  </div>
                  <div class="panel-body">
                    <div id="item">
                      <div class="row">
                          <div class="col-sm-2">
                            <div class="form-group input-group-sm">
                              <input type="text" class="form-control" name="item[0][VlrCodigo]" id="codProducto0" placeholder="Codigo" />
                          </div>
                        </div>
                        <div class="col-sm-10">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="item[0][NmbItem]" placeholder="Nombre Producto" required/>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="form-group input-group-sm">
                            <textarea name="item[0][DscItem]" class="form-control" placeholder="Descripción" /></textarea>
                          </div>
                        </div>                
                      </div>
                      <div class="row">
                        <div class="col-sm-2">
                          <div class="form-group input-group-sm">
                            <input type="number" max="999999999999" maxlength="12" class="form-control" name="item[0][QtyItem]" placeholder="Cantidad" required/>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="item[0][PrcItem]" placeholder="Precio" required/>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group input-group-sm">
                            <select name="item[0][CodImpAdic]" class="form-control">
                              <option value="" selected="">Impuesto Adicional</option>
                              <option value="23">Art. de oro, Joyas y Pieles finas 15%</option>
                              <option value="44">Tapices, Casas rod., Caviar y Arm.de aire 15%</option>
                              <option value="24">Licores, Pisco, Destilados 31,5%</option>
                              <option value="25">Vinos, Chichas, Sidras 20,5%</option>
                              <option value="26">Cervezas y Otras bebidas alcohólicas 20,5%</option>
                              <option value="27">Aguas minerales y Beb. analcohól. 10%</option>
                              <option value="271">Beb. analcohól. elevado cont azucar 18%</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-1">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="item[0][UnmdItem]" placeholder="Uni. Med." />
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="item[0][DescuentoPct]" placeholder="% Desc." />
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="item[0][SubTotal]" placeholder="SubTotal" />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group input-group-sm text-center">
                        <button type="button" class="btn ink-reaction btn-primary" id="agregar_item">Agregar Linea de Detalle</button>
                        <button type="button" class="btn ink-reaction btn-warning hidden" id="quitar_item">Remover Linea de Detalle</button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="panel panel-default hidden" id="panel_referencias">  
                  <div class="panel-heading">
                    <h5 class="panel-title">
                      REFERENCIA
                    </h5>
                  </div>
                    <div class="panel-body">
                      <div id="refe">
                        <div class="row">
                            <div class="col-sm-4">
                              <div class="form-group input-group-sm">
                                <select name="refe[0][TpoDocRef]" class="form-control">
                                  <option value="" selected>Seleccione Tipo Documento de Referencia</option>
                                  <option value="SET">SET</option>
                                  <option value="30">Factura</option>
                                  <option value="32">Factura no Afecta</option>
                                  <option value="38">Boleta exenta</option>
                                  <option value="40">Liq. Factura</option>
                                  <option value="45">Factura Compra</option>
                                  <option value="50">Guia despacho</option>
                                  <option value="55">Nota Débito</option>
                                  <option value="60">Nota Crédito</option>
                                  <option value="103">Liq. com. dis.</option>
                                  <option value="33">Factura elec.</option>
                                  <option value="34">Factura no Afecta elec.</option>
                                  <option value="39">Boleta elec.</option>
                                  <option value="46">Factura Compra elec.</option>
                                  <option value="56">Nota Débito elec.</option>
                                  <option value="61">Nota Crédito elec.</option>
                                  <option value="52">Guia despacho elec.</option>
                                  <option value="801">Orden de Compra</option>
                                  <option value="802">Nota de pedido</option>
                                  <option value="803">Contrato</option>
                                  <option value="804">Resolución</option>
                                  <option value="805">Proceso ChileCompra</option>
                                  <option value="804">Ficha ChileCompra</option>
                                  <option value="HES">Hoja Entrada Servicio</option>
                                </select>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="form-group input-group-sm">
                              <input type="text" class="form-control" name="refe[0][FolioRef]" placeholder="Folio de Referencia" />
                            </div>
                          </div>
                          <div class="col-sm-4 text-center">
                            <div class="input-group input-group-sm">
                              <label class="input-group-addon">Fecha Referencia</label>
                                <input type="date" class="form-control" name="refe[0][FchRef]" maxLength='10' value="">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group input-group-sm">
                              <input type="text" name="refe[0][RazonRef]" class="form-control" placeholder="Descripción de referencia" />
                            </div>
                          </div>                
                        </div>                        
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group input-group-sm text-center">
                          <button type="button" class="btn ink-reaction btn-primary" id="agregar_refe">Agregar Linea de Referencia</button>
                          <button type="button" class="btn ink-reaction btn-warning hidden" id="quitar_refe">Remover Linea de Referencia</button>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="panel panel-default hidden" id="panel_pagos">
                  <div class="panel-heading">
                    <h5 class="panel-title">
                      PAGOS
                    </h5>
                  </div>
                    <div class="panel-body">
                      <div id="pago">
                        <div class="row">
                          <div class="col-sm-3 text-center">
                            <div class="input-group input-group-sm">
                              <label class="input-group-addon">Fecha de Pago</label>
                                <input type="date" class="form-control" name="pago[0][FchPago]" maxLength='10' value="<?php echo date('Y-m-d') ?>">
                            </div>
                          </div>
                            <div class="col-sm-3">
                              <div class="form-group input-group-sm">
                                <input type="text" class="form-control" name="pago[0][MntPago]" placeholder="Monto de Pago" />
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group input-group-sm">
                              <input type="text" class="form-control" name="pago[0][GlosaPagos]" placeholder="Glosa de Pago" />
                            </div>
                          </div>
                        </div>                        
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group input-group-sm text-center">
                          <button type="button" class="btn ink-reaction btn-primary" id="agregar_pago">Agregar Linea de Pago</button>
                          <button type="button" class="btn ink-reaction btn-warning hidden" id="quitar_pago">Remover Linea de Pago</button>
                        </div>
                      </div>
                    </div>
                </div>         

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h5 class="panel-title">
                        FIRMA ELECTRONICA
                    </h5>
                  </div>
                    <div class="panel-body">
                      <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                            <select name="rutaFirma" class="form-control">
                              <option  value="" selected="selected">Seleccione firma a usar</option>
                              <?php
                              $datosFirma = $Firma->listaFirma();
                              foreach ($datosFirma as $key) {
                              ?>                               
                                <option value="<?php echo $key["ruta"]; ?>"><?php echo $key["nombre"]; ?></option>
                              <?php
                              }
                              ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group input-group-sm">
                        <input type="password" class="form-control" name="passFirma" placeholder="Contraseña de la firma">
                      </div>
                    </div>
                </div>                

              </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                      <button type="submit" class="btn btn-block ink-reaction btn-primary">Generar Documento</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
</div>

<!-- BEGIN FORM MODAL MARKUP -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="formModalLabel">Buscar por RUT o Nombre del cliente</h4>
      </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table id="dttClientes" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>RUT</th>
                  <th>Razon Social</th>
                  <th>Giro</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listaClientes as $cliente): ?>
                  <tr>
                    <td><?php echo $cliente['rut'] ?></td>
                    <td><?php echo $cliente['rznsoc'] ?></td>
                    <td><?php echo $cliente['giro'] ?></td>
                    <td><a class="btn ink-reaction btn-primary btn-xs selectCliente" id="<?php echo $cliente['rut'] ?>">Usar</a></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>
<!-- END FORM MODAL MARKUP -->

<!-- BEGIN FORM MODAL MARKUP -->
<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="formModalLabel">Buscar por Producto</h4>
      </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table id="dttProductos" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Codigo</th>
                  <th>Nombre</th>
                  <th>Precio</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listaProductos as $producto): ?>
                  <tr>
                    <td><?php echo $producto['codigo'] ?></td>
                    <td><?php echo $producto['nombre'] ?></td>
                    <td><?php echo $producto['precio'] ?></td>
                    <td><a class="btn ink-reaction btn-primary btn-xs selectProducto" id="<?php echo $producto['codigo'] ?>">Usar</a></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>
<!-- END FORM MODAL MARKUP -->

  <script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/DataTables/dataTables.min.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/typeahead.jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    function calcularSubtotal(indexItem) {
        let cantidad = parseFloat($(`[name="item[${indexItem}][QtyItem]"]`).val()) || 0;
        let precio = parseFloat($(`[name="item[${indexItem}][PrcItem]"]`).val()) || 0;
        let descuento = parseFloat($(`[name="item[${indexItem}][DescuentoPct]"]`).val()) || 0;
        
        let subtotalSinDescuento = cantidad * precio;
        let montoDescuento = subtotalSinDescuento * (descuento / 100);
        let subtotalFinal = subtotalSinDescuento - montoDescuento;
        
        $(`[name="item[${indexItem}][SubTotal]"]`).val(Math.round(subtotalFinal));
    }

    // Eventos para el primer ítem
    $(document).ready(function(){
        $('[name="item[0][QtyItem]"]').change(function(){
            calcularSubtotal(0);
        });

        $('[name="item[0][PrcItem]"]').change(function(){
            calcularSubtotal(0);
        });

        $('[name="item[0][DescuentoPct]"]').change(function(){
            calcularSubtotal(0);
        });
    });
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
    // Para la tabla de clientes
    if ($.fn.DataTable.isDataTable('#dttClientes')) {
        $('#dttClientes').DataTable().destroy();
    }
    if ($('#dttClientes').length > 0) {
        $('#dttClientes').DataTable({
            'language': {
                'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
            },
            "responsive": true,
            "ordering": false,
            "serverSide": false,
            "order": []
        });
    }

    // Para la tabla de productos
    if ($.fn.DataTable.isDataTable('#dttProductos')) {
        $('#dttProductos').DataTable().destroy();
    }
    if ($('#dttProductos').length > 0) {
        $('#dttProductos').DataTable({
            'language': {
                'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
            },
            "responsive": true,
            "ordering": false,
            "serverSide": false,
            "order": []
        });
    }
});


    $(document).on('keyup', '#cliente', function(){
      var id = $(this).val();
      $.ajax({
        url:"data.php?cliente="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('#tablaClientes').removeAttr('hidden');
          $.each(data, function(i) {
            $('#tablaClientes').append(
                '<tr> ' +
                '  <td>'+data[i].rut+'</td> ' +
                '  <td>'+data[i].rznsoc+'</td> ' +
                '  <td>'+data[i].giro+'</td> ' +
                '  <td><a class="btn ink-reaction btn-primary btn-xs selectCliente" id="'+data[i].rut+'">Usar</a></td> ' +
                '</tr> '
              );
          });
        }
      })
    });

    $(document).on('click', '.selectCliente', function(){
      var id = $(this).attr("id"); //No existe id
      $.ajax({
        url:"data.php?datosCliente="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('[name="RUTRecep"]').val(data.rut);
          $('[name="RznSocRecep"]').val(data.rznsoc);
          $('[name="DirRecep"]').val(data.direccion);
          $('[name="CmnaRecep"]').val(data.comuna);
          $('[name="CiudadRecep"]').val(data.ciudad);
          $('[name="GiroRecep"]').val(data.giro);
          $('[name="Contacto"]').val(data.contacto);
          $('#formModal').modal('hide');
        }
        })
    });


    

    // Agregado, ya que, no existia un selectProducto
    $(document).on('click', '.selectProducto', function(){
      var id = $(this).attr("id");
      $.ajax({
        url:"data.php?datosProducto="+id,
        method:"GET", 
        dataType:"json",
        success:function(data)
      {
        $('[name="item[0][VlrCodigo]"]').val(data.codigo);
        $('[name="item[0][NmbItem]"]').val(data.nombre);
        $('[name="item[0][DscItem]"]').val(data.descripcion);
        $('[name="item[0][PrcItem]"]').val(data.precio);
        $('[name="item[0][UnmdItem]"]').val(data.unimed);
        $('#searchModal').modal('hide');
      }
      })
    });
    // Agregado los checkbox para mostrar los paneles referencia y pago
    $(document).ready(function(){
    $('#checkbox_referencias').change(function(){
        if(this.checked) {
            $('#panel_referencias').removeClass('hidden');
        } else {
            $('#panel_referencias').addClass('hidden');
        }
    });

    $('#checkbox_pagos').change(function(){
        if(this.checked) {
            $('#panel_pagos').removeClass('hidden');
        } else {
            $('#panel_pagos').addClass('hidden');
        }
    });
  });


  </script>
   <script type="text/javascript">
      $(document).ready(function(){
        $('#RUTRecep').typeahead({
          source: function(query, result){
            $.ajax({
              url:"data.php",
              method:"POST",
              data:{RUTRecep:query},
              dataType:"json",
              success:function(data){ result($.map(data, function(item){ return item; }))}
            })
          }
        });

        $("#RUTRecep").change(function(){
          $.ajax({
            url:'data.php',
            type:'POST',
            dataType:'json',
            data:{ datoRut:$('#RUTRecep').val()}
          }).done(function(respuesta){
            $('[name="RznSocRecep"]').val(respuesta.rznsoc);
            $('[name="DirRecep"]').val(respuesta.direccion);
            $('[name="CmnaRecep"]').val(respuesta.comuna);
            $('[name="CiudadRecep"]').val(respuesta.ciudad);
            $('[name="GiroRecep"]').val(respuesta.giro);
            $('[name="Contacto"]').val(respuesta.contacto);
          });
        });

        $('#codProducto0').typeahead({
          source: function(query, result){
            $.ajax({
              url:'data.php',
              method:'POST',
              dataType:'json',
              data:{codProducto:query},
              success:function(data){ result($.map(data, function(item){ return item; }))}
            })
          }
        });

        $("#codProducto0").change(function(){
          $.ajax({
            url:'data.php',
            type:'POST',
            dataType:'json',
            data:{ datoProducto:$('#codProducto0').val()},
          }).done(function(respuesta){
            $('[name="item[0][NmbItem]"]').val(respuesta.nombre);
            $('[name="item[0][PrcItem]"]').val(respuesta.precio);
            $('[name="item[0][DscItem]"]').val(respuesta.descripcion);
            $('[name="item[0][UnmdItem]"]').val(respuesta.unimed);
          });
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
                        '  <div class="row">' +
                        '      <div class="col-sm-2">' +
                        '        <div class="form-group input-group-sm">' +
                        '          <input type="text" class="form-control" name="item['+ count_item +'][VlrCodigo]" id="codProducto'+ count_item +'" placeholder="Codigo" />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-10">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][NmbItem]" placeholder="Nombre Producto" />' +
                        '      </div>' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="row">' +
                        '    <div class="col-sm-12">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <textarea name="item['+ count_item +'][DscItem]" class="form-control" placeholder="Descripción" /></textarea>' +
                        '      </div>' +
                        '    </div>                ' +
                        '  </div>' +
                        '  <div class="row">' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" max="999999999999" maxlength="12" class="form-control" name="item['+ count_item +'][QtyItem]" placeholder="Cantidad" />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][PrcItem]" placeholder="Precio" />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-3">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <select name="item['+ count_item +'][CodImpAdic]" class="form-control">' +
                        '          <option value="" selected="">Impuesto Adicional</option>' +
                        '          <option value="23">Art. de oro, Joyas y Pieles finas 15%</option>' +
                        '          <option value="44">Tapices, Casas rod., Caviar y Arm.de aire 15%</option>' +
                        '          <option value="24">Licores, Pisco, Destilados 31,5%</option>' +
                        '          <option value="25">Vinos, Chichas, Sidras 20,5%</option>' +
                        '          <option value="26">Cervezas y Otras bebidas alcohólicas 20,5%</option>' +
                        '          <option value="27">Aguas minerales y Beb. analcohól. 10%</option>' +
                        '          <option value="271">Beb. analcohól. elevado cont azucar 18%</option>' +
                        '        </select>' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-1">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][UnmdItem]" placeholder="Uni. Med." />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][DescuentoPct]" placeholder="% Desc." />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][SubTotal]" placeholder="SubTotal" />' +
                        '      </div>' +
                        '    </div>' +
                        '  </div>' +
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

  <script type="text/javascript">
  $(document).ready(function(){
    var count_refe=0;
    $('#agregar_refe').click(function(){
    $('#quitar_refe').removeClass('hidden');
    ++count_refe;
    $('#refe').append(  '<div id="nueva_refe' +count_refe+ '"><hr>' +
                        '<div class="row">' +
                        '    <div class="col-sm-4">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <select name="refe['+count_refe+'][TpoDocRef]" class="form-control">' +
                        '          <option value="No selecciono" selected>Seleccione Tipo Documento de Referencia</option>' +
                        '          <option value="30">Factura</option>' +
                        '          <option value="32">Factura no Afecta</option>' +
                        '          <option value="38">Boleta exenta</option>' +
                        '          <option value="40">Liq. Factura</option>' +
                        '          <option value="45">Factura Compra</option>' +
                        '          <option value="50">Guia despacho</option>' +
                        '          <option value="55">Nota Débito</option>' +
                        '          <option value="60">Nota Crédito</option>' +
                        '          <option value="103">Liq. com. dis.</option>' +
                        '          <option value="33">Factura elec.</option>' +
                        '          <option value="34">Factura no Afecta elec.</option>' +
                        '          <option value="39">Boleta elec.</option>' +
                        '          <option value="46">Factura Compra elec.</option>' +
                        '          <option value="56">Nota Débito elec.</option>' +
                        '          <option value="61">Nota Crédito elec.</option>' +
                        '          <option value="52">Guia despacho elec.</option>' +
                        '          <option value="801">Orden de Compra</option>' +
                        '          <option value="802">Nota de pedido</option>' +
                        '          <option value="803">Contrato</option>' +
                        '          <option value="804">Resolución</option>' +
                        '          <option value="805">Proceso ChileCompra</option>' +
                        '          <option value="804">Ficha ChileCompra</option>' +
                        '          <option value="HES">Hoja Entrada Servicio</option>' +
                        '        </select>' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="col-sm-4">' +
                        '    <div class="form-group input-group-sm">' +
                        '      <input type="text" class="form-control" name="refe['+count_refe+'][FolioRef]" placeholder="Folio de Referencia" />' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="col-sm-4 text-center">' +
                        '    <div class="input-group input-group-sm">' +
                        '      <label class="input-group-addon">Fecha Referencia</label>' +
                        '     <input type="date" class="form-control" name="refe['+count_refe+'][FchRef]" maxLength="10" value="<?php echo date('Y-m-d')?>">' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '<div class="row">' +
                        '  <div class="col-sm-12">' +
                        '    <div class="form-group input-group-sm">' +
                        '      <input type="text" name="refe['+count_refe+'][RazonRef]" class="form-control" placeholder="Descripción de referencia" />' +
                        '    </div>' +
                        '  </div>' +
                        '</div>  ' +
                        '</div> ');
  });

  $('#quitar_refe').click(function() {
    $('#nueva_refe'+count_refe).remove();

    count_refe--;
    
    if (count_refe==0) {
       $('#quitar_refe').addClass('hidden');
    }
  });
  });
  </script>

  <script type="text/javascript">
  $(document).ready(function(){
    var count_pago=0;
    $('#agregar_pago').click(function(){
    $('#quitar_pago').removeClass('hidden');
    ++count_pago;
    $('#pago').append(  '<div id="nueva_pago' +count_pago+ '"><hr>' +
                        '<div class="row">' +
                        '  <div class="col-sm-3 text-center">' +
                        '    <div class="input-group input-group-sm">' +
                        '      <label class="input-group-addon">Fecha de Pago</label>' +
                        '   <input type="date" class="form-control" name="pago['+count_pago+'][FchPago]" maxLength="10" value="<?php echo date('Y-m-d') ?>">' +
                        '    </div>' +
                        '  </div>' +
                        '    <div class="col-sm-3">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="pago['+count_pago+'][MntPago]" placeholder="Monto de Pago" />' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="col-sm-6">' +
                        '    <div class="form-group input-group-sm">' +
                        '      <input type="text" class="form-control" name="pago['+count_pago+'][GlosaPagos]" placeholder="Glosa de Pago" />' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '</div> ');
  });

  $('#quitar_pago').click(function() {
    $('#nueva_pago'+count_pago).remove();

    count_pago--;
    
    if (count_pago==0) {
       $('#quitar_pago').addClass('hidden');
    }
  });
  });
 
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
      // Para la tabla de clientes
      if ($.fn.DataTable.isDataTable('#dttClientes')) {
          $('#dttClientes').DataTable().destroy();
      }
      if ($('#dttClientes').length > 0) {
          $('#dttClientes').DataTable({
              'language': {
                  'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
              },
              "responsive": true,
              "ordering": false,
              "serverSide": false,
              "order": []
          });
      }

      // Para la tabla de productos
      if ($.fn.DataTable.isDataTable('#dttProductos')) {
          $('#dttProductos').DataTable().destroy();
      }
      if ($('#dttProductos').length > 0) {
          $('#dttProductos').DataTable({
              'language': {
                  'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
              },
              "responsive": true,
              "ordering": false,
              "serverSide": false,
              "order": []
          });
      }
  });

    $(document).on('keyup', '#cliente', function(){
      var id = $(this).val();
      $.ajax({
        url:"data.php?cliente="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('#tablaClientes').removeAttr('hidden');
          $.each(data, function(i) {
            $('#tablaClientes').append(
                '<tr> ' +
                '  <td>'+data[i].rut+'</td> ' +
                '  <td>'+data[i].rznsoc+'</td> ' +
                '  <td>'+data[i].giro+'</td> ' +
                '  <td><a class="btn ink-reaction btn-primary btn-xs selectCliente" id="'+data[i].rut+'">Usar</a></td> ' +
                '</tr> '
              );
          });
        }
      })
    });

    $(document).on('click', '.selectCliente', function(){
      var id = $(this).attr("id"); //No existe id
      $.ajax({
        url:"data.php?datosCliente="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('[name="RUTRecep"]').val(data.rut);
          $('[name="RznSocRecep"]').val(data.rznsoc);
          $('[name="DirRecep"]').val(data.direccion);
          $('[name="CmnaRecep"]').val(data.comuna);
          $('[name="CiudadRecep"]').val(data.ciudad);
          $('[name="GiroRecep"]').val(data.giro);
          $('[name="Contacto"]').val(data.contacto);
          $('#formModal').modal('hide');
        }
        })
    });


    

    // Agregado, ya que, no existia un selectProducto
    $(document).on('click', '.selectProducto', function(){
      var id = $(this).attr("id");
      $.ajax({
        url:"data.php?datosProducto="+id,
        method:"GET", 
        dataType:"json",
        success:function(data)
      {
        $('[name="item[0][VlrCodigo]"]').val(data.codigo);
        $('[name="item[0][NmbItem]"]').val(data.nombre);
        $('[name="item[0][DscItem]"]').val(data.descripcion);
        $('[name="item[0][PrcItem]"]').val(data.precio);
        $('[name="item[0][UnmdItem]"]').val(data.unimed);
        $('#searchModal').modal('hide');
      }
      })
    });
    // Agregado los checkbox para mostrar los paneles referencia y pago
    $(document).ready(function(){
    $('#checkbox_referencias').change(function(){
        if(this.checked) {
            $('#panel_referencias').removeClass('hidden');
        } else {
            $('#panel_referencias').addClass('hidden');
        }
    });

    $('#checkbox_pagos').change(function(){
        if(this.checked) {
            $('#panel_pagos').removeClass('hidden');
        } else {
            $('#panel_pagos').addClass('hidden');
        }
    });
  });


  </script>
   <script type="text/javascript">
      $(document).ready(function(){
        $('#RUTRecep').typeahead({
          source: function(query, result){
            $.ajax({
              url:"data.php",
              method:"POST",
              data:{RUTRecep:query},
              dataType:"json",
              success:function(data){ result($.map(data, function(item){ return item; }))}
            })
          }
        });

        $("#RUTRecep").change(function(){
          $.ajax({
            url:'data.php',
            type:'POST',
            dataType:'json',
            data:{ datoRut:$('#RUTRecep').val()}
          }).done(function(respuesta){
            $('[name="RznSocRecep"]').val(respuesta.rznsoc);
            $('[name="DirRecep"]').val(respuesta.direccion);
            $('[name="CmnaRecep"]').val(respuesta.comuna);
            $('[name="CiudadRecep"]').val(respuesta.ciudad);
            $('[name="GiroRecep"]').val(respuesta.giro);
            $('[name="Contacto"]').val(respuesta.contacto);
          });
        });

        $('#codProducto0').typeahead({
          source: function(query, result){
            $.ajax({
              url:'data.php',
              method:'POST',
              dataType:'json',
              data:{codProducto:query},
              success:function(data){ result($.map(data, function(item){ return item; }))}
            })
          }
        });

        $("#codProducto0").change(function(){
          $.ajax({
            url:'data.php',
            type:'POST',
            dataType:'json',
            data:{ datoProducto:$('#codProducto0').val()},
          }).done(function(respuesta){
            $('[name="item[0][NmbItem]"]').val(respuesta.nombre);
            $('[name="item[0][PrcItem]"]').val(respuesta.precio);
            $('[name="item[0][DscItem]"]').val(respuesta.descripcion);
            $('[name="item[0][UnmdItem]"]').val(respuesta.unimed);
          });
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
                        '  <div class="row">' +
                        '      <div class="col-sm-2">' +
                        '        <div class="form-group input-group-sm">' +
                        '          <input type="text" class="form-control" name="item['+ count_item +'][VlrCodigo]" id="codProducto'+ count_item +'" placeholder="Codigo" />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-10">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][NmbItem]" placeholder="Nombre Producto" />' +
                        '      </div>' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="row">' +
                        '    <div class="col-sm-12">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <textarea name="item['+ count_item +'][DscItem]" class="form-control" placeholder="Descripción" /></textarea>' +
                        '      </div>' +
                        '    </div>                ' +
                        '  </div>' +
                        '  <div class="row">' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" max="999999999999" maxlength="12" class="form-control" name="item['+ count_item +'][QtyItem]" placeholder="Cantidad" />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][PrcItem]" placeholder="Precio" />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-3">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <select name="item['+ count_item +'][CodImpAdic]" class="form-control">' +
                        '          <option value="" selected="">Impuesto Adicional</option>' +
                        '          <option value="23">Art. de oro, Joyas y Pieles finas 15%</option>' +
                        '          <option value="44">Tapices, Casas rod., Caviar y Arm.de aire 15%</option>' +
                        '          <option value="24">Licores, Pisco, Destilados 31,5%</option>' +
                        '          <option value="25">Vinos, Chichas, Sidras 20,5%</option>' +
                        '          <option value="26">Cervezas y Otras bebidas alcohólicas 20,5%</option>' +
                        '          <option value="27">Aguas minerales y Beb. analcohól. 10%</option>' +
                        '          <option value="271">Beb. analcohól. elevado cont azucar 18%</option>' +
                        '        </select>' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-1">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][UnmdItem]" placeholder="Uni. Med." />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][DescuentoPct]" placeholder="% Desc." />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][SubTotal]" placeholder="SubTotal" />' +
                        '      </div>' +
                        '    </div>' +
                        '  </div>' +
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

  <script type="text/javascript">
  $(document).ready(function(){
    var count_refe=0;
    $('#agregar_refe').click(function(){
    $('#quitar_refe').removeClass('hidden');
    ++count_refe;
    $('#refe').append(  '<div id="nueva_refe' +count_refe+ '"><hr>' +
                        '<div class="row">' +
                        '    <div class="col-sm-4">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <select name="refe['+count_refe+'][TpoDocRef]" class="form-control">' +
                        '          <option value="No selecciono" selected>Seleccione Tipo Documento de Referencia</option>' +
                        '          <option value="30">Factura</option>' +
                        '          <option value="32">Factura no Afecta</option>' +
                        '          <option value="38">Boleta exenta</option>' +
                        '          <option value="40">Liq. Factura</option>' +
                        '          <option value="45">Factura Compra</option>' +
                        '          <option value="50">Guia despacho</option>' +
                        '          <option value="55">Nota Débito</option>' +
                        '          <option value="60">Nota Crédito</option>' +
                        '          <option value="103">Liq. com. dis.</option>' +
                        '          <option value="33">Factura elec.</option>' +
                        '          <option value="34">Factura no Afecta elec.</option>' +
                        '          <option value="39">Boleta elec.</option>' +
                        '          <option value="46">Factura Compra elec.</option>' +
                        '          <option value="56">Nota Débito elec.</option>' +
                        '          <option value="61">Nota Crédito elec.</option>' +
                        '          <option value="52">Guia despacho elec.</option>' +
                        '          <option value="801">Orden de Compra</option>' +
                        '          <option value="802">Nota de pedido</option>' +
                        '          <option value="803">Contrato</option>' +
                        '          <option value="804">Resolución</option>' +
                        '          <option value="805">Proceso ChileCompra</option>' +
                        '          <option value="804">Ficha ChileCompra</option>' +
                        '          <option value="HES">Hoja Entrada Servicio</option>' +
                        '        </select>' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="col-sm-4">' +
                        '    <div class="form-group input-group-sm">' +
                        '      <input type="text" class="form-control" name="refe['+count_refe+'][FolioRef]" placeholder="Folio de Referencia" />' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="col-sm-4 text-center">' +
                        '    <div class="input-group input-group-sm">' +
                        '      <label class="input-group-addon">Fecha Referencia</label>' +
                        '     <input type="date" class="form-control" name="refe['+count_refe+'][FchRef]" maxLength="10" value="<?php echo date('Y-m-d')?>">' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '<div class="row">' +
                        '  <div class="col-sm-12">' +
                        '    <div class="form-group input-group-sm">' +
                        '      <input type="text" name="refe['+count_refe+'][RazonRef]" class="form-control" placeholder="Descripción de referencia" />' +
                        '    </div>' +
                        '  </div>' +
                        '</div>  ' +
                        '</div> ');
  });

  $('#quitar_refe').click(function() {
    $('#nueva_refe'+count_refe).remove();

    count_refe--;
    
    if (count_refe==0) {
       $('#quitar_refe').addClass('hidden');
    }
  });
  });
  </script>

  <script type="text/javascript">
  $(document).ready(function(){
    var count_pago=0;
    $('#agregar_pago').click(function(){
    $('#quitar_pago').removeClass('hidden');
    ++count_pago;
    $('#pago').append(  '<div id="nueva_pago' +count_pago+ '"><hr>' +
                        '<div class="row">' +
                        '  <div class="col-sm-3 text-center">' +
                        '    <div class="input-group input-group-sm">' +
                        '      <label class="input-group-addon">Fecha de Pago</label>' +
                        '   <input type="date" class="form-control" name="pago['+count_pago+'][FchPago]" maxLength="10" value="<?php echo date('Y-m-d') ?>">' +
                        '    </div>' +
                        '  </div>' +
                        '    <div class="col-sm-3">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="pago['+count_pago+'][MntPago]" placeholder="Monto de Pago" />' +
                        '    </div>' +
                        '  </div>' +
                        '  <div class="col-sm-6">' +
                        '    <div class="form-group input-group-sm">' +
                        '      <input type="text" class="form-control" name="pago['+count_pago+'][GlosaPagos]" placeholder="Glosa de Pago" />' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '</div> ');
  });

  $('#quitar_pago').click(function() {
    $('#nueva_pago'+count_pago).remove();

    count_pago--;
    
    if (count_pago==0) {
       $('#quitar_pago').addClass('hidden');
    }
  });
  });
  </script>
</body>
</html>