<?php
require_once '../../../../config.php';

// Configuración inicial - Configurar credenciales desde base de datos o variables de entorno
$rutaFirma = '';
$passFirma = '';
$rutaFolios33 = BASEURL.'client/folio/33/33.xml';
$rutaFolios61 = BASEURL.'client/folio/61/61.xml';
$rutaFolios56 = BASEURL.'client/folio/56/56.xml';
$Folio33 = new Folio();
$Folio56 = new Folio();
$Folio61 = new Folio();
$TipoFolio33 = new TipoFolio();
$TipoFolio56 = new TipoFolio();
$TipoFolio61 = new TipoFolio();
$TipoDTE33 = 33;
$datosTipoFolio33 = $TipoFolio33->numeroTipoFolio($TipoDTE33);
$datosFolio33 = $Folio33->getFolio($TipoDTE33);
$TipoDTE56 = 56;
$datosTipoFolio56 = $TipoFolio56->numeroTipoFolio($TipoDTE56);
$datosFolio56 = $Folio56->getFolio($TipoDTE56);
$TipoDTE61 = 61;
$datosTipoFolio61 = $TipoFolio61->numeroTipoFolio($TipoDTE61);
$datosFolio61 = $Folio61->getFolio($TipoDTE61);

$folioActual33 = isset($datosFolio33['folio_actual']) ? $datosFolio33['folio_actual'] : 1;
$folioActual56 = isset($datosFolio56['folio_actual']) ? $datosFolio56['folio_actual'] : 1;
$folioActual61 = isset($datosFolio61['folio_actual']) ? $datosFolio61['folio_actual'] : 1;


$Firma = new FirmaElectronica($rutaFirma, $passFirma);
if (!$Firma) {
    die('Error al cargar la firma electrónica');
}


$Folios33 = new Folios(file_get_contents($rutaFolios33));
$Folios61 = new Folios(file_get_contents($rutaFolios61));
$Folios56 = new Folios(file_get_contents($rutaFolios56));

if (!$Folios33 || !$Folios61 || !$Folios56) {
    die('Error al cargar los folios');
}

$fecha_emision = date('Y-m-d');
$fecha_resol = '2025-01-03'; 

function createBasicDTE($caso, $folio, $tipo) {
    global $fecha_emision;
    return [
        'Encabezado' => [
            'IdDoc' => [
                'TipoDTE' => $tipo,
                'Folio' => $folio,
                'FchEmis' => $fecha_emision
            ],
            'Emisor' => [
                'RUTEmisor' => '11.111.111-1',
                'RznSoc' => '',
                'GiroEmis' => 'SERVICIOS INFORMATICOS Y ACTIVIDADES DE PROGRAMACION',
                'Acteco' => '726000',
                'DirOrigen' => 'AV. PRUEBA 1000 OF. 100',
                'CmnaOrigen' => 'PROVIDENCIA',
                'CiudadOrigen' => 'Santiago'
            ],
            'Receptor' => [
                'RUTRecep' => '60803000-K',
                'RznSocRecep' => 'SERVICIO DE IMPUESTOS INTERNOS',
                'GiroRecep' => 'INSTITUCION FISCALIZADORA',
                'DirRecep' => 'TEATINOS 120',
                'CmnaRecep' => 'SANTIAGO',
                'CiudadRecep' => 'SANTIAGO'
            ],
            'Totales' => []
        ],
        'Detalle' => [],
        'Referencia' => [
            [
                'NroLinRef' => 1,
                'TpoDocRef' => 'SET',
                'FolioRef' => $folio,
                'FchRef' => $fecha_emision,
                'RazonRef' => 'CASO '. $caso
            ]
        ]
    ];
};
$allDTEs = [];
$currentFolio33 = $folioActual33;
$currentFolio56 = $folioActual56;
$currentFolio61 = $folioActual61;

// CASO 4159682-1
$caso1 = createBasicDTE('4159682-1', $currentFolio33++, '33');
$caso1['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Cajón AFECTO',
        'QtyItem' => 128,
        'PrcItem' => 1207,
        'MontoItem' => 154496
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Relleno AFECTO',
        'QtyItem' => 55,
        'PrcItem' => 1953,
        'MontoItem' => 107415
    ]
];
$caso1['Encabezado']['Totales'] = [
    'MntNeto' => 261911,
    'IVA' => 49763,
    'MntTotal' => 311674
];
$allDTEs[] = new Dte($caso1);

// CASO 4159682-2
$caso2 = createBasicDTE('4159682-2', $currentFolio33++, '33');
$caso2['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Pañuelo AFECTO',
        'QtyItem' => 293,
        'PrcItem' => 2363,
        'DescuentoPct' => 4,
        'DescuentoMonto' => 27694, //27694,36
        'MontoItem' => 664665 //dscto aplicado
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'ITEM 2 AFECTO',
        'QtyItem' => 221,
        'PrcItem' => 1425,
        'DescuentoPct' => 7,
        'DescuentoMonto' => 22045,
        'MontoItem' => 292880
    ]
];
$caso2['Encabezado']['Totales'] = [
    'MntNeto' => 957545,
    'TasaIVA' => 19,
    'IVA' => 181934,
    'MntTotal' => 1139479
];
$allDTEs[] = new Dte($caso2);

// CASO 4159682-3
$caso3 = createBasicDTE('4159682-3', $currentFolio33++, '33');
$caso3['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Pintura B&W AFECTO',
        'QtyItem' => 26,
        'PrcItem' => 2538,
        'MontoItem' => 65988
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'ITEM 2 AFECTO',
        'QtyItem' => 158,
        'PrcItem' => 3049,
        'MontoItem' => 481742
    ],
    [
        'NroLinDet' => 3,
        'NmbItem' => 'ITEM 3 SERVICIO EXENTO',
        'QtyItem' => 1,
        'PrcItem' => 34770,
        'MontoItem' => 34770,
        'IndExe' => 1
    ]
];
$caso3['Encabezado']['Totales'] = [
    'MntNeto' => 547730,
    'TasaIVA' => 19,
    'IVA' => 104069,
    'MntTotal' => 686569

];
$allDTEs[] = new Dte($caso3);

// CASO 4159682-4
$caso4 = createBasicDTE('4159682-4', $currentFolio33++, '33');
$caso4['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'ITEM 1 AFECTO',
        'QtyItem' => 118,
        'PrcItem' => 2141,
        'MontoItem' => 252638
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'ITEM 2 AFECTO',
        'QtyItem' => 50,
        'PrcItem' => 2046,
        'MontoItem' => 102300
    ],
    [
        'NroLinDet' => 3,
        'NmbItem' => 'ITEM 3 SERVICIO EXENTO',
        'QtyItem' => 2,
        'PrcItem' => 6775,
        'MontoItem' => 13550,
        'IndExe' => 1
    ]
];

$caso4['DscRcgGlobal'] = [
    [
        'NroLinDR' => 1,
        'TpoMov' => 'D',
        'GlosaDR' => 'DESCUENTO GLOBAL ITEMS AFECTOS',
        'TpoValor' => '%',
        'ValorDR' => 8,
        'ValorDROtrMnda' => false,
        'IndExeDR' => false
    ]
];

$caso4['Encabezado']['Totales'] = [
    'MntNeto' => 326543, 
    'TasaIVA' => 19,
    'IVA' => 62043,
    'MntTotal' => 402136
];
$allDTEs[] = new Dte($caso4);

// CASO 4159682-5 - Nota de crédito que corrige giro del receptor
$caso5 = createBasicDTE('4159682-5', $currentFolio61++, '61');
$caso5['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'CORRIGE GIRO DEL RECEPTOR',
        'MontoItem' => 0
    ],
];
$caso5['Referencia'] = [
    [
        'NroLinRef' => 1,
        'TpoDocRef' => 'SET', 
        'FolioRef' => $currentFolio61 - 1,
        'FchRef' => $fecha_emision,
        'RUTOtr' => false,
        'RazonRef' => 'CASO 4159682-5'
    ],
    [
        'NroLinRef' => 2,
        'TpoDocRef' => '33', 
        'FolioRef' => $caso1['Encabezado']['IdDoc']['Folio'],
        'FchRef' => $fecha_emision,
        'RUTOtr' => false,
        'CodRef' => '2',
        'RazonRef' => 'CORRIGE GIRO DEL RECEPTOR'
    ]
];

$caso5['Encabezado']['Totales'] = [
    'MntTotal' => 0 //OK
];
$allDTEs[] = new Dte($caso5);


// CASO 4159682-6 - Nota de crédito por devolución
$caso6 = createBasicDTE('4159682-6', $currentFolio61++, '61');

// Referencias - deben ir antes que el detalle según schema
$caso6['Referencia'] = [
    [
        'NroLinRef' => 1,
        'TpoDocRef' => 'SET',
        'FolioRef' => $currentFolio61 - 1,
        'RUTOtr' => false,
        'FchRef' => $fecha_emision, 
        'RazonRef' => 'CASO 4159682-6'
    ],
    [
        'NroLinRef' => 2,
        'TpoDocRef' => '33',
        'FolioRef' => $caso2['Encabezado']['IdDoc']['Folio'], 
        'RUTOtr' => false,
        'FchRef' => $fecha_emision,
        'CodRef' => '3', 
        'RazonRef' => 'DEVOLUCION DE MERCADERIAS'
    ]
];

// Detalle debe seguir el orden exacto según el schema
$caso6['Detalle'] = [
    [
        'NroLinDet' => 1,
        'CdgItem' => [
            'TpoCodigo' => 'INT1',
            'VlrCodigo' => 'PañueloA1'
        ],
        'IndExe' => false,
        'NmbItem' => 'Pañuelo AFECTO',
        'DscItem' => false,
        'QtyItem' => 107,
        'UnmdItem' => 'Und',
        'PrcItem' => 2363,
        'DescuentoPct' => 4,
        'DescuentoMonto' => 10114,
        'RecargoPct' => false,
        'RecargoMonto' => false,
        'CodImpAdic' => false,
        'MontoItem' => 242727
    ],
    [
        'NroLinDet' => 2,
        'CdgItem' => [
            'TpoCodigo' => 'INT1',
            'VlrCodigo' => 'ITEM2A'
        ],
        'IndExe' => false,
        'NmbItem' => 'ITEM 2 AFECTO',
        'DscItem' => false,
        'QtyItem' => 150,
        'UnmdItem' => 'Und',
        'PrcItem' => 1425,
        'DescuentoPct' => 7,
        'DescuentoMonto' => 14963,
        'RecargoPct' => false,
        'RecargoMonto' => false,
        'CodImpAdic' => false,
        'MontoItem' => 198787
    ]
];

// Totales
$caso6['Encabezado']['Totales'] = [
    'MntNeto' => 441514,
    'MntExe' => false,
    'TasaIVA' => 19,
    'IVA' => 83888,
    'MntTotal' => 525402
];

$allDTEs[] = new Dte($caso6);

// CASO 4159682-7 - Nota de crédito que anula factura
$caso7 = createBasicDTE('4159682-7', $currentFolio61++, '61');
$caso7['Referencia'] = [
    [
        'NroLinRef' => 1,
        'TpoDocRef' => 'SET',
        'FolioRef' => $currentFolio61 - 1,
        'FchRef' => $fecha_emision,
        'RazonRef' => 'CASO 4159682-7'
    ],
    [
        'NroLinRef' => 2,
        'TpoDocRef' => '33',
        'FolioRef' => $caso3['Encabezado']['IdDoc']['Folio'],
        'FchRef' => $fecha_emision,
        'CodRef' => '1',
        'RazonRef' => 'ANULA FACTURA'
    ]

];
$caso7['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Pintura B&W AFECTO',
        'QtyItem' => 26,
        'PrcItem' => 2538,
        'MontoItem' => 65988
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'ITEM 2 AFECTO',
        'QtyItem' => 158,
        'PrcItem' => 3049,
        'MontoItem' => 481742
    ],
    [
        'NroLinDet' => 3,
        'NmbItem' => 'ITEM 3 SERVICIO EXENTO',
        'QtyItem' => 1,
        'PrcItem' => 34770,
        'MontoItem' => 34770,
        'IndExe' => 1
    ]
];
$caso7['Encabezado']['Totales'] = [
    'MntNeto' => 547730,
    'TasaIVA' => 19,
    'IVA' => 104069,
    'MntTotal' => 686569
];

$allDTEs[] = new Dte($caso7);

// CASO 4159682-8 - Nota de débito que anula nota de crédito
$caso8 = createBasicDTE('4159682-8', $currentFolio56++, '56');
$caso8['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'CORRECION DATO',
        'MontoItem' => 0
    ]
];
$caso8['Referencia'] = [
    [
        'NroLinRef' => 1,
        'TpoDocRef' => 'SET',
        'FolioRef' => $currentFolio56 - 1,
        'FchRef' => $fecha_emision,
        'RazonRef' => 'CASO 4159682-8'
    ],
    [
        'NroLinRef' => 2,
        'TpoDocRef' => '61',
        'FolioRef' => $caso5['Encabezado']['IdDoc']['Folio'],
        'FchRef' => $fecha_emision,
        'CodRef' => '1',
        'RazonRef' => 'ANULA NOTA DE CREDITO ELECTRONICA'
    ]

];
$caso8['Encabezado']['Totales'] = [
    'MntTotal' => 0
];
$allDTEs[] = new Dte($caso8);

// Generar EnvioDTE
$EnvioDte = new EnvioDte();

// Agregar DTEs al envío
foreach ($allDTEs as $dte) {
    if ($dte->getTipo() == 33) {
        $dte->timbrar($Folios33);
    } else if ($dte->getTipo() == 61) {
        $dte->timbrar($Folios61);
    } else {
        $dte->timbrar($Folios56);
    }
    $dte->firmar($Firma);
    $EnvioDte->agregar($dte);
}

// Generar carátula del envío
$caratula = [
    'RutEmisor' => '11.111.111-1',
    'RutEnvia' => $Firma->getID(),
    'RutReceptor' => '60803000-K',
    'FchResol' => $fecha_resol,
    'NroResol' => 0,
    'TmstFirmaEnv' => date('Y-m-d\TH:i:s')
];

$EnvioDte->setCaratula($caratula);
$EnvioDte->setFirma($Firma);

// Generar XML final
$xml = $EnvioDte->generar();

// Guardar XML
$Directorio = new Directorio();
$carpeta = $Directorio->creaDirectorio(__ROOT__.'/archives/xml/SetPruebas');
$archivoXML = $carpeta.'SetPruebasBasico_11111111-1.xml';
file_put_contents($archivoXML, $xml);