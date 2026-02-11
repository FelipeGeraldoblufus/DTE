<?php
require_once '../../../../config.php';

$CuentaProveedor = new CuentaProveedor();

if (isset($_POST['data']) && !empty($_POST['data'])) {
  switch ($_POST['data']) {
    case 'create':
    $datos = [
      !empty($_POST['nombre']) ? $_POST['nombre'] : '',
      !empty($_POST['numero']) ? $_POST['numero'] : null,
      $_POST['rut'],
      !empty($_POST['banco']) ? $_POST['banco'] : null,
      !empty($_POST['tipo']) ? $_POST['tipo'] : null,
    ];

    var_dump($datos);
    $CuentaProveedor->newCuenta($datos);
    break;

    case 'update':
    $id = $_POST['id'];
    $datos = [
      !empty($_POST['nombre_edit']) ? $_POST['nombre_edit'] : '',
      !empty($_POST['numero_edit']) ? $_POST['numero_edit'] : null,
      !empty($_POST['banco_edit']) ? $_POST['banco_edit'] : null,
      !empty($_POST['tipo_edit']) ? $_POST['tipo_edit'] : null,
    ];

    $CuentaProveedor->updCuenta($id, $datos);
    break;

    case 'delete':
    $id = $_POST['id'];

    $CuentaProveedor->delCuenta($id);
    break;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $CuentaProveedor->getCuenta($_GET['id']);
  
  echo json_encode($datos);
}