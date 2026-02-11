<?php
require_once '../../../../config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función para registrar errores en un archivo de log
function debug_log($message) {
    $log_file = __DIR__ . '/debug_reenvio.log';
    $date = date('[Y-m-d H:i:s]');
    file_put_contents($log_file, $date . ' ' . $message . PHP_EOL, FILE_APPEND);
}

// Iniciar el log
debug_log('--- Inicio del proceso de reenvío ---');

// Verificar que se haya proporcionado un ID de documento
if (!isset($_GET['id']) || empty($_GET['id'])) {
    debug_log('Error: ID de documento no proporcionado');
    echo json_encode(['success' => false, 'message' => 'ID de documento no proporcionado']);
    exit;
}

$documentoId = (int)$_GET['id'];
debug_log("ID de documento: $documentoId");

// Verificar que la clase IngresoDocumento exista
if (!class_exists('IngresoDocumento')) {
    debug_log('Error: La clase IngresoDocumento no existe');
    echo json_encode(['success' => false, 'message' => 'Error interno: Clase IngresoDocumento no encontrada']);
    exit;
}

// Obtener información del documento
try {
    $Documentos = new IngresoDocumento();
    $documento = $Documentos->getDocumento($documentoId);
    
    if (!$documento) {
        debug_log("Error: Documento con ID $documentoId no encontrado");
        echo json_encode(['success' => false, 'message' => 'Documento no encontrado']);
        exit;
    }
    
    debug_log("Documento encontrado: " . json_encode($documento));
} catch (Exception $e) {
    debug_log("Error al obtener documento: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener información del documento: ' . $e->getMessage()]);
    exit;
}

// Obtener información del cliente/receptor
try {
    $Cliente = new Cliente();
    $receptor = $Cliente->getCliente($documento['cliente_rut']);
    
    if (!$receptor) {
        debug_log("Error: Cliente con RUT {$documento['cliente_rut']} no encontrado");
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
        exit;
    }
    
    if (empty($receptor['correo_envio'])) {
        debug_log("Error: El cliente {$receptor['rut']} no tiene un correo de envío configurado");
        echo json_encode(['success' => false, 'message' => 'El cliente no tiene un correo de envío configurado']);
        exit;
    }
    
    debug_log("Receptor encontrado: " . json_encode($receptor));
} catch (Exception $e) {
    debug_log("Error al obtener cliente: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener información del cliente: ' . $e->getMessage()]);
    exit;
}

// Obtener información de la empresa emisora
try {
    $Empresa = new Empresa();
    $emisor = $Empresa->getEmpresaRut($documento['empresa_rut']);
    
    if (!$emisor) {
        debug_log("Error: Empresa con RUT {$documento['empresa_rut']} no encontrada");
        echo json_encode(['success' => false, 'message' => 'Información del emisor no encontrada']);
        exit;
    }
    
    debug_log("Emisor encontrado: " . json_encode($emisor));
} catch (Exception $e) {
    debug_log("Error al obtener empresa: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener información de la empresa: ' . $e->getMessage()]);
    exit;
}

// Verificar rutas de archivos
debug_log("Verificando rutas de archivos - XML: {$documento['ruta_xml']}, PDF: {$documento['ruta_pdf']}");

if (empty($documento['ruta_xml']) || empty($documento['ruta_pdf'])) {
    debug_log("Error: Rutas de archivos no disponibles");
    echo json_encode(['success' => false, 'message' => 'Las rutas a los archivos no están disponibles']);
    exit;
}

// Comprobar si los archivos existen físicamente
if (!file_exists($documento['ruta_xml'])) {
    debug_log("Error: El archivo XML no existe en la ruta especificada: {$documento['ruta_xml']}");
    echo json_encode(['success' => false, 'message' => 'El archivo XML no existe en el servidor']);
    exit;
}

if (!file_exists($documento['ruta_pdf'])) {
    debug_log("Error: El archivo PDF no existe en la ruta especificada: {$documento['ruta_pdf']}");
    echo json_encode(['success' => false, 'message' => 'El archivo PDF no existe en el servidor']);
    exit;
}

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

$tipoDTE = isset($tiposDTE[$documento['dte']]) ? $tiposDTE[$documento['dte']] : 'Documento ' . $documento['dte'];
debug_log("Tipo de documento: $tipoDTE");

// Función para enviar correo electrónico con los documentos adjuntos
function enviarEmailDTE($correoDestinatario, $rutEmisor, $razonSocialEmisor, $rutReceptor, $razonSocialReceptor, 
                      $tipoDTE, $folio, $fechaEmision, $montoTotal, $rutaXML, $rutaPDF) 
{
    global $debug_log;
    debug_log("Iniciando envío de correo a: $correoDestinatario");
    
    // Asunto del correo
    $subject = "Reenvío DTE - " . $razonSocialEmisor;
    
    // Formatea el monto para mostrar
    $montoFormateado = number_format($montoTotal, 0, ',', '.');
    
    // El contenido del mensaje HTML
    $message = '
    <div style="background-color:#f4f4f4; padding-bottom:100px; padding-right:5px; padding-top:20px; padding-left:5px; font-family:Arial,sans-serif; color:#444444; border-collapse:collapse; margin-top:0; margin-left:auto; margin-bottom:0; margin-right:auto">
        <center>
            <table style="margin-bottom:25px">
                <tr>
                    <td>
                        <a href="">
                            <img src="" shrinktofit="true" border="0">
                        </a>
                    </td>
                </tr>
            </table>

            <table width="600" style="background-color:white; font-family: Arial, sans-serif;border-collapse: collapse; border-width: 1px; border-style: solid; border-color:#ddd; box-shadow:3px 3px 15px rgba(0,0,0,0.2); ">
                <tbody>
                    <!-- head -->
                    <tr style="background-color:#f5f5f5;">
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <h3 style="color:#004A8D; margin-top:5px;">Reenvío · Documentos Tributarios Electrónicos<p style="font-weight:bold;color:#f64b45;margin:0;float: right;width: 60%;"></p></span></h3>
                        </td>
                    </tr>

                    <!-- Emisor y Receptor -->
                    <tr>
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <table style="width: 100%;">
                                <tr style="font-size: 18px; ">
                                    <td style="font-weight: bold;">Emisor</td>
                                    <td style="font-weight: bold;">Receptor</td>
                                </tr>
                                <tr style="font-size: 13px;">
                                    <td>' . $razonSocialEmisor . ' R.U.T.: ' . $rutEmisor . '</td>
                                    <td>' . $razonSocialReceptor . ' R.U.T.: ' . $rutReceptor . '</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Detalle -->
                    <tr style="border-bottom:1px solid #DDD;">
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <table style="width: 100%;">
                                <thead>
                                    <tr style="font-size: 13px;">
                                        <th style="font-weight: bold;">Emisor</th>
                                        <th style="font-weight: bold;">Tipo</th>
                                        <th style="font-weight: bold;">Folio</th>
                                        <th style="font-weight: bold;">Fecha</th>
                                        <th style="font-weight: bold;">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="font-size: 11px; text-align: center;">
                                        <td>' . $rutEmisor . '</td>
                                        <td>' . $tipoDTE . '</td>
                                        <td>' . $folio . '</td>
                                        <td>' . $fechaEmision . '</td>
                                        <td>' . $montoFormateado . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- descripcion -->
                    <tr style="border-bottom:1px solid #DDD;">
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <span style="color:#666; font-size: 10px;">Este es un reenvío de Documento Tributario Electrónico (DTE) para el receptor electrónico indicado. Por favor responda con un acuse de recibo (RespuestaDTE) conforme al modelo de intercambio de Factura Electrónica del SII.<br /></span>
                        </td>
                    </tr>

                    <tr style="background-color:#f5f5f5;">
                        <td style="color:#333; ">
                            <table class="social" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5 style="margin-top:12px;margin-left:15px;margin-bottom:0px;font-size: 14px; color:#004A8D;">Contáctanos a:</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table class="column" align="left" style="width: 340px; min-width: 339px; float: left;">
                                                <tbody>
                                                    <tr>
                                                        <td style="padding: 15px">
                                                            <div style="line-height: 1.6;">
                                                                <div><a style="font-size: 14px;color:#45AEF6" href=""></a></strong></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </center>
    </div>';

    // Verificar que los archivos existan
    if (!file_exists($rutaXML) || !file_exists($rutaPDF)) {
        debug_log("Error: Archivo no encontrado - XML: " . (file_exists($rutaXML) ? "OK" : "NO EXISTE") . ", PDF: " . (file_exists($rutaPDF) ? "OK" : "NO EXISTE"));
        return false;
    }

    // Obtener el contenido de los archivos
    $file_xml_content = file_get_contents($rutaXML);
    $file_pdf_content = file_get_contents($rutaPDF);
    
    if (!$file_xml_content || !$file_pdf_content) {
        debug_log("Error: No se pudo leer el contenido de los archivos");
        return false;
    }

    // Nombres de los archivos
    $file_xml_name = basename($rutaXML);
    $file_pdf_name = basename($rutaPDF);

    debug_log("Preparando archivos: XML: $file_xml_name, PDF: $file_pdf_name");

    // Codifica los archivos en base64
    $encoded_file_xml = base64_encode($file_xml_content);
    $encoded_file_pdf = base64_encode($file_pdf_content);

    // Genera los encabezados para el correo
    $boundary = "----=_NextPart_" . md5(time());

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"" . "\r\n";

    // Agregar el encabezado From
    $headers .= 'From: "' . $razonSocialEmisor . '" <>' . "\r\n";
    $headers .= 'Reply-To: ' . "\r\n";

    // Encabezado del cuerpo del correo (mensaje principal)
    $body = "--$boundary" . "\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $body .= "Content-Transfer-Encoding: 7bit" . "\r\n";
    $body .= "\r\n";
    $body .= $message . "\r\n";

    // Adjuntar el archivo XML
    $body .= "--$boundary" . "\r\n";
    $body .= "Content-Type: application/xml; name=\"$file_xml_name\"" . "\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$file_xml_name\"" . "\r\n";
    $body .= "Content-Transfer-Encoding: base64" . "\r\n";
    $body .= "\r\n";
    $body .= chunk_split($encoded_file_xml) . "\r\n";

    // Adjuntar el archivo PDF
    $body .= "--$boundary" . "\r\n";
    $body .= "Content-Type: application/pdf; name=\"$file_pdf_name\"" . "\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$file_pdf_name\"" . "\r\n";
    $body .= "Content-Transfer-Encoding: base64" . "\r\n";
    $body .= "\r\n";
    $body .= chunk_split($encoded_file_pdf) . "\r\n";

    // Cerrar el mensaje con el boundary
    $body .= "--$boundary--" . "\r\n";

    // Enviar el correo
    debug_log("Intentando enviar correo a $correoDestinatario");
    $result = mail($correoDestinatario, $subject, $body, $headers);
    
    if ($result) {
        debug_log("Correo enviado exitosamente a $correoDestinatario");
    } else {
        debug_log("Error al enviar correo a $correoDestinatario");
    }
    
    return $result;
}

// Enviar el correo
try {
    $resultado = enviarEmailDTE(
        $receptor['correo_envio'],
        $documento['empresa_rut'],
        $emisor['rznsoc'],
        $documento['cliente_rut'],
        $receptor['rznsoc'],
        $documento['dte'],
        $documento['folio'],
        $documento['emision'],
        $documento['total'],
        $documento['ruta_xml'],
        $documento['ruta_pdf']
    );

    // Devolver respuesta
    if ($resultado) {
        debug_log("Correo reenviado exitosamente a " . $receptor['correo_envio']);
        echo json_encode([
            'success' => true, 
            'message' => 'Correo reenviado exitosamente a ' . $receptor['correo_envio']
        ]);
    } else {
        debug_log("Error al reenviar el correo a " . $receptor['correo_envio']);
        echo json_encode([
            'success' => false, 
            'message' => 'Error al reenviar el correo. Por favor revise el registro de errores.'
        ]);
    }
} catch (Exception $e) {
    debug_log("Excepción al enviar correo: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error al enviar el correo: ' . $e->getMessage()
    ]);
}

debug_log('--- Fin del proceso de reenvío ---');