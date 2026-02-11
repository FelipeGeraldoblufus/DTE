<?php
require '../../config.php';

$Banco = new Banco();

if (isset($_POST['data']) && !empty($_POST['data'])) {
  switch ($_POST['data']) {
    case 'create':
    $datos = [
      !empty($_POST['banco']) ? $_POST['banco'] : null,
    ];
    $Banco->newBanco($datos);
    break;

    case 'update':
    $id = $_POST['id'];
    $datos = [
      !empty($_POST['banco_edit']) ? $_POST['banco_edit'] : null,
    ];

    $Banco->updBanco($id, $datos);
    break;

    case 'delete':
    $id = $_POST['id'];

    $Banco->delBanco($id);
    break;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $Banco->getBanco($_GET['id']);
  
  echo json_encode($datos);
}