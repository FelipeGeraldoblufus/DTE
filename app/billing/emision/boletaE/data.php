<?php
require_once '../../../../config.php';

if (!empty($_POST['RUTRecep'])) {
	$data = array();
	$Cliente = new Cliente();
	$rutCliente = $Cliente->buscaCliente($_POST['RUTRecep']);
	
	foreach ($rutCliente as $value) {
		$data[] = $value['rut'];
	}
	echo json_encode($data);
}

if (!empty($_POST['datoRut'])) {
	$data = array();
	$Cliente = new Cliente();
	$dataCliente = $Cliente->getCliente($_POST['datoRut']); //debiera ser getCliente() 
	
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

if (!empty($_POST['datoProducto'])) {
	$data = array();
	$Producto = new Producto();
	$dataProducto = $Producto->getProducto($_POST['datoProducto']); 
	
	echo json_encode($dataProducto);
}

if (!empty($_GET['cliente'])) {
	$data = array();
	$Buscador = new Buscador();
	$data = $Buscador->buscarCliente($_GET['cliente']);
	
	echo json_encode($data);
}

if (!empty($_GET['datosCliente'])) {
	$data = array();
	$Cliente = new Cliente();
	$dataCliente = $Cliente->getCliente($_GET['datosCliente']); //getCliente al parecer
	
	echo json_encode($dataCliente);
}

if (!empty($_GET['datosProducto'])) {
    $data = array();
    $Producto = new Producto();
    $dataProducto = $Producto->getProducto($_GET['datosProducto']); // usando el método getProducto() existente
    
    echo json_encode($dataProducto);
}

?>