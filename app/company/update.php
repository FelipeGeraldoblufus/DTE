<?php 
require_once '../../config.php';
$_SESSION['navMenu']  = 'company';
$Empresa = new Empresa();
$datos = $Empresa->listaEmpresa();
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
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/datepicker.css">
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
                      <form action="upd.php" method="post" enctype="multipart/form-data">
                      <div class="col-md-5">
                      <div class="col-sm-12">
                        <div class="form-group input-group-sm">
                          <img src="<?php echo BASEURL.$datos['logo']?>"><br>
                          <input type="hidden"  class="form-control" name="logo_actual" value="<?php echo $datos['logo']?>">
                          <input type="file"  class="form-control" name="logo" accept="image/x-png">
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group input-group-sm">
                          <span class="opacity-50">Razon Social</span><br/>
                          <input type="text" class="form-control" name="RznSoc" placeholder="Razon Social" value="<?php echo $datos['rznsoc'] ?>" />
                          <span class="opacity-50">R.U.T.</span><br/>
                          <input type="text" class="form-control" name="rut" placeholder="R.U.T." value="<?php echo $datos['rut'] ?>" />
                          <span class="opacity-50">Giro</span><br/>
                          <input type="text" class="form-control" name="GiroEmis" placeholder="Giro" value="<?php echo $datos['giro'] ?>" />
                          <span class="opacity-50">Actividad economica</span><br/>
                          <select name="acteco" class="form-control">
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
                        <div class="col-sm-6">
                          <div class="form-group input-group-sm">
                            <span class="opacity-50">Fecha de Resolucion</span>
                            <input type="text" class="form-control date" name="fchresol" placeholder="Fecha de Resolucion" value="<?php echo $datos['fchresol'] ?>" />
                          </div>
                        </div>  
                        <div class="col-sm-6">
                          <div class="form-group input-group-sm">
                            <span class="opacity-50">Nro. Resolucion</span>
                            <input type="text" class="form-control" name="nroresol" placeholder="Nro. Resolucion" value="<?php echo $datos['nroresol'] ?>" />
                          </div>
                        </div>
                      </div>

                      <div class="col-md-7">
                        <h4>Informacion de contacto</h4>
                        <span class="opacity-50">Telefonos</span><br/>
                        <div class="col-sm-12">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="Telefono" placeholder="Telefono" value="<?php echo $datos['telefono'] ?>" />
                          </div>
                        </div>

                        <span class="opacity-50">Email</span><br/>
                        <div class="col-sm-12">
                          <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="CorreoEmisor" placeholder="Email" value="<?php echo $datos['correo'] ?>" />
                          </div>
                        </div>

                        <div class="col-sm-12">
                          <div class="form-group input-group-sm">
                            <span class="opacity-50">Direccion</span><br/>
                            <input type="text" class="form-control" name="DirOrigen" placeholder="Dirección" value="<?php echo $datos['direccion'] ?>" />
                            <span class="opacity-50">Comuna</span><br/>
                            <input type="text" class="form-control" name="CmnaOrigen" placeholder="Comuna" value="<?php echo $datos['comuna'] ?>" />
                            <span class="opacity-50">Ciudad</span><br/>
                            <input type="text" class="form-control" name="CiudadOrigen" placeholder="Ciudad" value="<?php echo $datos['ciudad'] ?>" />
                          </div>
                        </div>                        
                      </div>
                    </div>

                    <div class="row">
                      <div class="panel panel-default">
                        <div class="panel-body">
                          <button type="submit" class="btn btn-block ink-reaction btn-primary">Actualizar Datos</button>
                        </div>
                      </div>
                    </div>
                    </form>
                  </div>
                </div>
              </div>
              
            </div><!--end .section-body -->
        </section>
      </div>
      <!-- END CONTENT -->
    </div>
  </body>
  <script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/datepicker.js"></script>
  <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
  <script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
  <script type="text/javascript">
    $('.date').datepicker({autoclose: true, todayHighlight: true, format: "yyyy/mm/dd",language: 'es'});
  </script>
</html>