<?php
require_once '../../../../config.php';

class GeneradorRespuestasSetIntercambio {
    private $xmlOriginal;
    private $Firma;

    public function __construct($rutaArchivoXml, FirmaElectronica $Firma) {
        $this->xmlOriginal = simplexml_load_file($rutaArchivoXml);
        $this->Firma = $Firma;
    }

    public function generarRespuestaIntercambio() {
        $respuestaIntercambio = new RespuestaEnvio();
    
        $caratula = [
            'RutResponde' => '11.111.111-1',
            'RutRecibe' => (string)$this->xmlOriginal->SetDTE->Caratula->RutEmisor,
            'IdRespuesta' => 1,
            'NroDetalles' => count($this->xmlOriginal->SetDTE->DTE),
            'NmbContacto' => '',
            'FonoContacto' => '',
            'MailContacto' => '',
            'TmstFirmaResp' => date('Y-m-d\TH:i:s')
        ];
        $respuestaIntercambio->setCaratula($caratula);
    
        $respuestaIntercambio->agregarRespuestaEnvio([
            'NmbEnvio' => 'ENVIO_DTE_' . date('YmdHis'),
            'FchRecep' => date('Y-m-d\TH:i:s'),
            'CodEnvio' => 1,
            'EnvioDTEID' => 'SetDoc',
            'RutEmisor' => (string)$this->xmlOriginal->SetDTE->Caratula->RutEmisor,
            'RutReceptor' => (string)$this->xmlOriginal->SetDTE->Caratula->RutReceptor,
            'EstadoRecepEnv' => 0,
            'RecepEnvGlosa' => 'Envío recibido conforme.',
            'NroDTE' => count($this->xmlOriginal->SetDTE->DTE),
            'RecepcionDTE' => $this->generarRecepcionDTE()
        ]);
    
        $respuestaIntercambio->setFirma($this->Firma);
        return $respuestaIntercambio->generar();
    }
    
    private function generarRecepcionDTE() {
        $recepciones = [];
        
        foreach ($this->xmlOriginal->SetDTE->DTE as $dte) {
            $rutRecep = (string)$dte->Documento->Encabezado->Receptor->RUTRecep;
            $estadoRecepcion = 0;
            $glosaRecepcion = 'DTE Recibido OK.';
            
            if ($rutRecep != '11.111.111-1') {
                $estadoRecepcion = 3;
                $glosaRecepcion = 'DTE No Recibido - Error en RUT Receptor.';
            }
            
            $recepciones[] = [
                'TipoDTE' => (string)$dte->Documento->Encabezado->IdDoc->TipoDTE,
                'Folio' => (string)$dte->Documento->Encabezado->IdDoc->Folio,
                'FchEmis' => (string)$dte->Documento->Encabezado->IdDoc->FchEmis,
                'RUTEmisor' => (string)$dte->Documento->Encabezado->Emisor->RUTEmisor,
                'RUTRecep' => $rutRecep,
                'MntTotal' => (string)$dte->Documento->Encabezado->Totales->MntTotal,
                'EstadoRecepDTE' => $estadoRecepcion,
                'RecepDTEGlosa' => $glosaRecepcion
            ];
        }
        
        return $recepciones;
    }

    public function generarReciboMercaderias() {
        $envioRecibos = new EnvioRecibos();

        $caratula = [
            'RutResponde' => '11.111.111-1',
            'RutRecibe' => (string)$this->xmlOriginal->SetDTE->Caratula->RutEmisor,
            'NmbContacto' => '',
            'FonoContacto' => '',
            'MailContacto' => '',
        ];
        $envioRecibos->setCaratula($caratula);

        foreach ($this->xmlOriginal->SetDTE->DTE as $dte) {
            $datosRecibo = [
                'TipoDoc' => (string)$dte->Documento->Encabezado->IdDoc->TipoDTE,
                'Folio' => (string)$dte->Documento->Encabezado->IdDoc->Folio,
                'FchEmis' => (string)$dte->Documento->Encabezado->IdDoc->FchEmis,
                'RUTEmisor' => (string)$dte->Documento->Encabezado->Emisor->RUTEmisor,
                'RUTRecep' => (string)$dte->Documento->Encabezado->Receptor->RUTRecep,
                'MntTotal' => (string)$dte->Documento->Encabezado->Totales->MntTotal,
                'Recinto' => 'Santiago',
                'RutFirma' => $this->Firma->getID()
            ];
            $envioRecibos->agregar($datosRecibo);
        }

        $envioRecibos->setFirma($this->Firma);
        return $envioRecibos->generar();
    }

    public function generarResultadoAprobacionComercial() {
        $respuestaComercial = new RespuestaEnvio();
    
        $caratula = [
            'RutResponde' => '11.111.111-1',
            'RutRecibe' => (string)$this->xmlOriginal->SetDTE->Caratula->RutEmisor,
            'IdRespuesta' => 1,
            'NroDetalles' => count($this->xmlOriginal->SetDTE->DTE),
            'NmbContacto' => '',
            'FonoContacto' => '',
            'MailContacto' => '',
            'TmstFirmaResp' => date('Y-m-d\TH:i:s')
        ];
        $respuestaComercial->setCaratula($caratula);
    
        foreach ($this->xmlOriginal->SetDTE->DTE as $dte) {
            $respuestaComercial->agregarRespuestaDocumento([
                'TipoDTE' => (string)$dte->Documento->Encabezado->IdDoc->TipoDTE,
                'Folio' => (string)$dte->Documento->Encabezado->IdDoc->Folio,
                'FchEmis' => (string)$dte->Documento->Encabezado->IdDoc->FchEmis,
                'RUTEmisor' => (string)$dte->Documento->Encabezado->Emisor->RUTEmisor,
                'RUTRecep' => (string)$dte->Documento->Encabezado->Receptor->RUTRecep,
                'MntTotal' => (string)$dte->Documento->Encabezado->Totales->MntTotal,
                'CodEnvio' => 1,
                'EstadoDTE' => 0,
                'EstadoDTEGlosa' => 'DTE Aceptado OK'
            ]);
        }
    
        $respuestaComercial->setFirma($this->Firma);
        return $respuestaComercial->generar();
    }

    public function guardarArchivos($directorioSalida = null) {
        if ($directorioSalida === null) {
            $directorioSalida = __ROOT__ . '/archives/respuestas_set_intercambio/';
        }

        if (!file_exists($directorioSalida)) {
            mkdir($directorioSalida, 0777, true);
        }

        $timestamp = date('YmdHis');
        $archivos = [
            'respuesta_intercambio' => $directorioSalida . "RespuestaIntercambio_{$timestamp}.xml",
            'recibo_mercaderias' => $directorioSalida . "ReciboMercaderias_{$timestamp}.xml",
            'resultado_comercial' => $directorioSalida . "ResultadoComercial_{$timestamp}.xml"
        ];

        $resultados = [
            'respuesta_intercambio' => $this->generarRespuestaIntercambio(),
            'recibo_mercaderias' => $this->generarReciboMercaderias(),
            'resultado_comercial' => $this->generarResultadoAprobacionComercial()
        ];

        foreach ($resultados as $tipo => $contenido) {
            file_put_contents($archivos[$tipo], $contenido);
        }

        return $archivos;
    }
}

class ProcesamientoSetIntercambio {
    private $directorioSubidas;
    private $directorioRespuestas;

    public function __construct() {
        $this->directorioSubidas = __ROOT__ . '/archives/set_intercambio/';
        $this->directorioRespuestas = __ROOT__ . '/archives/respuestas_set_intercambio/';
        
        $this->crearDirectorio($this->directorioSubidas);
        $this->crearDirectorio($this->directorioRespuestas);
    }

    private function crearDirectorio($directorio) {
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
    }

    public function validarYSubirArchivo($archivo) {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error en la subida del archivo.');
        }

        $tipoArchivo = mime_content_type($archivo['tmp_name']);
        $extensionArchivo = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if ($tipoArchivo !== 'text/xml' && $extensionArchivo !== 'xml') {
            throw new Exception('El archivo debe ser XML.');
        }

        if ($archivo['size'] > 10 * 1024 * 1024) {
            throw new Exception('El archivo es demasiado grande. Máximo 10MB.');
        }

        $nombreArchivo = 'set_intercambio_' . date('YmdHis') . '_' . uniqid() . '.xml';
        $rutaCompleta = $this->directorioSubidas . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            throw new Exception('No se pudo guardar el archivo.');
        }

        return $rutaCompleta;
    }

    public function generarRespuestas($rutaXml) {
        // Configurar credenciales de firma desde base de datos o variables de entorno
        $rutaFirma = '';
        $passFirma = '';
        $Firma = new FirmaElectronica($rutaFirma, $passFirma);

        $generador = new GeneradorRespuestasSetIntercambio($rutaXml, $Firma);
        $archivosGenerados = $generador->guardarArchivos($this->directorioRespuestas);

        // Crear archivo ZIP
        $timestamp = date('YmdHis');
        $zipName = "respuestas_set_intercambio_{$timestamp}.zip";
        $zipPath = $this->directorioRespuestas . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($archivosGenerados as $tipo => $rutaArchivo) {
                $zip->addFile($rutaArchivo, basename($rutaArchivo));
            }
            $zip->close();

            // Forzar la descarga del ZIP
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
            header('Content-Length: ' . filesize($zipPath));
            readfile($zipPath);

            // Limpiar archivos
            unlink($zipPath);
            foreach ($archivosGenerados as $rutaArchivo) {
                unlink($rutaArchivo);
            }

            exit();
        }

        return [
            'original' => basename($rutaXml),
            'respuestas' => array_map('basename', $archivosGenerados)
        ];
    }
}

// Procesamiento de la página
$mensajeExito = null;
$mensajeError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmlFile'])) {
    try {
        $procesador = new ProcesamientoSetIntercambio();
        $rutaXml = $procesador->validarYSubirArchivo($_FILES['xmlFile']);
        $procesador->generarRespuestas($rutaXml);
        
        // Si llegamos aquí es porque hubo un error al crear el ZIP
        $mensajeError = "Error al crear el archivo ZIP de respuestas.";
    } catch (Exception $e) {
        $mensajeError = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir XML Set de Intercambio</title>
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASEURL ?>asset/css/style.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
</head>
<body class="menubar-hoverable header-fixed full-content">
    <?php require_once '../../../../inc/header.php'; ?>
    <?php require_once '../../../../inc/menu.php'; ?>

    <div id="base">
        <div id="content">
            <section>
                <div class="section-header">
                    <ol class="breadcrumb">
                        <li><a href="<?php echo BASEURL ?>">Inicio</a></li>
                        <li class="active">Subir Set de Intercambio</li>
                    </ol>
                </div>
                <div class="section-body">
                    <div class="col-lg-offset-2 col-md-8 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Subir XML de Set de Intercambio</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($mensajeExito): ?>
                                    <div class="alert alert-success">
                                        <?php echo $mensajeExito; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($mensajeError): ?>
                                    <div class="alert alert-danger">
                                        <?php echo $mensajeError; ?>
                                    </div>
                                <?php endif; ?>

                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="xmlFile">Seleccionar XML del Set de Intercambio</label>
                                        <input type="file" class="form-control" id="xmlFile" name="xmlFile" accept=".xml" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-upload"></i> Generar EnvioRecibos
                                        </button>
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
    <script src="<?php echo BASEURL ?>asset/js/navigation.js"></script>
</body>
</html>