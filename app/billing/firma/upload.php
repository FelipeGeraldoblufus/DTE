<?php
require_once '../../../config.php';


$file = $_FILES['archivo']['tmp_name'];
$pass = $_POST['password'];
$rut_usuario = $_POST['rut_usuario'];

$FirmaElectronica = new FirmaElectronica($file, $pass);
$FirmaManager = new FirmaManager();
$Directorio = new Directorio();
$Firma = new Firma();

var_dump($FirmaElectronica->getID()); // Verifica si la función getID devuelve lo esperado
var_dump($rut_usuario); // Verifica el valor de $rut_usuario 

if ($FirmaElectronica->getID() == $rut_usuario) {
	$rut = $FirmaElectronica->getID();
} else {
	
	echo 'ERROR! usuario y firma no coincide';

}

$carpeta = $Directorio->creaDirectorio(__ROOT__.'/client/firma/'.$rut);

$nombre 	= $FirmaElectronica->getName();
$desde 	= $FirmaElectronica->getFrom();
$hasta 	= $FirmaElectronica->getTo(); 

$ruta 	= str_replace('../', '', $carpeta.$rut.'.p12');

$FirmaManager->setDestination($carpeta);
$FirmaManager->setFileName($rut.'.p12');
$FirmaManager->upload($_FILES['archivo']);



$addFirma = $Firma->newFirma($rut, $nombre, $desde, $hasta, $ruta, $pass);

if ($addFirma) {
	echo "La Firma se a subido correctamente <br>";
} else {
	echo "ERROR! no se ha guardado la informacion <br>";
}

$id = $Firma->rutFirma($rut);

$id = $id['id'];

$userFirma = $Firma->addFirmaUsuario($rut, $id);

if ($userFirma) {
	echo "Se ha vinculado correctamente al Usuario con la firma <br>";
} else {
	echo "ERROR! no se ha guardado la informacion <br>";
}

?>
