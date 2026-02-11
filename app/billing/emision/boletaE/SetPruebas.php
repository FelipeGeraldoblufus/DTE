<?php

require_once '../../../../config.php';
// Configurar credenciales de firma desde base de datos o variables de entorno
$rutaFirma = '';
$passFirma = '';
$Folio = new Folio();
$Firma = new FirmaElectronica($rutaFirma, $passFirma);
$rutaFolios = BASEURL.'client/folio/39/39.xml';
$Folios = new Folios(file_get_contents($rutaFolios));
$TipoFolio = new TipoFolio();
$TipoDTE = 39;
$datosTipoFolio = $TipoFolio->numeroTipoFolio($TipoDTE);
$datosFolio = $Folio->getFolio($TipoDTE);

// Extraer valores específicos
$tipoNumero = isset($datosTipoFolio['tipo_numero']) ? $datosTipoFolio['tipo_numero'] : $TipoDTE;
$folioActual = isset($datosFolio['folio_actual']) ? $datosFolio['folio_actual'] : 1;

// Function to create basic DTE structure
function createBasicDTE($caso, $folio) {
    return [
        'Encabezado' => [
            'IdDoc' => [
                'TipoDTE' => '39',
                'Folio' => $folio,
                'FchEmis' => date('Y-m-d'),
                'IndServicio' => '3'
            ],
            'Emisor' => [
                'RUTEmisor' => '11.111.111-1',
                'RznSocEmisor' => '',
                'GiroEmisor' => 'SERVICIOS INFORMÁTICOS Y ACTIVIDADES DE ASESORAMIENTO EMPRESARIAL',
                'DirOrigen' => 'AV. PRUEBA 1000 OF. 100',
                'CmnaOrigen' => 'PROVIDENCIA',
                'CiudadOrigen' => 'SANTIAGO'
            ],
            'Receptor' => [
            'RUTRecep' => '66666666-6',
            'RznSocRecep' => 'EMPRESA DE PRUEBA LTDA',
            'DirRecep' => 'CALLE EJEMPLO 999',
            'CmnaRecep' => 'SANTIAGO'
            ],
            'Totales' => []
        ],
        'Detalle' => [],
        'Referencia' => [
            [
                'NroLinRef' => 1,
                'TpoDocRef' => 'SET',
                'FolioRef' => '0',
                'CodRef' => 'SET',
                'RazonRef' => 'CASO-' . $caso
            ]
        ]
    ];
}

// Create DTEs for all cases
$allDTEs = [];
$currentFolio = $folioActual;

// CASO 1
$caso1 = createBasicDTE(1, $currentFolio++);
$items1 = [
    ['name' => 'Cambio de aceite', 'qty' => 1, 'price' => 19900],
    ['name' => 'Alineacion y balanceo', 'qty' => 1, 'price' => 9900]
];
$totalAmount1 = 0;

foreach($items1 as $idx => $item) {
    $precio = $item['price'];
    $cantidad = $item['qty'];
    $montoItem = $precio * $cantidad;
    $totalAmount1 += $montoItem;
    
    $caso1['Detalle'][] = [
        'NroLinDet' => $idx + 1,
        'NmbItem' => $item['name'],
        'QtyItem' => $cantidad,
        'PrcItem' => $precio,
        'MontoItem' => $montoItem
    ];
}

$allDTEs[] = new Dte($caso1);

// CASO 2
$caso2 = createBasicDTE(2, $currentFolio++);
$baseAmount2 = 120 * 17;
$caso2['Detalle'][] = [
    'NroLinDet' => 1,
    'NmbItem' => 'Papel de regalo',
    'QtyItem' => 17,
    'PrcItem' => 120,
    'MontoItem' => $baseAmount2
];
$allDTEs[] = new Dte($caso2);

// CASO 3
$caso3 = createBasicDTE(3, $currentFolio++);
$items3 = [
    ['name' => 'Sandwic', 'qty' => 2, 'price' => 1500],
    ['name' => 'Bebida', 'qty' => 2, 'price' => 550]
];
$totalAmount3 = 0;
foreach($items3 as $idx => $item) {
    $baseAmount = $item['price'] * $item['qty'];
    $totalAmount3 += $baseAmount;
    $caso3['Detalle'][] = [
        'NroLinDet' => $idx + 1,
        'NmbItem' => $item['name'],
        'QtyItem' => $item['qty'],
        'PrcItem' => $item['price'],
        'MontoItem' => $baseAmount
    ];
}
$allDTEs[] = new Dte($caso3);

// CASO 4 (Mixed affected and exempt)
$caso4 = createBasicDTE(4, $currentFolio++);
$baseAmount4_1 = 1590 * 8;
$baseAmount4_2 = 1000 * 2;
$caso4['Detalle'] = [
    [
        'NroLinDet' => 1,
        'NmbItem' => 'item afecto 1',
        'QtyItem' => 8,
        'PrcItem' => 1590,
        'MontoItem' => $baseAmount4_1
    ],
    [
        'NroLinDet' => 2,
        'NmbItem' => 'item exento 2',
        'QtyItem' => 2,
        'PrcItem' => 1000,
        'MontoItem' => $baseAmount4_2,
        'IndExe' => 1
    ]
];
$allDTEs[] = new Dte($caso4);

// CASO 5 (with unit of measure)
$caso5 = createBasicDTE(5, $currentFolio++);
$baseAmount5 = 700 * 5;
$caso5['Detalle'][] = [
    'NroLinDet' => 1,
    'NmbItem' => 'Arroz',
    'QtyItem' => 5,
    'UnmdItem' => 'Kg',
    'PrcItem' => 700,
    'MontoItem' => $baseAmount5
];
$allDTEs[] = new Dte($caso5);

// Create EnvioBoleta and process all DTEs
$EnvioBoleta = new EnvioBoleta();
foreach($allDTEs as $dte) {
    $dte->timbrar($Folios);
    $dte->firmar($Firma);
    $EnvioBoleta->agregar($dte);
}

// Set carátula for all DTEs
$caratula = [    
    'RutEmisor' => '11.111.111-1',
    'RutEnvia' => $Firma->getID(),
    'RutReceptor' => '60803000-K',
    'FchResol' => '2025-01-03',
    'NroResol' => 0,
    'TmstFirmaEnv' => date('Y-m-d\TH:i:s'),
    'SubTotDTE' => [
        'TpoDTE' => '39',
        'NroDTE' => count($allDTEs)
    ]
];

$EnvioBoleta->setCaratula($caratula);
$EnvioBoleta->setFirma($Firma);

// Generate and save XML
$xml = $EnvioBoleta->generar();
$Directorio = new Directorio();
$carpeta = $Directorio->creaDirectorio(__ROOT__.'/archives/xml/39');

$archivoXML = $carpeta . 'D0C' . $tipoNumero . $folioActual . '.xml';

file_put_contents($archivoXML, $xml);


// Obtener token
$token = Autenticacion::getToken($Firma);
if (!$token) {
    die("Error: No se pudo obtener el token de autenticación");
}

// Preparar datos para el envío
$rutEmisor = '11.111.111-1';
$rutEnvia = $Firma->getID();

list($rutSender, $dvSender) = explode('-', str_replace('.', '', $rutEnvia));
list($rutCompany, $dvCompany) = explode('-', str_replace('.', '', $rutEmisor));

// Crear archivo temporal
$file = sys_get_temp_dir().'/dte_'.md5(microtime().$token.$xml).'.xml';
file_put_contents($file, $xml);

$data = [
    'rutSender' => $rutSender,
    'dvSender' => $dvSender,
    'rutCompany' => $rutCompany,
    'dvCompany' => $dvCompany,
    'archivo' => curl_file_create(
        $file,
        'application/xml',
        basename($file)
    ),
];

// Configurar y ejecutar CURL
$curl = curl_init();
$header = [
    'User-Agent: Mozilla/4.0 (compatible; PROG 1.0;)',
    'Referer: ',
    'Cookie: TOKEN='.$token,
];

curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => $header,
    CURLOPT_URL => 'https://maullin.sii.cl/cgi_dte/UPL/DTEUpload',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_VERBOSE => true
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

echo "Código HTTP: $httpCode\n";
echo "Respuesta: $response\n";

if (curl_errno($curl)) {
    echo "Error CURL: " . curl_error($curl) . "\n";
} else {
    // Extraer TrackID de la respuesta
    $partes = explode(" ", $response);
    $track_id = end($partes); // Obtiene el último elemento (TrackID)
    
    if ($httpCode == 200 && $track_id) {
        echo "TrackID obtenido: " . $track_id . "\n";
        sleep(5);
        
        // Consultar estado usando QueryEstUp 
        $estado = Sii::request('QueryEstUp', 'getEstUp', [
            'RutCompania' => $rutCompany,
            'DvCompania' => $dvCompany,
            'TrackId' => $track_id,
            'Token' => $token
        ]);
     
        if ($estado === false) {
            echo "No se pudo consultar el estado del envío\n";
        } else {
            echo "\nEstado del envío:\n";
            echo "TRACKID: " . $track_id . "\n";
            
            // Convertir SimpleXMLElement a array para mejor manejo
            $estadoArray = json_decode(json_encode($estado), true);
     
            if(isset($estadoArray['RESP_HDR'])) {
                $header = $estadoArray['RESP_HDR'];
                echo "ESTADO: " . $header['ESTADO'] . "\n";
                echo "GLOSA: " . $header['GLOSA'] . "\n";
            }
     
            if(isset($estadoArray['RESP_BODY'])) {
                echo "DETALLE:\n";
                print_r($estadoArray['RESP_BODY']);
            }
            
            echo "\nRespuesta completa:\n";
            print_r($estadoArray);
        }
     }
}

curl_close($curl);
unlink($file);

