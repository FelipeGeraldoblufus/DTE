<?php
require '../../../config.php';

$Bodega = new Bodega();

if (isset($_POST['data']) && !empty($_POST['data'])) {
  switch ($_POST['data']) {
    case 'create':
    $datos = [
      !empty($_POST['bodega']) ? $_POST['bodega'] : null,
      !empty($_POST['direccion']) ? $_POST['direccion'] : 'Sin direccion',
      !empty($_POST['telefono']) ? $_POST['telefono'] : 'Sin Telefono'
    ];
    $Bodega->newBodega($datos);
    break;

    case 'update':
    $id = $_POST['id'];
    $datos = [
      !empty($_POST['bodega_edit']) ? $_POST['bodega_edit'] : null,
      !empty($_POST['direccion_edit']) ? $_POST['direccion_edit'] : 'Sin direccion',
      !empty($_POST['telefono_edit']) ? $_POST['telefono_edit'] : 'Sin Telefono'
    ];

    $Bodega->updBodega($id, $datos);
    break;

    case 'delete':
    $id = $_POST['id'];

    $Bodega->delBodega($id);
    break;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $Bodega->getBodega($_GET['id']);
  
  echo json_encode($datos);
}