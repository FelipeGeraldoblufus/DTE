<?php
require '../../../config.php';

$Stock = new Stock();

if (isset($_POST['data']) && !empty($_POST['data'])) {
  switch ($_POST['data']) {
    case 'create':
    $datos = [
      !empty($_POST['stock']) ? $_POST['stock'] : null,
      !empty($_POST['bodega']) ? $_POST['bodega'] : null,
      !empty($_POST['producto']) ? $_POST['producto'] : null
    ];
    $Stock->newStock($datos);
    break;

    case 'update':
    $id = $_POST['id'];
    $datos = [
      !empty($_POST['stock_edit']) ? $_POST['stock_edit'] : null,
      !empty($_POST['bodega_edit']) ? $_POST['bodega_edit'] : null,
      !empty($_POST['producto_edit']) ? $_POST['producto_edit'] : null
    ];

    $Stock->updStock($id, $datos);
    break;

    case 'delete':
    $id = $_POST['id'];

    $Stock->delStock($id);
    break;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $Stock->getStock($_GET['id']);
  
  echo json_encode($datos);
}