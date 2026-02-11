<?php
require_once '../../../../config.php';

// Ruta de la firma electrónica - Configurar desde base de datos o variables de entorno
$rutaFirma = '';
$passFirma = '';

// Crear instancia de Firma Electrónica
$Firma = new FirmaElectronica($rutaFirma, $passFirma);

function roundInteger($valor) {
    return round($valor);
}

// Función para generar Libro de Ventas
function generarLibroVentas($Firma) {
    $LibroVentas = new LibroCompraVenta();
    $LibroVentas->setFirma($Firma);

    $caratulaVentas = [
        'RutEmisorLibro' => '11.111.111-1',
        'RutEnvia' => $Firma->getID(),
        'PeriodoTributario' => date('Y-m'),
        'FchResol' => '2025-01-03',
        'NroResol' => 0,
        'TipoOperacion' => 'VENTA',
        'TipoLibro' => 'ESPECIAL',
        'TipoEnvio' => 'TOTAL',
        'FolioNotificacion' => 2
    ];

    $LibroVentas->setCaratula($caratulaVentas);

    $detallesVentas = [
        // Caso 4159682-1: Factura Electrónica
        [
            'TpoDoc' => 33,
            'NroDoc' => '49',
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K', 
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 261911,
            'MntIVA' => 49763,
            'MntTotal' => 311674
        ],
        // Caso 4159682-2: Factura Electrónica con descuentos
        [
            'TpoDoc' => 33,
            'NroDoc' => '50',
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K',
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 957545,
            'MntIVA' => 181934,
            'MntTotal' => 1139479
        ],
        // Caso 4159682-3: Factura Electrónica con ítem exento
        [
            'TpoDoc' => 33,
            'NroDoc' => '51',
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K',
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 547730,
            'MntExe' => 34770,
            'MntIVA' => 104069,
            'MntTotal' => 686569
        ],
        // Caso 4159682-4: Factura Electrónica con descuento global
        [
            'TpoDoc' => 33,
            'NroDoc' => '52',
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K',
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 326543,
            'MntExe' => 13550,
            'MntIVA' => 62043,
            'MntTotal' => 402136
        ],
        // Caso 4159682-5: Nota de Crédito que corrige giro
        [
            'TpoDoc' => 61,
            'NroDoc' => '33',
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K',
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 0,
            'MntIVA' => 0,
            'MntTotal' => 0,
            'TpoDocRef' => 33,
            'FolioDocRef' => '49'
        ],
        // Caso 4159682-6: Nota de Crédito por devolución
        [
            'TpoDoc' => 61,
            'NroDoc' => '34',
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K',
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 441514,
            'MntIVA' => 83888,
            'MntTotal' => 525402,
            'TpoDocRef' => 33,
            'FolioDocRef' => '50'
        ],
        // Caso 4159682-7: Nota de Crédito que anula factura
        [
            'TpoDoc' => 61,
            'NroDoc' => '35',
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K',
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 547730,
            'MntExe' => 34770,
            'MntIVA' => 104069,
            'MntTotal' => 686569,
            'TpoDocRef' => 33,
            'FolioDocRef' => '51'
        ],
        [
            'TpoDoc' => 56,  // Nota de Débito
            'NroDoc' => '11',  // Siguiente número correlativo
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '60803000-K',
            'RznSoc' => 'SERVICIO DE IMPUESTOS INTERNOS',
            'MntNeto' => 0,
            'MntIVA' => 0,
            'MntTotal' => 0,
            'TpoDocRef' => 61,
            'FolioDocRef' => '33'
        ]

    ];

    foreach ($detallesVentas as $detalle) {
        $LibroVentas->agregar($detalle);
    }

    $xml_libro_ventas = $LibroVentas->generar();
    file_put_contents(__ROOT__.'/archives/LibroVentas.xml', $xml_libro_ventas);
    
    return $xml_libro_ventas;
}

// Función para generar Libro de Compras
function generarLibroCompras($Firma) {
    $LibroCompras = new LibroCompraVenta();
    $LibroCompras->setFirma($Firma);

    $caratulaCompras = [
        'RutEmisorLibro' => '11.111.111-1',
        'RutEnvia' => $Firma->getID(),
        'PeriodoTributario' => date('Y-m'),
        'FchResol' => '2025-01-03',
        'NroResol' => 0,
        'TipoOperacion' => 'COMPRA',
        'TipoLibro' => 'ESPECIAL',
        'TipoEnvio' => 'TOTAL',
        'FolioNotificacion' => 2
    ];

    $LibroCompras->setCaratula($caratulaCompras);

    $detallesCompras = [
        // Factura Folio 234
        [
            'TpoDoc' => 30,
            'NroDoc' => 234,
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '66666666-6',
            'RznSoc' => 'Proveedor 1',
            'MntNeto' => 27326, // Monto afecto
            'MntIVA' => roundInteger(27326 * 0.19), // IVA calculado
            'MntTotal' => 32518 // Neto + IVA
        ],
        // Factura Electrónica Folio 32
        [
            'TpoDoc' => 33,
            'NroDoc' => 32,
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '66666666-6',
            'RznSoc' => 'Proveedor 2',
            'MntNeto' => 7460, // Monto afecto
            'MntExe' => 9181, // Monto exento
            'MntIVA' => roundInteger(7460 * 0.19),
            'MntTotal' => 18058
        ],
        // Factura Folio 781 (IVA Uso Común)
        [
            'TpoDoc' => 30,
            'NroDoc' => 781,
            'TpoImp' => 1,
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '66666666-6',
            'RznSoc' => 'Proveedor 3',
            'MntNeto' => 29858, // Monto afecto
            'MntIVA' => roundInteger(29858 * 0.19),
            'FctProp' => 60, // IVA con factor de proporcionalidad
            'MntTotal' => 29858 + roundInteger(29858 * 0.19)
        ],
        // Nota de Crédito Folio 451
        [
            'TpoDoc' => 60,
            'NroDoc' => 451,
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '66666666-6',
            'RznSoc' => 'Proveedor 1',
            'MntNeto' => 2758, // Afecta
            'MntIVA' => roundInteger(2758 * 0.19),
            'MntTotal' => 2758 + roundInteger(2758 * 0.19),
            'TpoDocRef' => 33,
            'FolioDocRef' => 234
        ],
        // Factura Electrónica Folio 67
        [
            'TpoDoc' => 33, // Factura Electrónica
            'NroDoc' => 67, // Folio 67
            'TasaImp' => 0.19, // Tasa de IVA
            'FchDoc' => date('Y-m-d'), // Fecha actual
            'RUTDoc' => '66666666-6', // RUT del proveedor
            'RznSoc' => 'Proveedor 4', // Razón social del proveedor
            'MntNeto' => 10424, // Monto neto afecto
            'IVANoRec' => [
                [
                    'CodIVANoRec' => 4, // Código de IVA no recuperable (gastos rechazados no rebajables)
                    'MntIVANoRec' => roundInteger(10424 * 0.19) // Monto de IVA no recuperable
                ]
            ],
            'MntTotal' => 10424 + roundInteger(10424 * 0.19) // Monto total = Neto + IVA no recuperable
        ],
        // Factura de Compra Electrónica Folio 9
        [
            'TpoDoc' => 46,
            'NroDoc' => 9,
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '66666666-6',
            'RznSoc' => 'Proveedor 5',
            'MntNeto' => 9774, // Neto afecto
            'MntIVA' => roundInteger(9774 * 0.19), // IVA calculado
            'OtrosImp' => [
                [
                    'CodImp' => 15, // Código de impuesto para IVA retenido
                    'TasaImp' => 0.19, // Tasa de IVA
                    'MntImp' => roundInteger(9774 * 0.19) // Monto de IVA retenido
                ]
            ],
            'IVARetTotal' => roundInteger(9774 * 0.19), // IVA retenido totalmente
            'MntTotal' => 9774 // Monto total = Monto neto (IVA retenido no se suma)
        ],
        // Nota de Crédito Folio 211
        [
            'TpoDoc' => 60,
            'NroDoc' => 211,
            'TasaImp' => 0.19,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => '66666666-6',
            'RznSoc' => 'Proveedor 2',
            'MntNeto' => 5331, // Monto afecto
            'MntIVA' => roundInteger(5331 * 0.19),
            'MntTotal' => 5331 + roundInteger(5331 * 0.19),
            'TpoDocRef' => 33,
            'FolioDocRef' => 32
        ]
    ];

    foreach ($detallesCompras as $detalle) {
        $LibroCompras->agregar($detalle);
    }

    $xml_libro_compras = $LibroCompras->generar();
    file_put_contents(__ROOT__.'/archives/LibroCompras.xml', $xml_libro_compras);
    
    return $xml_libro_compras;
}

// Generar ambos libros
try {
    $libroVentas = generarLibroVentas($Firma);
    $libroCompras = generarLibroCompras($Firma);
    
    echo "Libros generados exitosamente:\n";
    echo "- Libro de Ventas guardado en: " . __ROOT__ . "/archives/LibroVentas.xml\n";
    echo "- Libro de Compras guardado en: " . __ROOT__ . "/archives/LibroCompras.xml\n";
} catch (Exception $e) {
    echo "Error al generar los libros: " . $e->getMessage();
}