<?php
/**
 * Clase que representa un DTE y permite trabajar con el
 */
class Dte
{

    private $tipo; ///< Identificador del tipo de DTE: 33 (factura electrónica)
    private $folio; ///< Folio del documento
    private $xml; ///< Objeto XML que representa el DTE
    private $id; ///< Identificador único del DTE
    private $tipo_general; ///< Tipo general de DTE: Documento, Liquidacion o Exportaciones
    private $timestamp; ///< Timestamp del DTE
    private $datos = null; ///< Datos normalizados que se usaron para crear el DTE
    private $Signature = null; ///< Datos de la firma del DTE

    private $tipos = [
        'Documento' => [33, 34, 39, 41, 46, 52, 56, 61],
        'Liquidacion' => [43],
        'Exportaciones' => [110, 111, 112],
    ]; ///< Tipos posibles de documentos tributarios electrónicos

    private $noCedibles = [39, 41, 56, 61, 110, 111, 112]; ///< Documentos que no son cedibles

    public function __construct($datos, $normalizar = true)
    {
        if (is_array($datos))
            $this->setDatos($datos, $normalizar);
        else if (is_string($datos))
            $this->loadXML($datos);
        $this->timestamp = date('Y-m-d\TH:i:s');
    }

    public function loadXML($xml)
    {
        if (!empty($xml)) {
            $this->xml = new XML();
            if (!$this->xml->loadXML($xml) or !$this->schemaValidate()) {
                Log::write(
                    Estado::DTE_ERROR_LOADXML,
                    Estado::get(Estado::DTE_ERROR_LOADXML)
                );
                return false;
            }
            $TipoDTE = $this->xml->getElementsByTagName('TipoDTE')->item(0);
            if (!$TipoDTE) {
                return false;
            }
            $this->tipo = $TipoDTE->nodeValue;
            $this->tipo_general = $this->getTipoGeneral($this->tipo);
            if (!$this->tipo_general) {
                return false;
            }
            $Folio = $this->xml->getElementsByTagName('Folio')->item(0);
            if (!$Folio) {
                return false;
            }
            $this->folio = $Folio->nodeValue;
            if (isset($this->getDatos()['@attributes'])) {
                $this->id = $this->getDatos()['@attributes']['ID'];
            } else {
                $this->id = 'D0C'.$this->tipo.'F0'.$this->folio;
            }
            return true;
        }
        return false;
    }

    private function setDatos(array $datos, $normalizar = true)
    {
        if (!empty($datos)) {
            $this->tipo = $datos['Encabezado']['IdDoc']['TipoDTE'];
            $this->folio = $datos['Encabezado']['IdDoc']['Folio'];
            $this->id = 'D0C'.$this->tipo.'F0'.$this->folio;
            if ($normalizar) {
                $this->normalizar($datos);
                $method = 'normalizar_'.$this->tipo;
                if (method_exists($this, $method))
                    $this->$method($datos);
                $this->normalizar_final($datos);
            }
            $this->tipo_general = $this->getTipoGeneral($this->tipo);
            $this->xml = (new XML())->generate([
                'DTE' => [
                    '@attributes' => [
                        'version' => '1.0',
                    ],
                    $this->tipo_general => [
                        '@attributes' => [
                            'ID' => $this->id
                        ],
                    ]
                ]
            ]);
            $parent = $this->xml->getElementsByTagName($this->tipo_general)->item(0);
            $this->xml->generate($datos + ['TED' => null], $parent);
            $this->datos = $datos;
            #if (!$this->verificarDatos())
            #   return false;
            return $this->schemaValidate();
        }
        return false;
    }

    


    public function getDatos()
    {
        if (!$this->datos) {
            $datos = $this->xml->toArray();
            if (!isset($datos['DTE'][$this->tipo_general])) {
                Log::write(
                    Estado::DTE_ERROR_GETDATOS,
                    Estado::get(Estado::DTE_ERROR_GETDATOS)
                );
                return false;
            }
            $this->datos = $datos['DTE'][$this->tipo_general];
            if (isset($datos['DTE']['Signature'])) {
                $this->Signature = $datos['DTE']['Signature'];
            }
        }
        
        return $this->datos;
    }


    public function getFirma()
    {   
        if (!$this->Signature) {
            $this->getDatos();
        }
        return $this->Signature;
    }



    public function getJSON()
    {
        if (!$this->getDatos())
            return false;
        return json_encode($this->datos, JSON_PRETTY_PRINT);
    }

    public function getID($estandar = false)
    {
        return $estandar ? ('T'.$this->tipo.'F'.$this->folio) : $this->id;
    }

    private function getTipoGeneral($dte)
    {
        foreach ($this->tipos as $tipo => $codigos)
            if (in_array($dte, $codigos))
                return $tipo;
        Log::write(
            Estado::DTE_ERROR_TIPO,
            Estado::get(Estado::DTE_ERROR_TIPO, $dte)
        );
        return false;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getFolio()
    {
        return $this->folio;
    }

    public function getEmisor()
    {
        $nodo = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Emisor/RUTEmisor')->item(0);
        if ($nodo)
            return $nodo->nodeValue;
        if (!$this->getDatos())
            return false;
        return $this->datos['Encabezado']['Emisor']['RUTEmisor'];
    }

    public function getReceptor()
    {
        $nodo = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Receptor/RUTRecep')->item(0);
        if ($nodo)
            return $nodo->nodeValue;
        if (!$this->getDatos())
            return false;
        return $this->datos['Encabezado']['Receptor']['RUTRecep'];
    }

    public function getFechaEmision()
    {
        $nodo = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/IdDoc/FchEmis')->item(0);
        if ($nodo)
            return $nodo->nodeValue;
        if (!$this->getDatos())
            return false;
        return $this->datos['Encabezado']['IdDoc']['FchEmis'];
    }

    public function getMontoTotal()
    {
        $nodo = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Totales/MntTotal')->item(0);
        if ($nodo)
            return $nodo->nodeValue;
        if (!$this->getDatos())
            return false;
        return $this->datos['Encabezado']['Totales']['MntTotal'];
    }

    public function getMoneda()
    {
        $nodo = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Totales/TpoMoneda')->item(0);
        if ($nodo)
            return $nodo->nodeValue;
        if (!$this->getDatos())
            return false;
        return $this->datos['Encabezado']['Totales']['TpoMoneda'];
    }

    public function getTED()
    {
        $xml = new XML();
        $TED = $this->xml->getElementsByTagName('TED')->item(0);
        if (!$TED)
            return '<TED/>';
        $xml->loadXML($TED->C14N());
        $xml->documentElement->removeAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi');
        $xml->documentElement->removeAttributeNS('http://www.sii.cl/SiiDte', '');
        $TED = $xml->getFlattened('/');
        return mb_detect_encoding($TED, ['UTF-8', 'ISO-8859-1']) != 'ISO-8859-1' ? utf8_decode($TED) : $TED;
    }

    public function getCertificacion()
    {
        $datos = $this->getDatos();
        $idk = !empty($datos['TED']['DD']['CAF']['DA']['IDK']) ? (int)$datos['TED']['DD']['CAF']['DA']['IDK'] : null;
        return $idk ? $idk === 100 : null;
    }

    public function timbrar(Folios $Folios)
    {
        // verificar que el folio que se está usando para el DTE esté dentro
        // del rango de folios autorizados que se usarán para timbrar
        // Esta validación NO verifica si el folio ya fue usado, sólo si está
        // dentro del CAF que se está usando
        $folio = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/IdDoc/Folio')->item(0)->nodeValue;
        if ($folio<$Folios->getDesde() or $folio>$Folios->getHasta()) {
            Log::write(
                Estado::DTE_ERROR_RANGO_FOLIO,
                Estado::get(Estado::DTE_ERROR_RANGO_FOLIO, $this->getID())
            );
            return false;
        }
        // verificar que existan datos para el timbre
        if (!$this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/IdDoc/FchEmis')->item(0)) {
            Log::write(
                Estado::DTE_FALTA_FCHEMIS,
                Estado::get(Estado::DTE_FALTA_FCHEMIS, $this->getID())
            );
            Log::write('Falta FchEmis del DTE '.$this->getID());
            return false;
        }
        if (!$this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Totales/MntTotal')->item(0)) {
            Log::write(
                Estado::DTE_FALTA_MNTTOTAL,
                Estado::get(Estado::DTE_FALTA_MNTTOTAL, $this->getID())
            );
            return false;
        }
        // timbrar
        $RR = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Receptor/RUTRecep')->item(0)->nodeValue;
        $RSR_nodo = $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Receptor/RznSocRecep');
        $RSR = $RSR_nodo->length ? trim(mb_substr($RSR_nodo->item(0)->nodeValue, 0, 40)) : $RR;
        $TED = new XML();
        $TED->generate([
            'TED' => [
                '@attributes' => [
                    'version' => '1.0',
                ],
                'DD' => [
                    'RE' => $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Emisor/RUTEmisor')->item(0)->nodeValue,
                    'TD' => $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/IdDoc/TipoDTE')->item(0)->nodeValue,
                    'F' => $folio,
                    'FE' => $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/IdDoc/FchEmis')->item(0)->nodeValue,
                    'RR' => $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Receptor/RUTRecep')->item(0)->nodeValue,
                    'RSR' => $RSR,
                    'MNT' => $this->xml->xpath('/DTE/'.$this->tipo_general.'/Encabezado/Totales/MntTotal')->item(0)->nodeValue,
                    'IT1' => trim(mb_substr($this->xml->xpath('/DTE/'.$this->tipo_general.'/Detalle')->item(0)->getElementsByTagName('NmbItem')->item(0)->nodeValue, 0, 40)),
                    'CAF' => $Folios->getCaf(),
                    'TSTED' => $this->timestamp,
                ],
                'FRMT' => [
                    '@attributes' => [
                        'algoritmo' => 'SHA1withRSA'
                    ],
                ],
            ]
        ]);
        $DD = $TED->getFlattened('/TED/DD');
        if (openssl_sign($DD, $timbre, $Folios->getPrivateKey(), OPENSSL_ALGO_SHA1)==false) {
            Log::write(
                Estado::DTE_ERROR_TIMBRE,
                Estado::get(Estado::DTE_ERROR_TIMBRE, $this->getID())
            );
            return false;
        }
        $TED->getElementsByTagName('FRMT')->item(0)->nodeValue = base64_encode($timbre);
        $xml = str_replace('<TED/>', trim(str_replace('<?xml version="1.0" encoding="ISO-8859-1"?>', '', $TED->saveXML())), $this->saveXML());
        if (!$this->loadXML($xml)) {
            Log::write(
                Estado::DTE_ERROR_TIMBRE,
                Estado::get(Estado::DTE_ERROR_TIMBRE, $this->getID())
            );
            return false;
        }
        return true;
    }

    public function firmar(FirmaElectronica $Firma)
    {
        $parent = $this->xml->getElementsByTagName($this->tipo_general)->item(0);
        $this->xml->generate(['TmstFirma'=>$this->timestamp], $parent);
        $xml = $Firma->signXML($this->xml->saveXML(), '#'.$this->id, $this->tipo_general);
        if (!$xml) {
            Log::write(
                Estado::DTE_ERROR_FIRMA,
                Estado::get(Estado::DTE_ERROR_FIRMA, $this->getID())
            );
            return false;
        }
        $this->loadXML($xml);
        return true;
    }

    public function saveXML()
    {
        return $this->xml->saveXML();
    }

    public function getResumen()
    {
        $this->getDatos();
        // generar resumen
        $resumen =  [
            'TpoDoc' => (int)$this->datos['Encabezado']['IdDoc']['TipoDTE'],
            'NroDoc' => (int)$this->datos['Encabezado']['IdDoc']['Folio'],
            'TasaImp' => 0,
            'FchDoc' => $this->datos['Encabezado']['IdDoc']['FchEmis'],
            'CdgSIISucur' => !empty($this->datos['Encabezado']['Emisor']['CdgSIISucur']) ? $this->datos['Encabezado']['Emisor']['CdgSIISucur'] : false,
            'RUTDoc' => $this->datos['Encabezado']['Receptor']['RUTRecep'],
            'RznSoc' => isset($this->datos['Encabezado']['Receptor']['RznSocRecep']) ? $this->datos['Encabezado']['Receptor']['RznSocRecep'] : false,
            'MntExe' => false,
            'MntNeto' => false,
            'MntIVA' => 0,
            'MntTotal' => 0,
        ];
        // obtener montos si es que existen en el documento
        $montos = ['TasaImp'=>'TasaIVA', 'MntExe'=>'MntExe', 'MntNeto'=>'MntNeto', 'MntIVA'=>'IVA', 'MntTotal'=>'MntTotal'];
        foreach ($montos as $dest => $orig) {
            if (!empty($this->datos['Encabezado']['Totales'][$orig])) {
                $resumen[$dest] = !$this->esExportacion() ? round($this->datos['Encabezado']['Totales'][$orig]) : $this->datos['Encabezado']['Totales'][$orig];
            }
        }
        // si es una boleta se calculan los datos para el resumen
        if ($this->esBoleta()) {
            if (!$resumen['TasaImp']) {
                $resumen['TasaImp'] = Sii::getIVA();
            }
            $resumen['MntExe'] = (int)$resumen['MntExe'];
            if (!$resumen['MntNeto']) {
                list($resumen['MntNeto'], $resumen['MntIVA']) = $this->calcularNetoIVA($resumen['MntTotal']-$resumen['MntExe'], $resumen['TasaImp']);
            }
        }
        // entregar resumen
        return $resumen;
    }

    private function calcularNetoIVA($total, $tasa = null)
    {
        if ($tasa === 0 or $tasa === false)
            return [0, 0];
        if ($tasa === null)
            $tasa = Sii::getIVA();
        // WARNING: el IVA obtenido puede no ser el NETO*(TASA/100)
        // se calcula el monto neto y luego se obtiene el IVA haciendo la resta
        // entre el total y el neto, ya que hay casos de borde como:
        //  - BRUTO:   680 => NETO:   571 e IVA:   108 => TOTAL:   679
        //  - BRUTO: 86710 => NETO: 72866 e IVA: 13845 => TOTAL: 86711
        $neto = round($total / (1+($tasa/100)));
        $iva = $total - $neto;
        return [$neto, $iva];
    }

    private function normalizar(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => false,
                    'Folio' => false,
                    'FchEmis' => date('Y-m-d'),
                    'IndNoRebaja' => false,
                    'TipoDespacho' => false,
                    'IndTraslado' => false,
                    'TpoImpresion' => false,
                    'IndServicio' => $this->esBoleta() ? 3 : false,
                    'MntBruto' => false,
                    'FmaPago' => false,
                    'FmaPagExp' => false,
                    'MntCancel' => false,
                    'SaldoInsol' => false,
                    'FchCancel' => false,
                    'MntPagos' => false,
                    'PeriodoDesde' => false,
                    'PeriodoHasta' => false,
                    'MedioPago' => false,
                    'TpoCtaPago' => false,
                    'NumCtaPago' => false,
                    'BcoPago' => false,
                    'TermPagoCdg' => false,
                    'TermPagoGlosa' => false,
                    'TermPagoDias' => false,
                    'FchVenc' => false,
                ],
                'Emisor' => [
                    'RUTEmisor' => false,
                    'RznSoc' => false,
                    'GiroEmis' => false,
                    'Telefono' => false,
                    'CorreoEmisor' => false,
                    'Acteco' => false,
                    'Sucursal' => false,
                    'CdgSIISucur' => false,
                    'DirOrigen' => false,
                    'CmnaOrigen' => false,
                    'CiudadOrigen' => false,
                    'CdgVendedor' => false,
                ],
                'Receptor' => [
                    'RUTRecep' => false,
                    'CdgIntRecep' => false,
                    'RznSocRecep' => false,
                    'Extranjero' => false,
                    'GiroRecep' => false,
                    'Contacto' => false,
                    'CorreoRecep' => false,
                    'DirRecep' => false,
                    'CmnaRecep' => false,
                    'CiudadRecep' => false,
                    'DirPostal' => false,
                    'CmnaPostal' => false,
                    'CiudadPostal' => false,
                ],
                'Totales' => [
                    'TpoMoneda' => false,
                ],
            ],
            'Detalle' => false,
            'SubTotInfo' => false,
            'DscRcgGlobal' => false,
            'Referencia' => false,
            'Comisiones' => false,
        ], $datos);
        // corregir algunos datos que podrían venir malos para no caer por schema
        $datos['Encabezado']['Emisor']['RUTEmisor'] = strtoupper($datos['Encabezado']['Emisor']['RUTEmisor']);
        $datos['Encabezado']['Receptor']['RUTRecep'] = strtoupper($datos['Encabezado']['Receptor']['RUTRecep']);
        $datos['Encabezado']['Receptor']['RznSocRecep'] = substr($datos['Encabezado']['Receptor']['RznSocRecep'], 0, 100);
        if (!empty($datos['Encabezado']['Receptor']['GiroRecep'])) {
            $datos['Encabezado']['Receptor']['GiroRecep'] = substr($datos['Encabezado']['Receptor']['GiroRecep'], 0, 40);
        }
        if (!empty($datos['Encabezado']['Receptor']['Contacto'])) {
            $datos['Encabezado']['Receptor']['Contacto'] = substr($datos['Encabezado']['Receptor']['Contacto'], 0, 80);
        }
        if (!empty($datos['Encabezado']['Receptor']['CorreoRecep'])) {
            $datos['Encabezado']['Receptor']['CorreoRecep'] = substr($datos['Encabezado']['Receptor']['CorreoRecep'], 0, 80);
        }
        if (!empty($datos['Encabezado']['Receptor']['DirRecep'])) {
            $datos['Encabezado']['Receptor']['DirRecep'] = substr($datos['Encabezado']['Receptor']['DirRecep'], 0, 70);
        }
        if (!empty($datos['Encabezado']['Receptor']['CmnaRecep'])) {
            $datos['Encabezado']['Receptor']['CmnaRecep'] = substr($datos['Encabezado']['Receptor']['CmnaRecep'], 0, 20);
        }
        // si existe descuento o recargo global se normalizan
        if (!empty($datos['DscRcgGlobal'])) {
            if (!isset($datos['DscRcgGlobal'][0]))
                $datos['DscRcgGlobal'] = [$datos['DscRcgGlobal']];
            $NroLinDR = 1;
            foreach ($datos['DscRcgGlobal'] as &$dr) {
                $dr = array_merge([
                    'NroLinDR' => $NroLinDR++,
                ], $dr);
            }
        }
        // si existe una o más referencias se normalizan
        if (!empty($datos['Referencia'])) {
            if (!isset($datos['Referencia'][0]))
                $datos['Referencia'] = [$datos['Referencia']];
            $NroLinRef = 1;
            foreach ($datos['Referencia'] as &$r) {
                $r = array_merge([
                    'NroLinRef' => $NroLinRef++,
                    'TpoDocRef' => false,
                    'IndGlobal' => false,
                    'FolioRef' => false,
                    'RUTOtr' => false,
                    'FchRef' => false,
                    'CodRef' => false,
                    'RazonRef' => false,
                ] + (!$this->esBoleta() ? ['FchRef' => date('Y-m-d')] : []), $r);
            }
        }
    }

    private function normalizar_final(array &$datos)
    {
        // normalizar montos de pagos programados
        if (is_array($datos['Encabezado']['IdDoc']['MntPagos'])) {
            $montos = 0;
            if (!isset($datos['Encabezado']['IdDoc']['MntPagos'][0]))
                $datos['Encabezado']['IdDoc']['MntPagos'] = [$datos['Encabezado']['IdDoc']['MntPagos']];
            foreach ($datos['Encabezado']['IdDoc']['MntPagos'] as &$MntPagos) {
                $MntPagos = array_merge([
                    'FchPago' => null,
                    'MntPago' => null,
                    'GlosaPagos' => false,
                ], $MntPagos);
                if ($MntPagos['MntPago']===null) {
                    $MntPagos['MntPago'] = $datos['Encabezado']['Totales']['MntTotal'];
                }
            }
        }
    }

    private function normalizar_33(array &$datos) {
        // Guardar el MntNeto original si existe
        $mntNetoOriginal = isset($datos['Encabezado']['Totales']['MntNeto']) 
            ? $datos['Encabezado']['Totales']['MntNeto'] 
            : false;
    
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'RUTMandante' => false,
                'Receptor' => false,
                'RUTSolicita' => false,
                'Transporte' => false,
                'Totales' => [
                    'MntNeto' => 0, // Colocar $mntNetoOriginal en caso de certificación
                    'MntExe' => false,
                    'TasaIVA' => Sii::getIVA(),
                    'IVA' => 0,
                    'ImptoReten' => false,
                    'CredEC' => false,
                    'MntTotal' => 0,
                ],
                'OtraMoneda' => false,
            ],
        ], $datos);
    
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
    
        // Si el MntNeto original existe, reemplazarlo después de las normalizaciones
        /*if ($mntNetoOriginal !== false) {
            $datos['Encabezado']['Totales']['MntNeto'] = $mntNetoOriginal;
        }*/ //Para certificación
    
        $this->normalizar_agregar_IVA_MntTotal($datos);
    
        return $datos;
    }
    private function normalizar_34(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'RUTSolicita' => false,
                'Totales' => [
                    'MntExe' => false,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
    }

    private function normalizar_39(array &$datos)
{
    // completar con nodos por defecto
    $datos = Arreglo::mergeRecursiveDistinct([
        'Encabezado' => [
            'IdDoc' => false,
            'Emisor' => [
                'RUTEmisor' => false,
                'RznSocEmisor' => false,
                'GiroEmisor' => false,
                'Telefono' => false,
            ],
            'Receptor' => false,
            'Totales' => [
                'MntNeto' => false,
                'MntExe' => false,
                'IVA' => false, // Agregado explícitamente
                'MntTotal' => 0
            ]
        ],
    ], $datos);
    
    // Normalizar el detalle primero para tener los montos base
    $this->normalizar_detalle($datos);
    $this->normalizar_boletas($datos);
    
    // Ajustar los totales
    $totales = &$datos['Encabezado']['Totales'];
    
    // Si hay monto exento, asegurarse que sea un número
    if (!isset($totales['MntExe'])) {
        $totales['MntExe'] = 0;
    }

    // Si tenemos el total pero no el neto ni IVA
    if ($totales['MntTotal']) {
        $montoAfecto = $totales['MntTotal'] - $totales['MntExe'];
        
        if (!isset($totales['MntNeto'])) {
            $totales['MntNeto'] = round($montoAfecto / 1.19);
        }
        
        if (!isset($totales['IVA'])) {
            $totales['IVA'] = $montoAfecto - $totales['MntNeto'];
        }
    }
    // Si tenemos el neto pero no el total
    else if ($totales['MntNeto']) {
        if (!isset($totales['IVA'])) {
            $totales['IVA'] = round($totales['MntNeto'] * 0.19);
        }
        
        $totales['MntTotal'] = 
            $totales['MntNeto'] + 
            $totales['IVA'] + 
            $totales['MntExe'];
    }

    $this->normalizar_aplicar_descuentos_recargos($datos);
}

    private function normalizar_41(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => [
                    'RUTEmisor' => false,
                    'RznSocEmisor' => false,
                    'GiroEmisor' => false,
                ],
                'Receptor' => false,
                'Totales' => [
                    'MntExe' => 0,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_boletas($datos);
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
    }

    private function normalizar_46(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'RUTSolicita' => false,
                'Totales' => [
                    'MntNeto' => 0,
                    'MntExe' => false,
                    'TasaIVA' => Sii::getIVA(),
                    'IVA' => 0,
                    'ImptoReten' => false,
                    'IVANoRet' => false,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
    }

    private function normalizar_52(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'RUTSolicita' => false,
                'Transporte' => false,
                'Totales' => [
                    'MntNeto' => 0,
                    'MntExe' => false,
                    'TasaIVA' => Sii::getIVA(),
                    'IVA' => 0,
                    'ImptoReten' => false,
                    'CredEC' => false,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // si es traslado interno se copia el emisor en el receptor sólo si el
        // receptor no está definido o bien si el receptor tiene RUT diferente
        // al emisor
        if ($datos['Encabezado']['IdDoc']['IndTraslado']==5) {
            if (!$datos['Encabezado']['Receptor'] or $datos['Encabezado']['Receptor']['RUTRecep']!=$datos['Encabezado']['Emisor']['RUTEmisor']) {
                $datos['Encabezado']['Receptor'] = [];
                $cols = [
                    'RUTEmisor'=>'RUTRecep',
                    'RznSoc'=>'RznSocRecep',
                    'GiroEmis'=>'GiroRecep',
                    'Telefono'=>'Contacto',
                    'CorreoEmisor'=>'CorreoRecep',
                    'DirOrigen'=>'DirRecep',
                    'CmnaOrigen'=>'CmnaRecep',
                ];
                foreach ($cols as $emisor => $receptor) {
                    if (!empty($datos['Encabezado']['Emisor'][$emisor])) {
                        $datos['Encabezado']['Receptor'][$receptor] = $datos['Encabezado']['Emisor'][$emisor];
                    }
                }
                if (!empty($datos['Encabezado']['Receptor']['GiroRecep'])) {
                    $datos['Encabezado']['Receptor']['GiroRecep'] = substr($datos['Encabezado']['Receptor']['GiroRecep'], 0, 40);
                }
            }
        }
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
    }

    private function normalizar_56(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'RUTSolicita' => false,
                'Totales' => [
                    'MntNeto' => 0,
                    'MntExe' => 0,
                    'TasaIVA' => Sii::getIVA(),
                    'IVA' => false,
                    'ImptoReten' => false,
                    'IVANoRet' => false,
                    'CredEC' => false,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
        $esDocumentoExento = isset($datos['Referencia']['TpoDocRef']) && in_array($datos['Referencia']['TpoDocRef'], [34, 41]);
        if ($esDocumentoExento) {
            // Si es documento exento, ajustar totales
            $datos['Encabezado']['Totales']['MntNeto'] = 0;
            $datos['Encabezado']['Totales']['TasaIVA'] = false;
            $datos['Encabezado']['Totales']['IVA'] = false;
            $datos['Encabezado']['Totales']['MntTotal'] = $datos['Encabezado']['Totales']['MntExe'];
        } else {
            // Procedimiento normal para agregar IVA
            $this->normalizar_agregar_IVA_MntTotal($datos);
        }

        if (!$datos['Encabezado']['Totales']['MntNeto']) {
            $datos['Encabezado']['Totales']['MntNeto'] = 0;
            $datos['Encabezado']['Totales']['TasaIVA'] = false;
        }     
    }

    private function normalizar_61(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'RUTSolicita' => false,
                'Totales' => [
                    'MntNeto' => 0,
                    'MntExe' => 0,
                    'TasaIVA' => Sii::getIVA(),
                    'IVA' => false,
                    'ImptoReten' => false,
                    'IVANoRet' => false,
                    'CredEC' => false,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
        if (!$datos['Encabezado']['Totales']['MntNeto']) {
            $datos['Encabezado']['Totales']['MntNeto'] = 0;
            $datos['Encabezado']['Totales']['TasaIVA'] = false;
        }
    }

    private function normalizar_110(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'Transporte' => [
                    'Patente' => false,
                    'RUTTrans' => false,
                    'Chofer' => false,
                    'DirDest' => false,
                    'CmnaDest' => false,
                    'CiudadDest' => false,
                    'Aduana' => [
                        'CodModVenta' => false,
                        'CodClauVenta' => false,
                        'TotClauVenta' => false,
                        'CodViaTransp' => false,
                        'NombreTransp' => false,
                        'RUTCiaTransp' => false,
                        'NomCiaTransp' => false,
                        'IdAdicTransp' => false,
                        'Booking' => false,
                        'Operador' => false,
                        'CodPtoEmbarque' => false,
                        'IdAdicPtoEmb' => false,
                        'CodPtoDesemb' => false,
                        'IdAdicPtoDesemb' => false,
                        'Tara' => false,
                        'CodUnidMedTara' => false,
                        'PesoBruto' => false,
                        'CodUnidPesoBruto' => false,
                        'PesoNeto' => false,
                        'CodUnidPesoNeto' => false,
                        'TotItems' => false,
                        'TotBultos' => false,
                        'TipoBultos' => false,
                        'MntFlete' => false,
                        'MntSeguro' => false,
                        'CodPaisRecep' => false,
                        'CodPaisDestin' => false,
                    ],
                ],
                'Totales' => [
                    'TpoMoneda' => null,
                    'MntExe' => 0,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
        $this->normalizar_exportacion($datos);
    }

    private function normalizar_111(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'Transporte' => [
                    'Patente' => false,
                    'RUTTrans' => false,
                    'Chofer' => false,
                    'DirDest' => false,
                    'CmnaDest' => false,
                    'CiudadDest' => false,
                    'Aduana' => [
                        'CodModVenta' => false,
                        'CodClauVenta' => false,
                        'TotClauVenta' => false,
                        'CodViaTransp' => false,
                        'NombreTransp' => false,
                        'RUTCiaTransp' => false,
                        'NomCiaTransp' => false,
                        'IdAdicTransp' => false,
                        'Booking' => false,
                        'Operador' => false,
                        'CodPtoEmbarque' => false,
                        'IdAdicPtoEmb' => false,
                        'CodPtoDesemb' => false,
                        'IdAdicPtoDesemb' => false,
                        'Tara' => false,
                        'CodUnidMedTara' => false,
                        'PesoBruto' => false,
                        'CodUnidPesoBruto' => false,
                        'PesoNeto' => false,
                        'CodUnidPesoNeto' => false,
                        'TotItems' => false,
                        'TotBultos' => false,
                        'TipoBultos' => false,
                        'MntFlete' => false,
                        'MntSeguro' => false,
                        'CodPaisRecep' => false,
                        'CodPaisDestin' => false,
                    ],
                ],
                'Totales' => [
                    'TpoMoneda' => null,
                    'MntExe' => 0,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
        $this->normalizar_exportacion($datos);
    }

    private function normalizar_112(array &$datos)
    {
        // completar con nodos por defecto
        $datos = Arreglo::mergeRecursiveDistinct([
            'Encabezado' => [
                'IdDoc' => false,
                'Emisor' => false,
                'Receptor' => false,
                'Transporte' => [
                    'Patente' => false,
                    'RUTTrans' => false,
                    'Chofer' => false,
                    'DirDest' => false,
                    'CmnaDest' => false,
                    'CiudadDest' => false,
                    'Aduana' => [
                        'CodModVenta' => false,
                        'CodClauVenta' => false,
                        'TotClauVenta' => false,
                        'CodViaTransp' => false,
                        'NombreTransp' => false,
                        'RUTCiaTransp' => false,
                        'NomCiaTransp' => false,
                        'IdAdicTransp' => false,
                        'Booking' => false,
                        'Operador' => false,
                        'CodPtoEmbarque' => false,
                        'IdAdicPtoEmb' => false,
                        'CodPtoDesemb' => false,
                        'IdAdicPtoDesemb' => false,
                        'Tara' => false,
                        'CodUnidMedTara' => false,
                        'PesoBruto' => false,
                        'CodUnidPesoBruto' => false,
                        'PesoNeto' => false,
                        'CodUnidPesoNeto' => false,
                        'TotItems' => false,
                        'TotBultos' => false,
                        'TipoBultos' => false,
                        'MntFlete' => false,
                        'MntSeguro' => false,
                        'CodPaisRecep' => false,
                        'CodPaisDestin' => false,
                    ],
                ],
                'Totales' => [
                    'TpoMoneda' => null,
                    'MntExe' => 0,
                    'MntTotal' => 0,
                ]
            ],
        ], $datos);
        // normalizar datos
        $this->normalizar_detalle($datos);
        $this->normalizar_aplicar_descuentos_recargos($datos);
        $this->normalizar_impuesto_retenido($datos);
        $this->normalizar_agregar_IVA_MntTotal($datos);
        $this->normalizar_exportacion($datos);
    }

    public function normalizar_exportacion(array &$datos)
    {
        // agregar modalidad de venta por defecto si no existe
        if (empty($datos['Encabezado']['Transporte']['Aduana']['CodModVenta']) and (!isset($datos['Encabezado']['IdDoc']['IndServicio']) or !in_array($datos['Encabezado']['IdDoc']['IndServicio'], [3, 4, 5]))) {
            $datos['Encabezado']['Transporte']['Aduana']['CodModVenta'] = 1;
        }
        // quitar campos que no son parte del documento de exportacion
        $datos['Encabezado']['Receptor']['CmnaRecep'] = false;
    }

    private function normalizar_detalle(array &$datos)
    {
        if (!isset($datos['Detalle'][0]))
            $datos['Detalle'] = [$datos['Detalle']];
        $item = 1;
        
        // Check if reference document is an exempt document (34 or 41)
        $esDocumentoExento = isset($datos['Referencia']['TpoDocRef']) && in_array($datos['Referencia']['TpoDocRef'], [34, 41]);

        foreach ($datos['Detalle'] as &$d) {
            $d = array_merge([
                'NroLinDet' => $item++,
                'CdgItem' => false,
                'IndExe' => false,
                'Retenedor' => false,
                'NmbItem' => false,
                'DscItem' => false,
                'QtyRef' => false,
                'UnmdRef' => false,
                'PrcRef' => false,
                'QtyItem' => false,
                'UnmdItem' => false,
                'PrcItem' => false,
                'DescuentoPct' => false,
                'DescuentoMonto' => false,
                'RecargoPct' => false,
                'RecargoMonto' => false,
                'CodImpAdic' => false,
                'MontoItem' => false,
            ], $d);
            // corregir datos
            $d['NmbItem'] = substr($d['NmbItem'], 0, 80);
            if (!empty($d['DscItem'])) {
                $d['DscItem'] = substr($d['DscItem'], 0, 1000);
            }
            // normalizar
            if ($this->esExportacion()) {
                $d['IndExe'] = 1;
            }
            if (is_array($d['CdgItem'])) {
                $d['CdgItem'] = array_merge([
                    'TpoCodigo' => false,
                    'VlrCodigo' => false,
                ], $d['CdgItem']);
                if ($d['Retenedor']===false and $d['CdgItem']['TpoCodigo']=='CPCS') {
                    $d['Retenedor'] = true;
                }
            }
            if ($d['Retenedor']!==false) {
                if (!is_array($d['Retenedor'])) {
                    $d['Retenedor'] = ['IndAgente'=>'R'];
                }
                $d['Retenedor'] = array_merge([
                    'IndAgente' => 'R',
                    'MntBaseFaena' => false,
                    'MntMargComer' => false,
                    'PrcConsFinal' => false,
                ], $d['Retenedor']);
            }
            if ($d['CdgItem']!==false and !is_array($d['CdgItem'])) {
                $d['CdgItem'] = [
                    'TpoCodigo' => empty($d['Retenedor']['IndAgente']) ? 'INT1' : 'CPCS',
                    'VlrCodigo' => $d['CdgItem'],
                ];
            }
            if ($d['PrcItem']) {
                if (!$d['QtyItem'])
                    $d['QtyItem'] = 1;
                if (empty($d['MontoItem'])) {
                    $d['MontoItem'] = $this->round(
                        $d['QtyItem'] * $d['PrcItem'],
                        $datos['Encabezado']['Totales']['TpoMoneda']
                    );
                    // aplicar descuento
                    if ($d['DescuentoPct']) {
                        $d['DescuentoMonto'] = round($d['MontoItem'] * (int)$d['DescuentoPct']/100);
                    }
                    $d['MontoItem'] -= $d['DescuentoMonto'];
                    // aplicar recargo
                    if ($d['RecargoPct']) {
                        $d['RecargoMonto'] = round($d['MontoItem'] * (int)$d['RecargoPct']/100);
                    }
                    $d['MontoItem'] += $d['RecargoMonto'];
                    // aproximar monto del item
                    $d['MontoItem'] = $this->round(
                        $d['MontoItem'], $datos['Encabezado']['Totales']['TpoMoneda']
                    );
                }
            } else if (empty($d['MontoItem'])) {
                $d['MontoItem'] = 0;
            }
            // sumar valor del monto a MntNeto o MntExe según corresponda
            if ($d['MontoItem']) {
                // si no es boleta
                if (!$this->esBoleta()) {
                    // Si el documento de referencia es exento, tratar todo como exento
                    if ($esDocumentoExento) {
                        $datos['Encabezado']['Totales']['MntExe'] += $d['MontoItem'];
                        // Forzar exención
                        $d['IndExe'] = 1;
                    } else {
                        if ((!isset($datos['Encabezado']['Totales']['MntNeto']) or $datos['Encabezado']['Totales']['MntNeto']===false) and isset($datos['Encabezado']['Totales']['MntExe'])) {
                            $datos['Encabezado']['Totales']['MntExe'] += $d['MontoItem'];
                        } else {
                            if (!empty($d['IndExe'])) {
                                if ($d['IndExe']==1) {
                                    $datos['Encabezado']['Totales']['MntExe'] += $d['MontoItem'];
                                }
                            }
                            else {
                                $datos['Encabezado']['Totales']['MntNeto'] += $d['MontoItem'];  //Eliminar "else" en caso de Certificación
                            }
                        }
                    }
                }
                // si es boleta
                else {
                    // si es exento
                    if (!empty($d['IndExe'])) {
                        if ($d['IndExe']==1) {
                            // En vez de usar +=, vamos a hacer la suma y asignación explícita
                            $nuevoMonto = (int)$datos['Encabezado']['Totales']['MntExe'] + (int)$d['MontoItem'];
                            $datos['Encabezado']['Totales']['MntExe'] = $nuevoMonto;
                        }
                    }
                    // agregar al monto total
                    $datos['Encabezado']['Totales']['MntTotal'] += $d['MontoItem'];
                }
            }
        }
    }

    private function normalizar_aplicar_descuentos_recargos(array &$datos)
    {
        if (!empty($datos['DscRcgGlobal'])) {
            if (!isset($datos['DscRcgGlobal'][0]))
                $datos['DscRcgGlobal'] = [$datos['DscRcgGlobal']];
            foreach ($datos['DscRcgGlobal'] as $dr) {
                $dr = array_merge([
                    'NroLinDR' => false,
                    'TpoMov' => false,
                    'GlosaDR' => false,
                    'TpoValor' => false,
                    'ValorDR' => false,
                    'ValorDROtrMnda' => false,
                    'IndExeDR' => false,
                ], $dr);
                if ($this->esExportacion()) {
                    $dr['IndExeDR'] = 1;
                }
                // determinar a que aplicar el descuento/recargo
                if (!isset($dr['IndExeDR']) or $dr['IndExeDR']===false)
                    $monto = $this->getTipo()==39 ? 'MntTotal' : 'MntNeto';
                else if ($dr['IndExeDR']==1)
                    $monto = 'MntExe';
                else if ($dr['IndExeDR']==2)
                    $monto = 'MontoNF';
                // si no hay monto al que aplicar el descuento se omite
                if (empty($datos['Encabezado']['Totales'][$monto]))
                    continue;
                // calcular valor del descuento o recargo
                if ($dr['TpoValor']=='$')
                    $dr['ValorDR'] = $this->round($dr['ValorDR'], $datos['Encabezado']['Totales']['TpoMoneda'], 2);
                $valor =
                    $dr['TpoValor']=='%'
                    ? $this->round(($dr['ValorDR']/100)*$datos['Encabezado']['Totales'][$monto], $datos['Encabezado']['Totales']['TpoMoneda'])
                    : $dr['ValorDR']
                ;
                // aplicar descuento
                if ($dr['TpoMov']=='D') {
                    $datos['Encabezado']['Totales'][$monto] -= $valor;
                }
                // aplicar recargo
                else if ($dr['TpoMov']=='R') {
                    $datos['Encabezado']['Totales'][$monto] += $valor;
                }
                $datos['Encabezado']['Totales'][$monto] = $this->round(
                    $datos['Encabezado']['Totales'][$monto],
                    $datos['Encabezado']['Totales']['TpoMoneda']
                );
                // si el descuento global se aplica a una boleta exenta se copia el valor exento al total
                if ($this->getTipo()==41 and isset($dr['IndExeDR']) and $dr['IndExeDR']==1) {
                    $datos['Encabezado']['Totales']['MntTotal'] = $datos['Encabezado']['Totales']['MntExe'];
                }
            }
        }
    }

    private function normalizar_impuesto_retenido(array &$datos)
    {
        // copiar montos
        $montos = [];
        foreach ($datos['Detalle'] as &$d) {
            if (!empty($d['CodImpAdic'])) {
                if (!isset($montos[$d['CodImpAdic']]))
                    $montos[$d['CodImpAdic']] = 0;
                $montos[$d['CodImpAdic']] += $d['MontoItem'];
            }
        }
        // si hay montos y no hay total para impuesto retenido se arma
        if (!empty($montos)) {
            if (!is_array($datos['Encabezado']['Totales']['ImptoReten'])) {
                $datos['Encabezado']['Totales']['ImptoReten'] = [];
            } else if (!isset($datos['Encabezado']['Totales']['ImptoReten'][0])) {
                $datos['Encabezado']['Totales']['ImptoReten'] = [$datos['Encabezado']['Totales']['ImptoReten']];
            }
        }
        // armar impuesto adicional o retención en los totales
        foreach ($montos as $codigo => $neto) {
            // buscar si existe el impuesto en los totales
            $i = 0;
            foreach ($datos['Encabezado']['Totales']['ImptoReten'] as &$ImptoReten) {
                if ($ImptoReten['TipoImp']==$codigo) {
                    break;
                }
                $i++;
            }
            // si no existe se crea
            if (!isset($datos['Encabezado']['Totales']['ImptoReten'][$i])) {
                $datos['Encabezado']['Totales']['ImptoReten'][] = [
                    'TipoImp' => $codigo
                ];
            }
            // se normaliza
            $datos['Encabezado']['Totales']['ImptoReten'][$i] = array_merge([
                'TipoImp' => $codigo,
                'TasaImp' => ImpuestosAdicionales::getTasa($codigo),
                'MontoImp' => null,
            ], $datos['Encabezado']['Totales']['ImptoReten'][$i]);
            // si el monto no existe se asigna
            if ($datos['Encabezado']['Totales']['ImptoReten'][$i]['MontoImp']===null) {
                $datos['Encabezado']['Totales']['ImptoReten'][$i]['MontoImp'] = round(
                    $neto * $datos['Encabezado']['Totales']['ImptoReten'][$i]['TasaImp']/100
                );
            }
        }
        // quitar los codigos que no existen en el detalle
        if (isset($datos['Encabezado']['Totales']['ImptoReten']) and is_array($datos['Encabezado']['Totales']['ImptoReten'])) {
            $codigos = array_keys($montos);
            $n_impuestos = count($datos['Encabezado']['Totales']['ImptoReten']);
            for ($i=0; $i<$n_impuestos; $i++) {
                if (!in_array($datos['Encabezado']['Totales']['ImptoReten'][$i]['TipoImp'], $codigos)) {
                    unset($datos['Encabezado']['Totales']['ImptoReten'][$i]);
                }
            }
            sort($datos['Encabezado']['Totales']['ImptoReten']);
        }
    }

    private function normalizar_agregar_IVA_MntTotal(array &$datos)
    {
        // agregar IVA y monto total
        if (!empty($datos['Encabezado']['Totales']['MntNeto'])) {
            // Si es boleta electrónica (tipo 39)
            if ($this->getTipo() == 39) {
                // Asegurar el orden correcto de los elementos
                $totales = [
                    'MntNeto' => $datos['Encabezado']['Totales']['MntNeto'],
                    'MntExe' => !empty($datos['Encabezado']['Totales']['MntExe']) ? $datos['Encabezado']['Totales']['MntExe'] : 0,
                    'IVA' => $datos['Encabezado']['Totales']['IVA'],
                    'MntTotal' => $datos['Encabezado']['Totales']['MntTotal'],
                    'MontoNF' => isset($datos['Encabezado']['Totales']['MontoNF']) ? $datos['Encabezado']['Totales']['MontoNF'] : '0',
                    'TotalPeriodo' => $datos['Encabezado']['Totales']['TotalPeriodo'],
                    'SaldoAnterior' => isset($datos['Encabezado']['Totales']['SaldoAnterior']) ? $datos['Encabezado']['Totales']['SaldoAnterior'] : '0',
                    'VlrPagar' => $datos['Encabezado']['Totales']['VlrPagar']
                ];
                
                // Reemplazar los totales existentes con los nuevos ordenados
                $datos['Encabezado']['Totales'] = $totales;
            }
            if ($datos['Encabezado']['IdDoc']['MntBruto']==1) {
                list($datos['Encabezado']['Totales']['MntNeto'], $datos['Encabezado']['Totales']['IVA']) = $this->calcularNetoIVA(
                    $datos['Encabezado']['Totales']['MntNeto'],
                    $datos['Encabezado']['Totales']['TasaIVA']
                );
            } else {
                if (empty($datos['Encabezado']['Totales']['IVA']) and !empty($datos['Encabezado']['Totales']['TasaIVA'])) {
                    $datos['Encabezado']['Totales']['IVA'] = round(
                        $datos['Encabezado']['Totales']['MntNeto']*($datos['Encabezado']['Totales']['TasaIVA']/100)
                    );
                }
            }
            if (empty($datos['Encabezado']['Totales']['MntTotal'])) {
                $datos['Encabezado']['Totales']['MntTotal'] = $datos['Encabezado']['Totales']['MntNeto'];
                if (!empty($datos['Encabezado']['Totales']['IVA']))
                    $datos['Encabezado']['Totales']['MntTotal'] += $datos['Encabezado']['Totales']['IVA'];
                if (!empty($datos['Encabezado']['Totales']['MntExe']))
                    $datos['Encabezado']['Totales']['MntTotal'] += $datos['Encabezado']['Totales']['MntExe'];
            }
        } else {
            if (!$datos['Encabezado']['Totales']['MntTotal'] and !empty($datos['Encabezado']['Totales']['MntExe'])) {
                $datos['Encabezado']['Totales']['MntTotal'] = $datos['Encabezado']['Totales']['MntExe'];
            }
        }
        // si hay impuesto retenido o adicional se contabiliza en el total
        if (!empty($datos['Encabezado']['Totales']['ImptoReten'])) {
            foreach ($datos['Encabezado']['Totales']['ImptoReten'] as &$ImptoReten) {
                // si es retención se resta al total y se traspasaa IVA no retenido
                // en caso que corresponda
                if (ImpuestosAdicionales::getTipo($ImptoReten['TipoImp'])=='R') {
                    $datos['Encabezado']['Totales']['MntTotal'] -= $ImptoReten['MontoImp'];
                    if ($ImptoReten['MontoImp']!=$datos['Encabezado']['Totales']['IVA']) {
                        $datos['Encabezado']['Totales']['IVANoRet'] = $datos['Encabezado']['Totales']['IVA'] - $ImptoReten['MontoImp'];
                    }
                }
                // si es adicional se suma al total
                else if (ImpuestosAdicionales::getTipo($ImptoReten['TipoImp'])=='A' and isset($ImptoReten['MontoImp'])) {
                    $datos['Encabezado']['Totales']['MntTotal'] += $ImptoReten['MontoImp'];
                }
            }
        }
        // si hay impuesto de crédito a constructoras del 65% se descuenta del total
        if (!empty($datos['Encabezado']['Totales']['CredEC'])) {
            if ($datos['Encabezado']['Totales']['CredEC']===true)
                $datos['Encabezado']['Totales']['CredEC'] = round($datos['Encabezado']['Totales']['IVA'] * 0.65); // TODO: mover a constante o método
            $datos['Encabezado']['Totales']['MntTotal'] -= $datos['Encabezado']['Totales']['CredEC'];
        }
    }

    private function normalizar_boletas(array &$datos)
    {
        // cambiar tags de DTE a boleta si se pasaron
        if ($datos['Encabezado']['Emisor']['RznSoc']) {
            $datos['Encabezado']['Emisor']['RznSocEmisor'] = $datos['Encabezado']['Emisor']['RznSoc'];
            $datos['Encabezado']['Emisor']['RznSoc'] = false;
        }
        if ($datos['Encabezado']['Emisor']['GiroEmis']) {
            $datos['Encabezado']['Emisor']['GiroEmisor'] = $datos['Encabezado']['Emisor']['GiroEmis'];
            $datos['Encabezado']['Emisor']['GiroEmis'] = false;
        }
        $datos['Encabezado']['Emisor']['Acteco'] = false;
        $datos['Encabezado']['Emisor']['Telefono'] = false;
        $datos['Encabezado']['Emisor']['CorreoEmisor'] = false;
        $datos['Encabezado']['Emisor']['CdgVendedor'] = false;
        $datos['Encabezado']['Receptor']['GiroRecep'] = false;
        $datos['Encabezado']['Receptor']['CorreoRecep'] = false;
        // quitar otros tags que no son parte de las boletas
        $datos['Encabezado']['IdDoc']['FmaPago'] = false;
        $datos['Encabezado']['IdDoc']['FchCancel'] = false;
        $datos['Encabezado']['IdDoc']['TermPagoGlosa'] = false;
        $datos['Encabezado']['RUTSolicita'] = false;
        // si es boleta no nominativa se deja sólo el RUT en el campo del receptor
        if ($datos['Encabezado']['Receptor']['RUTRecep']=='66666666-6') {
            $datos['Encabezado']['Receptor'] = ['RUTRecep'=>'66666666-6'];
        }
    }

    private function round($valor, $moneda = false, $decimal = 4)
    {
        return (!$moneda or $moneda=='PESO CL') ? (int)round($valor) : (float)round($valor, $decimal);
    }

    public function getEstadoValidacion(array $datos = null)
    {
        /*if (!$this->checkFirma())
            return 1;*/
        if (is_array($datos)) {
            if (isset($datos['RUTEmisor']) and $this->getEmisor()!=$datos['RUTEmisor'])
                return 2;
            if (isset($datos['RUTRecep']) and $this->getReceptor()!=$datos['RUTRecep'])
                return 3;
        }
        return 0;
    }

    public function checkFirma()
    {
        if (!$this->xml)
            return null;
        // obtener firma
        $Signature = $this->xml->documentElement->getElementsByTagName('Signature')->item(0);
        // preparar documento a validar
        $D = $this->xml->documentElement->getElementsByTagName('Documento')->item(0);
        $Documento = new XML();
        $Documento->loadXML($D->C14N());
        $Documento->documentElement->removeAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi');
        $Documento->documentElement->removeAttributeNS('http://www.sii.cl/SiiDte', '');
        $SignedInfo = new XML();
        $SignedInfo->loadXML($Signature->getElementsByTagName('SignedInfo')->item(0)->C14N());
        $SignedInfo->documentElement->removeAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi');
        $DigestValue = $Signature->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $SignatureValue = $Signature->getElementsByTagName('SignatureValue')->item(0)->nodeValue;
        $X509Certificate = $Signature->getElementsByTagName('X509Certificate')->item(0)->nodeValue;
        $X509Certificate = '-----BEGIN CERTIFICATE-----'."\n".wordwrap(trim($X509Certificate), 64, "\n", true)."\n".'-----END CERTIFICATE----- ';
        $valid = openssl_verify($SignedInfo->C14N(), base64_decode($SignatureValue), $X509Certificate) === 1 ? true : false;
        return $valid;
        //return $valid and $DigestValue===base64_encode(sha1($Documento->C14N(), true));
    }

    public function esCedible()
    {
        return !in_array($this->getTipo(), $this->noCedibles);
    }

    public function esBoleta()
    {
        return in_array($this->getTipo(), [39, 41]);
    }

    public function esExportacion()
    {
        return in_array($this->getTipo(), $this->tipos['Exportaciones']);
    }

    public function schemaValidate()
    {
        return true;
    }

    public function verificarDatos()
    {
        if (class_exists('VerificadorDatos')) {
            if (!VerificadorDatos::Dte($this->getDatos())) {
                return false;
            }
        }
        return true;
    }
    public function getEstadoEnvio(FirmaElectronica $Firma, $trackId, $rutEmisor)//Estado Envío
{
    // solicitar token
    $token = Autenticacion::getToken($Firma);
    if (!$token) {
        Log::write('DEBUG_QUERYESTUP', 'Error: No se pudo obtener token');
        return false;
    }
    
    // obtener RUT empresa
    //list($RutCompania, $DvCompania) = explode('-', $this->getEmisor());
    list($rutCompania, $dvCompania) = explode('-', str_replace('.', '', $rutEmisor));
    

    // consultar estado envío
    $xml = Sii::request('QueryEstUp', 'getEstUp', [
        'RutCompania' => $rutCompania,
        'DvCompania' => $dvCompania,
        'TrackId' => $trackId,
        'Token' => $token
    ]);

    // Loguear respuesta
    if ($xml === false) {
        return false;
    }

    $resultado = (array)$xml->xpath('/SII:RESPUESTA/SII:RESP_HDR')[0];

    return $resultado;
}

    public function getEstado(FirmaElectronica $Firma) //Estado DTE 
    {
        // solicitar token
        $token = Autenticacion::getToken($Firma);
        if (!$token)
            return false;
        // consultar estado dte
        $run = $Firma->getID();
        if ($run===false)
            return false;
        list($RutConsultante, $DvConsultante) = explode('-', $run);
        list($RutCompania, $DvCompania) = explode('-', $this->getEmisor());
        list($RutReceptor, $DvReceptor) = explode('-', $this->getReceptor());
        list($Y, $m, $d) = explode('-', $this->getFechaEmision());
        $xml = Sii::request('QueryEstDte', 'getEstDte', [
            'RutConsultante'    => $RutConsultante,
            'DvConsultante'     => $DvConsultante,
            'RutCompania'       => $RutCompania,
            'DvCompania'        => $DvCompania,
            'RutReceptor'       => $RutReceptor,
            'DvReceptor'        => $DvReceptor,
            'TipoDte'           => $this->getTipo(),
            'FolioDte'          => $this->getFolio(),
            'FechaEmisionDte'   => $d.$m.$Y,
            'MontoDte'          => $this->getMontoTotal(),
            'token'             => $token,
        ]);
        // si el estado se pudo recuperar se muestra
        if ($xml===false)
            return false;
        // entregar estado
        return (array)$xml->xpath('/SII:RESPUESTA/SII:RESP_HDR')[0];
    }

    public function getFirmaValue()
    {
    if (!$this->xml) return false;
    
    // Obtener SignatureValue del DTE
    $SignatureValues = $this->xml->getElementsByTagName('SignatureValue');
    if ($SignatureValues->length > 0) {
        return trim($SignatureValues->item(0)->nodeValue);
    }
    
        return false;
    }

    public function getEstadoAvanzado(FirmaElectronica $Firma)//Estado DTE Avanzado
    {
        // solicitar token
        $token = Autenticacion::getToken($Firma);
        if (!$token)
            return false;
        // consultar estado dte
        list($RutEmpresa, $DvEmpresa) = explode('-', $this->getEmisor());
        list($RutReceptor, $DvReceptor) = explode('-', $this->getReceptor());
        list($Y, $m, $d) = explode('-', $this->getFechaEmision());
        $xml = Sii::request('QueryEstDteAv', 'getEstDteAv', [
            'RutEmpresa'       => $RutEmpresa,
            'DvEmpresa'        => $DvEmpresa,
            'RutReceptor'       => $RutReceptor,
            'DvReceptor'        => $DvReceptor,
            'TipoDte'           => $this->getTipo(),
            'FolioDte'          => $this->getFolio(),
            'FechaEmisionDte'   => $d.$m.$Y,
            'MontoDte'          => $this->getMontoTotal(),
            'FirmaDte'          => 'YnERFCVre37sSStS0zlQaqxqp7mfZrZ51AqKHEiL/cHiS2Tz7oGZSS4qPl0BGN6u
                                    xJ5UhkD+Q/egfMg5ags1gdnxoxhX+JE646N4bgmADPMkHS9wgU7BVSktxjPsiJGI
                                    QaD/kUeFp82Gdclkne0CWPle7P6zPoXM5JaUfBifFxl475wZ5WHrLauPlFF7iClb
                                    Dnv+fNHtLkb0zTGWL8JbyEus5s1+I1t8EGKT311//QCde9Uy2i+K/AVFYu7e/CSx
                                    HZD0+ENBlk1s3YrEp3spz7VGK4QY6xK4sCMx2rG40J73GrLMEH9cTW0UR6J9fOLW
                                    hsI2jVVpiXvc4FTD0E1RFg==',
            'token'             => $token,
        ]);
        //str_replace("\n", '', $this->getFirma()['SignatureValue'])
        // si el estado se pudo recuperar se muestra
        if ($xml===false)
            return false;
        // entregar estado
        return (array)$xml->xpath('/SII:RESPUESTA/SII:RESP_BODY')[0];
    }

}
