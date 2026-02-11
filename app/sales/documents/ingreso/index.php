<?php 
require_once '../../../../config.php';
$_SESSION['navMenu']  = 'sales';
$Empresa = new Empresa();
$Cliente = new Cliente();
$Producto = new Producto();
$datos = $Empresa->listaEmpresa();
$listaClientes = $Cliente->listaCliente();
$listaProductos = $Producto->listaProducto();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | Ingreso Documentos</title>
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
                <li><a href="<?php echo BASEURL ?>app/sales/">Ventas</a></li>
                <li><a href="<?php echo BASEURL ?>app/sales/documents/">Documentos</a></li>
                <li class="active">Ingresar Venta</li>
              </ol>
            </div>
            <div class="section-body">
              <div class="col-lg-offset-1 col-md-11 col-sm-12">
                <div class="card">
                  <div class="card-body">

                <form class="form-horizontal sendForm" autocomplete="off">


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
                          <input type="text" class="form-control" name="RUTRecep" placeholder="RUT" id="RUTRecep" required/>
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
                          <input type="text" class="form-control" name="DirRecep" placeholder="Direccion" required/>
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
                  </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h5 class="panel-title">
                      INFORMACION
                    </h5>
                  </div>
                  <div class="panel-body">
                      <div class="row">
                        <div class="col-sm-3 text-center">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">Fecha Emisi&oacute;n</label>
                              <input type="text" name="FchEmis" class="form-control datepicker" maxLength="10" value="<?php echo date('Y-m-d') ?>" required>
                          </div>
                        </div>
                        <div class="col-sm-3 text-center">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">Fecha Vencimiento</label>
                              <input type="text" name="FchVence" class="form-control datepicker" maxLength="10" value="<?php echo date('Y-m-d') ?>" required>
                          </div>
                        </div>

                        <div class="col-sm-6 text-center">
                          <div class="row">
                            <div class="col-sm-8">
                              <div class="input-group input-group-sm">
                                <label class="input-group-addon">Tipo Doc.</label>
                                  <select name="TipoDTE" class="form-control">
                                    <option value="33">FACTURA ELECTRONICA</option>
                                    <option value="34">FACTURA NO AFECTA O EXENTA ELECTRONICA</option>
                                    <option value="39">BOLETA ELECTRONICA</option>
                                    <option value="41">BOLETA EXENTA ELECTRONICA</option>
                                    <option value="43">LIQUIDACION FACTURA ELECTRONICA</option>
                                    <option value="46">FACTURA COMPRA ELECTRONICA</option>
                                    <option value="52">GUIA DESPACHO ELECTRONICA</option>
                                    <option value="56">NOTA DEBITO ELECTRONICA</option>
                                    <option value="61">NOTA CREDITO ELECTRONICA</option>
                                    <option value="66">BOLETAS DE HONORARIOS ELECTRONICAS</option>
                                    <option value="96">BOLETA DE TERCEROS ELECTRONICAS</option>
                                    <option value="110">FACTURA DE EXPORTACION ELECTRONICA</option>
                                    <option value="111">NOTA DE DEBITO EXPORTACION ELECTRONICA</option>
                                    <option value="112">NOTA DE CREDITO EXPORTACION ELECTRONICA</option>
                                  </select>
                              </div>                              
                            </div>
                            <div class="col-sm-4">
                              <div class="input-group input-group-sm">
                                <label class="input-group-addon">Folio</label>
                                  <input type="number" name="Folio" class="form-control" maxLength="10" value="" required>
                              </div>
                            </div>
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
                            <input type="number" max="99999" maxlength="12" class="form-control" name="item[0][QtyItem]" onkeyup="subtotal0(this)" placeholder="Cantidad" required/>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="item[0][PrcItem]" onkeyup="subtotal0(this)" placeholder="Precio" required/>
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
                            <input type="text" class="form-control" name="item[0][DescuentoPct]" onkeyup="descuento0(this)" placeholder="% Desc." />
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
                                <input type="text" class="form-control datepicker" name="refe[0][FchRef]" maxLength='10' value="">
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
                                <input type="text" class="form-control datepicker" name="pago[0][FchPago]" maxLength='10' value="<?php echo date('Y-m-d') ?>">
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
                      REFERENCIAS Y PAGOS
                    </h5>
                  </div>
                    <div class="panel-body">
                      <div class="col-sm-4">
                        <div class="input-group input-group-sm">
                          <label class="input-group-addon">Forma de Pago</label>
                          <select class="form-control" name="FmaPago">
                            <option value="Contado" selected>Contado</option>
                            <option value="Crédito">Crédito</option>
                            <option value="Sin Costo">Sin Costo</option>
                          </select>                          
                        </div>
                      </div>
                      <div class="col-sm-4 col-sm-offset-4">
                        <label class="checkbox-inline checkbox-styled">
                          <input type="checkbox" id="checkbox_referencias"><span>Panel de Referencias</span>
                        </label>
                        <label class="checkbox-inline checkbox-styled">
                          <input type="checkbox" id="checkbox_pagos"><span>Panel de Pagos</span>
                        </label>
                      </div>
                    </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h5 class="panel-title">
                      TOTALES
                    </h5>
                  </div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-sm-4">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">Exento</label>
                            <input type="text" class="form-control" name="exento" id="exento" placeholder="Exento">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">Otro Impuesto</label>
                            <input type="text" class="form-control" name="otro_impuesto" id="otro_impuesto" placeholder="Otro Impuesto">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">Descuento Global</label>
                            <input type="text" class="form-control" name="descuento" id="descuento" onkeyup="calcularDescuento(this)" placeholder="Descuento Global">
                          </div>
                        </div>                        
                      </div>

                      <div class="row">
                        <div class="col-sm-4">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">NETO</label>
                            <input type="text" class="form-control" name="neto" id="neto" placeholder="NETO">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">IVA</label>
                            <input type="text" class="form-control" name="iva" id="iva" placeholder="IVA">
                          </div>
                        </div>


                        <div class="col-sm-4">
                          <div class="input-group input-group-sm">
                            <label class="input-group-addon">Totales</label>
                            <input type="text" class="form-control" name="total" id="total">
                          </div>
                        </div>                        
                      </div>


                    </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-body">
                    <input type="hidden" name="tipo" value="Venta">
                    <button type="submit" class="btn btn-block ink-reaction btn-primary">Guardar</button>
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
        <h4 class="modal-title" id="formModalLabel">Buscar por RUT o Nombre</h4>
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
        <h4 class="modal-title" id="formModalLabel">Buscar por Codigo o Nombre del producto</h4>
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
  <script src="<?php echo BASEURL ?>asset/js/toastr.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/jquery.Rut.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/datepicker.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/DataTables/dataTables.min.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/typeahead.jquery.min.js" type="text/javascript"></script>
  <div id="script">
    
  </div>
  <script type="text/javascript">
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
            window.location.replace('../index.php');
          },
          error: function()
          {
            toastr.info('ERROR! no se han guardado los datos.', '');
          }
        });
      });

      $('#RUTRecep').Rut({ on_error: function(){ toastr.info('ERROR! R.U.T. incorrecto.', ''); $('#RUTRecep').val(''); $("#RUTRecep").focus();}, format_on: 'keyup'});
    });  

    function subtotal0(){
      var cantidad = parseInt(document.getElementsByName('item[0][QtyItem]')[0].value);
      var precio = parseInt(document.getElementsByName('item[0][PrcItem]')[0].value);
      var descuento = parseFloat(document.getElementsByName('item[0][DescuentoPct]')[0].value);
      if (precio != '' ||  precio != 0) {
        subtotal = cantidad * precio;
        if (isNaN(descuento)) { 
          $('[name="item[0][SubTotal]"]').val(subtotal);
          calculartotales(0);
        } else {
          subtotal = parseInt(subtotal - ((descuento/100)*subtotal));
          $('[name="item[0][SubTotal]"]').val(subtotal); 
          calculartotales(0);
        }
      }
      if (isNaN(cantidad) || isNaN(precio)){$('[name="item[0][SubTotal]"]').val('');}
    }

    function descuento0(){
      var cantidad = parseInt(document.getElementsByName('item[0][QtyItem]')[0].value);
      var precio = parseInt(document.getElementsByName('item[0][PrcItem]')[0].value);
      var descuento = parseFloat(document.getElementsByName('item[0][DescuentoPct]')[0].value);
      var subtotal = parseInt(document.getElementsByName('item[0][SubTotal]')[0].value);
      if (subtotal != '' || subtotal != 0 && descuento != 0 || descuento != '') {
        subtotal = cantidad * precio;
        subtotal = Math.round( subtotal - ((descuento/100)*subtotal));
        $('[name="item[0][SubTotal]"]').val(subtotal);
        calculartotales(0);
      } else {
        if (precio != 0 || precio != '' && cantidad != 0 || cantidad != '') {
          subtotal = cantidad * precio;
          $('[name="item[0][SubTotal]"]').val(subtotal); 
          calculartotales(0);
        }        
      }
      if (isNaN(descuento)) {
        $('[name="item[0][DescuentoPct]"]').val('');
        subtotal = cantidad * precio;
        $('[name="item[0][SubTotal]"]').val(subtotal);
        calculartotales(0);
      }
    }

    function calculartotales(counter, suma = true){
      var total = 0;
      for (var i = 0; i <= counter; i++) {
        subtotal = document.getElementsByName('item['+i+'][SubTotal]')[0].value;
        if (isNaN(subtotal)) {} else {total = total + parseInt(subtotal);}
      }

      if (document.getElementsByName('TipoDTE')[0].value == '34') {
        $('[name="neto"]').val(total);
        $('[name="exento"]').val(total);
        $('[name="total"]').val(total);
        $('[name="iva"]').val('');
      } else {
        calcularIVA(total);
        $('[name="exento"]').val('');
      }
    }

    function calcularIVA(total){
      iva = Math.round((19/100)*total);
      totales = parseInt(iva + total);
      $('[name="neto"]').val(total);
      $('[name="iva"]').val(iva);
      $('[name="total"]').val(totales);
    }

    function calcularDescuento(){
      var descuento = Math.round(document.getElementsByName('descuento')[0].value);
      var neto = Math.round(document.getElementsByName('neto')[0].value);
      if (isNaN(descuento)) { } else { var total = parseInt(neto - descuento); calcularIVA(total);}
    }
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
        $('#dttClientes').DataTable({
          'language': {
            'url': '<?php echo BASEURL ?>asset/js/DataTables/spanish.json'
          },
          "responsive": true,
          "ordering":  false,
          "serverSide": false, //Feature control DataTables' server-side processing mode.
          "order": [], //Initial no order.
        });
      });

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

    $('.datepicker').datepicker({autoclose: true, todayHighlight: true, format: "yyyy/mm/dd"});

    $(document).on('click', '.selectProducto', function(){
      var id = $(this).attr("id");
      $.ajax({
        url:"data.php?getProducto="+id,
        method:"GET",
        dataType:"json",
        success:function(data)
        {
          $('[name="item[0][VlrCodigo]"]').val(id);
          $('[name="item[0][NmbItem]"]').val(data.nombre);
          $('[name="item[0][PrcItem]"]').val(data.precio);
          $('[name="item[0][DscItem]"]').val(data.descripcion);
          $('[name="item[0][UnmdItem]"]').val(data.unimed);
          $('#searchModal').modal('hide');
        }
        })
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
      var id = $(this).attr("id");
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

    $(document).on('click', '.selectProveedor', function(){
      var id = $(this).attr("id");
      $.ajax({
        url:"data.php?datosProveedor="+id,
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
  </script>

  <script type="text/javascript">
      $(document).ready(function(){
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
                        '        <input type="text" max="999999999999" maxlength="12" class="form-control" name="item['+ count_item +'][QtyItem]" onkeyup="subtotal'+ count_item +'(this)" placeholder="Cantidad" />' +
                        '      </div>' +
                        '    </div>' +
                        '    <div class="col-sm-2">' +
                        '      <div class="form-group input-group-sm">' +
                        '        <input type="text" class="form-control" name="item['+ count_item +'][PrcItem]" onkeyup="subtotal'+ count_item +'(this)" placeholder="Precio" />' +
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
                          '\n function subtotal'+ count_item +'(){' +
                          '\n   var cantidad = parseInt(document.getElementsByName(\'item['+ count_item +'][QtyItem]\')[0].value);' +
                          '\n   var precio = parseInt(document.getElementsByName(\'item['+ count_item +'][PrcItem]\')[0].value);' +
                          '\n   var descuento = parseFloat(document.getElementsByName(\'item['+ count_item +'][DescuentoPct]\')[0].value);' +
                          '\n   if (precio != "" ||  precio != 0) {' +
                          '\n     subtotal = cantidad * precio;' +
                          '\n     if (isNaN(descuento)) { ' +
                          '\n       $(\'[name="item['+ count_item +'][SubTotal]"]\').val(subtotal);' +
                          '\n       calculartotales('+ count_item +');' +
                          '\n     } else {' +
                          '\n       subtotal = parseInt(subtotal - ((descuento/100)*subtotal));' +
                          '\n       $(\'[name="item['+ count_item +'][SubTotal]"]\').val(subtotal); ' +
                          '\n       calculartotales('+ count_item +');' +
                          '\n     }' +
                          '\n   }' +
                          '\n   if (isNaN(cantidad) || isNaN(precio)){$(\'[name="item['+ count_item +'][SubTotal]"]\').val("");}' +
                          '\n }' +
                          '\n function descuento'+ count_item +'(){' +
                          '\n   var cantidad = parseInt(document.getElementsByName(\'item['+ count_item +'][QtyItem]\')[0].value);' +
                          '\n   var precio = parseInt(document.getElementsByName(\'item['+ count_item +'][PrcItem]\')[0].value);' +
                          '\n   var descuento = parseFloat(document.getElementsByName(\'item['+ count_item +'][DescuentoPct]\')[0].value);' +
                          '\n   var subtotal = parseInt(document.getElementsByName(\'item['+ count_item +'][SubTotal]\')[0].value);' +
                          '\n   if (subtotal != "" || subtotal != 0 && descuento != 0 || descuento != "") {' +
                          '\n     subtotal = cantidad * precio;' +
                          '\n     subtotal = parseInt( subtotal - ((descuento/100)*subtotal));' +
                          '\n     $(\'[name="item['+ count_item +'][SubTotal]"]\').val(subtotal);' +
                          '\n     calculartotales('+ count_item +');' +
                          '\n   } else {' +
                          '\n     if (precio != 0 || precio != "" && cantidad != 0 || cantidad != "") {' +
                          '\n       subtotal = cantidad * precio;' +
                          '\n       $(\'[name="item['+ count_item +'][SubTotal]"]\').val(subtotal); ' +
                          '\n       calculartotales('+ count_item +');' +
                          '\n     }' +
                          '\n   }' +
                          '\n   if (isNaN(descuento)) {' +
                          '\n     $(\'[name="item['+ count_item +'][DescuentoPct]"]\').val("");' +
                          '\n     subtotal = cantidad * precio;' +
                          '\n     $(\'[name="item['+ count_item +'][SubTotal]"]\').val(subtotal);' +
                          '\n     calculartotales('+ count_item +');' +
                          '\n   }' +
                          '\n }' +
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

    $("#checkbox_referencias").change(function() {
      if($(this).is(":checked")) {
        $('#panel_referencias').removeClass('hidden');
      }
      else {
        $('#panel_referencias').addClass('hidden');
      }
    });
    
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
                        '     <input type="text" class="form-control datepicker" name="refe['+count_refe+'][FchRef]" maxLength="10" value="<?php echo date('Y-m-d')?>">' +
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

    $("#checkbox_pagos").change(function() {
      if($(this).is(":checked")) {
        $('#panel_pagos').removeClass('hidden');
      }
      else {
        $('#panel_pagos').addClass('hidden');
      }
    });

    var count_pago=0;
    $('#agregar_pago').click(function(){
    $('#quitar_pago').removeClass('hidden');
    ++count_pago;
    $('#pago').append(  '<div id="nueva_pago' +count_pago+ '"><hr>' +
                        '<div class="row">' +
                        '  <div class="col-sm-3 text-center">' +
                        '    <div class="input-group input-group-sm">' +
                        '      <label class="input-group-addon">Fecha de Pago</label>' +
                        '   <input type="text" class="form-control datepicker" name="pago['+count_pago+'][FchPago]" maxLength="10" value="<?php echo date('Y-m-d') ?>">' +
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