<?php
require_once('../../../../core/tcpdf/tcpdf.php');
require_once('../../../../core/PDF.php');

define('__ROOT__', dirname(dirname(dirname(__FILE__))));

$archivo_xml = realpath(__DIR__ . '/../../../../archives/xml/39/D0C3967.xml');
if (!$archivo_xml || !file_exists($archivo_xml)) {
    die(json_encode(['success' => false, 'message' => 'Error: Archivo XML no encontrado']));
}

$xml = simplexml_load_file($archivo_xml);

// Tomar datos del XML y guardarlos en variables
$emisor = $xml->SetDTE->DTE->Documento->Encabezado->Emisor;
$receptor = $xml->SetDTE->DTE->Documento->Encabezado->Receptor;
$idDoc = $xml->SetDTE->DTE->Documento->Encabezado->IdDoc;
$totales = $xml->SetDTE->DTE->Documento->Encabezado->Totales;
$rutEmpresa = (string) $emisor->RUTEmisor;
$folio = (string) $idDoc->Folio;
$giro = utf8_decode((string) $emisor->GiroEmis);
$fono = (string) $emisor->Telefono;
$nombreEmpresa = (string) $emisor->RznSoc;
$direccion = (string)$emisor->DirOrigen;
$comuna = (string)$emisor->CmnaOrigen;
$ciudad = (string)$emisor->CiudadOrigen;
$fecha = (string) $idDoc->FchEmis;
$formaPagoCodigo = (string)$idDoc->FmaPago;
$cliente = (string) $receptor->RznSocRecep;
$rutCliente = (string) $receptor->RUTRecep;
$total = (float) $totales->MntNeto;
$iva = (float) $totales->IVA;
$totalFinal = $total + $iva;

// Extraer el código TED (para generar el codigo PDF)
$ted = $xml->SetDTE->DTE->Documento->TED->asXML();

class CustomPDF extends TCPDF
{
    public function __construct()
    {
        parent::__construct('P', 'mm', array(80, 200), true, 'UTF-8', false); // Tamaño del papel
        $this->SetMargins(4, 4, 4, 4); // Margenes 
        $this->setPrintHeader(false); // Quitar linea superior
    }

    function formatRut($rut)
    {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        $number = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        $number = number_format($number, 0, '', '.');

        return $number . '-' . strtoupper($dv);
    }
}

$pdf = new CustomPDF();
$pdf->AddPage();

// Formatear RUT de la empresa
$rutEmpresaFormateado = $pdf->formatRut($rutEmpresa);

// Invertir formato de fecha
$fechaFormateada = date('d-m-Y', strtotime($fecha));

// Encabezado
$pdf->Image('', -5, 6, 40);
$pdf->SetXY(30, 3);
$pdf->SetFont('Helvetica', 'B', 9.5);
$pdf->Cell(46, 20, '', 1, 2, 'C'); // Cuadro
$pdf->SetXY(30, 4);
$pdf->Cell(46, 0, 'RUT: ' . $rutEmpresaFormateado, 0, 2, 'C');
$pdf->Cell(46, 10, 'BOLETA ELECTRÓNICA', 0, 2, 'C');
$pdf->Cell(46, 0, 'N° ' . $folio, 0, 2, 'C');
$pdf->SetXY(30, 24);
$pdf->Cell(46, 0, 'S.I.I. - PROVIDENCIA', 0, 2, 'C');
$pdf->Ln(2);

// Problema de los simbolos (No funciono)
$pdf->SetFont('Helvetica', 'B', 9.5);
$pdf->MultiCell(70, 5, utf8_decode($giro), 0, 'C');
$pdf->Ln(2);

// Razon Social
$pdf->SetFont('Helvetica', '', 9.5);
$pdf->Cell(0, 5, $nombreEmpresa, 0, 2, 'C');
$pdf->Ln(3); // Espaciado para separar el encabezado del contenido

// Datos
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
/*$pdf->Ln(1);
$pdf->SetX(4);
$pdf->Cell($ancho, $altura, 'Vendedor: ', 0, 0, 'L');
$pdf->MultiCell($anchoDato, $altura, '', 0, 'L');*/
$pdf->Ln(1);
$pdf->SetX(4);
$pdf->Cell($ancho, $altura, 'Fecha: ', 0, 0, 'L');
$pdf->MultiCell($anchoDato, $altura, $fechaFormateada, 0, 'L');
$pdf->Ln(1);
$pdf->SetX(4);
$pdf->Cell($ancho, $altura, 'Forma de Pago: ', 0, 0, 'L');
$pdf->MultiCell($anchoDato, $altura, $formaPagoCodigo, 0, 'L');
$pdf->Ln(1);
$pdf->SetX(4);
$pdf->Cell($ancho, $altura, 'Sucursal: ', 0, 0, 'L');
$pdf->MultiCell($anchoDato, $altura, '', 0, 'L');
$pdf->Ln(1);

// Linea Superior
$pdf->Ln(2);
$pdf->Cell(0, 0, '', 'T', 1, 'C');

// Datos
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(8, 4, 'UND', 0, 0, 'C');
$pdf->Cell(18, 4, 'ÍTEM', 0, 0, 'C');
$pdf->Cell(16, 4, 'V.UNI', 0, 0, 'C');
$pdf->Cell(18, 4, 'DESC.', 0, 0, 'C');
$pdf->Cell(14, 4, 'SUBTOTAL', 0, 1, 'C');

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
    $rowHeight = $yAfterDesc - $startY;
    $pdf->Cell(16, $rowHeight, '$' . $valorUnitario, 0, 0, 'C');
    $pdf->Cell(18, $rowHeight, ($descuento == '-') ? '-' : '$' . $descuento, 0, 0, 'C');
    $pdf->Cell(14, $rowHeight, '$' . $subtotal, 0, 1, 'C');

    $pdf->SetY($yAfterDesc);
}

// Línea inferior
$pdf->Ln(2);
$pdf->Cell(0, 0, '', 'T', 1, 'C');

$pdf->SetFont('Helvetica', 'B', 9);
$ancho = 70;
$alto = 6 * 5;
$margenDerecho = 10;
$x = $pdf->GetPageWidth() - $ancho - $margenDerecho;
$y = $pdf->GetY();

$pdf->Rect(30, $y, 45, $alto);

$pdf->SetX($x);
$pdf->Cell(55, 6, 'NETO:', 0, 0, 'R');
$pdf->Cell(20, 6, '$' . (fmod($total, 1) == 0 ? number_format($total, 0, ',', '.') : number_format($total, 2, ',', '.')), 0, 1, 'R');

$pdf->SetX($x);
$pdf->Cell(55, 6, 'IVA:', 0, 0, 'R');
$pdf->Cell(20, 6, '$' . (fmod($iva, 1) == 0 ? number_format($iva, 0, ',', '.') : number_format($iva, 2, ',', '.')), 0, 1, 'R');

$pdf->SetX($x);
$pdf->Cell(55, 6, 'Total Exento:', 0, 0, 'R');
$pdf->Cell(20, 6, '$' . (fmod($totalFinal, 1) == 0 ? number_format($totalFinal, 0, ',', '.') : number_format($totalFinal, 2, ',', '.')), 0, 1, 'R');

$pdf->SetX($x);
$pdf->Cell(55, 6, 'Vuelto:', 0, 0, 'R');
$pdf->Cell(20, 6, '$', 0, 1, 'R');

$pdf->SetX($x);
$pdf->Cell(55, 6, 'TOTAL:', 0, 0, 'R');
$pdf->Cell(20, 6, '$' . (fmod($totalFinal, 1) == 0 ? number_format($totalFinal, 0, ',', '.') : number_format($totalFinal, 2, ',', '.')), 0, 1, 'R');


$yAfterTotals = $pdf->GetY() + 2;

// Codigo
$barcodeWidth = 70;
$barcodeHeight = 25;

// Posicion
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

// Generar
$nombre_pdf = 'boleta_' . $folio . '.pdf';
$pdf->Output($nombre_pdf, 'I');