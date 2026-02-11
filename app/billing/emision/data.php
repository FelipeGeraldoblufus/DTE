<?php
require_once '../../../config.php';

if (!empty($_GET['RUTRecep'])) {
	$Cliente = new Cliente();
	$dataCliente = $Cliente->buscaCliente($_GET['RUTRecep']);

	foreach ($dataCliente as $value) {
		$data[] = $value['rut'];
	}
	echo json_encode($data);
}
?>