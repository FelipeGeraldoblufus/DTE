<?php
require_once '../../../../config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicializar objetos necesarios
$Folio = new Folio();
$TipoFolio = new TipoFolio();
$TipoDTE = 41; // Boleta Exenta Electrónica

// Obtener el siguiente folio disponible
$datosTipoFolio = $TipoFolio->numeroTipoFolio($TipoDTE);
$datosFolio = $Folio->getFolio($TipoDTE);
$tipoNumero = isset($datosTipoFolio['tipo_numero']) ? $datosTipoFolio['tipo_numero'] : $TipoDTE;
$folioActual = isset($datosFolio['folio_actual']) ? $datosFolio['folio_actual'] : 1;

// Obtener datos de la empresa emisora
$Empresa = new Empresa();
$datosEmpresa = $Empresa->listaEmpresa();
$rutEmisor = $datosEmpresa['rut'];
$logo = !empty($datosEmpresa['logo']) ? BASEURL.$datosEmpresa['logo'] : false;

// Obtener firma de la empresa
$empresaData = $Empresa->getEmpresaRut($rutEmisor);
$rutFirma = $empresaData['firma'];
$FirmaA = new Firma();
$datosFirma = $FirmaA->getFirmaByRut($rutFirma);
if (!$datosFirma) {
  die('Error: No se encontró la firma electrónica para esta empresa');
}
$rutaFirma = $datosFirma['ruta'];
$passFirma = $datosFirma['pass'];

// Añade verificación antes de crear el objeto FirmaElectronica
if (!file_exists($rutaFirma)) {
  die('Error: No se encuentra el archivo de firma electrónica');
}

// Crear objeto firma
$Firma = new FirmaElectronica($rutaFirma, $passFirma);

// Ruta de los folios
$rutaFolios = BASEURL.'client/folio/41/41.xml';
$Folios = new Folios(file_get_contents($rutaFolios));

// Crear estructura básica del DTE
$dte = [
    'Encabezado' => [
        'IdDoc' => [
            'TipoDTE' => '41', // Boleta Exenta Electrónica
            'Folio' => $folioActual,
            'FchEmis' => $_POST['FchEmis'] ?: date('Y-m-d'),
            'IndServicio' => $_POST['IndServicio'] ?: '3', // Por defecto servicios periódicos
        ],
        'Emisor' => [
            'RUTEmisor' => $rutEmisor,
            'RznSocEmisor' => $datosEmpresa['rznsoc'],
            'GiroEmisor' => $datosEmpresa['giro'],
            'DirOrigen' => $datosEmpresa['direccion'],
            'CmnaOrigen' => $datosEmpresa['comuna'],
            'CiudadOrigen' => $datosEmpresa['ciudad'],
            'Telefono' => $datosEmpresa['telefono']
        ],
        'Receptor' => [
            'RUTRecep' => $_POST['RUTRecep'] ?: '66666666-6',
            'RznSocRecep' => $_POST['RznSocRecep'] ?: 'Cliente Boleta',
            'DirRecep' => $_POST['DirRecep'] ?: '',
            'CmnaRecep' => $_POST['CmnaRecep'] ?: '',
            'CiudadRecep' => $_POST['CiudadRecep'] ?: '',
            'Contacto' =>  $_POST['Contacto'] ?: ''
        ],
        'Totales' => []
    ],
    'Detalle' => []
];

// Procesar items - Para boleta exenta, todo es exento de IVA
$totalExento = 0;

// Verificamos que existan items
if (!empty($_POST['item'])) {
    foreach ($_POST['item'] as $idx => $item) {
        // Verificamos que existan los campos requeridos
        if (!empty($item['NmbItem']) && !empty($item['QtyItem']) && !empty($item['PrcItem'])) {
            $cantidad = $item['QtyItem'];
            $precio = $item['PrcItem'];
            $montoItem = $cantidad * $precio;
            
            // Aplicar descuento si existe
            if (!empty($item['DescuentoPct'])) {
                $descuentoPct = $item['DescuentoPct'];
                $descuentoMonto = round($montoItem * ($descuentoPct / 100));
                $montoItem -= $descuentoMonto;
            }
            
            $itemDTE = [
                'NroLinDet' => $idx + 1,
                'NmbItem' => $item['NmbItem'],
                'QtyItem' => $cantidad,
                'PrcItem' => $precio,
                'MontoItem' => $montoItem,
                'IndExe' => 1  // Marcamos todos los ítems como exentos para boleta exenta
            ];
            
            // Agregar descripción del item si existe
            if (!empty($item['DscItem'])) {
                $itemDTE['DscItem'] = $item['DscItem'];
            }
            
            // Agregar unidad de medida si existe
            if (!empty($item['UnmdItem'])) {
                $itemDTE['UnmdItem'] = $item['UnmdItem'];
            }
            
            // Agregar descuento si existe
            if (!empty($item['DescuentoPct'])) {
                $itemDTE['DescuentoPct'] = $item['DescuentoPct'];
                $itemDTE['DescuentoMonto'] = $descuentoMonto;
            }
            
            // Todos los items son exentos en una boleta exenta
            $totalExento += $montoItem;
            
            $dte['Detalle'][] = $itemDTE;
        }
    }
}

// Verificar si se enviaron detalles
if (empty($dte['Detalle'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: No se encontraron productos para la boleta exenta'
    ]);
    exit();
}

// Calcular totales
$dte['Encabezado']['Totales']['MntExe'] = $totalExento;
$dte['Encabezado']['Totales']['MntTotal'] = $totalExento;

// Crear y procesar DTE
try {
    $DTE = new Dte($dte);
    $DTE->timbrar($Folios);
    $DTE->firmar($Firma);

    // Crear EnvioBoleta
    $EnvioBoleta = new EnvioBoleta();
    $EnvioBoleta->agregar($DTE);
    
    // Configurar carátula para SII
    $caratulaSII = [    
        'RutEmisor' => $rutEmisor,
        'RutEnvia' => $Firma->getID(),
        'RutReceptor' => '60803000-K', // Rut SII
        'FchResol' => '2025-01-03',
        'NroResol' => 0,
        'TmstFirmaEnv' => date('Y-m-d\TH:i:s')
    ];

    // Configurar carátula para el Receptor
    $caratulaReceptor = [    
        'RutEmisor' => $rutEmisor,
        'RutEnvia' => $Firma->getID(),
        'RutReceptor' => $_POST['RUTRecep'] ?: '66666666-6',
        'FchResol' => '2025-01-03',
        'NroResol' => 0,
        'TmstFirmaEnv' => date('Y-m-d\TH:i:s')
    ];

    $EnvioBoleta->setCaratula($caratulaSII);
    $EnvioBoleta->setFirma($Firma);

    // Preparar para el Receptor
    $EnvioBoletaReceptor = new EnvioBoleta();
    $EnvioBoletaReceptor->agregar($DTE);
    $EnvioBoletaReceptor->setFirma($Firma);
    $EnvioBoletaReceptor->setCaratula($caratulaReceptor);

    // Generar XML
    $xml = $EnvioBoleta->generar();
    $xmlReceptor = $EnvioBoletaReceptor->generar();
    
    $Directorio = new Directorio();
    
    try {
        $carpetaXML = $Directorio->creaDirectorio(__ROOT__.'/archives/xml/41');
        if (!$carpetaXML) {
            throw new Exception('Error: No se pudo crear el directorio XML');
        }
        
        $carpetaXMLCopia = $Directorio->creaDirectorio(__ROOT__.'/archives/xml_copia/41');
        if (!$carpetaXMLCopia) {
            throw new Exception('Error: No se pudo crear el directorio XML Copia');
        }
        
        $carpetaPDF = $Directorio->creaDirectorio(__ROOT__.'/archives/pdf/41');
        if (!$carpetaPDF) {
            throw new Exception('Error: No se pudo crear el directorio PDF');
        }
    } catch (Exception $e) {
        throw new Exception('Error al crear directorios: ' . $e->getMessage());
    }
    
    $nombreArchivo = 'D0C' . $TipoDTE . $folioActual;
    $archivoXML = $carpetaXML . $nombreArchivo . '.xml';
    $archivoXMLCopia = $carpetaXMLCopia . $nombreArchivo . '.xml';
    
    file_put_contents($archivoXML, $xml);
    file_put_contents($archivoXMLCopia, $xmlReceptor);

    // Generar PDF de boleta exenta
    $archivoPDF = generarPDFBoletaExenta($DTE, $carpetaPDF, $nombreArchivo);
    
    // Obtener el correo del cliente
    $cliente = new Cliente();
    $rut = $cliente->getCliente($_POST['RUTRecep']);
    $correo = isset($rut['correo_envio']) ? $rut['correo_envio'] : '';
    
    // Enviar correo si hay un destinatario
    if (!empty($correo)) {
        $resultado_email = enviarEmailDTE(
            $correo,                        // Correo del destinatario
            $rutEmisor,                     // RUT emisor
            $datosEmpresa['rznsoc'],        // Razón social emisor
            $_POST['RUTRecep'],             // RUT receptor
            $_POST['RznSocRecep'],          // Razón social receptor
            $TipoDTE,                       // Tipo de DTE (41 para boleta exenta)
            $folioActual,                   // Número de folio
            $_POST['FchEmis'] ?: date('Y-m-d'), // Fecha de emisión
            $totalExento,                   // Monto total (exento)
            $archivoXMLCopia,               // Ruta al XML (copia para receptor)
            $archivoPDF                     // Ruta al PDF
        );
        
        if ($resultado_email) {
            $mensaje_email = "Correo enviado exitosamente a: " . $correo;
        } else {
            $mensaje_email = "Error al enviar el correo a: " . $correo;
        }
    } else {
        $mensaje_email = "No se envió correo: destinatario no especificado.";
    }
    
    // Guardar en la base de datos
    $Documentos = new IngresoDocumento();
    
    // Preparar datos para guardar en la base de datos
    $documentoValues = [
        "Venta",                          // tipo (string)
        intval($TipoDTE),                 // dte (integer)
        intval($folioActual),             // folio (integer)
        $_POST['FchEmis'] ?: date('Y-m-d'), // emision (string)
        $_POST['FchEmis'] ?: date('Y-m-d'), // vencimiento (string)
        isset($dte['Encabezado']['IdDoc']['FmaPago']) ? 
            ($dte['Encabezado']['IdDoc']['FmaPago'] == 1 ? 'Contado' : 
            ($dte['Encabezado']['IdDoc']['FmaPago'] == 2 ? 'Crédito' : 'Sin Costo')) : 'Contado', // forma_pago
        null,                             // desc_documento (string vacío en lugar de null)
        $totalExento,                     // exento (numeric)
        0,                                // iva (numeric) - 0 para boletas exentas
        null,                             // otro_impuesto (numeric)
        $totalExento,                     // total (numeric)
        $rutEmisor,                       // emisor_rut
        $_POST['RUTRecep'] ?: '66666666-6', // cliente_rut (string)
        null,                             // proveedor_rut (string)
        $archivoXMLCopia,                 // ruta_xml (string) - usando la ruta exacta del archivo XML
        $archivoPDF                       // ruta_pdf (string) - usando la ruta exacta del archivo PDF
    ];
    
    // Registrar el documento
    $resultadoRegistro = $Documentos->newDocumento($documentoValues);
    
    // Procesar resultado del registro
    if ($resultadoRegistro === true) {
        $documentoId = $_SESSION['id_doc'];
        $mensaje_db = "Documento insertado con éxito. ID: " . $documentoId;
        
        // Ahora insertamos los detalles
        $exito_detalles = true;
        $mensaje_detalles = "";
        
        foreach ($dte['Detalle'] as $index => $item) {
            // Obtiene los valores del ítem
            $valorCodigo = isset($item['CdgItem']['VlrCodigo']) ? $item['CdgItem']['VlrCodigo'] : '';
            $cantidad = isset($item['QtyItem']) ? floatval($item['QtyItem']) : 0;
            $precio = isset($item['PrcItem']) ? floatval($item['PrcItem']) : 0;
            $descuento = isset($item['DescuentoMonto']) ? floatval($item['DescuentoMonto']) : 0;
            $total = $item['MontoItem'];
            
            // Crear array con solo los campos que existen en la tabla
            $detalleValues = [
                $documentoId,           // documento_id
                $valorCodigo,           // codigo_producto
                '',                     // codigo_servicio
                $cantidad,              // cantidad
                $precio,                // precio
                $descuento,             // descuento
                $total                  // total
            ];
            
            $resultadoDetalle = $Documentos->newDetalleDocumento($detalleValues);
            
            if ($resultadoDetalle !== true) {
                $exito_detalles = false;
                $mensaje_detalles .= "Error al guardar detalle #" . ($index + 1) . ": ";
                if (is_object($resultadoDetalle)) {
                    $mensaje_detalles .= $resultadoDetalle->getMessage() . " (Código: " . $resultadoDetalle->getCode() . ")";
                } else {
                    $mensaje_detalles .= $resultadoDetalle;
                }
                $mensaje_detalles .= "\n";
            }
        }
        
        if ($exito_detalles) {
            $mensaje_detalles = "Todos los detalles fueron guardados correctamente";
        }
    } else {
        $mensaje_db = "Error al guardar el documento en la base de datos";
        $mensaje_detalles = "";
    }
     
    // Enviar al SII - Comentado para pruebas
    $trackID = $EnvioBoleta->enviar();
    
    if ($trackID === false) {
        throw new Exception('Error al enviar la boleta exenta al SII');
    }

    // Incrementar el folio actual en la base de datos
    $nuevoFolioActual = $folioActual + 1;
    if (!$Folio->updFolio($rutEmisor, $TipoDTE, $nuevoFolioActual)) {
        throw new Exception('Error al actualizar el folio en la base de datos');
    }
     // Respuesta exitosa
     echo json_encode([
        'success' => true,
        'message' => 'Boleta exenta generada exitosamente',
        'folio' => $folioActual,
        'archivo_xml' => $archivoXML,
        'archivo_xml_copia' => $archivoXMLCopia,
        'archivo_pdf' => $archivoPDF,
        'email' => $mensaje_email,
        'base_datos' => $mensaje_db,
        'detalles' => $mensaje_detalles,
        'folio_update' => $mensaje_folio
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
   
/**
 * Genera el PDF de la boleta exenta en formato papel 80mm
 */
function generarPDFBoletaExenta($archivo_xml, $carpetaPDF, $nombreArchivo) {
    // Cargamos el archivo XML
    $xml = simplexml_load_file($archivo_xml);
    
    // Extraer el TED (Timbre Electrónico de DTE)
    $ted = '';
    if (isset($xml->SetDTE->DTE->Documento->TED)) {
        $ted = $xml->SetDTE->DTE->Documento->TED->asXML();
    }
    
    // clase para el PDF
    class BoletaExentaPDF extends TCPDF {
        protected $empresaLogo;
        
        public function __construct($logo = null) {
            // Configurar para papel continuo (80mm)
            parent::__construct('P', 'mm', array(80, 200), true, 'UTF-8', false);
            $this->SetMargins(4, 4, 4);
            $this->setPrintHeader(false);
            $this->setPrintFooter(false);
            $this->empresaLogo = $logo;
        }
        
        public function formatRut($rut) {
            $rut = preg_replace('/[^0-9kK]/', '', $rut);
            $dv = substr($rut, -1);
            $number = substr($rut, 0, -1);
            $number = number_format($number, 0, '', '.');
            return $number . '-' . strtoupper($dv);
        }
    }
    
    // Extraer datos del XML
    $emisor = $xml->SetDTE->DTE->Documento->Encabezado->Emisor;
    $receptor = $xml->SetDTE->DTE->Documento->Encabezado->Receptor;
    $idDoc = $xml->SetDTE->DTE->Documento->Encabezado->IdDoc;
    $totales = $xml->SetDTE->DTE->Documento->Encabezado->Totales;
    
    // Información básica
    $rutEmpresa = (string) $emisor->RUTEmisor;
    $folio = (string) $idDoc->Folio;
    $nombreEmpresa = isset($emisor->RznSoc) ? (string) $emisor->RznSoc : (string) $emisor->RznSocEmisor;
    $giro = isset($emisor->GiroEmis) ? (string) $emisor->GiroEmis : (string) $emisor->GiroEmisor;
    $direccion = (string) $emisor->DirOrigen;
    $comuna = (string) $emisor->CmnaOrigen;
    $ciudad = (string) $emisor->CiudadOrigen;
    
    $fono = '';
    if (isset($emisor->Telefono)) {
        $fono = (string) $emisor->Telefono;
    }
    $fecha = (string) $idDoc->FchEmis;
    $formaPagoCodigo = isset($idDoc->FmaPago) ? (string) $idDoc->FmaPago : '1';
    
    // Información del cliente
    $rutCliente = (string) $receptor->RUTRecep;
    $cliente = (string) $receptor->RznSocRecep;
    
    // Totales para boleta exenta
    $exento = isset($totales->MntExe) ? (float) $totales->MntExe : 0;
    $total = isset($totales->MntTotal) ? (float) $totales->MntTotal : $exento;
    
    // Fecha formateada
    $fechaFormateada = date('d-m-Y', strtotime($fecha));

    // Crear el objeto PDF
    $logo = '';
    $pdf = new BoletaExentaPDF($logo);
    $pdf->AddPage();
    
    // Formatear RUT
    $rutEmpresaFormateado = $pdf->formatRut($rutEmpresa);
    
    // ENCABEZADO
    $pdf->Image($logo, -5, 6, 40);
    $pdf->SetXY(30, 3);
    $pdf->SetFont('Helvetica', 'B', 9.5);
    $pdf->Cell(46, 20, '', 1, 2, 'C'); // Cuadro
    $pdf->SetXY(30, 4);
    $pdf->Cell(46, 0, 'RUT: ' . $rutEmpresaFormateado, 0, 2, 'C');
    // Reducir el tamaño de la fuente para que quepa "BOLETA EXENTA ELECTRÓNICA"
    $pdf->SetFont('Helvetica', 'B', 7.5); 
    $pdf->Cell(46, 10, 'BOLETA EXENTA ELECTRÓNICA', 0, 2, 'C');
    $pdf->SetFont('Helvetica', 'B', 9.5); // Restaurar tamaño original
    $pdf->Cell(46, 0, 'N° ' . $folio, 0, 2, 'C');
    $pdf->SetXY(30, 24);
    $pdf->Cell(46, 0, 'S.I.I. - PROVIDENCIA', 0, 2, 'C');
    $pdf->Ln(2);
    
    // Giro Comercial
    $pdf->SetFont('Helvetica', 'B', 9.5);
    $pdf->MultiCell(70, 5, $giro, 0, 'C');
    $pdf->Ln(2);
    
    // Razón Social
    $pdf->SetFont('Helvetica', '', 9.5);
    $pdf->Cell(0, 5, $nombreEmpresa, 0, 2, 'C');
    $pdf->Ln(3);
    
    // DATOS EMPRESA
    $pdf->SetFont('Helvetica', '', 9.5);
    $altura = 0;
    $ancho = 25;
    $anchoDato = 50;
    
    $pdf->Cell($ancho, $altura, 'Dirección: ', 0, 0, 'L');
    $pdf->MultiCell($anchoDato, $altura, $direccion, 0, 'L');
    $pdf->Ln(1);
    $pdf->SetX(4);
    $pdf->Cell($ancho, $altura, 'Comuna: ', 0, 0, 'L');
    $pdf->MultiCell($anchoDato, $altura, $comuna, 0, 'L');
    $pdf->Ln(1);
    $pdf->SetX(4);
    $pdf->Cell($ancho, $altura, 'Ciudad: ', 0, 0, 'L');
    $pdf->MultiCell($anchoDato, $altura, $ciudad, 0, 'L');
    $pdf->Ln(1);
    $pdf->SetX(4);
    $pdf->Cell($ancho, $altura, 'Fono: ', 0, 0, 'L');
    $pdf->MultiCell($anchoDato, $altura, $fono, 0, 'L');
    $pdf->Ln(1);
    $pdf->SetX(4);
    $pdf->Cell($ancho, $altura, 'Fecha: ', 0, 0, 'L');
    $pdf->MultiCell($anchoDato, $altura, $fechaFormateada, 0, 'L');
    $pdf->Ln(1);
    
    // Forma de pago
    $formasPago = [
        '1' => 'Contado',
        '2' => 'Crédito',
        '3' => 'Sin Costo'
    ];
    $formaPagoTexto = isset($formasPago[$formaPagoCodigo]) ? $formasPago[$formaPagoCodigo] : $formaPagoCodigo;
    
    $pdf->SetX(4);
    $pdf->Cell($ancho, $altura, 'Forma de Pago: ', 0, 0, 'L');
    $pdf->MultiCell($anchoDato, $altura, $formaPagoTexto, 0, 'L');
    $pdf->Ln(1);
    $pdf->SetX(4);
    $pdf->Cell($ancho, $altura, 'Sucursal: ', 0, 0, 'L');
    $pdf->MultiCell($anchoDato, $altura, '', 0, 'L');
    $pdf->Ln(1);
    
    // Línea separadora
    $pdf->Ln(2);
    $pdf->Cell(0, 0, '', 'T', 1, 'C');
    
    // ENCABEZADO DETALLE
    $pdf->SetFont('Helvetica', 'B', 7);
    $pdf->Cell(8, 4, 'UND', 0, 0, 'C');
    $pdf->Cell(18, 4, 'ÍTEM', 0, 0, 'C');
    $pdf->Cell(16, 4, 'V.UNI', 0, 0, 'C');
    $pdf->Cell(18, 4, 'DESC.', 0, 0, 'C');
    $pdf->Cell(14, 4, 'SUBTOTAL', 0, 1, 'C');
    
    // DETALLE ITEMS
    $pdf->SetFont('Helvetica', '', 8.5);
    
    foreach ($xml->SetDTE->DTE->Documento->Detalle as $item) {
        $cantidad = (int) $item->QtyItem;
        $descripcion = (string) $item->NmbItem;
        
        $valorUnitario = fmod((float) $item->PrcItem, 1) == 0 
            ? number_format((float) $item->PrcItem, 0, ',', '.') 
            : number_format((float) $item->PrcItem, 2, ',', '.');
    
        $descuento = isset($item->DescuentoMonto) 
            ? (fmod((float) $item->DescuentoMonto, 1) == 0 
                ? number_format((float) $item->DescuentoMonto, 0, ',', '.') 
                : number_format((float) $item->DescuentoMonto, 2, ',', '.')) 
            : '-';
    
        $subtotal = fmod((float) $item->MontoItem, 1) == 0 
            ? number_format((float) $item->MontoItem, 0, ',', '.') 
            : number_format((float) $item->MontoItem, 2, ',', '.');
    
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
        $pdf->Cell(8, 4, $cantidad, 0, 0, 'C');
    
        $xDesc = $pdf->GetX();
        $yDesc = $pdf->GetY();
        $pdf->MultiCell(18, 4, $descripcion, 0, 'C');
    
        $yAfterDesc = $pdf->GetY();
        $pdf->SetXY($xDesc + 17, $startY);
        $rowHeight = max(4, $yAfterDesc - $startY);
        $pdf->Cell(16, $rowHeight, '$' . $valorUnitario, 0, 0, 'C');
        $pdf->Cell(18, $rowHeight, ($descuento == '-') ? '-' : '$' . $descuento, 0, 0, 'C');
        $pdf->Cell(14, $rowHeight, '$' . $subtotal, 0, 1, 'C');
    
        $pdf->SetY($yAfterDesc);
    }
    
    // Línea inferior
    $pdf->Ln(2);
    $pdf->Cell(0, 0, '', 'T', 1, 'C');
    
    // TOTALES PARA BOLETA EXENTA
    $pdf->SetFont('Helvetica', 'B', 9);
    $ancho = 70;
    $alto = 4 * 5; // Menos filas que la boleta normal
    $margenDerecho = 10;
    $x = $pdf->GetPageWidth() - $ancho - $margenDerecho;
    $y = $pdf->GetY();

    $pdf->Rect(30, $y, 45, $alto);
    
    $pdf->SetX($x);
    $pdf->Cell(55, 6, 'Total Exento:', 0, 0, 'R');
    $pdf->Cell(20, 6, '$' . (fmod($exento, 1) == 0 ? number_format($exento, 0, ',', '.') : number_format($exento, 2, ',', '.')), 0, 1, 'R');
    
    $pdf->SetX($x);
    $pdf->Cell(55, 6, 'Vuelto:', 0, 0, 'R');
    $pdf->Cell(20, 6, '$', 0, 1, 'R');
    
    $pdf->SetX($x);
    $pdf->Cell(55, 6, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(20, 6, '$' . (fmod($total, 1) == 0 ? number_format($total, 0, ',', '.') : number_format($total, 2, ',', '.')), 0, 1, 'R');
    
    $yAfterTotals = $pdf->GetY() + 8;
    
    // TIMBRE ELECTRÓNICO (CÓDIGO DE BARRAS)
    if (!empty($ted)) {
        $barcodeWidth = 70;
        $barcodeHeight = 25;
        
        // Posición
        $xBarcode = ($pdf->GetPageWidth() - $barcodeWidth) / 2;
        
        // Estilo
        $style = array(
            'border' => false,
            'padding' => 2,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'module_width' => 4.2,
            'module_height' => 2.5
        );
        
        $pdf->SetAutoPageBreak(false, 0);
        
        // Imprimir código
        $pdf->write2DBarcode($ted, 'PDF417', $xBarcode, $yAfterTotals, $barcodeWidth, $barcodeHeight, $style, 'N');
        
        $pdf->SetXY($xBarcode, $yAfterTotals + $barcodeHeight);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->Cell($barcodeWidth, 5, 'Timbre Electrónico S.I.I.', 0, 1, 'C');
        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->Cell(0, 5, 'Verifique Documentos en: www.sii.cl', 0, 0, 'C');
    }
    
    // Guardar PDF
    $archivoPDF = $carpetaPDF . $nombreArchivo . '.pdf';
    $pdf->Output($archivoPDF, 'FD');
    
    return $archivoPDF;
}