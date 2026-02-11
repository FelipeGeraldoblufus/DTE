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

// Array de receptores de prueba
$receptores = [
    [
        'RUTRecep' => '60803000-K',
        'RznSocRecep' => 'SERVICIO DE IMPUESTOS INTERNOS',
        'GiroRecep' => 'ADMINISTRACION PUBLICA',
        'DirRecep' => 'TEATINOS 120',
        'CmnaRecep' => 'SANTIAGO',
        'CiudadRecep' => 'SANTIAGO'
    ],
    [
        'RUTRecep' => '66666666-6',
        'RznSocRecep' => 'ENTIDAD PUBLICA DE PRUEBA 1',
        'GiroRecep' => 'ADMINISTRACION PUBLICA',
        'DirRecep' => 'CALLE EJEMPLO 100',
        'CmnaRecep' => 'SANTIAGO',
        'CiudadRecep' => 'SANTIAGO'
    ],
    [
        'RUTRecep' => '66666666-6',
        'RznSocRecep' => 'ENTIDAD PUBLICA DE PRUEBA 2',
        'GiroRecep' => 'ADMINISTRACION PUBLICA',
        'DirRecep' => 'CALLE EJEMPLO 200',
        'CmnaRecep' => 'SANTIAGO',
        'CiudadRecep' => 'SANTIAGO'
    ],
    [
        'RUTRecep' => '66666666-6',
        'RznSocRecep' => 'ENTIDAD PUBLICA DE PRUEBA 3',
        'GiroRecep' => 'ADMINISTRACION PUBLICA',
        'DirRecep' => 'CALLE EJEMPLO 300',
        'CmnaRecep' => 'SANTIAGO',
        'CiudadRecep' => 'SANTIAGO'
    ],
    [
        'RUTRecep' => '66666666-6',
        'RznSocRecep' => 'ENTIDAD PUBLICA DE PRUEBA 4',
        'GiroRecep' => 'ADMINISTRACION PUBLICA',
        'DirRecep' => 'CALLE EJEMPLO 400',
        'CmnaRecep' => 'SANTIAGO',
        'CiudadRecep' => 'SANTIAGO'
    ]
];

function createBasicDTE($caso, $folio, $tipo, $receptor) {
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
            'Receptor' => $receptor,
            'Totales' => []
        ],
        'Detalle' => [],
        'Referencia' => [
            [
                'NroLinRef' => 1,
                'TpoDocRef' => 'SET',
                'FolioRef' => $folio,
                'FchRef' => $fecha_emision,
                'RazonRef' => 'CASO ' . $caso
            ]
        ]
    ];
}
$allDTEs = [];
$currentFolio33 = $folioActual33;
$currentFolio56 = $folioActual56;
$currentFolio61 = $folioActual61;

/// 1. Factura por Desarrollo de Sistema ERP
$caso1 = createBasicDTE('SIM-001', $currentFolio33++, '33', $receptores[0]);
$caso1['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Desarrollo Sistema ERP - Módulo Contabilidad',
        'DscItem' => 'Incluye análisis, desarrollo e implementación',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 2500000,
        'MontoItem' => 2500000
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Capacitación Usuarios',
        'DscItem' => '20 horas de capacitación para 10 usuarios',
        'QtyItem' => 20,
        'UnmdItem' => 'HRS',
        'PrcItem' => 25000,
        'MontoItem' => 500000
    ]
];
$caso1['Encabezado']['Totales'] = [
    'MntNeto' => 3000000,
    'TasaIVA' => 19,
    'IVA' => 570000,
    'MntTotal' => 3570000
];

// 2. Factura por Mantención Mensual
$caso2 = createBasicDTE('SIM-002', $currentFolio33++, '33', $receptores[1]);
$caso2['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Mantención Mensual Sistema ERP',
        'DscItem' => 'Periodo: Febrero 2025',
        'QtyItem' => 1,
        'UnmdItem' => 'MES',
        'PrcItem' => 450000,
        'MontoItem' => 450000
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Soporte Técnico Premium',
        'DscItem' => 'Soporte 24/7',
        'QtyItem' => 1,
        'UnmdItem' => 'MES',
        'PrcItem' => 250000,
        'DescuentoPct' => 10,
        'DescuentoMonto' => 25000,
        'MontoItem' => 225000
    ]
];
$caso2['Encabezado']['Totales'] = [
    'MntNeto' => 675000,
    'TasaIVA' => 19,
    'IVA' => 128250,
    'MntTotal' => 803250
];

// 3. Factura por Consultoría
$caso3 = createBasicDTE('SIM-003', $currentFolio33++, '33', $receptores[2]);
$caso3['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Consultoría Transformación Digital',
        'DscItem' => 'Diagnóstico y plan de implementación',
        'QtyItem' => 80,
        'UnmdItem' => 'HRS',
        'PrcItem' => 35000,
        'MontoItem' => 2800000
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Informe Técnico',
        'DscItem' => 'Documentación y recomendaciones',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 500000,
        'MontoItem' => 500000
    ]
];
$caso3['Encabezado']['Totales'] = [
    'MntNeto' => 3300000,
    'TasaIVA' => 19,
    'IVA' => 627000,
    'MntTotal' => 3927000
];

// 4. Factura por Licencias de Software
$caso4 = createBasicDTE('SIM-004', $currentFolio33++, '33', $receptores[3]);
$caso4['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Licencia ERP Enterprise',
        'DscItem' => 'Licencia anual - 20 usuarios',
        'QtyItem' => 20,
        'UnmdItem' => 'UND',
        'PrcItem' => 180000,
        'MontoItem' => 3600000
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Módulo Adicional Reportería',
        'DscItem' => 'Módulo Business Intelligence',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 1200000,
        'DescuentoPct' => 15,
        'DescuentoMonto' => 180000,
        'MontoItem' => 1020000
    ]
];
$caso4['DscRcgGlobal'] = [
    [
        'NroLinDR' => 1,
        'TpoMov' => 'D',
        'GlosaDR' => 'Descuento por Volumen',
        'TpoValor' => '%',
        'ValorDR' => 5
    ]
];
$caso4['Encabezado']['Totales'] = [
    'MntNeto' => 4389000,
    'TasaIVA' => 19,
    'IVA' => 833910,
    'MntTotal' => 5222910
];

// 5. Nota de Crédito por Corrección
$caso5 = createBasicDTE('SIM-005', $currentFolio61++, '61', $receptores[0]); // NC relacionada al caso1
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
        'RazonRef' => 'CASO SIM-005'
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
    'MntTotal' => 0 // Ok
];

// 6. Factura por Servicios Múltiples
$caso6 = createBasicDTE('SIM-006', $currentFolio33++, '33', $receptores[4]);
$caso6['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Desarrollo API Personalizada',
        'DscItem' => 'Integración con sistema legacy',
        'QtyItem' => 120,
        'UnmdItem' => 'HRS',
        'PrcItem' => 28000,
        'MontoItem' => 3360000
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Configuración Servidor',
        'DscItem' => 'Instalación y configuración en cloud',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 850000,
        'MontoItem' => 850000
    ],
    [
        'NroLinDet' => 3,
        'NmbItem' => 'Documentación Técnica',
        'DscItem' => 'Manual de usuario y técnico',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 450000,
        'MontoItem' => 450000,
        'IndExe' => 1
    ]
];
$caso6['Encabezado']['Totales'] = [
    'MntNeto' => 4210000,
    'TasaIVA' => 19,
    'IVA' => 799900,
    'MntTotal' => 5459900
];

// 7. Factura por Servicios Recurrentes
$caso7 = createBasicDTE('SIM-007', $currentFolio33++, '33', $receptores[1]);
$caso7['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Hosting Cloud Premium',
        'DscItem' => 'Servicio mensual - Marzo 2025',
        'QtyItem' => 1,
        'UnmdItem' => 'MES',
        'PrcItem' => 350000,
        'MontoItem' => 350000
    ], 
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Backup Automatizado',
        'DscItem' => 'Backup diario con retención 30 días',
        'QtyItem' => 1,
        'UnmdItem' => 'MES',
        'PrcItem' => 180000,
        'MontoItem' => 180000
    ],
    [
        'NroLinDet' => 3,
        'NmbItem' => 'Monitoreo 24/7',
        'DscItem' => 'Monitoreo continuo de servidores',
        'QtyItem' => 1,
        'UnmdItem' => 'MES',
        'PrcItem' => 250000,
        'MontoItem' => 250000
    ]
];
$caso7['Encabezado']['Totales'] = [
    'MntNeto' => 780000,
    'TasaIVA' => 19,
    'IVA' => 148200,
    'MntTotal' => 928200
];

// 8. Nota de Débito por Cobros Adicionales
$caso8 = createBasicDTE('SIM-008', $currentFolio56++, '56', $receptores[1]); // ND relacionada al caso7
$caso8['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Horas Adicionales Soporte',
        'DscItem' => 'Excedente de horas de soporte mensual',
        'QtyItem' => 8,
        'UnmdItem' => 'HRS',
        'PrcItem' => 35000,
        'MontoItem' => 280000
    ]
];
$caso8['Referencia'] = [
    [
        'NroLinRef' => 1,
        'TpoDocRef' => 'SET',
        'FolioRef' => $currentFolio56 - 1,
        'FchRef' => $fecha_emision,
        'RazonRef' => 'CASO SIM-008'
    ],
    [
        'NroLinRef' => 2,
        'TpoDocRef' => '33',
        'FolioRef' => $caso7['Encabezado']['IdDoc']['Folio'],
        'FchRef' => $fecha_emision,
        'CodRef' => '3',
        'RazonRef' => 'Cargo por horas adicionales'
    ]
];
$caso8['Encabezado']['Totales'] = [
    'MntNeto' => 280000,
    'TasaIVA' => 19,
    'IVA' => 53200,
    'MntTotal' => 333200
];

// 9. Factura con Ítems Exentos y Afectos
$caso9 = createBasicDTE('SIM-009', $currentFolio33++, '33', $receptores[2]);
$caso9['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Desarrollo Móvil iOS',
        'DscItem' => 'Desarrollo de aplicación iOS',
        'QtyItem' => 160,
        'UnmdItem' => 'HRS',
        'PrcItem' => 32000,
        'MontoItem' => 5120000
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Certificado Apple Developer',
        'DscItem' => 'Licencia anual desarrollador',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 99990,
        'MontoItem' => 99990,
        'IndExe' => 1
    ],
    [
        'NroLinDet' => 3,
        'NmbItem' => 'Servicio Publicación AppStore',
        'DscItem' => 'Gestión de publicación',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 150000,
        'MontoItem' => 150000,
        'IndExe' => 1
    ]
];
$caso9['Encabezado']['Totales'] = [
    'MntNeto' => 5120000,
    'TasaIVA' => 19,
    'IVA' => 972800,
    'MntTotal' => 6342790
];

// 10. Nota de Crédito por Descuento Comercial
$caso10 = createBasicDTE('SIM-010', $currentFolio61++, '61', $receptores[2]); // NC relacionada al caso9
$caso10['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Desarrollo Móvil iOS',
        'DscItem' => 'Desarrollo de aplicación iOS',
        'QtyItem' => 160,
        'UnmdItem' => 'HRS',
        'PrcItem' => 32000,
        'MontoItem' => 5120000
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'Certificado Apple Developer',
        'DscItem' => 'Licencia anual desarrollador',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 99990,
        'MontoItem' => 99990,
        'IndExe' => 1
    ],
    [
        'NroLinDet' => 3,
        'NmbItem' => 'Servicio Publicación AppStore',
        'DscItem' => 'Gestión de publicación',
        'QtyItem' => 1,
        'UnmdItem' => 'UND',
        'PrcItem' => 150000,
        'MontoItem' => 150000,
        'IndExe' => 1
    ]
];
$caso10['Referencia'] = [
    [
        'NroLinRef' => 1,
        'TpoDocRef' => 'SET',
        'FolioRef' => $currentFolio61 - 1,
        'FchRef' => $fecha_emision,
        'RazonRef' => 'CASO SIM-010'
    ],
    [
        'NroLinRef' => 2,
        'TpoDocRef' => '33',
        'FolioRef' => $caso9['Encabezado']['IdDoc']['Folio'],
        'FchRef' => $fecha_emision,
        'CodRef' => '1',
        'RazonRef' => 'ANULA FACTURA'
    ]
];
$caso10['Encabezado']['Totales'] = [
    'MntNeto' => 5120000,
    'TasaIVA' => 19,
    'IVA' => 972800,
    'MntTotal' => 6342790
];
// 11. Nota de Débito por Ajuste de Precios
$caso11 = createBasicDTE('SIM-011', $currentFolio56++, '56', $receptores[3]); // ND relacionada al caso4
$caso11['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'Ajuste Precio Licencias Adicionales',
        'DscItem' => 'Cargo por usuarios adicionales no contemplados',
        'QtyItem' => 5,
        'UnmdItem' => 'UND',
        'PrcItem' => 180000,
        'MontoItem' => 900000
    ]
];
$caso11['Referencia'] = [
    [
        'NroLinRef' => 1,
        'TpoDocRef' => 'SET',
        'FolioRef' => $currentFolio56 - 1,
        'FchRef' => $fecha_emision,
        'RazonRef' => 'CASO SIM-011'
    ],
    [
        'NroLinRef' => 2,
        'TpoDocRef' => '33',
        'FolioRef' => $caso4['Encabezado']['IdDoc']['Folio'],
        'FchRef' => $fecha_emision,
        'CodRef' => '3',
        'RazonRef' => 'Cargo por licencias adicionales'
    ]
];
$caso11['Encabezado']['Totales'] = [
    'MntNeto' => 900000,
    'TasaIVA' => 19,
    'IVA' => 171000,
    'MntTotal' => 1071000
];

// Agregar todos los DTEs al arreglo
$allDTEs = [];
$allDTEs[] = new Dte($caso1);
$allDTEs[] = new Dte($caso2);
$allDTEs[] = new Dte($caso3);
$allDTEs[] = new Dte($caso4);
$allDTEs[] = new Dte($caso5);
$allDTEs[] = new Dte($caso6);
$allDTEs[] = new Dte($caso7);
$allDTEs[] = new Dte($caso8);
$allDTEs[] = new Dte($caso9);
$allDTEs[] = new Dte($caso10);
$allDTEs[] = new Dte($caso11);

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
$carpeta = $Directorio->creaDirectorio(__ROOT__.'/archives/xml/SetSimulacion');
$archivoXML = $carpeta.'SetSimulacion_'.date('Ymd').'_11111111-1.xml';
file_put_contents($archivoXML, $xml);