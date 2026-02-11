<?php
require '../../config.php';

$Producto = new Producto();

if (isset($_POST['data']) && !empty($_POST['data'])) {
  switch ($_POST['data']) {
    case 'create':
    $datos = [
      !empty($_POST['codigo']) ? $_POST['codigo'] : null,
      !empty($_POST['nombre']) ? $_POST['nombre'] : null,
      !empty($_POST['precio']) ? $_POST['precio'] : null,
      !empty($_POST['descripcion']) ? $_POST['descripcion'] : null,
      !empty($_POST['unimed']) ? $_POST['unimed'] : null
    ];
    $Producto->newProducto($datos);
    break;

    case 'update':
    $id = $_POST['id'];
    $datos = [
      !empty($_POST['nombre_edit']) ? $_POST['nombre_edit'] : null,
      !empty($_POST['precio_edit']) ? $_POST['precio_edit'] : null,
      !empty($_POST['descripcion_edit']) ? $_POST['descripcion_edit'] : null,
      !empty($_POST['unimed_edit']) ? $_POST['unimed_edit'] : null
    ];

    $Producto->updProducto($id, $datos);
    break;

    case 'delete':
    $id = $_POST['id'];

    $Producto->delProducto($id);
    break;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $Producto->getProducto($_GET['id']);
  
  echo json_encode($datos);
}