<?php 
require_once '../../../../config.php';
$Folio = new Folio();
$datosFolio = $Folio->getFolio(39);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>DTE Chile | SET BÁSICO</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/style.css">
  </head>
  <body class="menubar-hoverable header-fixed full-content">
    <?php require_once '../../../../inc/header.php'; ?>
    <?php require_once '../../../../inc/menu.php'; ?>

    <div id="base">
      <div id="content">
        <section>
          <div class="section-header">
            <ol class="breadcrumb">
              <li><a href="<?php echo BASEURL ?>emision.php">Emitir documento</a></li>
              <li class="active">SET BÁSICO - BOLETAS ELECTRÓNICAS</li>
            </ol>
          </div>
          <div class="section-body">
            <div class="col-lg-offset-1 col-md-10 col-sm-12">
              <div class="card">
                <div class="card-body">
                  <form action="SetPruebas.php" method="post" class="form-horizontal">
                    <div class="form-group">
                      <label class="col-sm-3 control-label">Folio Inicial:</label>
                      <div class="col-sm-9">
                        <input type="number" class="form-control" name="folioInicial" 
                               value="<?php echo $datosFolio['folio_actual'] ?>" required>
                      </div>
                    </div>

                    <!-- Caso 1 -->
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Caso 1</h4>
                      </div>
                      <div class="panel-body">
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item 1:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso1_item1" value="Cambio de aceite">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso1_cantidad1" value="1" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso1_precio1" value="19900" placeholder="Precio">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item 2:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso1_item2" value="Alineacion y balanceo">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso1_cantidad2" value="1" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso1_precio2" value="9900" placeholder="Precio">
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Caso 2 -->
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Caso 2</h4>
                      </div>
                      <div class="panel-body">
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso2_item1" value="Papel de regalo">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso2_cantidad1" value="17" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso2_precio1" value="120" placeholder="Precio">
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Caso 3 -->
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Caso 3</h4>
                      </div>
                      <div class="panel-body">
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item 1:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso3_item1" value="Sandwic">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso3_cantidad1" value="2" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso3_precio1" value="1500" placeholder="Precio">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item 2:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso3_item2" value="Bebida">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso3_cantidad2" value="2" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso3_precio2" value="550" placeholder="Precio">
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Caso 4 -->
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Caso 4</h4>
                      </div>
                      <div class="panel-body">
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item 1:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso4_item1" value="item afecto 1">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso4_cantidad1" value="8" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso4_precio1" value="1590" placeholder="Precio">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item 2:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso4_item2" value="item exento 2">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso4_cantidad2" value="2" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso4_precio2" value="1000" placeholder="Precio">
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Caso 5 -->
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">Caso 5</h4>
                      </div>
                      <div class="panel-body">
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Item:</label>
                          <div class="col-sm-4">
                            <input type="text" class="form-control" name="caso5_item1" value="Arroz">
                          </div>
                          <div class="col-sm-2">
                            <input type="number" class="form-control" name="caso5_cantidad1" value="5" placeholder="Cantidad">
                          </div>
                          <div class="col-sm-3">
                            <input type="number" class="form-control" name="caso5_precio1" value="700" placeholder="Precio">
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group text-center">
                      <button type="submit" class="btn btn-primary btn-lg">Generar Documentos</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
    <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
    <script src="<?php echo BASEURL ?>asset/js/application.js"></script>
  </body>
</html>