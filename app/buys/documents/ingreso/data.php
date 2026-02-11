<?php
require_once '../../../../config.php';

if (!empty($_POST['RUTRecep'])) {
  $data = array();
  $Proveedor = new Proveedor();
  $rutCliente = $Proveedor->buscaProveedor($_POST['RUTRecep']);
  
  foreach ($rutCliente as $value) {
    $data[] = $value['rut'];
  }
  echo json_encode($data);
}

if (!empty($_POST['datoRut'])) {
  $data = array();
  $Proveedor = new Proveedor();
  $dataCliente = $Proveedor->getProveedor($_POST['datoRut']);
  
  echo json_encode($dataCliente);
}

if (!empty($_POST['codProducto'])) {
  $data = array();
  $Producto = new Producto();
  $codProducto = $Producto->buscaProducto($_POST['codProducto']);
  
  foreach ($codProducto as $value) {
    $data[] = $value['codigo'];
  }
  echo json_encode($data);
}

if (!empty($_GET['getProducto'])) {
  $data = array();
  $Producto = new Producto();
  $dataProducto = $Producto->getProducto($_GET['getProducto']);
  
  echo json_encode($dataProducto);
}

if (!empty($_POST['datoProducto'])) {
  $data = array();
  $Producto = new Producto();
  $dataProducto = $Producto->getProducto($_POST['datoProducto']);
  
  echo json_encode($dataProducto);
}

if (!empty($_GET['cliente'])) {
  $data = array();
  $Buscador = new Buscador();
  $data = $Buscador->buscarProveedor($_GET['cliente']);
  
  echo json_encode($data);
}

if (!empty($_GET['datosCliente'])) {
  $data = array();
  $Proveedor = new Proveedor();
  $dataCliente = $Proveedor->getProveedor($_GET['datosCliente']);
  
  echo json_encode($dataCliente);
}

if (isset($_POST['tipo']) && !empty($_POST['tipo'])) {
  $IngresoDocumento = new IngresoDocumento();

  $Documento = [
    $_POST['tipo'],
    !empty($_POST['TipoDTE']) ? $_POST['TipoDTE'] : null,
    !empty($_POST['Folio']) ? $_POST['Folio'] : null,
    !empty($_POST['FchEmis']) ? $_POST['FchEmis'] : null,
    !empty($_POST['FchVence']) ? $_POST['FchVence'] : null,
    !empty($_POST['FmaPago']) ? $_POST['FmaPago'] : null,
    !empty($_POST['descuento']) ? $_POST['descuento'] : null,
    !empty($_POST['exento']) ? $_POST['exento'] : null,
    !empty($_POST['iva']) ? $_POST['iva'] : null,
    !empty($_POST['otro_impuesto']) ? $_POST['otro_impuesto'] : null,
    !empty($_POST['total']) ? $_POST['total'] : null,
    !empty($_POST['RUTEmisor']) ? $_POST['RUTEmisor'] : null,
    null,
    !empty($_POST['RUTRecep']) ? $_POST['RUTRecep'] : null
  ];
  $newDocumento = $IngresoDocumento->newDocumento($Documento);

  $x = count($_POST['item']);

  for ($i=0; $i < $x ; $i++) {
    $Detalle = [
      !empty($_POST['item'][$i]['VlrCodigo']) ? $_POST['item'][$i]['VlrCodigo'] : null,
      !empty($_POST['item'][$i]['NmbItem']) ? $_POST['item'][$i]['NmbItem'] : null,
      !empty($_POST['item'][$i]['DscItem']) ? $_POST['item'][$i]['DscItem'] : null,
      !empty($_POST['item'][$i]['QtyItem']) ? $_POST['item'][$i]['QtyItem'] : null,
      !empty($_POST['item'][$i]['PrcItem']) ? $_POST['item'][$i]['PrcItem'] : null,
      !empty($_POST['item'][$i]['CodImpAdic']) ? $_POST['item'][$i]['CodImpAdic'] : null,
      !empty($_POST['item'][$i]['UnmdItem']) ? $_POST['item'][$i]['UnmdItem'] : null,
      !empty($_POST['item'][$i]['DescuentoPct']) ? $_POST['item'][$i]['DescuentoPct'] : null,
      !empty($_POST['item'][$i]['SubTotal']) ? $_POST['item'][$i]['SubTotal'] : null,
      $_SESSION['id_doc']
    ];
    $IngresoDocumento->newDetalleDocumento($Detalle);
    echo "<pre>";
    var_dump($Detalle);
    echo "</pre>";
  }


  $y = count($_POST['refe']);
  if (!empty($y)) {
    for ($i=0; $i < $y ; $i++) {
      if (!empty($_POST['refe'][$i]['TpoDocRef']) && !empty($_POST['refe'][$i]['FolioRef']) && !empty($_POST['refe'][$i]['FchRef']) && !empty($_POST['refe'][$i]['RazonRef'])) {
        $Referencia = [
          !empty($_POST['refe'][$i]['TpoDocRef']) ? $_POST['refe'][$i]['TpoDocRef'] : null,
          !empty($_POST['refe'][$i]['FolioRef']) ? $_POST['refe'][$i]['FolioRef'] : null,
          !empty($_POST['refe'][$i]['FchRef']) ? $_POST['refe'][$i]['FchRef'] : null,
          !empty($_POST['refe'][$i]['RazonRef']) ? $_POST['refe'][$i]['RazonRef'] : null,
          $_SESSION['id_doc']
        ];
        $IngresoDocumento->newReferenciaDocumento($Referencia);
        echo "<pre>";
        var_dump($Referencia);
        echo "</pre>";
      }
    }
  }

  $z = count($_POST['pago']);
  if (!empty($z)) {
    for ($i=0; $i < $z ; $i++) {
      $NroLinRef = $i + 1;
      if (!empty($_POST['pago'][$i]['FchPago']) && !empty($_POST['pago'][$i]['MntPago']) && !empty($_POST['pago'][$i]['GlosaPagos'])) {
        $Pago = [
          !empty($_POST['pago'][$i]['FchPago']) ? $_POST['pago'][$i]['FchPago'] : null,
          !empty($_POST['pago'][$i]['MntPago']) ? $_POST['pago'][$i]['MntPago'] : null,
          !empty($_POST['pago'][$i]['GlosaPagos']) ? $_POST['pago'][$i]['GlosaPagos'] : null,
          $_SESSION['id_doc']
        ];
        $IngresoDocumento->newPagoDocumento($Pago);
        echo "<pre>";
        var_dump($Pago);
        echo "</pre>";
      }
    }
  }
  if ($newDocumento) {
    $_SESSION['offlog'] = 'Se ha guardado el Documento';
  } else {
    $_SESSION['offlog'] = 'Error! No se han guardado los datos';
  }
}



