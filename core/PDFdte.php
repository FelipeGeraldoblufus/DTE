<?php
/**
 * Clase para generar el PDF de un (DTE)
 */
class PDFdte extends PDF
{

    private $dte; ///< Tipo de DTE que se está generando
    private $logo; ///< Datos del logo que se ubicará en el PDF (ruta, datos y/o posición)
    private $resolucion; ///< Arreglo con los datos de la resolución (índices: NroResol y FchResol)
    private $cedible = false; ///< Por defecto DTEs no son cedibles
    protected $papelContinuo = false; ///< Indica si se usa papel continuo o no
    private $sinAcuseRecibo = [39, 41, 56, 61, 110, 111, 112]; ///< Boletas, notas de crédito y notas de débito no tienen acuse de recibo
    private $web_verificacion = 'www.sii.cl'; ///< Página web para verificar el documento
    private $ecl = 5; ///< error correction level para PHP >= 7.0.0
    private $ybaja;

    private $tipos = [
        // códigos oficiales SII
        29 => 'FACTURA DE INICIO',
        30 => 'FACTURA',
        32 => 'FACTURA DE VENTA BIENES Y SERVICIOS NO AFECTOS O EXENTOS DE IVA',
        33 => 'FACTURA ELECTRÓNICA',
        34 => 'FACTURA NO AFECTA O EXENTA ELECTRÓNICA',
        35 => 'BOLETA',
        38 => 'BOLETA EXENTA',
        39 => 'BOLETA ELECTRÓNICA',
        40 => 'LIQUIDACION FACTURA',
        41 => 'BOLETA NO AFECTA O EXENTA ELECTRÓNICA',
        43 => 'LIQUIDACIÓN FACTURA ELECTRÓNICA',
        45 => 'FACTURA DE COMPRA',
        46 => 'FACTURA DE COMPRA ELECTRÓNICA',
        48 => 'COMPROBANTE DE PAGO ELECTRÓNICO',
        50 => 'GUÍA DE DESPACHO',
        52 => 'GUÍA DE DESPACHO ELECTRÓNICA',
        55 => 'NOTA DE DÉBITO',
        56 => 'NOTA DE DÉBITO ELECTRÓNICA',
        60 => 'NOTA DE CRÉDITO',
        61 => 'NOTA DE CRÉDITO ELECTRÓNICA',
        101 => 'FACTURA DE EXPORTACIÓN',
        102 => 'FACTURA DE VENTA EXENTA A ZONA FRANCA PRIMARIA',
        103 => 'LIQUIDACIÓN',
        104 => 'NOTA DE DÉBITO DE EXPORTACIÓN',
        105 => 'BOLETA LIQUIDACIÓN',
        106 => 'NOTA DE CRÉDITO DE EXPORTACIÓN',
        108 => 'SOLICITUD REGISTRO DE FACTURA (SRF)',
        109 => 'FACTURA TURISTA',
        110 => 'FACTURA DE EXPORTACIÓN ELECTRÓNICA',
        111 => 'NOTA DE DÉBITO DE EXPORTACIÓN ELECTRÓNICA',
        112 => 'NOTA DE CRÉDITO DE EXPORTACIÓN ELECTRÓNICA',
        801 => 'ORDEN DE COMPRA',
        802 => 'NOTA DE PEDIDO',
        803 => 'CONTRATO',
        804 => 'RESOLUCIÓN',
        805 => 'PROCEDO CHILECOMPRA',
        806 => 'FICHA CHILECOMPRA',
        807 => 'DUS',
        808 => 'B/L (CONOCIMIENTO DE EMBARQUE)',
        809 => 'AWB',
        810 => 'MIC (MANIFIESTO INTERNACIONAL)',
        811 => 'CARTA DE PORTE',
        812 => 'RESOLUCION SNA',
        813 => 'PASAPORTE',
        814 => 'CERTIFICADO DE DEPÓSITO BOLSA PROD. CHILE',
        815 => 'VALE DE PRENDA BOLSA PROD. CHILE',
        901 => 'FACTURA DE VENTAS A EMPRESAS DEL TERRITORIO PREFERENCIAL',
        902 => 'CONOCIMIENTO DE EMBARQUE',
        903 => 'DOCUMENTO ÚNICO DE SALIDA (DUS)',
        904 => 'FACTURA DE TRASPASO',
        905 => 'FACTURA DE REEXPEDICIÓN',
        906 => 'BOLETAS VENTA MÓDULOS ZF (TODAS)',
        907 => 'FACTURAS VENTA MÓDULO ZF (TODAS)',
        909 => 'FACTURAS VENTA MÓDULO ZF',
        910 => 'SOLICITUD TRASLADO ZONA FRANCA (Z)',
        911 => 'DECLARACIÓN DE INGRESO A ZONA FRANCA PRIMARIA',
        914 => 'DECLARACIÓN DE INGRESO (DIN)',
        919 => 'RESUMEN VENTAS DE NACIONALES PASAJES SIN FACTURA',
        920 => 'OTROS REGISTROS NO DOCUMENTADOS (AUMENTA DÉBITO)',
        922 => 'OTROS REGISTROS (DISMINUYE DÉBITO)',
        924 => 'RESUMEN VENTAS DE INTERNACIONALES PASAJES SIN FACTURA',

        0 => 'COTIZACIÓN',
        'HES' => 'HOJA DE ENTRADA DE SERVICIOS (HES)',
    ]; ///< Glosas para los tipos de documentos (DTE y otros)

    private $formas_pago = [
        1 => 'Contado',
        2 => 'Crédito',
        3 => 'Sin costo',
    ]; ///< Glosas de las formas de pago

    private $formas_pago_exportacion = [
        1 => 'Cobranza hasta 1 año',
        2 => 'Cobranza más de 1 año',
        11 => 'Acreditivo hasta 1 año',
        12 => 'Acreditivo más de 1 año',
        21 => 'Sin pago',
        32 => 'Pago anticipado a la fecha de embarque',
    ]; ///< Códigos de forma de pago (básicos) de la aduana para exportaciones

    private $detalle_cols = [
        'CdgItem' => ['title'=>'Código', 'align'=>'left', 'width'=>20],
        'NmbItem' => ['title'=>'Item', 'align'=>'left', 'width'=>0],
        'IndExe' => ['title'=>'IE', 'align'=>'left', 'width'=>'7'],
        'QtyItem' => ['title'=>'Cant.', 'align'=>'right', 'width'=>15],
        'UnmdItem' => ['title'=>'Unidad', 'align'=>'left', 'width'=>22],
        'PrcItem' => ['title'=>'P. Unitario', 'align'=>'right', 'width'=>22],
        'DescuentoMonto' => ['title'=>'Descuento', 'align'=>'right', 'width'=>22],
        'RecargoMonto' => ['title'=>'Recargo', 'align'=>'right', 'width'=>22],
        'MontoItem' => ['title'=>'Total', 'align'=>'right', 'width'=>22],
    ]; ///< Nombres de columnas detalle, alineación y ancho

    private $referencia_cols = [
        'CodRef' => ['title'=>'CodRef', 'align'=>'left', 'width'=>15],
        #'RazonRef' => ['title'=>'Razón Referencia', 'align'=>'left', 'width'=>0],
        'TpoDocRef' => ['title'=>'Doc. Referencia', 'align'=>'left', 'width'=>0],
        'FolioRef' => ['title'=>'N° Doc', 'align'=>'right', 'width'=>15],
        'FchRef' => ['title'=>'Fecha', 'align'=>'right', 'width'=>25],
    ]; ///< Nombres de columnas referencia, alineación y ancho

    private $item_detalle_posicion = 0; ///< Posición del detalle del item respecto al nombre
    private $detalle_fuente = 10; ///< Tamaño de la fuente para el detalle en hoja carta

    private $traslados = [
        1 => 'Operación constituye venta',
        2 => 'Ventas por efectuar',
        3 => 'Consignaciones',
        4 => 'Entrega gratuita',
        5 => 'Traslados internos',
        6 => 'Otros traslados no venta',
        7 => 'Guía de devolución',
        8 => 'Traslado para exportación (no venta)',
        9 => 'Venta para exportación',
    ]; ///< Tipos de traslado para guías de despacho

    public static $papel = [
        0  => 'Hoja carta',
    ]; ///< Tamaño de papel que es soportado

    public function __construct($papelContinuo = false)
    {
        parent::__construct();
        $this->SetTitle('Documento Tributario Electrónico (DTE) de Chile');
        $this->papelContinuo = $papelContinuo === true ? 80 : $papelContinuo;
    }

    public function setLogo($logo, $posicion = 0)
    {
        $this->logo = [
            'uri' => $logo,
            'posicion' => (int)$posicion,
        ];
    }

    public function setResolucion(array $resolucion)
    {
        $this->resolucion = $resolucion;
    }

    public function setWebVerificacion($web)
    {
        $this->web_verificacion = $web;
    }

    public function setCedible($cedible = true)
    {
        $this->cedible = $cedible;
    }

    public function setPosicionDetalleItem($posicion)
    {
        $this->item_detalle_posicion = (int)$posicion;
    }

    public function setFuenteDetalle($fuente)
    {
        $this->detalle_fuente = (int)$fuente;
    }

    public function setAnchoColumnasDetalle(array $anchos)
    {
        foreach ($anchos as $col => $ancho) {
            if (isset($this->detalle_cols[$col]) and $ancho) {
                $this->detalle_cols[$col]['width'] = (int)$ancho;
            }
        }
    }

    public function agregar(array $dte, $timbre = null)
    {
        $this->dte = $dte['Encabezado']['IdDoc']['TipoDTE'];
        if ($this->papelContinuo) {
            $this->agregarContinuo($dte, $timbre, $this->papelContinuo);
        } else {
            $this->agregarNormal($dte, $timbre);
        }
    }

    private function agregarNormal(array $dte, $timbre)
    {
        // agregar página para la factura
        $this->AddPage();
        // agregar cabecera del documento
        $y[] = $this->agregarEmisor($dte['Encabezado']['Emisor']);
        $y[] = $this->agregarFolio(
            $dte['Encabezado']['Emisor']['RUTEmisor'],
            $dte['Encabezado']['IdDoc']['TipoDTE'],
            $dte['Encabezado']['IdDoc']['Folio'],
            $dte['Encabezado']['Emisor']['CmnaOrigen']
        );
        // datos del documento
        $this->setY(max($y));
        $this->Ln();
        $y = [];
        $y[] = $this->agregarDatosEmision($dte['Encabezado']['IdDoc'], !empty($dte['Encabezado']['Emisor']['CdgVendedor'])?$dte['Encabezado']['Emisor']['CdgVendedor']:null);
        $y[] = $this->agregarReceptor($dte['Encabezado']);
        $this->setY(max($y));
        $this->agregarTraslado(
            !empty($dte['Encabezado']['IdDoc']['IndTraslado']) ? $dte['Encabezado']['IdDoc']['IndTraslado'] : null,
            !empty($dte['Encabezado']['Transporte']) ? $dte['Encabezado']['Transporte'] : null
        );
        $this->agregarDetalle($dte['Detalle']);
        if (!empty($dte['Referencia'])){
            $this->agregarReferencia($dte['Referencia']);
        }
        // agregar acuse de recibo y leyenda cedible
        if ($this->cedible and !in_array($dte['Encabezado']['IdDoc']['TipoDTE'], $this->sinAcuseRecibo)) {
            $this->agregarAcuseRecibo();
        }
        // agregar timbre
        $this->agregarTimbre($timbre);
        //dibuja un rectangulo en el total
        $this->dibujarRectangulo();

        if (!empty($dte['DscRcgGlobal'])) {
            $this->agregarSubTotal($dte['Detalle']);
            $this->agregarDescuentosRecargos($dte['DscRcgGlobal']);
        }
        //agrega totales
        if (!empty($dte['Encabezado']['IdDoc']['MntPagos']))
            $this->agregarPagos($dte['Encabezado']['IdDoc']['MntPagos']);
        $this->agregarTotales($dte['Encabezado']['Totales']);
        // agregar observaciones
        $this->agregarObservacion($dte['Encabezado']['IdDoc']);
        // agregar leyenda cedible
        if ($this->cedible and !in_array($dte['Encabezado']['IdDoc']['TipoDTE'], $this->sinAcuseRecibo)) {
            $this->agregarLeyendaDestino($dte['Encabezado']['IdDoc']['TipoDTE']);
        }
    }

    private function agregarEmisor(array $emisor, $x = 10, $y = 20, $w = 120, $w_img = 60, $font_size = null, array $color = null)
{
    // logo del documento
    if (isset($this->logo)) {
        $this->Image(
            $this->logo['uri'],
            $x,
            $y,
            $w_img,
            #!$this->logo['posicion']?$w_img:null, $this->logo['posicion']?($w_img/2):null,
            'PNG'
        );
        if ($this->logo['posicion']) {
            $this->SetY($this->y + ($w_img/2));
            $w += 40;
        } else {
            $x = $this->x+3;
        }
    } else {
        $this->y = $y-2;
        $w += 40;
    }
    
    // agregar datos del emisor
    $this->setFont('', 'B', $font_size ? $font_size : 10);
    $this->SetTextColorArray($color===null?[0,0,0]:$color);
    $this->MultiTexto(!empty($emisor['RznSoc']) ? $emisor['RznSoc'] : $emisor['RznSocEmisor'], $x-9, $this->y+10, 'L', $w);
    
    $this->setFont('', '', $font_size ? $font_size : 9);
    $this->SetTextColorArray([0,0,0]);
    
    // Modificar el giro si es el que necesitamos cambiar
    $giroEmis = !empty($emisor['GiroEmis']) ? $emisor['GiroEmis'] : $emisor['GiroEmisor'];
    if ($giroEmis === 'SERVICIOS INFORMATICOS Y ACTIVIDADES DE PROGRAMACION') {
        $giroEmis = 'SERVICIOS INFORMÁTICOS Y ACTIVIDADES DE ASESORAMIENTO EMPRESARIAL';
    }
    $this->MultiTexto($giroEmis, $x-9, $this->y, 'L', $w);
    
    $ciudad = !empty($emisor['CiudadOrigen']) ? strtoupper($emisor['CiudadOrigen']) : strtoupper(Chile::getCiudad($emisor['CmnaOrigen']));
    $direccion = strtoupper($emisor['DirOrigen']);
    $comuna = strtoupper($emisor['CmnaOrigen']);

    $this->MultiTexto($direccion.', '.$comuna.($ciudad?(', '.$ciudad):''), $x-9, $this->y, 'L', $w);

    if (!empty($emisor['Sucursal'])) {
        $this->MultiTexto('Sucursal: '.strtoupper($emisor['Sucursal']), $x-9, $this->y, 'L', $w);
    }
    
    $contacto = [];
    if (!empty($emisor['Telefono']) || !empty($emisor['CorreoEmisor'])) {
        if (!empty($emisor['Telefono'])) {
            if (!is_array($emisor['Telefono']))
                $emisor['Telefono'] = [$emisor['Telefono']];
            foreach ($emisor['Telefono'] as $t)
                $contacto[] = $t;
        }
        if (!empty($emisor['CorreoEmisor'])) {
            $contacto[] = $emisor['CorreoEmisor'];
        }
    } else {
        $contacto = [];
    }
    if ($contacto) {
        $this->MultiTexto(implode(' / ', $contacto), $x-9, $this->y, 'L', $w);
    }
    return $this->y;
}

    private function agregarFolio($rut, $tipo, $folio, $sucursal_sii = null, $x = 130, $y = 20, $w = 70, $font_size = null, array $color = null)
    {
        if ($color===null) {
            $color = $tipo ? ($tipo==52 ? [0,172,140] : [255,0,0]) : [0,0,0];
        }
        $this->SetTextColorArray($color);
        // colocar rut emisor, glosa documento y folio
        list($rut, $dv) = explode('-', $rut);
        $this->setFont ('', 'B', $font_size ? $font_size : 15);
        $this->MultiTexto('R.U.T.: '.$this->num($rut).'-'.$dv, $x, $y+4, 'C', $w);
        $this->setFont('', 'B', $font_size ? $font_size : 12);
        $this->MultiTexto($this->getTipo($tipo), $x, null, 'C', $w);
        $this->setFont('', 'B', $font_size ? $font_size : 15);
        $this->MultiTexto('N° '.$folio, $x, null, 'C', $w);
        // dibujar rectángulo rojo
        $this->Rect($x, $y, $w, round($this->getY()-$y+3), 'D', ['all' => ['width' => 0.5, 'color' => $color]]);
        // colocar unidad del SII
        $this->setFont('', 'B', $font_size ? $font_size : 10);
        if ($tipo) {
            $this->Texto('S.I.I. - '.Sii::getDireccionRegional($sucursal_sii), $x, $this->getY()+4, 'C', $w);
        }
        $this->SetTextColorArray([0,0,0]);
        $this->Ln();
        return $this->y;
    }

    private function getTipo($tipo)
    {
        if (!is_numeric($tipo) and !isset($this->tipos[$tipo]))
            return $tipo;
        return isset($this->tipos[$tipo]) ? strtoupper($this->tipos[$tipo]) : 'Documento '.$tipo;
    }

    private function agregarDatosEmision($IdDoc, $CdgVendedor, $x = 10, $offset = 22, $mostrar_dia = true)
    {
        // si es hoja carta
        if ($x==10) {
            $y = $this->GetY();
            // fecha emisión
            $this->setFont('', 'B', null);
            $this->MultiTexto($this->date($IdDoc['FchEmis'], $mostrar_dia), $x, $y-5, 'R');
            $this->setFont('', '', null);
            // período facturación
            if (!empty($IdDoc['PeriodoDesde']) and !empty($IdDoc['PeriodoHasta'])) {
                $this->MultiTexto('Período del '.date('d/m/y', strtotime($IdDoc['PeriodoDesde'])).' al '.date('d/m/y', strtotime($IdDoc['PeriodoHasta'])), $x, null, 'R');
            }
            // pago anticicado
            if (!empty($IdDoc['FchCancel'])) {
                $this->MultiTexto('Pagado el '.$this->date($IdDoc['FchCancel'], false), $x, null, 'R');
            }
            // fecha vencimiento
            if (!empty($IdDoc['FchVenc'])) {
                $this->MultiTexto('Vence el '.$this->date($IdDoc['FchVenc'], false), $x, null, 'R');
            }
            // forma de pago nacional
            if (!empty($IdDoc['FmaPago'])) {
                $this->MultiTexto('Venta: '.$this->formas_pago[$IdDoc['FmaPago']], $x, null, 'R');
            }
            // forma de pago exportación
            if (!empty($IdDoc['FmaPagExp'])) {
                $this->MultiTexto('Venta: '.$this->formas_pago_exportacion[$IdDoc['FmaPagExp']], $x, null, 'R');
            }
            // vendedor
            if (!empty($CdgVendedor)) {
                $this->MultiTexto('Vendedor: '.$CdgVendedor, $x, null, 'R');
            }
            $y_end = $this->GetY();
            $this->SetY($y);
        }
        // papel contínuo
        else {
            // fecha de emisión
            $this->setFont('', 'B', null);
            $this->Texto('Emisión', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto($this->date($IdDoc['FchEmis'], $mostrar_dia), $x+$offset+2);
            // forma de pago nacional
            if (!empty($IdDoc['FmaPago'])) {
                $this->setFont('', 'B', null);
                $this->Texto('Venta', $x);
                $this->Texto(':', $x+$offset);
                $this->setFont('', '', null);
                $this->MultiTexto($this->formas_pago[$IdDoc['FmaPago']], $x+$offset+2);
            }
            // forma de pago exportación
            if (!empty($IdDoc['FmaPagExp'])) {
                $this->setFont('', 'B', null);
                $this->Texto('Venta', $x);
                $this->Texto(':', $x+$offset);
                $this->setFont('', '', null);
                $this->MultiTexto($this->formas_pago_exportacion[$IdDoc['FmaPagExp']], $x+$offset+2);
            }
            // pago anticicado
            if (!empty($IdDoc['FchCancel'])) {
                $this->setFont('', 'B', null);
                $this->Texto('Pagado el', $x);
                $this->Texto(':', $x+$offset);
                $this->setFont('', '', null);
                $this->MultiTexto($this->date($IdDoc['FchCancel'], $mostrar_dia), $x+$offset+2);
            }
            // fecha vencimiento
            if (!empty($IdDoc['FchVenc'])) {
                $this->setFont('', 'B', null);
                $this->Texto('Vence el', $x);
                $this->Texto(':', $x+$offset);
                $this->setFont('', '', null);
                $this->MultiTexto($this->date($IdDoc['FchVenc'], $mostrar_dia), $x+$offset+2);
            }
            $y_end = $this->GetY();
        }
        return $y_end;
    }

    private function agregarReceptor(array $Encabezado, $x = 10, $offset = 22, $w = 190)
    {
        $y = $this->GetY();
        $receptor = $Encabezado['Receptor'];
        if (!empty($receptor['RUTRecep']) and $receptor['RUTRecep']!='66666666-6') {
            list($rut, $dv) = explode('-', $receptor['RUTRecep']);
            $this->setFont('', 'B', null);
            $this->Texto('R.U.T.', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto($this->num($rut).'-'.$dv, $x+$offset+2);
        }
        if (!empty($receptor['RznSocRecep'])) {
            $this->setFont('', 'B', null);
            $this->Texto('Señor(es)', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto($receptor['RznSocRecep'], $x+$offset+2, null, '', $x==10?105:0);
        }
        if (!empty($receptor['GiroRecep'])) {
            $this->setFont('', 'B', null);
            $this->Texto('Giro', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto($receptor['GiroRecep'], $x+$offset+2);
        }
        if (!empty($receptor['DirRecep'])) {
            $this->setFont('', 'B', null);
            $this->Texto('Dirección', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $ciudad = !empty($receptor['CiudadRecep']) ? $receptor['CiudadRecep'] : (
                !empty($receptor['CmnaRecep']) ? Chile::getCiudad($receptor['CmnaRecep']) : ''
            );
            $this->MultiTexto($receptor['DirRecep'].(!empty($receptor['CmnaRecep'])?(', '.$receptor['CmnaRecep']):'').($ciudad?(', '.$ciudad):''), $x+$offset+2);
        }
        if (!empty($receptor['Extranjero']['Nacionalidad'])) {
            $this->setFont('', 'B', null);
            $this->Texto('Nacionalidad', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto(Aduana::getNacionalidad($receptor['Extranjero']['Nacionalidad']), $x+$offset+2);
        }
        $contacto = [];
        if (!empty($receptor['Contacto']))
            $contacto[] = $receptor['Contacto'];
        if (!empty($receptor['CorreoRecep']))
            $contacto[] = $receptor['CorreoRecep'];
        if (!empty($contacto)) {
            $this->setFont('', 'B', null);
            $this->Texto('Contacto', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto(implode(' / ', $contacto), $x+$offset+2);
        }
        if (!empty($Encabezado['RUTSolicita'])) {
            list($rut, $dv) = explode('-', $Encabezado['RUTSolicita']);
            $this->setFont('', 'B', null);
            $this->Texto('RUT solicita', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto($this->num($rut).'-'.$dv, $x+$offset+2);
        }
        if (!empty($receptor['CdgIntRecep'])) {
            $this->setFont('', 'B', null);
            $this->Texto('Cód. recep.', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto($receptor['CdgIntRecep'], $x+$offset+2, null, '', $x==10?105:0);
        }
        $this->Rect($x, $y, $w, round($this->getY()-$y), 'D', ['all' => ['width' => 0.3, 'color' => [0,0,0]]]);
        return $this->GetY();
    }

    private function agregarTraslado($IndTraslado, array $Transporte = null, $x = 10, $offset = 22)
    {
        // agregar tipo de traslado
        if ($IndTraslado) {
            $this->setFont('', 'B', null);
            $this->Texto('Tipo oper.', $x);
            $this->Texto(':', $x+$offset);
            $this->setFont('', '', null);
            $this->MultiTexto($this->traslados[$IndTraslado], $x+$offset+2);
        }
        // agregar información de transporte
        if ($Transporte) {
            $transporte = '';
            if (!empty($Transporte['DirDest']) and !empty($Transporte['CmnaDest'])) {
                $transporte .= 'a '.$Transporte['DirDest'].', '.$Transporte['CmnaDest'];
            }
            if (!empty($Transporte['RUTTrans']))
                $transporte .= ' por '.$Transporte['RUTTrans'];
            if (!empty($Transporte['Patente']))
                $transporte .= ' en vehículo '.$Transporte['Patente'];
            if (isset($Transporte['Chofer']) and is_array($Transporte['Chofer'])) {
                if (!empty($Transporte['Chofer']['NombreChofer']))
                    $transporte .= ' con chofer '.$Transporte['Chofer']['NombreChofer'];
                if (!empty($Transporte['Chofer']['RUTChofer']))
                    $transporte .= ' ('.$Transporte['Chofer']['RUTChofer'].')';
            }
            if ($transporte) {
                $this->setFont('', 'B', null);
                $this->Texto('Traslado', $x);
                $this->Texto(':', $x+$offset);
                $this->setFont('', '', null);
                $this->MultiTexto(ucfirst(trim($transporte)), $x+$offset+2);
            }
        }
        // agregar información de aduana
        if (!empty($Transporte['Aduana']) and is_array($Transporte['Aduana'])) {
            $col = 0;
            foreach ($Transporte['Aduana'] as $tag => $codigo) {
                if ($codigo===false)
                    continue;
                $glosa = Aduana::getGlosa($tag);
                $valor = Aduana::getValor($tag, $codigo);
                if ($glosa!==false and $valor!==false) {
                    if ($tag=='TipoBultos' and $col) {
                        $col = abs($col-110);
                        $this->Ln();
                    }
                    $this->setFont('', 'B', null);
                    $this->Texto($glosa, $x+$col);
                    $this->Texto(':', $x+$offset+$col);
                    $this->setFont('', '', null);
                    $this->Texto($valor, $x+$offset+2+$col);
                    if ($tag=='TipoBultos')
                        $col = abs($col-110);
                    if ($col)
                        $this->Ln();
                    $col = abs($col-110);
                }
            }
            if ($col)
                $this->Ln();
        }
    }

    private function agregarDetalle($detalle, $x = 10)
    {
        $this->Ln();
        
        if (!isset($detalle[0]))
            $detalle = [$detalle];
        $this->setFont('', '', $this->detalle_fuente);
        // titulos
        $titulos = [];
        $titulos_keys = array_keys($this->detalle_cols);
        foreach ($this->detalle_cols as $key => $info) {
            $titulos[$key] = $info['title'];
        }
        // normalizar cada detalle
        $dte_exento = in_array($this->dte, [34, 110, 111, 112]);
        foreach ($detalle as &$item) {
            // quitar columnas
            foreach ($item as $col => $valor) {
                if ($col=='DscItem' and !empty($item['DscItem'])) {
                    $item['NmbItem'] .= !$this->item_detalle_posicion ? '<br/>' : ': ';
                    $item['NmbItem'] .= $item['DscItem'];
                }
                if (!in_array($col, $titulos_keys) or ($dte_exento and $col=='IndExe'))
                    unset($item[$col]);
            }
            // ajustes a IndExe
            if (isset($item['IndExe'])) {
                if ($item['IndExe']==1)
                    $item['IndExe'] = 'EX';
                else if ($item['IndExe']==2)
                    $item['IndExe'] = 'NF';
            }
            // agregar todas las columnas que se podrían imprimir en la tabla
            $item_default = [];
            foreach ($this->detalle_cols as $key => $info)
                $item_default[$key] = false;
            $item = array_merge($item_default, $item);
            // si hay código de item se extrae su valor
            if ($item['CdgItem'])
                $item['CdgItem'] = $item['CdgItem']['VlrCodigo'];
            // dar formato a números
            foreach (['QtyItem', 'PrcItem', 'DescuentoMonto', 'RecargoMonto', 'MontoItem'] as $col) {
                if ($item[$col])
                    $item[$col] = str_replace(',00', '',$this->num($item[$col]));
            }
        }
        // opciones
        $options = ['align'=>[]];
        $i = 0;
        foreach ($this->detalle_cols as $info) {
            if (isset($info['width']))
                $options['width'][$i] = $info['width'];
            $options['align'][$i] = $info['align'];
            $i++;
        }
        // agregar tabla de detalle
        $this->SetX($x);
        $this->addTableWithoutEmptyCols($titulos, $detalle, $options);
    }

    private function agregarReferencia($referencias, $x = 10)
    {
        if (!isset($referencias[0]))
            $referencias = [$referencias];
        $titulos = [];
        $titulos_keys = array_keys($this->referencia_cols);
          foreach ($this->referencia_cols as $key => $info) {
                $titulos[$key] = $info['title'];
        }

        $referencia=[];

        foreach($referencias as $key => $value) {
          $referencia[$key]=[
              'CodRef' => $value['CodRef'],
              #'RazonRef' => $value['RazonRef'],
              'TpoDocRef' => $this->getTipo($value['TpoDocRef']),
              'FolioRef' => $value['FolioRef'],
              'FchRef' => $value['FchRef'],
          ];
        }
        $options = ['align'=>[]];
        $i = 0;
        foreach ($this->referencia_cols as $info) {
            if (isset($info['width']))
                $options['width'][$i] = $info['width'];
            $options['align'][$i] = $info['align'];
            $i++;
        }
        $this->SetX($x);
        $this->addTableWithoutEmptyCols($titulos, $referencia, $options);
    }

    private function agregarAcuseRecibo($x = 10, $w = 190, $h = 15)
    {
        $y = $this->GetY();
        $this->SetTextColorArray([0,0,0]);
        $this->Rect($x, $y, $w, $h, 'D', ['all' => ['width' => 0.3, 'color' => [0, 0, 0]]]);
        $this->setFont('', 'B', 8);
        $this->Texto('Nombre: _________________ R.U.T.: _________________ Fecha: _________________ Recinto: _________________ Firma: ________________', $x, $y+3, 'C', $w);
        $this->setFont('', 'B', 7);
        $this->MultiTexto('El acuse de recibo que se declara en este acto, de acuerdo a lo dispuesto en la letra b) del Art. 4°, y la letra c) del Art. 5° de la Ley 19.983, acredita que la entrega de mercaderías o servicio (s) prestado (s) ha (n) sido recibido (s).'."\n", $x, $this->y+6, 'J', $w);
    }

    private function agregarObservacion($IdDoc, $x = 10, $y = 175)
    {
        $this->SetXY($x, $y);
        if (!empty($IdDoc['TermPagoGlosa'])) {
            $this->MultiTexto('Observación: '.$IdDoc['TermPagoGlosa']);
        }
        return $this->GetY();
    }

    public function setYbaja($y){
      $this->ybaja = $y;
    }

    public function getYbaja(){
      return $this->ybaja;
    }

    private function agregarTimbre($timbre, $x_timbre = 10, $x = 10, $w = 70, $font_size = 8)
    {
        $y = $this->GetY()+4;
        $this->setYbaja($y);
        if ($timbre!==null) {
            $style = [
                'border' => false,
                'padding' => 0,
                'hpadding' => 0,
                'vpadding' => 0,
                'module_width' => 1, // width of a single module in points
                'module_height' => 1, // height of a single module in points
                'fgcolor' => [0,0,0],
                'bgcolor' => false, // [255,255,255]
                'position' => $this->papelContinuo ? 'C' : 'S',
            ];
            $ecl = version_compare(phpversion(), '7.0.0', '<') ? -1 : $this->ecl;
            $this->write2DBarcode($timbre, 'PDF417,,'.$ecl, $x_timbre, $y, $w, 0, $style, 'B');
            $this->setFont('', 'B', $font_size);
            $this->Texto('Timbre Electrónico SII', $x, null, 'C', $w);
            $this->Ln();
            $this->Texto('Resolución '.$this->resolucion['NroResol'].' de '.explode('-', $this->resolucion['FchResol'])[0], $x, null, 'C', $w);
            $this->Ln();
            $this->Texto('Verifique documento: '.$this->web_verificacion, $x, null, 'C', $w);
        }
    }

    private function dibujarRectangulo()
    {
      $x = 140;
      $y = $this->getYbaja();
      $w = 60;
      $h = 25;
      $this->Rect($x, $y, $w, $h, 'D', ['all' => ['width' => 0.3, 'color' => [0,0,0]]]);
    }

    private function agregarSubTotal(array $detalle, $x = 145, $offset = 25)
    {
        $y = $this->getYbaja();
        $this->setYbaja($y);
        $this->setFont('', 'B', 9);
        $subtotal = 0;
        if (!isset($detalle[0])) {
            $detalle = [$detalle];
        }
        foreach($detalle as  &$d) {
            if (!empty($d['MontoItem'])) {
                $subtotal += $d['MontoItem'];
            }
        }
        $this->MultiTexto('Subtotal $ :', $x, $y, 'R', 30);
        $this->Texto($this->num($subtotal), $x+$offset, $y, 'R', 30);
        $this->Ln();
    }

    private function agregarDescuentosRecargos(array $descuentosRecargos, $x = 140, $offset = 25)
    {
        $y = $this->getYbaja()+4;
        $this->setYbaja($y);
        if (!isset($descuentosRecargos[0]))
            $descuentosRecargos = [$descuentosRecargos];
        foreach($descuentosRecargos as $dr) {
            $tipo = $dr['TpoMov']=='D' ? 'Descuento' : 'Recargo';
            $valor = $dr['TpoValor']=='%' ? $dr['ValorDR'].'%' : '$'.$this->num($dr['ValorDR']).'.-';
            //$this->MultiTexto($tipo.' global $ :', $x, $y, 'R', 35);
            $this->Texto($valor.(!empty($dr['GlosaDR'])?(' ('.$dr['GlosaDR'].')'):''), $x+$offset, $y, 'R', 35);
            $this->Ln();
        }
    }

    private function agregarTotales(array $totales, $x = 145, $offset = 25)
    {
        $y = $this->getYbaja()+4;
        $this->setYbaja($y);
        // normalizar totales
        $totales = array_merge([
            'TpoMoneda' => false,
            'MntNeto' => false,
            'MntExe' => false,
            'TasaIVA' => false,
            'IVA' => false,
            'IVANoRet' => false,
            'CredEC' => false,
            'MntTotal' => false,
            'MontoNF' => false,
            'MontoPeriodo' => false,
            'SaldoAnterior' => false,
            'VlrPagar' => false,
        ], $totales);
        // glosas
        $glosas = [
            'TpoMoneda' => 'Moneda',
            'MntNeto' => 'Neto $',
            'MntExe' => 'Exento $',
            'IVA' => 'IVA ('.$totales['TasaIVA'].'%) $',
            'IVANoRet' => 'IVA no retenido $',
            'CredEC' => 'Desc. 65% IVA $',
            'MntTotal' => 'Total $',
            'MontoNF' => 'Monto no facturable $',
            'MontoPeriodo' => 'Monto período $',
            'SaldoAnterior' => 'Saldo anterior $',
            'VlrPagar' => 'Valor a pagar $',
        ];
        // agregar impuestos adicionales y retenciones
        if (!empty($totales['ImptoReten'])) {
            $ImptoReten = $totales['ImptoReten'];
            $MntTotal = $totales['MntTotal'];
            unset($totales['ImptoReten'], $totales['MntTotal']);
            if (!isset($ImptoReten[0])) {
                $ImptoReten = [$ImptoReten];
            }
            foreach($ImptoReten as $i) {
                $totales['ImptoReten_'.$i['TipoImp']] = $i['MontoImp'];
                if (!empty($i['TasaImp'])) {
                    $glosas['ImptoReten_'.$i['TipoImp']] = ImpuestosAdicionales::getGlosa($i['TipoImp']).' ('.$i['TasaImp'].'%) $';
                    $this->Ln();
                } else {
                    $glosas['ImptoReten_'.$i['TipoImp']] = ImpuestosAdicionales::getGlosa($i['TipoImp']).' $';
                    $this->Ln();
                }
            }
            $totales['MntTotal'] = $MntTotal;
        }

        // agregar cada uno de los totales
        $this->setY($y);
        $this->setFont('', 'B', null);
        foreach ($totales as $key => $total) {
            if ($total!==false and isset($glosas[$key])) {
                $y = $this->GetY();
                if (!$this->cedible or $this->papelContinuo) {
                    $this->Texto($glosas[$key].' :', $x, null, 'R', 30);
                    $this->Texto($this->num($total), $x+$offset, $y, 'R', 30);
                    $this->Ln();
                } else {
                    $this->MultiTexto($glosas[$key].' :', $x, null, 'R', 30);
                    $y_new = $this->GetY();
                    $this->Texto($this->num($total), $x+$offset, $y, 'R', 30);
                    $this->SetY($y_new);
                }
            }
        }
    }

    private function agregarPagos(array $pagos, $x = 10)
    {
        if (!isset($pagos[0]))
            $pagos = [$pagos];
        $this->Texto('Pago(s) programado(s):', $x);
        $this->Ln();
        foreach($pagos as $p) {
            $this->Texto('  - '.$this->date($p['FchPago'], false).': $'.$this->num($p['MntPago']).'.-'.(!empty($p['GlosaPagos'])?(' ('.$p['GlosaPagos'].')'):''), $x);
            $this->Ln();
        }
    }

    private function agregarLeyendaDestino($tipo, $y = 240, $font_size = 10)
    {
        $this->setFont('', 'B', $font_size);
        $this->SetTextColorArray([255,0,0]);
        $this->Texto('CEDIBLE'.($tipo==52?' CON SU FACTURA':''), null, $y, 'R');
    }

    private function num($n)
    {
        if (!is_numeric($n))
            return $n;
        $broken_number = explode('.', (string)$n);
        if (isset($broken_number[1]))
            return number_format($broken_number[0], 0, ',', '.').','.$broken_number[1];
        return number_format($broken_number[0], 0, ',', '.');
    }

    public function date($date, $mostrar_dia = true)
    {
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $unixtime = strtotime($date);
        $fecha = date(($mostrar_dia?'\D\I\A ':'').'j \d\e \M\E\S \d\e\l Y', $unixtime);
        $dia = $dias[date('w', $unixtime)];
        $mes = $meses[date('n', $unixtime)-1];
        return str_replace(array('DIA', 'MES'), array($dia, $mes), $fecha);
    }

}
