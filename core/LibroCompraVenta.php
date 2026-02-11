<?php
/**
 * Clase que representa el envío de un Libro de Compra o Venta
 *  - Libros simplificados: https://www.sii.cl/DJI/DJI_Formato_XML.html
 */
class LibroCompraVenta extends Libro
{

    private $simplificado = false; ///< Indica si el libro es simplificado o no
    private $datos = null; ///< Arreglo con los datos del XML del libro

    private $total_default = [
        'TpoDoc' => null,
        'TotDoc' => 0,
        'TotAnulado' => false,
        'TotOpExe' => false,
        'TotMntExe' => 0,
        'TotMntNeto' => 0,
        'TotMntIVA' => 0,
        'TotIVAPropio' => false,
        'TotIVATerceros' => false,
        'TotLey18211' => false,
        'TotMntActivoFijo' => false,
        'TotMntIVAActivoFijo' => false,
        'TotIVANoRec' => false,
        'TotOpIVAUsoComun' => false,
        'TotIVAUsoComun' => false,
        'FctProp' => false,
        'TotCredIVAUsoComun' => false,
        'TotIVAFueraPlazo' => false,
        'TotOtrosImp' => false,
        'TotIVARetTotal' => false,
        'TotIVARetParcial' => false,
        'TotImpSinCredito' => false,
        'TotMntTotal' => 0,
        'TotIVANoRetenido' => false,
        'TotMntNoFact' => false,
        'TotMntPeriodo' => false,
    ]; ///< Campos para totales

    public function __construct($simplificado = false)
    {
        $this->simplificado = $simplificado;
    }

    public function getID()
    {
        if ($this->datos===null) {
            $this->datos = $this->toArray();
        }
        return !empty($this->datos['LibroCompraVenta']['EnvioLibro']['@attributes']['ID']) ? $this->datos['LibroCompraVenta']['EnvioLibro']['@attributes']['ID'] : false;
    }

    public function agregar(array $detalle, $normalizar = true)
    {
        if ($normalizar)
            $this->normalizarDetalle($detalle);
        if (!$detalle['TpoDoc'])
            return false;
        $this->detalles[] = $detalle;
        return true;
    }

    private function normalizarDetalle(array &$detalle)
    {
        // agregar nodos (esto para mantener orden del XML)
        $detalle = array_merge([
            'TpoDoc' => false,
            'Emisor' => false,
            'IndFactCompra' => false,
            'NroDoc' => false,
            'Anulado' => false,
            'Operacion' => false,
            'TpoImp' => false,
            'TasaImp' => false,
            'NumInt' => false,
            'IndServicio' => false,
            'IndSinCosto' => false,
            'FchDoc' => false,
            'CdgSIISucur' => false,
            'RUTDoc' => false,
            'RznSoc' => false,
            'Extranjero' => false,
            'TpoDocRef' => false,
            'FolioDocRef' => false,
            'MntExe' => false,
            'MntNeto' => false,
            'MntIVA' => false,
            'MntActivoFijo' => false,
            'MntIVAActivoFijo' => false,
            'IVANoRec' => false,
            'IVAUsoComun' => false,
            'IVAFueraPlazo' => false,
            'IVAPropio' => false,
            'IVATerceros' => false,
            'Ley18211' => false,
            'OtrosImp' => false,
            'MntSinCred' => false,
            'IVARetTotal' => false,
            'IVARetParcial' => false,
            'CredEC' => false,
            'DepEnvase' => false,
            'Liquidaciones' => false,
            'MntTotal' => false,
            'IVANoRetenido' => false,
            'MntNoFact' => false,
            'MntPeriodo' => false,
            'PsjNac' => false,
            'PsjInt' => false,
            'TabPuros' => false,
            'TabCigarrillos' => false,
            'TabElaborado' => false,
            'ImpVehiculo' => false,
        ], $detalle);
        // largo campos
        $detalle['RznSoc'] = substr($detalle['RznSoc'], 0, 50);
        // calcular valores que no se hayan entregado
        if (isset($detalle['FctProp'])) {
            if ($detalle['IVAUsoComun']===false)
                $detalle['IVAUsoComun'] = round($detalle['MntNeto'] * ($detalle['TasaImp']));
                unset($detalle['MntIVA']);
        } else if (!$detalle['MntIVA'] and !is_array($detalle['IVANoRec']) and $detalle['TasaImp'] and $detalle['MntNeto']) {
            $detalle['MntIVA'] = round($detalle['MntNeto'] * ($detalle['TasaImp']));
        }
        // si el monto total es 0 pero no se asigno neto ni exento se coloca
        if ($detalle['MntExe']===false and $detalle['MntNeto']===false and $detalle['MntTotal']===0) {
            $detalle['MntNeto'] = 0;
        }
        // normalizar IVA no recuperable
        if (!empty($detalle['IVANoRec'])) {
            if (!isset($detalle['IVANoRec'][0]))
                $detalle['IVANoRec'] = [$detalle['IVANoRec']];
            // si son múltiples iva no recuperable se arma arreglo real
            if (strpos($detalle['IVANoRec'][0]['CodIVANoRec'], ',')) {
                $CodIVANoRec = explode(',', $detalle['IVANoRec'][0]['CodIVANoRec']);
                $MntIVANoRec = explode(',', $detalle['IVANoRec'][0]['MntIVANoRec']);
                $detalle['IVANoRec'] = [];
                $n_inr = count($CodIVANoRec);
                for ($i=0; $i<$n_inr; $i++) {
                    $detalle['IVANoRec'][] = [
                        'CodIVANoRec' => $CodIVANoRec[$i],
                        'MntIVANoRec' => $MntIVANoRec[$i],
                    ];
                }
            }
        }
        // normalizar otros impuestos
        if (!empty($detalle['OtrosImp'])) {
            if (!isset($detalle['OtrosImp'][0]))
                $detalle['OtrosImp'] = [$detalle['OtrosImp']];
            // si son múltiples impuestos se arma arreglo real
            if (strpos($detalle['OtrosImp'][0]['CodImp'], ',')) {
                $CodImp = explode(',', $detalle['OtrosImp'][0]['CodImp']);
                $TasaImp = explode(',', $detalle['OtrosImp'][0]['TasaImp']);
                $MntImp = explode(',', $detalle['OtrosImp'][0]['MntImp']);
                $detalle['OtrosImp'] = [];
                $n_impuestos = count($CodImp);
                for ($i=0; $i<$n_impuestos; $i++) {
                    $detalle['OtrosImp'][] = [
                        'CodImp' => $CodImp[$i],
                        'TasaImp' => !empty($TasaImp[$i]) ? $TasaImp[$i] : false,
                        'MntImp' => $MntImp[$i],
                    ];
                }
            }
            // calcular y agregar IVA no retenido si corresponde
            $retenido = ImpuestosAdicionales::getRetenido($detalle['OtrosImp']);
            if ($retenido) {
                // si el iva retenido es total
                if ($retenido == $detalle['MntIVA']) {
                    $detalle['IVARetTotal'] = $retenido;
                }
                // si el iva retenido es parcial
                else {
                    $detalle['IVARetParcial'] = $retenido;
                    $detalle['IVANoRetenido'] = $detalle['MntIVA'] - $retenido;
                }
            }
        }
        // calcular monto total si no se especificó
        if ($detalle['MntTotal']===false) {
            // calcular monto total inicial
            $detalle['MntTotal'] = $detalle['MntExe'] + $detalle['MntNeto'] + (int)$detalle['MntIVA'];
            // agregar iva no recuperable al monto total
            if (!empty($detalle['IVANoRec'])) {
                foreach ($detalle['IVANoRec'] as $IVANoRec) {
                    $detalle['MntTotal'] += $IVANoRec['MntIVANoRec'];
                }
            }
            // agregar iva de uso común al monto total
            if (isset($detalle['FctProp'])) {
                $detalle['MntTotal'] += $detalle['IVAUsoComun'];
            }
            // descontar del total la retención total de IVA
            if (!empty($detalle['OtrosImp'])) {
                foreach ($detalle['OtrosImp'] as $OtrosImp) {
                    if (ImpuestosAdicionales::getTipo($OtrosImp['CodImp'])=='R') {
                        $detalle['MntTotal'] -= $OtrosImp['MntImp'];
                    }
                }
            }
            // agregar otro montos e impuestos al total
            $detalle['MntTotal'] += (int)$detalle['MntSinCred'] + (int)$detalle['TabPuros'] + (int)$detalle['TabCigarrillos'] + (int)$detalle['TabElaborado'] + (int)$detalle['ImpVehiculo'];
        }
        // si no hay no hay monto neto, no se crean campos para IVA
        if ($detalle['MntNeto']===false) {
            $detalle['MntNeto'] = $detalle['TasaImp'] = $detalle['MntIVA'] = false;
        }
        // si el código de sucursal no existe se pone a falso, esto básicamente
        // porque algunos sistemas podrían usar 0 cuando no hay CdgSIISucur
        if (!$detalle['CdgSIISucur'])
            $detalle['CdgSIISucur'] = false;
    }

    public function agregarComprasCSV($archivo, $separador = ';')
    {
        $data = CSV::read($archivo);
        $n_data = count($data);
        $detalles = [];
        for ($i=1; $i<$n_data; $i++) {
            // detalle genérico
            $detalle = [
                'TpoDoc' => $data[$i][0],
                'NroDoc' => $data[$i][1],
                'RUTDoc' => $data[$i][2],
                'TasaImp' => !empty($data[$i][3]) ? $data[$i][3] : false,
                'RznSoc' => !empty($data[$i][4]) ? $data[$i][4] : false,
                'TpoImp' => !empty($data[$i][5]) ? $data[$i][5] : 1,
                'FchDoc' => $data[$i][6],
                'Anulado' => !empty($data[$i][7]) ? $data[$i][7] : false,
                'MntExe' => !empty($data[$i][8]) ? $data[$i][8] : false,
                'MntNeto' => !empty($data[$i][9]) ? $data[$i][9] : false,
                'MntIVA' => !empty($data[$i][10]) ? $data[$i][10] : 0,
                'IVANoRec' => false, // 11 y 12
                'IVAUsoComun' => !empty($data[$i][13]) ? $data[$i][13] : false,
                'OtrosImp' => false, // 14 al 16
                'MntSinCred' => !empty($data[$i][17]) ? $data[$i][17] : false,
                'MntActivoFijo' => !empty($data[$i][18]) ? $data[$i][18] : false,
                'MntIVAActivoFijo' => !empty($data[$i][19]) ? $data[$i][19] : false,
                'IVANoRetenido' => !empty($data[$i][20]) ? $data[$i][20] : false,
                'TabPuros' => !empty($data[$i][21]) ? $data[$i][21] : false,
                'TabCigarrillos' => !empty($data[$i][22]) ? $data[$i][22] : false,
                'TabElaborado' => !empty($data[$i][23]) ? $data[$i][23] : false,
                'ImpVehiculo' => !empty($data[$i][24]) ? $data[$i][24] : false,
                'CdgSIISucur' => !empty($data[$i][25]) ? $data[$i][25] : false,
                'NumInt' => !empty($data[$i][26]) ? $data[$i][26] : false,
                'Emisor' => !empty($data[$i][27]) ? $data[$i][27] : false,
                //'MntTotal' => !empty($data[$i][28]) ? $data[$i][28] : false,
                //'FctProp' => !empty($data[$i][29]) ? $data[$i][29] : false,
            ];
            // agregar código y monto de iva no recuperable si existe
            if (!empty($data[$i][11])) {
                $detalle['IVANoRec'] = [
                    'CodIVANoRec' => $data[$i][11],
                    'MntIVANoRec' => !empty($data[$i][12]) ? $data[$i][12] : round($detalle['MntNeto'] * ($detalle['TasaImp']/100)),
                ];
            }
            // agregar código y monto de otros impuestos
            if (!empty($data[$i][14]) and (!empty($data[$i][15]) or !empty($data[$i][16]))) {
                $detalle['OtrosImp'] = [
                    'CodImp' => $data[$i][14],
                    'TasaImp' => !empty($data[$i][15]) ? $data[$i][15] : 0,
                    'MntImp' => !empty($data[$i][16]) ? $data[$i][16] : round($detalle['MntNeto'] * ($data[$i][15]/100)),
                ];
            }
            // si hay monto total se agrega
            if (!empty($data[$i][28])) {
                $detalle['MntTotal'] = $data[$i][28];
            }
            // si hay factor de proporcionalidad se agrega
            if (!empty($data[$i][29])) {
                $detalle['FctProp'] = $data[$i][29];
            }
            // agregar a los detalles
            $this->agregar($detalle);
        }
    }

    public function agregarVentasCSV($archivo, $separador = ';')
    {
        $data = CSV::read($archivo);
        $n_data = count($data);
        $detalles = [];
        for ($i=1; $i<$n_data; $i++) {
            // detalle genérico
            $detalle = [
                'TpoDoc' => $data[$i][0],
                'NroDoc' => $data[$i][1],
                'RUTDoc' => $data[$i][2],
                'TasaImp' => !empty($data[$i][3]) ? $data[$i][3] : false,
                'RznSoc' => !empty($data[$i][4]) ? $data[$i][4] : false,
                'FchDoc' => $data[$i][5],
                'Anulado' => !empty($data[$i][6]) ? 'A' : false,
                'MntExe' => !empty($data[$i][7]) ? $data[$i][7] : false,
                'MntNeto' => !empty($data[$i][8]) ? $data[$i][8] : false,
                'MntIVA' => !empty($data[$i][9]) ? $data[$i][9] : 0,
                'IVAFueraPlazo' => !empty($data[$i][10]) ? $data[$i][10] : false,
                'IVAPropio' => !empty($data[$i][14]) ? $data[$i][14] : false,
                'IVATerceros' => !empty($data[$i][15]) ? $data[$i][15] : false,
                'IVARetTotal' => !empty($data[$i][16]) ? $data[$i][16] : false,
                'IVARetParcial' => !empty($data[$i][17]) ? $data[$i][17] : false,
                'IVANoRetenido' => !empty($data[$i][18]) ? $data[$i][18] : false,
                'Ley18211' => !empty($data[$i][19]) ? $data[$i][19] : false,
                'CredEC' => !empty($data[$i][20]) ? $data[$i][20] : false,
                'TpoDocRef' => !empty($data[$i][21]) ? $data[$i][21] : false,
                'FolioDocRef' => !empty($data[$i][22]) ? $data[$i][22] : false,
                'DepEnvase' => !empty($data[$i][23]) ? $data[$i][23] : false,
                'MntNoFact' => !empty($data[$i][24]) ? $data[$i][24] : false,
                'MntPeriodo' => !empty($data[$i][25]) ? $data[$i][25] : false,
                'PsjNac' => !empty($data[$i][26]) ? $data[$i][26] : false,
                'PsjInt' => !empty($data[$i][27]) ? $data[$i][27] : false,
                'IndServicio' => !empty($data[$i][30]) ? $data[$i][30] : false,
                'IndSinCosto' => !empty($data[$i][31]) ? $data[$i][31] : false,
                'CdgSIISucur' => !empty($data[$i][36]) ? $data[$i][36] : false,
                'NumInt' => !empty($data[$i][37]) ? $data[$i][37] : false,
                'Emisor' => !empty($data[$i][38]) ? 1 : false,
            ];
            // agregar código y monto de otros impuestos
            if (!empty($data[$i][11])) {
                $detalle['OtrosImp'] = [
                    'CodImp' => $data[$i][11],
                    'TasaImp' => !empty($data[$i][12]) ? $data[$i][12] : false,
                    'MntImp' => !empty($data[$i][13]) ? $data[$i][13] : round($detalle['MntNeto'] * ($data[$i][12]/100)),
                ];
            }
            // agregar datos extranjeros
            if (!empty($data[$i][28]) or !empty($data[$i][29])) {
                $detalle['Extranjero'] = [
                    'NumId' => !empty($data[$i][28]) ? $data[$i][28] : false,
                    'Nacionalidad' => !empty($data[$i][29]) ? $data[$i][29] : false,
                ];
            }
            // agregar datos de liquidaciones
            if (!empty($data[$i][32])) {
                $detalle['Liquidaciones'] = [
                    'RutEmisor' => $data[$i][32],
                    'ValComNeto' => !empty($data[$i][33]) ? $data[$i][33] : false,
                    'ValComExe' => !empty($data[$i][34]) ? $data[$i][34] : false,
                    'ValComIVA' => !empty($data[$i][35]) ? $data[$i][35] : false,
                ];
            }
            // si hay monto total se agrega
            if (!empty($data[$i][39])) {
                $detalle['MntTotal'] = $data[$i][39];
            }
            // agregar a los detalles
            $this->agregar($detalle);
        }
    }

    public function setCaratula(array $caratula)
    {
        $this->caratula = array_merge([
            'RutEmisorLibro' => false,
            'RutEnvia' => isset($this->Firma) ? $this->Firma->getID() : false,
            'PeriodoTributario' => date('Y-m'),
            'FchResol' => false,
            'NroResol' => false,
            'TipoOperacion' => 'VENTA',
            'TipoLibro' => 'MENSUAL',
            'TipoEnvio' => 'TOTAL',
            'FolioNotificacion' => false,
        ], $caratula);
        if ($this->caratula['TipoEnvio']=='ESPECIAL')
            $this->caratula['FolioNotificacion'] = null;
        $this->id = 'LIBRO_'.$this->caratula['TipoOperacion'].'_'.str_replace('-', '', $this->caratula['RutEmisorLibro']).'_'.str_replace('-', '', $this->caratula['PeriodoTributario']);
    }

    public function generar($incluirDetalle = true)
    {
        // si ya se había generado se entrega directamente
        if ($this->xml_data)
            return $this->xml_data;
        // generar totales de DTE y sus montos
        $TotalesPeriodo = $this->getResumen();
        $ResumenPeriodo = $TotalesPeriodo ? ['TotalesPeriodo'=>$TotalesPeriodo] : false;
        // generar XML del envío
        $xmlEnvio = (new XML())->generate([
            'LibroCompraVenta' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => $this->simplificado ? 'http://www.sii.cl/SiiDte LibroCVS_v10.xsd' : 'http://www.sii.cl/SiiDte LibroCV_v10.xsd',
                    'version' => '1.0',
                ],
                'EnvioLibro' => [
                    '@attributes' => [
                        'ID' => $this->id,
                    ],
                    'Caratula' => $this->caratula,
                    'ResumenPeriodo' => $ResumenPeriodo,
                    'Detalle' => $incluirDetalle ? $this->getDetalle() : false,
                    'TmstFirma' => date('Y-m-d\TH:i:s'),
                ],
            ]
        ])->saveXML();
        // firmar XML del envío y entregar
        $this->xml_data = (!$this->simplificado and $this->Firma) ? $this->Firma->signXML($xmlEnvio, '#'.$this->id, 'EnvioLibro', true) : $xmlEnvio;
        return $this->xml_data;
    }

    public function setResumen($resumen)
    {
        // verificar que se haya pasado el tipo de documento y total como mínimo
        foreach ($resumen as $tipo) {
            if (!isset($tipo['TpoDoc']) or !isset($tipo['TotDoc'])) {
                return false;
            }
        }
        // asignar resumen
        $this->resumen = [];
        foreach ($resumen as $tipo) {
            $this->resumen[$tipo['TpoDoc']] = $tipo;
        }
    }

    public function getResumen()
    {
        $totales = [];
        // agregar resumen de detalles
        foreach ($this->detalles as &$d) {
            if (!isset($totales[$d['TpoDoc']])) {
                $totales[$d['TpoDoc']] = array_merge($this->total_default, ['TpoDoc'=>$d['TpoDoc']]);
            }
            // contabilizar cantidad de documentos y montos (exento, neto, iva y total)
            $totales[$d['TpoDoc']]['TotDoc']++;
            $totales[$d['TpoDoc']]['TotMntExe'] += $d['MntExe'];
            $totales[$d['TpoDoc']]['TotMntNeto'] += $d['MntNeto'];
            if (!empty($d['MntIVA'])) {
                $totales[$d['TpoDoc']]['TotMntIVA'] += $d['MntIVA'];
            }
            $totales[$d['TpoDoc']]['TotMntTotal'] += $d['MntTotal'];
            // contabilizar documentos anulados
            if (!empty($d['Anulado']) and $d['Anulado']=='A')
                $totales[$d['TpoDoc']]['TotAnulado']++;
            // si hay activo fijo se contabiliza
            if (!empty($d['MntActivoFijo']))
                $totales[$d['TpoDoc']]['TotMntActivoFijo'] += $d['MntActivoFijo'];
            if (!empty($d['MntIVAActivoFijo']))
                $totales[$d['TpoDoc']]['TotMntIVAActivoFijo'] += $d['MntIVAActivoFijo'];
            // si hay iva no recuperable se contabiliza
            if (!empty($d['IVANoRec'])) {
                foreach ($d['IVANoRec'] as $IVANoRec) {
                    if (!isset($totales[$d['TpoDoc']]['TotIVANoRec'][$IVANoRec['CodIVANoRec']])) {
                        $totales[$d['TpoDoc']]['TotIVANoRec'][$IVANoRec['CodIVANoRec']] = [
                            'CodIVANoRec' => $IVANoRec['CodIVANoRec'],
                            'TotOpIVANoRec' => 0,
                            'TotMntIVANoRec' => 0,
                        ];
                    }
                    $totales[$d['TpoDoc']]['TotIVANoRec'][$IVANoRec['CodIVANoRec']]['TotOpIVANoRec']++;
                    $totales[$d['TpoDoc']]['TotIVANoRec'][$IVANoRec['CodIVANoRec']]['TotMntIVANoRec'] += $IVANoRec['MntIVANoRec'];
                }
            }
            // si hay IVA de uso común se contabiliza
            if (!empty($d['FctProp'])) {
                $totales[$d['TpoDoc']]['TotIVAUsoComun'] += $d['IVAUsoComun'];
                $totales[$d['TpoDoc']]['FctProp'] = $d['FctProp']/100;
                $totales[$d['TpoDoc']]['TotCredIVAUsoComun'] += roundInteger($d['IVAUsoComun'] * ($d['FctProp']/100));
                //$totales[$d['TpoDoc']]['TotMntTotal'] -= 2269;
                $totales[$d['TpoDoc']]['TotOpIVAUsoComun'] += 1;
                unset($d['FctProp']); // se quita el factor de proporcionalidad del detalle ya que no es parte del XML
            }
            // contabilizar IVA fuera de plazo
            if (!empty($d['IVAFueraPlazo']))
                $totales[$d['TpoDoc']]['TotIVAFueraPlazo'] += $d['IVAFueraPlazo'];
            // si hay otro tipo de impuesto se contabiliza
            if (!empty($d['OtrosImp'])) {
                foreach ($d['OtrosImp'] as $OtrosImp) {
                    if (!isset($totales[$d['TpoDoc']]['TotOtrosImp'][$OtrosImp['CodImp']])) {
                        $totales[$d['TpoDoc']]['TotOtrosImp'][$OtrosImp['CodImp']] = [
                            'CodImp' => $OtrosImp['CodImp'],
                            'TotMntImp' => 0,
                        ];
                    }
                    $totales[$d['TpoDoc']]['TotOtrosImp'][$OtrosImp['CodImp']]['TotMntImp'] += $OtrosImp['MntImp'];
                }
            }
            // contabilizar impuesto sin derecho a crédito
            if (!empty($d['MntSinCred']))
                $totales[$d['TpoDoc']]['TotImpSinCredito'] += $d['MntSinCred'];
            // contabilidad IVA retenido total
            if (!empty($d['IVARetTotal']))
                $totales[$d['TpoDoc']]['TotIVARetTotal'] += $d['IVARetTotal'];
            // contabilizar IVA retenido parcial
            if (!empty($d['IVARetParcial']))
                $totales[$d['TpoDoc']]['TotIVARetParcial'] += $d['IVARetParcial'];
            // contabilizar IVA no retenido
            if (!empty($d['IVANoRetenido']))
                $totales[$d['TpoDoc']]['TotIVANoRetenido'] += $d['IVANoRetenido'];
        }
        
        // agregar resumenes pasados que no se hayan generado por los detalles
        foreach ($this->resumen as $tipo => $resumen) {
            if (!isset($totales[$tipo])) {
                $totales[$tipo] = array_merge($this->total_default, $resumen);
            }
        }
        // entregar resumen
        ksort($totales);
        return $totales;
    }

    public function getResumenManual()
    {
        $manual = [];
        if (isset($this->toArray()['LibroCompraVenta']['EnvioLibro']['ResumenPeriodo']['TotalesPeriodo'])) {
            $totales = $this->toArray()['LibroCompraVenta']['EnvioLibro']['ResumenPeriodo']['TotalesPeriodo'];
            if (!isset($totales[0]))
                $totales = [$totales];
            foreach ($totales as $total) {
                if (isset($total['TpoDoc']) and in_array($total['TpoDoc'], [35, 38, 48])) {
                    $manual[$total['TpoDoc']] = array_merge($this->total_default, $total);
                }
            }
        }
        return $manual;
    }

    public function getResumenBoletas()
    {
        $manual = [];
        if (isset($this->toArray()['LibroCompraVenta']['EnvioLibro']['ResumenPeriodo']['TotalesPeriodo'])) {
            $totales = $this->toArray()['LibroCompraVenta']['EnvioLibro']['ResumenPeriodo']['TotalesPeriodo'];
            if (!isset($totales[0]))
                $totales = [$totales];
            foreach ($totales as $total) {
                if (in_array($total['TpoDoc'], [39, 41])) {
                    $manual[$total['TpoDoc']] = array_merge($this->total_default, $total);
                }
            }
        }
        return $manual;
    }

    private function getDetalle()
    {
        if ($this->caratula['TipoOperacion']=='VENTA') {
            $omitir = [35, 38, 39, 41, 105, 500, 501, 919, 920, 922, 924];
            $detalles = [];
            foreach ($this->detalles as $d) {
                if (!in_array($d['TpoDoc'], $omitir)) {
                    $detalles[] = $d;
                }
            }
            return $detalles;
        }
        return $this->detalles;
    }

    public function getCompras()
    {
        $detalle = [];
        foreach ($this->detalles as $d) {
            // armar iva no recuperable
            $iva_no_recuperable_codigo = [];
            $iva_no_recuperable_monto = [];
            foreach ((array)$d['IVANoRec'] as $inr) {
                $iva_no_recuperable_codigo[] = $inr['CodIVANoRec'];
                $iva_no_recuperable_monto[] = $inr['MntIVANoRec'];
            }
            // armar impuestos adicionales
            $impuesto_adicional_codigo = [];
            $impuesto_adicional_tasa = [];
            $impuesto_adicional_monto = [];
            foreach ((array)$d['OtrosImp'] as $ia) {
                $impuesto_adicional_codigo[] = $ia['CodImp'];
                $impuesto_adicional_tasa[] = $ia['TasaImp'];
                $impuesto_adicional_monto[] = $ia['MntImp'];
            }
            // armar detalle
            $detalle[] = [
                (int)$d['TpoDoc'],
                (int)$d['NroDoc'],
                $d['RUTDoc'],
                (int)$d['TasaImp'],
                $d['RznSoc'],
                $d['TpoImp']!==false ? $d['TpoImp'] : 1,
                $d['FchDoc'],
                $d['Anulado']!==false ? $d['Anulado'] : null,
                $d['MntExe']!==false ? $d['MntExe'] : null,
                $d['MntNeto']!==false ? $d['MntNeto'] : null,
                (int)$d['MntIVA'],
                $iva_no_recuperable_codigo ? implode(',', $iva_no_recuperable_codigo) : null,
                $iva_no_recuperable_monto ? implode(',', $iva_no_recuperable_monto) : null,
                $d['IVAUsoComun']!==false ? $d['IVAUsoComun'] : null,
                $impuesto_adicional_codigo ? implode(',', $impuesto_adicional_codigo) : null,
                $impuesto_adicional_tasa ? implode(',', $impuesto_adicional_tasa) : null,
                $impuesto_adicional_monto ? implode(',', $impuesto_adicional_monto) : null,
                $d['MntSinCred']!==false ? $d['MntSinCred'] : null,
                $d['MntActivoFijo']!==false ? $d['MntActivoFijo'] : null,
                $d['MntIVAActivoFijo']!==false ? $d['MntIVAActivoFijo'] : null,
                $d['IVANoRetenido']!==false ? $d['IVANoRetenido'] : null,
                $d['TabPuros']!==false ? $d['TabPuros'] : null,
                $d['TabCigarrillos']!==false ? $d['TabCigarrillos'] : null,
                $d['TabElaborado']!==false ? $d['TabElaborado'] : null,
                $d['ImpVehiculo']!==false ? $d['ImpVehiculo'] : null,
                $d['CdgSIISucur']!==false ? $d['CdgSIISucur'] : null,
                $d['NumInt']!==false ? $d['NumInt'] : null,
                $d['Emisor']!==false ? $d['Emisor'] : null,
                $d['MntTotal']!==false ? $d['MntTotal'] : null,
                (isset($d['FctProp']) and $d['FctProp']!==false) ? $d['FctProp'] : null,
            ];
        }
        return $detalle;
    }

}
