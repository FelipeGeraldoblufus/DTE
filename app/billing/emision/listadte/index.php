<?php
require_once '../../../../config.php';

// Obtener RUT de la empresa solo desde la sesión
$rutEmpresa = isset($_SESSION['rut_empresa']) ? $_SESSION['rut_empresa'] : null;

// Definir tipos de documentos
$tiposDTE = [
    '33' => 'Factura Electrónica',
    '34' => 'Factura Electrónica Exenta',
    '39' => 'Boleta Electrónica',
    '41' => 'Boleta Electrónica exenta',
    '52' => 'Guía de despacho',
    '56' => 'Nota de débito Electrónica',
    '61' => 'Nota de crédito Electrónica'
];

// Inicializar variables
$documentos = [];
$tipoDTESeleccionado = isset($_GET['tipo_dte']) ? $_GET['tipo_dte'] : '';
$mostrarDocumentos = false;

// Obtener documentos si hay un RUT de empresa y se ha seleccionado un tipo de DTE
if (!empty($rutEmpresa) && !empty($tipoDTESeleccionado)) {
    $mostrarDocumentos = true;
    $Documentos = new IngresoDocumento();
    
    // Obtener documentos del tipo seleccionado
    $documentos = $Documentos->getDocumentosPorEmpresaYTipo($rutEmpresa, $tipoDTESeleccionado);
    
    // Si no hay resultados, inicializar como array vacío
    if ($documentos === false) {
        $documentos = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>DTE Chile | Listado de Documentos</title>
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
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/toastr.css">
    <style>
        .list-documentos .panel {
            border-left: 4px solid #2196F3;
            margin-bottom: 15px;
        }
        .list-documentos h4 {
            margin-top: 0;
            font-weight: 500;
        }
        .list-documentos h5 {
            color: #666;
            margin-bottom: 15px;
        }
        .list-documentos .btn-group {
            margin-top: 10px;
        }
        .filtro-dte {
            margin-bottom: 20px;
        }
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-left: 5px;
            vertical-align: middle;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: #2196F3;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <!-- END STYLESHEET -->
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
                        <li><a href="<?php echo BASEURL ?>">Inicio</a></li>
                        <li class="active">Documentos</li>
                    </ol>
                </div>
                <div class="section-body">
                    <?php if (empty($rutEmpresa)): ?>
                        <div class="alert alert-warning">
                            No hay información de empresa en la sesión. Por favor inicie sesión nuevamente.
                        </div>
                    <?php else: ?>
                        <!-- Filtro por tipo de DTE -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-head">
                                        <header>Filtrar Documentos</header>
                                    </div>
                                    <div class="card-body">
                                        <form method="GET" action="" class="form-horizontal filtro-dte">
                                            <div class="form-group">
                                                <label for="tipo_dte" class="col-sm-2 control-label">Tipo de Documento:</label>
                                                <div class="col-sm-4">
                                                    <select name="tipo_dte" id="tipo_dte" class="form-control" required>
                                                        <option value="">Seleccione un tipo de documento</option>
                                                        <?php foreach ($tiposDTE as $codigo => $nombre): ?>
                                                            <option value="<?php echo $codigo; ?>" <?php echo ($tipoDTESeleccionado == $codigo) ? 'selected' : ''; ?>>
                                                                <?php echo $nombre; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <button type="submit" class="btn btn-primary">Buscar Documentos</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($mostrarDocumentos): ?>
                            <!-- Resultados de la búsqueda -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-head">
                                            <header>
                                                <?php 
                                                    echo isset($tiposDTE[$tipoDTESeleccionado]) ? $tiposDTE[$tipoDTESeleccionado] : 'Documentos'; 
                                                ?>
                                            </header>
                                        </div>
                                        <div class="card-body">
                                            <?php if (empty($documentos)): ?>
                                                <div class="alert alert-info">
                                                    No se han encontrado documentos del tipo seleccionado.
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info">
                                                    Se han encontrado <?php echo count($documentos); ?> documentos
                                                </div>
                                                
                                                <!-- Listado de documentos estilo tarjeta -->
                                                <div class="list-documentos">
                                                    <?php foreach ($documentos as $doc): ?>
                                                        <div class="panel panel-default">
                                                            <div class="panel-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <h4>
                                                                            <?php 
                                                                            $tipoDTE = isset($tiposDTE[$doc['dte']]) ? $tiposDTE[$doc['dte']] : 'Documento';
                                                                            echo $tipoDTE; 
                                                                            ?> 
                                                                            N° <span class="text-primary"><?php echo $doc['folio']; ?></span>
                                                                        </h4>
                                                                        <h5><?php echo isset($doc['cliente_rut']) ? $doc['cliente_rut'] : 'Cliente no especificado'; ?></h5>
                                                                        
                                                                        <p>
                                                                            Documento emitido con fecha <?php echo date('d/m/Y', strtotime($doc['emision'])); ?> por un monto de $ <?php echo number_format($doc['total'], 0, ',', '.'); ?><br>
                                                                        </p>
                                                                        
                                                                        <div class="btn-group" role="group">
                                                                            <a href="descargar_pdf.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-info" title="Descargar PDF">
                                                                                <i class="fa fa-download"></i> PDF
                                                                            </a>
                                                                            <a href="descargar_xml.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-primary" title="Descargar XML">
                                                                                <i class="fa fa-download"></i> XML
                                                                            </a>
                                                                            <button type="button" class="btn btn-sm btn-success btn-reenviar" 
                                                                                   data-id="<?php echo $doc['id']; ?>" 
                                                                                   title="Reenviar por correo">
                                                                                <i class="fa fa-envelope"></i> Reenviar
                                                                                <span class="loading-<?php echo $doc['id']; ?>" style="display:none;"></span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <!-- END CONTENT -->
    </div>
    <!-- END BASE -->

    <script src="<?php echo BASEURL ?>asset/js/jquery.js"></script>
    <script src="<?php echo BASEURL ?>asset/js/bootstrap.js"></script>
    <script src="<?php echo BASEURL ?>asset/js/application.js" type="text/javascript"></script>
    <script src="<?php echo BASEURL ?>asset/js/navigation.js" type="text/javascript"></script>
    <script src="<?php echo BASEURL ?>asset/js/toastr.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Configuración de toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000"
            };
            
            // Evento para el botón de reenvío de correo
            $('.btn-reenviar').click(function() {
                var btn = $(this);
                var docId = btn.data('id');
                var loadingElement = $('.loading-' + docId);
                
                // Mostrar indicador de carga
                loadingElement.addClass('loading').show();
                btn.prop('disabled', true);
                
                // Realizar solicitud AJAX para reenviar el correo
                $.ajax({
                    url: 'reenviar_correo.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: docId
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Error al procesar la solicitud');
                    },
                    complete: function() {
                        // Ocultar indicador de carga y habilitar botón
                        loadingElement.removeClass('loading').hide();
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>