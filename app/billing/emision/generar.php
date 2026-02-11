<?php
require_once '../../../config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener tipo de DTE y folio desde el POST
$tipoDTE = (int)$_POST['TipoDTE'];
$folioPost = (int)$_POST['Folio'];

// Crear objeto Folio y obtener datos del folio según el tipo de DTE
$Folio = new Folio();
$datosFolio = $Folio->getFolio($tipoDTE);

$Empresa = new Empresa();
$empresaData = $Empresa->getEmpresaRut($_POST['RUTEmisor']);

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
$Firma = new FirmaElectronica($rutaFirma,$passFirma);

$logo = !empty($_POST['logo_empresa']) ? $_POST['logo_empresa'] : false; 
  

// Colocar Rut de SII '60803000-K' para certificacion y producción.
// Actualizar si es que se tiene otra fecha ('2025-01-03' para certificacion) ('2014-08-22' produccion)
// Colocar NroResol = 0 para certificación y 80 para produccion
//Además ir a EnvioDte.php funcion setCaratula y cambiar el receptor
//Y por ultimo ir a Sii.php y cambiar ambiente a produccion o certificacion

  // Caratula para envío al SII
$caratulaSII = [    
  'RutEnvia' => $Firma->getID(),
  'RutReceptor' => '60803000-K', // Colocar Rut de SII
  'FchResol' => '2025-01-03',
  'NroResol' => 0,
]; 

// Caratula para la copia con el receptor
$caratulaReceptor = [    
  'RutEnvia' => $Firma->getID(),
  'RutReceptor' => !empty($_POST['RUTRecep']) ? $_POST['RUTRecep'] : false,
  'FchResol' => '2025-01-03',
  'NroResol' => 0,
];
if (isset($_POST['IndTraslado'])) {
  $IndTraslado = !empty($_POST['IndTraslado']) ? $_POST['IndTraslado'] : false;
} else {
  $IndTraslado = false;
}

if (isset($_POST['FmaPago'])) {
  $FmaPago = !empty($_POST['FmaPago']) ? $_POST['FmaPago'] : false;
} else {
  $FmaPago = false;
}

$IdDoc = [
  'TipoDTE' => !empty($_POST['TipoDTE']) ? $_POST['TipoDTE'] : false,
  'Folio' => !empty($_POST['Folio']) ? $_POST['Folio'] : false,
  'FchEmis' => !empty($_POST['FchEmis']) ? $_POST['FchEmis'] : false,
  #'IndNoRebaja' => !empty($_POST['IndNoRebaja']) ? $_POST['IndNoRebaja'] : false,
  #'TipoDespacho'  => !empty($_POST['TipoDespacho']) ? $_POST['TipoDespacho'] : false,
  'IndTraslado' => $IndTraslado,
  #'TpoImpresion'  => !empty($_POST['TpoImpresion']) ? $_POST['TpoImpresion'] : false,
  #'IndServicio' => !empty($_POST['IndServicio']) ? $_POST['IndServicio'] : false,
  #'MntBruto'  => !empty($_POST['MntBruto']) ? $_POST['MntBruto'] : false,
  #'TpoTranCompra' => !empty($_POST['TpoTranCompra']) ? $_POST['TpoTranCompra'] : false,
  #'TpoTranVenta'  => !empty($_POST['TpoTranVenta']) ? $_POST['TpoTranVenta'] : false,
  'FmaPago' => $FmaPago,
  #'FmaPagExp' => !empty($_POST['FmaPagExp']) ? $_POST['FmaPagExp'] : false,
  #'FchCancel' => !empty($_POST['FchCancel']) ? $_POST['FchCancel'] : false,
  #'MntCancel' => !empty($_POST['MntCancel']) ? $_POST['MntCancel'] : false,
  #'SaldoInsol'  => !empty($_POST['SaldoInsol']) ? $_POST['SaldoInsol'] : false,
  #'MntPagos'  =>  [
  #  'FchPago' => !empty($_POST['FchPago']) ? $_POST['FchPago'] : false,
  #  'MntPago' => !empty($_POST['MntPago']) ? $_POST['MntPago'] : false,
  #  'GlosaPagos'  => !empty($_POST['GlosaPagos']) ? $_POST['GlosaPagos'] : false,
  #],
  #'PeriodoDesde'  => !empty($_POST['PeriodoDesde']) ? $_POST['PeriodoDesde'] : false,
  #'PeriodoHasta'  => !empty($_POST['PeriodoHasta']) ? $_POST['PeriodoHasta'] : false,
  #'MedioPago' => !empty($_POST['MedioPago']) ? $_POST['MedioPago'] : false,
  #'TpoCtaPago'  => !empty($_POST['TpoCtaPago']) ? $_POST['TpoCtaPago'] : false,
  #'NumCtaPago'  => !empty($_POST['NumCtaPago']) ? $_POST['NumCtaPago'] : false,
  #'BcoPago' => !empty($_POST['BcoPago']) ? $_POST['BcoPago'] : false,
  #'TemPagoCdg'  => !empty($_POST['TemPagoCdg']) ? $_POST['TemPagoCdg'] : false,
  #'TempPagoGlosa' => !empty($_POST['TempPagoGlosa']) ? $_POST['TempPagoGlosa'] : false,
  #'TempPagoDias'  => !empty($_POST['TempPagoDias']) ? $_POST['TempPagoDias'] : false,
  #'FchVenc' => !empty($_POST['FchVenc']) ? $_POST['FchVenc'] : false,
];

//Datos del Emisor
$Emisor = [
  'RUTEmisor'     => !empty($_POST['RUTEmisor']) ? $_POST['RUTEmisor'] : false,
  'RznSoc'        => !empty($_POST['RznSoc']) ? $_POST['RznSoc'] : false,
  'GiroEmis'      => !empty($_POST['GiroEmis']) ? $_POST['GiroEmis'] : false,
  'Telefono'      => !empty($_POST['Telefono']) ? $_POST['Telefono'] : false,
  'CorreoEmisor'  => !empty($_POST['CorreoEmisor']) ? $_POST['CorreoEmisor'] : false,
  'Acteco'        => !empty($_POST['Acteco']) ? $_POST['Acteco'] : false,
  'CdgTraslado'   => !empty($_POST['CdgTraslado']) ? $_POST['CdgTraslado'] : false,
  'FolioAut'      => !empty($_POST['FolioAut']) ? $_POST['FolioAut'] : false,
  'FchAut'        => !empty($_POST['FchAut']) ? $_POST['FchAut'] : false,
  'Sucursal'      => !empty($_POST['Sucursal']) ? $_POST['Sucursal'] : false,
  'CdgSIISucur'   => !empty($_POST['CdgSIISucur']) ? $_POST['CdgSIISucur'] : false,
  'CodAdicSucur'  => !empty($_POST['CodAdicSucur']) ? $_POST['CodAdicSucur'] : false,
  'DirOrigen'     => !empty($_POST['DirOrigen']) ? $_POST['DirOrigen'] : false,
  'CmnaOrigen'    => !empty($_POST['CmnaOrigen']) ? $_POST['CmnaOrigen'] : false,
  'CiudadOrigen'  => !empty($_POST['CiudadOrigen']) ? $_POST['CiudadOrigen'] : false,
  'CdgVendedor'   => !empty($_POST['CdgVendedor']) ? $_POST['CdgVendedor'] : false,
  'IdAdicEmisor'  => !empty($_POST['IdAdicEmisor']) ? $_POST['IdAdicEmisor'] : false,
];

//Datos del Receptor
$Receptor = [
  'RUTRecep'      => !empty($_POST['RUTRecep']) ? $_POST['RUTRecep'] : false,
  'CdgIntRecep'   => !empty($_POST['CdgIntRecep']) ? $_POST['CdgIntRecep'] : false,
  'RznSocRecep'   => !empty($_POST['RznSocRecep']) ? $_POST['RznSocRecep'] : false,
  'NumId'         => !empty($_POST['NumId']) ? $_POST['NumId'] : false,
  'Nacionalidad'  => !empty($_POST['Nacionalidad']) ? $_POST['Nacionalidad'] : false,
  #'Nacionalidad'  => !empty($_POST['Nacionalidad']) ? $_POST['Nacionalidad'] : false, //repetido
  'IdAdicRecep'   => !empty($_POST['IdAdicRecep']) ? $_POST['IdAdicRecep'] : false,
  'GiroRecep'     => !empty($_POST['GiroRecep']) ? $_POST['GiroRecep'] : false,
  'Contacto'      => !empty($_POST['Contacto']) ? $_POST['Contacto'] : false,
  'CorreoRecep'   => !empty($_POST['CorreoRecep']) ? $_POST['CorreoRecep'] : false,
  'DirRecep'      => !empty($_POST['DirRecep']) ? $_POST['DirRecep'] : false,
  'CmnaRecep'     => !empty($_POST['CmnaRecep']) ? $_POST['CmnaRecep'] : false,
  'CiudadRecep'   => !empty($_POST['CiudadRecep']) ? $_POST['CiudadRecep'] : false,
  'DirPostal'     => !empty($_POST['DirPostal']) ? $_POST['DirPostal'] : false,
  'CmnaPostal'    => !empty($_POST['CmnaPostal']) ? $_POST['CmnaPostal'] : false,
  'CiudadPostal'  => !empty($_POST['CiudadPostal']) ? $_POST['CiudadPostal'] : false       
];

$Transporte = [
#  'Patente'           => !empty($_POST['Patente']) ? $_POST['Patente'] : false,
# 'RUTTrans'          => !empty($_POST['RUTTrans']) ? $_POST['RUTTrans'] : false,
#  'RUTChofer'         => !empty($_POST['RUTChofer']) ? $_POST['RUTChofer'] : false,
#  'NombreChofer'      => !empty($_POST['NombreChofer']) ? $_POST['NombreChofer'] : false,
#  'DirDest'           => !empty($_POST['DirDest']) ? $_POST['DirDest'] : false,
#  'CmnaDest'          => !empty($_POST['CmnaDest']) ? $_POST['CmnaDest'] : false,
#  'CiudadDest'        => !empty($_POST['CiudadDest']) ? $_POST['CiudadDest'] : false,
#  'Aduana'  => [
#    'CodModVenta'     => !empty($_POST['CodModVenta']) ? $_POST['CodModVenta'] : false,
#    'CodClauVenta'    => !empty($_POST['CodClauVenta']) ? $_POST['CodClauVenta'] : false,
#    'TotClauVenta'    => !empty($_POST['TotClauVenta']) ? $_POST['TotClauVenta'] : false,
#    'CodViaTransp'    => !empty($_POST['CodViaTransp']) ? $_POST['CodViaTransp'] : false,
#    'NombreTransp'    => !empty($_POST['NombreTransp']) ? $_POST['NombreTransp'] : false,
#    'RUTCiaTransp'    => !empty($_POST['RUTCiaTransp']) ? $_POST['RUTCiaTransp'] : false,
#    'NomCiaTransp'    => !empty($_POST['NomCiaTransp']) ? $_POST['NomCiaTransp'] : false,
#    'IdAdicTransp'    => !empty($_POST['IdAdicTransp']) ? $_POST['IdAdicTransp'] : false,
#    'Booking'         => !empty($_POST['Booking']) ? $_POST['Booking'] : false,
#    'Operador'        => !empty($_POST['Operador']) ? $_POST['Operador'] : false,
#    'CodPtoEmbarque'  => !empty($_POST['CodPtoEmbarque']) ? $_POST['CodPtoEmbarque'] : false,
#    'IdAdicPtoEmb'    => !empty($_POST['IdAdicPtoEmb']) ? $_POST['IdAdicPtoEmb'] : false,
#    'CodPtoDesemb'    => !empty($_POST['CodPtoDesemb']) ? $_POST['CodPtoDesemb'] : false,
#    'IdAdicPtoDesemb' => !empty($_POST['IdAdicPtoDesemb']) ? $_POST['IdAdicPtoDesemb'] : false,
#    'Tara'            => !empty($_POST['Tara']) ? $_POST['Tara'] : false,
#    'CodUnidMedTara'  => !empty($_POST['CodUnidMedTara']) ? $_POST['CodUnidMedTara'] : false,
#    'PesoBruto'       => !empty($_POST['PesoBruto']) ? $_POST['PesoBruto'] : false,
#    'CodUnidPesoBruto'=> !empty($_POST['CodUnidPesoBruto']) ? $_POST['CodUnidPesoBruto'] : false,
#    'PesoNeto'        => !empty($_POST['PesoNeto']) ? $_POST['PesoNeto'] : false,
#    'CodUnidPesoNeto' => !empty($_POST['CodUnidPesoNeto']) ? $_POST['CodUnidPesoNeto'] : false,
#    'TotItems'        => !empty($_POST['TotItems']) ? $_POST['TotItems'] : false,
#    'TotBultos'       => !empty($_POST['TotBultos']) ? $_POST['TotBultos'] : false,
#    'TipoBultos'  => [
#      'CodTpoBultos'  => !empty($_POST['CodTpoBultos']) ? $_POST['CodTpoBultos'] : false,
#      'CantBultos'    => !empty($_POST['CantBultos']) ? $_POST['CantBultos'] : false,
#      'Marcas'        => !empty($_POST['Marcas']) ? $_POST['Marcas'] : false,
#      'IdContainer'   => !empty($_POST['IdContainer']) ? $_POST['IdContainer'] : false,
#      'Sello'         => !empty($_POST['Sello']) ? $_POST['Sello'] : false,
#      'EmisorSello'   => !empty($_POST['EmisorSello']) ? $_POST['EmisorSello'] : false,
#    ],
#    'MntFlete'        => !empty($_POST['MntFlete']) ? $_POST['MntFlete'] : false,
#    'MntSeguro'       => !empty($_POST['MntSeguro']) ? $_POST['MntSeguro'] : false,
#    'CodPaisRecep'    => !empty($_POST['CodPaisRecep']) ? $_POST['CodPaisRecep'] : false,
#    'CodPaisDestin'   => !empty($_POST['CodPaisDestin']) ? $_POST['CodPaisDestin'] : false,
#  ],
];

$Totales =  [
#  'TpoMoneda'         => !empty($_POST['TpoMoneda']) ? $_POST['TpoMoneda'] : false,
#  'MntNeto'           => !empty($_POST['MntNeto']) ? $_POST['MntNeto'] : false,
#  'MntExe'            => !empty($_POST['MntExe']) ? $_POST['MntExe'] : false,
#  'MntBase'           => !empty($_POST['MntBase']) ? $_POST['MntBase'] : false,
#  'MntMargenCom'      => !empty($_POST['MntMargenCom']) ? $_POST['MntMargenCom'] : false,
#  #'TasaIVA'           => !empty($_POST['TasaIVA']) ? $_POST['TasaIVA'] : false,
#  #'IVA'               => !empty($_POST['IVA']) ? $_POST['IVA'] : false,
#  'IVAProp'           => !empty($_POST['IVAProp']) ? $_POST['IVAProp'] : false,
#  'IVATerc'           => !empty($_POST['IVATerc']) ? $_POST['IVATerc'] : false,
#  'ImpReten'  =>  [
#    'TipoImp'         => !empty($_POST['TipoImp']) ? $_POST['TipoImp'] : false,
#    'TasaImp'         => !empty($_POST['TasaImp']) ? $_POST['TasaImp'] : false,
#    'MontoImp'        => !empty($_POST['MontoImp']) ? $_POST['MontoImp'] : false,
#  ],
#  'IvaNoRet'          => !empty($_POST['IvaNoRet']) ? $_POST['IvaNoRet'] : false,
#  'CredEC'            => !empty($_POST['CredEC']) ? $_POST['CredEC'] : false,
#  'GmtDep'            => !empty($_POST['GmtDep']) ? $_POST['GmtDep'] : false,
#  'ValComNeto'        => !empty($_POST['ValComNeto']) ? $_POST['ValComNeto'] : false,
#  'ValComExe'         => !empty($_POST['ValComExe']) ? $_POST['ValComExe'] : false,
#  'ValComIVA'         => !empty($_POST['ValComIVA']) ? $_POST['ValComIVA'] : false,
#  #'MntTotal'          => !empty($_POST['MntTotal']) ? $_POST['MntTotal'] : false,
#  'MontoNF'           => !empty($_POST['MontoNF']) ? $_POST['MontoNF'] : false,
#  'MontoPeriodo'      => !empty($_POST['MontoPeriodo']) ? $_POST['MontoPeriodo'] : false,
#  'SaldoAnterior'     => !empty($_POST['SaldoAnterior']) ? $_POST['SaldoAnterior'] : false,
#  'VlrPagar'          => !empty($_POST['VlrPagar']) ? $_POST['VlrPagar'] : false,
];

$OtraMoneda = [
#  'TpoMoneda'         => !empty($_POST['TpoMoneda']) ? $_POST['TpoMoneda'] : false,
#  'TpoCambio'         => !empty($_POST['TpoCambio']) ? $_POST['TpoCambio'] : false,
#  'MntNetoOtrMnda'    => !empty($_POST['MntNetoOtrMnda']) ? $_POST['MntNetoOtrMnda'] : false,
#  'MntExeOtrMnda'     => !empty($_POST['MntExeOtrMnda']) ? $_POST['MntExeOtrMnda'] : false,
#  'MntFaeCarneOtrMnda'=> !empty($_POST['MntFaeCarneOtrMnda']) ? $_POST['MntFaeCarneOtrMnda'] : false,
#  'MntMargComOtrMnda' => !empty($_POST['MntMargComOtrMnda']) ? $_POST['MntMargComOtrMnda'] : false,
#  'IVAOtrMnda'        => !empty($_POST['IVAOtrMnda']) ? $_POST['IVAOtrMnda'] : false,
#  'ImpRetOtrMnda' => [
#    'TipoImpOtraMnda' => !empty($_POST['TipoImpOtraMnda']) ? $_POST['TipoImpOtraMnda'] : false,
#    'TasaImpOtrMnda'  => !empty($_POST['TasaImpOtrMnda']) ? $_POST['TasaImpOtrMnda'] : false,
#    'VlrImpOtrMnda'   => !empty($_POST['VlrImpOtrMnda']) ? $_POST['VlrImpOtrMnda'] : false,
#  ],
#  'IVANoRetOtrMnda'   => !empty($_POST['IVANoRetOtrMnda']) ? $_POST['IVANoRetOtrMnda'] : false,
#  'MntTotOtrMnda'     => !empty($_POST['MntTotOtrMnda']) ? $_POST['MntTotOtrMnda'] : false,
];

//Detalle
$x = count($_POST['item']);

for ($i=0; $i < $x ; $i++) {
  $NroLinDet = $i + 1;

  if (!empty($_POST['item'][$i]['VlrCodigo'])) {
    $CdgItem = [
      'TpoCodigo'         => 'INT2',
      'VlrCodigo'         => !empty($_POST['item'][$i]['VlrCodigo']) ? $_POST['item'][$i]['VlrCodigo'] : false,
    ];
  } else {
    //$CdgItem = [];
    // Siempre incluir TpoCodigo incluso si no hay VlrCodigo
    $CdgItem = [
      'TpoCodigo' => 'INT2',
      'VlrCodigo' => '000'  // o algún valor por defecto
    ];
  }

  $Detalle[$i]  = [
    'NroLinDet'           => $NroLinDet,
    'CdgItem'             => $CdgItem,
    'TpoDocLiq'           => !empty($_POST['item'][$i]['TpoDocLiq']) ? $_POST['item'][$i]['TpoDocLiq'] : false,
    'IndExe'              => !empty($_POST['item'][$i]['IndExe']) ? $_POST['item'][$i]['IndExe'] : false,
#    'Retenedor' => [
#      'IndAgente'         => !empty($_POST['item'][$i]['IndAgente']) ? $_POST['item'][$i]['IndAgente'] : false,
#      'MntBaseFaena'      => !empty($_POST['item'][$i]['MntBaseFaena']) ? $_POST['item'][$i]['MntBaseFaena'] : false,
#      'MntMargComer'      => !empty($_POST['item'][$i]['MntMargComer']) ? $_POST['item'][$i]['MntMargComer'] : false,
#      'PrcConsFinal'      => !empty($_POST['item'][$i]['PrcConsFinal']) ? $_POST['item'][$i]['PrcConsFinal'] : false,
#    ],
    'NmbItem'             => !empty($_POST['item'][$i]['NmbItem']) ? $_POST['item'][$i]['NmbItem'] : false,
    'DscItem'             => !empty($_POST['item'][$i]['DscItem']) ? $_POST['item'][$i]['DscItem'] : false,
    'QtyRef'              => !empty($_POST['item'][$i]['QtyRef']) ? $_POST['item'][$i]['QtyRef'] : false,
    'UnmdRef'             => !empty($_POST['item'][$i]['UnmdRef']) ? $_POST['item'][$i]['UnmdRef'] : false,
    'PrcRef'              => !empty($_POST['item'][$i]['PrcRef']) ? $_POST['item'][$i]['PrcRef'] : false,
    'QtyItem'             => !empty($_POST['item'][$i]['QtyItem']) ? $_POST['item'][$i]['QtyItem'] : false,
#    'Subcantidad' =>[
#      'SubQty'            => !empty($_POST['item'][$i]['SubQty']) ? $_POST['item'][$i]['SubQty'] : false,
#      'SubCod'            => !empty($_POST['item'][$i]['SubCod']) ? $_POST['item'][$i]['SubCod'] : false,
#      'TipCodSubQty'      => !empty($_POST['item'][$i]['TipCodSubQty']) ? $_POST['item'][$i]['TipCodSubQty'] : false,
#    ],
    'FchElabor'           => !empty($_POST['item'][$i]['FchElabor']) ? $_POST['item'][$i]['FchElabor'] : false,
    'FchVencim'           => !empty($_POST['item'][$i]['FchVencim']) ? $_POST['item'][$i]['FchVencim'] : false,
    'UnmdItem'            => !empty($_POST['item'][$i]['UnmdItem']) ? $_POST['item'][$i]['UnmdItem'] : false,
    'PrcItem'             => !empty($_POST['item'][$i]['PrcItem']) ? $_POST['item'][$i]['PrcItem'] : false,
#    'OtrMnda' => [
#      'PrcOtrMon'         => !empty($_POST['item'][$i]['PrcOtrMon']) ? $_POST['item'][$i]['PrcOtrMon'] : false,
#      'Moneda'            => !empty($_POST['item'][$i]['Moneda']) ? $_POST['item'][$i]['Moneda'] : false,
#      'FctConv'           => !empty($_POST['item'][$i]['FctConv']) ? $_POST['item'][$i]['FctConv'] : false,
#      'DctoOtrMnda'       => !empty($_POST['item'][$i]['DctoOtrMnda']) ? $_POST['item'][$i]['DctoOtrMnda'] : false,
#      'RecargoOtrMnda'    => !empty($_POST['item'][$i]['RecargoOtrMnda']) ? $_POST['item'][$i]['RecargoOtrMnda'] : false,
#      'MontoItemOtrMnda'  => !empty($_POST['item'][$i]['MontoItemOtrMnda']) ? $_POST['item'][$i]['MontoItemOtrMnda'] : false,
#    ],
#    'DescuentoPct'        => !empty($_POST['item'][$i]['DescuentoPct']) ? $_POST['item'][$i]['DescuentoPct'] : false,
#    'DescuentoMonto'      => !empty($_POST['item'][$i]['DescuentoMonto']) ? $_POST['item'][$i]['DescuentoMonto'] : false,
#    'SubDscto'  => [
#      'TipoDscto'         => !empty($_POST['item'][$i]['TipoDscto']) ? $_POST['item'][$i]['TipoDscto'] : false,
#      'ValorDscto'        => !empty($_POST['item'][$i]['ValorDscto']) ? $_POST['item'][$i]['ValorDscto'] : false,
#    ],
#    'RecargoPct'          => !empty($_POST['item'][$i]['RecargoPct']) ? $_POST['item'][$i]['RecargoPct'] : false,
#    'RecargoMonto'        => !empty($_POST['item'][$i]['RecargoMonto']) ? $_POST['item'][$i]['RecargoMonto'] : false,
#    'SubRecargo' => [
#      'TipoRecargo'       => !empty($_POST['item'][$i]['TipoRecargo']) ? $_POST['item'][$i]['TipoRecargo'] : false,
#      'ValorRecargo'      => !empty($_POST['item'][$i]['ValorRecargo']) ? $_POST['item'][$i]['ValorRecargo'] : false,
#    ],
    'CodImpAdic'          => !empty($_POST['item'][$i]['CodImpAdic']) ? $_POST['item'][$i]['CodImpAdic'] : false,
    #'MontoItem'           => !empty($_POST['item'][$i]['MontoItem']) ? $_POST['item'][$i]['MontoItem'] : false,
  ];
}

if (isset($_POST['sti'])) {
  $v = count($_POST['sti']);
  for ($i=0; $i < $v; $i++) {
    $NroSTI = $i + 1;
    if (!empty($_POST['GlosaSTI']) && !empty($_POST['OrdenSTI'])) {
      $SubTotInfo = [
        'NroSTI'          => $NroSTI,
        'GlosaSTI'        => !empty($_POST['GlosaSTI']) ? $_POST['GlosaSTI'] : false,
        'OrdenSTI'        => !empty($_POST['OrdenSTI']) ? $_POST['OrdenSTI'] : false,
        'SubTotNetoSTI'   => !empty($_POST['SubTotNetoSTI']) ? $_POST['SubTotNetoSTI'] : false,
        'SubTotIVASTI'    => !empty($_POST['SubTotIVASTI']) ? $_POST['SubTotIVASTI'] : false,
        'SubTotAdicSTI'   => !empty($_POST['SubTotAdicSTI']) ? $_POST['SubTotAdicSTI'] : false,
        'SubTotExeSTI'    => !empty($_POST['SubTotExeSTI']) ? $_POST['SubTotExeSTI'] : false,
        'ValSubtotSTI'    => !empty($_POST['ValSubtotSTI']) ? $_POST['ValSubtotSTI'] : false,
        'LineasDeta'      => !empty($_POST['LineasDeta']) ? $_POST['LineasDeta'] : false,
      ];          
    } else {
      $SubTotInfo = [];
    } 
  }
} else {
  $SubTotInfo = [];
}

$y = count($_POST['refe']);
for ($i=0; $i < $y ; $i++) {
  $NroLinRef = $i + 1;
  if (!empty($_POST['refe'][$i]['TpoDocRef']) && !empty($_POST['refe'][$i]['FolioRef']) && !empty($_POST['refe'][$i]['FchRef']) && !empty($_POST['refe'][$i]['RazonRef'])) {
    $Referencia[$i] = [
    'NroLinRef'       => $NroLinRef,
    'TpoDocRef'       => !empty($_POST['refe'][$i]['TpoDocRef']) ? $_POST['refe'][$i]['TpoDocRef'] : false,
    'IndGlobal'       => !empty($_POST['refe'][$i]['IndGlobal']) ? $_POST['refe'][$i]['IndGlobal'] : false,
    'FolioRef'        => !empty($_POST['refe'][$i]['FolioRef']) ? $_POST['refe'][$i]['FolioRef'] : false,
    'RUTOtr'          => !empty($_POST['refe'][$i]['RUTOtr']) ? $_POST['refe'][$i]['RUTOtr'] : false,
    'IdAdicOtr'       => !empty($_POST['refe'][$i]['IdAdicOtr']) ? $_POST['refe'][$i]['IdAdicOtr'] : false,
    'FchRef'          => !empty($_POST['refe'][$i]['FchRef']) ? $_POST['refe'][$i]['FchRef'] : false,
    'CodRef'          => !empty($_POST['refe'][$i]['CodRef']) ? $_POST['refe'][$i]['CodRef'] : false,
    'RazonRef'        => !empty($_POST['refe'][$i]['RazonRef']) ? $_POST['refe'][$i]['RazonRef'] : false,
    ];
  } else {
    $Referencia = [];
  } 
}

if (isset($_POST['dsrc'])) {
  $z = count($_POST['dsrc']);
  for ($i=0; $i < $z ; $i++) {
    $NroLinDR = $i + 1;
    if (!empty($_POST['dsrc'][$i]['TpoMov']) && !empty($_POST['dsrc'][$i]['GlosaDR']) && !empty($_POST['dsrc'][$i]['ValorDR'])) {
      $DscRcgGlobal[$i] = [
      'NroLinDR'        => $NroLinDR,
      'TpoMov'          => !empty($_POST['dsrc'][$i]['TpoMov']) ? $_POST['dsrc'][$i]['TpoMov'] : false,
      'GlosaDR'         => !empty($_POST['dsrc'][$i]['GlosaDR']) ? $_POST['dsrc'][$i]['GlosaDR'] : false,
      'TpoValor'        => !empty($_POST['dsrc'][$i]['TpoValor']) ? $_POST['dsrc'][$i]['TpoValor'] : false,
      'ValorDR'         => !empty($_POST['dsrc'][$i]['ValorDR']) ? $_POST['dsrc'][$i]['ValorDR'] : false,
      'ValorDROtrMnda'  => !empty($_POST['dsrc'][$i]['ValorDROtrMnda']) ? $_POST['dsrc'][$i]['ValorDROtrMnda'] : false,
      'IndExeDR'        => !empty($_POST['dsrc'][$i]['IndExeDR']) ? $_POST['dsrc'][$i]['IndExeDR'] : false,
      ];
    } else {
      $DscRcgGlobal = [];
    }
  }
} else {
  $DscRcgGlobal = [];
}

if (isset($_POST['comi'])) {
  $w = count($_POST['comi']);
  for ($i=0; $i < $z ; $i++) {
    $NroLinCom = $i + 1;
    if (!empty($_POST['TipoMovim']) && !empty($_POST['Glosa']) && !empty($_POST['TasaComision'])) {
      $Comisiones = [
        'NroLinCom'       => $NroLinCom,
        'TipoMovim'       => !empty($_POST['TipoMovim']) ? $_POST['TipoMovim'] : false,
        'Glosa'           => !empty($_POST['Glosa']) ? $_POST['Glosa'] : false,
        'TasaComision'    => !empty($_POST['TasaComision']) ? $_POST['TasaComision'] : false,
        'ValComBeto'      => !empty($_POST['ValComBeto']) ? $_POST['ValComBeto'] : false,
        'ValComExe'       => !empty($_POST['ValComExe']) ? $_POST['ValComExe'] : false,
        'ValComIVA'       => !empty($_POST['ValComIVA']) ? $_POST['ValComIVA'] : false,
      ];    
    } else {
      $Comisiones = [];
    }
  }
} else {
  $Comisiones = [];
}

#################################################################################
#                         DTE CON LOS DATOS RECIBIDOS                           #
$Documento = [
  'Encabezado'  => [
    'IdDoc'       => $IdDoc,
    'Emisor'      => $Emisor,
    'RUTMandante' => !empty($_POST['RUTMandante']) ? $_POST['RUTMandante'] : false,
    'Receptor'    => $Receptor,
    'RUTSocilita' => !empty($_POST['RUTSocilita']) ? $_POST['RUTSocilita'] : false,
    #'Transporte'  => $Transporte,
    #'Totales'     => $Totales,
    #'OtraMoneda'  => $OtraMoneda,
  ],
  'Detalle'       => $Detalle,
  'SubTotInfo'    => $SubTotInfo,
  'DscRcgGlobal'  => $DscRcgGlobal,
  'Referencia'    => $Referencia,
  'Comisiones'    => $Comisiones, 
];

#################################################################################
#                       PROCESO GENERACIÓN Y ENVIO DTE                          #
$rutaFolios = BASEURL.$_POST['rutaFolio'];

$Folios = new Folios(file_get_contents($rutaFolios));

$EnvioDTE = new EnvioDte();
#################################################################################
#                       GENERACIÓN DTE, TIMBRADO Y FIRMADO                      #

$DTE = new Dte($Documento);

$DTE->timbrar($Folios);
$DTE->firmar($Firma);
#################################################################################
#             SE AGREGA EL DTE GENERADO AL SOBRE DE ENVÍO Y SE FIRMA            #

$EnvioDTE->agregar($DTE);
$EnvioDTE->setFirma($Firma);
$EnvioDTE->setCaratula($caratulaSII);
#################################################################################


// Para Receptor
$EnvioDTEReceptor = new EnvioDte();
$EnvioDTEReceptor->agregar($DTE);
$EnvioDTEReceptor->setFirma($Firma);
$EnvioDTEReceptor->setCaratula($caratulaReceptor);

$Directorio = new Directorio();

try {
  $carpetaXML = $Directorio->creaDirectorio(__ROOT__.'/archives/xml/'.$_POST['TipoDTE']);
  if (!$carpetaXML) {
      die('Error: No se pudo crear el directorio XML');
  }

  $carpetaXMLCopia = $Directorio->creaDirectorio(__ROOT__.'/archives/xml_copia/'.$_POST['TipoDTE']);
  if (!$carpetaXMLCopia) {
      die('Error: No se pudo crear el directorio XML Copia');
  }
  
  $carpetaPDF = $Directorio->creaDirectorio(__ROOT__.'/archives/pdf/'.$_POST['TipoDTE']);
  if (!$carpetaPDF) {
      die('Error: No se pudo crear el directorio PDF');
  }
} catch (Exception $e) {
  die('Error al crear directorios: ' . $e->getMessage());
}

$archivoXML = $carpetaXML.'D0C'.$_POST['TipoDTE'].$_POST['Folio'].'.xml';
// Guardar XML copia para receptor
$archivoXMLCopia = $carpetaXMLCopia.'D0C'.$_POST['TipoDTE'].$_POST['Folio'].'.xml';
file_put_contents($archivoXMLCopia, $EnvioDTEReceptor->generar());

file_put_contents($archivoXML, $EnvioDTE->generar());

$EnvioDTE->loadXML(file_get_contents($archivoXML));

$cliente = new Cliente();
$rut = $cliente->getCliente($_POST['RUTRecep']);

$correo = $rut['correo_envio'];
$totalmonto = $DTE->getMontoTotal();


##################### GENERACIÓN DEL PDF ##############################

$pdf = new PDFdte(false);
#$pdf->setFooterText();
$pdf->setLogo($logo);
$pdf->setResolucion(['FchResol'=>$caratulaSII['FchResol'], 'NroResol'=>$caratulaSII['NroResol']]);

if (isset($_POST['cedible'])) {
  if ($_POST['cedible'] === 'true') {
    $pdf->setCedible(true);
    $pdf->agregar($DTE->getDatos(), $DTE->getTED());
    $pdf->Output($carpetaPDF.'D0C'.$DTE->getID().'Cedible.pdf', 'FD');

  } else {
    $pdf->setCedible(false);
    $pdf->agregar($DTE->getDatos(), $DTE->getTED());
    $pdf->Output($carpetaPDF.'D0C'.$DTE->getID().'.pdf', 'FD');
  }
} else {
  $pdf->setCedible(false);
  $pdf->agregar($DTE->getDatos(), $DTE->getTED());
  $pdf->Output($carpetaPDF.'D0C'.$DTE->getID().'.pdf', 'FD');
}
/**
 * Función para enviar un correo electrónico con los documentos tributarios adjuntos
 * 
 * @param string $correoDestinatario Email del destinatario
 * @param string $rutEmisor RUT del emisor
 * @param string $razonSocialEmisor Razón social del emisor
 * @param string $rutReceptor RUT del receptor
 * @param string $razonSocialReceptor Razón social del receptor
 * @param string $tipoDTE Tipo de DTE (33, 34, etc.)
 * @param string $folio Número de folio
 * @param string $fechaEmision Fecha de emisión
 * @param float $montoTotal Monto total del documento
 * @param string $rutaXML Ruta al archivo XML
 * @param string $rutaPDF Ruta al archivo PDF
 * @return bool Retorna true si el correo fue enviado con éxito, false en caso contrario
 */
function enviarEmailDTE($correoDestinatario, $rutEmisor, $razonSocialEmisor, $rutReceptor, $razonSocialReceptor, 
                      $tipoDTE, $folio, $fechaEmision, $montoTotal, $rutaXML, $rutaPDF) {
    
    // Asunto del correo
    $subject = "Envío DTE - " . $razonSocialEmisor;
    
    // Formatea el monto para mostrar
    $montoFormateado = number_format($montoTotal, 0, ',', '.');
    
    // El contenido del mensaje HTML
    $message = '
    <div style="background-color:#f4f4f4; padding-bottom:100px; padding-right:5px; padding-top:20px; padding-left:5px; font-family:Arial,sans-serif; color:#444444; border-collapse:collapse; margin-top:0; margin-left:auto; margin-bottom:0; margin-right:auto">
        <center>
            <table style="margin-bottom:25px">
                <tr>
                    <td>
                        <a href="">
                            <img src="" shrinktofit="true" border="0">
                        </a>
                    </td>
                </tr>
            </table>

            <table width="600" style="background-color:white; font-family: Arial, sans-serif;border-collapse: collapse; border-width: 1px; border-style: solid; border-color:#ddd; box-shadow:3px 3px 15px rgba(0,0,0,0.2); ">
                <tbody>
                    <!-- head -->
                    <tr style="background-color:#f5f5f5;">
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <h3 style="color:#004A8D; margin-top:5px;">Producción · Intercambio · Envío de DTEs<p style="font-weight:bold;color:#f64b45;margin:0;float: right;width: 60%;"></p></span></h3>
                        </td>
                    </tr>

                    <!-- Emisor y Receptor -->
                    <tr>
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <table style="width: 100%;">
                                <tr style="font-size: 18px; ">
                                    <td style="font-weight: bold;">Emisor</td>
                                    <td style="font-weight: bold;">Receptor</td>
                                </tr>
                                <tr style="font-size: 13px;">
                                    <td>' . $razonSocialEmisor . ' R.U.T.: ' . $rutEmisor . '</td>
                                    <td>' . $razonSocialReceptor . ' R.U.T.: ' . $rutReceptor . '</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Detalle -->
                    <tr style="border-bottom:1px solid #DDD;">
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <table style="width: 100%;">
                                <thead>
                                    <tr style="font-size: 13px;">
                                        <th style="font-weight: bold;">Emisor</th>
                                        <th style="font-weight: bold;">Tipo</th>
                                        <th style="font-weight: bold;">Folio</th>
                                        <th style="font-weight: bold;">Fecha</th>
                                        <th style="font-weight: bold;">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="font-size: 11px; text-align: center;">
                                        <td>' . $rutEmisor . '</td>
                                        <td>' . $tipoDTE . '</td>
                                        <td>' . $folio . '</td>
                                        <td>' . $fechaEmision . '</td>
                                        <td>' . $montoFormateado . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- descripcion -->
                    <tr style="border-bottom:1px solid #DDD;">
                        <td style="padding:0.8em 1em 0.9em 1em ; line-height: 1.4;">
                            <span style="color:#666; font-size: 10px;">Este correo adjunta Documentos Tributarios Electrónicos (DTE) para el receptor electrónico indicado. Por favor responda con un acuse de recibo (RespuestaDTE) conforme al modelo de intercambio de Factura Electrónica del SII.<br /></span>
                        </td>
                    </tr>

                    <tr style="background-color:#f5f5f5;">
                        <td style="color:#333; ">
                            <table class="social" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5 style="margin-top:12px;margin-left:15px;margin-bottom:0px;font-size: 14px; color:#004A8D;">Contáctanos a:</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table class="column" align="left" style="width: 340px; min-width: 339px; float: left;">
                                                <tbody>
                                                    <tr>
                                                        <td style="padding: 15px">
                                                            <div style="line-height: 1.6;">
                                                                <div><a style="font-size: 14px;color:#45AEF6" href=""></a></strong></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="margin-bottom:25px">
                <tbody>
                    <tr>
                        <td style="border-collapse:collapse" height="20" valign="top"></td>
                    </tr>
                    <tr>
                        <td style="padding:0cm 0cm 0cm 0cm; height:37.55pt" valign="center">
                            <a href="">
                                <img style="margin-left: 15%;" src="" shrinktofit="true" border="0">
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </center>
    </div>';

    // Verificar que los archivos existan
    if (!file_exists($rutaXML) || !file_exists($rutaPDF)) {
        return false;
    }

    // Obtener el contenido de los archivos
    $file_xml_content = file_get_contents($rutaXML);
    $file_pdf_content = file_get_contents($rutaPDF);

    // Nombres de los archivos
    $file_xml_name = basename($rutaXML);
    $file_pdf_name = basename($rutaPDF);

    // Codifica los archivos en base64
    $encoded_file_xml = base64_encode($file_xml_content);
    $encoded_file_pdf = base64_encode($file_pdf_content);

    // Genera los encabezados para el correo
    $boundary = "----=_NextPart_" . md5(time());

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"" . "\r\n";

    // Agregar el encabezado From
    $headers .= 'From: "' . $razonSocialEmisor . '"' . "\r\n";

    // Encabezado del cuerpo del correo (mensaje principal)
    $headers .= "--$boundary" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "Content-Transfer-Encoding: 7bit" . "\r\n";
    $headers .= "\r\n";
    $headers .= $message . "\r\n";

    // Adjuntar el archivo XML
    $headers .= "--$boundary" . "\r\n";
    $headers .= "Content-Type: application/xml; name=\"$file_xml_name\"" . "\r\n";
    $headers .= "Content-Disposition: attachment; filename=\"$file_xml_name\"" . "\r\n";
    $headers .= "Content-Transfer-Encoding: base64" . "\r\n";
    $headers .= "\r\n";
    $headers .= $encoded_file_xml . "\r\n";

    // Adjuntar el archivo PDF
    $headers .= "--$boundary" . "\r\n";
    $headers .= "Content-Type: application/pdf; name=\"$file_pdf_name\"" . "\r\n";
    $headers .= "Content-Disposition: attachment; filename=\"$file_pdf_name\"" . "\r\n";
    $headers .= "Content-Transfer-Encoding: base64" . "\r\n";
    $headers .= "\r\n";
    $headers .= $encoded_file_pdf . "\r\n";

    // Cerrar el mensaje con el boundary
    $headers .= "--$boundary--" . "\r\n";

    // Enviar el correo
    return mail($correoDestinatario, $subject, "", $headers);
}



// Para el PDF, usamos la misma lógica de la generación
if (isset($_POST['cedible']) && $_POST['cedible'] === 'true') {
  $archivoPDF = $carpetaPDF.'D0C'.$DTE->getID().'Cedible.pdf';
} else {
  $archivoPDF = $carpetaPDF.'D0C'.$DTE->getID().'.pdf';
}


if (!empty($correo)) {  // Verifica que tengamos un correo para enviar
  $resultado_email = enviarEmailDTE(
      $correo,                       // Correo del destinatario
      $_POST['RUTEmisor'],           // RUT emisor
      $_POST['RznSoc'],              // Razón social emisor
      $_POST['RUTRecep'],            // RUT receptor
      $_POST['RznSocRecep'],         // Razón social receptor
      $tipoDTE,                      // Tipo de DTE
      $folioPost,                    // Número de folio
      $_POST['FchEmis'],             // Fecha de emisión
      floatval($DTE->getMontoTotal()), // Monto total
      $archivoXMLCopia,              // Ruta al XML (copia para receptor)
      $archivoPDF                    // Ruta al PDF
  );
  
  if ($resultado_email) {
      echo "<br>Correo enviado exitosamente a: " . $correo;
  } else {
      echo "<br>Error al enviar el correo a: " . $correo;
  }
} else {
  echo "<br>No se envió correo: destinatario no especificado.";
}

// Crea una instancia de IngresoDocumento
$Documentos = new IngresoDocumento();

// Extrae los valores de exento e IVA directamente del DTE
$exento = null;
if (isset($DTE->getDatos()['Encabezado']['Totales']['MntExe'])) {
    $exento = floatval($DTE->getDatos()['Encabezado']['Totales']['MntExe']);
}

$iva = 0;
if (isset($DTE->getDatos()['Encabezado']['Totales']['IVA'])) {
    $iva = floatval($DTE->getDatos()['Encabezado']['Totales']['IVA']);
}
 

// Prepara un array con valores escalares asegurándonos de que exento e IVA sean valores numéricos
$documentoValues = [
    "Venta",                          // tipo (string)
    intval($tipoDTE),                 // dte (integer)
    intval($folioPost),               // folio (integer)
    $_POST['FchEmis'],                // emision (string)
    $_POST['FchEmis'],                // vencimiento (string)
    isset($IdDoc['FmaPago']) ? ($IdDoc['FmaPago'] == 1 ? 'Contado' : ($IdDoc['FmaPago'] == 2 ? 'Crédito' : 'Sin Costo')) : 'NULL', // forma_pago
    null,                               // desc_documento (string vacío en lugar de null)
    $exento,                          // exento (numeric) - valor del DTE
    $iva,                             // iva (numeric) - valor del DTE
    null,                                // otro_impuesto (numeric)
    floatval($DTE->getMontoTotal()),  // total (numeric)
    $_POST['RUTEmisor'],  
    $_POST['RUTRecep'], // cliente_rut (string)
    null, // proveedor_rut (string)
    $archivoXMLCopia,  // ruta_xml (string) - usando la ruta exacta del archivo XML
    $archivoPDF  // ruta_pdf (string) - usando la ruta exacta del archivo PDF
];

// Intenta registrar el documento
$resultadoRegistro = $Documentos->newDocumento($documentoValues);

// Después de insertar el documento principal
if ($resultadoRegistro === true) {
  // El documento se insertó correctamente
  $documentoId = $_SESSION['id_doc'];
  echo "Documento insertado con éxito. ID: " . $documentoId;
  
  // Ahora inserta los detalles
  echo "<br>Procesando " . count($Detalle) . " detalles:<br>";
  
  // Ahora inserta los detalles
foreach ($Detalle as $index => $item) {
  // Obtiene los valores del ítem
  $valorCodigo = isset($item['CdgItem']['VlrCodigo']) ? $item['CdgItem']['VlrCodigo'] : '';
  $cantidad = isset($item['QtyItem']) ? floatval($item['QtyItem']) : 0;
  $precio = isset($item['PrcItem']) ? floatval($item['PrcItem']) : 0;
  $total = $cantidad * $precio;
  
  // Crear array con solo los campos que existen en la tabla
  $detalleValues = [
      $documentoId,           // documento_id
      $valorCodigo,           // codigo_producto
      '',                     // codigo_servicio
      $cantidad,              // cantidad
      $precio,                // precio
      0,                      // descuento
      $DTE->getMontoTotal()                // total
  ];
  
  // Debug
  echo "Insertando detalle con valores: ";
  print_r($detalleValues);
  echo "<br>";
  
  $resultadoDetalle = $Documentos->newDetalleDocumento($detalleValues);
  
  if ($resultadoDetalle === true) {
      echo "✓ Detalle #" . ($index + 1) . " guardado correctamente<br>";
  } else {
      echo "✗ Error al guardar detalle #" . ($index + 1) . ": ";
      if (is_object($resultadoDetalle)) {
          echo $resultadoDetalle->getMessage() . " (Código: " . $resultadoDetalle->getCode() . ")";
      } else {
          echo $resultadoDetalle;
      }
      echo "<br>";
  }
}
  
  echo "<br>Proceso de guardado finalizado";
}



################### ENVÍO DEL DTE AL SII ##############################
$track_id = $EnvioDTE->enviar(); //OK
var_dump($track_id);
$rutEmisor = isset($_POST['RUTEmisor']) ? $_POST['RUTEmisor'] : null;
var_dump("RUT Emisor recibido:", $rutEmisor);

// Incrementar el folio actual en la base de datos
if ($track_id) {
    // Obtener el folio actual
    $folioActual = $datosFolio['folio_actual'];
    $nuevoFolioActual = $folioActual + 1;

    // Llamar a la función updFolio para actualizar solo el folio_actual
    $empresaRut = $_POST['RUTEmisor']; // Obtener el RUT de la empresa
    $tipoFolio = $_POST['TipoDTE']; // Tipo de folio (debe coincidir con el tipo en la base de datos)

    if ($Folio->updFolio($empresaRut, $tipoFolio, $nuevoFolioActual)) {
        var_dump("Folio actual incrementado a:", $nuevoFolioActual);
    } else {
        var_dump("Error al actualizar el folio en la base de datos.");
    }
    // Enviar el correo con los documentos adjuntos
    if (!empty($correo)) {  // Verifica que tengamos un correo para enviar
      $resultado_email = enviarEmailDTE(
          $correo,                       // Correo del destinatario
          $_POST['RUTEmisor'],           // RUT emisor
          $_POST['RznSoc'],              // Razón social emisor
          $_POST['RUTRecep'],            // RUT receptor
          $_POST['RznSocRecep'],         // Razón social receptor
          $tipoDTE,                      // Tipo de DTE
          $folioPost,                    // Número de folio
          $_POST['FchEmis'],             // Fecha de emisión
          floatval($DTE->getMontoTotal()), // Monto total
          $archivoXMLCopia,              // Ruta al XML (copia para receptor)
          $archivoPDF                    // Ruta al PDF
      );
      
      if ($resultado_email) {
          echo "<br>Correo enviado exitosamente a: " . $correo;
      } else {
          echo "<br>Error al enviar el correo a: " . $correo;
      }
    } else {
      echo "<br>No se envió correo: destinatario no especificado.";
    }
} else {
    var_dump("Error al enviar el DTE, no se incrementó el folio.");
}

######################## CONSULTAS DE ESTADO ENVIO PARA CERTIFICACIÓN ##################################
//IGUALMENTE ESTO SE VE AUTOMATICAMENTE CUANDO LLEGA EL CORREO CON EL ESTADO COMO RESPUESTA
/*//Estado Envio 
$respuesta = $DTE->getEstadoEnvio($Firma, $track_id,$_POST['RUTEmisor'] ); //OK 
var_dump($respuesta);

//Estado Dte
$respuesta = $DTE->getEstado($Firma);  //OK DOK "Documento Recibido por el SII. Datos Coinciden con los Registrados"
var_dump($respuesta);*/

//Estado Dte Avanzado
//$respuesta = $DTE->getEstadoAvanzado($Firma); //PENDIENTE 
//var_dump($respuesta);

// Si hubo errores mostrar
foreach (Log::readAll() as $error)
    echo $error,"<br>";