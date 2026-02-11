<?php
require_once '../../../../config.php';

class ProcesadorSetPruebas {
    private $xmlOriginal;
    private $documentos = [];
    private $carpetaSalida;
    private $logo;

    public function __construct($rutaXmlSet) {
        $this->xmlOriginal = simplexml_load_file($rutaXmlSet);
        $this->carpetaSalida = __ROOT__ . '/archives/documentos_individuales/';
        
        // Obtener logo de la empresa
        $Empresa = new Empresa();
        $datos = $Empresa->listaEmpresa();
        $this->logo = BASEURL.$datos['logo'];
        
        if (!file_exists($this->carpetaSalida)) {
            mkdir($this->carpetaSalida, 0777, true);
        }
    }
    public function calcularSubTotal($detalles) {
        $subtotal = 0;
        foreach ($detalles as $detalle) {
            $subtotal += (float)$detalle->MontoItem;
        }
        return (string)$subtotal;
    }


    public function generarVisualizacionSetPruebas() {
        try {
            foreach ($this->xmlOriginal->SetDTE->DTE as $dte) {
                // Crear el documento base
                $dom = new DOMDocument('1.0', 'ISO-8859-1');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                
                // Crear elemento raíz con namespaces
                $envioDTE = $dom->createElementNS('http://www.sii.cl/SiiDte', 'EnvioDTE');
                $envioDTE->setAttribute('version', '1.0');
                $envioDTE->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $envioDTE->setAttribute('xsi:schemaLocation', 'http://www.sii.cl/SiiDte EnvioDTE_v10.xsd');
                $dom->appendChild($envioDTE);

                // Crear SetDTE
                $setDTE = $dom->createElement('SetDTE');
                $setDTE->setAttribute('ID', 'DTECHILE');
                $envioDTE->appendChild($setDTE);

                // Crear Caratula
                $this->crearCaratula($dom, $setDTE, $dte);

                // Crear el DTE 
                $dteNodo = $dom->createElement('DTE');
                $dteNodo->setAttribute('version', '1.0');
                $setDTE->appendChild($dteNodo);

                // Importar el contenido del DTE original
                $documentoOriginal = dom_import_simplexml($dte);
                foreach ($documentoOriginal->childNodes as $child) {
                    if ($child->nodeName != 'DTE') {
                        $importedNode = $dom->importNode($child, true);
                        $dteNodo->appendChild($importedNode);
                    }
                }

                // Copiar firma
                $firmaOriginal = $this->xmlOriginal->Signature;
                if ($firmaOriginal) {
                    $signatureNode = $dom->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
                    
                    // SignedInfo
                    $signedInfo = $dom->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'SignedInfo');
                    
                    // CanonicalizationMethod
                    $canonMethod = $dom->createElement('CanonicalizationMethod');
                    $canonMethod->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
                    $signedInfo->appendChild($canonMethod);
                    
                    // SignatureMethod
                    $sigMethod = $dom->createElement('SignatureMethod');
                    $sigMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
                    $signedInfo->appendChild($sigMethod);
                    
                    // Reference
                    $reference = $dom->createElement('Reference');
                    $reference->setAttribute('URI', '#DTECHILE');
                    
                    // Transforms
                    $transforms = $dom->createElement('Transforms');
                    $transform = $dom->createElement('Transform');
                    $transform->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
                    $transforms->appendChild($transform);
                    $reference->appendChild($transforms);
                    
                    // DigestMethod
                    $digestMethod = $dom->createElement('DigestMethod');
                    $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
                    $reference->appendChild($digestMethod);
                    
                    // DigestValue
                    $digestValue = $dom->createElement('DigestValue', (string)$firmaOriginal->SignedInfo->Reference->DigestValue);
                    $reference->appendChild($digestValue);
                    
                    $signedInfo->appendChild($reference);
                    $signatureNode->appendChild($signedInfo);
                    
                    // SignatureValue
                    $sigValue = $dom->createElement('SignatureValue', (string)$firmaOriginal->SignatureValue);
                    $signatureNode->appendChild($sigValue);
                    
                    // KeyInfo
                    $keyInfo = $dom->importNode(dom_import_simplexml($firmaOriginal->KeyInfo), true);
                    $signatureNode->appendChild($keyInfo);
                    
                    $envioDTE->appendChild($signatureNode);
                }

                // Obtener información para el nombre del archivo
                $tipoDTE = (string)$dte->Documento->Encabezado->IdDoc->TipoDTE;
                $folio = (string)$dte->Documento->Encabezado->IdDoc->Folio;
                
                // Generar nombre y guardar archivo
                $nombreArchivo = sprintf('DTE_%s_F%s.xml', $tipoDTE, $folio);
                $rutaCompleta = $this->carpetaSalida . $nombreArchivo;
                $dom->save($rutaCompleta);
                
                // Preparar datos para DTE
                $datosDocumento = [
                    'Encabezado' => [
                        'IdDoc' => [
                            'TipoDTE' => $tipoDTE,
                            'Folio' => $folio,
                            'FchEmis' => (string)$dte->Documento->Encabezado->IdDoc->FchEmis,
                            'IndTraslado' => (string)$dte->Documento->Encabezado->IdDoc->IndTraslado,
                            'FmaPago' => (string)$dte->Documento->Encabezado->IdDoc->FmaPago,
                        ],
                        'Emisor' => [
                            'RUTEmisor' => (string)$dte->Documento->Encabezado->Emisor->RUTEmisor,
                            'RznSoc' => (string)$dte->Documento->Encabezado->Emisor->RznSoc,
                            'GiroEmis' => (string)$dte->Documento->Encabezado->Emisor->GiroEmis,
                            'Acteco' => (string)$dte->Documento->Encabezado->Emisor->Acteco,
                            'DirOrigen' => (string)$dte->Documento->Encabezado->Emisor->DirOrigen,
                            'CmnaOrigen' => (string)$dte->Documento->Encabezado->Emisor->CmnaOrigen,
                        ],
                        'Receptor' => [
                            'RUTRecep' => (string)$dte->Documento->Encabezado->Receptor->RUTRecep,
                            'RznSocRecep' => (string)$dte->Documento->Encabezado->Receptor->RznSocRecep,
                            'GiroRecep' => (string)$dte->Documento->Encabezado->Receptor->GiroRecep,
                            'DirRecep' => (string)$dte->Documento->Encabezado->Receptor->DirRecep,
                            'CmnaRecep' => (string)$dte->Documento->Encabezado->Receptor->CmnaRecep,
                        ],
                        'Totales' => [
                        'MntNeto' => (string)$dte->Documento->Encabezado->Totales->MntNeto,
                        'TasaIVA' => (string)$dte->Documento->Encabezado->Totales->TasaIVA,
                        'IVA' => (string)$dte->Documento->Encabezado->Totales->IVA,
                        'MntTotal' => (string)$dte->Documento->Encabezado->Totales->MntTotal,
                        'MntExe' => isset($dte->Documento->Encabezado->Totales->MntExe) ? 
                            (string)$dte->Documento->Encabezado->Totales->MntExe : false,
                        'SubTotal' => $this->calcularSubTotal($dte->Documento->Detalle), // Agregamos el subtotal
                    ],
 
                    ],
                    'Detalle' => []
                ];

                // Procesar detalles
                foreach ($dte->Documento->Detalle as $detalle) {
                    $itemDetalle = [
                        'NroLinDet' => (string)$detalle->NroLinDet,
                        'NmbItem' => (string)$detalle->NmbItem,
                        'DscItem' => isset($detalle->DscItem) ? (string)$detalle->DscItem : false,
                        'QtyItem' => (string)$detalle->QtyItem,
                        'UnmdItem' => isset($detalle->UnmdItem) ? (string)$detalle->UnmdItem : false,
                        'PrcItem' => (string)$detalle->PrcItem,
                        'MontoItem' => (string)$detalle->MontoItem,
                        // Agregar campos de descuento
                        'DescuentoPct' => isset($detalle->DescuentoPct) ? (string)$detalle->DescuentoPct : false,
                        'DescuentoMonto' => isset($detalle->DescuentoMonto) ? (string)$detalle->DescuentoMonto : false
                    ];
                
                    if (isset($detalle->CdgItem)) {
                        $itemDetalle['CdgItem'] = [
                            'TpoCodigo' => 'INT1',
                            'VlrCodigo' => (string)$detalle->CdgItem->VlrCodigo
                        ];
                    }
                
                    $datosDocumento['Detalle'][] = $itemDetalle;
                }
                
                // Procesar descuentos/recargos globales si existen
                if (isset($dte->Documento->DscRcgGlobal)) {
                    $datosDocumento['DscRcgGlobal'] = [];
                    foreach ($dte->Documento->DscRcgGlobal as $dscRcg) {
                        $datosDocumento['DscRcgGlobal'][] = [
                            'NroLinDR' => (string)$dscRcg->NroLinDR,
                            'TpoMov' => (string)$dscRcg->TpoMov,
                            'GlosaDR' => (string)$dscRcg->GlosaDR,
                            'TpoValor' => (string)$dscRcg->TpoValor,
                            'ValorDR' => (string)$dscRcg->ValorDR
                        ];
                    }
                }
                $ted = '';
            if ($dte->Documento->TED) {
                $tedDom = new DOMDocument();
                $tedDom->loadXML($dte->Documento->TED->asXML());
                $tedDom->documentElement->removeAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi');
                $tedDom->documentElement->removeAttributeNS('http://www.sii.cl/SiiDte', '');
                $ted = $tedDom->saveXML($tedDom->documentElement);
                
                // Asegurar codificación correcta
                $ted = mb_detect_encoding($ted, ['UTF-8', 'ISO-8859-1']) != 'ISO-8859-1' ? 
                    utf8_decode($ted) : $ted;
            }

                // Generar PDFs
                $Directorio = new Directorio();
                $carpetaPDF = $Directorio->creaDirectorio(__ROOT__.'/archives/pdf/'.$tipoDTE);
                
                // Crear instancia DTE
                $DTE = new Dte($datosDocumento);

                // Configurar PDF
                $pdf = new PDFdte(false);
                $pdf->setLogo($this->logo);
                $pdf->setResolucion([
                    'FchResol' => (string)$this->xmlOriginal->SetDTE->Caratula->FchResol,
                    'NroResol' => (string)$this->xmlOriginal->SetDTE->Caratula->NroResol
                ]);

                

                // Generar PDF cedible si se requiere
                if (isset($_POST['cedible']) && $_POST['cedible'] === 'true') {
                    $pdf = new PDFdte(false);
                    $pdf->setLogo($this->logo);
                    $pdf->setResolucion([
                        'FchResol' => (string)$this->xmlOriginal->SetDTE->Caratula->FchResol,
                        'NroResol' => (string)$this->xmlOriginal->SetDTE->Caratula->NroResol
                    ]);
                    $pdf->setCedible(true);
                    $pdf->agregar($DTE->getDatos(), $ted);
                    $pdf->Output($carpetaPDF.'D0C'.$DTE->getID().'Cedible.pdf', 'FD');
                }else{
                    // Generar PDF normal
                    $pdf->setCedible(false);
                    $pdf->agregar($DTE->getDatos(), $ted);
                    $pdf->Output($carpetaPDF.'D0C'.$DTE->getID().'.pdf', 'FD');
                }

                $this->documentos[] = [
                    'tipo' => $tipoDTE,
                    'folio' => $folio,
                    'archivo' => $nombreArchivo
                ];
            }

            return $this->documentos;
        } catch (Exception $e) {
            throw new Exception("Error al procesar el set de pruebas: " . $e->getMessage());
        }
    }

    private function crearCaratula($dom, $setDTE, $dte) {
        $caratula = $dom->createElement('Caratula');
        $caratula->setAttribute('version', '1.0');
        
        $caratulaOriginal = $this->xmlOriginal->SetDTE->Caratula;
        
        $elementosCaratula = [
            'RutEmisor', 'RutEnvia', 'RutReceptor', 
            'FchResol', 'NroResol', 'TmstFirmaEnv'
        ];

        foreach ($elementosCaratula as $elemento) {
            if (isset($caratulaOriginal->$elemento)) {
                $valor = (string)$caratulaOriginal->$elemento;
                $caratula->appendChild($dom->createElement($elemento, $valor));
            }
        }

        // Agregar SubTotDTE
        $subTotDTE = $dom->createElement('SubTotDTE');
        $subTotDTE->appendChild($dom->createElement('TpoDTE', 
            (string)$dte->Documento->Encabezado->IdDoc->TipoDTE));
        $subTotDTE->appendChild($dom->createElement('NroDTE', '1'));
        $caratula->appendChild($subTotDTE);

        $setDTE->appendChild($caratula);
    }
}

// Procesar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmlFile'])) {
    try {
        // Validar archivo
        if ($_FILES['xmlFile']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error en la subida del archivo.');
        }

        // Verificar tipo de archivo
        $tipoArchivo = mime_content_type($_FILES['xmlFile']['tmp_name']);
        $extensionArchivo = strtolower(pathinfo($_FILES['xmlFile']['name'], PATHINFO_EXTENSION));
        
        if ($tipoArchivo !== 'text/xml' && $extensionArchivo !== 'xml') {
            throw new Exception('El archivo debe ser XML.');
        }

        // Procesar el set
        $procesador = new ProcesadorSetPruebas($_FILES['xmlFile']['tmp_name']);
        $resultados = $procesador->generarVisualizacionSetPruebas();
        
        $mensajeExito = "Documentos procesados exitosamente:<br>";
        foreach ($resultados as $doc) {
            $mensajeExito .= "DTE Tipo {$doc['tipo']} Folio {$doc['folio']}: {$doc['archivo']}<br>";
        }
    } catch (Exception $e) {
        $mensajeError = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesar Set de Pruebas</title>
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
                        <li><a href="<?php echo BASEURL ?>">Inicio</a></li>
                        <li class="active">Procesar Set de Pruebas</li>
                    </ol>
                </div>
                <div class="section-body">
                    <div class="col-lg-offset-2 col-md-8 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Separar Set de Pruebas en XMLs individuales</h3>
                            </div>
                            <div class="card-body">
                                <?php if (isset($mensajeExito)): ?>
                                    <div class="alert alert-success">
                                        <?php echo $mensajeExito; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($mensajeError)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo $mensajeError; ?>
                                    </div>
                                <?php endif; ?>

                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="xmlFile">Seleccionar XML del Set de Pruebas</label>
                                        <input type="file" class="form-control" id="xmlFile" name="xmlFile" accept=".xml" required>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="cedible" value="true"> Generar versión cedible
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Procesar Set de Pruebas
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
</body>
</html>