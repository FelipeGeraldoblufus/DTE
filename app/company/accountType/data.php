<?php
require '../../../config.php';

$TipoCuenta = new TipoCuenta();

if (isset($_POST['data']) && !empty($_POST['data'])) {
  switch ($_POST['data']) {
    case 'create':
    $datos = [
      !empty($_POST['tipo']) ? $_POST['tipo'] : null,
    ];
    $TipoCuenta->newTipoCuenta($datos);
    break;

    case 'update':
    $id = $_POST['id'];
    $datos = [
      !empty($_POST['tipo_edit']) ? $_POST['tipo_edit'] : null,
    ];

    $TipoCuenta->updTipoCuenta($id, $datos);
    break;

    case 'delete':
    $id = $_POST['id'];

    $TipoCuenta->delTipoCuenta($id);
    break;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $TipoCuenta->getTipoCuenta($_GET['id']);
  
  echo json_encode($datos);
}